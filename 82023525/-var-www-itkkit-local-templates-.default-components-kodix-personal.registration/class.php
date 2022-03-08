<?php
/**
 * Created by:  KODIX 11.09.14 12:54
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("kodix.mailchimp"))
    return false;



if(!function_exists('walker'))
{
    function walker(&$value, $key)
    {
        $value = htmlspecialchars(trim($value));
    }
}

class CPersonalRegistration extends CBitrixComponent
{
    protected $bNoError = true;
    protected $subscribe = false;
    protected $bSendPassword = false;
    protected $arFields = array(
        'NAME' => array(
            'NAME' => 'Имя',
            'REQUIRED' => 'Y',
            'TYPE' => 'text',
            'CLASS' => '',
        ),
        'LAST_NAME' => array(
            'NAME' => 'Фамилия',
            'REQUIRED' => 'Y',
            'TYPE' => 'text',
            'CLASS' => '',
        ),
        'EMAIL' => array(
            'NAME' => 'E-mail',
            'REQUIRED' => 'Y',
            'TYPE' => 'text',
            'CLASS' => '',
        ),
        'PERSONAL_PHONE' => array(
            'NAME' => 'Телефон',
            'REQUIRED' => 'Y',
            'TYPE' => 'text',
            'CLASS' => 'phoneMask',
        ),
        'PASSWORD' => array(
            'NAME' => 'Пароль',
            'REQUIRED' => 'Y',
            'TYPE' => 'password',
            'CLASS' => '',
        ),
        'CONFIRM_PASSWORD' => array(
            'NAME' => 'Повторите пароль',
            'REQUIRED' => 'Y',
            'TYPE' => 'password',
            'CLASS' => '',
        ),
        'SUBSCRIBE' => array(
            'NAME' => 'Пожписаться на рассылку',
            'REQUIRED' => 'N',
            'TYPE' => 'checkbox',
            'CLASS' => '',
        ),
        'AGREEMENT' => array(
            'NAME' => 'Соглашаюсь с политикой конфиденциальности',
            'REQUIRED' => 'Y',
            'TYPE' => 'checkbox',
            'CLASS' => '',
        )
    );



    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    function prepareFields()
    {
        if(!empty($this->arParams['FIELDS']))
        {
            if(!is_array($this->arParams['FIELDS'])){
                $this->arParams['FIELDS'] = explode(',', $this->arParams['FIELDS']);
            }

            $prepFields = array();
            foreach($this->arParams['FIELDS'] as $value)
            {
                $value = trim($value);
                $prepFields[$value] = $this->arFields[$value];
            }

            $this->arFields = $prepFields;
        }
        $this->arParams['FIELDS'] = $this->arFields;
        foreach($this->arParams['FIELDS'] as $name => &$arField)
        {
            $arField['NAME'] = GetMessage('FIELD_' . $name);
        }

        unset($arField);
    }

    function checkFields()
    {
        array_walk_recursive($_REQUEST['USER'],'walker');
        foreach($this->arParams['FIELDS'] as $name => $arField)
        {
            if($arField['REQUIRED'] == 'Y' && empty($_REQUEST['USER'][$name]) && isset($_REQUEST['USER'][$name]))
            {
                $this->bNoError = false;
                $this->arResult[$name]['ERROR'] = GetMessage('CT_ERROR_EMPTY');
                continue;
            }

            $this->arResult[$name]['VALUE'] = $_REQUEST['USER'][$name];

            switch($name)
            {
                case 'NAME':
                case 'LAST_NAME':
                    if(!preg_match('/^[\w\s\-\–\—]+$/',$_REQUEST['USER'][$name]))
                    {
                        $this->arResult[$name]['ERROR'] = GetMessage('CT_ERROR_NOT_ALLOWED_CHARS');
                        $this->bNoError = false;
                    }
                    break;
                case 'PERSONAL_PHONE':
                    if (!preg_match('/^[0-9\+\-\(\)\s]*$/', $_REQUEST['USER'][$name]))
                    {
                        $this->arResult[$name]['ERROR'] = GetMessage('CT_ERROR_PHONE');
                        $this->bNoError = false;
                    }
                    break;
                case 'EMAIL':
                    if(!check_email($_REQUEST['USER'][$name]))
                    {
                        $this->arResult[$name]['ERROR'] = GetMessage('CT_ERROR_EMAIL');
                        $this->bNoError = false;
                    }
                    else
                    {
                        $_REQUEST['USER']['LOGIN'] = $_REQUEST['USER'][$name];
                    }
                    break;
                case 'PASSWORD':
                    if($_REQUEST['USER'][$name] !== $_REQUEST['USER']['CONFIRM_'.$name] && isset($_REQUEST['USER'][$name]))
                    {
                        $this->arResult[$name]['ERROR'] = GetMessage('CT_ERROR_PASSWORD_NOT_MATCH');
                        $this->bNoError = false;
                    }
                    break;
                case 'SUBSCRIBE':
                    $this->subscribe = $_REQUEST['USER'][$name];
                    break;
            }
        }

        if(!isset($_REQUEST['USER']['PASSWORD']) && !isset($_REQUEST['USER']['CONFIRM_PASSWORD']))
        {
            $_REQUEST['USER']['PASSWORD'] = $_REQUEST['USER']['CONFIRM_PASSWORD'] = substr(md5(time()),0,9);
            //$this->bSendPassword = true;
        }
        return $this->bNoError;
    }

    function register()
    {
        global $USER;
        $arFields = array_merge($_REQUEST['USER'], array(
            "GROUP_ID"          => array(2),
        ));

        $arUser = $USER->Register(
            $_REQUEST['USER']['LOGIN'],
            $_REQUEST['USER']['NAME'],
            $_REQUEST['USER']['LAST_NAME'],
            $_REQUEST['USER']['PASSWORD'],
            $_REQUEST['USER']['CONFIRM_PASSWORD'],
            $_REQUEST['USER']['EMAIL']
        );

        if(intval($arUser['ID']) == 0)
        {
            $this->arResult['ERROR'][] = $arUser['MESSAGE'];
        }
        else
        {
            $arUpdateFields = array(
                'PERSONAL_PHONE' => $_REQUEST['USER']['PERSONAL_PHONE'],
                'UF_REGION_ID' => $_SESSION['USER_REGION_ID']
            );
            $USER->Update($arUser['ID'], $arUpdateFields);

            $USER->Authorize($arUser['ID']);
            if($this->subscribe != false)
            {
                $this->addToSubscribe(intval($arUser['ID']));
            }

            /*$event = new CEvent();

            $login = $_REQUEST['USER']['LOGIN'];
            $couponDescription = 'За регистрацию '.$login;

            $event->SendImmediate('KDX_NEW_USER', SITE_ID,
                array(
                    'LOGIN' => $login,
                    'PASSWORD' => $_REQUEST['USER']['PASSWORD'],
                    'COUPON'=> addOnetimeCoupon( 'DISCOUNT_5_PROCENT', 'O', $couponDescription)
                )
            );*/

        }

        $_SESSION['USER']['REGISTERED_NOW'] = 'Y';

        return $arUser['ID'];
    }

    function registerFromEmail($data) {
        //$data = unserialize(base64_decode($userData));
        global $USER;
        $arUser = $USER->Register(
            $data['LOGIN'],
            $data['NAME'],
            $data['LAST_NAME'],
            $data['PASSWORD'],
            $data['CONFIRM_PASSWORD'],
            $data['EMAIL']
        );
        if(intval($arUser['ID']) == 0)
        {
            $this->arResult['ERROR'][] = $arUser['MESSAGE'];
        }
        else
        {

            $USER->Authorize($arUser['ID']);


            if($this->subscribe != false)
            {
                $this->addToSubscribe(intval($arUser['ID']));
            }


        }

        $_SESSION['USER']['REGISTERED_NOW'] = 'Y';

        return $arUser['ID'];
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

    public function executeComponent()
    {
        global $USER;
        if($USER->isAuthorized())
            return;

        $this->prepareFields();




        $page = '';

        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['checkword']))
        {

            $data = unserialize(base64_decode($_GET['checkword']));

            if ($this->registerFromEmail($data) > 0)
                $page = 'success';
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: http://www.itkkit.ru/checkout/");
            exit();

        }
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid() && $this->checkFields())
        {
            $arCheckword = Array(
                'LOGIN' => $_POST['USER']['EMAIL'],
                'NAME' => $_POST['USER']['NAME'],
                'LAST_NAME' => $_POST['USER']['LAST_NAME'],
                'PASSWORD' => $_POST['USER']['PASSWORD'],
                'CONFIRM_PASSWORD' => $_POST['USER']['CONFIRM_PASSWORD'],
                'EMAIL' => $_POST['USER']['EMAIL']
            );

            $fields = Array(
                'LOGIN' => $_POST['USER']['EMAIL'],
                'PASSWORD' => $_POST['USER']['PASSWORD'],
                'CHECKWORD' => base64_encode(serialize($arCheckword))
            );

//            CEvent::Send(
//                'KDX_NEW_USER',
//                's1',
//                $fields
//            );

            custom_mail($_POST['USER']['EMAIL'], 'MANDRIL|SKARYUK_KDX_NEW_USER|LOGIN=#LOGIN#');

            if (isset($_GET['template']) && htmlspecialchars($_GET['template']) != 'checkout_new')
            {
                if($this->register() > 0)
                    $page = 'success';
            }
        }

        $this->IncludeComponentTemplate($page);
    }
}