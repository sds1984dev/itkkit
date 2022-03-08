<?php
/**
 * Created by:  KODIX 25.03.2015 16:35
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<div class="checkout__step checkout__step--current">
    <div class="checkout__step-header">
        <div class="checkout__step-heading">1. <?=GetMessage('Данные о покупателе')?></div>
        <button class="btn link link--secondary checkout__step-edit js_checkout-edit" type="button">
                Изменить
        </button>
    </div>
    <div class="checkout__step-content">
        <div data-tab-name="checkout-auth" data-tab-id="1" data-tab-show="true">
            <div class="checkout__auth-toggle-wrapper">
                <span><?=GetMessage('HAVE_ACCOUNT')?></span>
<!--                <a class="link link--secondary" href="#" data-tab-name="checkout-auth" data-tab-for="2">--><?//=GetMessage('Войти')?><!--</a>-->
                <a href="/checkout/?action=login" class="ajax_load link link--secondary" data-ajax-response-wrapper="#checkout_wrap"><?=GetMessage('Войти')?></a>
            </div>
            <form data-validation-function="validate_form_eng_char" data-ajax-response-wrapper="#block_register" method="post" class="ajax_load"
                  action="/local/app/auth.php?template=checkout_new&fields=<?=implode(',', array_keys($arParams['FIELDS']))?>&register=yes">
                <?=bitrix_sessid_post()?>
                <div class="lang_error_msg popup_lang_err" style="display:none"><?=GetMessage('VALIDATION_ERROR')?></div>
                <div class="grid-row">

                    <?foreach($arParams['FIELDS'] as $name => $arField){
                        $error = isset($arResult[$name]['ERROR']) ?'error':''?>
                        <div class="col-md-6">
                            <div class="form-row form-row--lg-gap <?=$error?>">
                                <input
                                    type="<?=$arField['TYPE']?>"
                                    id="<?=$name?>"
                                    name="USER[<?=$name?>]"
                                    value="<?=$arResult[$name]['VALUE']?>"
                                    placeholder="<?=$arField['NAME']?>"
                                    class="<?=($name=='NAME' || $name=='LAST_NAME'?'validate_eng ':'')?>form-input<?=(!empty($arResult[$name]['ERROR'])) ? ' alarm' : ''?><?=$name == 'PERSONAL_PHONE' ? ' js-order-phone' : ''?>">
                                <?if($error){?>
                                    <div class="form-row__error"><?=$arResult[$name]['ERROR']?></div>
                                <?}?>
                            </div>
                        </div>
                        <?
                    }?>
                    <?if(!empty($arResult['ERROR'])){?>
                        <div class="form-row error">
                            <?foreach ($arResult['ERROR'] as $error) {?>
                                <div class="form-row__error "><?ShowError($error);?></div>
                            <?}?>
                        </div>
                    <?}?>
                    <div class="col-md-12">
                        <label class="checkbox">
                            <input checked="checked" name="AGREEMENT" value="Y" type="checkbox" class="checkbox__input">
                            <div class="checkbox__icon"></div>
                            <div class="checkbox__label">Соглашаюсь с <a href="/help/privacy-policy/">политикой конфиденциальности</a></div>
                        </label>
                    </div>
                    <div class="col-lg-12">
                        <label class="checkbox">
                            <input checked="checked" name="SUBSCRIBE" value="Y" type="checkbox" class="checkbox__input">
                            <div class="checkbox__icon"></div>
                            <div class="checkbox__label">Подписаться на рассылку</div>
                        </label>
                    </div>
                </div>


                <div class="txt_center">
                    <button class="btn btn--primary btn--inline-lg btn--block-md checkout__btn--step lg_btn"><?=GetMessage('Продолжить')?></button>
                </div>
            </form>
        </div>

    </div>
</div>
<script type="text/javascript">
    if ($('.js-order-phone').length){
        var input = document.querySelector('.js-order-phone');
        var maskPhone = window.intlTelInput(input, {
            initialCountry: '<?=getCountryByPhone()?>',
            preferredCountries: ['gb','us','hk','jp','fr','de','ru','tw','kr','lt','lv','ee'],
            utilsScript: '/local/templates/kit_new/js/utils.js',
            autoHideDialCode: false,
            nationalMode: false,
        });

        var country = maskPhone.getSelectedCountryData(),
            code = country.iso2
        ;

        input.addEventListener('close:countrydropdown', function(){
            var country = maskPhone.getSelectedCountryData(),
                code = country.iso2
            ;
        });
    }
    $('.js-order-phone').bind('change keyup input click', function(){
        if ($(this).val().match(/[^0-9\+]/g)){
            $(this).val($(this).val().replace(/[^0-9\+]/g, ''));
        }
        var num = this.value.substr(0, 1);
        if (num !== '+'){
            $(this).val('+' + $(this).val());
        }
    });
</script>