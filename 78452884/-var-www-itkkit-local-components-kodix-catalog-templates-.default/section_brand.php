<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->IncludeComponent("kodix:catalog.list", "", array(
    "IBLOCK_TYPE" =>$arParams["IBLOCK_TYPE"],
    "CATALOG_IBLOCK_ID"=>$arParams["CATALOG_IBLOCK_ID"],
    "SKU_IBLOCK_ID"=>$arParams["SKU_IBLOCK_ID"],
    "BRANDS_IBLOCK_ID"=>$arParams["BRANDS_IBLOCK_ID"],
    "GRIDS_IBLOCK_ID"=>$arParams["GRIDS_IBLOCK_ID"],
    "PAGE_ITEMS_COUNT"=>$arParams["PAGE_ITEMS_COUNT"],
    "SORT_BY1" => $arParams["SORT_BY1"],
    "SORT_ORDER1" => $arParams["SORT_ORDER1"],
    "SORT_BY2" => $arParams["SORT_BY2"],
    "SORT_ORDER2" => $arParams["SORT_ORDER2"],
    "SORT_VARIANTS" => $arParams["SORT_VARIANTS"],
    "SECTION_CODE"=>$arResult["VARIABLES"]["SECTION_CODE"],
    "BRANDS_FILTER"=>$arResult["VARIABLES"]["BRAND"]
), $component);