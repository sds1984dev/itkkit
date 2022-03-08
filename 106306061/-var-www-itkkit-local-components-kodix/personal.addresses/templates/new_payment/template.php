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
?>

<?=bitrix_sessid_post()?>
<input type="hidden" name="ADDRESS_FORM" value="Y">
<input type="hidden" name="DELIVERY_TYPE" value="ADDRESS"/>
<input type="hidden" id="" name="EDIT_ADDRESS" value="Y"/>
<input type="hidden" id="PROFILE_ID" name="PROFILE_ID" value="<?=$lastProfileID?>"/>
<input type="hidden" name="PROFILE" value="<?=$lastProfileID?>"/>

<div class="checkout_sec_row">
    <div class="check_item_v1">
        <input type="checkbox" <?=$type == 'self' || $arParams['pay_system_id'] == 3?'checked':''?> name="MATCHES" value="Y" id="check_1" class="form_f_check_v1 js_block_address_ctrl">
        <label for="check_1" class="form_lbl_check_v1 check_label"><?=GetMessage('Адрес оплаты совпадает с адресом доставки')?></label>
    </div>
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

            if($io == 1) echo '<div class="checkout_sec_row">';

            $required="";
            if($value["REQUIED"]=="Y") $required="required";

            switch(true){
                case $value["TYPE"]=="TEXTAREA":
                    ?>
                    <div class="checkout_sec_row">
                        <textarea name="ADDRESS[<?=$lastProfileID?>][<?=$type?>][<?=$key?>]" class="validate_eng full_width f_field_v1 textarea" placeholder="<?=GetMessage($value['CODE'])?>"><?=$value['DEFAULT_VALUE']?></textarea>
                    </div>
                    <?
                break;

                case $value["CODE"]==$type."_COUNTRY":
                    ?>
                    <div class="checkout_sec_column_<?=$io?>">
                        <select name="ADDRESS[<?=$lastProfileID?>][<?=$type?>][<?=$key?>]" class="full_width f_field_v1 js_select_color ik <?=$required?>">
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
                    ?>
                    <div class="checkout_sec_column_<?=$io?>">
                        <input type="text" name="ADDRESS[<?=$lastProfileID?>][<?=$type?>][<?=$key?>]" placeholder="<?=GetMessage($value['CODE'])?>" value="<?=$value['DEFAULT_VALUE']?>" class="validate_eng <?=$required?> f_field_v1"/>
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

<div class="txt_center">
    <input type="hidden" name="ACTION[<?=$lastProfileID?>][SAVE]" value="Y">
    <input type="submit" class="btn lg_btn" value="<?=GetMessage('Продолжить')?>" />
</div>
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
                $(inp).addClass("alarm");
                is_valid=false;
            }else{
                $(inp).removeClass("alarm");
            }
            //$(inp).focusout();
        });
        $.each($(form).find($("select.required")), function(i, select){
            if(!$(select).val()){
                $(select).closest(".form_row").removeClass("ok").addClass("not_ok");
                is_valid=false;
            }else{
                $(select).closest(".form_row").removeClass("not_ok").addClass("ok");
            }
        });
        if($(form).find(".not_ok").length) is_valid = false;
        return is_valid;
    }
</script>