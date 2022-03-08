<?php
/**
 * Created by:  KODIX 16.03.2015 13:47
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->SetDirProperty('NOT_SHOW_NAV_CHAIN_IN_HEADER','N');
$APPLICATION->SetPageProperty('WEBPACK_JS','product-v2');
//$APPLICATION->SetPageProperty('AMP','amp');
$APPLICATION->SetDirProperty('PAGE_CLASS','brand_list_page');

if (SITE_ID === 's1') {
    $APPLICATION->AddChainItem("Каталог", '/catalog/');

}
$tpl = 'new'; // - Old

// if (!empty($_GET['rev']) && $_GET['rev'] == '2') {
// $tpl = 'rev2'; // - New
// }

// if (!empty($_GET['new']) && $_GET['new'] == 'new') {
// $tpl = 'new'; // - New
// }

$APPLICATION->IncludeComponent("kodix:catalog.detail", $tpl, array(
    "IBLOCK_TYPE"=>$arParams["IBLOCK_TYPE"],
    "IBLOCK_ID"=>$arParams["IBLOCK_ID"],
    "SKU_IBLOCK_ID"=>$arParams["SKU_IBLOCK_ID"],
    "BRANDS_IBLOCK_ID"=>$arParams["BRANDS_IBLOCK_ID"],
    "GRIDS_IBLOCK_ID"=>$arParams["GRIDS_IBLOCK_ID"],
    "CODE"=>$arResult["VARIABLES"]["CODE"],
    "ELEMENT_ID"=>$arResult["VARIABLES"]["ELEMENT_ID"],
), $component);
