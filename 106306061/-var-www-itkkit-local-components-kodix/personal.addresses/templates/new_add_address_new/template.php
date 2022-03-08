<?php
/**
 * Created by:  KODIX 09.10.14 11:59
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
//echo '<pre>';print_r($arResult["ADDRESS_PROPS"]);echo '</pre>';
?>
<?
$arHiddenFields = array('DELIVERY_HOUSE', 'DELIVERY_CORPUS', 'DELIVERY_BUILDING', 'DELIVERY_FLAT');
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
    <div class="checkout__step-subheading checkout__step-subheading--sm-margin"><?=GetMessage('ADDRESS_TYPE_DELIVERY')?></div>

    <?
    echo '<pre style="display: none">';
    print_r($arResult['ERRORS']);
    echo '</pre>';
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

        if($io == 1) echo '<div class="grid-row">';

        $required="";
        if($value["REQUIED"]=="Y") $required="required";

        switch(true){
            case $value["TYPE"]=="TEXTAREA":
                ?>
                    <textarea name="ADDRESS[<?=$form_type?>][DELIVERY][<?=$key?>]" class="form-input form-input--textarea textarea validate_eng" placeholder="<?=GetMessage($value['CODE'])?>"><?=$value['DEFAULT_VALUE']?></textarea>
                <?
            break;

            case $value["CODE"]=="DELIVERY_COUNTRY":
                ?>
                <div class="col-md-6">
                    <select class="chosen-select js-chosen-select form-input  <?=$required?>" name="ADDRESS[<?=$form_type?>][DELIVERY][<?=$key?>]">
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
                        <input type="text" name="ADDRESS[<?=$form_type?>][DELIVERY][<?=$key?>]" placeholder="<?=GetMessage($value['CODE'])?>" value="<?=$value['DEFAULT_VALUE']?>" class="form-input <?=$required?> <?=$key !== 'DELIVERY_PHONE' ? 'validate_eng' : ''?> <?=$key == 'DELIVERY_PHONE' ? 'js-order-phone' : ''?>"/>
                    </div>
                </div>
                <?
            break;
        }
        if($io == 2){ $io = 0; echo '</div>';}
    }if($io != 0) echo '</div>';
    ?>

    <input type="hidden" name="ACTION[<?=$form_type?>][SAVE]" value="Y">
    <input type="submit" class="btn btn--primary btn--inline-lg btn--block-md checkout__btn--step" value="<?=GetMessage('SAVE_ADDRESS')?>" />
</form>
<script>

    $(".chosen-select").change(function() {

        let val = $(".chosen-single > span");
        $.ajax({
            method: "POST",
            url: "getCountryCode.php",
            data: {COUNTRY_NAME_RU : val[0].outerText},
            dataType: "text"
        }).done(function(data) {

            let iconFlag = $(".header-action__dd-for > .icon-flag");
            let arCurrentClassFlag = iconFlag.attr("class").split(" ");
            let newClassName = "icon-flag-" + data;
            let currentClassName = arCurrentClassFlag[1];

            if (data == '' || data == 'Fail') {
                newClassName = "icon-flag-LV";
            }

            iconFlag.removeClass(currentClassName).addClass(newClassName).next('span').html(data);
        });
    });

    function isValid(form){
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
console.log(is_valid);
        return is_valid;
    }
</script>

<style>
    .form-input.form-input--textarea.textarea {
        margin-bottom: 35px;
    }
</style>
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