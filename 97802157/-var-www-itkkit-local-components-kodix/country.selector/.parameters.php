<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$dbLangs = CLanguage::GetList($by='sort',$order='asc');

$arLangs = array();
while($arLang = $dbLangs->Fetch()) {
    $arLangs[$arLang['LID']] = $arLang['NAME'];
}


$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"LANGS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("KDX_LANGS"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arLangs,
            "MULTIPLE"=>"Y",
		),
        "CACHE_TIME"  =>  Array("DEFAULT"=>3600),
	),
);

?>