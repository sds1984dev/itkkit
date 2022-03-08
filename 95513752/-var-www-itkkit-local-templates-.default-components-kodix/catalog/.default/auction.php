<?php
/**
 * Created by:  KODIX 16.03.2015 13:47
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("PAGE_CLASS", "product_page");
$APPLICATION->SetTitle("Аукцион");
$APPLICATION->IncludeComponent(
    "kodix:auction.detail",
    "itkauc",
    Array(
        "COMPONENT_TEMPLATE" => "itkauc",
        "AUCTION_IBLOCK_TYPE" => "kodix_catalog",
        "AUCTION_IBLOCK_ID" => "9",
        "BETS_IBLOCK_TYPE" => "kodix_catalog",
        "BETS_IBLOCK_ID" => "10",
        "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
        "ELEMENT_CODE" => "",
        "AUCTION_JQUERY" => "Y",
        "AUCTION_PERMISSIONS" => "Y",
        "AUCTION_HIDE" => "N",
        "AUCTION_BUY_LOT" => "Y",
        "AUCTION_SHOW_NAME" => "Y",
        "AUCTION_EDIT_PRICE" => "N",
        "AUCTION_DOUBLE_BETS" => "Y",
        "AUCTION_PRICE_CONFIRM" => "N",
        "AUCTION_CHAT" => "N",
        "AUCTION_EXTEND" => "0",
        "AUCTION_INTERVAL" => "0",
        "AUCTION_MAX_BUY" => "0",
        "COUNT_BETS" => "5",
        "AVATAR_WIDTH" => "50",
        "AVATAR_HEIGHT" => "50",
        "IMAGE_WIDTH" => "250",
        "IMAGE_HEIGHT" => "250",
        "IBLOCK_URL" => "",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "86400",
        "PRICE_CODE" => "RETAIL",
        "AUCTION_SET_TITLE" => "N"
    )
);?>