<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


$cart=new KDXCart();
$cart->clearCart();

$result["STATUS"]="OK";


echo json_encode($result);
die;