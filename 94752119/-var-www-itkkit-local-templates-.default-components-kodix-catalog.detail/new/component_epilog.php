<?php
/**
 * Date: 28.03.2015
 * Time: 13:02
 */
//$APPLICATION->AddChainItem($arResult['NAME']);
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$curPage = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

if (LANGUAGE_ID == 'en'){
    if (LANGUAGE_ID == 'en' && $arResult["PROPERTY_EN_NAME_VALUE"] !== ''){
        $pordName = $arResult["PROPERTY_EN_NAME_VALUE"];
    } else {
        $pordName = $arResult["NAME"];
    }
    $APPLICATION->SetPageProperty('title', 'Shop '.$pordName.' at itk online store');
    $APPLICATION->SetPageProperty('description', 'Shop '.$pordName.' at itk online store. Free Shipping on All Orders Over €350 &#10003; Fast Delivery &#10003; 14 Days Return Policy &#10003; 100% Authenticity &#10003;');
} else {
    include('seo_meta.php');
    $arrCountries = getHlCountries();
    $useVAT = $arrCountries[$_SESSION['LAST_COUNTRY']]['UF_USE_VAT'];
    foreach ($arResult['REAL_SIZES'] as $arSKU){
        $price = KDXCurrency::convert($useVAT=="N" ? $arSKU['RETAIL_PRICE'] / 1.21 : $arSKU['RETAIL_PRICE'], 'RUB');
    }
    if (isset($arSeoDetail[$curPage])){
        $APPLICATION->SetPageProperty('title', str_replace('#PRICE#', $price, $arSeoDetail[$curPage]['title']));
        $APPLICATION->SetPageProperty('description', str_replace('#PRICE#', $price, $arSeoDetail[$curPage]['description']));
    } else {   
        $APPLICATION->SetPageProperty('title', $arResult['NAME'].' - '.$price.' руб. - купить в интернет-магазине itk');
        $APPLICATION->SetPageProperty('description', 'Интернет-магазин itk предлагает купить '.$arResult['NAME'].': &#9989; Бесплатная доставка на все заказы свыше €350 &#9989; Быстрая доставка &#9989; 14 дневная политика возврата товаров &#9989; 100% оригинальный товар');
    }
}
$APPLICATION->SetTitle($arResult['NAME']);
/*$APPLICATION->IncludeComponent(
    "kodix:social",
    ".default",
    array(
        "TYPE" => "og",
        "TAGS" => array(
            "OG:TITLE" => $APPLICATION->GetProperty('title'),
            "OG:DESCRIPTION" => $APPLICATION->GetProperty('description'),
            "OG:TYPE" => "website",
            "OG:URL" => "https://".$_SERVER['SERVER_NAME'].$arResult['DETAIL_PAGE_URL'],
            "OG:SITE_NAME" => $_SERVER['SERVER_NAME'],
            "OG:IMAGE" => 'https://'.$_SERVER['SERVER_NAME'].CFile::GetPath($arResult['DETAIL_PICTURE']),
        )
    ),
    false
);*/

$detailImg = CFile::GetFileArray($arResult['DETAIL_PICTURE']);

$APPLICATION->AddHeadString('<meta property="og:title" content="'.$APPLICATION->GetProperty('title').'"/>', true);
$APPLICATION->AddHeadString('<meta property="og:description" content="'.$APPLICATION->GetProperty('description').'"/>', true);
$APPLICATION->AddHeadString('<meta property="og:type" content="website"/>', true);
$APPLICATION->AddHeadString('<meta property="og:url" content="https://'.$_SERVER['SERVER_NAME'].$arResult['DETAIL_PAGE_URL'].'"/>', true);
$APPLICATION->AddHeadString('<meta property="og:site_name" content="'.$_SERVER['SERVER_NAME'].'"/>', true);
$APPLICATION->AddHeadString('<meta property="og:image" content="https://'.$_SERVER['SERVER_NAME'].CFile::GetPath($arResult['GALLERY'][0]).'"/>', true);
$APPLICATION->AddHeadString('<meta property="vk:image" content="https://'.$_SERVER['SERVER_NAME'].CFile::GetPath($arResult['GALLERY'][0]).'"/>', true);
$APPLICATION->AddHeadString('<meta property="og:image:width" content="'.$detailImg['WIDTH'].'"/>', true);
$APPLICATION->AddHeadString('<meta property="og:image:height" content="'.$detailImg['HEIGHT'].'"/>', true);

$APPLICATION->AddHeadString('<meta property="twitter:site" content="'.$_SERVER['SERVER_NAME'].'"/>', true);
$APPLICATION->AddHeadString('<meta property="twitter:card" content="summary_large_image"/>', true);
$APPLICATION->AddHeadString('<meta property="twitter:url" content="https://'.$_SERVER['SERVER_NAME'].$arResult['DETAIL_PAGE_URL'].'"/>', true);
$APPLICATION->AddHeadString('<meta property="twitter:title" content="'.$APPLICATION->GetProperty('title').'"/>', true);
$APPLICATION->AddHeadString('<meta property="twitter:description" content="'.$APPLICATION->GetProperty('description').'"/>', true);
$APPLICATION->AddHeadString('<meta property="twitter:image:src" content="https://'.$_SERVER['SERVER_NAME'].CFile::GetPath($arResult['GALLERY'][0]).'"/>', true);

$vk_img = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], array('width' => 500, 'height' => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 100);

$APPLICATION->AddHeadString('<meta property="vk:image" content="https://'.$_SERVER['SERVER_NAME'].$vk_img['src'].'"/>', true);
