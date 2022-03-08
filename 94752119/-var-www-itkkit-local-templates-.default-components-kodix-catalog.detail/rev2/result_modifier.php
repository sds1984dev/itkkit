<?
$arResult['DELIVERY'] = getListDelivery();

$arResult['REVIEWS'] = array();
$iblockId = LANGUAGE_ID == 'en' ? 20 : 19;
$similarItemIds = [];
$resItems = CIBlockElement::GetList(array('ACTIVE_FROM'=>'DESC'), array('IBLOCK_ID'=>1, 'ACTIVE'=>'Y', '=IBLOCK_SECTION_ID'=>$arResult['IBLOCK_SECTION_ID']), false, false, array('ID', 'NAME', 'PROPERTY_EN_NAME'));
while ($item = $resItems->Fetch()){
	if (LANGUAGE_ID == 'en'){
		similar_text($item['PROPERTY_EN_NAME_VALUE'], $arResult['PROPERTY_EN_NAME_VALUE'], $percent);
	} else {
		similar_text($item['NAME'], $arResult['NAME'], $percent);
	}
	if ((int) $percent > 73){
		$similarItemIds[] = $item['ID'];
	}
}

$resReview = CIBlockElement::GetList(array('ACTIVE_FROM'=>'DESC'), array('IBLOCK_ID'=>$iblockId, '=PROPERTY_PRODUCT'=>$similarItemIds, 'ACTIVE'=>'Y'), false, false, array('*', 'PROPERTY_*'));
$rCount = 0;
while ($arReview = $resReview->GetNextElement()){
	$arFields = $arReview->GetFields();
	$arProps = $arReview->GetProperties();
	$rCount += $arProps['RATING']['VALUE'];
	$arResult['REVIEWS'][] = array_merge($arFields, array('PROPS'=>$arProps));
}
$arResult['REVIEWS_ALL'] = count($arResult['REVIEWS']);
$arResult['REVIEWS_RATING'] = $rCount;

$parSections = CIBlockSection::GetNavChain(false, $arResult['IBLOCK_SECTION_ID']);
if ($arSectionPath = $parSections->GetNext()){
    $arParentId = $arSectionPath['ID'];
}
$arResult['DEFAULT_SIZE'] = [];
$resTable = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>24, 'ACTIVE'=>'Y', '=PROPERTY_BRAND'=>$arResult['CML2_MANUFACTURER'][0]['ID'], '=PROPERTY_TYPE_XML_ID'=>$arParentId), false, false, array('ID', 'IBLOCK_ID', 'PROPERTY_SITE_DEFAULT'));
if ($arTable = $resTable->Fetch()){
    $arResult['DEFAULT_SIZE'] = $arTable['PROPERTY_SITE_DEFAULT_VALUE'];
}
$arSec = array();
$res = CIBlockSection::GetList(
	array(), 
	array(
		"ID" => $arResult['IBLOCK_SECTION_ID'],
		"IBLOCK_ID" => 1
	),
	false,
	array("NAME", "SECTION_PAGE_URL", "UF_EN_NAME")
);
if($ar_res = $res->GetNext()) {
	$arSec['RU_NAME'] = $ar_res['NAME'];
	$arSec['EN_NAME'] = $ar_res['UF_EN_NAME'];
	$arSec['SECTION_PAGE_URL'] = $ar_res['SECTION_PAGE_URL'];
}
$arResult['SEC'] = $arSec;
?>