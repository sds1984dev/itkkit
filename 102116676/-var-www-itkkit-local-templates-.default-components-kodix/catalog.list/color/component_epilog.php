<?php

if($arResult['SELECTION_DATA']['SELECTION_ID']) {
    $selection = $arResult['SELECTION_DATA']['DATA'];

    /*if(LANGUAGE_ID=='en') {
        $APPLICATION->SetPageProperty('description', $selection['PROPERTY_DESCRIPTION_EN_VALUE']);
        $APPLICATION->SetPageProperty('keywords', $selection['PROPERTY_KEYWORDS_EN_VALUE']);
        $APPLICATION->SetPageProperty('title', $selection['PROPERTY_TITLE_EN_VALUE']);
    } else {
        $APPLICATION->SetPageProperty('description', $selection['PROPERTY_DESCRIPTION_RU_VALUE']);
        $APPLICATION->SetPageProperty('keywords', $selection['PROPERTY_KEYWORDS_RU_VALUE']);
        $APPLICATION->SetPageProperty('title', $selection['PROPERTY_TITLE_RU_VALUE']);
    }*/

    if($fileID = $selection['PROPERTY_BACKGROUND_IMAGE_VALUE']) {
        $background = 'url('.CFile::GetPath($fileID).') '.$selection['PROPERTY_IMAGE_STYLE_VALUE'].' ';
    }

    $APPLICATION->SetPageProperty('SELECTION_COLOR', $background.$selection['PROPERTY_BACKGROUND_COLOR_VALUE']);
    $APPLICATION->SetPageProperty('SELECTION_PRODUCT_STYLE', 'border: '.$selection['PROPERTY_PRODUCT_LINES_STYLE_VALUE']);
    $APPLICATION->SetPageProperty('SELECTION_STYLE', '<style>'.$selection['PROPERTY_SELECTION_STYLE_VALUE'].'</style>');
}

if (in_array(strtolower($arParams['COLORS_FILTER']), getAllColors())){
    $arCurrentSection = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>1, 'CODE'=>$arParams['SECTION_CODE']), true, array('ID', 'IBLOCK_ID', 'NAME', 'UF_EN_NAME'))->GetNext();
    $arCurrentBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, 'CODE'=>$arParams['BRAND_CODE']), false, array(), array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_EN_NAME'))->Fetch();
    if (LANGUAGE_ID == 'en'){
        $APPLICATION->SetPageProperty('title', 'Shop '.ucfirst($arParams['COLORS_FILTER']).' '.$arCurrentBrand['PROPERTY_EN_NAME_VALUE'].' '.$arCurrentSection['UF_EN_NAME'].' at itk online store');
        $APPLICATION->SetPageProperty('description', 'Shop '.ucfirst($arParams['COLORS_FILTER']).' '.$arCurrentBrand['PROPERTY_EN_NAME_VALUE'].' '.$arCurrentSection['UF_EN_NAME'].' at itk online store. Free Shipping on All Orders Over &euro;350 &#10003; Fast Delivery &#10003; 14 Days Return Policy &#10003; 100% Authenticity &#10003;.');
        $APPLICATION->SetTitle(ucfirst($arParams['COLORS_FILTER']).' '.$arCurrentBrand['PROPERTY_EN_NAME_VALUE'].' '.$arCurrentSection['UF_EN_NAME']);
    } else {
        $APPLICATION->SetPageProperty('title', 'Купить '.ucfirst($arParams['COLORS_FILTER']).' '.$arCurrentBrand['NAME'].' '.$arCurrentSection['NAME'].' в онлайн магазине itk');
        $APPLICATION->SetPageProperty('description', 'Купить '.ucfirst($arParams['COLORS_FILTER']).' '.$arCurrentBrand['NAME'].' '.$arCurrentSection['NAME'].' в онлайн магазине itk. Бесплатная доставка на все заказы свыше €350 &#10003; Быстрая доставка &#10003; 14 дневная политика возврата товаров &#10003; 100% оригинальный товар &#10003;');   
        $APPLICATION->SetTitle(ucfirst($arParams['COLORS_FILTER']).' '.$arCurrentBrand['NAME'].' '.$arCurrentSection['NAME']);
    }
} else {
    $arCurrentSection = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>1, 'CODE'=>$arParams['SECTION_CODE']), true, array('ID', 'IBLOCK_ID', 'NAME', 'UF_EN_NAME'))->GetNext();
    $arCurrentBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, 'CODE'=>$arParams['BRAND_CODE']), false, array(), array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_EN_NAME'))->Fetch();
    if (LANGUAGE_ID == 'en'){
        $APPLICATION->SetPageProperty('title', 'Shop '.$arCurrentBrand['PROPERTY_EN_NAME_VALUE'].' '.$arParams['TAG'].' '.$arCurrentSection['UF_EN_NAME'].' at itk online store');
        $APPLICATION->SetPageProperty('description', 'Shop '.$arCurrentBrand['PROPERTY_EN_NAME_VALUE'].' '.$arParams['TAG'].' '.$arCurrentSection['UF_EN_NAME'].' at itk online store. Free Shipping on All Orders Over €350 ✓ Fast Delivery ✓ 14 Days Return Policy ✓ 100% Authenticity ✓.');
        $APPLICATION->SetTitle($arCurrentBrand['PROPERTY_EN_NAME_VALUE'].' '.$arParams['TAG'].' '.$arCurrentSection['UF_EN_NAME']);
    } else {
        $APPLICATION->SetPageProperty('title', 'Купить '.$arCurrentBrand['NAME'].' '.$arParams['TAG'].' '.$arCurrentSection['NAME'].' в онлайн магазине itk');
        $APPLICATION->SetPageProperty('description', 'Купить '.$arCurrentBrand['NAME'].' '.$arParams['TAG'].' '.$arCurrentSection['NAME'].' в онлайн магазине itk. Бесплатная доставка на все заказы свыше €350 &#10003; Быстрая доставка &#10003; 14 дневная политика возврата товаров &#10003; 100% оригинальный товар &#10003;');
        $APPLICATION->SetTitle($arCurrentBrand['NAME'].' '.$arParams['TAG'].' '.$arCurrentSection['NAME']);
    }
}

if (LANGUAGE_ID == 'ru') {
    $page = $APPLICATION->GetCurPage(false);
    if (file_exists(__DIR__ . '/seo.csv')) {
        $file = fopen(__DIR__ . '/seo.csv', 'r');
        $data = array();
        while (($buffer = fgets($file, 4096)) !== false) {
            $line = explode('|', $buffer);
            $data[trim($line[0])]['h1'] = $line[1];
            $data[trim($line[0])]['title'] = $line[2];
            $data[trim($line[0])]['desc'] = trim($line[3]);
        }
        fclose($file);

        if (array_key_exists($page, $data)) {
            $APPLICATION->SetPageProperty('title', $data[$page]['title']);
            $APPLICATION->SetTitle($data[$page]['h1']);
            $APPLICATION->SetPageProperty('description', $data[$page]['desc']);
        }
    }
}

$APPLICATION->AddChainItem(ucfirst($arParams['COLORS_FILTER']), '');