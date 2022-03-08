<?
$iblockId = LANGUAGE_ID == 'en' ? 20 : 19;
foreach ($arResult['ITEMS'] as $key => $item){
	$reviews = 0;
	$resReview = CIBlockElement::GetList(array('ACTIVE_FROM'=>'DESC'), array('IBLOCK_ID'=>$iblockId, 'ACTIVE'=>'Y', '=PROPERTY_PRODUCT'=>$item['ID']), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_RATING'));
	while ($arReview = $resReview->Fetch()){
		$reviews += 1;
	}
	$arResult['ITEMS'][$key]['REVIEW'] = $reviews;
}

if (empty($arResult['ITEMS'])) {
	if (is_numeric($arParams['BRANDS_FILTER'])) {
		$dbBrand = CIBlockElement::GetByID($arParams['BRANDS_FILTER'])->Fetch();
		switch (SITE_ID) {
			case 's1':
				$arResult['BRANDS_DESCRIPTION'] = $dbBrand['DETAIL_TEXT'];
				break;
			case 'en':
				$dbBrandProperties = CIBlockElement::GetProperty(3, $arParams['BRANDS_FILTER'], array(), array('CODE' => 'EN_DETAIL_TEXT'))->Fetch();
				$arResult['BRANDS_DESCRIPTION'] = $dbBrandProperties['VALUE'];
				break;
			default:
				break;
		}
	}
}

?>