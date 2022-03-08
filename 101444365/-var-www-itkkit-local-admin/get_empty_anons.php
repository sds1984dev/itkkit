<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

define('IBLOCK_ID', 1);

function get_empty_anons()
{
    if (CModule::IncludeModule('iblock'))
    {
        $arResult = Array();
        $arOrder = Array("SORT"=>"ASC");
        $arFilter = Array(
            "IBLOCK_ID" => IBLOCK_ID,
            "INCLUDE_SUBSECTIONS" => "Y",
        );
        $arSelect = Array("ID", "NAME", "DETAIL_PICTURE", "PREVIEW_PICTURE");

        $dbItems = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

        while ($item = $dbItems->GetNext())
        {
            if (empty($item["PREVIEW_PICTURE"]) && !empty($item["DETAIL_PICTURE"]))
            {
                $arResult[] = Array(
                    "ID" => $item["ID"],
                    "NAME" => $item["NAME"],
                    "DETAIL_PICTURE" => $item["DETAIL_PICTURE"],
                    "PREVIEW_PICTURE" => $item["PREVIEW_PICTURE"]
                );
            }
        }

        return $arResult;
    }
    return false;
}