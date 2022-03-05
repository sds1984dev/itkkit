<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->IncludeComponent("kodix:catalog.detail", "", array(
    "IBLOCK_TYPE"=>$arParams["IBLOCK_TYPE"],
    "IBLOCK_ID"=>$arParams["IBLOCK_ID"],
    "SKU_IBLOCK_ID"=>$arParams["SKU_IBLOCK_ID"],
    "BRANDS_IBLOCK_ID"=>$arParams["BRANDS_IBLOCK_ID"],
    "GRIDS_IBLOCK_ID"=>$arParams["GRIDS_IBLOCK_ID"],
    "CODE"=>$arResult["VARIABLES"]["CODE"]
), $component);
