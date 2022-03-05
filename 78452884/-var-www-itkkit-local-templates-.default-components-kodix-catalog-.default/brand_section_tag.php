<?php
/**
 * Created by:  KODIX 17.03.2015 10:18
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->SetPageProperty('WEBPACK_JS','catalog');

if (LANGUAGE_ID == 'en'){
    $arCurrentSection = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>1, 'CODE'=>$arResult['VARIABLES']['SECTION_CODE']), true, array('ID', 'IBLOCK_ID', 'UF_EN_NAME'))->GetNext()['UF_EN_NAME'];
    $arCurrentBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, 'CODE'=>$arResult['VARIABLES']['BRAND']), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_EN_NAME'))->Fetch()['PROPERTY_EN_NAME_VALUE'];
} else {
    $arCurrentSection = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>1, 'CODE'=>$arResult['VARIABLES']['SECTION_CODE']), true, array('ID', 'IBLOCK_ID', 'NAME'))->GetNext()['NAME'];
    $arCurrentBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, 'CODE'=>$arResult['VARIABLES']['BRAND']), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_RU_NAME'))->Fetch()['PROPERTY_RU_NAME_VALUE'];
}

if(!trim($arResult['VARIABLES']['TAG']))
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
if(!empty($arResult['VARIABLES']['BRAND']))
{
    $arBrands = KDXSaleDataCollector::getBrands();
    foreach($arBrands as $arBrand)
    {
        if($arBrand['CODE'] == $arResult['VARIABLES']['BRAND']){
            $brandUrl = $arBrand['DETAIL_PAGE_URL'];
            $brandName = $arBrand['NAME'];
            $brand = $arBrand['ID'];
            break;
        }
    }
}

if (SITE_ID === 's1') {
    $APPLICATION->AddChainItem("Бренды", '/catalog/brands/');
} else {
    $APPLICATION->AddChainItem("Brands", '/catalog/brands/');
}
$APPLICATION->AddChainItem($arCurrentBrand, $brandUrl);
$sectionPath = explode('/', $arResult['VARIABLES']['SECTION_CODE_PATH']);

foreach ($sectionPath as $path){
    $arSection = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>1, 'CODE'=>$path), true, array('ID', 'IBLOCK_ID', 'NAME', 'SECTION_PAGE_URL', 'UF_EN_NAME'))->GetNext();
    if (SITE_ID === 's1') {
        $APPLICATION->AddChainItem($arSection['NAME'], $brandUrl.str_replace('/catalog/', '', $arSection['SECTION_PAGE_URL']));
    } else {
        $APPLICATION->AddChainItem($arSection['UF_EN_NAME'], $brandUrl.str_replace('/catalog/', '', $arSection['SECTION_PAGE_URL']));
    }
}

$tagTitle = '';
$arrFilter = [];
$curPage = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
$curTagPage = explode('/tag/', $curPage, 2);
$resTag = CIBlockElement::GetList(array(),array('IBLOCK_ID'=>21,'CODE'=>$arResult['VARIABLES']['TAG'],'=PROPERTY_SHOW'=>$curTagPage[0].'/','=PROPERTY_SITE_VALUE'=>LANGUAGE_ID),false,array(),array('*','PROPERTY_*'));
if ($arTag = $resTag->GetNextElement()){
    $arTagFields = $arTag->GetFields();
    $arTagProps = $arTag->GetProperties();
    $tagTitle = $arTagProps['H1_SEO']['VALUE'];
    $APPLICATION->SetPageProperty('title', $arTagProps['TITLE_SEO']['VALUE']);
    $APPLICATION->SetPageProperty('description', $arTagProps['DESCRIPTION_SEO']['VALUE']);
    if (empty($arTagProps['FILTER']['VALUE'])){
        show404();
    } else {
        foreach ($arTagProps['FILTER']['VALUE'] as $key => $value){
            switch (str_replace(array('%', '!', '!%'), '', $value)){
                case 'NAME':
                case 'DETAIL_TEXT':
                case 'PREVIEW_TEXT':
                    if (substr($value, 0, 2) == '!%'){
                        $arFilters[] = array('!%'.substr($value, 2) => $arTagProps['FILTER']['DESCRIPTION'][$key]);
                    } elseif (substr($value, 0, 1) == '!'){
                        $arFilters[] = array('!='.substr($value, 1) => $arTagProps['FILTER']['DESCRIPTION'][$key]);
                    } else {
                        $arFilters[] = array('%'.$value => $arTagProps['FILTER']['DESCRIPTION'][$key]);
                    }
                    break;
                case 'ID':
                    $arFilters[] = array('='.$value => explode(',',$arTagProps['FILTER']['DESCRIPTION'][$key]));
                    break;
                default:
                    $arPropsResult = [];
                    foreach (explode(',',$arTagProps['FILTER']['DESCRIPTION'][$key]) as $prop){
                        $arPropsResult[] = trim($prop);
                    }
                    if (substr($value, 0, 2) == '!%'){
                        $arFilters[] = array('!%PROPERTY_'.substr($value, 2) => $arPropsResult);
                    } elseif (substr($value, 0, 1) == '%'){
                        $arFilters[] = array('%PROPERTY_'.substr($value, 1) => $arPropsResult);
                    } elseif (substr($value, 0, 1) == '!'){
                        $arFilters[] = array('!=PROPERTY_'.substr($value, 1) => $arPropsResult);
                    } else {
                        $arFilters[] = array('=PROPERTY_'.$value => $arPropsResult);
                    }
                    break;
            }
        }

        $arrFilter = call_user_func_array('array_merge', $arFilters);
    }
} else {
	show404();
}

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
        'FILTER' => array($arrFilter),
        'BRAND_PAGE' => 'Y',
        "BRANDS_FILTER" => $arResult['VARIABLES']['BRAND'],
    ),
    $component
);


$APPLICATION->IncludeComponent(
    "kodix:catalog.list",
    "tag",
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
        "TAG" => $tagTitle,
        "FILTER" => $arrFilter,
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

if(!isAjax()){
    if (LANGUAGE_ID == 'en'){
        if (!empty($tagTitle)){
            getTagPageDescription($arParams['CATALOG_IBLOCK_ID'], $arResult['VARIABLES']['SECTION_ID'], $brand, $arCurrentBrand, strtolower($arCurrentSection), '', $arResult['VARIABLES']['TAG']);
        }
    }
}

if(!isRestoreHistory('ALL'))
{
    ?></div><?
}

if(!isAjax())
{
    ?></div><?
}
