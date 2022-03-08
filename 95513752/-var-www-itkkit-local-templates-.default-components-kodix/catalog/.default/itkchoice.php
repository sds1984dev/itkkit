<?php
/**
 * Created by:  KODIX 16.03.2015 13:47
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isAjax())
{
    ?><div id="catalog"><?
}

if(!isRestoreHistory('ALL'))
{
    ?><div class="empty_div"><?
}

$url = $arParams['SEF_FOLDER'] . CComponentEngine::makePathFromTemplate($arParams['SEF_URL_TEMPLATES']['gift'],array());
$APPLICATION->AddChainItem(GetMessage('ITKCHOICE_TITLE'),$url);
$APPLICATION->SetTitle(GetMessage('ITKCHOICE_TITLE'));
if(SITE_TEMPLATE_ID != 'ajax' || ($_SERVER['REQUEST_METHOD'] == 'POST' && is_array($_POST['FILTER']))) {
    $APPLICATION->IncludeComponent(
        "kodix:catalog.filter",
        ".default",
        array(
            "CATALOG_IBLOCK_ID" => "1",
            "SELECTIONS_IBLOCK_ID" => $arParams["SELECTIONS_IBLOCK_ID"],
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "FILTER_PRICE" => "1",
            "FILTER_PRICE_PROP" => $arParams['FILTER_PRICE_PROP'],
            "SECTION_ID" => '',
            'FILTER' => array('PROPERTY_BADGE_VALUE' => 'ITK CHOICE'),
        ),
        $component
    );
}
$APPLICATION->IncludeComponent(
    "kodix:catalog.list",
    ".default",
    array(
        "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
        "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
        "SKU_IBLOCK_ID" => $arParams['SKU_IBLOCK_ID'],
        "BRANDS_IBLOCK_ID" => $arParams['BRANDS_IBLOCK_ID'],
        "GRIDS_IBLOCK_ID" => $arParams['GRIDS_IBLOCK_ID'],
        "SORT_BY1" => 'DATE_ACTIVE_FROM',
        "SORT_ORDER1" => 'DESC',
        "SORT_BY2" => 'PROPERTY_MAX_DISCOUNT',
        "SORT_ORDER2" => 'DESC',
        "SORT_VARIANTS" => $arParams['SORT_VARIANTS'],
        "ITKCHOICE_INCLUDE_AREA" => "Y",
        "BRANDS_FILTER" => array(
        ),
        'FILTER' => array('PROPERTY_BADGE_VALUE' => 'ITK CHOICE'),
        'STABLE_FILTER' => 'Y',
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