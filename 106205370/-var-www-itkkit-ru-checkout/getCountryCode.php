<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

CModule::IncludeModule('sale');
CModule::IncludeModule('highloadblock');

if (isset($_POST['COUNTRY_NAME_RU']) && $_POST['COUNTRY_NAME_RU'] != '') {
    $hlblock = HLBT::getById(4)->fetch();
    $entity = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    $rsData = $entity_data_class::getList(array(
        'select' => array('UF_CONTRY_CODE'),
        'order' => array(),
        'limit' => 1,
        'filter' => array('UF_NAME_RU' => $_POST['COUNTRY_NAME_RU'])
    ));
    while($el = $rsData->fetch()){
        if (is_array($el) && isset($el['UF_CONTRY_CODE'])) {
            echo $el['UF_CONTRY_CODE'];
            die();
        }
    }
}

if (isset($_POST['ID']) && $_POST['ID'] != '') {

    $dbProfile = CSaleOrderUserPropsValue::GetList(Array(), Array("USER_PROPS_ID"=>$_POST['ID']));

    while ($arPropVals = $dbProfile->Fetch()) {

        if ($arPropVals["NAME"] == "D_Страна") {

            $hlblock = HLBT::getById(4)->fetch();
            $entity = HLBT::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $rsData = $entity_data_class::getList(array(
               'select' => array('UF_CONTRY_CODE'),
               'order' => array(),
               'limit' => 1,
               'filter' => array('UF_COUNTRY_ID' => $arPropVals["VALUE"])
            ));

            while($el = $rsData->fetch()){
                if (is_array($el) && isset($el['UF_CONTRY_CODE'])) {
                    echo $el['UF_CONTRY_CODE'];
                    die();
                }
            }

            break;

        }

    }
}
die('Fail');

?>