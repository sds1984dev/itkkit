<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?


if (($_SERVER['SERVER_ADDR']!=='65.21.9.242') and ($_SERVER['REMOTE_ADDR']!=='185.253.219.28')) {
    if (($cur_user!==$arUser['ID']) and (CSite::InGroup(array(1,7))==false)) {
        LocalRedirect ("/404.php");
    }
}
//$cur_user=$USER->GetID();
//if (($cur_user!==$arUser['ID']) and (CSite::InGroup(array(1,7))==false)) {
//    LocalRedirect ("/404.php");
//}
function price2word_en($price, $currency = array('euro', 'cent'))
{
    $result = '';
    $translate = array(
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70=> 'seventy',
        80 => 'eighty',
        90 => 'ninety',
    );
    $parts = explode('.', $price);
    $_int = array();

    $dig_cnt = ceil(strlen($parts[0]) / 3);

    for ($i=1;$i<=$dig_cnt;$i++)
    {
        $length = ($i == 1 ? 3 : -(($i-1)*3));
        $_int[$i - 1] = substr($parts[0], -($i*3), $length);
        while(strlen($_int[$i - 1]) < 3)
            $_int[$i - 1] = '0' . $_int[$i - 1];
    }

    foreach($_int as &$digit)
    {
        $str = '';
        if(intval($digit{0}) > 0)
        {
            $str .= $translate[$digit{0}] . ' hundred ';
        }

        if(intval($digit{1}) >= 2)
        {
            $str .= $translate[intval($digit{1} . '0')];
            if(intval($digit{2}) > 0)
                $str .= ' ' . $translate[$digit{2}];
        }
        else
        {
            $str .= $translate[intval(substr($digit, 1))];
        }

        $digit = $str;
    }

    if(isset($_int[1]) && !empty($_int[1]))
        $result .= $_int[1] . ' thousand ';
    if(isset($_int[0]) && !empty($_int[0]) && strlen($_int[0]) > 0)
        $result .= $_int[0];

    if(strlen($result) > 0)
        $result .= ' ' . $currency[0];
    if($parts[1])
        $result .= ' ' . $parts[1] . ' ' . $currency[1];

    $result = substr_replace($result, strtoupper($result{0}), 0, 1);
    return $result;
}

$dbRes = \CSaleOrderPropsValue::GetList([], ['ORDER_ID' => $arOrder['ID']]);
$arProps = [];
while($arRes = $dbRes->GetNext())
$arProps[$arRes['CODE']] = $arRes['VALUE'];

$arResult['REG_NO'] = KDXSettings::getSetting('REG_NO');
$arResult['COMPANY_ADDRESS'] = KDXSettings::getSetting('COMPANY_ADDRESS');
$arResult['VAT_ID'] = KDXSettings::getSetting('VAT_ID');
$arResult['SUPPLIER'] = KDXSettings::getSetting('SUPPLIER');

$arResult['PAYSYSTEM'] = (  KDXSettings::getSetting('PAYSYSTEM_' . $arOrder['PAY_SYSTEM_ID'] . '_NAME_PRINT')
                            ? : \CSalePaySystem::GetByID($arOrder['PAY_SYSTEM_ID'])['NAME']);

$arResult['DELIVERY'] = \CSaleDeliveryHandler::GetBySID('kdx_upsex')->Fetch()['NAME'];
/*printr($arOrder);
printr($arProps);*/

$arResult['COUNTRY'] = \CSaleLocation::getCountryByID($arProps['DELIVERY_COUNTRY']);
$arResult['PAY_COUNTRY'] = (    $arProps['DELIVERY_COUNTRY'] == $arProps['PAY_COUNTRY']
                                ? $arResult['COUNTRY']
                                : \CSaleLocation::getCountryByID($arProps['PAY_COUNTRY']));

$products = [];
$dbBasket = \CSaleBasket::GetList([], ['ORDER_ID' => $arOrder['ID'], 'ID' => $arBasketIDs]);

while($arBasket = $dbBasket->Fetch())
    $products[] = $arBasket;

$arResult['FULL_NAME'] =    ($arProps['DELIVERY_CONTACT_LAST_NAME'] ? : $arProps['PAY_CONTACT_LAST_NAME'])
                . ' ' . ($arProps['DELIVERY_CONTACT_NAME'] ? : $arProps['PAY_CONTACT_NAME']);

$arResult['EMAIL'] = ($arProps['PAY_CONTACT_EMAIL'] ? : $arProps['DELIVERY_CONTACT_EMAIL']);

$arResult['DELIVERY_ADDRESS'] =  $arResult['COUNTRY']['NAME']
                        . ', ' . $arProps['DELIVERY_CITY']
                        . ', ' . $arProps['DELIVERY_STREET']
                        . ', ' . $arProps['DELIVERY_HOUSE']
                        . ($arProps['DELIVERY_CORPUS'] ? 'c' . $arProps['DELIVERY_CORPUS'] : '')
                        . ($arProps['DELIVERY_BUILDING'] ? 'b' . $arProps['DELIVERY_BUILDING'] : '')
                        . ' ' . $arProps['DELIVERY_FLAT']
                        . ', ' . $arProps['DELIVERY_ZIP'];

$arResult['PAY_ADDRESS'] =  $arResult['PAY_COUNTRY']['NAME']
                            . ', ' . $arProps['PAY_CITY']
                            . ', ' . $arProps['PAY_STREET']
                            . ', ' . $arProps['PAY_HOUSE']
                            . ($arProps['PAY_CORPUS'] ? 'c' . $arProps['PAY_CORPUS'] : '')
                            . ($arProps['PAY_BUILDING'] ? 'b' . $arProps['PAY_BUILDING'] : '')
                            . ' ' . $arProps['PAY_FLAT']
                            . ', ' . $arProps['PAY_ZIP'];
CModule::IncludeModule("catalog");
//определение налога

$ENTITY_ID = 4;
use Bitrix\Highloadblock as HL;
$hlblock = HL\HighloadBlockTable::getById($ENTITY_ID)->fetch();
$vat=1;
if (isset($hlblock['ID']))
{
    $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $res = $entity_data_class::getList( array('filter'=>array('UF_NAME'=>$arResult['COUNTRY']['NAME'])) );
    while ($item = $res->fetch())
    {
        if ((isset($item['UF_USE_VAT'])) && ($item['UF_USE_VAT']=='Y')) {
            foreach($products as $key=>$product)
            {
                $products[$key]['USE_VAT'] = 'Y';
            }
        }
        else
        {
            foreach($products as $key=>$product)
            {
                $products[$key]['USE_VAT'] = 'N';
            }
            $vat=1;
        }
    }
}

$arVAT12 = array(554);
foreach($products as $key=>$product) {
    $SKU = CCatalogSku::GetProductInfo($product['PRODUCT_ID']);
    if ($SKU) {
        $dbElementGroups = CIBlockElement::GetElementGroups($SKU['ID'], true);
        while ($arElementGroups = $dbElementGroups->Fetch()) {
            $groups[] = $arElementGroups["ID"];
        }
    }
    foreach ($groups as $value) {
        if (in_array($value, $arVAT12)) {
            $products[$key]['VAT_RATE'] = 0.12;
        } else {
            $products[$key]['VAT_RATE'] = 0.21;
        }
    }
}

//определение дисконтов по цене и определение купона
$print_discount=false;
$DISCOUNT_COUPON='None';
//foreach ($products as $key=>$item) {
//    $rsPrices = CPrice::GetList(array(),array('PRODUCT_ID' => $item['PRODUCT_ID'],'CATALOG_GROUP_ID' => 2)); //Получаем массив свойств товара
//    if($arPrice = $rsPrices->Fetch()) {
//       $base_price=$arPrice["PRICE"];
//       $products[$key]['BASE_PRICE']=$base_price;
//       if ($base_price!==$item['PRICE']) {$print_discount=true;}
//    }
//
//}
 
                
            
//определение дисконтов по цене и определение купона
?>


<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title langs="ru">Накладная itk (с дисконтом)</title>
        <link rel="stylesheet" href="reports/print.css">
    </head>

    <body bgcolor="white" lang="RU" style="background: #fff;">
        
        <div class="container">
            <div class='logo_row'>
                <div class='logo'>
                    <img src="reports/ITK_Logo_RGB.svg" alt="">
                </div>
            </div>
            
            <div class='header_row'>
                <span class='header_row_name'>Order number/Pasūtijuma nr:</span>
                <span class='header_row_value'><?=$arOrder['ID']?> (<?=$arOrder['DATE_INSERT_FORMAT']?>)</span>
                </br>
                
                <span class='header_row_name'>Payment/Apmaksas veids:</span>
                <span class='header_row_value'><?=$arResult['PAYSYSTEM']?></span>
                </br>
                
                <span class='header_row_name'>Buyer/Pircejs:</span>
                <span class='header_row_value'><?=$arResult['FULL_NAME']?></span>
                </br>
                
                <span class='header_row_name'>Adress/Adrese:</span>
                <span class='header_row_value'><?=$arResult['DELIVERY_ADDRESS']?></span>
                </br>   
            </div>
            <?
//            

            ?>
            
            <div class='table_row'>
                <table class='report_table'>
                    <tr>
                        <th>&nbsp;#&nbsp;</th>
                        <th>Name<br/>Prece</th>
                        <th>Size<br/>Mērv.</th>
                        <th>Quantity<br/>Daudzums</th>
                        <th>Unit price<br/>Cena</th>
                        <th>VAT<br/>PVN</th>
                        <th>Sum<br/>Summa </th>
<!--                         --><?//if ($print_discount) {?><!--<th>Discount<br/>Atlaide</th>--><?//}?>
                        <th>Discount<br/>Atlaide</th>
                    </tr>
                    <?
                    $quantity = 0.00;
                    $sum = 0.00;
                    $sum_without_vat=0.00;
                    $sum_vat=0.00;
                    $sum_total=0.00;

                    ?>
                    <?
                    $arDiscounts = Array();
                    $dbOrderBasket = CSaleBasket::GetList(Array(), Array("ORDER_ID" => $arOrder["ID"]));

                    while ($arBasket = $dbOrderBasket->Fetch())
                    {
                        if ($arBasket["DISCOUNT_PRICE"] > 0)
                            $arDiscounts[$arBasket["ID"]] = $arBasket["DISCOUNT_PRICE"];// * $arBasket['VAT_RATE'] / (1 + $arBasket['VAT_RATE']);
                    }

                    if (count($arDiscounts) > 0) $print_discount = true;


                    foreach ($products as $item)
                    {
//                        echo "<pre>";
//                        print_r($item);
//                        echo "</pre>";
                        if ($item['DISCOUNT_COUPON'] !== '')
                            $DISCOUNT_COUPON = $item['DISCOUNT_COUPON'];

                        $itemBasePrice = 0;
                        $diffPriceDiscount = 0;
                        $arSort = Array();
                        $arFilter = Array('PRODUCT_ID' => $item['PRODUCT_ID'], 'CATALOG_GROUP_ID' => 2);
                        $dbPrices = CPrice::GetList($arSort, $arFilter);

                        while ($arPrice = $dbPrices->Fetch())
                        {
                            if ($arPrice['BASE'] == 'Y') $itemBasePrice = $arPrice['PRICE'];
                            if ($item['PRICE'] < $arPrice['PRICE']) $diffPriceDiscount = ($arPrice['PRICE'] - $item['PRICE'] - $item['DISCOUNT_PRICE']) * $item['QUANTITY'];
                        }

                        $discount = in_array($item["ID"], array_keys($arDiscounts));
                        $p_val['quantity']=number_format($item['QUANTITY']);
//                        $p_val['unit_price-vat']= round($item['PRICE']/($item['VAT_RATE']+1), 2);
                        $p_val['unit_price-vat'] = $item['PRICE'];
                        $p_val['vat'] = ($item['USE_VAT'] == 'Y') ? number_format($item['PRICE']-$item['PRICE']/($item['VAT_RATE']+1), 2, '.', '') : 0;
//                        $p_val['sum']=$p_val['quantity'] * ($p_val['unit_price-vat'] + $p_val['vat']);
                        $p_val['sum'] = $p_val['quantity'] * $p_val['unit_price-vat'];
                        $sum_without_vat+=$p_val['unit_price-vat']*$p_val['quantity'];
                        $sum_vat+=$p_val['vat']*$p_val['quantity'];
                        $sum_total+=$p_val['sum'];

                        //if ($discount)
                        //{
//                        if ($item['USE_VAT'] == 'N') {
//                            $discount_value = ($itemBasePrice - $item['PRICE']) / (1 + $item['VAT_RATE']) * $item["QUANTITY"];
//                        } else {
//                            $discount_value = ($itemBasePrice - $item['PRICE']) * $item["QUANTITY"];
//                        }
                        
                        $arSelect = Array("ID","PROPERTY_14");
                        $arFilter = Array("IBLOCK_ID"=>IntVal(2), "ID"=>$item['PRODUCT_ID']);
                        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
                        while($ob = $res->GetNextElement())
                        {
                            $arFields = $ob->GetFields();
                        }
                        if (isset($arFields['PROPERTY_14_VALUE'])) {
                            $size=$arFields['PROPERTY_14_VALUE'];
                        } else {
                            $size='NaN';
                        }
                        static $i = 1;
                        $quantity += intval($item['QUANTITY']);
                        $sum += floatval(number_format($item['PRICE'] + $item['PRICE']*($item['VAT_RATE']+1), 2, '.', ''));
                        ?>
                        <tr>
                            <td>&nbsp;<?= $i++; ?>&nbsp;</td>
                            <td class='item_name'><?= $item['NAME'] ?></td>
                            <td><?=$size?></td>
                            <td><?=$p_val['quantity']?></td>
                            <td><?=$p_val['unit_price-vat']?> EUR</td>
                            <td><?=$p_val['vat']?> EUR</td>
                            <td><?=$p_val['sum']?> EUR</td>
<!--                            --><?//if ($print_discount):?>
                                <td><?=number_format($item['DISCOUNT_PRICE'] + $diffPriceDiscount, 2)?> EUR</td>
<!--                            --><?//endif;?>
                        </tr>
                        <?
                        ?>
                    <? } ?>    
                </table>
            </div>  
                <div class='discount_row'>
                    <?if ($DISCOUNT_COUPON !== 'None') {?>
                        <span class='summary_name'>COUPON: <?=($item['DISCOUNT_COUPON'] !== '') ? $DISCOUNT_COUPON : 0?></span>
                    <?}?>
                </div>     
            
            <div class='summary_row'>
                <span class='summary_name'>Total without VAT/Kopā bez PVN:</span>
<!--                <span class='summary_value'><?=$sum_without_vat?> EUR</span>-->
                <?//проблема 1 цента?>
                <span class='summary_value'><?=$sum_total?> EUR</span>
                </br>
                
                <span class='summary_name'>VAT/PVN:</span>
                <span class='summary_value'><?=$sum_vat?> EUR</span>
                </br>
                
                <span class='summary_name'>Shipping/Piegāde:</span>
                <span class='summary_value'><?=number_format($arOrder['PRICE_DELIVERY'], 2, '.', '')?> EUR</span>
                </br>
                
                <span class='summary_name summary_total'>Total/Kopā:</span>
                <span class='summary_value summary_total'><?=$sum_total + number_format($arOrder['PRICE_DELIVERY'], 2, '.', '')?> EUR</span>
                </br>
            </div>  
            
            <div class='sign_row'>
                <div class='sign_row_issued'>
                    Issued/Izrakstija:	_______________________
                </div>
                <div class='sign_row_recieved'>
                    Received/Saņemts:	_______________________
                </div>
                
            </div>
			
        	<div class="footer_block" id="Footer">
				<div class="footer_hr"></div>
				Supplier: <?=$arResult['SUPPLIER']?> / Reg. Number <?=$arResult['REG_NO']?> / VAT: <?=$arResult['VAT_ID']?><br>
				Address: <?=$arResult['COMPANY_ADDRESS']?>
			</div>
		</div>
    </body>
</html>