<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * Created by PhpStorm.
 * User: Kodix
 * Date: 22.09.2017
 * Time: 18:55
 */
$APPLICATION->SetPageProperty('WEBPACK_JS','brands');
$APPLICATION->AddHeadString('<meta property="og:title" content="Shop the Best Streetwear Brands at itk online store"/>');
$APPLICATION->AddHeadString('<meta property="og:type" content="website"/>');
$APPLICATION->AddHeadString('<meta property="og:url" content="https://www.itkkit.com/catalog/brands/"/>');
$APPLICATION->AddHeadString('<meta property="og:site_name" content="Itkkit.com" />');
$APPLICATION->AddHeadString('<meta property="og:description" content="Shop the Best Streetwear Brands at itk online store. Free Shipping on All Orders Over €350 ✓ Fast Delivery ✓ 14 Days Return Policy ✓ 100% Authenticity ✓"/>');
$APPLICATION->AddHeadString('<meta property="og:image" content="https://www.itkkit.com/images/itk-og.jpg"/>');
$APPLICATION->AddHeadString('<meta property="vk:image" content="https://www.itkkit.com/images/itk-vk-image.jpg"/>');
$APPLICATION->AddHeadString('<meta property="twitter:card" content="summary_large_image"/>');
$APPLICATION->AddHeadString('<meta property="twitter:url" content="https://www.itkkit.com/catalog/brands/"/>');
$APPLICATION->AddHeadString('<meta property="twitter:title" content="Shop the Best Streetwear Brands at itk online store"/>');
$APPLICATION->AddHeadString('<meta property="twitter:description" content="Shop the Best Streetwear Brands at itk online store. Free Shipping on All Orders Over €350 ✓ Fast Delivery ✓ 14 Days Return Policy ✓ 100% Authenticity ✓"/>');
$APPLICATION->AddHeadString('<meta property="twitter:image:src" content="https://www.itkkit.com/images/logo-social.jpg?ver=1"/>');
$APPLICATION->AddHeadString('<meta property="twitter:site" content="Itkkit.com"/>');

if (SITE_ID === 's1') {
    $APPLICATION->AddChainItem("Бренды", '/catalog/brands/');
} else {
	$APPLICATION->AddChainItem("Brands", '/catalog/brands/');
}
?>

<?$APPLICATION->IncludeComponent(
    "kodix:brands.list",
    "",
    Array(
        "IBLOCK_ID" => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
        "FILTER" => array($component->arResult['CHILDREN_COMPONENT_BUFFER']['FILTER_ARRAY'],"ACTIVE"=>"Y"),
        "FILTER_ON" => 'Y'
    )
);?>