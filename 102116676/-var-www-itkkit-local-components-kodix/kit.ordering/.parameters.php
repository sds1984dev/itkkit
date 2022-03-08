<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("sale"))
	return;

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
        "CLOSE_CHECKOUT" => array(
            "NAME" => GetMessage('CP_KO_P_CLOSE_CHECKOUT'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N"
        ),
	),
);
?>
