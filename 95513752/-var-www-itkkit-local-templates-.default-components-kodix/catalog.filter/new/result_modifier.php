<?php
/**
 * Created by:  KODIX 26.03.2015 15:38
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach($arResult['PROPERTIES'] as $key => $arProp)
{
    unset($arResult['PROPERTIES'][$key]);
    $arResult['PROPERTIES'][$arProp['CODE']] = $arProp;
}

$arResult['SIZES_CLOTHING_TABLE'] = [];
$currentClothingSize = [];
foreach ($arResult['PROPERTY_VALUES']['SIZES_CLOTHING'] as $size => $cnt){
	$currentClothingSize[] = $size;
}
$arSizesClothing = [];
$resSizesClothing = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>26, '=PROPERTY_SIZES'=>$currentClothingSize), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_SIZES'));
while ($sizesClothing = $resSizesClothing->GetNext()){
	$arSizesClothing[$sizesClothing['NAME']][] = $sizesClothing['PROPERTY_SIZES_VALUE'];
}
$arResult['SIZES_CLOTHING_TABLE'] = $arSizesClothing;

$resSection = CIBlockSection::GetByID($arParams['SECTION_ID']);
if ($arSection = $resSection->GetNext()){
	$arResult['SECTION_URL'] = $arSection['SECTION_PAGE_URL'];
}

$arCurrentBrands = [];
foreach ($arResult['PROPERTY_VALUES']['CML2_MANUFACTURER'] as $key => $value){
	$arCurrentBrands[] = $key;
}

$arResult['SIZES_US_TRAINERS'] = [];
$arResult['SIZES_EU_TRAINERS'] = [];
foreach ($arResult['PROPERTY_VALUES']['SIZES_TRAINERS'] as $size => $count){
	if (!is_numeric($size)){
		if (!in_array($size, array('Sold','Sold Out','ONE_SIZE','Soon'))){
			if (stripos($size, 'woman')){
				$wSize = str_ireplace('woman', '<i>Woman</i>', $size);
				$arResult['SIZES_US_TRAINERS'][] = $wSize;
			} else {
				$arResult['SIZES_US_TRAINERS'][] = $size;
			}
		}
	}
}
foreach ($arResult['PROPERTY_VALUES']['SIZES_TRAINERS_EU'] as $size => $count){
	if (!in_array($size, array('Sold','Sold Out','ONE_SIZE','Soon'))){
		$arResult['SIZES_EU_TRAINERS'][] = $size;
	}
}

/*function getClothingSizeTable($size)
{
	$
	$resSizes = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>26, '=NAME'=>$size), false, array(), array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_SIZES'));
	if ($arSizes = $resSizes->GetNextElement()){
		$props = $arSizes->GetProperties();
		echo '<pre style="display: none">';
		print_r($props);
		echo '</pre>';
	}
}

foreach ($arResult['PROPERTY_VALUES']['SIZES_CLOTHING'] as $size => $count){
	getClothingSizeTable($size);
}*/

/*$arResult['SIZES_US_TRAINERS'] = [];
$arResult['SIZES_EU_TRAINERS'] = [];
foreach ($arResult['PROPERTY_VALUES']['SIZES_TRAINERS'] as $size => $count){
	$arResult['SIZES_US_TRAINERS'][] = $size;
}
foreach ($arResult['PROPERTY_VALUES']['SIZES_TRAINERS_EU'] as $size => $count){
	$arResult['SIZES_EU_TRAINERS'][] = $size;
}*/

/*$arResult['SIZES_TRAINERS_US'] = [];
$arResult['SIZES_TRAINERS_EU'] = [];
foreach ($arResult['PROPERTY_VALUES']['SIZES_TRAINERS'] as $size => $count){
	if (is_numeric($size) && $size !== 'Soon'){
	} else {
		$arResult['SIZES_TRAINERS_US'][] = $size;
	}
}
if (array_key_exists('Soon', $arResult['PROPERTY_VALUES']['SIZES_TRAINERS'])){
	$arResult['SIZES_TRAINERS_EU'][] = 'Soon';
}

$arResult['SIZES_TRAINERS_EU'] = [];
$resSizes = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>24, 'ACTIVE'=>'Y', '=PROPERTY_TYPE'=>56, 'PROPERTY_BRAND'=>144643), false, false, array('ID', 'IBLOCK_ID', 'NAME'));
while ($row = $resSizes->GetNextElement()){
	$fields = $row->GetFields();
	$props = $row->GetProperties();
	foreach ($props['SIZES']['VALUE'] as $key => $value){
		if (in_array($value, $arResult['SIZES_TRAINERS_US'])){
			$arResult['SIZES_TRAINERS_EU'][] = $props['SIZES']['DESCRIPTION'][$key];
		}
	}
}*/