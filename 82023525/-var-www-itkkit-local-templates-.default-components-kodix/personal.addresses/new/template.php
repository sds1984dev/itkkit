<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
//$arHiddenFields = array('DELIVERY_HOUSE', 'DELIVERY_FLAT', 'PAY_HOUSE', 'PAY_FLAT');
$key = 1;
?>
<div class="grid-row" style="display: block;">
    <?$phoneNum = 1;?>
    <?foreach($arResult['ADDRESSES'] as $profileID => $arAddress){?>
        <?$key++;?>
        <form action="" method="post" class="addresses-form text_validation_eng_char">
        <?foreach($arResult['ADDRESS_GROUPS'] as $groupID => $groupCode){?>
            <div class="col-lg-5 <?if($groupCode=="PAY"){?>col-lg-offset-1<?}?>">
                <div class="account-section">
                    <div class="account-section__heading 111"><?=GetMessage('ADDRESS_TYPE_'.$groupCode) . ' №'.$key?>
                        <?if($groupCode=="PAY"){?>
                            <div class="tooltip js_tooltip">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#info"></use>
                                </svg>
                                <div class="tooltip__block">
                                    <div class="tooltip__content">
                                        <div class="tooltip__content-text">
                                            <span><?=GetMessage('PAYMENT_INFO')?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?}?>
                    </div>
                    <?$arAddressProps = $arResult['ADDRESS_PROPS'][ $groupCode ];?>
<!--                    <form>-->
                        <div class="form-row">
                            <select class="chosen-select js-chosen-select form-input">
                                <?
                                $str = '';
                                foreach ($arParams['FIELD_MAP_'] as $row) {
                                    if ($row === 'CITY' || $row === 'STREET') {
                                        $newRow = $groupCode . '_' . $row;
                                        $prop = $arAddressProps[$newRow];
                                        $str .= $arAddress[$groupCode][$prop['CODE']] . ' ';
                                    }
                                }
                                ?>
                                <option><?=$str?></option>
                            </select>
                        </div>
                        <div class="grid-row">
                            <div class="col-sm-6" data-toggle-for="address_<?=$profileID?>" data-toggle-id="address_<?=$profileID?>">
                                <button class="btn link link--primary account-section__edit" type="button"><?=GetMessage('EDIT_BTN')?></button>
                            </div>
                        </div>
                        <div data-toggle-id="address_<?=$profileID?>" data-toggle-show="false">
                            <?foreach($arParams['FIELD_MAP'] as $arRow){?>
                                <div class="grid-row">
                                    <?foreach($arRow as $row){
                                        $row = $groupCode.'_'.$row;
                                        if(!array_key_exists($row,$arAddressProps))
                                            continue;
                                        $prop = $arAddressProps[$row];
                                        $isEmail = (stripos($prop['CODE'], 'EMAIL')!==false); //флаг isEmail показывает является ли текущий input полем для email
                                        ?>
                                        <?$value = $_REQUEST['ADDRESS'][$profileID][$groupCode][ $prop['CODE'] ]? htmlspecialchars($_REQUEST['ADDRESS'][$profileID][$groupCode][ $prop['CODE'] ]):($arAddress[$groupCode][ $prop['CODE'] ]?:$prop['DEFAULT_VALUE']);?>
                                        <?switch($prop['TYPE']){
                                            case'TEXTAREA':
                                                ?>
                                                <textarea placeholder="<?=GetMessage($prop['CODE'])?>" class="form-input form-input--textarea textarea" id="field_NaN" name="ADDRESS[<?=$profileID?>][<?=$groupCode?>][<?=$prop['CODE']?>]"><?=$value?></textarea>
                                                <?
                                                break;
                                            case'LOCATION':
                                                ?>
                                                <div class="col-md-6">
                                                    <select class="chosen-select js-chosen-select form-input js_autocomplete" name="ADDRESS[<?=$profileID?>][<?=$groupCode?>][<?=$prop['CODE']?>]">
                                                        <?foreach($arResult['COUNTRIES'] as $arLoc){?>
                                                            <option value="<?=$arLoc['LOC_CODE']?>" <?if($value == $arLoc['LOC_CODE']){?>selected="selected" <?}?>><?=$arLoc['COUNTRY_NAME']?></option>
                                                        <?}?>
                                                    </select>
                                                </div>
                                                <?
                                                break;
                                            default:
                                                if(in_array($prop["CODE"], $arHiddenFields))
                                                    continue;
                                                ?>
                                                <div class="col-md-6">
                                                    <div class="form-row form-row--lg-gap">
                                                        <input type="text" id="field_7" name="ADDRESS[<?=$profileID?>][<?=$groupCode?>][<?=$prop['CODE']?>]" placeholder="<?=GetMessage($prop['CODE'])?>" class="form-input <?=(!$isEmail) ? 'validate_eng' : ''?> <?=($isPhone) ? 'phone_validate' : ''?><?=$prop['CODE'] == 'DELIVERY_PHONE' || $prop['CODE'] == 'PAY_PHONE' ? ' js-personal-phone js-personal-phone'.$phoneNum : ''?>" value="<?=$value?>">
                                                    </div>
                                                </div>
                                                <?
                                                if ($prop['CODE'] == 'DELIVERY_PHONE' || $prop['CODE'] == 'PAY_PHONE') $phoneNum++;
                                                break;
                                        }
                                        ?>
                                    <?}?>
                                </div>
                            <?}?>

                            <?if($groupCode == 'PAY'){?>
                                <div class="grid-row">
                                    <div class="col-sm-6">
                                        <input name="ACTION[<?=$profileID?>][SAVE]" class="btn btn--secondary btn--block-md" value="<?=GetMessage('SAVE_ADDRESS')?>" type="submit">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="submit" name="ACTION[<?=$profileID?>][DELETE]" value="<?=GetMessage('DELETE_ADDRESS')?>" class="btn btn--secondary btn--block-md">
                                    </div>
                                </div>
<!--                                <li class="form_item_v1 mod_submit">-->
<!--                                    <input type="submit" name="ACTION[--><?//=$profileID?><!--][DELETE]" value="--><?//=GetMessage('DELETE_ADDRESS')?><!--" class="f_submit_v1">-->
<!--                                    <input type="submit" name="ACTION[--><?//=$profileID?><!--][SAVE]" value="--><?//=GetMessage('SAVE_ADDRESS')?><!--" class="f_submit_v1 trigger_focus">-->
<!--                                </li>-->
                            <?} else {?>
                                <div class="grid-row">
                                    <div class="col-sm-6">
                                        <a class="btn btn--secondary btn--block-md" href="#" data-toggle-for="address_<?=$profileID?>"><?=GetMessage('CANCEL_BTN')?></a>
                                    </div>
                                </div>
                            <?}?>
                        </div>
<!--                    </form>-->
                </div>
            </div>
            <input type="hidden" name="ADDRESS[<?=$profileID?>][<?=$groupCode?>][PROFILE_ID]" value="<?=$profileID?>">
        <?}?>
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="ADDRESS_FORM" value="Y">
        </form>
    <?}?>
</div>

<div class="grid-row">
    <div class="col-sm-12 col-md-6">
        <a class="btn btn--secondary btn--block-md mod_add" href="#" data-toggle-for="address_NEW" data-toggle-id="address_<?=$profileID?>">
            <?=GetMessage('ADD_ADDRESS')?>
        </a>
    </div>
</div>
<div class="grid-row" data-toggle-id="address_NEW" data-toggle-show="<?=isset($_REQUEST['ADDRESS']['NEW']) ? 'true' : 'false'?>" style="display: block;">
    <form id="new_addr_form" action="" method="post" class="addresses-form text_validation_eng_char">
        <?foreach($arResult['ADDRESS_GROUPS'] as $groupID => $groupCode){?>
            <div class="col-lg-5 <?if($groupCode=="PAY"){?>col-lg-offset-1<?}?>">
                <div class="account-section">
                    <div class="account-section__heading 112"><?=GetMessage('ADDRESS_TYPE_'.$groupCode)?>
                        <?if($groupCode=="PAY"){?>
                            <div class="tooltip js_tooltip">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#info"></use>
                                </svg>
                                <div class="tooltip__block">
                                    <div class="tooltip__content">
                                        <div class="tooltip__content-text">
                                            <span><?=GetMessage('PAYMENT_INFO')?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?}?>
                    </div>
                    <?$arAddressProps = $arResult['ADDRESS_PROPS'][ $groupCode ];?>

                    <?if(!empty($arResult['ERRORS']['NEW'][$groupCode]))
                    {
                        foreach($arResult['ERRORS']['NEW'][$groupCode] as $error)
                        {
                            echo ShowError(GetMessage($error));
                        }
                    }?>
                    <div class="lang_error_msg" style="display:none"><?=ShowError(GetMessage('VALIDATION_ERROR'))?></div>
                    <div class="phone_error_msg" style="display:none"><?=ShowError(GetMessage('PHONE_VALIDATION_ERROR'))?></div>

                    <?foreach($arParams['FIELD_MAP'] as $arRow){?>
                        <div class="grid-row">
                            <?foreach($arRow as $row){
                                $row = $groupCode.'_'.$row;
                                if(!array_key_exists($row,$arAddressProps))
                                    continue;
                                $prop = $arAddressProps[$row];
                                $isEmail = (stripos($prop['CODE'], 'EMAIL')!==false) //флаг isEmail показывает является ли текущий input полем для email
                                ?>
                                <?$value = $_REQUEST['ADDRESS']['NEW'][$groupCode][ $prop['CODE'] ]? htmlspecialchars($_REQUEST['ADDRESS']['NEW'][$groupCode][ $prop['CODE'] ]):$prop['DEFAULT_VALUE'];?>

                                <?switch($prop['TYPE']){
                                    case'TEXTAREA':
                                        ?>
                                        <textarea placeholder="<?=GetMessage($prop['CODE'])?>" class="form-input form-input--textarea textarea" id="field_NaN" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]"><?=$value?></textarea>
                                        <?
                                        break;
                                    case'LOCATION':
                                        ?>
                                        <div class="col-md-6">
                                            <select class="chosen-select js-chosen-select form-input js_autocomplete" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]">
                                                <?foreach($arResult['COUNTRIES'] as $arLoc){?>
                                                    <option value="<?=$arLoc['LOC_CODE']?>" <?if($value == $arLoc['LOC_CODE']){?>selected="selected" <?}?>><?=$arLoc['COUNTRY_NAME']?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                        <?
                                        break;
                                    default:
                                        if(in_array($prop["CODE"], $arHiddenFields))
                                            continue;
                                        ?>
                                        <div class="col-md-6">
                                            <div class="form-row form-row--lg-gap">
                                                <input type="text" id="field_7" name="ADDRESS[NEW][<?=$groupCode?>][<?=$prop['CODE']?>]" placeholder="<?=GetMessage($prop['CODE'])?>" class="form-input <?=(!$isEmail) ? 'validate_eng' : ''?> <?=($isPhone) ? 'phone_validate' : ''?><?=$prop['CODE'] == 'DELIVERY_PHONE' || $prop['CODE'] == 'PAY_PHONE' ? ' js-personal-phone js-personal-phone'.$phoneNum : ''?>" value="<?=$value?>">
                                            </div>
                                        </div>
                                        <?
                                        if ($prop['CODE'] == 'DELIVERY_PHONE' || $prop['CODE'] == 'PAY_PHONE') $phoneNum++;
                                        break;
                                }
                                ?>

                            <?}?>
                        </div>
                    <?}?>
                    <?if($groupCode == 'PAY'){?>
                        <div class="grid-row">
                            <div class="col-sm-6">
                                <input name="ACTION[NEW][SAVE]" class="btn btn--secondary btn--block-md" value="<?=GetMessage('SAVE_ADDRESS')?>" type="submit">
                            </div>
                            <div class="col-sm-6">
                                <a class="btn btn--secondary btn--block-md" href="#" data-toggle-for="address_NEW"><?=GetMessage('CANCEL_BTN')?></a>
                            </div>
                        </div>
                    <?}?>

                    <input type="hidden" name="ADDRESS_FORM" value="Y">
                    <input type="hidden" name="ADDRESS[NEW][<?=$groupCode?>][PROFILE_ID]" value="NEW">
                </div>
            </div>
        <?}?>
        <?=bitrix_sessid_post()?>
    </form>
</div>

<style>
    form.addresses-form {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }
    .form-input.form-input--textarea.textarea {
        margin-bottom: 35px;
    }
</style>