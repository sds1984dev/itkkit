<?php
/**
 * Created by:  KODIX 17.03.2015 10:18
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->SetPageProperty('WEBPACK_JS','catalog');

if(!trim($arResult['VARIABLES']['SECTION_ID']))
    show404();

if(!isAjax())
{
    ?><div id="catalog"><?
}

if(!isRestoreHistory('ALL'))
{
    ?><div class="empty_div"><?
}

$brand = '';

if (!empty($arResult['VARIABLES']['BRAND']))
{
    $resBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, '=CODE'=>$arResult['VARIABLES']['BRAND'], 'ACTIVE'=>'Y'), false, false, array('ID', 'NAME', 'CODE'));
    if ($arBrand = $resBrand->Fetch()){
        $brand = $arBrand['ID'];
        $brand_name = $arBrand['NAME'];
        $code = $arBrand['CODE'];
    } else {
    @define('ERROR_404', 'Y');
    return;
        //Bitrix\Iblock\Component\Tools::process404('Не найден', true, true, true);
    }
    /*$arBrands = KDXSaleDataCollector::getBrands();
    foreach($arBrands as $arBrand)
    {
        if($arBrand['CODE'] == $arResult['VARIABLES']['BRAND']){
            $brand = $arBrand['ID'];
            break;
        }
    }*/
}
/*if (SITE_ID === 's1') {
    $APPLICATION->AddChainItem("Каталог", '/catalog/');
    $APPLICATION->AddChainItem("Бренды", '/catalog/brand/');
    $APPLICATION->AddChainItem($brand_name, '/catalog/brand/'.$code .'/');
}*/
$APPLICATION->IncludeComponent(
    "kodix:catalog.filter",
    "brand",
    array(
        "CATALOG_IBLOCK_ID" => "1",
        "SELECTIONS_IBLOCK_ID"=>$arParams["SELECTIONS_IBLOCK_ID"],
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
        "FILTER_PRICE" => "1",
        "FILTER_PRICE_PROP" => $arParams['FILTER_PRICE_PROP'],
        "SECTION_ID" => $arResult['VARIABLES']['SECTION_ID'],
        'FILTER' => array('PROPERTY_CML2_MANUFACTURER' => $brand),
        'BRAND_PAGE' => 'Y',
    ),
    $component
);


$APPLICATION->IncludeComponent(
    "kodix:catalog.list",
    "new",
    array(
        "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
        "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
        "SKU_IBLOCK_ID" => $arParams['SKU_IBLOCK_ID'],
        "BRANDS_IBLOCK_ID" => $arParams['BRANDS_IBLOCK_ID'],
        "GRIDS_IBLOCK_ID" => $arParams['GRIDS_IBLOCK_ID'],
        "SORT_BY1" => $arParams['SORT_BY1'],
        "SORT_ORDER1" => $arParams['SORT_ORDER1'],
        "SORT_BY2" => $arParams['SORT_BY2'],
        "SORT_ORDER2" => $arParams['SORT_ORDER2'],
        "SORT_VARIANTS" => $arParams['SORT_VARIANTS'],
        "JUST_NEW" => "N",
        "SALE" => "N",
        "BRANDS_FILTER" => $brand,
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "CACHE_TIME" => $arParams['CACHE_TIME'],
        "PAGE_ITEMS_COUNT" => $arParams['PAGE_ITEMS_COUNT'],
        "PAGINATION_TEMPLATE" => $arParams['PAGINATION_TEMPLATE'],
        "PAGINATION_WRAPPER" => $arParams['PAGINATION_WRAPPER'],
        "SECTION_ID" => $arResult['VARIABLES']['SECTION_ID'],
        "SECTION_CODE" => $arResult['VARIABLES']['SECTION_CODE'],
        "BRAND_CODE" => $arResult['VARIABLES']['BRAND'],
    ),
    $component
);

if(!isRestoreHistory('ALL'))
{
    ?></div><?
}

if(!isAjax())
{
    ?></div><?
}
?>
<style>
    /*.breadcrumb{
        margin-top: 50px;
        position: relative;
        top: 0;
        left: 0;
    }*/
</style>
