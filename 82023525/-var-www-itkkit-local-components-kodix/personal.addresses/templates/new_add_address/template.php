<?php
/**
 * Created by:  KODIX 09.10.14 11:59
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
//echo '<pre>';print_r($arResult["ADDRESS_PROPS"]);echo '</pre>';
?>
<form method="post" id="kdx_edit_addr" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap"  data-validation-function="isValid">
    <?=bitrix_sessid_post()?>

    <?
    $io = 0;

    $last_addr = !empty($_REQUEST['id'])?(int)$_REQUEST['id']:'';

    $form_type = 'NEW';
    if($last_addr) $form_type = $last_addr;

    ?>
    <div class="lang_error_msg popup_lang_err" style="display:none"><?=GetMessage('VALIDATION_ERROR')?></div>
    <input type="hidden" name="MATCHES" value="Y">
    <input type="hidden" name="ADDRESS_FORM" value="Y">
    <input type="hidden" name="DELIVERY_TYPE" value="ADDRESS">
    <input type="hidden" id="" name="EDIT_ADDRESS" value="Y"/>
    <input type="hidden" id="PROFILE_ID" name="PROFILE_ID" value="<?=$form_type?>"/>
    <input type="hidden" name="PROFILE" value="<?=$form_type?>">
    <div class="checkout_sub_title"><?=GetMessage('ADDRESS_TYPE_DELIVERY')?></div>

    <?

    if(!empty($arResult['ERRORS']['NEW']['DELIVERY']))
    {
        foreach($arResult['ERRORS']['NEW']['DELIVERY'] as $error)
        {
            echo ShowError(GetMessage($error));
        }
    }

    //printr($arResult["ADDRESS_PROPS"]["DELIVERY"]);
    foreach($arResult["ADDRESS_PROPS"]["DELIVERY"] as $key=>$value){
        $io++;
        $value['NAME'] = str_replace('D_', '', $value['NAME']);

        if($last_addr) {
            $value['DEFAULT_VALUE'] = $arResult["ADDRESSES"][$last_addr]['DELIVERY'][$key];
        }

        if($io == 1) echo '<div class="checkout_sec_row">';

        $required="";
        if($value["REQUIED"]=="Y") $required="required";

        switch(true){
            case $value["TYPE"]=="TEXTAREA":
                ?>
                <div class="checkout_sec_row">
                    <textarea name="ADDRESS[<?=$form_type?>][DELIVERY][<?=$key?>]" class="validate_eng full_width f_field_v1 textarea" placeholder="<?=GetMessage($value['CODE'])?>"><?=$value['DEFAULT_VALUE']?></textarea>
                </div>
                <?
            break;

            case $value["CODE"]=="DELIVERY_COUNTRY":
                ?>
                <div class="checkout_sec_column_<?=$io?>">
                    <select name="ADDRESS[<?=$form_type?>][DELIVERY][<?=$key?>]" class="full_width f_field_v1 js_select_color ik <?=$required?>">
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
                    <input type="text" name="ADDRESS[<?=$form_type?>][DELIVERY][<?=$key?>]" placeholder="<?=GetMessage($value['CODE'])?>" value="<?=$value['DEFAULT_VALUE']?>" class="validate_eng <?=$required?> f_field_v1"/>
                </div>
                <?
            break;
        }
        if($io == 2){ $io = 0; echo '</div>';}
    }if($io != 0) echo '</div>';
    ?>

    <div class="txt_center">
        <input type="hidden" name="ACTION[<?=$form_type?>][SAVE]" value="Y">
        <input type="submit" class="btn lg_btn" value="<?=GetMessage('SAVE_ADDRESS')?>" />
    </div>
</form>
<script>
    function isValid(form){
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

        if($(form).find('.language_error').is('input'))
        {
            return false;
        }

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