<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION;

$dir = str_replace('\\', '/', __DIR__);
include($dir."/lang/".LANGUAGE_ID."/template.php");
include($dir."/lang/".LANGUAGE_ID."/close.php");
include($dir."/lang/".LANGUAGE_ID."/success.php");

$APPLICATION->AddHeadString('<script type="text/javascript">BX.message('.CUtil::PhpToJsObject($MESS).');</script>');

$script = "
    dataLayer.push = ({
      event: 'eec.checkout',
      ecommerce: {
        checkout: {
          actionField: {
            step: 1
          },
     products: [\n
 ";
$i = 0;
foreach($arResult['AVAILABLE'] as $key=>$item) {
    $brandId = null;
    $newName = str_replace("'", "", $item['NAME']);
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
    $script .= "id:"."'".$item['PROPS']['PARENT_ID']."',\n";
    $script .= "price:"."'".$item['PRICE']."',\n";
    $script .= "name:"."'".$newName."',\n";
    $script .= "brands:"."'".$brands."',\n";
    $script .= "quantity:"."'".intval($item['QUANTITY'])."'\n";
    $script .= "}\n";
    $i++;
}
unset($i);
$script .= "]\n
            }\n
        }\n
        }\n
    );";
   //$APPLICATION->AddHeadString('<script type="text/javascript">'.$script.'</script>');
    $arResult["GA_SCRIPT"] = $script;
?>