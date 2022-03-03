<?php
/**
 * Created by:  KODIX 16.03.2015 13:47
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->SetPageProperty('WEBPACK_JS','catalog');
$APPLICATION->AddHeadString('<meta property="og:title" content="Sale at itk online store"/>');
$APPLICATION->AddHeadString('<meta property="og:type" content="website"/>');
$APPLICATION->AddHeadString('<meta property="og:url" content="https://www.itkkit.com/catalog/sale/"/>');
$APPLICATION->AddHeadString('<meta property="og:site_name" content="Itkkit.com" />');
$APPLICATION->AddHeadString('<meta property="og:description" content="Sale at itk online store. Free Shipping on All Orders Over €350 ✓ Fast Delivery ✓ 14 Days Return Policy ✓ 100% Authenticity ✓"/>');
$APPLICATION->AddHeadString('<meta property="og:image" content="https://www.itkkit.com/images/itk-og.jpg"/>');
$APPLICATION->AddHeadString('<meta property="vk:image" content="https://www.itkkit.com/images/itk-vk-image.jpg"/>');
$APPLICATION->AddHeadString('<meta property="twitter:card" content="summary_large_image"/>');
$APPLICATION->AddHeadString('<meta property="twitter:url" content="https://www.itkkit.com/catalog/sale/"/>');
$APPLICATION->AddHeadString('<meta property="twitter:title" content="Sale at itk online store"/>');
$APPLICATION->AddHeadString('<meta property="twitter:description" content="Sale at itk online store. Free Shipping on All Orders Over €350 ✓ Fast Delivery ✓ 14 Days Return Policy ✓ 100% Authenticity ✓"/>');
$APPLICATION->AddHeadString('<meta property="twitter:image:src" content="https://www.itkkit.com/images/logo-social.jpg?ver=1"/>');
$APPLICATION->AddHeadString('<meta property="twitter:site" content="Itkkit.com"/>');

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
<?
    ?><div id="catalog"><?
}

if(!isRestoreHistory('ALL'))
{
    ?><div class="empty_div"><?php
}

$brand_filter = array();
if(isset($_GET['BRAND']) && $_GET['BRAND'] != '') {
    $brand_filter[] = intval(htmlspecialchars($_GET['BRAND']));
}

$url = $arParams['SEF_FOLDER'] . CComponentEngine::makePathFromTemplate($arParams['SEF_URL_TEMPLATES']['sale'],array());

//$APPLICATION->AddChainItem(GetMessage('SALE_TITLE'),$url);
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
            "SECTION_ID" => '',
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
        "SORT_BY1" => $sectionSort,
        "SORT_ORDER1" => $sectionOrder,
        "SORT_BY2" => 'PROPERTY_MAX_DISCOUNT',
        "SORT_ORDER2" => 'DESC',
        "SORT_VARIANTS" => $arParams['SORT_VARIANTS'],
        "JUST_NEW" => "N",
        "SALE" => "Y",
        "BRANDS_FILTER" => $brand_filter,
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "CACHE_TIME" => $arParams['CACHE_TIME'],
        "PAGE_ITEMS_COUNT" => $arParams['PAGE_ITEMS_COUNT'],
        "PAGINATION_TEMPLATE" => $arParams['PAGINATION_TEMPLATE'],
        "PAGINATION_WRAPPER" => $arParams['PAGINATION_WRAPPER'],
        "SECTION_ID" => '',
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
