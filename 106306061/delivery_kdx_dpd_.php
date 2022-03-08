<?php
/**
 * Created by:  KODIX 08.10.14 19:04
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Salnikov Dmitry
 */

CModule::IncludeModule("sale");

class KDXDeliveryDPD
{
    function Init()
    {
        if(defined('ADMIN_SECTION') && ADMIN_SECTION && $_SERVER['SCRIPT_URL']=='/bitrix/admin/sale_delivery_handler_edit.php' && $_GET['SID']=='kdx_dpd'){
            CJSCore::Init(array('kodix_jquery'));
        }
        return array(
            /* Основное описание */
            "SID" => "kdx_dpd",
            "NAME" => "DPD",
            "DESCRIPTION" => "",
            "DESCRIPTION_INNER" => "Доставка курьером",
            "BASE_CURRENCY" => COption::GetOptionString("sale", "default_currency", "EUR"),

            "HANDLER" => __FILE__,

            /* Методы обработчика */
            "DBGETSETTINGS" => array("KDXDeliveryDPD", "GetSettings"),
            "DBSETSETTINGS" => array("KDXDeliveryDPD", "SetSettings"),
            "GETCONFIG" => array("KDXDeliveryDPD", "GetConfig"),

            "COMPABILITY" => array("KDXDeliveryDPD", "Compability"),
            "CALCULATOR" => array("KDXDeliveryDPD", "Calculate"),

            /* Список профилей доставки */
            "PROFILES" => array(
                "courier" => array(
                    "TITLE" => "Delivery",
                    "DESCRIPTION" => "",

                    "RESTRICTIONS_WEIGHT" => array(0), // без ограничений
                    "RESTRICTIONS_SUM" => array(1500), // без ограничений
                ),
            )
        );
    }

    // настройки обработчика
    function GetConfig()
    {
        $arConfig = array(
            "CONFIG_GROUPS" => array(
                "available" => "Доступность доставки",
                "price" => "Стоимость доставки",
                "transit" => "Сроки доставки"
            ),

            "CONFIG" => array(
                'AVAILABLE_GROUPS'=>array(
                    'PRE_TEXT'=>'<div id="kdx_location_group_select">',
                    'POST_TEXT'=>'</div>',
                    "TYPE" => "MULTISELECT",
                    "TITLE" => 'Доставка доступна для следующих групп местоположений',
                    "GROUP" => "available",
                    "VALUES" => array(
                    ),
                ),
                'OVERSIZED'=>array(
                    'PRE_TEXT'=>'<div id="kdx_oversized_checkbox">',
                    'POST_TEXT'=>'</div>',
                    "TYPE" => "CHECKBOX",
                    "TITLE" => 'Доставка доступна для негабаритных товаров',
                    "GROUP" => "available",
                ),
                'CHECK_DISCOUNT'=>array(
                    "TYPE" => "RADIO",
                    "DEFAULT" => "N",
                    "TITLE" => 'При достижении пограничной суммы заказа',
                    "GROUP" => "price",
                    "VALUES"=>array(
                        'N'=>'Ничего не делать',
                        'P'=>'Применять процентную скидку к стоимости доставки',
                        'F'=>'Устанавкливать фиксированную стоимость доставки',
                    )
                ),
                'DISCOUNT_DELIVERY'=>array(
                    "TYPE" => "STRING",
                    "DEFAULT" => "50",
                    "TITLE" => 'Возможная скидка на доставку(% или '.COption::GetOptionString('sale', 'default_currency', 'EUR').')',
                    "GROUP" => "price",
                ),
                'DISCOUNT_SALE_ITEMS'=>array(
                    "TYPE" => "CHECKBOX",
                    "TITLE" => 'Не применять скидку на доставку при наличии товара со скидкой',
                    "GROUP" => "price",
                ),
                'DISCOUNT_SALE_IGNORE_ITEMS'=>array(
                    "TYPE" => "CHECKBOX",
                    "TITLE" => 'Применять скидку если пограничная сумма >= сумме товаров без скидки',
                    "GROUP" => "price",
                ),
                'DISCOUNT_NAMES_ITEMS'=>array(
                    "TYPE" => "STRING",
                    "TITLE" => 'Не применять скидку на доставку, если название товара содержит',
                    "GROUP" => "price",
                ),
                'DISCOUNT_SECTION'=>array(
                    'PRE_TEXT'=>'<script>
                        KDX.$(document).on("ready",function(){
                            var oversized=false;
                            var location_groups=[];
                            function rebuildDeliveryForm(){
                                var countBuilded = 0
                                KDX.$("div.kdx_location_group").closest("tr").hide();
                                for(var key in location_groups){
                                    KDX.$("div.kdx_location_group.kdx_lg_id_"+location_groups[key]).closest("tr").show()
                                    countBuilded++;
                                }
                                if(!oversized){
                                    KDX.$("div.kdx_oversized").closest("tr").hide()
                                }
                                if(countBuilded==0){
                                    KDX.$("div.kdx_location_group.kdx_not_found_any").closest("tr").show()
                                }
                            }
                            KDX.$(document).on("change","#kdx_oversized_checkbox input[type=checkbox]",function(){
                                oversized = KDX.$(this).is(":checked");
                                rebuildDeliveryForm()
                            })

                            KDX.$(document).on("change","#kdx_location_group_select select",function(){
                                location_groups = KDX.$(this).val();
                                rebuildDeliveryForm()
                            })

                            KDX.$("#kdx_location_group_select select").change()
                            KDX.$("#kdx_oversized_checkbox input[type=checkbox]").change()
                        });
                        </script>',
                    "TYPE" => "SECTION",
                    "TITLE" => 'Стоимость и условия скидки:',
                    "GROUP" => "price",
                ),
                'KDX_NOT_FOUNT_ANY_D'=>array(
                    'PRE_TEXT'=>'<div class="kdx_location_group kdx_not_found_any adm-info-message">',
                    'POST_TEXT'=>'</div>',
                    "TYPE"=>"TEXT_CENTERED",
                    "TITLE"=>"Пожалуйста, выбирете доступные для доставки группы местоположений",
                    "GROUP" => "price",
                ),
                'KDX_NOT_FOUNT_ANY_T'=>array(
                    'PRE_TEXT'=>'<div class="kdx_location_group kdx_not_found_any adm-info-message">',
                    'POST_TEXT'=>'</div>',
                    "TYPE"=>"TEXT_CENTERED",
                    "TITLE"=>"Пожалуйста, выбирете доступные для доставки группы местоположений",
                    "GROUP" => "transit",
                )
            ),
        );

        // настройками обработчика в данном случае являются значения стоимости доставки в различные группы местоположений.
        // для этого сформируем список настроек на основе списка групп
        $dbLocationGroups = CSaleLocationGroup::GetList();
        while ($arLocationGroup = $dbLocationGroups->Fetch())
        {
            $arField = array(
                'PRE_TEXT'=>'<div class="kdx_location_group kdx_lg_id_'.$arLocationGroup["ID"].'">',
                'POST_TEXT'=>'</div>',
                "TYPE" => "STRING",
                "DEFAULT" => "300",
                "TITLE" =>
                    "Стоимость доставки в группу \""
                    .$arLocationGroup["NAME"]."\" "
                    ."(".COption::GetOptionString("sale", "default_currency", "EUR").')',
                "GROUP" => "price",
            );
            if(!empty($arConfig["CONFIG"]['AVAILABLE_GROUPS']['VALUES'])){
                $arField['TOP_LINE']='Y';
            }

            $arConfig["CONFIG"]['AVAILABLE_GROUPS']['VALUES'][$arLocationGroup["ID"]]=$arLocationGroup["NAME"];

            $arConfig["CONFIG"]["price_".$arLocationGroup["ID"]] = $arField;
            $arConfig["CONFIG"]["price_for_discount".$arLocationGroup["ID"]] = array(
                'PRE_TEXT'=>'<div class="kdx_location_group kdx_lg_id_'.$arLocationGroup["ID"].'">',
                'POST_TEXT'=>'</div>',
                "TYPE" => "STRING",
                "DEFAULT" => "50000",
                "TITLE" =>
                    "Порог стоимости заказа для скидки на доставку для \""
                    .$arLocationGroup["NAME"]."\" "
                    ."(".COption::GetOptionString("sale", "default_currency", "EUR").')',
                "GROUP" => "price",
            );
            $arConfig["CONFIG"]["transit_".$arLocationGroup["ID"]] = array(
                'PRE_TEXT'=>'<div class="kdx_location_group kdx_lg_id_'.$arLocationGroup["ID"].'">',
                'POST_TEXT'=>'</div>',
                "TYPE" => "STRING",
                "DEFAULT" => "5",
                "TITLE" =>
                    "Срок доставки для \""
                    .$arLocationGroup["NAME"]."\" "
                    ."(в днях)",
                "GROUP" => "transit",
            );

            //негабарит
            $arConfig["CONFIG"]["oversized_".$arLocationGroup["ID"]]=array(
                'PRE_TEXT'=>'<div class="kdx_location_group kdx_oversized kdx_lg_id_'.$arLocationGroup["ID"].'">',
                'POST_TEXT'=>'</div>',
                "TYPE" => "TEXT_CENTERED",
                "TITLE" =>
                    "Для негабаритной доставки в  \""
                    .$arLocationGroup["NAME"]."\": ",
                "GROUP" => "price",
            );
            $arConfig["CONFIG"]["price_oversized_".$arLocationGroup["ID"]] = array(
                'PRE_TEXT'=>'<div class="kdx_location_group kdx_oversized kdx_lg_id_'.$arLocationGroup["ID"].'">',
                'POST_TEXT'=>'</div>',
                "TYPE" => "STRING",
                "DEFAULT" => "300",
                "TITLE" =>
                    "Стоимость доставки в группу \""
                    .$arLocationGroup["NAME"]."\" "
                    ."(".COption::GetOptionString("sale", "default_currency", "EUR").')',
                "GROUP" => "price",
            );
            $arConfig["CONFIG"]["price_oversized_for_discount".$arLocationGroup["ID"]] = array(
                'PRE_TEXT'=>'<div class="kdx_location_group kdx_oversized kdx_lg_id_'.$arLocationGroup["ID"].'">',
                'POST_TEXT'=>'</div>',
                "TYPE" => "STRING",
                "DEFAULT" => "50000",
                "TITLE" =>
                    "Порог стоимости заказа для скидки на доставку для \""
                    .$arLocationGroup["NAME"]."\" "
                    ."(".COption::GetOptionString("sale", "default_currency", "EUR").')',
                "GROUP" => "price",
            );
            $arConfig["CONFIG"]["transit_oversized_".$arLocationGroup["ID"]] = array(
                'PRE_TEXT'=>'<div class="kdx_location_group kdx_oversized kdx_lg_id_'.$arLocationGroup["ID"].'">',
                'POST_TEXT'=>'</div>',
                "TYPE" => "STRING",
                "DEFAULT" => "5",
                "TITLE" =>
                    "Срок доставки негабарита для \""
                    .$arLocationGroup["NAME"]."\" "
                    ."(в днях)",
                "GROUP" => "transit",
            );
        }

        return $arConfig;
    }

    // подготовка настроек для занесения в базу данных
    function SetSettings($settings)
    {
        if($settings['CHECK_DISCOUNT']=='P')
            $settings['DISCOUNT_DELIVERY']=intval($settings['DISCOUNT_DELIVERY'])>100?100:intval($settings['DISCOUNT_DELIVERY']);
        elseif($settings['CHECK_DISCOUNT']=='F')
            $settings['DISCOUNT_DELIVERY']=intval($settings['DISCOUNT_DELIVERY']);
        return serialize($settings);
    }

    // подготовка настроек, полученных из базы данных
    function GetSettings($strSettings)
    {
        // вернем десериализованный массив настроек
        return unserialize($strSettings);
    }


    function Compability($arOrder, $arConfig)
    {
        $arConfig['OVERSIZED']['VALUE']=in_array($arConfig['OVERSIZED']['VALUE'],array('N','Y'))?$arConfig['OVERSIZED']['VALUE']:'N';
        if($arOrder['ORDER_PROPS']['OVERSIZED']['VALUE']=='Y' && $arConfig['OVERSIZED']['VALUE']!='Y'){
            return array();
        }
        $found = false;
        $arLocations = collectParentsLocations($arOrder["LOCATION_TO"]);
        $dbLocationGroups = CSaleLocationGroup::GetLocationList(array("LOCATION_ID" => $arLocations));
        while ($arLocationGroup = $dbLocationGroups->Fetch())
        {
            if(in_array($arLocationGroup['LOCATION_GROUP_ID'],$arConfig['AVAILABLE_GROUPS']['VALUE'])){
                $found=true;
                break;
            }
        }
        if(!$found){
            return array();
        }
        $init = self::Init();
        return array_keys($init['PROFILES']); // в противном случае вернем массив, содержащий идентфиикатор единственного профиля доставки
    }

    // собственно, рассчет стоимости
    function Calculate($profile, $arConfig, $arOrder, $STEP, $TEMP = false)
    {
        $saleItemsAvailable = false;
        
        if($arOrder['ORDER_PROPS']['OVERSIZED']['VALUE']=='Y' && $arConfig['OVERSIZED']['VALUE']!='Y'){
            return array(
                'RESULT' =>'ERROR',
                'TEXT' => 'Не достпуная доставкка'
            );
        }
        $found = false;
        $arLocations = collectParentsLocations($arOrder["LOCATION_TO"]);
        $dbLocationGroups = CSaleLocationGroup::GetLocationList(array("LOCATION_ID" => $arLocations));
        while ($arLocationGroup = $dbLocationGroups->Fetch())
        {
            if(in_array($arLocationGroup['LOCATION_GROUP_ID'],$arConfig['AVAILABLE_GROUPS']['VALUE'])){
                $found=true;
                break;
            }
        }
        if(!$found){
            return array(
                'RESULT' =>'ERROR',
                'TEXT' => 'Не достпуная доставкка'
            );
        }

        $overs='';
        if($arOrder['ORDER_PROPS']['OVERSIZED']['VALUE']=='Y'){
            $overs = "oversized_";
        }

        if ($arConfig['DISCOUNT_SALE_ITEMS']['VALUE'] == 'Y'){
            $dbBasketItems = CSaleBasket::GetList(array(), array('FUSER_ID'=>CSaleBasket::GetBasketUserID(true), 'LID'=>SITE_ID, 'ORDER_ID'=>'NULL'), false, false, array('PRODUCT_ID'));
            while ($basketItem = $dbBasketItems->Fetch()){
                $resOffers = CCatalogSku::GetProductInfo($basketItem['PRODUCT_ID']);
                if (is_array($resOffers)){
                    $productId = $resOffers['ID'];
                }
                $price1 = 0;
                $price2 = 0;
                $resPrice1 = CPrice::GetList(array(), array('PRODUCT_ID'=>$basketItem['PRODUCT_ID'], 'CATALOG_GROUP_ID'=>1));
                $resPrice2 = CPrice::GetList(array(), array('PRODUCT_ID'=>$basketItem['PRODUCT_ID'], 'CATALOG_GROUP_ID'=>2));
                if ($arPrice1 = $resPrice1->Fetch()){
                    $price1 = $arPrice1['PRICE'];
                }
                if ($arPrice2 = $resPrice2->Fetch()){
                    $price2 = $arPrice2['PRICE'];
                }
                if ($price1 <> $price2){
                    $saleItemsAvailable = true;
                }
            }
        }
        if ($arConfig['DISCOUNT_SALE_IGNORE_ITEMS']['VALUE'] == 'Y'){
            $dsiSum = 0;
            $dbBasketItems = CSaleBasket::GetList(array(), array('FUSER_ID'=>CSaleBasket::GetBasketUserID(true), 'LID'=>SITE_ID, 'ORDER_ID'=>'NULL'), false, false, array('PRODUCT_ID'));
            while ($basketItem = $dbBasketItems->Fetch()){
                $resOffers = CCatalogSku::GetProductInfo($basketItem['PRODUCT_ID']);
                if (is_array($resOffers)){
                    $productId = $resOffers['ID'];
                }
                $price1 = 0;
                $price2 = 0;
                $resPrice1 = CPrice::GetList(array(), array('PRODUCT_ID'=>$basketItem['PRODUCT_ID'], 'CATALOG_GROUP_ID'=>1));
                $resPrice2 = CPrice::GetList(array(), array('PRODUCT_ID'=>$basketItem['PRODUCT_ID'], 'CATALOG_GROUP_ID'=>2));
                if ($arPrice1 = $resPrice1->Fetch()){
                    $price1 = $arPrice1['PRICE'];
                }
                if ($arPrice2 = $resPrice2->Fetch()){
                    $price2 = $arPrice2['PRICE'];
                }
                if ($price1 == $price2){
                    $dsiSum += $price1;
                }
            }
            if ($dsiSum < $arConfig["price_".$overs."for_discount".$arLocationGroup['LOCATION_GROUP_ID']]['VALUE']){
                $saleItemsAvailable = true;
            }
        }
        if ($arConfig['DISCOUNT_NAMES_ITEMS']['VALUE'] !== ''){
            $dbBasketItems = CSaleBasket::GetList(array(), array('FUSER_ID'=>CSaleBasket::GetBasketUserID(true), 'LID'=>SITE_ID, 'ORDER_ID'=>'NULL'), false, false, array('PRODUCT_ID'));
            while ($basketItem = $dbBasketItems->Fetch()){
                $resOffers = CCatalogSku::GetProductInfo($basketItem['PRODUCT_ID']);
                if (is_array($resOffers)){
                    $productId = $resOffers['ID'];
                }
                $filterName = explode(',', $arConfig['DISCOUNT_NAMES_ITEMS']['VALUE'], 2);
                $resItems = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>KDXSettings::getSetting('CATALOG_IBLOCK_ID'), 'ID'=>$productId, '%NAME'=>$filterName), false, array(), array('IBLOCK_ID', 'ID', 'NAME'));
                if ($arItem = $resItems->Fetch()){
                    $saleItemsAvailable = true;
                }
            }
        }
        
        $price = $arConfig["price_".$overs.$arLocationGroup['LOCATION_GROUP_ID']]['VALUE'];
        if($arOrder['PRICE']>=$arConfig["price_".$overs."for_discount".$arLocationGroup['LOCATION_GROUP_ID']]['VALUE']){
            switch($arConfig['CHECK_DISCOUNT']['VALUE']){
                case 'P':
                    if ($saleItemsAvailable){
                        $price = $price;
                    } else {
                        $price = $price - $price*(intval($arConfig["DISCOUNT_DELIVERY"]['VALUE'])/100);
                    }
                    break;
                case 'F':
                    $price = intval($arConfig["DISCOUNT_DELIVERY"]['VALUE']);
                    break;
                default:
                    //$price = $arConfig["price".$arLocationGroup['LOCATION_GROUP_ID']];
                    break;
            }
        }
        $transit = $arConfig["transit_".$overs.$arLocationGroup['LOCATION_GROUP_ID']]['VALUE'];
        return array(
            'RESULT'    => 'OK',
            'VALUE'     => $price,
            'TRANSIT' => ($transit?:'3-7')
        );
    }
}
?>