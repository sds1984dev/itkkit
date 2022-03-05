<?php
/**
 * Created by:  KODIX 16.03.2015 13:47
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->SetPageProperty('WEBPACK_JS','brand-detail');

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
    ?><div class="empty_div"><?
}

$brand = '';

if(!empty($arResult['VARIABLES']['BRAND']))
{
    $arBrands = KDXSaleDataCollector::getBrands();
    foreach($arBrands as $arBrand)
    {
        if($arBrand['CODE'] == $arResult['VARIABLES']['BRAND']){
            $brand = $arBrand['ID'];
            break;
        }
    }

    if ($brand == '') show404();
}
if(SITE_TEMPLATE_ID != 'ajax' || ($_SERVER['REQUEST_METHOD'] == 'POST' && is_array($_POST['FILTER']))) {
    $APPLICATION->IncludeComponent(
        "kodix:catalog.filter",
        "brand",
        array(
            "CATALOG_IBLOCK_ID" => "1",
            "SELECTIONS_IBLOCK_ID" => $arParams["SELECTIONS_IBLOCK_ID"],
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "FILTER_PRICE" => "1",
            "FILTER_PRICE_PROP" => $arParams['FILTER_PRICE_PROP'],
            "SECTION_ID" => '',
            'FILTER' => array('PROPERTY_CML2_MANUFACTURER' => $brand),
            'BRAND_PAGE' => 'Y',
            "BRANDS_FILTER" => $arResult['VARIABLES']['BRAND'],
        ),
        $component
    );
}
if (SITE_ID === 's1') {
    $APPLICATION->AddChainItem("Бренды", '/catalog/brands/');
} else {
    $APPLICATION->AddChainItem("Brands", '/catalog/brands/');
}
//echo '<pre>';
//print_r ($brand);
//echo '</pre>';
//die();
$APPLICATION->IncludeComponent(
    "kodix:catalog.list.brand",
    "",
    array(
        "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
        "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
        "SKU_IBLOCK_ID" => $arParams['SKU_IBLOCK_ID'],
        "BRANDS_IBLOCK_ID" => $arParams['BRANDS_IBLOCK_ID'],
        "GRIDS_IBLOCK_ID" => $arParams['GRIDS_IBLOCK_ID'],
        "SORT_BY1" => $sectionSort,
        "SORT_ORDER1" => $sectionOrder,
        "SORT_BY2" => $arParams['SORT_BY2'],
        "SORT_ORDER2" => $arParams['SORT_ORDER2'],
        "SORT_VARIANTS" => $arParams['SORT_VARIANTS'],
        "JUST_NEW" => "N",
        "SALE" => "N",
        "BRANDS_INCLUDE_AREA" => "Y",
        "BRANDS_FILTER" => $brand,
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "CACHE_TIME" => $arParams['CACHE_TIME'],
        "PAGE_ITEMS_COUNT" => $arParams['PAGE_ITEMS_COUNT'],
        "PAGINATION_TEMPLATE" => 'kit_brand',
        "PAGINATION_WRAPPER" => $arParams['PAGINATION_WRAPPER'],
        "SECTION_ID" => ''
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

$resBrand = CIblockElement::GetList(array(), array('IBLOCK_ID'=>$arParams['BRANDS_IBLOCK_ID'], 'ACTIVE'=>'Y', 'CODE'=>$arResult['VARIABLES']['BRAND']), false, array(), array());
if ($arBrand = $resBrand->Fetch()){
    $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arBrand['IBLOCK_ID'], $arBrand['ID']);
    $iProperty = $ipropValues->getValues();
    if (!empty($iProperty['ELEMENT_META_TITLE'])){
        $APPLICATION->SetPageProperty('title', $iProperty['ELEMENT_META_TITLE']);
        $APPLICATION->AddHeadString('<meta property="og:title" content="'.$iProperty['ELEMENT_META_TITLE'].'"/>', true);
        $APPLICATION->AddHeadString('<meta property="twitter:title" content="'.$iProperty['ELEMENT_META_TITLE'].'"/>', true);
    } else {
        if (LANGUAGE_ID == 'en'){
            $APPLICATION->SetPageProperty('title', 'Shop '.$arBrand['NAME'].' at itk online store');
            $APPLICATION->AddHeadString('<meta property="og:title" content="Shop '.$arBrand['NAME'].' at itk online store"/>', true);
            $APPLICATION->AddHeadString('<meta property="twitter:title" content="Shop '.$arBrand['NAME'].' at itk online store"/>', true);
        } else {
            $APPLICATION->SetPageProperty('title', 'Товары бренда '.$arBrand['NAME'].' - купить в интернет-магазине itk');
            $APPLICATION->AddHeadString('<meta property="og:title" content="Товары бренда '.$arBrand['NAME'].' - купить в интернет-магазине itk"/>', true);
            $APPLICATION->AddHeadString('<meta property="twitter:title" content="Товары бренда '.$arBrand['NAME'].' - купить в интернет-магазине itk"/>', true);
        }
    }
    if (!empty($iProperty['ELEMENT_META_DESCRIPTION'])){
        $APPLICATION->SetPageProperty('description', $iProperty['ELEMENT_META_DESCRIPTION']);
        $APPLICATION->AddHeadString('<meta property="og:description" content="'.$iProperty['ELEMENT_META_DESCRIPTION'].'"/>', true);
        $APPLICATION->AddHeadString('<meta property="twitter:description" content="'.$iProperty['ELEMENT_META_DESCRIPTION'].'"/>', true);
    } else {
        if (LANGUAGE_ID == 'en'){
            $APPLICATION->SetPageProperty('description', 'Shop '.$arBrand['NAME'].' at itk online store. Free Shipping on All Orders Over &euro;350 &#10003; Fast Delivery &#10003; 14 Days Return Policy &#10003; 100% Authenticity &#10003;');
            $APPLICATION->AddHeadString('<meta property="og:description" content="Shop '.$arBrand['NAME'].' at itk online store. Free Shipping on All Orders Over &euro;350 &#10003; Fast Delivery &#10003; 14 Days Return Policy &#10003; 100% Authenticity &#10003;"/>', true);
            $APPLICATION->AddHeadString('<meta property="twitter:description" content="Shop '.$arBrand['NAME'].' at itk online store. Free Shipping on All Orders Over &euro;350 &#10003; Fast Delivery &#10003; 14 Days Return Policy &#10003; 100% Authenticity &#10003;"/>', true);
        } else {
            $APPLICATION->SetPageProperty('description', 'Интернет-магазин itk предлагает купить товары бренда '.$arBrand['NAME'].' &#9989; Бесплатная доставка на все заказы свыше €350 &#9989; Быстрая доставка &#9989; 14 дневная политика возврата товаров &#9989; 100% оригинальный товар');
            $APPLICATION->AddHeadString('<meta property="og:description" content="Интернет-магазин itk предлагает купить товары бренда '.$arBrand['NAME'].' &#9989; Бесплатная доставка на все заказы свыше €350 &#9989; Быстрая доставка &#9989; 14 дневная политика возврата товаров &#9989; 100% оригинальный товар"/>', true);
            $APPLICATION->AddHeadString('<meta property="twitter:description" content="Интернет-магазин itk предлагает купить товары бренда '.$arBrand['NAME'].' &#9989; Бесплатная доставка на все заказы свыше €350 &#9989; Быстрая доставка &#9989; 14 дневная политика возврата товаров &#9989; 100% оригинальный товар"/>', true);
        }
    }

    $url = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

    $APPLICATION->AddHeadString('<meta property="og:type" content="website"/>', true);
    $APPLICATION->AddHeadString('<meta property="og:url" content="https://'.$_SERVER['SERVER_NAME'].$url.'"/>', true);
    $APPLICATION->AddHeadString('<meta property="og:site_name" content="'.$_SERVER['SERVER_NAME'].'"/>', true);
    $APPLICATION->AddHeadString('<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/images/itk-og.jpg"/>', true);
    $APPLICATION->AddHeadString('<meta property="vk:image" content="https://'.$_SERVER['SERVER_NAME'].'/images/itk-vk-image.jpg"/>', true);

    $APPLICATION->AddHeadString('<meta property="twitter:site" content="'.$_SERVER['SERVER_NAME'].'"/>', true);
    $APPLICATION->AddHeadString('<meta property="twitter:card" content="summary_large_image"/>', true);
    $APPLICATION->AddHeadString('<meta property="twitter:url" content="https://'.$_SERVER['SERVER_NAME'].$url.'"/>', true);
    $APPLICATION->AddHeadString('<meta property="twitter:image:src" content="https://'.$_SERVER['SERVER_NAME'].'/images/logo-social.jpg?ver=1"/>', true);
}

$GLOBALS['og_set'] = true;

?>
