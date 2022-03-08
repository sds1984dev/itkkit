<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!CModule::IncludeModule('sale'))
    die();

class CKodixPersonalAddressComponent extends CBitrixComponent
{
    protected $fieldMap = array(
        array('CONTACT_NAME','CONTACT_LAST_NAME'),
        array('CONTACT_EMAIL','PHONE'),
        array('ZIP','COUNTRY'),
        array('CITY','STREET'),
        //array('STREET'),
        //array('HOUSE', 'FLAT'),
        array('ADDRESS_COMMENT'),
    );

    protected $fieldMap_ = array(
        'CONTACT_NAME','CONTACT_LAST_NAME',
        'CONTACT_EMAIL','PHONE',
        'ZIP','COUNTRY',
        'CITY',
        'STREET',
        //'HOUSE', 'FLAT',
        'ADDRESS_COMMENT',
    );

    public $profileID = false;

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function prepareFieldMap()
    {
        $this->arParams['FIELD_MAP'] = $this->arParams['FIELD_MAP']?:$this->fieldMap;
        $this->arParams['FIELD_MAP_'] = $this->arParams['FIELD_MAP_']?:$this->fieldMap_;
    }

    function collectProps(){
        global $USER;

        $this->arResult['ADDRESS_GROUPS'] = KDXSettings::getSetting('ADDRESS_PROPS_GROUP_ID');

        $res=CSaleOrderProps::GetList(array("SORT"=>"ASC"), array(
                "PROPS_GROUP_ID"=>array_keys(KDXSettings::getSetting("ADDRESS_PROPS_GROUP_ID")),
                "ACTIVE" => "Y"
            )
        );

        while($prop=$res->GetNext()){
            switch($prop['CODE'])
            {
                case'PAY_CONTACT_NAME':
                case'DELIVERY_CONTACT_NAME':
                    $prop['DEFAULT_VALUE'] = $USER->GetFirstName();
                    break;
                case'PAY_CONTACT_LAST_NAME':
                case'DELIVERY_CONTACT_LAST_NAME':
                    $prop['DEFAULT_VALUE'] = $USER->GetLastName();
                    break;
                case'PAY_CONTACT_EMAIL':
                case'DELIVERY_CONTACT_EMAIL':
                    $prop['DEFAULT_VALUE'] = $USER->GetEmail();
                    break;
                case'PAY_PHONE':
                case'DELIVERY_PHONE':
                    $arUser = CUser::GetByID( $USER->GetID() )->Fetch();
                    $prop['DEFAULT_VALUE'] = $arUser['PERSONAL_PHONE'];
                    break;

            }
            $this->arResult["ADDRESS_PROPS"][ $this->arResult['ADDRESS_GROUPS'][ $prop["PROPS_GROUP_ID"] ] ][ $prop["CODE"] ]=$prop;
        }
    }

    function postHandler()
    {
        global $USER, $APPLICATION;

        if($_SERVER['REQUEST_METHOD'] != 'POST' || !check_bitrix_sessid() || $_REQUEST['ADDRESS_FORM'] !== 'Y')
            return;

        $arFields = array();

        foreach($_REQUEST['ADDRESS'] as $profileID => &$arProfiles) {

            if(isset($_REQUEST['ACTION'][$profileID]['DELETE']))
            {
                KDXAddress::removeAddress($profileID);
                if(!isAjax())
                    LocalRedirect($APPLICATION->GetCurPageParam('SUCCESS=Y', array('SUCCESS')));
                else
                    $_REQUEST['SUCCESS'] = 'Y';

            }
            elseif(isset($_REQUEST['ACTION'][$profileID]['SAVE']))
            {

                $bNoErrors = true;

                foreach ($arProfiles as $type => &$arProfile) {
                    if($_REQUEST['MATCHES'] == 'Y' && $type == 'PAY')
                    {
                        foreach($arProfile as $code => $value)
                        {
                            $tempcode =  str_replace('PAY_', 'DELIVERY_', $code);

                            $arProfile[ $code ] = $arProfiles['DELIVERY'][$tempcode];

                        }
                    }
                    $arErrors = array();
                    foreach ($this->arResult['ADDRESS_PROPS'][$type] as $arProp) {
                        if ($arProp['REQUIED'] == 'Y' && empty($arProfile[$arProp['CODE']]))
                            $arErrors[] = 'ERROR_EMPTY_' . $arProp['CODE'];
                        //TODO: Валидоторы
                        switch ($arProp['CODE']) {
                            case'PAY_CONTACT_EMAIL':
                            case'DELIVERY_CONTACT_EMAIL':
                                if(!check_email($arProfile[$arProp['CODE']]))
                                {
                                    $arErrors[] = 'ERROR_FORMAT_' . $arProp['CODE'];
                                }
                                break;
                            case 'DELIVERY_CITY':
                                if(!empty($arProfile[$arProp['CODE']])){
                                    if (preg_match('/[А-Яа-я]/', $arProfile[$arProp['CODE']])){
                                        $arErrors[] = 'ERROR_FORMAT_' . $arProp['CODE'];
                                    }
                                }
                                break;
                            case 'DELIVERY_STREET':
                                if(!empty($arProfile[$arProp['CODE']])){
                                    if (preg_match('/[А-Яа-я]/', $arProfile[$arProp['CODE']])){
                                        $arErrors[] = 'ERROR_FORMAT_' . $arProp['CODE'];
                                    }
                                }
                                break;
                            case 'PAY_PHONE':
                            case 'DELIVERY_PHONE':
                                if(!preg_match('/^([0-9\(\)\-\+\s]+)$/',$arProfile[$arProp['CODE']]))
                                {
                                    $arErrors[] = 'ERROR_FORMAT_' . $arProp['CODE'];
                                }
                                break;
                        }

                        $arFields[] = array(
                            "USER_PROPS_ID" => $profileID,
                            "ORDER_PROPS_ID" => $arProp["ID"],
                            "NAME" => $arProp["NAME"],
                            "VALUE" => $arProfile[$arProp['CODE']]
                        );

                    }

                    if (count($arErrors) > 0) $bNoErrors = false;

                    $this->arResult['ERRORS'][$profileID][$type] = $arErrors;

                }

                if($bNoErrors && $_REQUEST['NEW_USER'] == 'Y')
                {
                    global $USER;
                    $pass = randString();
                    $arResult = $USER->Register(
                        $_REQUEST['ADDRESS'][ $profileID ]['PAY']['PAY_CONTACT_EMAIL'],
                        $_REQUEST['ADDRESS'][ $profileID ]['PAY']['PAY_CONTACT_NAME'],
                        $_REQUEST['ADDRESS'][ $profileID ]['PAY']['PAY_CONTACT_LAST_NAME'],
                        $pass,
                        $pass,
                        $_REQUEST['ADDRESS'][ $profileID ]['PAY']['PAY_CONTACT_EMAIL']
                    );
                    $this->arResult['USER_RESULT'] = $arResult;
                    if($arResult['TYPE'] == 'ERROR')
                        $bNoErrors = false;
                    else
                    {
                        /*$event = new CEvent();

                        $login = $_REQUEST['ADDRESS'][$profileID]['PAY']['PAY_CONTACT_EMAIL'];
                        $couponDescription = 'За регистрацию '.$login;

                        $event->SendImmediate('KDX_NEW_USER', SITE_ID,
                            array(
                                'LOGIN' => $login,
                                'PASSWORD' => $pass,
                                'COUPON'=> addOnetimeCoupon('DISCOUNT_5_PROCENT', 'O', $couponDescription)
                            )
                        );*/
                    }
                }

                if ($bNoErrors)
                {
                    if ($profileID == 'NEW')
                    {
                        $arProfileFields = array(
                            "NAME" => "Профиль покупателя (" . $USER->GetLogin() . ')',
                            "USER_ID" => $USER->GetId(),
                            "PERSON_TYPE_ID" => KDXSettings::getSetting("PHISICAL_PAYER_ID")
                        );

                        $prID = CSaleOrderUserProps::Add($arProfileFields);

                        if (!$prID) {
                            throw new \Bitrix\Main\SystemException(GetMessage('KDX_COULD_NOT_CREATE_PROFILE'));
                        }

                        foreach ($arFields as $arField) {
                            $arField['USER_PROPS_ID'] = $prID;
                            CSaleOrderUserPropsValue::Add($arField);
                        }

                        $this->profileID = $prID;
                        // маленький хак для корзины
                        $_POST['PROFILE'] = $prID;
                    }
                    else
                    {
                        $res = CSaleOrderUserPropsValue::GetList(
                            array(),
                            array(
                                "USER_PROPS_ID" => $profileID,
                            )
                        );
                        $oldProps = array();
                        while ($oldProp = $res->GetNext()) {
                            $oldProps[$oldProp["ORDER_PROPS_ID"]] = $oldProp;
                        }

                        foreach ($arFields as $arField) {
                            if ($oldProps[$arField['ORDER_PROPS_ID']]) {

                                CSaleOrderUserPropsValue::Update($oldProps[$arField['ORDER_PROPS_ID']]["ID"], $arField);
                            }
                            else {
                                CSaleOrderUserPropsValue::Add($arField);
                            }
                        }

                        $this->profileID = $profileID;
                    }

                    $address = new KDXAddress($this->profileID);

                    $address->setAsLast();

                    if(!isAjax()){
                        if($this->arResult['USER_RESULT']['TYPE'] != 'ERROR')
                        {
                            LocalRedirect($APPLICATION->GetCurPageParam('', array()));
                        }
                        else
                            LocalRedirect($APPLICATION->GetCurPageParam('SUCCESS=Y', array('SUCCESS')));
                    }
                    else
                    {
                        $_REQUEST['ADDRESS'] = false;
                        $_REQUEST['SUCCESS'] = 'Y';
                    }

                }
            }

        }

    }

    function returnLoadUserAddresses()
    {
        global $USER;

        $arGroups = $this->arResult['ADDRESS_GROUPS'];

        $arRes = CSaleOrderUserProps::DoLoadProfiles($USER->GetID(),KDXSettings::getSetting('PHISICAL_PAYER_ID'));

        $arProfiles = array();
        foreach($arRes as $profileID => $arProfile)
        {


            foreach ($arGroups as $type)
            {
                $arProfiles[$profileID][ $type ][ 'TYPE' ] = $arProfile['NAME'];

                foreach ($this->arResult['ADDRESS_PROPS'][$type] as $arProp)
                {
                    if($arProp['TYPE'] == 'LOCATION')
                        $arProfile['VALUES'][ $arProp['ID'] ] = CSaleLocation::getLocationCODEbyID($arProfile['VALUES'][ $arProp['ID'] ]);

                    $arProfiles[$profileID][ $type ][ $arProp['CODE'] ] = $arProfile['VALUES'][ $arProp['ID'] ];
                }
            }

        }
        return $arProfiles;
    }

    function loadUserAddresses()
    {
        $this->arResult['ADDRESSES'] = $this->returnLoadUserAddresses();
    }

    public function collectCountries()
    {
        $result = array();

        $cache_id = md5('countries-'.LANGUAGE_ID);
        $cache_time = 3600;
        $cache_path = 'kcache/countries';
        $cache = new CPHPCache();

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path))
        {
            $result = $cache->GetVars();
        }
        else
        {
            $res = CSaleLocation::GetList(
                array("SORT"=>"ASC", "COUNTRY_NAME_LANG"=>"ASC", "CITY_NAME_LANG"=>"ASC"),
                array(
                    'CITY_ID' => false,
                    'REGION_ID' => false,
                    'COUNTRY_LID' => LANGUAGE_ID,
                ),
                false,
                false
            );


            while ($arRes = $res->Fetch())
            {
                $arRes['LOC_CODE'] = CSaleLocation::getLocationCODEbyID($arRes['ID']);
                $result[ $arRes['ID'] ] = $arRes;
            }

            if($cache->StartDataCache())
                $cache->EndDataCache($result);
        }

        $this->arResult['COUNTRIES'] = $result;

    }


    public function executeComponent()
    {
        $this->prepareFieldMap();
        $this->collectProps();
        $this->postHandler();
        $this->loadUserAddresses();

        $this->collectCountries();

        $this->IncludeComponentTemplate();

        return KDXAddress::getLastAddressId();
    }
}
