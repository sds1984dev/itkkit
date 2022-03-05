<?php

if($arResult['SELECTION_DATA']['SELECTION_ID']) {
    $selection = $arResult['SELECTION_DATA']['DATA'];

    if($fileID = $selection['PROPERTY_BACKGROUND_IMAGE_VALUE']) {
        $background = 'url('.CFile::GetPath($fileID).') '.$selection['PROPERTY_IMAGE_STYLE_VALUE'].' ';
    }

    $APPLICATION->SetPageProperty('SELECTION_COLOR', $background.$selection['PROPERTY_BACKGROUND_COLOR_VALUE']);
    $APPLICATION->SetPageProperty('SELECTION_PRODUCT_STYLE', 'border: '.$selection['PROPERTY_PRODUCT_LINES_STYLE_VALUE']);
    $APPLICATION->SetPageProperty('SELECTION_STYLE', '<style>'.$selection['PROPERTY_SELECTION_STYLE_VALUE'].'</style>');

    if (!empty($arResult['SELECTION_DATA']['DATA']['NAME'])) {
      $APPLICATION->SetTitle($arResult['SELECTION_DATA']['DATA']['NAME']);
      if (LANGUAGE_ID == 'en') {
        $APPLICATION->addChainItem($arResult['SELECTION_DATA']['DATA']['NAME']);
      }
    }
}

if (!empty($arParams['BRANDS_FILTER'])){
    $arCurrentSection = '';
    if (LANGUAGE_ID == 'en'){
        if (!empty($arParams['SECTION_ID'])){
            $arCurrentSection = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>1, 'ID'=>$arParams['SECTION_ID']), true, array('ID', 'IBLOCK_ID', 'UF_EN_NAME'))->GetNext()['UF_EN_NAME'];
        }
        $arCurrentBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, 'ID'=>$arParams['BRANDS_FILTER']), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_EN_NAME'))->Fetch()['PROPERTY_EN_NAME_VALUE'];
    } else {
        if (!empty($arParams['SECTION_ID'])){
            $arCurrentSection = CIBlockSection::GetList(array('name'=>'asc'), array('IBLOCK_ID'=>1, 'ID'=>$arParams['SECTION_ID']), true, array('ID', 'IBLOCK_ID', 'NAME'))->GetNext()['NAME'];
        }
        $arCurrentBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, 'ID'=>$arParams['BRANDS_FILTER']), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_RU_NAME'))->Fetch()['PROPERTY_RU_NAME_VALUE'];
    }

    if (LANGUAGE_ID == 'en'){
        $APPLICATION->SetPageProperty('title', 'Shop '.$arCurrentBrand.' '.$arCurrentSection.' at itk online store');
        $APPLICATION->SetPageProperty('description', 'Shop '.$arCurrentBrand.' '.$arCurrentSection.' at itk online store. Free Shipping on All Orders Over &euro;350 &#10003; Fast Delivery &#10003; 14 Days Return Policy &#10003; 100% Authenticity &#10003;.');
    } else {
        $APPLICATION->SetPageProperty('title', 'Купить '.$arCurrentBrand.' '.$arCurrentSection.' в онлайн магазине itk');
        $APPLICATION->SetPageProperty('description', 'Купить '.$arCurrentBrand.' '.$arCurrentSection.' в онлайн магазине itk. Бесплатная доставка на все заказы свыше €350 &#10003; Быстрая доставка &#10003; 14 дневная политика возврата товаров &#10003; 100% оригинальный товар &#10003;');
    }

    $APPLICATION->SetTitle($arCurrentBrand . ' ' . $arCurrentSection);
}

if (!empty($arParams['SECTION_ID']) && !CSite::InDir('/catalog/brand/')){
    $resSection = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$arParams['CATALOG_IBLOCK_ID'], 'ID'=>$arParams['SECTION_ID']), true, array('*', 'UF_EN_NAME'));
    if ($arSection = $resSection->GetNext()){
        $sectionName = $arSection['NAME'];
        if (LANGUAGE_ID == 'en' && !empty($arSection['UF_EN_NAME'])){
            $sectionName = $arSection['UF_EN_NAME'];
        }
        $title = $APPLICATION->GetProperty("title");
        $description = $APPLICATION->GetProperty("description");
        $url = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

        $APPLICATION->AddHeadString('<meta property="og:title" content="'.$title.'"/>', true);
        $APPLICATION->AddHeadString('<meta property="og:description" content="'.$description.'"/>', true);
        $APPLICATION->AddHeadString('<meta property="og:type" content="website"/>', true);
        $APPLICATION->AddHeadString('<meta property="og:url" content="https://'.$_SERVER['SERVER_NAME'].$url.'"/>', true);
        $APPLICATION->AddHeadString('<meta property="og:site_name" content="'.$_SERVER['SERVER_NAME'].'"/>', true);
        $APPLICATION->AddHeadString('<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/images/itk-og.jpg"/>', true);
        $APPLICATION->AddHeadString('<meta property="vk:image" content="https://'.$_SERVER['SERVER_NAME'].'/images/itk-vk-image.jpg"/>', true);

        $APPLICATION->AddHeadString('<meta property="twitter:title" content="'.$title.'"/>', true);
        $APPLICATION->AddHeadString('<meta property="twitter:description" content="'.$description.'"/>', true);
        $APPLICATION->AddHeadString('<meta property="twitter:site" content="'.$_SERVER['SERVER_NAME'].'"/>', true);
        $APPLICATION->AddHeadString('<meta property="twitter:card" content="summary_large_image"/>', true);
        $APPLICATION->AddHeadString('<meta property="twitter:url" content="https://'.$_SERVER['SERVER_NAME'].$url.'"/>', true);
        $APPLICATION->AddHeadString('<meta property="twitter:image:src" content="https://'.$_SERVER['SERVER_NAME'].'/images/logo-social.jpg?ver=1"/>', true);
    }
}/* elseif (CSite::InDir('/catalog/brand/')) {
    $title = $APPLICATION->GetProperty("title");
    $description = $APPLICATION->GetProperty("description");
    $url = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

    $APPLICATION->AddHeadString('<meta property="og:title" content="'.$title.'"/>', true);
    $APPLICATION->AddHeadString('<meta property="og:description" content="'.$description.'"/>', true);
    $APPLICATION->AddHeadString('<meta property="og:type" content="website"/>', true);
    $APPLICATION->AddHeadString('<meta property="og:url" content="https://'.$_SERVER['SERVER_NAME'].$url.'"/>', true);
    $APPLICATION->AddHeadString('<meta property="og:site_name" content="'.$_SERVER['SERVER_NAME'].'"/>', true);
    $APPLICATION->AddHeadString('<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].'/images/itk-og.jpg"/>', true);
    $APPLICATION->AddHeadString('<meta property="vk:image" content="https://'.$_SERVER['SERVER_NAME'].'/images/itk-vk-image.jpg"/>', true);

    $APPLICATION->AddHeadString('<meta property="twitter:title" content="'.$title.'"/>', true);
    $APPLICATION->AddHeadString('<meta property="twitter:description" content="'.$description.'"/>', true);
    $APPLICATION->AddHeadString('<meta property="twitter:site" content="'.$_SERVER['SERVER_NAME'].'"/>', true);
    $APPLICATION->AddHeadString('<meta property="twitter:card" content="summary_large_image"/>', true);
    $APPLICATION->AddHeadString('<meta property="twitter:url" content="https://'.$_SERVER['SERVER_NAME'].$url.'"/>', true);
    $APPLICATION->AddHeadString('<meta property="twitter:image:src" content="https://'.$_SERVER['SERVER_NAME'].'/images/logo-social.jpg?ver=1"/>', true);
}*/
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

// Добавление rel="next" и rel="prev" в <head>
$pageCount = $arResult['RES']->NavPageCount;
$pageNum = $_REQUEST["PAGEN_".$arResult['RES']->NavNum];

if ($pageNum == 1)
    LocalRedirect($APPLICATION->GetCurPage());
if ($pageCount > 1 && !empty($arResult['ITEMS'])){
    if (empty($pageNum)){
        $APPLICATION->AddHeadString('<link rel="next" href="'.getDomain().$APPLICATION->GetCurPage(false).'?PAGEN_'.$arResult['RES']->NavNum.'=2">',true);
    } else {
        if ($pageNum == 2){
            if ($pageNum == $pageCount){
                $APPLICATION->AddHeadString('<link rel="prev" href="'.getDomain().$APPLICATION->GetCurPage(false).'">',true);
            } else {
                $APPLICATION->AddHeadString('<link rel="prev" href="'.getDomain().$APPLICATION->GetCurPage(false).'">',true);
                $APPLICATION->AddHeadString('<link rel="next" href="'.getDomain().$APPLICATION->GetCurPage(false).'?PAGEN_'.$arResult['RES']->NavNum.'='. ($pageNum+1) .'">',true);
            }
        } elseif ($pageNum > 2 && $pageNum < $pageCount){
            $APPLICATION->AddHeadString('<link rel="prev" href="'.getDomain().$APPLICATION->GetCurPage(false).'?PAGEN_'.$arResult['RES']->NavNum.'='. ($pageNum-1) .'">',true);
            $APPLICATION->AddHeadString('<link rel="next" href="'.getDomain().$APPLICATION->GetCurPage(false).'?PAGEN_'.$arResult['RES']->NavNum.'='. ($pageNum+1) .'">',true);
        } elseif ($pageNum == $pageCount){
            $APPLICATION->AddHeadString('<link rel="prev" href="'.getDomain().$APPLICATION->GetCurPage(false).'?PAGEN_'.$arResult['RES']->NavNum.'='. ($pageNum-1) .'">',true);
        }
    }
}


if ($pageCount > 1){
    $APPLICATION->AddHeadString('<link rel="canonical" href="'.getDomain().$APPLICATION->GetCurPage(false).'">', true);
}