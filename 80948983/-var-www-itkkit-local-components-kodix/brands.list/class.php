<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class CBrandsListComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        $russian = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Щ','Ш','Э','Ю','Я');
        $russian2 = array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','щ','ш','ь','ы','ъ','э','ю','я');
        $full_alphabet = array_merge(range('A', 'Z'), range('a', 'z'), $russian, $russian2); 
        $uppercase_alphabet = array_merge(array('#'), range('0', '9'),range('A', 'Z'), $russian);

        $arBrands = KDXSaleDataCollector::getBrands(true,'ID',true);
		
		$global_filter = getGlobalFilterForSite();
        $arFilter = Array(
            "IBLOCK_ID" => KDXSettings::getSetting('CATALOG_IBLOCK_ID'),
            "PROPERTY_CML2_MANUFACTURER" => array_keys($arBrands)
        );
        $arFilter = array_merge($global_filter,$arFilter);
        $arGroup = array("PROPERTY_CML2_MANUFACTURER","ID");
        $res = CIBlockElement::GetList(array("SORT"=>"ASC"), $arFilter, $arGroup);
		
		$arAvailableBrands = array();
        while($ar_fields = $res->Fetch())
        {
            $brandID = $ar_fields['PROPERTY_CML2_MANUFACTURER_VALUE'];
            $arAvailableBrands[$brandID] = $arBrands[$brandID];
        }
		
		foreach ($arBrands as $brend) {
			if (isset($arAvailableBrands[$brend['ID']])) {
				$arAvailableBrands[$brend['ID']]['available']=1;
			} else {
                $dbBrandProp = CIBlockElement::GetProperty(3, $brend['ID'], array(), array('CODE' => 'SHOW_IF_ZERO'))->Fetch();
                if ($dbBrandProp['VALUE_ENUM'] == "Y") {
                    $arAvailableBrands[$brend['ID']]=$brend;
                    $arAvailableBrands[$brend['ID']]['available']=1;
    			}
            }
        }
		unset($arBrands);
        uasort($arAvailableBrands, function($old, $current) {
            return strcasecmp($old['NAME'], $current['NAME']);
        });
        
        foreach($arAvailableBrands as $item) {
            $alpha = substr($item['NAME'], 0, 1);
            if (in_array($alpha,$full_alphabet))
                $brands[strtoupper($alpha)][] = array('available'=>$item['available'], 'DETAIL_PAGE_URL' => $item['DETAIL_PAGE_URL'],'NAME' => $item['NAME']);
            else
                $brands['#'][] = array('available'=>$item['available'], 'DETAIL_PAGE_URL' => $item['DETAIL_PAGE_URL'], 'NAME' => $item['NAME']);
        }

        $this->arResult['UPPERCASE_ALPHABET'] = $uppercase_alphabet;
        $this->arResult['RUSSIAN'] = $russian;
        $this->arResult['BRANDS'] = $brands;

        $this->IncludeComponentTemplate();
    }
}