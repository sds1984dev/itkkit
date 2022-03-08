<?//++отключение ботов?>
<? 
require($_SERVER['DOCUMENT_ROOT'].'/antibot.php');?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Catalog");
$APPLICATION->SetPageProperty('PAGE_CLASS', 'brand_list_page');
$APPLICATION->SetPageProperty('SHOW_BANNER', 'Y');
$APPLICATION->SetPageProperty('MAIN_CLASS','main--full-height');
$APPLICATION->SetPageProperty('PAGE_WRAPPER_CLASS','grid-container--space-between');
?>

<?$APPLICATION->IncludeComponent(
    "kodix:catalog",
    ".default",
    array(
        "IBLOCK_TYPE" => "kodix_catalog",
        "IBLOCK_ID" => "1",
        "SELECTIONS_IBLOCK_ID" => "11",
        "SHOW_404" => "Y",
        "SEF_MODE" => "Y",
        "SEF_FOLDER" => "/",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
        "PAGE_ITEMS_COUNT" => "24",
        "PAGINATION_TEMPLATE" => "kit_modern",
        "PAGINATION_WRAPPER" => "#catalog .catalog_block",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_ORDER1" => "DESC",
        "SORT_BY2" => "SORT",
        "SORT_ORDER2" => "ASC",
        "SORT_VARIANTS" => array(
        ),
        "CATALOG_IBLOCK_ID" => "1",
        "SKU_IBLOCK_ID" => "2",
        "BRANDS_IBLOCK_ID" => "3",
        "GRIDS_IBLOCK_ID" => "4",
        "FILTER_PRICE" => "1",
        "FILTER_PRICE_PROP" => "RETAIL_PRICE_MIN",
        "SEF_URL_TEMPLATES" => array(

            "auction" => "catalog/auction/index.php",
            "auction_section" => "catalog/auction/#SECTION_CODE_PATH#/",

            "new" => "catalog/new/index.php",
            "new_section" => "catalog/new/#SECTION_CODE_PATH#/",

            "sale" => "catalog/sale/index.php",
            "sale_section" => "catalog/sale/#SECTION_CODE_PATH#/",

            "gift" => "catalog/gift/index.php",
            //"gift_section" => "catalog/gift/#SECTION_CODE_PATH#/",

            "itkchoice" => "catalog/itkchoice/index.php",
            //"itkchoice" => "catalog/itkchoice/#SECTION_CODE_PATH#/",

            "brand_list" => "catalog/brands/index.php",
            "brand" => "catalog/brand/#BRAND#/index.php",
            "brand_section" => "catalog/brand/#BRAND#/#SECTION_CODE_PATH#/",
            //"section_brand" => "catalog/#SECTION_CODE_PATH#/brand/#BRAND#/",
            "brand_section_color" => "catalog/brand/#BRAND#/#SECTION_CODE_PATH#/#COLOR#/",
            "section_brand_color" => "catalog/#SECTION_CODE_PATH#/brand/#BRAND#/#COLOR#/",

            "brand_section_tag" => "catalog/brand/#BRAND#/#SECTION_CODE_PATH#/tag/#TAG#/",

            "section_tag" => "catalog/#SECTION_CODE_PATH#/tag/#TAG#/",
            "section_tag_brand" => "catalog/#SECTION_CODE_PATH#/tag/#TAG#/brand/#BRAND#/",

            "list" => "catalog/index.php",
            "section" => "catalog/#SECTION_CODE_PATH#/",

            //"selection" => "catalog/selection/#SELECTION#/",
            
            "detail" => "catalog/product/#ELEMENT_ID#_#CODE#/",
            "search" => "search/index.php",
            'auction' => "catalog/auction/#ELEMENT_ID#/",
        )
    ),
    false
);?>
<?php
//if((int)$_REQUEST['PAGEN_1'] > 1){
//    $page = 'https://www.itkkit.com' . $APPLICATION->GetCurUri();
//    $APPLICATION->AddHeadString('<link rel="canonical" href="'.$page.'" />');
//}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>