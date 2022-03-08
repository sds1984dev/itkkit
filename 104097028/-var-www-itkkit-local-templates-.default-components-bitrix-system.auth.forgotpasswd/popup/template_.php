<?php
/**
 * Created by:  KODIX 25.03.2015 16:35
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?if(!isAjax()){?>
<div class="" id="forgot_tab">
<?}else{
    $status = $APPLICATION->arAuthResult['TYPE'] === 'OK' ? 'success':'error';?>
    <script>
        KDX.Informers.init();
        var old_time = KDX.Informers.options.timeOut;
        KDX.Informers.options.timeOut = 10000;
        <?if($status == 'success'){?>
            KDX.Informers.addSuccess("<?=str_replace('#USER_EMAIL#', $_POST['USER_LOGIN'], GetMessage('MESSAGE_SUCCESS'))?>");
        <?}else{?>
            KDX.Informers.addError("<?=$APPLICATION->arAuthResult['MESSAGE'];?>");
        <?}?>
        KDX.Informers.options.timeOut = old_time;
    </script>
<?}?>
    <form action="/ajax/auth/auth.php?change_password=yes" class="ajax_load" method="post" data-ajax-response-wrapper="#forgot_tab">
        <?if (strlen($arResult["BACKURL"]) > 0){?>
            <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
        <?}?>
        <input type="hidden" name="AUTH_FORM" value="Y">
        <input type="hidden" name="TYPE" value="SEND_PWD">

        <div class="form-row <?=$status?>">
            <input class="form-input" type="text" name="USER_LOGIN" placeholder="<?=GetMessage('EMAIL_INPUT_PLACEHOLDER')?>" value="<?=$_POST['USER_LOGIN']?:$arResult["LAST_LOGIN"]?>">
        </div>

        <button type="submit" class="btn btn--primary btn--inline-lg btn--block-md"><?=GetMessage('FORGOT_SUBMIT')?></button>
        <div class="text--center">
            <a class="link link--primary link--bold" href="#" data-popup-close data-popup-for="enter"><?=GetMessage('AUTH_SIGNIN')?></a>
        </div>

    </form>
<?if(!isAjax()){?>
</div>
<?}?>
</div>
