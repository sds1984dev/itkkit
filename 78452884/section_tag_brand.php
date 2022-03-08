<?php
/**
 * Created by:  KODIX 17.03.2015 10:18
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->SetPageProperty('WEBPACK_JS','catalog');

if(!trim($arResult['VARIABLES']['TAG']))
    show404();

if(!trim($arResult['VARIABLES']['BRAND']))
    show404();

$sort = 'sort_date';

if (isset($_COOKIE['sectionItemsSort']) &&
isset($_COOKIE['sectionItemsOrder']) &&
  $_COOKIE['sectionItemsSort'] !== '' &&
  $_COOKIE['sectionItemsOrder'] !== ''
){
  switch ($_COOKIE['sectionItemsSort']) {
    case 'low_price':
    case 'high_price':
    $sort = $_COOKIE['sectionItemsSort'];
    $sectionSort = 'property_RETAIL_PRICE_MIN';
    $sectionOrder = $_COOKIE['sectionItemsOrder'];
    break;

    default:
    $sectionSort = 'DATE_ACTIVE_FROM';
    $sectionOrder = 'desc';
    break;
  }

} else {
  $sectionSort = 'DATE_ACTIVE_FROM';
  $sectionOrder = 'desc';
}

if(!isAjax())
{
    ?>
    <div class="section-bar">
        <div class="sort">
          <div class="sort-btn js-btn-sort"><?=LANGUAGE_ID == 'en' ? 'Sort by' : 'Сортировка';?></div>
          <ul class="sort-wrap js-wrap-sort">
            <li><a href="#"<?=$sort == 'sort_date' ? ' class="_active"' : '';?> data-type="sort_date" data-order="desc"><?=LANGUAGE_ID == 'en' ? 'New in' : 'По умолчанию'?></a></li>
            <li><a href="#"<?=$sort == 'low_price' ? ' class="_active"' : '';?> data-type="low_price" data-order="asc"><?=LANGUAGE_ID == 'en' ? 'Price (Low)' : 'Возрастанию цены'?></a></li>
            <li><a href="#"<?=$sort == 'high_price' ? ' class="_active"' : '';?> data-type="high_price" data-order="desc"><?=LANGUAGE_ID == 'en' ? 'Price (High)' : 'Убыванию цены'?></a></li>
          </ul>
        </div>
        <?/*<select class="section-bar__sort js-sort-btn">
          <option <?=$sort == 'sort_date' ? ' selected' : '';?> disabled hidden><?=LANGUAGE_ID == 'en' ? 'Sort by' : 'Сортировка'?></option>
          <option data-type="sort_date" data-order="desc" value="">
            <?=LANGUAGE_ID == 'en' ? 'New in' : 'По умолчанию'?>
          </option>

          <option <?=$sort == 'low_price' ? ' selected' : '';?> data-type="low_price" data-order="asc" value="">
            <?=LANGUAGE_ID == 'en' ? 'Price (Low)' : 'Возрастанию цены'?>
          </option>

          <option <?=$sort == 'high_price' ? ' selected' : '';?> data-type="high_price" data-order="desc" value="">
            <?=LANGUAGE_ID == 'en' ? 'Price (High)' : 'Убыванию цены'?>
          </option>

        </select>*/?>
      </div>
    <div id="catalog"><?
}

if(!isRestoreHistory('ALL'))
{
    ?><div class="empty_div"><?
}
$tagTitle = '';
$arrFilter = [];
$curPage = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
$curTagBrandPage = explode($arResult['VARIABLES']['BRAND'].'/', $curPage, 2);
$resTagBrand = CIBlockElement::GetList(array(),array('IBLOCK_ID'=>21,'CODE'=>$arResult['VARIABLES']['BRAND'],'=PROPERTY_SHOW'=>$curTagBrandPage[0],'=PROPERTY_SITE_VALUE'=>LANGUAGE_ID),false,array(),array('*','PROPERTY_*'));
if ($arTag = $resTagBrand->GetNextElement()){
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
    "new",
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
        "BRANDS_FILTER" => '',
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
$curTagPage = explode('/tag/', $curPage, 2);
$resTag = CIBlockElement::GetList(array(),array('IBLOCK_ID'=>21,'CODE'=>$arResult['VARIABLES']['TAG'],'=PROPERTY_SHOW'=>$curTagPage[0].'/','=PROPERTY_SITE_VALUE'=>LANGUAGE_ID),false,array(),array('NAME', 'CODE'))->Fetch();
$APPLICATION->AddChainItem($resTag['NAME'], $curTagPage[0].'/tag/'.$resTag['CODE'].'/');