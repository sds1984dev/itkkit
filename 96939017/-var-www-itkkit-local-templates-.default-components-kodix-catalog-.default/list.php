<?php
/**
 * Created by:  KODIX 16.03.2015 13:47
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->SetPageProperty('WEBPACK_JS','catalog');

if(!isAjax())
{
    ?><div id="catalog"><?
}

if(!isRestoreHistory('ALL'))
{
    ?><div class="empty_div"><?
}

if($arParams['FROM_SEARCH'] == 'Y' && !empty($arParams['SEARCH']))
{
    $filter = array('?SEARCHABLE_CONTENT'=>$arParams['SEARCH']);
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
        "SECTION_ID" => '',
        'FILTER' => $filter,
    ),
    $component
);
global $selection_name;
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
        "SORT_BY1" => $arParams['SORT_BY1'],
        "SORT_ORDER1" => $arParams['SORT_ORDER1'],
        "SORT_BY2" => $arParams['SORT_BY2'],
        "SORT_ORDER2" => $arParams['SORT_ORDER2'],
        "SORT_VARIANTS" => $arParams['SORT_VARIANTS'],
        "JUST_NEW" => "N",
        "SALE" => "N",
        "BRANDS_FILTER" => array(
        ),
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "CACHE_TIME" => $arParams['CACHE_TIME'],
        "PAGE_ITEMS_COUNT" => $arParams['PAGE_ITEMS_COUNT'],
        "PAGINATION_TEMPLATE" => $arParams['PAGINATION_TEMPLATE'],
        "PAGINATION_WRAPPER" => $arParams['PAGINATION_WRAPPER'],
        "SECTION_ID" => '',
        'FILTER' => $filter,
        'SELECTION_NAME' => $selection_name,
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