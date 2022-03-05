<?php
/**
 * Created by PhpStorm.
 * User: Kodix
 * Date: 02.08.2017
 * Time: 18:01
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$cur_page = $APPLICATION->GetCurPage(true);

$cur_page_no_index = $APPLICATION->GetCurPage(false);
CModule::IncludeModule("iblock");

foreach($arResult as $key => $arItem){
    $current_depth = $arItem['PARAMS']['DEPTH_LEVEL']?:$arItem['DEPTH_LEVEL'];

    if($current_depth == 1){

        $first_level_index = count($newMenu);
        
        if($arItem['PARAMS']['BRANDS'] == "Y") {
            $cache_filename='/var/www/itkkit/local/cache_sql/'.$_SERVER['HTTP_HOST'].'_local_templates_.default_components_bitrix_menu_kit_main_new_result_modifier.newMenu_26.obj';
            
            if (file_exists($cache_filename)) {
                //если есть кеш
                $objData = file_get_contents($cache_filename);
                $newMenu = unserialize($objData);
            } else {
                $arBrands = KDXSaleDataCollector::getBrands(false,array('PROPERTY_SHOW_MENU_VALUE' => 'Y'));
                $global_filter = getGlobalFilterForSite();
                $arFilter = Array(
                    "IBLOCK_ID" => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
                    "PROPERTY_CML2_MANUFACTURER" => array_keys($arBrands)
                );
                $arFilter = array_merge($global_filter,$arFilter);
                $arGroup = array("PROPERTY_CML2_MANUFACTURER","ID");
                $res = CIBlockElement::GetList(array("SORT"=>"ASC"), $arFilter, $arGroup, false);
                $arAvailableBrands = array();
                while($ar_fields = $res->Fetch())
                {
                    $brandID = $ar_fields['PROPERTY_CML2_MANUFACTURER_VALUE'];
                    $SELECTED = CMenu::IsItemSelected($arBrands[$brandID]['DETAIL_PAGE_URL'], $cur_page, $cur_page_no_index);
                    if($SELECTED)
                        $arBrands[$brandID]['SELECTED'] = true;
                    $arAvailableBrands[$brandID]['item'] = $arBrands[$brandID];
                }
                unset($arBrands);
                uasort($arAvailableBrands, function($first, $second) {
                    return strcasecmp($first['item']['NAME'], $second['item']['NAME']);
                });
                $newMenu[$first_level_index] = array(
                    'item' => $arItem,
                    'children' => $arAvailableBrands
                );
                $objData = serialize($newMenu);
                $t=file_put_contents($cache_filename, $objData);
            }
        } else {
            $newMenu[$first_level_index] = array(
                'item' => $arItem,
                'children' => array()
            );
            $column = 0;
        }

        $arBrandFilter = array('IBLOCK_ID'=>KDXSettings::getSetting('BRANDS_IBLOCK_ID'),'PROPERTY_SHOW_MENU_VALUE'=>'Y','PROPERTY_CATEGORY'=>$arItem['PARAMS']['SID']);

    } elseif($current_depth == 2){
        $second_level_index = count($newMenu[$first_level_index]['children']);

        $newMenu[$first_level_index]['children'][$second_level_index] = array(
            'item' => $arItem,
            'children' => array()
        );
    }else{//третий уровень
        $third_level_index = count($newMenu[$first_level_index]['children'][$second_level_index]['children']);

        $newMenu[$first_level_index]['children'][$second_level_index]['children'][$third_level_index] = array(
            'item' => $arItem,
        );
    }
}


foreach ($newMenu as $key => $value) {
    if (isset($value["children"])) {
        foreach($value["children"] as $key1 => $value1) {
            if (strlen(strstr($value1["item"]["LINK"], "tag")) > 0) {
                $dbTag = CIBlockElement::GetList(Array(), Array("IBLOCK_ID" => [21,22], "NAME" => $value1["item"]["TEXT"]))->Fetch();
                // TODO проверить еще раз и выложить на бой
                if ($dbTag["ACTIVE"] == "N")
                    unset($newMenu[$key]["children"][$key1]);
            }
        }
    }
}

$arResult = $newMenu;
foreach($arResult as &$arItem){
    foreach($arItem['children'] as $arChild){
        if($arChild['item']['SELECTED'] == 1){
            $arItem['item']['SELECTED'] = 1;
        }
    }
}

if (SITE_ID == 'en') {
    $arSections = ['Clothing', 'Footwear', 'Accessories'];
    foreach($arResult as $key => &$item) {
        if (in_array($item['item']['TEXT'], $arSections)) {
            if (count($item['children']) > 0) {
                usort($item['children'], 'sort_subitems');
            }
        }
    }
}

if (!function_exists('sort_subitems')) {
    function sort_subitems($a, $b) {
        if ($a['item']['TEXT'][0] > $b['item']['TEXT'][0]) return true;
        if ($a['item']['TEXT'][0] == $b['item']['TEXT'][0]) {
            if ($a['item']['TEXT'][1] > $b['item']['TEXT'][1]) return true;
        }
    }
}


$arOrder = array();
$arFilter = array('IBLOCK_ID' => 1, 'PROPERTY_BADGE_VALUE' => 'Sale', 'INCLUDE_SUBSECTIONS' => 'Y', 'ACTIVE' => 'Y');
$arSelect = array('IBLOCK_SECTION_ID');
$dbElements = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);


// $arSaleBrands = array();

// while ($arElement = $dbElements->Fetch()) {
//     $arProperty = CIBlockElement::GetPropertyValues(1, array('ID' => $arElement['ID']), false, array('ID' => 1))->Fetch();
//     $arBrand = CIBlockElement::GetByID($arProperty[1][0])->Fetch();
    //NAME, CODE
//     echo "<pre>";
// print_r($arBrand);
// echo "</pre>";
// die();
//     $arBrands[] = $arBrand['NAME'];
// }
// $arSaleBrands = array_unique($arBrands);

$arSaleSection = array();

while ($arElement = $dbElements->Fetch()) {
    $rsParentSection = CIBlockSection::GetByID($arElement['IBLOCK_SECTION_ID']);
    if ($arParentSection = $rsParentSection->GetNext())
    {
       $arFilter = array('IBLOCK_ID' => 1,'>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL'], 'UF_SHOW_SALE' => true);
       $rsSect = CIBlockSection::GetList(array('IBLOCK_ID' => 1, 'left_margin' => 'asc'),$arFilter, false, array('UF_EN_NAME'));
       while ($arSect = $rsSect->GetNext())
       {
            $sectionParts = explode('/', $arSect['SECTION_PAGE_URL']);
            $fromattedSect = '/' . $sectionParts[1] . '/sale/' . $sectionParts[2] . '/' . $sectionParts[3];
            $arSaleSection[] = array('NAME' => (SITE_ID == 's1') ? $arSect['NAME'] : $arSect['UF_EN_NAME'], 'SECTION_PATH' => $fromattedSect);
       }
    }
}

$arMenuSale = array();

foreach ($arSaleSection as $section) {
    $arSection = array('item' => array(), 'children' => array());
    $arSection['item']['TEXT'] = $section['NAME'];
    $arSection['item']['LINK'] = $section['SECTION_PATH'];
    $arSection['item']['DEPTH_LEVEL'] = 1;
    $arSection['item']['PERMISSION'] = 'R';
    $arSection['item']['PARAMS']['DEPTH_LEVEL'] = 2;
    $arMenuSale[] = $arSection;
}

foreach ($arResult as &$section) {
    if ($section['item']['TEXT'] == 'Sale') {
        $section['children'] = $arMenuSale;
        break;
    }
}
    
?>
