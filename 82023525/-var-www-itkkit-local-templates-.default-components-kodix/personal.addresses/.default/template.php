<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="profile_box mod_1">
    <h2 class="profile_title"><?=GetMessage('ADDRESS_TITLE')?></h2>
    <?foreach($arResult['ADDRESSES'] as $profileID => $arAddress){?>
    <div class="list_holder">
        <form action="" method="post" class="text_validation_eng_char">
            <?foreach($arResult['ADDRESS_GROUPS'] as $groupID => $groupCode){?>
                <?$arAddressProps = $arResult['ADDRESS_PROPS'][ $groupCode ];?>
                <div class="profile_b_item base_wrapper_eng_char">
                    <h3 class="profile_title_v2"><?=GetMessage('ADDRESS_TYPE_'.$groupCode)?></h3>
                    <?if(!empty($arResult['ERRORS'][$profileID][$groupCode]))
                    {
                        foreach($arResult['ERRORS'][$profileID][$groupCode] as $error)
                        {
                            echo ShowError(GetMessage($error));
                        }
                    }?>
                    <div class="lang_error_msg" style="display:none"><?=ShowError(GetMessage('VALIDATION_ERROR'))?></div>
                    <div class="phone_error_msg" style="display:none"><?=ShowError(GetMessage('PHONE_VALIDATION_ERROR'))?></div>
                    <p>&nbsp;</p>

                    <ul class="form_list">
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
                                            <?$value = $_REQUEST['ADDRESS'][$profileID][$groupCode][ $prop['CODE'] ]? htmlspecialchars($_REQUEST['ADDRESS'][$profileID][$groupCode][ $prop['CODE'] ]):($arAddress[$groupCode][ $prop['CODE'] ]?:$prop['DEFAULT_VALUE']);?>
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
                                                    <input type="text" id="field_7" name="ADDRESS[<?=$profileID?>][<?=$groupCode?>][<?=$prop['CODE']?>]" placeholder="<?=GetMessage($prop['CODE'])?>" class="f_field_v1 <?=(!$isEmail && !$isPhone) ? 'validate_eng' : ''?> <?=($isPhone) ? 'phone_validate' : ''?>" value="<?=$value?>">
                                                    <?
                                                    break;
                                            }?>
                                        </dd>
                                    </dl>
                                <?}?>
                            </li>
                        <?}?>
                        <?if($groupCode == 'PAY'){?>
                        <li class="form_item_v1 mod_submit">
                            <input type="submit" name="ACTION[<?=$profileID?>][DELETE]" value="<?=GetMessage('DELETE_ADDRESS')?>" class="f_submit_v1">
                            <input type="submit" name="ACTION[<?=$profileID?>][SAVE]" value="<?=GetMessage('SAVE_ADDRESS')?>" class="f_submit_v1 trigger_focus">
                        </li>
                        <?}?>
                    </ul>


                </div>
                <input type="hidden" name="ADDRESS[<?=$profileID?>][<?=$groupCode?>][PROFILE_ID]" value="<?=$profileID?>">
            <?}?>
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="ADDRESS_FORM" value="Y">
        </form>
    </div>
    <?}?>
    <div class="list_holder">
        <form action="" method="post" class="text_validation_eng_char">

            <?foreach($arResult['ADDRESS_GROUPS'] as $groupID => $groupCode){?>
            <?$arAddressProps = $arResult['ADDRESS_PROPS'][ $groupCode ];?>
            <div class="profile_b_item">
            <h3 class="profile_title_v2"><?=GetMessage('ADDRESS_TYPE_'.$groupCode)?></h3>

                <?if(!empty($arResult['ERRORS']['NEW'][$groupCode]))
                {
                    foreach($arResult['ERRORS']['NEW'][$groupCode] as $error)
                    {
                        echo ShowError(GetMessage($error));
                    }
                }?>
                <div class="lang_error_msg" style="display:none"><?=ShowError(GetMessage('VALIDATION_ERROR'))?></div>
                <div class="phone_error_msg" style="display:none"><?=ShowError(GetMessage('PHONE_VALIDATION_ERROR'))?></div>
                <p>&nbsp;</p>
                <ul class="form_list">
                    <?$bShowSplash = true;?>
                    <?foreach($arParams['FIELD_MAP'] as $arRow){?>
                    <li class="form_item_v1">
                        <?$class = count($arRow) == 1?'mod_full':'mod_1';?>
                        <?foreach($arRow as $row){
                            $row = $groupCode.'_'.$row;
                            if(!array_key_exists($row,$arAddressProps))
                                continue;?>
                            <?$prop = $arAddressProps[$row]?>
                            <?$isEmail = (stripos($prop['CODE'], 'EMAIL')!==false) //флаг isEmail показывает является ли текущий input полем для email?>
                            <dl class="form_cell_v1 <?=$class?>">
                                <dt class="form_hline_v1">
                                    <label for="field_7" class="hide"><?=GetMessage($prop['CODE'])?></label>
                                </dt>
                                <dd class="form_f_w_v1">
                                    <?if($_REQUEST['ADDRESS']['NEW'][$groupCode][ $prop['CODE'] ])$bShowSplash = false;?>
                                    <?$value = $_REQUEST['ADDRESS']['NEW'][$groupCode][ $prop['CODE'] ]? htmlspecialchars($_REQUEST['ADDRESS']['NEW'][$groupCode][ $prop['CODE'] ]):$prop['DEFAULT_VALUE'];?>
                                    <?switch($prop['TYPE']){
                                        case'TEXTAREA':
                                            ?>
                                    <textarea placeholder="<?=GetMessage($prop['CODE'])?>" class="f_field_v1 textarea" id="field_NaN" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]"><?=$value?></textarea>
                                            <?
                                            break;
                                        case'LOCATION':
                                            ?>
                                            <select class="f_field_v1" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]">
                                                <?foreach($arResult['COUNTRIES'] as $arLoc){?>
                                                    <option value="<?=$arLoc['LOC_CODE']?>" <?if($value == $arLoc['LOC_CODE']){?>selected="selected" <?}?>><?=$arLoc['COUNTRY_NAME']?></option>
                                                <?}?>
                                            </select>
                                        <?
                                            break;
                                        default:
                                            ?>
                                            <input type="text" id="field_7" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]" placeholder="<?=GetMessage($prop['CODE'])?>" class="f_field_v1 <?=(!$isEmail) ? 'validate_eng' : ''?> <?=($isPhone) ? 'phone_validate' : ''?>" value="<?=$value?>">
                                            <?
                                            break;
                                    }?>
                                </dd>
                            </dl>
                        <?}?>
                    </li>
                    <?}?>
                    <?if($groupCode == 'PAY'){?>
                        <li class="form_item_v1 mod_submit">
                            <input type="submit" name="ACTION[NEW][SAVE]" value="<?=GetMessage('SAVE_ADDRESS')?>" class="f_submit_v1 trigger_focus">
                        </li>
                    <?}?>
                </ul>


        </div>
            <input type="hidden" name="ADDRESS_FORM" value="Y">
            <input type="hidden" name="ADDRESS[NEW][<?=$groupCode?>][PROFILE_ID]" value="NEW">
            <?}?>
            <?=bitrix_sessid_post()?>
        </form>
        <?if($bShowSplash){?>
        <div class="address_splash">
            <div class="btn_profile_wrap">
                <a href="#" title="#" class="btn_profile mod_add"><?=GetMessage('ADD_ADDRESS')?></a>
            </div>
        </div>
        <?}?>
    </div>
</div>
