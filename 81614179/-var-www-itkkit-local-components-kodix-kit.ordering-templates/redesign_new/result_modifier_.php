<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION;

$dir = str_replace('\\', '/', __DIR__);
include($dir."/lang/".LANGUAGE_ID."/template.php");
include($dir."/lang/".LANGUAGE_ID."/close.php");
include($dir."/lang/".LANGUAGE_ID."/success.php");

$APPLICATION->AddHeadString('<script type="text/javascript">BX.message('.CUtil::PhpToJsObject($MESS).');</script>');
?>