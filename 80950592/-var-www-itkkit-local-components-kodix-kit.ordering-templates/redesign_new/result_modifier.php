<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION;

$dir = str_replace('\\', '/', __DIR__);
include($dir."/lang/".LANGUAGE_ID."/template.php");
include($dir."/lang/".LANGUAGE_ID."/close.php");
include($dir."/lang/".LANGUAGE_ID."/success.php");

$arVAT12 = array(554);

$groups = array();
$VAT = 0;
$VAT_AMOUNT = 0;
$arResult['VAT'] = array(
	'12' => 0,
	'21' => 0
);

$total = 0;

foreach($arResult["AVAILABLE"] as $key => $arItem) {

	$SKU = CCatalogSku::GetProductInfo($arItem['PRODUCT_ID']);
	if ($SKU) {
		$dbElementGroups = CIBlockElement::GetElementGroups($SKU['ID'], true);
	    while ($arElementGroups = $dbElementGroups->Fetch()) {
	    	$groups[] = $arElementGroups["ID"];
	    }
	}
	
	foreach ($groups as $value) {
		if (in_array($value, $arVAT12)) {
			$VAT = 0.12;
			$arResult['VAT'][12] += $arItem['PRICE'] * $arItem['QUANTITY'] * $VAT / ($VAT + 1);
		} else {
			$VAT = 0.21;
			$arResult['VAT'][21] += $arItem['PRICE'] * $arItem['QUANTITY'] * $VAT / ($VAT + 1);
		}
	}
	$arResult['AVAILABLE'][$key]['VAT_RATE'] = $VAT;
	//$arResult['CART'][$key]['VAT_RATE'] = $VAT;
	unset($groups);
	$VAT_AMOUNT += $arItem['PRICE'] * $VAT;

	$total +=  floatval($arItem["PRICE"]+$arItem["DISCOUNT_PRICE"])*intval($arItem["QUANTITY"]);
}
$arResult['TOTAL'] = $total;

if ($arResult['ORDER']->add_vat) {
	$arResult['ORDER']->vat_price = $VAT_AMOUNT;
} else {
	//if (isset($arResult['VAT'])) unset($arResult['VAT']);
	
}

$APPLICATION->AddHeadString('<script type="text/javascript">BX.message('.CUtil::PhpToJsObject($MESS).');</script>');

$script = "
    dataLayer = [{
      ‘event’: 'eec.checkout',
      ‘ecommerce’: {
        ‘checkout’: {
          ‘actionField’: {
            ‘step’: 1
          },
     ‘products’: [\n
 ";
$i = 0;
foreach($arResult['AVAILABLE'] as $key=>$item) {
    $brandId = null;
    $iterator = CIBlockElement::GetProperty(1, $item['PROPS']['PARENT_ID'],array(),array('ID' => 1));
    while ($row = $iterator->Fetch())
    {
      $brandId = $row['VALUE'];
    }

    if (!is_null($brandId)) {
        $k = 0;
        $brands = '';
        $dbBrands = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>3,"ID"=>$brandId));
        while($arBrand = $dbBrands->GetNext())
        {
            if ($k > 0) {
                $brands .= ','.$arBrand['NAME'];
            } else {
                $brands .= $arBrand['NAME'];
            }
            $k++;
        }
        unset($k);
    }
    if ($i > 0) $script .= ",";
    $script .= "{\n";
    $script .= "'id':"."'".$item['PROPS']['PARENT_ID']."',\n";
    $script .= "'price':"."'".$item['PRICE']."',\n";
    $script .= "'name':"."'".$item['NAME']."',\n";
    $script .= "'brands':"."'".$brands."',\n";
    $script .= "'quantity':"."'".intval($item['QUANTITY'])."'\n";
    $script .= "}\n";
    $i++;
}
unset($i);
$script .= "]\n
            }\n
        }\n
    ];";
   $APPLICATION->AddHeadString('<script type="text/javascript">'.$script.'</script>');
?>
