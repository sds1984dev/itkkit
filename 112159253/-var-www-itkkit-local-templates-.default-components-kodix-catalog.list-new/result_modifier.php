<?php
$iblockId = LANGUAGE_ID == 'en' ? 20 : 19;
/*foreach ($arResult['ITEMS'] as $key => $item){
	$reviews = 0;
	$resReview = CIBlockElement::GetList(array('ACTIVE_FROM'=>'DESC'), array('IBLOCK_ID'=>$iblockId, 'ACTIVE'=>'Y', '=PROPERTY_PRODUCT'=>$item['ID']), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_RATING'));
	while ($arReview = $resReview->Fetch()){
		$reviews += 1;
	}
	$arResult['ITEMS'][$key]['REVIEW'] = $reviews;
}*/
?>

