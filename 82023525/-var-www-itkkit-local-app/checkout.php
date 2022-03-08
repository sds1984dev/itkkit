<?

use Bitrix\Main;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty('WEBPACK_JS', 'checkout');
$APPLICATION->SetTitle($APPLICATION->GetProperty('SECTION_NAME'));

if (!empty($_GET['unset']) && $_GET['unset'] == 'DELIVERY') {
    $_POST['PROFILE'] = KDXAddress::getLastAddressId();
}

if (!empty($_GET['unset']) && $_GET['unset'] == 'PAYSYSTEM') {
    $_POST['PAYMENT'] = '';
}

if ($USER->IsAuthorized()) {
    if (!\Bitrix\Main\Loader::includeModule("sale")) {
        return;
    }

    $Qprofile = CSaleOrderUserProps::GetList(array(), array("USER_ID" => CUser::GetId(), 'NAME' => 'self'), false, array('nTopCount' => 1));
    $Rprofile = $Qprofile->Fetch();

    //if(!KDXAddress::getList(CUser::GetId()))
    if (!is_array($Rprofile)) {
        $arProfileFields = array(
            "NAME" => "self",
            "USER_ID" => CUser::GetId(),
            "PERSON_TYPE_ID" => KDXSettings::getSetting("PHISICAL_PAYER_ID")
        );

        $arUser = CUser::GetByID($USER->GetID())->Fetch();

        $profile_id = CSaleOrderUserProps::Add($arProfileFields);

        $arProp[] = array("VALUE" => 'LV-1050', "NAME" => "D_Индекс", "ORDER_PROPS_ID" => '11', "USER_PROPS_ID" => $profile_id);
        $arProp[] = array("VALUE" => '0000028003', "NAME" => "D_Страна", "ORDER_PROPS_ID" => '1', "USER_PROPS_ID" => $profile_id);
        $arProp[] = array("VALUE" => '0000028003', "NAME" => "P_Страна", "ORDER_PROPS_ID" => '12', "USER_PROPS_ID" => $profile_id);
        $arProp[] = array("VALUE" => 'Riga', "NAME" => "D_Город", "ORDER_PROPS_ID" => '2', "USER_PROPS_ID" => $profile_id);
        $arProp[] = array("VALUE" => 'Z.A. Meierovica Blvd.', "NAME" => "D_Улица", "ORDER_PROPS_ID" => '3', "USER_PROPS_ID" => $profile_id);
        $arProp[] = array("VALUE" => '18', "NAME" => "D_Дом", "ORDER_PROPS_ID" => '4', "USER_PROPS_ID" => $profile_id);
        $arProp[] = array("VALUE" => '1', "NAME" => "D_Квартира/офис", "ORDER_PROPS_ID" => '7', "USER_PROPS_ID" => $profile_id);

        $arProp[] = array("VALUE" => $USER->GetFirstName(), "NAME" => "D_Имя", "ORDER_PROPS_ID" => '9', "USER_PROPS_ID" => $profile_id);
        $arProp[] = array("VALUE" => $USER->GetLastName(), "NAME" => "D_Фамилия", "ORDER_PROPS_ID" => '22', "USER_PROPS_ID" => $profile_id);
        $arProp[] = array("VALUE" => $USER->GetEmail(), "NAME" => "D_Email", "ORDER_PROPS_ID" => '24', "USER_PROPS_ID" => $profile_id);
        $arProp[] = array("VALUE" => $arUser['PERSONAL_PHONE'] ?: '+37167671505', "NAME" => "D_Телефон", "ORDER_PROPS_ID" => '8', "USER_PROPS_ID" => $profile_id);

        foreach ($arProp as $prop) {
            CSaleOrderUserPropsValue::Add($prop);
        }
    }
}

//    var ShopAddress = {
//        'DELIVERY_ZIP' : 'LV-1050',
//        'DELIVERY_COUNTRY' : '0000028003',
//        'DELIVERY_CITY' : 'Riga',
//        'DELIVERY_STREET' : 'Z.A. Meierovica Blvd.',
//        'DELIVERY_HOUSE' : '18',
//        'DELIVERY_FLAT' : '1'
//    }

$APPLICATION->IncludeComponent(
    "kodix:kit.ordering",
    "redesign_new",
    array(
        "COMPONENT_TEMPLATE" => "redesign_new",
        "CLOSE_CHECKOUT" => "N"
    ),
    false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
