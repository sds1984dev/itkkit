<?php
/**
 * Created by:  KODIX 01.04.2015 9:46
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */

$arResult['ADDRESS_GROUPS'] = KDXSettings::getSetting('ADDRESS_PROPS_GROUP_ID');

$res = CSaleOrderPropsValue::GetOrderProps($arResult['ORDER']->id);

while($arPropValue = $res->Fetch())
{
    $arResult['ORDER_PROPS'][ $arPropValue['PROPS_GROUP_ID'] ][ $arPropValue['CODE'] ] = $arPropValue['VALUE'];
}

$cart = new KDXCart(false, $arResult['ORDER']->id);
$arResult['AVAILABLE'] = $cart->getAvailable();

if (count($arResult['AVAILABLE']) > 0) {

    if (!empty($arResult['ORDER']->properties["DELIVERY_COUNTRY"]["VALUE"])) {
        $arrCountries = getHlCountries();
        foreach ($arrCountries as $arCountry) {
            if ($arCountry['UF_COUNTRY_ID'] == $arResult['ORDER']->properties["DELIVERY_COUNTRY"]['VALUE']) {
                $useVAT = $arCountry['UF_USE_VAT'];
                if ($useVAT == 'Y') {
                    $arResult['ORDER']->add_vat = true;
                }
                break;
            }
        }
    }

    $arVAT12 = array(554);
    $group = array();
    $VAT = 0;
    $VAT_AMOUNT = 0;
    $arResult['VAT'] = array(
        '12' => 0,
        '21' => 0
    );
    foreach($arResult['AVAILABLE'] as $key => $arItem)
    {

        $SKU = CCatalogSku::GetProductInfo($arItem['PRODUCT_ID']);
        if ($SKU) {
            $dbElementGroups = CIBlockElement::GetElementGroups($SKU['ID'], true);
            while ($arElementGroups = $dbElementGroups->Fetch()) {
                $group = $arElementGroups["ID"];
            }
        }

        if (in_array($group, $arVAT12)) {
            $VAT = 0.12;
            $arResult['AVAILABLE'][$key]['VAT'][12] += $arItem['PRICE'] * $VAT / ($VAT + 1);
        } else {
            $VAT = 0.21;
            $arResult['AVAILABLE'][$key]['VAT'][21] += $arItem['PRICE'] * $VAT / ($VAT + 1);
        }

        $arResult['AVAILABLE'][$key]['VAT_RATE'] = $VAT;
        $arResult['AVAILABLE'][$key]['SECTION_ID'] = $group;
        $VAT_AMOUNT += $arItem['PRICE'] * $VAT / ($VAT + 1) * $arItem['QUANTITY'];
    }

    $arResult['arVAT12'] = $arVAT12;

    if ($arResult['ORDER']->add_vat) {
        $arResult['ORDER']->vat_price = $VAT_AMOUNT;
    } else {
        if (isset($arResult['VAT'])) unset($arResult['VAT']);
        
    }
}

