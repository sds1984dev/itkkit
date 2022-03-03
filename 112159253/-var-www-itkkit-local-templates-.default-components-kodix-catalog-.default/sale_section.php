<?php
/**
 * Created by:  KODIX 16.03.2015 13:47
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->SetPageProperty('WEBPACK_JS','catalog');

if(!trim($arResult['VARIABLES']['SECTION_ID']))
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
  </div>
<?
    ?><div id="catalog"><?
}

if(!isRestoreHistory('ALL'))
{
    ?><div class="empty_div"><?
}
$url = $arParams['SEF_FOLDER'] . CComponentEngine::makePathFromTemplate($arParams['SEF_URL_TEMPLATES']['sale'],array());
$APPLICATION->AddChainItem(GetMessage('SALE_TITLE'), str_replace('index.php', '', $url));
$sectionName = '';
$resSection = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>1, 'ID'=>$arResult['VARIABLES']['SECTION_ID']), false, array('ID', 'IBLOCK_ID', 'NAME', 'UF_EN_NAME'));
if ($arSection = $resSection->GetNext()){
    if (LANGUAGE_ID == 'en'){
        $sectionName = $arSection['UF_EN_NAME'];
    } else {
        $sectionName = $arSection['NAME'];
    }
    $APPLICATION->AddChainItem($sectionName, '');
    $APPLICATION->SetPageProperty('title', GetMessage('SALE_TITLE').' &mdash; '.$sectionName);
}
if(SITE_TEMPLATE_ID != 'ajax' || ($_SERVER['REQUEST_METHOD'] == 'POST' && is_array($_POST['FILTER']))) {
    $APPLICATION->IncludeComponent(
        "kodix:catalog.filter",
        "new",
        array(
            "CATALOG_IBLOCK_ID" => "1",
            "SELECTIONS_IBLOCK_ID" => $arParams["SELECTIONS_IBLOCK_ID"],
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "FILTER_PRICE" => "1",
            "FILTER_PRICE_PROP" => $arParams['FILTER_PRICE_PROP'],
            "SECTION_ID" => $arResult['VARIABLES']['SECTION_ID'],
            'FILTER' => array('PROPERTY_BADGE_VALUE' => 'Sale'),
            "JUST_NEW" => "N",
            "SALE" => "Y",
        ),
        $component
    );
}
$GLOBALS["NavNum"] = 0;
$APPLICATION->IncludeComponent(
    "kodix:catalog.list",
    "new",
    array(
        "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
        "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
        "SKU_IBLOCK_ID" => $arParams['SKU_IBLOCK_ID'],
        "BRANDS_IBLOCK_ID" => $arParams['BRANDS_IBLOCK_ID'],
        "GRIDS_IBLOCK_ID" => $arParams['GRIDS_IBLOCK_ID'],
        "SORT_BY1" => 'SORT',
        "SORT_ORDER1" => 'ASC',
        "SORT_BY2" => 'PROPERTY_MAX_DISCOUNT',
        "SORT_ORDER2" => 'DESC',
        "SORT_VARIANTS" => $arParams['SORT_VARIANTS'],
        "JUST_NEW" => "N",
        "SALE" => "Y",
        "BRANDS_FILTER" => array(
        ),
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "CACHE_TIME" => $arParams['CACHE_TIME'],
        "PAGE_ITEMS_COUNT" => $arParams['PAGE_ITEMS_COUNT'],
        "PAGINATION_TEMPLATE" => $arParams['PAGINATION_TEMPLATE'],
        "PAGINATION_WRAPPER" => $arParams['PAGINATION_WRAPPER'],
        "SECTION_ID" => $arResult['VARIABLES']['SECTION_ID'],
        'FROM_SEARCH' => $arParams['FROM_SEARCH'],
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