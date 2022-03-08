<?php
/**
 * Created by:  KODIX 25.07.14 11:59
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Alexander Samakin 
 */
IncludeModuleLangFile(__FILE__);
class KDXDelivery {
    public $id;
    public $is_auto=false;
    public $profile;
    public $name;
    public function __construct($id){
        if(!CModule::IncludeModule('sale')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }
        if(!$id)
            return true;
        $tmp=explode(":", $id);
        $this->id=$tmp[0];
        if($tmp[1]){
            $this->is_auto=true;
            $this->profile=$tmp[1];
        }
        if($this->is_auto){
            $res = CSaleDeliveryHandler::GetList(array("SORT" => "ASC"), array(
                "ACTIVE" => "Y",
                "LID" => SITE_ID,
                "SID" => $this->id
            ))->GetNext();
            $this->name=$res["NAME"];
        }else{
            $res = CSaleDelivery::GetList(array(), array(
                "LID" => SITE_ID,
                "ACTIVE" => "Y",
                "ID"=>$this->id
            ))->GetNext();
            $this->name=$res["NAME"];
        }
    }

    public static function getList(){
        if(!CModule::IncludeModule('sale')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }

        $currency = CSaleLang::GetLangCurrency(SITE_ID);

        $result=array();
        $res = CSaleDelivery::GetList(
            array(
                "SORT" => "ASC",
                "NAME" => "ASC"
            ),
            array(
                "LID" => SITE_ID,
                "ACTIVE" => "Y",
            )
        );
        while($del=$res->GetNext()){
            $result[]=$del;
        }

        $cart=new KDXCart();


        $arCurOrderProps = array();
        $rCoupons = CCatalogDiscountCoupon::GetCoupons();
        $arShoppingCart = CSaleBasket::DoGetUserShoppingCart(
            SITE_ID,
            intval(CUser::GetID()),
            intval(CSaleBasket::GetBasketUserID(true)),
            $arErrors,
            $rCoupons
        );

        CCatalogDiscountCoupon::SetCoupon(array_shift($rCoupons));
        $arBasketInfo = CSaleOrder::DoCalculateOrder(
            SITE_ID,
            CUser::GetID(),
            $arShoppingCart,
            1,
            $arCurOrderProps,
            0,
            0,
            array(),
            $arErrors,
            $arWarnings
        );

        $price=0;

        foreach ($arBasketInfo['BASKET_ITEMS'] as $item) {
            $price+=floatval($item["PRICE"])*intval($item["QUANTITY"]);
        }

        $order=KDXOrder::loadFromSession();
        $order->getVATPrice($price);

        if ($order->properties["DELIVERY_COUNTRY"]["VALUE"]) {
            if (!$order->add_vat) {
                $price -=  $order->vat_price;
            }

            $arOrder = array(
                "WEIGHT" => $cart->getWeightAvailable(),
                "PRICE" => $price,
                "LOCATION_FROM" => COption::GetOptionInt('sale', 'location'), // местоположение магазина
                "ORDER_PROPS" => $order->properties,
                "LOCATION_TO"=>$order->properties["DELIVERY_COUNTRY"]["VALUE"]
            );
        }
        $res = CSaleDeliveryHandler::GetList(array("SORT" => "ASC"), array(
            "ACTIVE" => "Y",
            "SITE_ID" => SITE_ID,
            "!ID"=>'kdx_self'
        ));
        while ($deliv = $res->GetNext()) {
            if (!empty($arOrder)) {
                $arOrder['TEMP_DELIVERY']=$deliv;
                $arProfiles = CSaleDeliveryHandler::GetHandlerCompability($arOrder, $deliv);
                unset($arOrder['TEMP_DELIVERY']);
                foreach ($arProfiles as $k => $prof) {
                    $arDelivery = CSaleDeliveryHandler::CalculateFull(
                        $deliv["SID"], // идентификатор службы доставки
                        $k, // идентификатор профиля доставки
                        $arOrder, // заказ
                        $currency // валюта, в которой требуется вернуть стоимость
                    );
                    $arProfiles[$k]["PRICE"]=$arDelivery;
                }
                $deliv["PROFILES"]=$arProfiles;
            }
            $result[]=$deliv;
        }
        return $result;
    }

    public static function getArrayNames(){
        if(!CModule::IncludeModule('sale')) die('sale module');
        $arResult=array();
        $res = CSaleDeliveryHandler::GetList(array("SORT" => "ASC"), array(
            "ACTIVE" => "ALL",
            "SITE_ID" => "ALL",
        ));
        while ($deliv = $res->GetNext()){
            foreach($deliv['PROFILES'] as $id=>&$val){
                $arResult[$deliv['SID'].':'.$id]=$val['TITLE'];
            }
        }
        return $arResult;
    }

    public function getPrice($order=false){
        if(!$order)
            $order=KDXOrder::loadFromSession();
        if(!$order){
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_COULD_NOT_CALCULATE_DELIVERY_PRICE'));
        }
        if($this->is_auto){
            $currency = CSaleLang::GetLangCurrency(SITE_ID);
            $cart=new KDXCart();
            $arOrder = array(
                "WEIGHT" => $cart->getWeightAvailable(),
                "PRICE" => $cart->getAvailablePriceWithDiscount() + $order->vat_price,
                "LOCATION_FROM" => COption::GetOptionInt('sale', 'location'), // местоположение магазина
                "LOCATION_TO" => $order->properties["DELIVERY_COUNTRY"]["VALUE"],
                "ORDER_PROPS" => $order->properties,
            );

            $arDelivery = CSaleDeliveryHandler::CalculateFull(
                $this->id, // идентификатор службы доставки
                $this->profile, // идентификатор профиля доставки
                $arOrder, // заказ
                $currency // валюта, в которой требуется вернуть стоимость
            );

            if($arDelivery["RESULT"]=="ERROR"){
                //throw new \Bitrix\Main\SystemException(GetMessage('KDX_COULD_NOT_CALCULATE_DELIVERY_PRICE')." ".$arDelivery["TEXT"] . " $this->id $this->profile");
                 $arDelivery["VALUE"]=0;
            }
            return $arDelivery["VALUE"];
        }elseif($this->id){
            $delivery=CSaleDelivery::GetByID($this->id);
            return $delivery["PRICE"];
        }
        throw new \Bitrix\Main\SystemException(GetMessage('KDX_COULD_NOT_CALCULATE_DELIVERY_PRICE'));
    }
} 