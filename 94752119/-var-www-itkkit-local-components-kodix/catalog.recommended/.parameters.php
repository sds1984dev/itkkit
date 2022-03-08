<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];


$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("KDX_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KDX_CATALOG_IBLOCK_ID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ),
        "ELEMENT_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KDX_ELEMENT_ID"),
            "TYPE" => "STRING",
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "N",
        ),
        "ITEMS_COUNT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KDX_ITEMS_COUNT"),
            "TYPE" => "STRING",
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "N",
        ),
        "RR_QUERY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KDX_RR_QUERY"),
            "TYPE" => "STRING",
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "N",
        ),
        "RR_PARAMS" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KDX_RR_PARAMS"),
            "TYPE" => "STRING",
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "N",
        ),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		/*"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),*/
	),
);
?>
