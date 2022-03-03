<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//Проверка на установку модуля
if(!CModule::IncludeModule("iblock")) return;

//Получаем список типов ИБ
$arIBlockType = CIBlockParameters::GetIBlockTypes();

//получаем список ИБ
$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch()){
    $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arSorts = Array("ASC"=>GetMessage("KDX_SORT_ASC"), "DESC"=>GetMessage("KDX_SORT_DESC"));
$arSortFields = Array(
    "ID"=>GetMessage("KDX_SORT_BY_ID"),
    "NAME"=>GetMessage("KDX_SORT_BY_NAME"),
    "ACTIVE_FROM"=>GetMessage("KDX_SORT_BY_ACTIVE_FROM"),
    "SORT"=>GetMessage("KDX_SORT_BY_SORT"),
    "TIMESTAMP_X"=>GetMessage("KDX_SORT_BY_TIMESTAMP")
);


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
$arFilterPrices=array();
$dbResultList = CCatalogGroup::GetList(
    array('SORT'=>'ASC'),
    array('CAN_ACCESS'=>'Y','CAN_BUY'=>'Y',)
);
while($fields = $dbResultList ->Fetch()){
    $arSortsVariants["CATALOG_PRICE_".$fields['ID']]=$fields['NAME_LANG'];
    $arFilterPrices[$fields['ID']]=$fields['NAME_LANG'];
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
        'LIST'=>array(
            'NAME'=>GetMessage('KDX_LIST_GROUP')
        ),
        'FILTER'=>array(
            'NAME'=>GetMessage('KDX_FILTER_GROUP')
        )
    ),
    "PARAMETERS" => array(
        "SEF_MODE" => array(
            "new" => array(
                "NAME" => GetMessage("KDX_NEW_PAGE"),
                "DEFAULT" => "new/",
                "VARIABLES" => array(),
            ),
            "sale" => array(
                "NAME" => GetMessage("KDX_SALE_PAGE"),
                "DEFAULT" => "sale/",
                "VARIABLES" => array(),
            ),
            "detail" => array(
                "NAME" => GetMessage("KDX_DETAIL_PAGE"),
                "DEFAULT" => "product/#CODE#/",
                "VARIABLES" => array(),
            ),

            "brand" => array(
                "NAME" => GetMessage("KDX_BRAND_PAGE"),
                "DEFAULT" => "brand/#BRAND#/",
                "VARIABLES" => array(),
            ),
            "brand_section" => array(
                "NAME" => GetMessage("KDX_BRAND_SECTION_PAGE"),
                "DEFAULT" => "brand/#BRAND#/#SECTION_CODE_PATH#/",
                "VARIABLES" => array(),
            ),
            "section" => array(
                "NAME" => GetMessage("KDX_SECTION_PAGE"),
                "DEFAULT" => "#SECTION_CODE_PATH#/",
                "VARIABLES" => array(),
            ),
            "selection" => array(
                "NAME" => GetMessage("KDX_SELECTION_PAGE"),
                "DEFAULT" => "selection/#SELECTION#/",
                "VARIABLES" => array(),
            ),
        ),

        "IBLOCK_TYPE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KDX_CATALOG_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
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
            "PARENT" => "LIST",
            "NAME" => GetMessage("KDX_ITEMS_COUNT"),
            "TYPE" => "STRING",
            "REFRESH" => "N",
        ),
        "PAGINATION_TEMPLATE" => array(
            "PARENT" => "LIST",
            "NAME" => GetMessage("KDX_PAGINATION_TEMPLATES"),
            "TYPE" => "LIST",
            "REFRESH" => "N",
            "VALUES"=>$paginationTemplates,
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => ".default",
        ),
        "PAGINATION_WRAPPER" => array(
            "PARENT" => "LIST",
            "NAME" => GetMessage("KDX_PAGINATION_WRAPPER"),
            "TYPE" => "STRING",
            "REFRESH" => "N",
        ),
        "SORT_BY1" => Array(
            "PARENT" => "LIST",
            "NAME" => GetMessage("KDX_SORT_FIRST_FIELD"),
            "TYPE" => "LIST",
            "DEFAULT" => "ACTIVE_FROM",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "N",
        ),
        "SORT_ORDER1" => Array(
            "PARENT" => "LIST",
            "NAME" => GetMessage("KDX_SORT_FIRST_DIRECTION"),
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "N",
        ),
        "SORT_BY2" => Array(
            "PARENT" => "LIST",
            "NAME" => GetMessage("KDX_SORT_SECOND_FIELD"),
            "TYPE" => "LIST",
            "DEFAULT" => "SORT",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "N",
        ),
        "SORT_ORDER2" => Array(
            "PARENT" => "LIST",
            "NAME" => GetMessage("KDX_SORT_SECOND_DIRECTION"),
            "TYPE" => "LIST",
            "DEFAULT" => "ASC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "N",
        ),
        "SORT_VARIANTS" => Array(
            "PARENT" => "LIST",
            "NAME" => GetMessage("KDX_SORT_VARIANTS"),
            "TYPE" => "LIST",
            "DEFAULT" => "SORT",
            "VALUES" => $arSortsVariants,
            "ADDITIONAL_VALUES" => "N",
            "MULTIPLE"=>"Y",
        ),
        "FILTER_PRICE" => Array(
            "PARENT" => "FILTER",
            "NAME" => GetMessage("KDX_FILTER_PRICE"),
            "TYPE" => "LIST",
            "VALUES" => $arFilterPrices,
            "ADDITIONAL_VALUES" => "N",
        ),

        "CACHE_TIME"  =>  Array("DEFAULT"=>3600),
    ),
);

?>