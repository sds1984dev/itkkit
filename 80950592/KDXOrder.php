<?php
/**
 * Created by:  KODIX 25.07.14 10:33
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Alexander Samakin
 */
IncludeModuleLangFile(__FILE__);

/**
 * Класс для работы с заказом.
 * @link https://bitbucket.org/kodixrock/product/wiki/KDXOrder
 */
class KDXOrder
{

    private static $instance;


    public $id              = false;
    public $profile_id      = false;
    public $properties      = array();
    public $delivery_id     = false;
    public $price_delivery  = false;
    public $pay_system_id   = false;
    public $comment         = false;
    public $payed           = false;
    public $date_payed      = false;
    public $canceled        = false;
    public $date_canceled   = false;
    public $reason_canceled = false;
    public $status_id       = false;
    public $date_status     = false;
    /**
     * Сумма к оплате
     * @var bool
     */
    public $price            = false;
    public $currency         = false;
    public $discount_value   = false;
    public $sum_paid         = false;
    public $user_id          = false;
    public $date_insert      = false;
    public $date_update      = false;
    public $user_description = false;
    public $user_login       = false;
    public $user_name        = false;
    public $user_last_name   = false;
    public $cart             = false;
    public $delivery_type    = false;
    public $vat_price        = false;
    public $add_vat          = false;
    public $calculatedOrder = array();

    public $productsVAT = array();

    private function __clone() { /* ... @return Singleton */ } // Защищаем от создания через клонирование
    private function __wakeup() { /* ... @return Singleton */} // Защищаем от создания через unserialize
    private function __construct()
    {
        if (!CModule::IncludeModule('sale')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }
    }

    public function setAddress($profile_id)
    {
        $address_props = KDXAddress::getAddressProps();
        $address = KDXAddress::getByProfileId($profile_id);
        $address->setAsLast();
//        $this->properties=array();
        foreach ($address_props as $code => $prop) {
            $code = strtolower($code);
            $this->properties[ strtoupper($code) ] = array(
                'NAME'           => $prop['NAME'],
                'VALUE'          => $address->$code,
                'CODE'           => strtoupper($code),
                'ORDER_ID'       => false,
                'ORDER_PROPS_ID' => $prop['ID'],
            );
        }
        $this->profile_id = $profile_id;

    }

    public function setDelivery($delivery_id)
    {
        $delivery = new KDXDelivery($delivery_id);
        $this->price_delivery = $delivery->getPrice();
        $this->delivery_id = $delivery_id;
    }

    public function setPaySystem($ps_id)
    {
        $ps = new KDXPaySystem($ps_id);
        if (!$this->delivery_id || $ps->isAvailableForDelivery($this->delivery_id))
            $this->pay_system_id = $ps_id;
        else
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BAD_PS_FOR_CURRENT_DELIVERY'));
    }

    public function saveToSession()
    {
        $_SESSION["ORDERING"]["ORDER"] = serialize($this);
    }

    public static function loadFromSession()
    {
        if(!self::$instance)
        {
            $order = unserialize($_SESSION["ORDERING"]["ORDER"]);
            if (is_object($order))
                self::$instance = $order;
            else
            {
                self::$instance = new self();
                self::$instance->saveToSession();
            }
        }
        self::$instance->cart = false;
//        self::$instance->getVATPrice();

        return self::$instance;
    }

    public function create($user_id = false)
    {	
        if (!$user_id) {
            $user_id = CUser::GetId();
        }
        if (!$user_id) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BAD_USER_ID'));
        }
        if ($this->id) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_ORDER_ALREADY_CREATED'));
        }
        CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(true), SITE_ID);

        $address = KDXAddress::getByProfileId($this->profile_id);
        if (!$address->isValid() && $this->delivery_type == 'ADDRESS') {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BAD_ADDRESS'));
        }

        $cart = new KDXCart();
        if (!count($cart->getAvailable())) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_AVAILABLE_CART_EMPTY'));
        }

        $delivery = new KDXDelivery($this->delivery_id);
        if (!$delivery->name) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_DELIVERY_NOT_FOUND'));
        }

        $pay_system = new KDXPaySystem($this->pay_system_id);
        if (!$pay_system->name) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_PS_NOT_FOUND'));
        }
        if (!$pay_system->isAvailableForDelivery($this->delivery_id)) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BAD_PS_FOR_CURRENT_DELIVERY'));
        }

        Bitrix\Sale\DiscountCouponsManager::init();
        $this->price = $cart->getAvailablePriceWithDiscount() + $this->price_delivery;
        $this->currency = CURRENCY_DEFAULT;

        $arFields = array(
            "LID"              => SITE_ID,
            "PERSON_TYPE_ID"   => KDXSettings::getSetting("PHISICAL_PAYER_ID"),
            "PAYED"            => "N",
            "CANCELED"         => "N",
            "STATUS_ID"        => "N",
            "PRICE"            => $this->price,
            "CURRENCY"         => $this->currency,
            "USER_ID"          => $user_id,
            "PAY_SYSTEM_ID"    => $this->pay_system_id,
            "PRICE_DELIVERY"   => $this->price_delivery,
            "DELIVERY_ID"      => $this->delivery_id,
            "USER_DESCRIPTION" => $this->comment
            //"DISCOUNT_VALUE"=>$total_discount
        );
        //todo: необходимо заменить на схему CSaleOrder::DoCalculateOrder => CSaleOrder::DoSaveOrder (и выпилить KDXOrder::OrderBasket)
        // START for REPLACE
        $this->id = CSaleOrder::Add($arFields);
        if (!$this->id) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_CREATE_ORDER_FAIL'));
        }
        //CSaleBasket::OrderBasket - применяет купоны до того как вызван DoProcessOrder (который подготавливает купоны к применению)
        self::OrderBasket($this->id, CSaleBasket::GetBasketUserID(true), SITE_ID, false);
        $dbBasketItems = CSaleBasket::GetList(
            array("ID" => "ASC"),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(true),
                //"LID" => SITE_ID, // комментируем, т.к. пссле добавления заказа он обновляет цену с учетом скидки для 1 корзины (а надо 2)
                "ORDER_ID" => $this->id
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME", "DISCOUNT_PRICE", "CURRENCY", "PRODUCT_PROVIDER_CLASS", "DIMENSIONS")
        );
        $arResult["ORDER_PRICE"] = 0;

        $arOrderForDiscount = array(
            'SITE_ID' => SITE_ID,
            'LID' => SITE_ID,
            'USER_ID' => $user_id,
            'ORDER_PRICE' => $cart->getAvailablePriceWithDiscount()/* + $this->price_delivery*/,
            'ORDER_WEIGHT' => 0,
            "WEIGHT_UNIT" => htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', false, SITE_ID)),
            "WEIGHT_KOEF" => htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID)),
            'PRICE_DELIVERY' => &$this->price_delivery,
            'BASKET_ITEMS' => array(),
            'CURRENCY' => htmlspecialcharsbx(COption::GetOptionString('sale', 'default_currency', 'RUB', SITE_ID)),
            "PERSON_TYPE_ID" => KDXSettings::getSetting("PHISICAL_PAYER_ID"),
            "USE_VAT" => false,
            "VAT_RATE" => 0,
            "VAT_SUM" => 0,
            "PAY_SYSTEM_ID" => $this->pay_system_id,
            "DELIVERY_ID" => $this->delivery_id,
        );

        while ($arOneItem = $dbBasketItems->GetNext())
        {
            $arOrderForDiscount['BASKET_ITEMS'][] = $arOneItem;
            $arOrderForDiscount['ORDER_WEIGHT'] += doubleval($arOneItem['WEIGHT']);
        }

        $arDiscountOptions = array();

        $arDiscountErrors = array();
		
		foreach ($arOrderForDiscount['BASKET_ITEMS'] as $key=>$item) {
				if (isset ($item['PRODUCT_ID'])) {
					//Ищем родителя
					$mxResult=CCatalogSku::GetProductInfo($item['PRODUCT_ID']);
					if (isset($mxResult['ID'])) {
						$parent_ID=$mxResult['ID'];
						$res = CIBlockElement::GetProperty(1, $parent_ID, array("sort" => "asc"), Array('CODE'=>'BADGE'));
						$BADGE=array();
						while ($ob = $res->GetNext()) { 
							$BADGE[]=$ob['VALUE'];
						}
						$arOrderForDiscount['BASKET_ITEMS'][$key]['CATALOG']['PROPERTY_22_VALUE']=$BADGE;
					}
					
					//print_r('<pre class="SKdebug" style="display:none;">');
					//print_r($item);
					//print_r('</pre>');
				}
			}
			
		/*if ($_SERVER['REMOTE_ADDR']=='109.237.2.66') {
			print_r('<pre>');
			print_r($arOrderForDiscount);
			print_r('</pre>');
			die();
		}*/

        CSaleDiscount::DoProcessOrder($arOrderForDiscount, $arDiscountOptions, $arDiscountErrors);
        \Bitrix\Sale\DiscountCouponsManager::finalApply();
        \Bitrix\Sale\DiscountCouponsManager::saveApplied();

        $arResult["ORDER_PRICE"] = 0;


        $arBasketItems["BASKET_ITEMS"] = $arOrderForDiscount['BASKET_ITEMS'];
        $this->getVATPrice($arOrderForDiscount["BASKET_ITEMS"]);
        var_dump($this->add_vat);
        var_dump($this->vat_price);
        //$this->getVATPrice($arOrderForDiscount["ORDER_PRICE"]);
        if($this->add_vat == false){
            $totalOrderPrice = $arOrderForDiscount["ORDER_PRICE"] + $this->price_delivery + $arOrderForDiscount["TAX_PRICE"] - $arOrderForDiscount["DISCOUNT_PRICE"] - $this->vat_price;
        }else{
            $totalOrderPrice = $arOrderForDiscount["ORDER_PRICE"] + $this->price_delivery + $arOrderForDiscount["TAX_PRICE"] - $arOrderForDiscount["DISCOUNT_PRICE"];
        }

        foreach ($arOrderForDiscount['BASKET_ITEMS'] as $arOneItem)
        {
//            echo "<pre>";
//            print_r($arOneItem);
//            echo "</pre>";
//            $productPrice = $arOneItem['PRICE'];
            if ($this->add_vat == false) $arOneItem['PRICE'] /= (1 + $this->productsVAT[$arOneItem['PRODUCT_ID']]);

            $arResult["ORDER_PRICE"] += doubleval($arOneItem['PRICE'])*doubleval($arOneItem['QUANTITY']);
            $arBasketInfo = array(
                'IGNORE_CALLBACK_FUNC' => 'Y',
                'PRICE' => $arOneItem['PRICE'],
            );
            if (array_key_exists('DISCOUNT_PRICE', $arOneItem))
            {
                $arBasketInfo['DISCOUNT_PRICE'] = $arOneItem['DISCOUNT_PRICE'];
            }
//                    echo "<pre>";
//        print_r($arOneItem);
//        echo "</pre>";

            CSaleBasket::Update(
                $arOneItem['ID'],
                $arBasketInfo
            );
        }



        if (isset($arOneItem))
            unset($arOneItem);




        CSaleOrder::Update($this->id, array("PRICE" => $totalOrderPrice,'PRICE_DELIVERY'=>$this->price_delivery));
        $this->price = $totalOrderPrice;
        // END for REPLACE

//        echo "<pre>";print_r($arOrderForDiscount["ORDER_PRICE"]); echo "</pre>";
//        echo "<br>";
//        echo "<pre>";print_r($this->price_delivery); echo "</pre>";
//        echo "<br>";
//        echo "<pre>";print_r($arOrderForDiscount["DISCOUNT_PRICE"]); echo "</pre>";
//        echo "<br>";
//        echo "<pre>";print_r($this->vat_price); echo "</pre>";
//        echo "<br>";
//        echo "<pre>";print_r($totalOrderPrice); echo "</pre>";
//
//        die();

        foreach (GetModuleEvents("kodix.sale", "OnBasketOrder", true) as $arEvent) {
            ExecuteModuleEventEx($arEvent, array($this->id, CSaleBasket::GetBasketUserID(true), SITE_ID, false));
        }

        if ($this->properties['PHONE']['VALUE']) {
            $this->setProperty('PHONE_QIWI', '+7' . substr(preg_replace('/\D/', '', $this->properties['PHONE']['VALUE']),1));
        } elseif (intval($user_id)) {
            $user = CUser::GetByID($user_id)->Fetch();
            if ($user['PERSONAL_PHONE']) {
                $this->setProperty('PHONE_QIWI', '+7' . substr(preg_replace('/\D/', '', $user['PERSONAL_PHONE']),1));
            }
        }

        $this->sendEmail();
        unset($_SESSION["ORDERING"]["ORDER"]);

        $this->saveProperties();

        if(\Bitrix\Main\Loader::includeModule('kodix.alfalinkexchange'))
            KDXAlfalinkExchange::getInstance()->addSendOrder($this->id);

        return $this->id;
    }

    public function sendEmail()
    {
        global $DB;

        if (!$this->id)
            return false;

        $strOrderList = "";
        $baseLangCurrency = KDXCurrency::$CurrentCurrency;
        $orderNew = CSaleOrder::GetByID($this->id);
        $orderNew["BASKET_ITEMS"] = array();

        $rsUser = CUser::GetByID($orderNew["USER_ID"]);
        $arUser = $rsUser->Fetch();

        $userFullName = sprintf("%s %s", $arUser["LAST_NAME"], $arUser["NAME"]);

        $dbBasketTmp = CSaleBasket::GetList(
            array("SET_PARENT_ID" => "DESC", "TYPE" => "DESC", "NAME" => "ASC"),
            array("ORDER_ID" => $this->id),
            false,
            false,
            array(
                "PRICE", "QUANTITY", "NAME",'CURRENCY'
            )
        );

        while ($arBasketTmp = $dbBasketTmp->GetNext()) {
            $orderNew["BASKET_ITEMS"][] = $arBasketTmp;
        }

        $orderNew["BASKET_ITEMS"] = getMeasures($orderNew["BASKET_ITEMS"]);

        foreach ($orderNew["BASKET_ITEMS"] as $val) {
            if (CSaleBasketHelper::isSetItem($val))
                continue;

            $measure = (isset($val["MEASURE_TEXT"])) ? $val["MEASURE_TEXT"] : GetMessage("SALE_YMH_SHT");
            $strOrderList .= $val["NAME"] . " - " . $val["QUANTITY"] . " " . $measure . ": " . KDXCurrency::format($val["PRICE"], $val["CURRENCY"]);
            $strOrderList .= "\n";
        }

        //send mail
        $arFields = array(
            "ORDER_ID"       => $orderNew["ACCOUNT_NUMBER"],
            "ORDER_DATE"     => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", SITE_ID))),
            "ORDER_USER"     => $userFullName,
            "PRICE"          => KDXCurrency::format($orderNew["PRICE"]),
            "BCC"            => COption::GetOptionString("sale", "order_email", "order@" . $_SERVER['SERVER_NAME']),
            "EMAIL"          => $arUser["EMAIL"],
            "ORDER_LIST"     => $strOrderList,
            "SALE_EMAIL"     => COption::GetOptionString("sale", "order_email", "order@" . $_SERVER['SERVER_NAME']),
            "DELIVERY_PRICE" => $orderNew["DELIVERY_PRICE"],
        );

        $eventName = "SALE_NEW_ORDER";

        $bSend = true;
        foreach (GetModuleEvents("sale", "OnOrderNewSendEmail", true) as $arEvent)
            if (ExecuteModuleEventEx($arEvent, array($this->id, &$eventName, &$arFields)) === false)
                $bSend = false;

        if ($bSend) {
            $event = new CEvent;
            $event->Send($eventName, SITE_ID, $arFields, "N");
        }

    }

    public function setFields($fields)
    {
        foreach ($fields as $code => $value) {
            $code = strtolower($code);
            if (isset($this->$code)) {
                $this->$code = $value;
            }
        }
    }

    public static function getList($user_id = false, $site_id = false)
    {
        if (!CModule::IncludeModule('sale')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }
        if (!$user_id) {
            $user_id = CUser::GetId();
        }
        $filter = array();
        if ($user_id)
            $filter["USER_ID"] = $user_id;
        if ($site_id)
            $filter["LID"] = $site_id;

        $res = CSaleOrder::GetList(array("ID" => "DESC"), $filter);
        $orders = array();
        while ($or = $res->GetNext()) {
            $order = new KDXOrder();
            $order->setFields($or);
            $order->fillProps();
            $order->cart = new KDXCart(false, $or["ID"]);
            $orders[ $or["ID"] ] = $order;
        }
        return $orders;
    }

    public function fillProps()
    {
        $res = CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $this->id));
        while ($fields = $res->Fetch()) {
            $this->properties[ strtoupper($fields['CODE']) ] = array(
                'NAME'           => $fields['NAME'],
                'VALUE'          => $fields['VALUE'],
                'CODE'           => strtoupper($fields['CODE']),
                'ORDER_ID'       => $fields['ORDER_ID'],
                'ORDER_PROPS_ID' => $fields['ID'],
            );
        }
    }

    public static function getStatuses()
    {
        $statuses = array();
        $res = CSaleStatus::GetList();
        while ($s = $res->GetNext()) {
            $statuses[ $s["ID"] ] = $s;
        }
        return $statuses;
    }

    public static function getFullById($id, $user_id = -1)
    {
        if (!intval($id))
            return false;
        if ($user_id == -1) {
            $user_id = CUser::GetId();
        }
        $order = new KDXOrder();
        $arFilter = array("ID" => $id);
        if ($user_id) {
            $arFilter['USER_ID'] = $user_id;
        }
        $fields = CSaleOrder::GetList(array("ID" => "DESC"), $arFilter)->GetNext();
        $order->setFields($fields);
        if (!$order->id) {
            return false;
        }
        $cart = KDXCart::getByOrderId(intval($order->id));
        $order->cart = $cart;
        $order->fillProps();

        return $order;
    }

    public function setProperty($code, $value)
    {

        $res = CSaleOrderProps::GetList(array("SORT" => "ASC"), array(
                "CODE" => $code
            )
        );
        if ($prop = $res->Fetch()) {
            $this->properties[ $prop['CODE'] ] = array(
                'NAME'           => $prop['NAME'],
                'VALUE'          => $value,
                'CODE'           => $prop['CODE'],
                'ORDER_ID'       => $this->id,
                'ORDER_PROPS_ID' => $prop['ID'],
            );
        }
    }

    public function saveProperty($code)
    {
        if (!$this->id || !is_array($this->properties[ ToUpper($code) ])) {
            return false;
        }

        $arPropVal = CSaleOrderPropsValue::GetList(
            array("SORT" => "ASC"),
            array(
                "ORDER_ID" => $this->properties[ ToUpper($code) ]['ORDER_ID'],
                "ORDER_PROPS_ID" => $this->properties[ ToUpper($code) ]['ORDER_PROPS_ID']
            )
        )->Fetch();

        if($arPropVal)
        {
            CSaleOrderPropsValue::Update($arPropVal['ID'],$this->properties[ ToUpper($code) ]);
            return $arPropVal['ID'];
        }

        return CSaleOrderPropsValue::Add($this->properties[ ToUpper($code) ]);
    }

    private function saveProperties()
    {
        if (!$this->id || !is_array($this->properties))
            return;

        foreach (GetModuleEvents("kodix.sale", "OnBeforeSaveOrderProps", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array(&$this->properties,$this->id,$this->pay_system_id,$this->delivery_id));


        foreach ($this->properties as $code => $arProperty) {
            $this->properties[ $code ]['ORDER_ID'] = $this->id;
            $this->saveProperty($code);
        }

    }

    public function getVATPrice($orderItems)
    {
//        echo "getVatPrice";
//        var_dump($this->add_vat);
//        echo "getVat<pre>";
//        print_r($orderItems);
//        echo "</pre>";
        $result = 0;
        $arProductsVAT = Array();
        foreach ($orderItems as $key => $item)
        {

            $arrCountries = getHlCountries();

            foreach ($arrCountries as $arCountry) {
                if ($arCountry['UF_COUNTRY_ID'] == $this->properties["DELIVERY_COUNTRY"]['VALUE']) {

                    $useVAT = $arCountry['UF_USE_VAT'];
                    if ($useVAT == 'Y') {
                        $this->add_vat = true;
                    } else {
                        $this->add_vat = false;
                    }
                    break;
                }
            }

                $SKU = CCatalogSku::GetProductInfo($item['PRODUCT_ID']);

                if ($SKU) {
                    $dbElementGroups = CIBlockElement::GetElementGroups($SKU['ID'], true);
                    while ($arElementGroups = $dbElementGroups->Fetch()) {
                        $group = $arElementGroups["ID"];
                    }
                }

                $vatRate = ($group == 554) ? 0.12 : 0.21;
                $arProductsVAT[$item['PRODUCT_ID']] = $vatRate;
                $result += $item['PRICE'] * $item['QUANTITY'] * $vatRate / (1 + $vatRate);

//            var_dump($item['PRICE']);
//            var_dump($item['DISCOUNT_PRICE']);


        }

        $this->vat_price = $result;
        $this->productsVAT = $arProductsVAT;
        $this->setProperty('VAT_PRICE', $result);

    }

//    public function getVATPrice($price)
//    {
//
//        if(!CModule::IncludeModule('sale')) {
//            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
//        }
//
//        $result = 0;
//
//        if (!empty($this->properties["DELIVERY_COUNTRY"]["VALUE"])) {
////            $dbTAXLoc = Bitrix\Sale\Tax\RateLocationTable::getList(
////                array(
////                    'filter' => array('LOCATION_CODE' => array(
////                        CSaleLocation::getLocationCODEbyID($this->properties["DELIVERY_COUNTRY"]["VALUE"])
////                    ))
////                )
////            );
////
////            if(!empty( $this->properties["PAY_COUNTRY"]["VALUE"]))
////            {
////                $dbTAXLoc = Bitrix\Sale\Tax\RateLocationTable::getList(
////                    array(
////                        'filter' => array('LOCATION_CODE' => array(
////                            CSaleLocation::getLocationCODEbyID($this->properties["DELIVERY_COUNTRY"]["VALUE"]),
////                            CSaleLocation::getLocationCODEbyID($this->properties["PAY_COUNTRY"]["VALUE"]),
////                        ))
////                    )
////                );
////            }
//
//            $arrCountries = getHlCountries();
//
//
//
//            foreach ($arrCountries as $arCountry) {
//                if ($arCountry['UF_COUNTRY_ID'] == $this->properties["DELIVERY_COUNTRY"]['VALUE']) {
//
//                    $useVAT = $arCountry['UF_USE_VAT'];
//                    if ($useVAT == 'Y') {
//                        $this->add_vat = true;
//                    } else {
//                        $this->add_vat = false;
//                    }
//                    break;
//                }
//            }
//
//            $arTax = CSaleTaxRate::GetList(array(), array('TAX_ID' => KDXSettings::getSetting('TAX_RATE_ID')))->Fetch();
//
//            if ($arTax) {
//                $vatRate = $arTax['VALUE'] / 100;
//                $result += $price * $vatRate/(1+$vatRate);
//            }
//        }
//        $result = sprintf('%3.2f', $result);
//
//        if($this->delivery_id == 'simple:simple')
//            $result = 0;
//
//        $this->vat_price = $result;
//        $this->setProperty('VAT_PRICE',$result);
//    }

    public function calculateSaleDiscount(){
        global $USER;
        $cart = new KDXCart();
        $arOrder = array(
            'SITE_ID' => SITE_ID,
            'LID' => SITE_ID,
            'ORDER_PRICE' => $cart->getAvailablePriceWithDiscount(),
            'ORDER_WEIGHT' => $cart->getWeightAvailable(),
            "WEIGHT_UNIT" => htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', false, SITE_ID)),
            "WEIGHT_KOEF" => htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID)),
            'BASKET_ITEMS' => $cart->getAvailable(),
            'CURRENCY' => htmlspecialcharsbx(COption::GetOptionString('sale', 'default_currency', 'RUB', SITE_ID)),
            'PERSON_TYPE_ID' => '1',
            "USE_VAT" => false,
            "VAT_RATE" => 0,
            "VAT_SUM" => 0,
            "DELIVERY_ID" => false,
        );
        if(intval($USER->GetID())){
            $arOrder['USER_ID'] = intval($USER->GetID());
        }

        if(isset($this->price_delivery)){
            $arOrder['ORDER_PRICE'] += $this->price_delivery;
            $arOrder['OLD_PRICE_DELIVERY']=$this->price_delivery;
            $arOrder['PRICE_DELIVERY']=$this->price_delivery;
        }

        $arOptions = array();

		//$arOrder['BASKET_ITEMS']['626916']['CATALOG']['PROPERTY_22_VALUE']=array('3');
		//Ручное добавление свойства БЕЙДЖ (BADGE) к товару, чтобы считались скидки на распродажный товар. https://pyrus.com/t#id79680445
		foreach ($arOrder['BASKET_ITEMS'] as $item) {
			if (isset ($item['PROPS']['PARENT_ID'])) {
				$res = CIBlockElement::GetProperty(1, $item['PROPS']['PARENT_ID'], array("sort" => "asc"), Array('CODE'=>'BADGE'));
				$BADGE=array();
				while ($ob = $res->GetNext()) { 
					$BADGE[]=$ob['VALUE'];
				}
				$arOrder['BASKET_ITEMS'][$item['ID']]['CATALOG']['PROPERTY_22_VALUE']=$BADGE;
				//print_r('<pre class="SKdebug" style="display:none;">');
				//print_r($item);
				//print_r('</pre>');
			}
		}
		//print_r('<pre class="SKdebug" style="display:none;">');
		//print_r($arOrder);
		//print_r('</pre>');
		
        $arErrors = array();
		
        CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
        $this->calculatedOrder=$arOrder;
    }

    /**
     * @param $orderID
     * @param int $fuserID
     * @param string $strLang
     * @param bool $arDiscounts
     * @return bool
     *
     * CSaleBasket::OrderBasket - применяет купоны до того как вызван DoProcessOrder (который подготавливает купоны к применению)
     */
    function OrderBasket($orderID, $fuserID = 0, $strLang = SITE_ID, $arDiscounts = False)
    {
        $orderID = (int)$orderID;
        if ($orderID <= 0)
            return false;

        $fuserID = (int)$fuserID;
        if ($fuserID <= 0)
            $fuserID = CSaleBasket::GetBasketUserID(true);

        $arOrder = array();

        if (empty($arOrder))
        {
            $rsOrders = CSaleOrder::GetList(
                array(),
                array('ID' => $orderID),
                false,
                false,
                array('ID', 'USER_ID', 'RECURRING_ID', 'LID', 'RESERVED')
            );
            if (!($arOrder = $rsOrders->Fetch()))
                return false;
            $arOrder['RECURRING_ID'] = (int)$arOrder['RECURRING_ID'];
        }
        $boolRecurring = $arOrder['RECURRING_ID'] > 0;

        $needSaveCoupons = false;
        $dbBasketList = CSaleBasket::GetList(
            array("PRICE" => "DESC"),
            array("FUSER_ID" => $fuserID, "ORDER_ID" => 0), // удаляем LID, чтобы получить ID корзины как на EN, так и на RU
            false,
            false,
            array(
                'ID', 'ORDER_ID', 'PRODUCT_ID', 'MODULE',
                'CAN_BUY', 'DELAY', 'ORDER_CALLBACK_FUNC', 'PRODUCT_PROVIDER_CLASS',
                'QUANTITY'
            )
        );
        while ($arBasket = $dbBasketList->Fetch())
        {
            $arFields = array();
            if ($arBasket["DELAY"] == "N" && $arBasket["CAN_BUY"] == "Y")
            {
                if (!empty($arBasket["ORDER_CALLBACK_FUNC"]) || !empty($arBasket["PRODUCT_PROVIDER_CLASS"]))
                {
                    /** @var $productProvider IBXSaleProductProvider */
                    if ($productProvider = CSaleBasket::GetProductProvider($arBasket))
                    {
                        $arQuery = array(
                            "PRODUCT_ID" => $arBasket["PRODUCT_ID"],
                            "QUANTITY"   => $arBasket["QUANTITY"],
                            'BASKET_ID' => $arBasket['ID']
                        );
                        if ($boolRecurring)
                        {
                            $arQuery['RENEWAL'] = 'Y';
                            $arQuery['USER_ID'] = $arOrder['USER_ID'];
                            $arQuery['SITE_ID'] = $strLang;
                        }
                        $arFields = $productProvider::OrderProduct($arQuery);
                    }
                    else
                    {
                        if ($boolRecurring)
                        {
                            $arFields = CSaleBasket::ExecuteCallbackFunction(
                                $arBasket["ORDER_CALLBACK_FUNC"],
                                $arBasket["MODULE"],
                                $arBasket["PRODUCT_ID"],
                                $arBasket["QUANTITY"],
                                'Y',
                                $arOrder['USER_ID'],
                                $strLang
                            );
                        }
                        else
                        {
                            $arFields = CSaleBasket::ExecuteCallbackFunction(
                                $arBasket["ORDER_CALLBACK_FUNC"],
                                $arBasket["MODULE"],
                                $arBasket["PRODUCT_ID"],
                                $arBasket["QUANTITY"]
                            );
                        }
                    }

                    if (!empty($arFields) && is_array($arFields))
                    {
                        $arFields["CAN_BUY"] = "Y";
                        $arFields["ORDER_ID"] = $orderID;
                        $needSaveCoupons = true;
                    }
                    else
                    {
                        $arFields = array(
                            'CAN_BUY' => 'N'
                        );
                        $removeCoupon = \Bitrix\Sale\DiscountCouponsManager::deleteApplyByProduct(array(
                            'MODULE' => $arBasket['MODULE'],
                            'PRODUCT_ID' => $arBasket['PRODUCT_ID'],
                            'BASKET_ID' => $arBasket['ID']
                        ));

                        file_put_contents ($_SERVER['DOCUMENT_ROOT'].'/basket_can_buy.log' ,
                            date("Y-m-d H:i:s")." -- BASKET:".$arBasket."\n USER_ID:".$arOrder['USER_ID'].
                            " ".$arFields." FROM FILE:".print_r($last_call['file'], true). " \n" ,
                            FILE_APPEND);
                    }
                }
                else
                {
                    $arFields["ORDER_ID"] = $orderID;
                    $needSaveCoupons = true;
                }

                if (!empty($arFields))
                {
                    $arFields['LID'] = SITE_ID; // при обновлении корзины ставим корзинам на разных сайтах общий SITE_ID
                    if (CSaleBasket::Update($arBasket["ID"], $arFields))
                    {
                        $_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]--;
                    }
                }
            }
        }//end of while

        if ($_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID] < 0)
            $_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID] = 0;

        foreach(GetModuleEvents("sale", "OnBasketOrder", true) as $arEvent)
        {
            ExecuteModuleEventEx($arEvent, array($orderID, $fuserID, $strLang, $arDiscounts));
        }
//все ради того чтобы не вызывать saveApplied до того как вызыватеся DoProcessOrder
        if ($needSaveCoupons && false)
        {
            \Bitrix\Sale\DiscountCouponsManager::finalApply();
            \Bitrix\Sale\DiscountCouponsManager::saveApplied();
        }
        //reservation
        if ($arOrder['RESERVED'] != "Y" && COption::GetOptionString("sale", "product_reserve_condition") == "O")
        {
            if (!CSaleOrder::ReserveOrder($orderID, "Y"))
                return false;
        }
        return true;
    }
}