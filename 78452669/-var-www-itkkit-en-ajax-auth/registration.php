<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
error_reporting(0);

if (!CModule::IncludeModule("iblock"))
	die();

if(!CModule::IncludeModule("kodix.mailchimp"))
    return false;

if (isset($_REQUEST['act']) && !empty($_REQUEST['act'])){
	$bidType = $_REQUEST['act'];
}

function isValidEmail($email){ 
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function addToSubscribe($userID = 0)
{
    if(intval($userID) <= 0)
        return false;

    global $USER;
    $oChimp = new KDXMailChimp();


    $arUser = $USER->GetByID($userID)->Fetch();

    if($arUser)
    {
        $oChimp->userSubscribe(
            $arUser['EMAIL'], array(
            'FNAME' => $arUser['NAME'],
            'LNAME' => $arUser['LAST_NAME']
        ));
    }

}

$arReq = array(
	'EMAIL',
	'NAME',
	'LAST_NAME',
	'PERSONAL_PHONE',
	'PASSWORD',
	'CONFIRM_PASSWORD'
);
$arrayFields = array(
	'AGREEMENT' => $_POST['AGREEMENT'],
	'SUBSCRIBE' => $_POST['SUBSCRIBE'],
	'EMAIL' => $_POST['EMAIL'],
	'NAME' => $_POST['NAME'],
	'LAST_NAME' => $_POST['LAST_NAME'],
	'PERSONAL_PHONE' => $_POST['PERSONAL_PHONE'],
	'PASSWORD' => $_POST['PASSWORD'],
	'CONFIRM_PASSWORD' => $_POST['CONFIRM_PASSWORD'],
	'REG_LANG' => $_POST['REG_LANG'],
);

foreach ($arrayFields as $fieldName => $oneField){
	if (in_array($fieldName, $arReq) && ($oneField == '' || !isset($oneField))){
		if ($arrayFields['REG_LANG'] == 'en'){
			$errorContainer[$fieldName] = "field is empty";
		} else {
			$errorContainer[$fieldName] = "поле не заполнено";
		}
	}
}

if ($arrayFields['PASSWORD'] !== $arrayFields['CONFIRM_PASSWORD']){
	if ($arrayFields['REG_LANG'] == 'en'){
		$errorContainer['PASSWORD'] = "password not match";
		$errorContainer['CONFIRM_PASSWORD'] = "password not match";
	} else {
		$errorContainer['PASSWORD'] = "пароль не совпадает";
		$errorContainer['CONFIRM_PASSWORD'] = "пароль не совпадает";
	}
}

/*if ($arrayFields['PERSONAL_PHONE'] !== '' && !is_numeric($arrayFields['PERSONAL_PHONE'])){
	if ($arrayFields['REG_LANG'] == 'en'){
		$errorContainer['PERSONAL_PHONE'] = "only numbers";
	} else {
		$errorContainer['PERSONAL_PHONE'] = "только цифры";
	}
}*/

$chr_en = "a-zA-Z0-9";
if ($arrayFields['NAME'] !== '' && !preg_match("/^[$chr_en]+$/", $arrayFields['NAME'])){
	$errorContainer['NAME'] = "eng";
}
if ($arrayFields['LAST_NAME'] !== '' && !preg_match("/^[$chr_en]+$/", $arrayFields['LAST_NAME'])){
	$errorContainer['LAST_NAME'] = "eng";
}

if ($arrayFields['EMAIL'] !== '' && !isValidEmail($arrayFields['EMAIL'])){
	if ($arrayFields['REG_LANG'] == 'en'){
		$errorContainer['EMAIL'] = "incorrect email";
	} else {
		$errorContainer['EMAIL'] = "указан некорректный адрес";
	}
}

if (empty($errorContainer)){
	global $USER;
	$arUser = $USER->Register($arrayFields['EMAIL'], $arrayFields['NAME'], $arrayFields['LAST_NAME'], $arrayFields['PASSWORD'], $arrayFields['CONFIRM_PASSWORD'], $arrayFields['EMAIL']);
	if ($USER->GetID()){
		$arUpdateFields = array(
            'PERSONAL_PHONE' => $arrayFields['PERSONAL_PHONE'],
            'UF_REGION_ID' => $_SESSION['USER_REGION_ID'],
        );
        if ($arrayFields['SUBSCRIBE'] !== 'Y'){
        	$arUpdateFields['UF_TRIGGER_MAIL_SUB'] = 0;
        	$arUpdateFields['UF_MT_UNSUB'] = 0;
        }
        $USER->Update($arUser['ID'], $arUpdateFields);
        $USER->Authorize($arUser['ID']);
        if ($arrayFields['SUBSCRIBE'] == 'Y'){
            addToSubscribe(intval($arUser['ID']));
        }

        $event = new CEvent();

        $login = $arrayFields['EMAIL'];
        $couponDescription = 'За регистрацию '.$login;

        $siteId = $arrayFields['REG_LANG'] == 'en' ? 'en' : 's1';
        /*$event->SendImmediate('KDX_NEW_USER', $siteId,
            array(
                'LOGIN' => $login,
                'PASSWORD' => $arrayFields['PASSWORD'],
                'COUPON'=> addOnetimeCoupon( 'DISCOUNT_5_PROCENT', 'O', $couponDescription)
            )
        );*/

    	$_SESSION['USER']['REGISTERED_NOW'] = 'Y';

		echo json_encode(array('result' => 'success', 'msg' => 'You have successfully registered on the site!'));
	} else {
		echo json_encode(array('result' => 'error', 'text_msg' => $arUser));
	}
} else {
	echo json_encode(array('result' => 'error', 'text_error' => $errorContainer));
}
?>