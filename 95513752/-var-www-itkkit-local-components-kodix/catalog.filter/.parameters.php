<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * Created by:  KODIX 07.07.14 12:49
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Salnikov Dmitry
 */
//Проверка на установку модуля
if(!CModule::IncludeModule("iblock")) return;
if(!CModule::IncludeModule("catalog")) return;


//получаем список ИБ
$arIBlock=array();
$res = CCatalog::GetList(array(),array('IBLOCK_ACTIVE'=>'Y','!OFFERS_IBLOCK_ID'=>false,'PRODUCT_IBLOCK_ID'=>false));
while($fields = $res->Fetch()){
    $arIBlock[$fields['IBLOCK_ID']]='['.$fields['IBLOCK_ID'].'] '.$fields['NAME'] ;
}
$dbResultList = CCatalogGroup::GetList(
    array('SORT'=>'ASC'),
    array('CAN_ACCESS'=>'Y','CAN_BUY'=>'Y',)
);
while($fields = $dbResultList ->Fetch()){
    $arFilterPrices[$fields['ID']]=$fields['NAME_LANG'];
}
$arComponentParameters = array(
	"GROUPS" => array(
        'FIELDS'=>array(
            "NAME" => GetMessage("KODIX_FILTER_FIELDS_GROUP"),
        )
	),
	"PARAMETERS" => array(
        "CATALOG_IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KODIX_FILTER_IBLOCK_ID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ),
        "FILTER_PRICE" => Array(
            "PARENT" => "FIELDS",
            "NAME" => GetMessage("KODIX_FILTER_PRICE"),
            "TYPE" => "LIST",
            "VALUES" => $arFilterPrices,
            "ADDITIONAL_VALUES" => "N",
        ),
        "SECTION_ID" => Array(
            "PARENT" => "FIELDS",
            "NAME" => GetMessage("KODIX_SECTION_ID"),
            "TYPE" => "STRING",
        ),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
	),
);
?>