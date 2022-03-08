<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty_UF = array();
$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION");
foreach($arUserFields as $FIELD_NAME=>$arUserField){
    if($arUserField['USER_TYPE_ID'] == 'enumeration')
	$arProperty_UF[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME;
}

$arSorts = Array("ASC"=>GetMessage("KDX_SORT_ASC"), "DESC"=>GetMessage("KDX_SORT_DESC"));
$arSortFields = Array(
    "ID"=>GetMessage("KDX_SORT_BY_ID"),
    "NAME"=>GetMessage("KDX_SORT_BY_NAME"),
    "ACTIVE_FROM"=>GetMessage("KDX_SORT_BY_ACTIVE_FROM"),
    "SORT"=>GetMessage("KDX_SORT_BY_SORT"),
    "TIMESTAMP_X"=>GetMessage("KDX_SORT_BY_TIMESTAMP")
);


$brands_res=CIBlockElement::GetList(array("NAME"=>"ASC"), array(
    "IBLOCK_ID"=>$arCurrentValues["BRANDS_IBLOCK_ID"]
), false, false, array("ID", "NAME", "XML_ID"));
$brands=array();
while($b=$brands_res->GetNext()){
    $brands[$b["XML_ID"]]=$b["NAME"];
}

$arPaginationComponentTemplates = CComponentUtil::GetTemplatesList('bitrix:system.pagenavigation');
$arTemplateID = array();
foreach($arPaginationComponentTemplates as $template)
    if($template["TEMPLATE"] <> '' && $template["TEMPLATE"] <> '.default')
        $arTemplateID[] = $template["TEMPLATE"];

$arTemplates = array(".default"=>GetMessage("comp_prop_default_templ"));
if(!empty($arTemplateID))
{
    $db_site_templates = CSiteTemplate::GetList(array(), array("ID"=>$arTemplateID), array());
    while($ar_site_templates = $db_site_templates->Fetch())
        $arTemplates[$ar_site_templates['ID']] = $ar_site_templates['NAME'];
}
$paginationTemplates=array(/*'.default'=>'.default'*/);

foreach($arPaginationComponentTemplates as $template){
    $showTemplateName = ($template["TEMPLATE"] <> '' && $arTemplates[$template["TEMPLATE"]] <> ''? $arTemplates[$template["TEMPLATE"]] : GetMessage("comp_prop_template_sys"));
    $paginationTemplates[$template['NAME']]= htmlspecialcharsbx($template["NAME"]." (".$showTemplateName.")");
}

$arSortsVariants=array();

if(!CModule::IncludeModule('catalog')) die('catalog module');
$dbResultList = CCatalogGroup::GetList(
    array('SORT'=>'ASC'),
    array('CAN_ACCESS'=>'Y','CAN_BUY'=>'Y',)
);
while($fields = $dbResultList ->Fetch()){
    $arSortsVariants["CATALOG_PRICE_".$fields['ID']]=$fields['NAME_LANG'];
}
$arSortsVariants["ACTIVE_FROM"]=GetMessage("KDX_SORT_BY_ACTIVE_FROM");

if(intval($arCurrentValues["CATALOG_IBLOCK_ID"])){
    $res = CIBlockProperty::GetList(array('SORT'=>'ASC'),array('ACTIVE'=>'Y','IBLOCK_ID'=>$arCurrentValues["CATALOG_IBLOCK_ID"]));
    while($fields = $res->Fetch()){
        $arSortsVariants['PROPERTY_'.$fields['CODE']]=$fields['NAME'];
    }
}

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
        "CATALOG_IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KDX_CATALOG_IBLOCK_ID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ),
        "SKU_IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KDX_SKU_IBLOCK_ID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ),
        "BRANDS_IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KDX_BRANDS_IBLOCK_ID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ),
        "GRIDS_IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KDX_GRIDS_IBLOCK_ID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ),
		"PAGE_ITEMS_COUNT" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("KDX_ITEMS_COUNT"),
			"TYPE" => "STRING",
			"REFRESH" => "N",
		),
		"PAGINATION_TEMPLATE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("KDX_PAGINATION_TEMPLATES"),
			"TYPE" => "LIST",
			"REFRESH" => "N",
            "VALUES"=>$paginationTemplates,
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => ".default",
		),
        "PAGINATION_WRAPPER" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("KDX_PAGINATION_WRAPPER"),
            "TYPE" => "STRING",
            "REFRESH" => "N",
        ),
        "SORT_BY1" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("KDX_SORT_FIRST_FIELD"),
            "TYPE" => "LIST",
            "DEFAULT" => "ACTIVE_FROM",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "N",
        ),
        "SORT_ORDER1" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("KDX_SORT_FIRST_DIRECTION"),
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "N",
        ),
        "SORT_BY2" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("KDX_SORT_SECOND_FIELD"),
            "TYPE" => "LIST",
            "DEFAULT" => "SORT",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "N",
        ),
        "SORT_ORDER2" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("KDX_SORT_SECOND_DIRECTION"),
            "TYPE" => "LIST",
            "DEFAULT" => "ASC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "N",
        ),
        "SORT_VARIANTS" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("KDX_SORT_VARIANTS"),
            "TYPE" => "LIST",
            "DEFAULT" => "SORT",
            "VALUES" => $arSortsVariants,
            "ADDITIONAL_VALUES" => "N",
            "MULTIPLE"=>"Y",
        ),
        "JUST_NEW" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("KDX_FILTER_NEW"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "ASC",
            "ADDITIONAL_VALUES" => "N",
        ),
        "SALE" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("KDX_FILTER_SALE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "ASC",
            "ADDITIONAL_VALUES" => "N",
        ),

        "BRANDS_FILTER" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("KDX_BRANDS_FILTER"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $brands,
            "MULTIPLE"=>"Y",
        ),

        "SECTION_ID" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("KDX_SECTION_ID"),
            "TYPE" => "STRING",
            "MULTIPLE"=>"N",
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

/*if (0 < intval($arCurrentValues['IBLOCK_ID'])){
   $arPropList = array();
   $rsProps = CIBlockProperty::GetList(array(),array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']));
   while ($arProp = $rsProps->Fetch()){
      $arPropList[$arProp['CODE']] = $arProp['NAME'];
   }
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
