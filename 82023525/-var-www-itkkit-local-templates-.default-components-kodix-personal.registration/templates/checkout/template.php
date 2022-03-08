<?php
/**
 * Created by:  KODIX 25.03.2015 16:35
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<form data-validation-function="validate_form_eng_char"
      data-ajax-response-wrapper="#block_register"
      method="post" class="ajax_load"
      action="/local/app/auth.php?template=checkout&fields=<?=implode(',', array_keys($arParams['FIELDS']))?>&register=yes">
    <?=bitrix_sessid_post()?>
    <div class="checkout_section active">
        <div class="checkout_sec_title">1. <?=GetMessage('Данные о покупателе')?></div>
        <a href="/checkout/?action=login" class="ajax_load checkout_auth_btn" data-ajax-response-wrapper="#checkout_wrap"><?=GetMessage('Войти')?></a>


        <div class="lang_error_msg popup_lang_err" style="display:none"><?=GetMessage('VALIDATION_ERROR')?></div>
        <?$cou = 0;foreach($arParams['FIELDS'] as $name => $arField){ $cou++;
            ?>
            <?=$cou==1?'<div class="checkout_sec_row mt">':''?>
            <div class="checkout_sec_column_<?=$cou?>">
            <input
                type="<?=$arField['TYPE']?>"
                id="<?=$name?>"
                name="USER[<?=$name?>]"
                value="<?=$arResult[$name]['VALUE']?>"
                placeholder="<?=$arField['NAME']?>"
                class="<?=($name=='NAME' || $name=='LAST_NAME'?'validate_eng ':'')?>f_field_v1<?=(!empty($arResult[$name]['ERROR'])) ? ' alarm' : ''?>">
            </div>
            <?=$cou==2?'</div>':''?>
        <?
            if($cou > 1) $cou = 0;
        }?>

        <?if(!empty($arResult['ERROR'])){?>
            <div class="error_msg ">
                <?foreach ($arResult['ERROR'] as $error) {
                    ShowError($error);
                }?>
            </div>
        <?}?>
        <div class="txt_center">
            <button class="btn lg_btn"><?=GetMessage('Продолжить')?></button>
        </div>
    </div>
</form>