<?php
/**
 * Date: 23.03.2015
 * Time: 19:23
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!intval($_POST["product_id"])){
    die;
}
if(intval($_POST["quantity"])<=0){
    $_POST["quantity"]=1;
}

if (!CModule::IncludeModule("iblock"))
    die();

$arSelect = array(
    'ID',
    'CATALOG_QUANTITY',
    'PROPERTY_CML2_LINK'
);

$sku=CIblockElement::GetList(
    array("SORT"=>"ASC"),
    array("ID"=>intval($_POST["product_id"])),
    false,
    false,
    $arSelect
)->GetNext();

$mainProduct = CIblockElement::GetList(array(), array('ID'=>$sku['PROPERTY_CML2_LINK_VALUE']), false, false, array('ID', 'IBLOCK_ID', 'PROPERTY_CANT_ORDER_MORE_ONE'))->GetNext();
if ($mainProduct['PROPERTY_CANT_ORDER_MORE_ONE_VALUE'] == 'Да' && KDXCart::getQuantityByProductOffers(intval($_POST["product_id"])) > 0){
    $result['STATUS'] = 'NO_QUANTITY';
    $result['CANT_ORDER'] = 'Y';
    $result['MESSAGE'] = 'This item could be ordered in quantities of 1 only';
} else {
    $record_id = KDXCart::add($_POST["product_id"], $_POST["quantity"]);

    if($record_id == false)
    {
        $result["STATUS"]="NO_QUANTITY";
    }
    else
    {
        $result["STATUS"]="OK";
    }

    $result["RECORD_ID"]= $record_id;

    if($_POST["refresh_small"]){
        $result["CART_MINI"]=getHtmlBasketMini();
    }

    $result['CANT_ORDER'] = 'N';

    $result['QUANTITY'] = $sku['CATALOG_QUANTITY'] - KDXCart::getQuantityByProduct(intval($_POST["product_id"]));
    $result['QUANTITY'] = $result['QUANTITY'] <= 0 ? 0 :$result['QUANTITY'];

    if(($sku['CATALOG_QUANTITY'] != 0) && ($result['QUANTITY'] == 0)){
        $result['MESSAGE'] = 'The requested item is already in your <a href="/checkout/">basket</a>.';
    }elseif($result['QUANTITY'] <= KDXSettings::getSetting('SMALL_QUANTITY')){
        $result['MESSAGE'] = 'Left '.$result['QUANTITY'].' pcs.';
    }
}

echo json_encode($result);
die;