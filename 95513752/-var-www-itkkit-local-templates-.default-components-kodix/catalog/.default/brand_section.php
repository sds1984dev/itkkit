<?php
/**
 * Created by:  KODIX 17.03.2015 10:18
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->SetPageProperty('WEBPACK_JS','catalog');

$arCurrentSection = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>1, 'CODE'=>$arResult['VARIABLES']['SECTION_CODE']), true, array('ID', 'IBLOCK_ID', 'NAME', 'SECTION_PAGE_URL', 'UF_EN_NAME'))->GetNext();
if (LANGUAGE_ID == 'en'){
    $arCurrentBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, 'CODE'=>$arResult['VARIABLES']['BRAND']), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_EN_NAME'))->Fetch()['PROPERTY_EN_NAME_VALUE'];
} else {
    $arCurrentBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, 'CODE'=>$arResult['VARIABLES']['BRAND']), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_RU_NAME'))->Fetch()['PROPERTY_RU_NAME_VALUE'];
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
            $brandUrl = $arBrand['DETAIL_PAGE_URL'];
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
//$APPLICATION->AddChainItem($arCurrentBrand, $brandUrl);
if (empty($arResult['VARIABLES']['COLOR'])){
    $sectionPath = explode('/', $arResult['VARIABLES']['SECTION_CODE_PATH']);
    foreach ($sectionPath as $path){
        $arSection = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>1, 'CODE'=>$path), true, array('ID', 'IBLOCK_ID', 'NAME', 'SECTION_PAGE_URL', 'UF_EN_NAME'))->GetNext();
        if (SITE_ID === 's1') {
            $APPLICATION->AddChainItem($arSection['NAME'], $brandUrl.str_replace('/catalog/', '', $arSection['SECTION_PAGE_URL']));
        } else {
            $APPLICATION->AddChainItem($arSection['UF_EN_NAME'], $brandUrl.str_replace('/catalog/', '', $arSection['SECTION_PAGE_URL']));
        }
    }
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
        'FILTER' => array('PROPERTY_CML2_MANUFACTURER' => $brand),
        'BRAND_PAGE' => 'Y',
        "BRANDS_FILTER" => $arResult['VARIABLES']['BRAND'],
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
        "SORT_BY1" => $sectionSort,
        "SORT_ORDER1" => $sectionOrder,
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
        "SECTION_ID" => $arResult['VARIABLES']['SECTION_ID']
    ),
    $component
);

if(!isAjax()){
	if (LANGUAGE_ID == 'en'){?>
		<?getTagPageDescription($arParams['CATALOG_IBLOCK_ID'], $arResult['VARIABLES']['SECTION_ID'], $brand, $arCurrentBrand, strtolower($arCurrentSection['UF_EN_NAME']));?>
	<?}
}

if(!isRestoreHistory('ALL'))
{
    ?></div><?
}

if(!isAjax())
{
    ?></div><?
}

$page = $APPLICATION->GetCurPage(false);
if (file_exists($_SERVER['DOCUMENT_ROOT'].'/local/templates/.default/components/kodix/catalog.list/new/seo.csv')) {
    $file = fopen($_SERVER['DOCUMENT_ROOT'].'/local/templates/.default/components/kodix/catalog.list/new/seo.csv', 'r');
    $data = array();
    while (($buffer = fgets($file, 4096)) !== false) {
        $line = explode('|', $buffer);
        $data[trim($line[0])]['h1'] = $line[1];
        $data[trim($line[0])]['title'] = $line[2];
        $data[trim($line[0])]['desc'] = trim($line[3]);
    }
    fclose($file);
}
if (array_key_exists($page, $data) && LANGUAGE_ID == 'ru'){
    $APPLICATION->SetPageProperty('title', $data[$page]['title']);
    $APPLICATION->SetTitle($data[$page]['h1']);
    $APPLICATION->SetPageProperty('description', $data[$page]['desc']);
} else {
    if (LANGUAGE_ID == 'en'){
        $APPLICATION->SetPageProperty('title', 'Shop '.$arCurrentBrand.' '.$arCurrentSection['UF_EN_NAME'].' at itk online store');
        $APPLICATION->SetPageProperty('description', 'Shop '.$arCurrentBrand.' '.$arCurrentSection['UF_EN_NAME'].' at itk online store. Free Shipping on All Orders Over &euro;350 &#10003; Fast Delivery &#10003; 14 Days Return Policy &#10003; 100% Authenticity &#10003;');
    } else {
        $APPLICATION->SetPageProperty('title', 'Купить '.$arCurrentBrand.' '.$arCurrentSection['NAME'].' в онлайн магазине itk');
        $APPLICATION->SetPageProperty('description', 'Купить '.$arCurrentBrand.' '.$arCurrentSection['NAME'].' в онлайн магазине itk. Бесплатная доставка на все заказы свыше €350 &#10003; Быстрая доставка &#10003; 14 дневная политика возврата товаров &#10003; 100% оригинальный товар &#10003;');
    }
    //$APPLICATION->SetTitle($arCurrentBrand);
}