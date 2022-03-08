<?php
/**
 * Date: 28.03.2015
 * Time: 13:02
 */

$APPLICATION->AddChainItem($arResult['NAME']);
$APPLICATION->SetTitle($arResult['NAME']);

$APPLICATION->IncludeComponent(
    "kodix:social",
    ".default",
    array(
        "TYPE" => "og",
        "TAGS" => array(
            "OG:TITLE" => $arResult['NAME'],
            "OG:DESCRIPTION" => strip_tags($arResult['DETAIL_TEXT']),
            "OG:TYPE" => "website",
            "OG:URL" => "",
            "OG:SITE_NAME" => "",
            "OG:IMAGE" => $arResult['OG_IMAGE']['src'],
        )
    ),
    false
);
