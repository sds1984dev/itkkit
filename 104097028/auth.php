<?
/*
 * точно такой же файл есть в en/ajax/auth
 * и в ru/ajax/auth
 *
 * в общем цель: избавиться в итоге от en/ajax/auth и ru/ajax/auth
 * т.е. если на сайте идет запрос к en/ajax/auth или ru/ajax/auth
 * нужно перенаправить запрос на /local/ajax/auth.php
 *
 * в итоге надо удалить файлы en/ajax/auth и ru/ajax/auth
 * */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if(!$USER->isAuthorized())
{
    global $register, $template, $fields, $change_password;

    if($register=="yes")
    {
        if(empty($template)) $template = 'popup_new';
        if(empty($fields)) $fields = 'NAME,LAST_NAME,EMAIL,PASSWORD,CONFIRM_PASSWORD,SUBSCRIBE';

        $APPLICATION->IncludeComponent('kodix:personal.registration', $template,
            array('FIELDS'=>$fields),
            false
        );
    }
    elseif($change_password == 'yes')
    {
        $APPLICATION->IncludeComponent('bitrix:system.auth.forgotpasswd', 'popup',
        array(),
        false
    );
    }
    else
    {
        $APPLICATION->IncludeComponent('bitrix:system.auth.authorize', 'popup_new',
        array(),
        false
    );
    }
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>