<?php
/**
 * Created by:  KODIX 09.10.14 11:59
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
//echo '<pre>';print_r($arResult["ADDRESS_PROPS"]);echo '</pre>';
?><?
$lastProfileID = KDXAddress::getLastAddressId();
$type = $arResult['ADDRESSES'][$lastProfileID]['PAY']['TYPE'];
$arHiddenFields = array('PAY_HOUSE', 'PAY_CORPUS', 'PAY_BUILDING', 'PAY_FLAT');
?>

<?=bitrix_sessid_post()?>
<input type="hidden" name="ADDRESS_FORM" value="Y">
<input type="hidden" name="DELIVERY_TYPE" value="ADDRESS"/>
<input type="hidden" id="" name="EDIT_ADDRESS" value="Y"/>
<input type="hidden" id="PROFILE_ID" name="PROFILE_ID" value="<?=$lastProfileID?>"/>
<input type="hidden" name="PROFILE" value="<?=$lastProfileID?>"/>
<div class="checkout__step-subheading"><?=GetMessage('ADDRESS_TYPE_PAY')?>
    <div class="tooltip js_tooltip">
        <svg class="icon icon-info">
            <use xlink:href="/local/templates/kit_new/resources/svg/app.svg#info"></use>
        </svg>
        <div class="tooltip__block">
            <div class="tooltip__content">
                <div class="tooltip__content-text">
                    <span><?=GetMessage('PAYMENT_INFO')?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-row form-row--double-gap">
    <label class="checkbox">
        <input type="checkbox" <?=$type == 'self' || $arParams['pay_system_id'] == 3?'checked':''?> name="MATCHES" value="Y" id="check_1" class="checkbox__input js_block_address_ctrl">
        <div class="checkbox__icon"></div>
        <div class="checkbox__label">
            <?=GetMessage('Адрес оплаты совпадает с адресом доставки')?>
        </div>
    </label>
</div>

<div class="lang_error_msg popup_lang_err" style="display:none"><?=GetMessage('VALIDATION_ERROR')?></div>

<?
    if(!empty($arResult['ERRORS']))
    {
        foreach($arResult['ERRORS'] as $id=>$errors)
        {
            foreach($arResult['ERRORS'][$id]['PAY'] as $error)
            {
                echo ShowError(GetMessage($error));
            }
        }
    }

echo '<div class="form_list mod_2 mb_10 js_block_address" '.($type == 'self' || $arParams['pay_system_id'] == 3?'style="display: none;"':'').'>';

$io = 0;

foreach($arResult['ADDRESS_GROUPS'] as $type){
    echo '<div'.($type != 'PAY' ? ' style="display: none;"' : '').'>';
        foreach($arResult["ADDRESS_PROPS"][$type] as $key=>$value){
            $io++;
            $value['NAME'] = str_replace('P_', '', $value['NAME']);

            if($lastProfileID) {
                $value['DEFAULT_VALUE'] = $arResult["ADDRESSES"][$lastProfileID][$type][$key];
            }

            if($io == 1) echo '<div class="grid-row">';

            $required="";
            if($value["REQUIED"]=="Y") $required="required";

            switch(true){
                case $value["TYPE"]=="TEXTAREA":
                    ?>
                        <textarea name="ADDRESS[<?=$lastProfileID?>][<?=$type?>][<?=$key?>]" class="form-input form-input--textarea textarea validate_eng" placeholder="<?=GetMessage($value['CODE'])?>"><?=$value['DEFAULT_VALUE']?></textarea>
                    <?
                break;

                case $value["CODE"]==$type."_COUNTRY":
                    ?>
                    <div class="col-md-6">
                        <select name="ADDRESS[<?=$lastProfileID?>][<?=$type?>][<?=$key?>]" class="chosen-select js-chosen-select form-input js_autocomplete <?=$required?>">
                            <?
                                foreach($arResult["COUNTRIES"] as $c_id=>$c)
                                {
                                    ?>
                                    <option value="<?=$c['LOC_CODE']?>" <?=$value['DEFAULT_VALUE'] == $c['LOC_CODE']?'selected=""':''?>>
                                        <?=$c['COUNTRY_NAME']?>
                                    </option>
                                    <?
                                }
                            ?>
                        </select>
                    </div>
                    <?
                break;

                default:
                    if(in_array($value["CODE"], $arHiddenFields))
                        continue;
                    ?>
                    <div class="col-md-6">
                        <div class="form-row form-row--lg-gap">
                            <input type="text" name="ADDRESS[<?=$lastProfileID?>][<?=$type?>][<?=$key?>]" placeholder="<?=GetMessage($value['CODE'])?>" value="<?=$value['DEFAULT_VALUE']?>" class="form-input <?=$required?> validate_eng"/>
                        </div>
                    </div>
                    <?
                break;
            }
            if($io == 2){ $io = 0; echo '</div>';}
        }
        if($io != 0){ $io = 0; echo '</div>';}
    echo '</div>';
}
echo '</div>';
?>

<input type="hidden" name="ACTION[<?=$lastProfileID?>][SAVE]" value="Y">
<input type="submit" class="btn btn--primary btn--inline-lg btn--block-md checkout__btn--step" value="<?=GetMessage('Продолжить')?>" />

<script>
    function isValid(form){

        if(!$('input[name=PAYSYSTEM]:checked').val()){
            $('input[name=PAYSYSTEM]').closest(".check_item_v1").addClass('alarm');
            return false;
        }

        if($('.js_block_address_ctrl:checked').val() === 'Y') return true;

        var is_valid=true;
        $.each($(form).find($("input.required")), function(i, inp){
            if(!$(inp).val()){
                $(inp).parent().addClass("error");
                is_valid=false;
            }else{
                $(inp).parent().removeClass("error");
            }
            //$(inp).focusout();
        });
        $.each($(form).find($("select.required")), function(i, select){
            if(!$(select).val()){
                $(select).closest(".form_row").removeClass("ok").addClass("error");
                is_valid=false;
            }else{
                $(select).closest(".form_row").removeClass("error").addClass("ok");
            }
        });
        if($(form).find(".not_ok").length) is_valid = false;
        return is_valid;
    }
</script>

<style>
    .form-input.form-input--textarea.textarea {
        margin-bottom: 35px;
    }
</style>