<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

/*$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty_UF = array();
$texttt='';
$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION");
foreach($arUserFields as $FIELD_NAME=>$arUserField){
    if($arUserField['USER_TYPE_ID'] == 'enumeration')
	$arProperty_UF[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME;
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"ITEMS_COUNT" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_ITEMS_COUNT"),
			"TYPE" => "STRING",
			"REFRESH" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);

if (0 < intval($arCurrentValues['IBLOCK_ID'])){
   $arPropList = array();
   $rsProps = CIBlockProperty::GetList(array(),array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']));
   while ($arProp = $rsProps->Fetch()){
      $arPropList[$arProp['CODE']] = $arProp['NAME'];
   }
   $arComponentParameters['PARAMETERS']['PROP_LIST'] = array(
      'NAME' => GetMessage("CP_BCSL_PROPS"),
      'TYPE' => 'LIST',
      'MULTIPLE'=>"Y",
      'VALUES' => $arPropList,
   );
   $arComponentParameters['PARAMETERS']['IS_NEW_PROPERTY'] = array(
      'NAME' => GetMessage("PROP_NEW"),
      'TYPE' => 'LIST',
      'MULTIPLE'=>"N",
      'VALUES' => $arPropList,
   );
   $arComponentParameters['PARAMETERS']['IS_SALE_PROPERTY'] = array(
      'NAME' => GetMessage("PROP_SALE"),
      'TYPE' => 'LIST',
      'MULTIPLE'=>"N",
      'VALUES' => $arPropList,
   );
   $arComponentParameters['PARAMETERS']['IS_POPULAR_PROPERTY'] = array(
      'NAME' => GetMessage("PROP_POPULAR"),
      'TYPE' => 'LIST',
      'MULTIPLE'=>"N",
      'VALUES' => $arPropList,
   );
   $arComponentParameters['PARAMETERS']['SHOW_ON_INDEX_PROPERTY'] = array(
      'NAME' => GetMessage("PROP_SHOW_ON_INDEX"),
      'TYPE' => 'LIST',
      'MULTIPLE'=>"N",
      'VALUES' => $arPropList,
   );
   $arComponentParameters['PARAMETERS']['MONTH_PRODUCT_PROPERTY'] = array(
      'NAME' => GetMessage("PROP_MONTH_PRODUCT"),
      'TYPE' => 'LIST',
      'MULTIPLE'=>"N",
      'VALUES' => $arPropList,
   );
}*/
?>
