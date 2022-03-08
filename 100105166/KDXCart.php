<?php
/**
 * Created by:  KODIX 14.07.14 10:16
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Alexander Samakin
 */
IncludeModuleLangFile(__FILE__);
class KDXCart {
    protected $fuser_id;
    protected $items=array();
    protected $order_id="NULL";
    public function __construct($fuser_id=false, $order_id="NULL"){
        if(!CModule::IncludeModule('sale') || !CModule::IncludeModule('iblock') || !CModule::IncludeModule('catalog')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }
        if(!$fuser_id){
            $fuser_id=CSaleBasket::GetBasketUserID(true);
        }
        $this->fuser_id=$fuser_id;
        $this->order_id=$order_id;
        //CSaleBasket::UpdateBasketPrices($fuser_id, SITE_ID);
        $this->items=$this->getAll();
    }


    public static function add($product_id, $quantity=1, $fuser_id=false, $arProps = array()){
        if(!CModule::IncludeModule('sale') || !CModule::IncludeModule('iblock') || !CModule::IncludeModule('catalog')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }
        $product_id=intval($product_id);
        $quantity=intval($quantity);
        $fuser_id=intval($fuser_id);
        if(!$product_id)
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_CART_ADD_WRONG_PRODUCT_ID'));
        if($quantity<=1)
            $quantity=1;
        /**
         * Нам необходимо узнать количество товара в корзине и доступное количество в каталоге,
         * чтобы в корзину можно было положить только доступное
         */

        $basketItemQuantity = self::getQuantityByProduct($product_id);


        $arSelect = array(
            "ID",
            'IBLOCK_ID',
            'XML_ID',
            "NAME",
            "PROPERTY_CML2_LINK",
            'PROPERTY_COLOR',
            "CATALOG_GROUP_".KDXSettings::getSetting("RETAIL_PRICE_ID"),
            "CATALOG_GROUP_".KDXSettings::getSetting("BASE_PRICE_ID"),
            'CATALOG_WEIGHT',
            'PREVIEW_PICTURE',
            'DETAIL_PICTURE',
            'DETAIL_PAGE_URL',
        );

        prepareLangSelect($arSelect);

        $sku=CIblockElement::GetList(
            array("SORT"=>"ASC"),
            array("ID"=>$product_id,'IBLOCK_ID' => KDXSettings::getSetting('SKU_IBLOCK_ID')),
            false,
            false,
            $arSelect
        )->GetNext();

        if(intval($sku['CATALOG_QUANTITY']) < $quantity + $basketItemQuantity )
            return false;

        if(!$sku["ID"])
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_CART_ADD_WRONG_PRODUCT_ID'));

        prepareLangFields($sku);

        //Если в корзину кладется торговое предложение, а не сам товар
        if($sku["PROPERTY_CML2_LINK_VALUE"]){
            $arSelect[]='PROPERTY_CML2_ARTICLE';
            $arSelect[]='CODE';
            $arSelect[]='PROPERTY_GALLERY';


            $product=CIblockElement::GetList(
                array("SORT"=>"ASC"),
                array(
                    "ID"=>$sku["PROPERTY_CML2_LINK_VALUE"],
                    'IBLOCK_ID' => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
                ),
                false,
                false,
                $arSelect
            )->GetNext();

            prepareLangFields($product);

            /*
             * торговое предложение ВСЕГДА должно иметь свою цену, иначе будут проблемы расчетом
             * возможности покупки торгового предложения для товара (CAN_BY всегда будет N)
             */
            if(!$sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")] || !$sku["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")]){
                CPrice::Add(array(
                    "PRODUCT_ID" => $sku["ID"],
                    "CATALOG_GROUP_ID" => KDXSettings::getSetting("BASE_PRICE_ID"),
                    "PRICE" => $product["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")],
                    "CURRENCY" => $product["CATALOG_CURRENCY_".KDXSettings::getSetting("BASE_PRICE_ID")],
                ));
                CPrice::Add(array(
                    "PRODUCT_ID" => $sku["ID"],
                    "CATALOG_GROUP_ID" => KDXSettings::getSetting("RETAIL_PRICE_ID"),
                    "PRICE" => $product["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")],
                    "CURRENCY" => $product["CATALOG_CURRENCY_".KDXSettings::getSetting("RETAIL_PRICE_ID")],
                ));

                $sku["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")] = $product["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")];
                $sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")] = $product["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")];
                $sku["CATALOG_CURRENCY_".KDXSettings::getSetting("RETAIL_PRICE_ID")] = $product["CATALOG_CURRENCY_".KDXSettings::getSetting("RETAIL_PRICE_ID")];
                $sku["CATALOG_CURRENCY_".KDXSettings::getSetting("BASE_PRICE_ID")] = $product["CATALOG_CURRENCY_".KDXSettings::getSetting("BASE_PRICE_ID")];
            }

            $arFields = array(
                "PRODUCT_ID" => $sku["ID"],
                "PRODUCT_PRICE_ID" => KDXSettings::getSetting("RETAIL_PRICE_ID"),
                "PRICE" => $sku["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")],
                "CURRENCY" => $sku["CATALOG_CURRENCY_".KDXSettings::getSetting("RETAIL_PRICE_ID")],
                "QUANTITY" => $quantity,
                "LID" => SITE_ID,
                "DELAY" => "N",
                "NAME" => $product["NAME"],
                //"FUSER_ID"=>$fuser_id ? $fuser_id : CSaleBasket::GetBasketUserID(true),
                'MODULE' => 'kodix.sale',
                "PRODUCT_PROVIDER_CLASS"=>"KDXProductProvider",
                'PRODUCT_XML_ID'=>$sku['XML_ID'],
                'CATALOG_XML_ID'=>$sku['IBLOCK_EXTERNAL_ID'],
                "WEIGHT"=>$sku["CATALOG_WEIGHT"],
                'DETAIL_PAGE_URL' => $product['DETAIL_PAGE_URL'],
            );

            $arFields["PROPS"][]=array(
                "NAME" => 'Детальная страница',
                "CODE" => 'DETAIL_PAGE_URL',
                "VALUE" => $product['DETAIL_PAGE_URL'],
            );
            $arFields["PROPS"][]=array(
                "NAME" => 'Id товара',
                "CODE" => 'PARENT_ID',
                "VALUE" => $sku["PROPERTY_CML2_LINK_VALUE"],
            );
            $arFields["PROPS"][]=array(
                "NAME" => 'Базовая цена',
                "CODE" => 'BASE_PRICE_P',
                "VALUE" => $sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")],
            );

            if($sku['DETAIL_PICTURE'])
            {
                $arFields["PROPS"][]=array(
                    "NAME" => 'Картинка для анонса',
                    "CODE" => 'PREVIEW_PICTURE',
                    "VALUE" => $sku['DETAIL_PICTURE'],
                );
            }
            elseif($product['DETAIL_PICTURE'])
            {
                $arFields["PROPS"][]=array(
                    "NAME" => 'Картинка для анонса',
                    "CODE" => 'PREVIEW_PICTURE',
                    "VALUE" => $product['DETAIL_PICTURE'],
                );
            }
            else
            {
                $arFields["PROPS"][]=array(
                    "NAME" => 'Картинка для анонса',
                    "CODE" => 'PREVIEW_PICTURE',
                    "VALUE" => reset($product['PROPERTY_GALLERY_VALUE']),
                );
            }

        }else{
            //Если у торгового предложения нет привязки к товару, считаем, что оно и есть товар
            $arFields = array(
                "PRODUCT_ID" => $sku["ID"],
                "PRODUCT_PRICE_ID" => KDXSettings::getSetting("RETAIL_PRICE_ID"),
                "PRICE" => $sku["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")],
                "CURRENCY" => $sku["CATALOG_CURRENCY_".KDXSettings::getSetting("RETAIL_PRICE_ID")],
                "QUANTITY" => $quantity,
                "LID" => SITE_ID,
                "DELAY" => "N",
                "NAME" => $sku["NAME"],
                //"FUSER_ID"=>$fuser_id ? $fuser_id : CSaleBasket::GetBasketUserID(true),
                'MODULE' => 'catalog',
                "PRODUCT_PROVIDER_CLASS"=>"KDXProductProvider",
                "WEIGHT"=>$sku["CATALOG_WEIGHT"],
                'PRODUCT_XML_ID'=>$sku['XML_ID'],
                'CATALOG_XML_ID'=>$sku['IBLOCK_EXTERNAL_ID'],
                'DETAIL_PAGE_URL' => $sku['DETAIL_PAGE_URL'],
            );

            $arFields["PROPS"][]=array(
                "NAME" => 'Детальная страница',
                "CODE" => 'DETAIL_PAGE_URL',
                "VALUE" => $sku['DETAIL_PAGE_URL'],
            );

            $arFields["PROPS"][]=array(
                "NAME" => 'Id товара',
                "CODE" => 'PARENT_ID',
                "VALUE" => $sku["PROPERTY_CML2_LINK_VALUE"],
            );

            $arFields["PROPS"][]=array(
                "NAME" => 'Базовая цена',
                "CODE" => 'BASE_PRICE_P',
                "VALUE" => $sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")],
            );

            if($sku['DETAIL_PICTURE'])
            {
                $arFields["PROPS"][]=array(
                    "NAME" => 'Картинка для анонса',
                    "CODE" => 'PREVIEW_PICTURE',
                    "VALUE" => $sku['DETAIL_PICTURE'],
                );
            }
            else
            {
                $arFields["PROPS"][]=array(
                    "NAME" => 'Картинка для анонса',
                    "CODE" => 'PREVIEW_PICTURE',
                    "VALUE" => reset($sku['PROPERTY_GALLERY_VALUE']),
                );
            }
        }

        if(is_array($arProps) && count($arProps) > 0)
        {
            if(!in_array('SIZE',$arProps))
                $arProps[] = 'SIZE';
            if(!in_array('COLOR',$arProps))
                $arProps[] = 'COLOR';
            if(!in_array('CML2_ARTICLE',$arProps))
                $arProps[] = 'CML2_ARTICLE';
        }
        else
        {
            $arProps = array('SIZE','COLOR','CML2_ARTICLE');
        }

        $dbProps = CIBlockElement::GetProperty($sku['IBLOCK_ID'],$sku["ID"]);

        while($arProp = $dbProps->Fetch())
        {
            if(in_array($arProp['CODE'],$arProps))
            {
                $arFields["PROPS"][]=array(
                    "NAME" => $arProp['NAME'],
                    "CODE" => $arProp['CODE'],
                    "VALUE" => $arProp['VALUE']?:($product['PROPERTY_'.$arProp['CODE'].'_VALUE']?:''),
                );
            }
        }
        foreach(GetModuleEvents("kodix.sale", "OnBeforeBasketAdd", true) as $arEvent)
            if (ExecuteModuleEventEx($arEvent, Array(&$arFields,$sku,$product))===false)
                return false;

        $record=CSaleBasket::Add($arFields);
        
        CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(true), SITE_ID);
        unset($_SESSION["ORDERING"]["STEP"]);
        return $record;
    }

    protected function getAll(){
        $result=array();
        $filter=array(
            "ORDER_ID"=>$this->order_id,
            "FUSER_ID"=>$this->fuser_id,
        );

        // комментируем для сквозной корзины

        /*if((!defined('ADMIN_SECTION') || !ADMIN_SECTION) && $this->order_id == "NULL"){
            $filter['LID']=SITE_ID;
        }*/


        $res=CSaleBasket::GetList(array("NAME"=>"ASC"), $filter);
        while($i=$res->GetNext()){
            $result[$i["ID"]]=$i;
        }
        $props=self::getCartProps(array_keys($result));
        foreach($result as $id=>$item){
            foreach($props[$id] as $code=>$prop){
                $result[$id]["PROPS"][$code]=$prop["VALUE"];
            }
        }
        return $result;
    }

    public function getAvailable(){
        $tmp=array();
        foreach($this->items as $i_id=>$i){
            if($i["CAN_BUY"]!="Y")
                continue;
            $tmp[$i_id]=$i;

        }
        return $tmp;
    }

    public function getUnavailable(){
        $tmp=array();
        foreach($this->items as $i_id=>$i){
            if($i["CAN_BUY"]!="N")
                continue;
            $tmp[$i_id]=$i;

        }
        return $tmp;
    }

    public static function getCartProps($record_id=false){
        if(!intval($record_id) || empty($record_id)){
            return false;
        }
        $res = CSaleBasket::GetPropsList(
            array(
                "SORT" => "ASC",
                "NAME" => "ASC"
            ),
            array("BASKET_ID" => $record_id)
        );
        $result=array();
        while ($prop = $res->GetNext()) {
            $result[$prop["BASKET_ID"]][$prop["CODE"]]=$prop;
        }
        return $result;
    }


    public function getProducts(){
        $skus=array();
        foreach($this->items as $item){
            $skus[]=$item["PRODUCT_ID"];
        }
        $skus=array_unique($skus);
        if(empty($skus))
            return false;

        $select=array(
            "ID", "NAME", "CODE", "XML_ID", "PREVIEW_TEXT", "DETAIL_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE",
            "PROPERTY_CML2_MANUFACTURER", "PROPERTY_SIZES", "PROPERTY_COLOR", "PROPERTY_SCOLOR", "PROPERTY_GALLERY",
            "PROPERTY_GRID", "PROPERTY_CML2_ARTICLE", "PROPERTY_CODE", "PROPERTY_GROUP_SORT", "PROPERTY_SEARCH_NAME",
            "PROPERTY_SALE", "DETAIL_PAGE_URL",
            "CATALOG_GROUP_".KDXSettings::getSetting("BASE_PRICE_ID"), "CATALOG_GROUP_".KDXSettings::getSetting("RETAIL_PRICE_ID")
        );
        $products=array();
        $res=CIblockElement::GetList(array(), array(
            "ID"=>$skus,
            "!PROPERTY_CML2_LINK"=>false
        ), array("ID", "PROPERTY_CML2_LINK"));
        while($p=$res->GetNext()){
            $products[$p["PROPERTY_CML2_LINK_VALUE"]]["SKU_ID"][]=$p["ID"];
        }

        $res=CIblockElement::GetList(array(), array(
            "ID"=>array_keys($products)
        ), false, false, $select);
        while($p=$res->GetNext()){
            $p["PICTURES"]=KDXGallery::sortGallery($p["PROPERTY_GALLERY_VALUE"], $p["PROPERTY_GALLERY_DESCRIPTION"]);
            $products[$p["ID"]]["PRODUCT"]=$p;
        }
        $result=array();
        foreach($products as $p_id=>$p){
            foreach($p["SKU_ID"] as $sku){
                $result[$sku]=$p["PRODUCT"];
            }
        }
        return $result;
    }

    public static function getProdIDsBySkus($arCart, $RR_format=false){
        $skus = array();
        $products=array();
        foreach ($arCart as $key => $value){
            $skus[] = $value['PRODUCT_ID'];
        }
        $res=CIblockElement::GetList(array(), array(
            "ID"=>$skus,
            "!PROPERTY_CML2_LINK"=>false
        ), array("ID", "PROPERTY_CML2_LINK"));
        while($p=$res->GetNext()){
            $products[] = $p["PROPERTY_CML2_LINK_VALUE"];
        }
        if ($RR_format) {
            $rr_params='';
            foreach ($products as $key => $value) {
                $rr_params .= $value . ',';
            }
            $rr_params = mb_substr($rr_params, 0, -1);
            return $rr_params;
        } else {
            return $products;
        }
    }

    public function getPrice(){
        $price=0;
        foreach($this->items as $i){
            $price+=  floatval($i["PRICE"]+$i["DISCOUNT_PRICE"])*intval($i["QUANTITY"]);
        }
        return $price;
    }
    public function getPriceAvailable(){
        $price=0;
        foreach($this->items as $i){
            if($i["CAN_BUY"]!="Y")
                continue;
            $price+=  floatval($i["PRICE"]+$i["DISCOUNT_PRICE"])*intval($i["QUANTITY"]);
        }
        return $price;
    }

    public function getPriceUnAvailable(){
        $price=0;
        foreach($this->items as $i){
            if($i["CAN_BUY"]!="N")
                continue;
            $price+=  floatval($i["PRICE"]+$i["DISCOUNT_PRICE"])*intval($i["QUANTITY"]);
        }
        return $price;
    }

    public function getPriceWithDiscount(){
        $price=0;
        foreach($this->items as $i){
            $price+=  floatval($i["PRICE"])*intval($i["QUANTITY"]);
        }
        return $price;
    }

    public function getAvailablePriceWithDiscount(){
        $price=0;
        foreach($this->items as $i){
            if($i["CAN_BUY"]!="Y")
                continue;
            $price+=  floatval($i["PRICE"])*intval($i["QUANTITY"]);
        }
        return $price;
    }
    public function getUnAvailablePriceWithDiscount(){
        $price=0;
        foreach($this->items as $i){
            if($i["CAN_BUY"]!="N")
                continue;
            $price+=  floatval($i["PRICE"])*intval($i["QUANTITY"]);
        }
        return $price;
    }

    public function getWeight(){
        $price=0;
        foreach($this->items as $i){
            $price+=floatval($i["WEIGHT"])*intval($i["QUANTITY"]);
        }
        return $price;
    }
    public function getWeightAvailable(){
        $price=0;
        foreach($this->items as $i){
            if($i["CAN_BUY"]!="Y")
                continue;
            $price+=floatval($i["WEIGHT"])*intval($i["QUANTITY"]);
        }
        return $price;
    }

    public function getWeightUnAvailable(){
        $price=0;
        foreach($this->items as $i){
            if($i["CAN_BUY"]!="N")
                continue;
            $price+=floatval($i["WEIGHT"])*intval($i["QUANTITY"]);
        }
        return $price;
    }

    public static function IsExistCoupon($code)
    {
        $code = trim($code);
        if (empty($code))
            return false;
        if (!CModule::IncludeModule('sale') || !CModule::IncludeModule('catalog')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }
        /*$rsDiscount = CCatalogDiscount::GetList(array(), array(
            "ACTIVE"  => "Y",
            "SITE_ID" => SITE_ID,
            "COUPON"  => $code
        ));
        return $rsDiscount->SelectedRowsCount() > 0;*/
        $result = \Bitrix\Sale\DiscountCouponsManager::getData($code);

        $active = ($result['ACTIVE'] == 'Y') ? true : false;

        return $active;
    }


    public static function applyCoupon($code){

        if(!CModule::IncludeModule('sale') || !CModule::IncludeModule('catalog')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }

        foreach (array($code,$code.'_'.SITE_ID) as $coupon) {
            if (self::IsExistCoupon($coupon)) {
                \Bitrix\Sale\DiscountCouponsManager::add($coupon);
                CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(true), SITE_ID);
                unset($_SESSION["ORDERING"]["STEP"]);
                if($coupon!=$code){
                    $_SESSION['CATALOG_USER_COUPON_ALIAS'][$coupon]=$code;
                }
                return true;
            }
        }

        return false;
    }

    public function getAppliedCoupons(){
        $tmp=array();
        foreach($this->items as $i_id=>$i){
            if($i["CAN_BUY"]!="Y" || !trim($i['DISCOUNT_COUPON']))
                continue;
            $tmp[$i_id]=$i['DISCOUNT_COUPON'];

        }
        return $tmp;
    }


    public static function getCoupons()
    {
        return \Bitrix\Sale\DiscountCouponsManager::get();
    }

    public static function ClearCoupon()
    {
        if(CModule::IncludeModule('sale'))
        {
            \Bitrix\Sale\DiscountCouponsManager::clear(true);
            unset($_SESSION["ORDERING"]["STEP"]);
        }
    }

    public function getDiscount(){
        $result = array();
        foreach($this->items as &$item){
            if($item["CAN_BUY"]=="Y"){
                if(intval($item['DISCOUNT_PRICE'])){
                    $result[$item['DISCOUNT_NAME']]=array(
                        'DISCOUNT_PRICE'=>$item['DISCOUNT_PRICE'],
                        'DISCOUNT_NAME'=>$item['DISCOUNT_NAME'],
                        'DISCOUNT_VALUE'=>$item['DISCOUNT_VALUE'],
                        'DISCOUNT_COUPON'=>$item['DISCOUNT_COUPON'],
                    );
                }
            }
        }
        return array_values($result);
    }

    public function clearCart(){
        unset($_SESSION["ORDERING"]["STEP"]);
        return CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID(true));
    }

    public function delete($record_id){
        CSaleBasket::Delete($record_id);
        CSaleBasket::UpdateBasketPrices($this->fuser_id, SITE_ID);
        unset($_SESSION["ORDERING"]["STEP"]);
    }

    public static function getQuantityByProduct($productID = 0)
    {
        if(!CModule::IncludeModule('sale') || !CModule::IncludeModule('iblock') || !CModule::IncludeModule('catalog')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }
        $productID = intval($productID);

        if($productID == 0)
            return false;

        $arBasketItemQuantity = CSaleBasket::Getlist(
            array(),
            array(
                "ORDER_ID" => 'NULL',
                "FUSER_ID" => CSaleBasket::GetBasketUserID(true),
            //    "LID" => SITE_ID,
                'PRODUCT_ID' => $productID
            ),
            false,
            false,
            array('QUANTITY')
        )->Fetch();


        return intval($arBasketItemQuantity['QUANTITY']);
    }

    public static function getQuantityByProductOffers($productID = 0, $offerCount = 0)
    {
        if(!CModule::IncludeModule('sale') || !CModule::IncludeModule('iblock') || !CModule::IncludeModule('catalog')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }
        $productID = intval($productID);

        if($productID == 0)
            return false;

        $mainProductId = 0;
        $resBasketItem = CSaleBasket::Getlist(array(), array('ORDER_ID'=>'NULL', 'FUSER_ID'=>CSaleBasket::GetBasketUserID(true)), false, false, array('*'));
        if ($arBasketItem = $resBasketItem->Fetch()){
            $resItems = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>KDXSettings::getSetting('SKU_IBLOCK_ID'), 'ID'=>$productID), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_CML2_LINK'));
            if ($arItem = $resItems->Fetch()){
                $mainProductId = $arItem['PROPERTY_CML2_LINK_VALUE'];
            }
        }

        $resAllBasketItems = CSaleBasket::Getlist(array(), array('ORDER_ID'=>'NULL', 'FUSER_ID'=>CSaleBasket::GetBasketUserID(true)), false, false, array('*'));
        while ($arAllBasketItems = $resAllBasketItems->Fetch()){
            $resItems = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>KDXSettings::getSetting('SKU_IBLOCK_ID'), 'ID'=>$arAllBasketItems['PRODUCT_ID']), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_CML2_LINK'));
            if ($arItem = $resItems->Fetch()){
                if ($arItem['PROPERTY_CML2_LINK_VALUE'] == $mainProductId){
                    $offerCount = $offerCount + 1;
                }
            }
        }

        return $offerCount;
    }

    public static function getAvailableQuantityByProduct($productID = 0)
    {
        if(!CModule::IncludeModule('sale') || !CModule::IncludeModule('iblock') || !CModule::IncludeModule('catalog')) {
            throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
        }
        $productID = intval($productID);

        if($productID == 0)
            return false;

        $arSelect = array(
            "ID",
            'IBLOCK_ID',
            'XML_ID',
            "NAME",
            "PROPERTY_CML2_LINK",
            'PROPERTY_COLOR',
            "CATALOG_GROUP_".KDXSettings::getSetting("RETAIL_PRICE_ID"),
            "CATALOG_GROUP_".KDXSettings::getSetting("BASE_PRICE_ID"),
            'CATALOG_WEIGHT',
            'PREVIEW_PICTURE',
            'DETAIL_PICTURE',
            'DETAIL_PAGE_URL',
        );

        $sku=CIblockElement::GetList(
            array("SORT"=>"ASC"),
            array("ID"=>$productID,'IBLOCK_ID' => KDXSettings::getSetting('SKU_IBLOCK_ID')),
            false,
            false,
            $arSelect
        )->GetNext();

        return intval($sku['CATALOG_QUANTITY']) > 0 ? intval($sku['CATALOG_QUANTITY']) : 0;
    }

    public function  getProductsArrayId(){
        $skus=array();
        foreach($this->items as $item){
            $skus[]=$item["PRODUCT_ID"];
        }
        $skus=array_unique($skus);
        if(empty($skus))
            return false;

        $products=array();
        $res=CIblockElement::GetList(array(), array(
            "ID"=>$skus,
            "!PROPERTY_CML2_LINK"=>false
        ), array("ID",'IBLOCK_ID', "PROPERTY_CML2_LINK"));
        while($p=$res->GetNext()){
            $products[]=$p["PROPERTY_CML2_LINK_VALUE"];
        }
        return $products;
    }

    public static function getByOrderId($order_id){
        if(!CModule::IncludeModule('sale')) die('sale module');
        $res = CSaleBasket::GetList(array(),array('ORDER_ID'=>$order_id))->Fetch();
        if($res)
            return new KDXCart($res['FUSER_ID'],$res['ORDER_ID']);
        return false;
    }
}