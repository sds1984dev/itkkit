<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$lastProfileID = KDXAddress::getLastAddressId();
$rr_grab_user_email = 'onblur="var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;if(regex.test(this.value)) { try {rrApi.setEmail(this.value);}catch(e){}}"';
$arReadonlyFields = array(
    'DELIVERY_ZIP',
    'DELIVERY_COUNTRY',
    'DELIVERY_CITY',
    'DELIVERY_STREET',
    'DELIVERY_HOUSE',
    'DELIVERY_FLAT',
);
?>
<?if($USER->IsAuthorized()){?>
    <?if(!empty($arResult['USER_RESULT'])){?>
        <?if($arResult['USER_RESULT']['TYPE'] == 'ERROR'){
            ShowMessage($arResult['USER_RESULT']);
        }?>
    <?}?>
<!-- profiles list -->
<div class="order_data_v2">
    <div class="title_v4"><?=GetMessage('CP_KO_ADDRESS')?></div>
    <form name="PROFILE" action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap">
        <ul class="radio_list">
            <?foreach($arResult['ADDRESSES'] as $profileID => $arAddresses){?>
                <li class="radio_item_v1">
                    <input type="radio" name="PROFILE" value="<?=$profileID?>" id="radio_<?=$profileID?>" <?if($profileID == $lastProfileID){?>checked=""<?}?> class="form_f_rad_v1">
                    <label for="radio_<?=$profileID?>" class="form_lbl_rad_v1">
                        <i><?=GetMessage('ADDRESS_LABEL_PAY')?></i><?=KDXAddress::makeShortAddress($arAddresses['PAY'],'PAY')?><br>
                        <i><?=GetMessage('ADDRESS_LABEL_DELIVERY')?></i><?=KDXAddress::makeShortAddress($arAddresses['DELIVERY'],'DELIVERY')?>
                    </label>
                </li>
            <?}?>

        </ul>
    </form>
    <button class="btn_coupon address_add"><?=GetMessage('ADD_ADDRESS')?></button>
    <?if($lastProfileID){?>
    <button data-profile="<?=$lastProfileID?>" class="btn_coupon address_edit"><?=GetMessage('EDIT_ADDRESS')?></button>
    <?}?>
</div><!-- /profiles list -->
<div class="profile_form" data-profile="NEW" <?if(isset($_REQUEST['ADDRESS_FORM']) && $_REQUEST['PROFILE'] == 'NEW' && $_REQUEST['SUCCESS'] !== 'Y'){?>style="display: block;"<?}?>>
    <form action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap" data-validation-function="validate_form_eng_char">
        <input type="hidden" name="ADDRESS_FORM" value="Y">
        <input type="hidden" name="PROFILE" value="NEW">
        <?=bitrix_sessid_post()?>
<?foreach($arResult['ADDRESS_GROUPS'] as $groupID => $groupCode){?>
<?$arAddressProps = $arResult['ADDRESS_PROPS'][ $groupCode ];?>
    <!-- pay address -->
    <div class="list_holder mod_1 base_wrapper_eng_char">
        <?if($groupCode == 'DELIVERY'){?>
            <ul class="check_list">
                <li class="check_item_v1">
                    <input type="checkbox" name="PICKUP" value="Y" <?if($_REQUEST['PICKUP']){?>checked="" <?}?> id="check_PICKUP" class="form_f_check_v1">
                    <label for="check_PICKUP" class="form_lbl_check_v1"><?=GetMessage('PICKUP_ADDRESS')?></label>
                </li>
            </ul>
        <?}?>
        <h3 class="profile_title_v2 mod_1"><?=GetMessage('ADDRESS_TYPE_'.$groupCode)?></h3>
        <?if($groupCode == 'PAY'){?>
            <div class="order_note" style="padding: 0rem"><?=GetMessage('P_A_NOTE')?></div>
        <ul class="check_list">
            <li class="check_item_v1">
                <input type="checkbox" name="MATCHES" value="Y" <?if($_REQUEST['MATCHES']){?>checked="" <?}?> id="check_1" class="form_f_check_v1 js_block_address_ctrl">
                <label for="check_1" class="form_lbl_check_v1 js_block_address_ctrl"><?=GetMessage('MATCH_ADDRESS')?></label>
            </li>
        </ul>
        <?}?>
        <ul class="form_list mod_2 <?if($groupCode == 'PAY'){?>mb_10 js_block_address<?}?>" <?if($groupCode == 'PAY' && $_REQUEST['MATCHES']){?>style="display: none;"<?}?>>
            <li>
                <?if(!empty($arResult['ERRORS']['NEW'][$groupCode]))
                {
                    foreach($arResult['ERRORS']['NEW'][$groupCode] as $error)
                    {
                        echo ShowError(GetMessage($error));
                    }
                }?>
            <div class="lang_error_msg" style="display:none"><?=ShowError(GetMessage('VALIDATION_ERROR'))?></div>
            <div class="phone_error_msg" style="display:none"><?=ShowError(GetMessage('PHONE_VALIDATION_ERROR'))?></div>
            </li>
        <?foreach($arParams['FIELD_MAP'] as $arRow){?>
            <li class="form_item_v1">
                <?$class = count($arRow) == 1?'mod_full':'mod_1';?>
                <?foreach($arRow as $row){
                    $row = $groupCode.'_'.$row;
                    if(!array_key_exists($row,$arAddressProps))
                        continue;?>
                    <?$prop = $arAddressProps[$row]?>
                    <?$bRO = ($groupCode == 'DELIVERY' && in_array($prop['CODE'], $arReadonlyFields) && isset($_REQUEST['PICKUP']))?'readonly="readonly"':'';?>
                    <?$bSelectRO = ($groupCode == 'DELIVERY' && in_array($prop['CODE'], $arReadonlyFields) && isset($_REQUEST['PICKUP']))?'disabled="disabled"':'';?>
                    <?$isEmail = (stripos($prop['CODE'], 'EMAIL')!==false) //флаг isEmail показывает является ли текущий input полем для email?>
                    <?$isPhone = (stripos($prop['CODE'], 'PHONE')!==false) //флаг isPhone показывает является ли текущий input полем для телефона?>
                    <dl class="form_cell_v1 <?=$class?>">
                        <dt class="form_hline_v1">
                            <label for="field_7" class="hide"><?=GetMessage($prop['CODE'])?></label>
                        </dt>
                        <dd class="form_f_w_v1">
                            <?$value = $_REQUEST['ADDRESS']['NEW'][$groupCode][ $prop['CODE'] ]? htmlspecialchars($_REQUEST['ADDRESS']['NEW'][$groupCode][ $prop['CODE'] ]):($arAddress[$groupCode][ $prop['CODE'] ]?:$prop['DEFAULT_VALUE']);?>
                            <?switch($prop['TYPE']){
                                case'TEXTAREA':
                                    ?>
                                    <textarea <?=$bRO?> placeholder="<?=GetMessage($prop['CODE'])?>" class="f_field_v1 textarea" id="field_NaN" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]"><?=$value?></textarea>
                                    <?
                                    break;
                                case'LOCATION':
                                    ?>
                                    <input type="hidden" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]" value="0000028003">
                                    <select <?=$bSelectRO?> class="f_field_v1" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]">
                                        <?foreach($arResult['COUNTRIES'] as $arLoc){?>
                                            <option value="<?=$arLoc['LOC_CODE']?>" <?if($value == $arLoc['LOC_CODE']){?>selected="selected" <?}?>><?=$arLoc['COUNTRY_NAME']?></option>
                                        <?}?>
                                    </select>
                                    <?
                                    break;
                                default:
                                    ?>
                                    <input <?=$bRO?> type="text" id="field_7" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]" placeholder="<?=GetMessage($prop['CODE'])?>" class="f_field_v1 <?=(!$isEmail && !$isPhone) ? 'validate_eng' : ''?> <?=($isPhone) ? 'phone_validate' : ''?>" value="<?=$value?>">
                                    <?
                                    break;
                            }?>
                        </dd>
                    </dl>
                <?}?>
            </li>
        <?}?>

        </ul>
        <?if($groupCode == 'PAY'){?>
        <ul>
        <li class="form_item_v1 mod_submit">
                    <input type="hidden" name="ACTION[NEW][SAVE]" value="Y">
                    <input type="submit" value="<?=GetMessage('SAVE_ADDRESS')?>" class="f_submit_v1 trigger_focus">
                </li>
        </ul>
        <?}?>
    </div>
<?}?>
    </form>
</div><!-- /profile_form -->
<?foreach($arResult['ADDRESSES'] as $profileID => $arAddress){?>
<div class="profile_form" data-profile="<?=$profileID?>" <?if(isset($_REQUEST['ADDRESS_FORM']) && $_REQUEST['PROFILE'] == $profileID && $_REQUEST['SUCCESS'] !== 'Y'){?>style="display: block;"<?}?>>
    <form action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap" data-validation-function="validate_form_eng_char">
        <input type="hidden" name="ADDRESS_FORM" value="Y">
        <input type="hidden" name="PROFILE" value="<?=$profileID?>">
        <?=bitrix_sessid_post()?>
        <?foreach($arResult['ADDRESS_GROUPS'] as $groupID => $groupCode){?>
            <?$arAddressProps = $arResult['ADDRESS_PROPS'][ $groupCode ];?>
            <!-- pay address -->
            <div class="list_holder mod_1 base_wrapper_eng_char">
                <h3 class="profile_title_v2 mod_1"><?=GetMessage('ADDRESS_TYPE_'.$groupCode)?></h3>
                <?if($groupCode == 'PAY'){?>

                    <div class="order_note" style="padding: 0"><?=GetMessage('P_A_NOTE')?></div>

                    <ul class="check_list">
                        <li class="check_item_v1">
                            <input type="checkbox" name="MATCHES" value="Y" <?if($_REQUEST['MATCHES']){?>checked="" <?}?> id="MATCHES_<?=$profileID?>" class="form_f_check_v1 js_block_address_ctrl">
                            <label for="MATCHES_<?=$profileID?>" class="form_lbl_check_v1 js_block_address_ctrl"><?=GetMessage('MATCH_ADDRESS')?></label>
                        </li>
                    </ul>
                <?}?>
                <ul class="form_list mod_2 <?if($groupCode == 'PAY'){?>mb_10 js_block_address<?}?>" <?if($groupCode == 'PAY' && $_REQUEST['MATCHES']){?>style="display: none;"<?}?>>
                    <li>
                        <?if(!empty($arResult['ERRORS'][$profileID][$groupCode]))
                        {
                            foreach($arResult['ERRORS'][$profileID][$groupCode] as $error)
                            {
                                echo ShowError(GetMessage($error));
                            }
                        }?>
                    <div class="lang_error_msg" style="display:none"><?=ShowError(GetMessage('VALIDATION_ERROR'))?></div>
                    <div class="phone_error_msg" style="display:none"><?=ShowError(GetMessage('PHONE_VALIDATION_ERROR'))?></div>
                    </li>
                    <?foreach($arParams['FIELD_MAP'] as $arRow){?>
                        <li class="form_item_v1">
                            <?$class = count($arRow) == 1?'mod_full':'mod_1';?>
                            <?foreach($arRow as $row){
                                $row = $groupCode.'_'.$row;
                                if(!array_key_exists($row,$arAddressProps))
                                    continue;?>
                                <?$prop = $arAddressProps[$row]?>
                                <?$isEmail = (stripos($prop['CODE'], 'EMAIL')!==false) //флаг isEmail показывает является ли текущий input полем для email?>
                                <?$isPhone = (stripos($prop['CODE'], 'PHONE')!==false) //флаг isPhone показывает является ли текущий input полем для телефона?>
                                <dl class="form_cell_v1 <?=$class?>">
                                    <dt class="form_hline_v1">
                                        <label for="field_7" class="hide"><?=GetMessage($prop['CODE'])?></label>
                                    </dt>
                                    <dd class="form_f_w_v1">
                                        <?$value = $_REQUEST['ADDRESS']['NEW'][$groupCode][ $prop['CODE'] ]? htmlspecialchars($_REQUEST['ADDRESS'][ $profileID ][$groupCode][ $prop['CODE'] ]):($arAddress[$groupCode][ $prop['CODE'] ]?:$prop['DEFAULT_VALUE']);?>
                                        <?switch($prop['TYPE']){
                                            case'TEXTAREA':
                                                ?>
                                                <textarea placeholder="<?=GetMessage($prop['CODE'])?>" class="f_field_v1 textarea" id="field_NaN" name="ADDRESS[<?=$profileID?>][<?=$groupCode?>][<?=$prop['CODE']?>]"><?=$value?></textarea>
                                                <?
                                                break;
                                            case'LOCATION':
                                                ?>
                                                <select class="f_field_v1" name="ADDRESS[<?=$profileID?>][<?=$groupCode?>][<?=$prop['CODE']?>]">
                                                    <?foreach($arResult['COUNTRIES'] as $arLoc){?>
                                                        <option value="<?=$arLoc['LOC_CODE']?>" <?if($value == $arLoc['LOC_CODE']){?>selected="selected" <?}?>><?=$arLoc['COUNTRY_NAME']?></option>
                                                    <?}?>
                                                </select>
                                                <?
                                                break;
                                            default:
                                                ?>
                                                <input type="text" id="field_7" name="ADDRESS[<?=$profileID?>][<?=$groupCode?>][<?=$prop['CODE']?>]" placeholder="<?=GetMessage($prop['CODE'])?>" class="f_field_v1 <?=(!$isEmail && !$isPhone) ? 'validate_eng' : ''?> <?=($isPhone) ? 'phone_validate' : ''?>" value="<?=$value?>" <?=($isEmail && !$USER->IsAuthorized()) ? $rr_grab_user_email : ''?>>
                                                <?
                                                break;
                                        }?>
                                    </dd>
                                </dl>
                            <?}?>
                        </li>
                    <?}?>

                </ul>
                <?if($groupCode == 'PAY'){?>
                    <ul>
                        <li class="form_item_v1 mod_submit">
                            <input type="hidden" name="ACTION[<?=$profileID?>][SAVE]" value="Y">
                            <input type="submit" value="<?=GetMessage('SAVE_ADDRESS')?>" class="f_submit_v1 trigger_focus">
                        </li>
                    </ul>
                <?}?>
            </div>
        <?}?>
    </form>
</div><!-- /profile_form -->
<?}?>
<?}else{?>
<div class="order_data_v2">
    <div class="title_v4"><?=GetMessage('CP_KO_ADDRESS')?></div>
</div><!-- /profiles list -->
    <div class="profile_form" data-profile="NEW" style="display: block;">
        <form action="" method="post" class="text_validation_eng_char" data-ajax-response-wrapper="#checkout_wrap">
            <input type="hidden" name="ADDRESS_FORM" value="Y">
            <input type="hidden" name="PROFILE" value="NEW">
            <input type="hidden" name="NEW_USER" value="Y">
            <?=bitrix_sessid_post()?>
            <?foreach($arResult['ADDRESS_GROUPS'] as $groupID => $groupCode){?>
                <?$arAddressProps = $arResult['ADDRESS_PROPS'][ $groupCode ];?>
                <!-- pay address -->
                <div class="list_holder mod_1 base_wrapper_eng_char">
                    <?if($groupCode == 'DELIVERY'){?>
                        <ul class="check_list">
                            <li class="check_item_v1">
                                <input type="checkbox" name="PICKUP" value="Y" <?if($_REQUEST['PICKUP']){?>checked="" <?}?> id="check_PICKUP" class="form_f_check_v1">
                                <label for="check_PICKUP" class="form_lbl_check_v1"><?=GetMessage('PICKUP_ADDRESS')?></label>
                            </li>
                        </ul>
                    <?}?>
                    <h3 class="profile_title_v2 mod_1"><?=GetMessage('ADDRESS_TYPE_'.$groupCode)?></h3>
                    <?if($groupCode == 'PAY'){?>
                        <div class="order_note" style="padding: 0"><?=GetMessage('P_A_NOTE')?></div>
                        <?if(!$_REQUEST['PICKUP']){?>
                        <ul class="check_list">
                            <li class="check_item_v1">
                                <input type="checkbox" name="MATCHES" value="Y" <?if($_REQUEST['MATCHES']){?>checked="" <?}?> id="check_1" class="form_f_check_v1 js_block_address_ctrl">
                                <label for="check_1" class="form_lbl_check_v1 js_block_address_ctrl"><?=GetMessage('MATCH_ADDRESS')?></label>
                            </li>
                        </ul>
                        <?}?>
                    <?}?>
                    <ul class="form_list mod_2 <?if($groupCode == 'PAY'){?>mb_10 js_block_address<?}?>" <?if($groupCode == 'PAY' && $_REQUEST['MATCHES']){?>style="display: none;"<?}?>>
                        <li>
                            <?if(!empty($arResult['ERRORS']['NEW'][$groupCode]))
                            {
                                foreach($arResult['ERRORS']['NEW'][$groupCode] as $error)
                                {
                                    ShowError(GetMessage($error));
                                }

                            }?>
                            <div class="lang_error_msg" style="display:none"><?=ShowError(GetMessage('VALIDATION_ERROR'))?></div>
                            <div class="phone_error_msg" style="display:none"><?=ShowError(GetMessage('PHONE_VALIDATION_ERROR'))?></div>
                            <?if(!empty($arResult['USER_RESULT'])){?>
                                <?if($arResult['USER_RESULT']['TYPE'] == 'ERROR'){
                                    ShowMessage($arResult['USER_RESULT']);
                                }else{?>
                                    <script>window.location.reload(true)</script>
                                <?}?>
                            <?}?>
                        </li>
                        <?foreach($arParams['FIELD_MAP'] as $arRow){?>
                            <li class="form_item_v1">
                                <?$class = count($arRow) == 1?'mod_full':'mod_1';?>
                                <?foreach($arRow as $row){
                                    $row = $groupCode.'_'.$row;
                                    if(!array_key_exists($row,$arAddressProps))
                                        continue;?>
                                    <?$prop = $arAddressProps[$row]?>
                                    <?$bRO = ($groupCode == 'DELIVERY' && in_array($prop['CODE'], $arReadonlyFields) && isset($_REQUEST['PICKUP']))?'readonly="readonly"':'';?>
                                    <?$bSelectRO = ($groupCode == 'DELIVERY' && in_array($prop['CODE'], $arReadonlyFields) && isset($_REQUEST['PICKUP']))?'disabled="disabled"':'';?>
                                    <?$isEmail = (stripos($prop['CODE'], 'EMAIL')!==false) //флаг isEmail показывает является ли текущий input полем для email?>
                                    <?$isPhone = (stripos($prop['CODE'], 'PHONE')!==false) //флаг isPhone показывает является ли текущий input полем для телефона?>
                                    <dl class="form_cell_v1 <?=$class?>">
                                        <dt class="form_hline_v1">
                                            <label for="field_7" class="hide"><?=GetMessage($prop['CODE'])?></label>
                                        </dt>
                                        <dd class="form_f_w_v1">
                                            <?$value = $_REQUEST['ADDRESS']['NEW'][$groupCode][ $prop['CODE'] ]? htmlspecialchars($_REQUEST['ADDRESS']['NEW'][$groupCode][ $prop['CODE'] ]):($arAddress[$groupCode][ $prop['CODE'] ]?:$prop['DEFAULT_VALUE']);?>
                                            <?switch($prop['TYPE']){
                                                case'TEXTAREA':
                                                    ?>
                                                    <textarea <?=$bRO?> placeholder="<?=GetMessage($prop['CODE'])?>" class="f_field_v1 textarea" id="field_NaN" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]"><?=$value?></textarea>
                                                    <?
                                                    break;
                                                case'LOCATION':
                                                    ?>
                                                    <input type="hidden" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]" value="0000028003">
                                                    <select <?=$bSelectRO?> class="f_field_v1" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]">
                                                        <?foreach($arResult['COUNTRIES'] as $arLoc){?>
                                                            <option value="<?=$arLoc['LOC_CODE']?>" <?if($value == $arLoc['LOC_CODE']){?>selected="selected" <?}?>><?=$arLoc['COUNTRY_NAME']?></option>
                                                        <?}?>
                                                    </select>
                                                    <?
                                                    break;
                                                default:
                                                    ?>
                                                    <input <?=$bRO?> type="text" id="field_7" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]" placeholder="<?=GetMessage($prop['CODE'])?>" class="f_field_v1 <?=(!$isEmail && !$isPhone) ? 'validate_eng' : ''?> <?=($isPhone) ? 'phone_validate' : ''?>" value="<?=$value?>">
                                                    <?
                                                    break;
                                            }?>
                                        </dd>
                                    </dl>
                                <?}?>
                            </li>
                        <?}?>

                    </ul>
                    <?if($groupCode == 'PAY'){?>
                        <ul>
                            <li class="form_item_v1 mod_submit">
                                <input type="hidden" name="ACTION[NEW][SAVE]" value="Y">
                                <input type="submit" value="<?=GetMessage('SAVE_ADDRESS')?>" class="f_submit_v1 trigger_focus">
                            </li>
                        </ul>
                    <?}?>
                </div>
            <?}?>
        </form>
    </div><!-- /profile_form -->

<?}?>
<?if ($USER->IsAuthorized()){?>
    <script>
        $(document).ready(function(){
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(regex.test("<?=$USER->GetEmail()?>")) {
                try {rrApi.setEmail("<?=$USER->GetEmail()?>");}catch(e){}
            }
        });
    </script>
<?}?>