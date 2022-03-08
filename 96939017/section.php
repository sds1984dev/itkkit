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
} else {
    $arCurrentSection = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>1, 'CODE'=>$arResult['VARIABLES']['SECTION_CODE']), true, array('ID', 'IBLOCK_ID', 'NAME'))->GetNext()['NAME'];
}

if (LANGUAGE_ID == 'en'){
    $APPLICATION->SetPageProperty('title', 'Shop '.$arCurrentSection.' at itk online store');
    $APPLICATION->SetPageProperty('description', 'Shop '.$arCurrentSection.' at itk online store. Free Shipping on All Orders Over &euro;350 &#10003; Fast Delivery &#10003; 14 Days Return Policy &#10003; 100% Authenticity &#10003;.');
} else {
    $plural_title = 'Брендов'.detect_plural_singular_ending($arCurrentSection);
    $plural_desc = detect_plural_singular_ending($arCurrentSection, 'vi');

    if (is_array($plural_desc)) {
      $desc_sect = 'брендов'.$plural_desc[0] .  ' ' . $plural_desc[1];
    } else {
      $desc_sect = 'брендов'.$plural_desc . ' ' . strtolower($arCurrentSection);
    }

    $APPLICATION->SetPageProperty('title', $plural_title . ' ' . strtolower($arCurrentSection) . ' - купить в интернет-магазине itk');
    $APPLICATION->SetPageProperty('description', 'Интернет-магазин itk предлагает купить '.$desc_sect.': &#9989; Бесплатная доставка на все заказы свыше €350 &#9989; Быстрая доставка &#9989; 14 дневная политика возврата товаров &#9989; 100% оригинальный товар');

    if ($_SERVER['REQUEST_URI'] == '/catalog/accessories/hot-stuff/') {
      $APPLICATION->SetPageProperty('title', 'Купить острый соус чили в онлайн магазине itk');
      $APPLICATION->SetPageProperty('description', 'Острые соусы чили в онлайн магазине itk. Бесплатная доставка на все заказы свыше €350 ✓ Быстрая доставка ✓ 14 дневная политика возврата товаров ✓ 100% оригинальный товар ✓');
    } else if ($_SERVER['REQUEST_URI'] == '/catalog/accessories/pechatnye-izdaniya/') {
      $APPLICATION->SetPageProperty('title', 'Купить печатные издания в онлайн магазине itk');
      $APPLICATION->SetPageProperty('description', 'Печатные издания в онлайн магазине itk. Бесплатная доставка на все заказы свыше €350 ✓ Быстрая доставка ✓ 14 дневная политика возврата товаров ✓ 100% оригинальный товар ✓');
    } else if ($_SERVER['REQUEST_URI'] == '/catalog/accessories/skateboarding/') {
      $APPLICATION->SetPageProperty('title', 'Купить товары для скейтбординга в онлайн магазине itk');
      $APPLICATION->SetPageProperty('description', 'Товары для скейтбординга в онлайн магазине itk. Бесплатная доставка на все заказы свыше €350 ✓ Быстрая доставка ✓ 14 дневная политика возврата товаров ✓ 100% оригинальный товар ✓');
    } else if ($_SERVER['REQUEST_URI'] == '/catalog/accessories/dlya-sobak/') {
      $APPLICATION->SetPageProperty('title', 'Купить аксессуары для собак в онлайн магазине itk');
      $APPLICATION->SetPageProperty('description', 'Аксессуары для собак в онлайн магазине itk. Бесплатная доставка на все заказы свыше €350 ✓ Быстрая доставка ✓ 14 дневная политика возврата товаров ✓ 100% оригинальный товар ✓');
    } else if ($_SERVER['REQUEST_URI'] == '/catalog/footwear/shoe-care/') {
      $APPLICATION->SetPageProperty('title', 'Купить товары для ухода за обувью в онлайн магазине itk');
      $APPLICATION->SetPageProperty('description', 'Товары для ухода за обувью в онлайн магазине itk. Бесплатная доставка на все заказы свыше €350 ✓ Быстрая доставка ✓ 14 дневная политика возврата товаров ✓ 100% оригинальный товар ✓');
    }

    // $APPLICATION->SetPageProperty('title', 'Купить '.$arCurrentSection.' в онлайн магазине itk');
    // $APPLICATION->SetPageProperty('description', 'Купить '.$arCurrentSection.' в онлайн магазине itk. Бесплатная доставка на все заказы свыше €350 &#10003; Быстрая доставка &#10003; 14 дневная политика возврата товаров &#10003; 100% оригинальный товар &#10003;');
}

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
    $sectionSort = 'ACTIVE_FROM';
    $sectionOrder = 'desc';
    break;
  }

} else {
  $sectionSort = 'ACTIVE_FROM';
  $sectionOrder = 'desc';
}

if(!isAjax())
{
// global $USER;
// if ($USER->IsAdmin()) {
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
// }
    ?><div id="catalog"><?
}

if(!isRestoreHistory('ALL'))
{
    ?><div class="empty_div"><?
}
if(SITE_TEMPLATE_ID != 'ajax' || ($_SERVER['REQUEST_METHOD'] == 'POST' && is_array($_POST['FILTER']))){
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
            "FILTER" => array('ACTIVE' => 'Y')
        ),
        $component
    );
}
global $selection_name;

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
        "SORT_BY2" => $arParams['SORT_BY2'],
        "SORT_ORDER2" => $arParams['SORT_ORDER2'],
        "SORT_VARIANTS" => $arParams['SORT_VARIANTS'],
        "JUST_NEW" => "N",
        "SALE" => "N",
        'SELECTION_NAME' => $selection_name,
        "BRANDS_FILTER" => array(
        ),
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "CACHE_TIME" => $arParams['CACHE_TIME'],
        "PAGE_ITEMS_COUNT" => $arParams['PAGE_ITEMS_COUNT'],
        "PAGINATION_TEMPLATE" => $arParams['PAGINATION_TEMPLATE'],
        "PAGINATION_WRAPPER" => $arParams['PAGINATION_WRAPPER'],
        "SECTION_ID" => $arResult['VARIABLES']['SECTION_ID']
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
