<?php

if($arResult['SELECTION_DATA']['SELECTION_ID']) {
    $selection = $arResult['SELECTION_DATA']['DATA'];

    if($fileID = $selection['PROPERTY_BACKGROUND_IMAGE_VALUE']) {
        $background = 'url('.CFile::GetPath($fileID).') '.$selection['PROPERTY_IMAGE_STYLE_VALUE'].' ';
    }

    $APPLICATION->SetPageProperty('SELECTION_COLOR', $background.$selection['PROPERTY_BACKGROUND_COLOR_VALUE']);
    $APPLICATION->SetPageProperty('SELECTION_PRODUCT_STYLE', 'border: '.$selection['PROPERTY_PRODUCT_LINES_STYLE_VALUE']);
    $APPLICATION->SetPageProperty('SELECTION_STYLE', '<style>'.$selection['PROPERTY_SELECTION_STYLE_VALUE'].'</style>');
}

if (!empty($arParams['TAG'])){
    $APPLICATION->SetTitle($arParams['TAG']);
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