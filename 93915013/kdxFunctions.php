<?php
/**
 * Date: 23.03.2015
 * Time: 19:31
 */

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

function getHtmlBasketMini()
{
    ob_start();
    global $APPLICATION;
    $APPLICATION->IncludeComponent('kodix:cart.mini','new',array(
        'PATH_TO_CART' => '/cart/',
        'PATH_TO_ORDER' => '/cart/',
    ),
        false
    );
    return ob_get_clean();
}

/**
 * Отрисовывает функционал оплаты. Это может быть кнопка оплатить, подгруженный функционал, кнопка распечатать, или просто ссылка
 * @param KDXOrder $Order Объект заказа
 * @return bool
 * @use GLOBAL $APPLICATION
 *
 */
function showPayButton(KDXOrder $Order){

    global $APPLICATION;
    static $PaymentFormat=array();
    static $PaymentPayLimit=array();
    if(empty($PaymentFormat)){
        $PaymentFormat=KDXSettings::getSetting('PAYMENT_FORMAT');
    }
    if(empty($PaymentPayLimit)){
        $PaymentPayLimit=KDXSettings::getSetting('PAYMENT_PAY_LIMIT');
    }
    if(empty($PaymentFormat)){
        return false;
    }
    switch  ($PaymentFormat[$Order->pay_system_id]){
        case 'hide':
            break;
        case 'print':
            if(SITE_TEMPLATE_ID == 'ajax'){
                $script = CJSCore::getExtInfo('kodix_print_page');
                ?><script src="<?=$script['js']?>"></script><?
            }
            else{
                CJSCore::Init(array('kodix_print_page'));
            }
            ?>
            <div class="">

                <div class="fs17"><?=GetMessage('KF_PRICE')?>: <b><?=KDXCurrency::convertAndFormat($Order->price,KDXCurrency::$CurrentCurrency)?></b></div>

                <a href="/ajax/cart/payment.php?ORDER_ID=<?=$Order->id?>" class="btn_profile mod_std btnPrint"><?=GetMessage('KF_TO_PRINT_PAGE')?></a>
            </div>
            <?break;
        case 'include':
            $bUseAccountNumber = (COption::GetOptionString("sale", "account_number_template", "") !== "") ? true : false;

            $ORDER_ID = $Order->id;

            $arOrder = false;
            if ($ORDER_ID)
            {
                $dbOrder = CSaleOrder::GetList(
                    array("DATE_UPDATE" => "DESC"),
                    array(
                        "LID" => SITE_ID,
                        "USER_ID" => IntVal($GLOBALS["USER"]->GetID()),
                        "ID" => $ORDER_ID
                    )
                );

                $arOrder = $dbOrder->GetNext();
            }

            if (!$arOrder)
            {
                $dbOrder = CSaleOrder::GetList(
                    array("DATE_UPDATE" => "DESC"),
                    array(
                        "LID" => SITE_ID,
                        "USER_ID" => IntVal($GLOBALS["USER"]->GetID()),
                        "ID" => $ORDER_ID
                    )
                );

                $arOrder = $dbOrder->GetNext();
            }

            if ($arOrder)
            {
                $dbPaySysAction = CSalePaySystemAction::GetList(
                    array(),
                    array(
                        "PAY_SYSTEM_ID" => $arOrder["PAY_SYSTEM_ID"],
                        "PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"]
                    ),
                    false,
                    false,
                    array("ACTION_FILE", "PARAMS", "ENCODING")
                );

                if ($arPaySysAction = $dbPaySysAction->Fetch())
                {
                    if(array_key_exists($Order->pay_system_id, $PaymentPayLimit)) {
                        $payLimit = $PaymentPayLimit[$Order->pay_system_id];
                        if($payLimit > 0 && $arOrder["PRICE"] > $payLimit) {
                            return;
                        }
                    }
                    if (strlen($arPaySysAction["ACTION_FILE"]) > 0)
                    {
                        CSalePaySystemAction::InitParamArrays($arOrder, 0, $arPaySysAction["PARAMS"]);

                        $pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];
                        $pathToAction = rtrim(str_replace("\\", "/", $pathToAction), "/");

                        if (file_exists($pathToAction))
                        {
                            if (is_dir($pathToAction))
                            {
                                if (file_exists($pathToAction."/payment.php"))
                                    include($pathToAction."/payment.php");
                            }
                            else
                            {
                                include($pathToAction);
                            }
                        }
                    }
                }
            }
            break;
        default:?>
            <div class="col-md-4">

<!--                <div class="fs17">--><?//=GetMessage('KF_PRICE')?><!--: <b>--><?//=KDXCurrency::format($Order->price)?><!--</b>-->
<!--                    --><?//if(KDXCurrency::$CurrentCurrency != CURRENCY_DEFAULT){?>
<!--                        (--><?//=KDXCurrency::convertAndFormat($Order->price,KDXCurrency::$CurrentCurrency)?><!--)-->
<!--                    --><?//}?>
<!--                </div>-->

                <a href="/payment/?ORDER_ID=<?=$Order->id?>" class="btn btn--primary btn_buy mod_std"><?=GetMessage('KF_GO_TO_PAY')?></a>
                <?if(KDXCurrency::$CurrentCurrency != CURRENCY_DEFAULT){?>
                    <div class="order_note"><?=GetMessage('KF_CURRENCY_NOTE',array(
                            'EURO_PRICE' => KDXCurrency::format(1.0),
                            'CURRENCY_PRICE' => KDXCurrency::convertAndFormat(1.0,KDXCurrency::$CurrentCurrency),
                        ))?></div>
                <?}?>

            </div>
            <?break;
            break;
    }
}


/**
 * @param bool|int|array $ID список id товаров, которые следует обновить
 * в карточку товара заносятся:
 *  перечень достпных размеров string multiply
 *  перечень доступных цветов string multiply
 *  Максимальная скидка int
 *  Диапазоны цен 4 int поля
 *  Наличие фотографий для цветов SKU int
 */
function setAvailableSizes($ID = false){
//    AddMessage2Log('setAvailableSizes');
//    AddMessage2Log($ID);
//    echo '<pre>';
//    print_r ($ID);
//    echo '</pre>';
//    die();
$fileLog = '/var/www/itkkit/bitrix/admin/tools/check_available_detail.log';
   
    if(!CModule::IncludeModule('iblock')) die('iblock module');
    $brands = KDXSaleDataCollector::getBrands();
    $arBadges=array();
    $res = CIBlockPropertyEnum::GetList(array(),array(
        "IBLOCK_ID" => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
        'CODE'=>'BADGE'
    ));
    while($fields = $res->Fetch()){
        $arBadges[$fields['VALUE']]=$fields['ID'];
    }

    $arUpdateFields=array();

    $products=array();
    $arFilter = array(
        //"ACTIVE"=>"Y",
        "IBLOCK_ID" => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
    );
    if(is_array($ID) && !empty($ID)){
        $arFilter['=ID']=array_values($ID);
    }
    elseif(intval($ID)){
        $arFilter['=ID']=intval($ID);
    }

    $arSelect = array(
        "ID",
        'IBLOCK_ID',
        'IBLOCK_SECTION_ID',
        'PROPERTY_BADGE',
        'TAGS',
        'PROPERTY_CML2_MANUFACTURER',
        'IBLOCK_SECTION_ID',
        'DATE_ACTIVE_FROM'
    );
    
    $arSelect = array_merge($arSelect, getSearchablePropertiesArray());

    $models=CIblockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while($p=$models->GetNext()){
        
        $arUpdateFields[$p["ID"]]=array(
            'TAGS'=>$p['TAGS'],
            'KDX_SEARCHABLE_CONTENT'=>''
        );
        foreach(getSearchablePropertiesArray() as $code)
        {
            $arUpdateFields[$p["ID"]]['KDX_SEARCHABLE_CONTENT'] .= PHP_EOL . $p[$code.'_VALUE'];
        }
        $arUpdateFields[$p["ID"]]['KDX_SEARCHABLE_CONTENT'].= PHP_EOL . $brands[$p['PROPERTY_CML2_MANUFACTURER_VALUE']]['NAME'];

        $arSection = getFirstLevelSection($p['IBLOCK_SECTION_ID']);

        //массив свойств для каждого элемента (далее мы его изменяем и сохраняем)
        $products[$p["ID"]]=array(
            'SIZES_CLOTHING'=>false,
            'SIZES_TRAINERS'=>false,
            'SIZES_TRAINERS_EU'=>false,
            'SIZES_ACCESSORIES'=>false,
            //'COLORS' => false,
            'RETAIL_PRICE_MAX'=>0,
            'RETAIL_PRICE_MIN'=>false,
            'BASE_PRICE_MAX'=>0,
            'BASE_PRICE_MIN'=>false,
            'MAX_DISCOUNT'=>0,
            'BADGE'=>$p['PROPERTY_BADGE_VALUE'],
            'SECTION_CODE' => strtoupper($arSection['CODE']),
            'SID' => $arSection['ID'],
            'DATE_ACTIVE_FROM' => $p["DATE_ACTIVE_FROM"]
        );

    }
    
    if(!empty($products)){
        $arSelectFields = array(
            "ID",
            "NAME",
            "ACTIVE",
            "PROPERTY_SIZE",
            "PROPERTY_SIZE_EU",
            "PROPERTY_COLOR",
            "CATALOG_QUANTITY",
            "PROPERTY_CML2_LINK",
            "CATALOG_GROUP_" . KDXSettings::getSetting("BASE_PRICE_ID"),
            "CATALOG_GROUP_" . KDXSettings::getSetting("RETAIL_PRICE_ID")
        );
        $arFilter1 = array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => KDXSettings::getSetting('SKU_IBLOCK_ID'),
            "PROPERTY_CML2_LINK" => array_keys($products),
            ">CATALOG_QUANTITY" => 0
        );
        $arOrder = array('CATALOG_QUANTITY' => 'DESC');

        $sku_res=CIblockElement::GetList($arOrder, $arFilter1, false, false, $arSelectFields);
        while($sku=$sku_res->GetNext()){

            $arProduct = &$products[$sku["PROPERTY_CML2_LINK_VALUE"]];

            // Пришлось привязать к ИД, тк код секций может поменяться
            if (!empty($sku["PROPERTY_SIZE_VALUE"]) && $sku['CATALOG_QUANTITY'] > 0) {
                if ($arProduct['SID'] == 351){
                    $code = "SIZES_ACCESSORIES";
                } elseif($arProduct['SID'] == 355){
                    $code = "SIZES_TRAINERS";
                    $code2 = "SIZES_TRAINERS_EU";
                } elseif($arProduct['SID'] == 356){
                    $code = "SIZES_CLOTHING";
                }

                if (!in_array($sku["PROPERTY_SIZE_VALUE"], $arProduct[$code]))
                    $arProduct[$code][] = $sku["PROPERTY_SIZE_VALUE"];
                if (!in_array($sku["PROPERTY_SIZE_EU_VALUE"], $arProduct[$code2]))
                    $arProduct[$code2][] = $sku["PROPERTY_SIZE_EU_VALUE"];
                //$arProduct['SIZES_'.$arProduct['SECTION_CODE']][] = $sku["PROPERTY_SIZE_VALUE"];
            }
            /*if (!empty($sku["PROPERTY_COLOR_VALUE"]) && !in_array($sku["PROPERTY_COLOR_VALUE"],$arProduct['COLORS']) && $sku['CATALOG_QUANTITY'] > 0) {
                $arProduct['COLORS'][] = $sku["PROPERTY_COLOR_VALUE"];
            }*/


            //диапазон цен
            if(intval($sku["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")]) > floatval($arProduct['RETAIL_PRICE_MAX'])){
                $arProduct['RETAIL_PRICE_MAX']=floatval($sku["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")]);
            }
            if(intval($sku["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")]) < floatval($arProduct['RETAIL_PRICE_MIN'])
                || $arProduct['RETAIL_PRICE_MIN'] === false){
                $arProduct['RETAIL_PRICE_MIN']=floatval($sku["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")]);
            }
            if(intval($sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")]) > floatval($arProduct['BASE_PRICE_MAX'])){
                $arProduct['BASE_PRICE_MAX']=floatval($sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")]);
            }
            if(intval($sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")]) < floatval($arProduct['BASE_PRICE_MIN'])
                || $arProduct['BASE_PRICE_MIN'] === false){
                $arProduct['BASE_PRICE_MIN']=floatval($sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")]);
            }

            //скидка
            if(floatval($sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")])
                && floatval($sku["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")])){
                $diff = round((floatval($sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")])-floatval($sku["CATALOG_PRICE_".KDXSettings::getSetting("RETAIL_PRICE_ID")]))/floatval($sku["CATALOG_PRICE_".KDXSettings::getSetting("BASE_PRICE_ID")])*100);
                if($diff>intval($arProduct['MAX_DISCOUNT'])){
                    $arProduct['MAX_DISCOUNT']=$diff;
                }
            }
        }
        foreach($products as $p_id=>&$sync){

            //$filter[">=DATE_ACTIVE_FROM"]=date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), time()-intval(KDXSettings::getSetting("IS_NEW_DAYS_COUNT")*86400));
            //Новинка?
            if(time() < strtotime($sync['DATE_ACTIVE_FROM']) + intval(KDXSettings::getSetting("IS_NEW_DAYS_COUNT")*86400) && !array_key_exists($arBadges['New'], $sync['BADGE']))
                $sync['BADGE'][ $arBadges['New'] ] = 'New';

            if(time() > strtotime($sync['DATE_ACTIVE_FROM']) + intval(KDXSettings::getSetting("IS_NEW_DAYS_COUNT")*86400) && array_key_exists($arBadges['New'], $sync['BADGE']))
                unset($sync['BADGE'][ $arBadges['New'] ]);

            //Распродажа?
            if(intval($sync['MAX_DISCOUNT']) && !array_key_exists($arBadges['Sale'], $sync['BADGE'])) {
                $sync['BADGE'][$arBadges['Sale']] = 'Sale';
                unset($sync['BADGE'][ $arBadges['New'] ]);
                unset($sync['BADGE'][ $arBadges['RE-STOCK'] ]);
            }

            if(!intval($sync['MAX_DISCOUNT']) && array_key_exists($arBadges['Sale'], $sync['BADGE']))
                unset($sync['BADGE'][ $arBadges['Sale'] ]);

            // Free shiping FREE SHIPPING
            if($sync['RETAIL_PRICE_MIN'] > KDXSettings::getSetting('FREE_SHIPING_PRICE') && !array_key_exists($arBadges['FREE SHIPPING'], $sync['BADGE']))
                $sync['BADGE'][ $arBadges['FREE SHIPPING'] ] = 'FREE SHIPPING';

            if($sync['RETAIL_PRICE_MIN'] <= KDXSettings::getSetting('FREE_SHIPING_PRICE') && array_key_exists($arBadges['FREE SHIPPING'], $sync['BADGE']))
                unset($sync['BADGE'][ $arBadges['FREE SHIPPING'] ]);

            $sync['BADGE'] = array_keys($sync['BADGE']);

            if(!is_array($sync['BADGE']) || empty($sync['BADGE']))
                $sync['BADGE'] = false;


            CIblockElement::SetPropertyValuesEx($p_id, KDXSettings::getSetting('CATALOG_IBLOCK_ID'), $sync);
            if ($sync['SID'] == 351){
                $code = "SIZES_ACCESSORIES";
            } elseif($sync['SID'] == 355){
                $code = "SIZES_TRAINERS";
            } elseif($sync['SID'] == 356){
                $code = "SIZES_CLOTHING";
            }
            $is_available = (is_array($sync[$code]) && count($sync[$code]));

            $arUpdFields = array(
                'KDX_STOP'=>'Y',
                'KDX_SEARCHABLE_CONTENT'=>$arUpdateFields[$p_id]['KDX_SEARCHABLE_CONTENT'],
            );
			file_put_contents(
				$fileLog,
				$p_id.'|setAvailableSizes - CHECK'."\n",
				FILE_APPEND | LOCK_EX
			);
            AddMessage2Log($p_id.'|setAvailableSizes - CHECK');
            if($is_available && $arUpdateFields[$p_id]['TAGS']!='AVAILABLE'){
                $arUpdFields['TAGS']='AVAILABLE';
                AddMessage2Log($p_id.'| AVAILABLE - SET ');
                file_put_contents(
                        $fileLog,
                        $p_id.'|setAvailableSizes - AVAILABLE - SET'."\n",
                        FILE_APPEND | LOCK_EX
                );
            }
            elseif(!$is_available && $arUpdateFields[$p_id]['TAGS']=='AVAILABLE'){
                $arUpdFields['TAGS']= false;
                AddMessage2Log($p_id.'| AVAILABLE - DELETE');
                file_put_contents(
                        $fileLog,
                        $p_id.'|setAvailableSizes - AVAILABLE - DELETE'."\n",
                        FILE_APPEND | LOCK_EX
                );
                
                $query = "update b_iblock_element".
                        " set ACTIVE='Y', TAGS = ''".
                        " where ID=".$p_id;
                global $DB;
                $res_sql = $DB->Query($query, true);
                AddMessage2Log ($query);
                AddMessage2Log ($res_sql);
            }

            $elm = new CIBlockElement();
            $elm->Update($p_id,$arUpdFields);
            if ((is_array($ID) && !empty($ID)) || (ctype_digit($ID) && intval($ID))) {
                $oCache = new CPHPCache();
                $oCache->CleanDir('kcache/catalog/detail/'.$p_id);
            }
        }
    }
}

function getFirstLevelSection($sID = 0)
{
    static $sections;
    $sID = intval($sID);

    if(!$sID)
        return false;
    if(!$sections)
        $sections = array();
    if(!$sections[$sID])
        $arSect = $sections[$sID] = CIBlockSection::GetByID($sID)->Fetch();
    else $arSect = $sections[$sID];
    while( $arSect['DEPTH_LEVEL'] > 1 )
    {
        if(!$sections[$arSect['IBLOCK_SECTION_ID']])
            $arSect = $sections[$arSect['IBLOCK_SECTION_ID']] = CIBlockSection::GetByID($arSect['IBLOCK_SECTION_ID'])->Fetch();
        else $arSect = $sections[$arSect['IBLOCK_SECTION_ID']];
    }

    return $arSect;
}

$arSearchableProperties = array();

function getSearchablePropertiesArray()
{
    if(empty($arSearchableProperties))
    {
        $dbProperties = CIBlockProperty::GetList(
            array(),
            array(
                'IBLOCK_ID' => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
                'SEARCHABLE' => 'Y',
            )
        );

        while($arProp = $dbProperties->Fetch())
        {
            $arSearchableProperties[] = 'PROPERTY_' . $arProp['CODE'];
        }
    }

    return $arSearchableProperties;
}

function collectParentsLocations($locationCode = '')
{
    $locationCode = trim($locationCode);

    if(!$locationCode)
        return false;

    if(!CModule::IncludeModule('sale'))
        return false;

    $arIDs = array();

    if(ADMIN_SECTION)
        $arLoc = Bitrix\Sale\Location\LocationTable::getById($locationCode)->fetch();
    else
        $arLoc = Bitrix\Sale\Location\LocationTable::getByCode($locationCode)->fetch();

    while($arLoc !== false)
    {
        $arIDs[] = $arLoc['ID'];
        $arLoc = Bitrix\Sale\Location\LocationTable::getById($arLoc['PARENT_ID'])->fetch();
    }

    return $arIDs;
}

function prepareLangSelect(&$arSelect)
{
    if(!is_array($arSelect))
        $arSelect = array();

    $arSelect[] = 'PROPERTY_EN_NAME';
    $arSelect[] = 'PROPERTY_EN_PREVIEW_TEXT';
    $arSelect[] = 'PROPERTY_EN_DETAIL_TEXT';
}

function prepareLangFields(&$arItem)
{
    if(LANGUAGE_ID == 'en')
    {
        $arItem['NAME'] = $arItem['PROPERTY_EN_NAME_VALUE']?$arItem['PROPERTY_EN_NAME_VALUE']:$arItem['NAME'];
        $arItem['PREVIEW_TEXT'] = $arItem['PROPERTY_EN_PREVIEW_TEXT_VALUE']?$arItem['PROPERTY_EN_PREVIEW_TEXT_VALUE']:$arItem['PREVIEW_TEXT'];
        $arItem['~PREVIEW_TEXT'] = $arItem['PROPERTY_EN_PREVIEW_TEXT_VALUE']?$arItem['PROPERTY_EN_PREVIEW_TEXT_VALUE']:$arItem['~PREVIEW_TEXT'];
        $arItem['DETAIL_TEXT'] = $arItem['PROPERTY_EN_DETAIL_TEXT_VALUE']?$arItem['PROPERTY_EN_DETAIL_TEXT_VALUE']:$arItem['DETAIL_TEXT'];
        $arItem['~DETAIL_TEXT'] = $arItem['PROPERTY_EN_DETAIL_TEXT_VALUE']?$arItem['PROPERTY_EN_DETAIL_TEXT_VALUE']:$arItem['~DETAIL_TEXT'];
    }
}

/**
 * @param $arProduct
 * DETAIL_PICTURE, DETAIL_PAGE_URL, PROPERTY_BADGE_VALUE, PROPERTY_SIZES_VALUE, PROPERTY_CML2_MANUFACTURER_NAME_VALUE,
 * PROPERTY_BASE_PRICE_MIN_VALUE, PROPERTY_RETAIL_PRICE_MIN_VALUE, PROPERTY_RETAIL_PRICE_MAX_VALUE, RETAIL_PRICE_CONVERTED_FORMATED, BASE_PRICE_CONVERTED_FORMATED
 */
function showCatalogItem($arProduct, $params = array())
{
    global $APPLICATION;
    if(empty($params['width'])) $params['width'] = 436;
    if(empty($params['height'])) $params['height'] = 458;

    if(is_array($arProduct['PROPERTY_GALLERY_VALUE']) && !empty($arProduct['PROPERTY_GALLERY_VALUE'])){

        $img_id = current($arProduct['PROPERTY_GALLERY_VALUE']);
        $img = CFile::ResizeImageGet($img_id,array('width'=>$params['width'],'height'=>$params['height']),BX_RESIZE_IMAGE_EXACT);
        $img_2x = CFile::ResizeImageGet($img_id ,array('width'=>$params['width']*2,'height'=>$params['height']*2),BX_RESIZE_IMAGE_EXACT);
        $img_mob = CFile::ResizeImageGet($img_id ,array('width'=>393,'height'=>413),BX_RESIZE_IMAGE_EXACT);
        $img_mob_2x = CFile::ResizeImageGet($img_id ,array('width'=>786,'height'=>826),BX_RESIZE_IMAGE_EXACT);

        $img2_id = next($arProduct['PROPERTY_GALLERY_VALUE']);
        $img2 = CFile::ResizeImageGet($img2_id,array('width'=>$params['width'],'height'=>$params['height']),BX_RESIZE_IMAGE_EXACT);
        $img2_2x = CFile::ResizeImageGet($img2_id,array('width'=>$params['width']*2,'height'=>$params['height']*2),BX_RESIZE_IMAGE_EXACT);
        $img2_mob = CFile::ResizeImageGet($img2_id,array('width'=>393,'height'=>413),BX_RESIZE_IMAGE_EXACT);
        $img2_mob_2x = CFile::ResizeImageGet($img2_id,array('width'=>786,'height'=>826),BX_RESIZE_IMAGE_EXACT);

    }
    else{
        $img = CFile::ResizeImageGet($arProduct['~DETAIL_PICTURE'],array('width'=>$params['width'],'height'=>$params['height']),BX_RESIZE_IMAGE_EXACT);
        $img_2x = CFile::ResizeImageGet($arProduct['~DETAIL_PICTURE'],array('width'=>$params['width']*2,'height'=>$params['height']*2),BX_RESIZE_IMAGE_EXACT);
        $img_mob = CFile::ResizeImageGet($arProduct['~DETAIL_PICTURE'],array('width'=>393,'height'=>413),BX_RESIZE_IMAGE_EXACT);
        $img_mob_2x = CFile::ResizeImageGet($arProduct['~DETAIL_PICTURE'],array('width'=>786,'height'=>826),BX_RESIZE_IMAGE_EXACT);
    }

    $haveExtraImg = false;
    $aloneClass = 'catalog-item__img-top--alone';
    if(is_array($img2) && !empty($img2) && $arProduct['PROPERTY_NOT_SHOW_SCND_PHOTO_VALUE'] != 'Y'){
        $haveExtraImg = true;
        $aloneClass = '';
    }
    ?>
    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6">
        <a class="catalog-item catalog-item--compact link link--primary" href="<?=$arProduct['DETAIL_PAGE_URL']?>">
            <span class="catalog-item__badge-wrapper">
                <?/*if(in_array('New',$arProduct['PROPERTY_BADGE_VALUE'])){?>
                    <div class="catalog-item__badge-row">
                        <span class="catalog-item__badge mod_1 catalog-item__badge--new">New</span>
                    </div>
                <?}*/?>
                <?if(in_array('ITK CHOICE',$arProduct['PROPERTY_BADGE_VALUE'])){?>
                    <div class="catalog-item__badge-row">
                        <span class="catalog-item__badge mod_itkchoice catalog-item__badge--sale">Itk Choice</span>
                    </div>
                <?}?>
                <?if(in_array('RE-STOCK',$arProduct['PROPERTY_BADGE_VALUE'])){?>
                    <div class="catalog-item__badge-row">
                        <span class="catalog-item__badge mod_2">Re-Stock</span>
                    </div>
                <?}?>
                <?
                // $arrCountries = empty($params['countries']) ? getHlCountries() : $params['countries'];
                // $useVAT = $arrCountries[$_SESSION['LAST_COUNTRY']]['UF_USE_VAT'];
                // $p = $useVAT=="N" ? $arProduct['RETAIL_PRICE'] / 1.21 : $arProduct['RETAIL_PRICE'];
                ?>
                <?//if(in_array('FREE SHIPPING',$arProduct['PROPERTY_BADGE_VALUE']) && $p < 350){?>
                    <!-- <div class="catalog-item__badge-row">
                        <span class="catalog-item__badge mod_3">Free shipping</span>
                    </div> -->
                <?//}?>
                <?if(in_array('Sale',$arProduct['PROPERTY_BADGE_VALUE'])){?>
                    <div class="catalog-item__badge-row">
                        <span class="catalog-item__badge mod_4 catalog-item__badge--sale"><?=$APPLICATION->ShowViewContent('SALE_SIZE'.$arProduct['ID']);?></span>
                    </div>
                <?}?>
                <?if(in_array('Gift',$arProduct['PROPERTY_BADGE_VALUE'])){?>
                    <div class="catalog-item__badge-row">
                        <span class="catalog-item__badge mod_gift">Gift idea</span>
                    </div>
                <?}?>
                <?if(!empty($arProduct['PROPERTY_SELECTION_VALUE'])){?>
                  <?
                  $res_selection = CIBlockElement::GetList(array('SORT'=>'ASC'),array('IBLOCK_ID' => 11, 'ID' => $arProduct['PROPERTY_SELECTION_VALUE']), false, false, array('NAME', 'PROPERTY_SHOW_ONLY_ADMIN'));
                  while ($ar_selection = $res_selection->Fetch()) {
                    if ($ar_selection['PROPERTY_SHOW_ONLY_ADMIN_VALUE'] == 'Да') {
                      global $USER;
                      if ($USER->IsAdmin()) {
                        ?>
                        <div class="catalog-item__badge-row">
                            <span class="catalog-item__badge mod_selection"><?=$ar_selection['NAME'];?></span>
                        </div>
                        <?
                      }
                    } else {
                      ?>
                      <div class="catalog-item__badge-row">
                          <span class="catalog-item__badge mod_selection"><?=$ar_selection['NAME'];?></span>
                      </div>
                      <?
                    }

                  }
                  ?>
                <?}?>
            </span>

            <div class="catalog-item__img-wrapper<?if(!$haveExtraImg){?> catalog-item__img-wrapper--single<?}?>" style="<?$APPLICATION->ShowProperty('SELECTION_PRODUCT_STYLE','')?>">
                <div class="catalog-item__img-list">
                    <div class="catalog-item__img catalog-item__img--active <?= $aloneClass ?>">
                        <picture>
                            <?/*<source srcset="<?= $img_mob['src'] ?> 1x, <?= $img_mob_2x['src'] ?> 2x"
                                    media="(max-width: 767px)">
                            <source srcset="<?= $img['src'] ?> 1x, <?= $img_2x['src'] ?> 2x">*/?>
                            <img class="lazyload img--lazyload"
                                 src=""
                                 data-src="<?= $img['src'] ?>"
                                 alt="<?= $arProduct['NAME'] ?>"
                                 data-object-fit="contain"
                                 data-lazy="<?= $img['src'] ?>"
                            >
                        </picture>
                    </div>
                    <?if($haveExtraImg){?>
                    <div class="catalog-item__img catalog-item__img--hover">
                        <picture>
                            <?/*<source data-srcset="<?= $img2_mob['src'] ?> 1x, <?= $img2_mob_2x['src'] ?> 2x"
                                    media="(max-width: 767px)">
                            <source data-srcset="<?= $img2['src'] ?> 1x, <?= $img2_2x['src'] ?> 2x">*/?>
                            <img <?/*class="lazyload img--lazyload"*/?>
                                 src=""
                                 data-src="<?= $img2['src'] ?>"
                                 alt="<?= $arProduct['NAME'] ?>"
                                 data-object-fit="contain"
                                 data-lazy="<?= $img2['src'] ?>"
                            >
                        </picture>
                    </div>
                    <?}?>
                </div>
                <div class="catalog-item__img-arrow catalog-item__img-arrow--prev js-catalog-item-arrow-prev"></div>
                <div class="catalog-item__img-arrow catalog-item__img-arrow--next js-catalog-item-arrow-next"></div>
                
				<?
				global $USER;
				//Show sizes only employee (1,6,7)
				$allowed_groups=array(1,6,7);
				$user_id=$USER->GetID();
				$user_groups=CUser::GetUserGroup($user_id);
				$cross_groups=array_uintersect($user_groups,$allowed_groups,"strcasecmp");
				if (count($cross_groups)>0) {?>
				<div class="catalog-item__img-hover">
				<?	$curSize = 'US';
					if (isset($_COOKIE['filterShoesSize']) && $_COOKIE['filterShoesSize'] !== ''){
						$curSize = $_COOKIE['filterShoesSize'];
					}
					switch ($curSize){
						case 'EU':
							if (!empty($arProduct['PROPERTY_SIZES_TRAINERS_EU_VALUE'])){
								$arProduct["PROPERTY_SIZES_VALUE"] = [];
								foreach ($arProduct['PROPERTY_SIZES_TRAINERS_EU_VALUE'] as $sizeEu){
									$arProduct["PROPERTY_SIZES_VALUE"][] = $sizeEu.' EU';
								}
							}
							break;
						case 'US':
						default:
							if (!empty($arProduct['PROPERTY_SIZES_TRAINERS_VALUE'])){
								$arProduct["PROPERTY_SIZES_VALUE"] = $arProduct['PROPERTY_SIZES_TRAINERS_VALUE'];
							}
							break;
					}
				
					// print_r('<pre class="skdebug" style="display:none;">');
					// print_r($cross_groups);
					// print_r('</pre>');
                    ?>
                    <?usort($arProduct["PROPERTY_SIZES_VALUE"],'KDXDataCollector::sortSizes');?>

                    <?$str = '';?>
                    <?foreach($arProduct['PROPERTY_SIZES_VALUE'] as $size){?>
                        <?if(in_array($size,KDXSettings::getSetting('NOT_SHOW_SIZES'))){continue;}?>
                        <?$str.=$size.' ';?>
                    <?}?>
                    <div><?=trim($str)?></div>
                </div>
				<?}
				//Show sizes only employee (1,6,7)?>
            </div>
            <?
            $brandName = '';
            $resBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, 'ID'=>$arProduct['PROPERTY_CML2_MANUFACTURER_VALUE'][0]), false, array(), array('NAME'));
            if ($arBrand = $resBrand->Fetch()){
                $brandName = $arBrand['NAME'];
                
            }
            ?>
            <div class="catalog-item__title">
                <div class="catalog-item__brand"><?=$brandName?></div>
                <div class="catalog-item__title-name"><?=str_ireplace($brandName.' ', '', $arProduct["NAME"])?></div>
                <?if(count($arProduct["PROPERTY_SIZES_VALUE"]) && reset($arProduct["PROPERTY_SIZES_VALUE"]) != "ONE_SIZE"){?>

                    <?/*<div class="catalog-item__hover">
                        <?
                        global $USER;
                        if ($USER->isAdmin()){
                            $curSize = 'US';
                            if (isset($_COOKIE['filterShoesSize']) && $_COOKIE['filterShoesSize'] !== ''){
                                $curSize = $_COOKIE['filterShoesSize'];
                            }
                            switch ($curSize){
                                case 'EU':
                                    $arProduct["PROPERTY_SIZES_VALUE"] = [];
                                    foreach ($arProduct['PROPERTY_SIZES_TRAINERS_EU_VALUE'] as $sizeEu){
                                        $arProduct["PROPERTY_SIZES_VALUE"][] = $sizeEu.' EU';
                                    }
                                    break;
                                case 'US':
                                default: 
                                    $arProduct["PROPERTY_SIZES_VALUE"] = $arProduct['PROPERTY_SIZES_TRAINERS_VALUE'];
                                    break;
                            }
                        }?>
                        <?usort($arProduct["PROPERTY_SIZES_VALUE"],'KDXDataCollector::sortSizes');?>

                        <?$str = '';?>
                        <?foreach($arProduct['PROPERTY_SIZES_VALUE'] as $size){?>
                            <?if(in_array($size,KDXSettings::getSetting('NOT_SHOW_SIZES'))){continue;}?>
                            <?$str.=$size.' ';?>
                        <?}?>
                        <div><?=trim($str)?></div>
                    </div>*/?>
                <?}?>
            </div>

            <div class="catalog-item__price">
                <div class="price__wrapper">
                    <div class="price price--has-discount">
                    <?
                    $arrCountries = empty($params['countries']) ? getHlCountries() : $params['countries'];
                    $useVAT = $arrCountries[$_SESSION['LAST_COUNTRY']]['UF_USE_VAT'];
                    ?>

                    <?if (isBot() && LANGUAGE_ID == 'ru'){
                        $curCurrency = 'RUB';
                    } else {
                        $curCurrency = KDXCurrency::$CurrentCurrency;
                    }?>

                    <?if($arProduct['PROPERTY_BASE_PRICE_MIN_VALUE']>$arProduct['PROPERTY_RETAIL_PRICE_MIN_VALUE']){?>
                        <?
                        $saleValuePercent = round(($arProduct['PROPERTY_BASE_PRICE_MIN_VALUE'] - $arProduct['PROPERTY_RETAIL_PRICE_MIN_VALUE']) / $arProduct['PROPERTY_BASE_PRICE_MIN_VALUE']  * 100, 0 , PHP_ROUND_HALF_DOWN);
                        $saleValuePercent = ceil($saleValuePercent/10) * 10;


                        $saleValue = /*$saleValuePercent > 60 ? 'Final Sale' :*/ 'Sale '.$saleValuePercent.'%';
                        $APPLICATION->AddViewContent('SALE_SIZE'.$arProduct['ID'], $saleValue);
                        ?>
                        <span class="price__block">
                            <span class="price--current">
                                <?if($arProduct['PROPERTY_RETAIL_PRICE_MAX_VALUE']>$arProduct['PROPERTY_RETAIL_PRICE_MIN_VALUE']){?><?=GetMessage('KF_PRICE_FROM')?><?}?>
                                <?=KDXCurrency::convert($useVAT=="N" ? $arProduct['RETAIL_PRICE'] / 1.21 : $arProduct['RETAIL_PRICE'], $curCurrency)?>
                                <?=KDXCurrency::GetCurrencyName($curCurrency)?>
                            </span>
                        </span>
                        <span class="price--old">
                            <span>
                                <?if($arProduct['PROPERTY_BASE_PRICE_MAX_VALUE']>$arProduct['PROPERTY_BASE_PRICE_MIN_VALUE']){?><?=GetMessage('KF_PRICE_FROM')?><?}?>
                                <?//=KdxCurrency::convertAndFormat($useVAT=="N" ? $arProduct['BASE_PRICE'] / 1.21 : $arProduct['BASE_PRICE'],KDXCurrency::$CurrentCurrency)?>
                                <?=KDXCurrency::convert($useVAT=="N" ? $arProduct['BASE_PRICE'] / 1.21 : $arProduct['BASE_PRICE'], $curCurrency)?>
                                <?=KDXCurrency::GetCurrencyName($curCurrency)?>
                             </span>
                        </span>
                    <?}else{?>
                        <span class="price__block">
                            <span>
                                <?if($arProduct['PROPERTY_RETAIL_PRICE_MAX_VALUE']>$arProduct['PROPERTY_RETAIL_PRICE_MIN_VALUE']){?><?=GetMessage('KF_PRICE_FROM')?><?}?>
                                <?//=KdxCurrency::convertAndFormat($useVAT=="N" ? $arProduct['RETAIL_PRICE'] / 1.21 : $arProduct['RETAIL_PRICE'],KDXCurrency::$CurrentCurrency)?>
                                <?=KDXCurrency::convert($useVAT=="N" ? $arProduct['RETAIL_PRICE'] / 1.21 : $arProduct['RETAIL_PRICE'], $curCurrency)?>
                                <?=KDXCurrency::GetCurrencyName($curCurrency)?>
                            </span>
                        </span>
                    <?}?>

                    </div>
                </div>
            </div>
            <?if (!empty($arProduct['REVIEW'])){?>
                <div class="catalog-item__review">
                    <?
                    if (LANGUAGE_ID == 'en'){
                        echo $arProduct['REVIEW'].' reviews';
                    } else {
                        echo getModelStr($arProduct['REVIEW'], array('отзыв', 'отзыва', 'отзывов'));
                    }
                    ?>
                </div>
            <?}?>
        </a>
    </div>
<?}

function getModelStr($num, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);

    return $num . " " . $titles[($num % 100 > 4 && $num % 100 < 20) ? 2 : $cases[min($num % 10, 5)]];
}

function cancelOrders()
{
    CModule::IncludeModule('sale');
    global $DB;

    $timeout = KDXSettings::getSetting('ORDER_TIMEOUT') * 3600;
    if($timeout <= 0)
        $timeout = 3600*48;

    $res = CSaleOrder::GetList(
        array('ID' => 'DESC'),
        array(
            '<DATE_INSERT' => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), time()-$timeout),
            'CANCELED' => 'N',
            'PAYED' => 'N',
            'PAY_SYSTEM_ID' => KDXSettings::getSetting('PAY_SYSTEM_CARD')
        ),
        false,
        false,
        array('ID')
    );

    while($arOrder = $res->Fetch())
    {
        CSaleOrder::StatusOrder($arOrder['ID'],'C');
        CSaleOrder::CancelOrder($arOrder['ID'],'Y');

        autoCancelOrderSendMail($arOrder['ID']);
    }
}

function cancelPaypalOrders()
{
    CModule::IncludeModule('sale');
    global $DB;

    $timeout = KDXSettings::getSetting('PAYPAL_ORDER_TIMEOUT') * 3600;
    if($timeout <= 0)
        $timeout = 3600;

    $res = CSaleOrder::GetList(
        array('ID' => 'DESC'),
        array(
            '<DATE_INSERT' => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), time() - $timeout),
            'CANCELED' => 'N',
            'PAYED' => 'N',
            'PAY_SYSTEM_ID' => KDXSettings::getSetting('PAY_SYSTEM_PAYPAL')
        ),
        false,
        false,
        array('ID')
    );

    while($arOrder = $res->Fetch())
    {
        CSaleOrder::StatusOrder($arOrder['ID'],'C');
        CSaleOrder::CancelOrder($arOrder['ID'],'Y');

        autoCancelOrderSendMail($arOrder['ID']);
    }
}

function autoCancelOrderSendMail($ID)
{
    if (!CModule::IncludeModule('sale')) {
        return;
    }
        CTimeZone::Disable();
        $arOrder = CSaleOrder::GetByID($ID);
        CTimeZone::Enable();

        $userEmail = '';
        $userName = '';
        $userLastName = '';

        $dbOrderProp = CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $ID));
        while ($arOrderProp = $dbOrderProp->Fetch()) {
            switch ($arOrderProp['CODE']){
                case 'PAY_CONTACT_EMAIL':
                    $userEmail = $arOrderProp['VALUE'];
                    break;
                case 'PAY_CONTACT_NAME':
                    $userName = $arOrderProp['VALUE'];
                    break;
                case 'PAY_CONTACT_LAST_NAME':
                    $userLastName = $arOrderProp['VALUE'];
                    break;
                default: continue;

            }
        }
        $userFullName = sprintf("%s %s", $userName, $userLastName);


        if (strlen($userEmail) <= 0) {
            $dbUser = CUser::GetByID($arOrder['USER_ID']);
            if ($arUser = $dbUser->Fetch()) {
                $userEmail = $arUser['EMAIL'];
                $userName = $arUser['NAME'];
                $userLastName = $arUser['LAST_NAME'];
                $userFullName = sprintf("%s %s", $userName, $userLastName);
            }
        }

        $arFields = array(
            'ORDER_ID' => $arOrder['ACCOUNT_NUMBER'],
            'ORDER_DATE' => $arOrder['DATE_INSERT_FORMAT'],
            'EMAIL' => $userEmail,
            'SALE_EMAIL' => COption::GetOptionString('sale', 'order_email'),
            "BCC" => COption::GetOptionString("sale", "order_email"),
            'USER_NAME' => $userFullName,

        );
        $eventName = 'KDX_SALE_ORDER_CANCEL';
        $event = new CEvent();
        $event->Send($eventName, $arOrder['LID'], $arFields, 'N');
}

function kitAgentLogger($arAgent, $strStatus, $eval_result, $e)
{
    if($arAgent['NAME'] == 'kdxBankLVCurrency::CurrencyAgent();')
    {
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.__FUNCTION__.'.log',serialize($arAgent).PHP_EOL,FILE_APPEND);
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.__FUNCTION__.'.log',$strStatus.PHP_EOL,FILE_APPEND);
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.__FUNCTION__.'.log',$eval_result.PHP_EOL,FILE_APPEND);
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.__FUNCTION__.'.log',$e.PHP_EOL,FILE_APPEND);
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.__FUNCTION__.'.log','--------------------'.PHP_EOL,FILE_APPEND);
    }
}

function isShopManager(){
    GLOBAL $USER;
    $aUserGroups = $USER->GetUserGroupArray();
    return in_array( GROUP_SHOP_MANAGERS, $aUserGroups );
}

// Метод генерирует одноразовый купон и привязывает его к скидке
// указанной в константе класса.
function addOnetimeCoupon($iDiscountID, $type = 'O', $descrText = ''){
    CModule::IncludeModule('sale');
    CModule::IncludeModule('catalog');

    $sCouponCode = CatalogGenerateCoupon();

    foreach(array('en', 's1') as $site)
    {
        $site = strtoupper($site);
        $aCoupon = array(
            'DISCOUNT_ID' => KDXSettings::getSetting($iDiscountID.'_'.$site),
            'ACTIVE' => 'Y',
            'ONE_TIME' => $type,
            'COUPON' => $sCouponCode.'_'.$site,
            'DATE_APPLY' => false,
            'DESCRIPTION' => $descrText
        );
        CCatalogDiscountCoupon::Add($aCoupon);
    }
    return $sCouponCode;
}

function addCanonicalLinks(){
    global $APPLICATION;

    if ((int)$_GET['PAGEN_1'] < 2 || empty($_GET['PAGEN_1'])) {
        $curPage = ($APPLICATION->GetCurPage() == "/") ? '' : $APPLICATION->GetCurPage();
        $APPLICATION->AddHeadString('<link href="' . $_SERVER['REQUEST_SCHEME'] . '://' . SITE_SERVER_NAME . $curPage . '" rel="canonical" />');
    } else {
        $page = 'https://www.itkkit.com' . $APPLICATION->GetCurUri();
        $APPLICATION->AddHeadString('<link rel="canonical" href="'.$page.'" />');
    }
}

function cleanAbandonedBaskets(){
    Main\Loader::includeModule('kodix.main');
    $days = KDXSettings::getSetting('CLEAN_DROPPED_BASKETS_TIME');

    $expired = new Main\Type\DateTime();
    $expired->add('-'.$days.'days');
    $expiredValue = $expired->format('Y-m-d H:i:s');

    $connection = Main\Application::getConnection();
    $sqlHelper = $connection->getSqlHelper();

    $query = "DELETE FROM b_sale_fuser WHERE
                                b_sale_fuser.DATE_UPDATE < ".$sqlHelper->getDateToCharFunction("'".$expiredValue."'")."
                                AND b_sale_fuser.USER_ID IS NOT NULL
                                AND b_sale_fuser.id NOT IN (select FUSER_ID from b_sale_basket)";

    $connection->queryExecute($query);

    $query = "DELETE FROM b_sale_basket WHERE DATE_UPDATE<".$sqlHelper->getDateToCharFunction("'".$expiredValue."'")."
                                          AND ORDER_ID IS NULL;";

    $connection->queryExecute($query);

    return "cleanAbandonedBaskets();";
}

function showPopupHtml($html, $data_popup_id, $need_wrap = true, $header=false, $needScroll=false, $isAgePopup=false){?>
    <div class="popup" data-popup-show="init" data-popup-id="<?=$data_popup_id?>">
        <div class="popup__overlay" data-popup-bg></div>
        <?if($needScroll){?>
            <div class="js_customscroll popup__content">
        <?}?>
            <div class="popup__inner">
                <div class="popup__close" data-popup-close>
                    <svg class="icon icon-cross_pop-up">
                        <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#cross_pop-up"></use>
                    </svg>
                </div>
                <div class="auth__heading"><?=$header?></div>
                <?if($need_wrap){?>
                    <div class="popup__body"><?=$html?></div>
                <?}else{
                    echo $html;
                }?>
            </div>
        <?if($needScroll){?>
            </div>
        <?}?>
    </div>
<?}

function getLastCountry() {
    global $USER;
    if($USER->IsAuthorized()) {
        $filter = array("ID" => CUser::GetID());
        $rsUser = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, array( 'FIELDS' => array("ID","NAME"), 'SELECT' => array("UF_LAST_COUNTRY")));
        if($arUser = $rsUser->fetch()) {
            return $last_country = $arUser['UF_LAST_COUNTRY'];
        }
    } else {
        return $last_country = $_SESSION['LAST_COUNTRY'];
    }

    /*if(strlen($last_country))
        return $last_country;
    else {

    }*/
}

function getHlCountries() {
    if (!CModule::IncludeModule('highloadblock')) {
        return false;
    }
    
//    $cur_request=explode('?',$_SERVER['REQUEST_URI'])[0];
    $cache_filename='/var/www/itkkit/local/cache_sql/local_php_interface_kodix_kdxFunctions_1030'.$prop['CODE'].'.obj';
    if (file_exists($cache_filename)) {
        $objData = file_get_contents($cache_filename);
        $arResult["COUNTRIES"]=unserialize($objData);
    } else {
        $arResult["COUNTRIES"] = array();
        if ($hb_entity = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter' => array('TABLE_NAME' => 'b_countries'),))->fetch()) {
            $classname = $hb_entity['NAME'] . 'Table';
            $hb_entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hb_entity);
            $hb_class = new $classname();

            $res = $hb_class->getList(array('order'=>array('ID')));
            while ($fields = $res->fetch()) {
                $arResult["COUNTRIES"][$fields["UF_CONTRY_CODE"]] = $fields;
            }
        }
        $objData = serialize($arResult["COUNTRIES"]);
        $t=file_put_contents($cache_filename, $objData);
    }
    
    return $arResult["COUNTRIES"];
}

function getListDelivery(){
    $cur_request=explode('?',$_SERVER['REQUEST_URI'])[0];
        if ($cur_request !== '/checkout/') {
            return;
        }
    
    if(!CModule::IncludeModule('sale')) {
        throw new \Bitrix\Main\SystemException(GetMessage('KDX_BITRIX_SALE_MODULE_NOT_INSTALLED'));
    }

    $hl_countries = getHlCountries();

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
    $order=KDXOrder::loadFromSession();
    $countryCode = $_SESSION['LAST_COUNTRY'];
    if(!$countryCode){
        $countryCode = 'RU';
    }
    $order->setProperty('DELIVERY_COUNTRY', $hl_countries[$countryCode]['UF_COUNTRY_ID']);

    $priceWithDiscount = $cart->getAvailablePriceWithDiscount();

    $order->getVATPrice($priceWithDiscount);

    if($order->properties["DELIVERY_COUNTRY"]["VALUE"]){
        $arOrder = array(
            "WEIGHT" => $cart->getWeightAvailable(),
            "PRICE" => $priceWithDiscount + $order->vat_price,
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
    while ($deliv = $res->GetNext()){
        if(!empty($arOrder)){
            $arOrder['TEMP_DELIVERY']=$deliv;
            $arProfiles = CSaleDeliveryHandler::GetHandlerCompability($arOrder, $deliv);
            unset($arOrder['TEMP_DELIVERY']);
            foreach($arProfiles as $k=>$prof){
                $arDelivery = CSaleDeliveryHandler::CalculateFull(
                    $deliv["SID"], // идентификатор службы доставки
                    $k, // идентификатор профиля доставки
                    $arOrder, // заказ
                    $currency // валюта, в которой требуется вернуть стоимость
                );
                //printr($arDelivery);
                $arProfiles[$k]["PRICE"]=$arDelivery;
            }
            $deliv["PROFILES"]=$arProfiles;
        }
        $result[]=$deliv;
    }
    return $result;
}

function getMailBasketTable($arBasket, $siteId = SITE_ID, $arOrder = []){?>
    <?ob_start();?>
    <?
    $arSite = \CSite::GetByID($siteId)->Fetch();
    $lang = $arSite['LANGUAGE_ID'];
    ?>
    <tr style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%;">
        <td style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; border-collapse: collapse; padding-bottom: 0;">
            <table class="order-list" border="0" cellspacing="0" cellpadding="0" style="padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; width: 100%; max-width: 600px; margin: 0 auto; border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">

                <?foreach ($arBasket as $arItem) {
                    $imgId = $arItem['PROPS']['DETAIL_PICTURE']?:$arItem['PROPS']['PREVIEW_PICTURE'];
                    $arSites = ['width'=>173,'height'=>200];
                    $arImg = CFile::ResizeImageGet(
                        $imgId,
                        $arSites,
                        BX_RESIZE_IMAGE_EXACT
                    );
                    while(!is_array($arImg) || empty($arImg)) {
                        $arImg = CFile::ResizeImageGet(
                            current($arItem['PROPS']['GALLERY']),
                            $arSites,
                            BX_RESIZE_IMAGE_EXACT
                        );
                        next($arItem['PROPS']['GALLERY']);
                    }
                    $itemImgSrc = $arSite['SERVER_NAME'].$arImg['src'];
                    $itemUrl =  $arSite['SERVER_NAME'].$arItem['PROPS']['DETAIL_PAGE_URL'];
                   ?>
                    <tr class="order-item" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%;">
                        <?if(is_array($arImg) && !empty($arImg)){?>
                            <td class="order-item__img-wrapper" style="margin: 0; padding-left: 45px; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; border-collapse: collapse; width: 173px; padding-bottom: 0;" width="173">
                                <a href="<?=$itemUrl?>" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%;">
                                    <img class="order-item__img" src="<?=$itemImgSrc?>" alt="item1" title="item1" width="<?=$arImg['width']?>" height="<?=$arImg['height']?>" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; -ms-interpolation-mode: bicubic; width: 173px; height: 200px;">
                                </a>
                            </td>
                        <?}?>
                        <td class="order-item__text-wrapper" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; border-collapse: collapse; padding-left: 20px; padding-right: 20px; padding-bottom: 0;">
                            <a class="order-item__text" href="<?=$itemUrl?>" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; display: block; padding-bottom: 5px;"><?=$arItem["NAME"]?></a>
                            <?if($arItem["CML2_ARTICLE"]){?>
                                <div class="order-item__text secondary" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; line-height: normal; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; font-size: 12px; color: #999999; display: block; padding-bottom: 5px;"><?=Loc::GetMessage('EMAIL_VENDOR_CODE_TITLE', null, $lang)?>: <?=$arItem["PROPS"]["CML2_ARTICLE"]?></div>
                            <?}?>
                            <?if($arItem["QUANTITY"] > 0){?>
                                <div class="order-item__text" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; display: block; padding-bottom: 5px;"><?=Loc::GetMessage('EMAIL_QUANTITY_TITLE', null, $lang)?>: <?=round($arItem["QUANTITY"])?></div>
                            <?}?>
                            <?if($arItem["PROPS"]["SIZE"]
                                && !in_array($arItem["PROPS"]["SIZE"],\KDXSettings::getSetting('NOT_SHOW_SIZES'))
                            ){?>
                                <div class="order-item__text" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; display: block; padding-bottom: 5px;"><?=Loc::GetMessage('EMAIL_SIZE_TITLE', null, $lang)?>: <?=$arItem["PROPS"]["SIZE"]?></div>
                            <?}?>

                            <?if(is_array($arItem["PROPS"]["SIZES"])
                                && count($arItem["PROPS"]["SIZES"]) > 0){?>
                                <?usort($arItem["PROPS"]["SIZES"],'\KDXDataCollector::sortSizes');?>

                                <?$str = '';?>
                                <?foreach($arItem["PROPS"]["SIZES"] as $size){?>
                                    <?if(in_array($size,\KDXSettings::getSetting('NOT_SHOW_SIZES'))){continue;}?>
                                    <?$str.=$size.' ';?>
                                <?}?>
                                <?if(strlen($str) > 0){?>
                                    <div class="order-item__text" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; display: block; padding-bottom: 5px;"><?= Loc::getMessage('EMAIL_SIZES_TITLE',null, $lang)?>: <?=quotemeta ( trim($str))?></div>
                                <?}?>

                            <?}?>
                            <?if($arItem['PROPS']['BASE_PRICE_P']){?>
                                <?$totalPrice_WO_discount = $arItem['PROPS']['BASE_PRICE_P']*$arItem["QUANTITY"];?>
                                <div class="order-item__text" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; display: block; padding-bottom: 5px;"><?=Loc::getMessage('EMAIL_PRICE_TITLE',null, $lang)?>: <?=\KDXCurrency::format($totalPrice_WO_discount, "EUR")?></div>
                            <?}?>
                            <?if($arItem["DISCOUNT_PRICE"]){?>
                                <? $totalDisc = $arItem["DISCOUNT_PRICE"] * $arItem["QUANTITY"]?>
                                <div class="order-item__text" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; display: block; padding-bottom: 5px;"><?=Loc::getMessage('EMAIL_DISCOUNT_TITLE',null, $lang)?>: <?=\KDXCurrency::format($totalDisc, "EUR")?></div>
                            <?}?>
                            <?if($arItem["PRICE"]){
                                if($arItem["QUANTITY"] >=  1){
                                    $totalPrice = $arItem["PRICE"] * $arItem["QUANTITY"];?>
                                    <div class="order-item__text bold" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; font-weight: bold; display: block; padding-bottom: 5px;"><?=Loc::getMessage('EMAIL_TOTAL_PRICE_TITLE',null, $lang)?>: <?=\KDXCurrency::format($totalPrice, "EUR")?></div>
                                <?}else{
                                    $totalPrice = $arItem["PRICE"]?>
                                    <div class="order-item__text bold" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; font-weight: bold; display: block; padding-bottom: 5px;"><?=Loc::getMessage('EMAIL_PRICE_TITLE',null, $lang)?>: <?=\KDXCurrency::format($totalPrice, "EUR")?></div>
                                <?}?>
                            <?}?>
                        </td>
                    </tr>
                <?}?>
            </table>
        </td>
    </tr>

    <?if(is_array($arOrder) && !empty($arOrder)){?>
        <?if($arOrder['GOODS_PRICE']){?>
            <tr style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%;">
                <td style="margin: 0; padding-left: 45px; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; border-collapse: collapse; padding-bottom: 30px;">
                    <span class="bold" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; font-weight: bold;"><?=Loc::getMessage('EMAIL_GOODS_PRICE_TITLE',null, $lang)?>: </span><?=\KDXCurrency::format($arOrder['GOODS_PRICE'], "EUR")?>
                </td>
            </tr>
        <?}?>

        <?if($arOrder['PRICE_DELIVERY']){?>
            <tr style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%;">
                <td style="margin: 0; padding-left: 45px; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; border-collapse: collapse; padding-bottom: 30px;">
                    <span class="bold" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; font-weight: bold;"><?=Loc::getMessage('EMAIL_DELIVERY_PRICE_TITLE',null, $lang)?>: </span><?=\KDXCurrency::format($arOrder['PRICE_DELIVERY'], "EUR")?>
                </td>
            </tr>
        <?}?>
        <?if($arOrder['PRICE']){?>
            <tr style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%;">
                <td style="margin: 0; padding-left: 45px; border: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; border-collapse: collapse; padding-bottom: 0;">
                    <span class="title bold" style="margin: 0; padding: 0; border: none; font-family: Helvetica, Arial, sans-serif; line-height: normal; color: #000000; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; text-size-adjust: 100%; font-size: 24px; font-weight: bold;"><?=Loc::getMessage('EMAIL_TOTAL_PRICE_WDISC_TITLE',null, $lang)?>: <?=\KDXCurrency::format($arOrder['PRICE'], "EUR")?></span>
                </td>
            </tr>
        <?}?>
    <?}?>
    <?
    return ob_get_clean();
}
