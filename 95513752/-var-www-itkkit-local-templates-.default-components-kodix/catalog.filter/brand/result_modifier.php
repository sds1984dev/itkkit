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

$arResult['SIZES_US_TRAINERS'] = [];
$arResult['SIZES_EU_TRAINERS'] = [];
foreach ($arResult['PROPERTY_VALUES']['SIZES_TRAINERS'] as $size => $count){
	if (!is_numeric($size)){
		if (!in_array($size, array('Sold','Sold Out','ONE_SIZE','Soon'))){
			$arResult['SIZES_US_TRAINERS'][] = $size;
		}
	}
}
foreach ($arResult['PROPERTY_VALUES']['SIZES_TRAINERS_EU'] as $size => $count){
	if (!in_array($size, array('Sold','Sold Out','ONE_SIZE','Soon'))){
		$arResult['SIZES_EU_TRAINERS'][] = $size;
	}
}