<?php
/**
 * Created by:  KODIX 09.10.14 11:59
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
?>
<script type="text/javascript">
    KDXSale.addresses = <?=json_encode($arResult['COUNTRIES'])?>;
</script>

<div class="addresses_list radio_group">

    <?if(count($arResult['ADDRESSES'])>0){?>
        <?$last_addr=KDXAddress::getLastAddressId();?>
        <?foreach($arResult["ADDRESSES"] as $k=>$addr){?>
            <div class="rg_item special_option shown" data-option="current_address" data-id="<?=$addr->profile_id?>">

                <a href="#" class="fl_right fs13 so_trigger jumpover" data-target="change_address" data-profile_id="<?=$addr->profile_id?>">Изменить</a>

                <label id="info" class="radio_label">
                    <input type="radio" <?=$last_addr==$addr->profile_id ? "checked" : ""?> value="<?=$addr->profile_id?>" name="address">
                    <?=$addr->country?>, <?=KDXAddress::getCityById($addr->city)?>,<br>
                    <?=$addr->getShortAddress();?><br>
                    <?=$addr->phone?>
                </label>


            </div><!--/rg_item-->
            <?$arAddress = (array)$addr;?>
            <!-- Изменение адреса -->
            <div class="rg_item special_option" data-option="change_address" data-id="<?=$addr->profile_id?>" style="display: none">

                <a href="#" class="fl_right fs13 so_trigger jumpover" data-target="cancel_address" data-id="<?=$addr->profile_id?>">Отмена</a>

                <label class="radio_label"><input type="radio" value="<?=$addr->profile_id?>" name="address" class="no_change"> Изменить адрес</label>

                <div class="std_form input28">
                    <form method="post" id="kdx_edit_addr" class="ajax_load" data-ajax-response-wrapper=".cart_reload" data-validation-function="isValid">
                        <input type="hidden" name="DELIVERY_TYPE" value="ADDRESS">
                        <input type="hidden" id="" name="EDIT_ADDRESS" value="Y"/>
                        <input type="hidden" id="PROFILE_ID" name="PROFILE_ID" value="<?=$addr->profile_id?>"/>
                        <?foreach($arResult["ADDRESS_PROPS"] as $p){?>
                            <?if($p["REQUIED"]=="Y"){
                                $required="required";
                            }else{
                                $required="";
                            }?>
                            <?if($p["TYPE"]=="TEXTAREA"){?>
                                <div class="form_row margintop05x">
                                    <textarea name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" class="<?=$required?>" placeholder="<?=GetMessage($p["CODE"])?>" data-default-value="<?=$p['DEFAULT_VALUE']?>"><?=$arAddress[strtolower($p['CODE'])]?></textarea>
                                </div><!--/form_row-->
                            <?}elseif($p["CODE"]=="COUNTRY"){?>
                                <div class="form_row">
                                    <?$selected=$arAddress[strtolower($p['CODE'])];?>
                                    <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                                    <select name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" class="ik <?=$required?>">
                                        <?foreach($arResult["COUNTRIES"] as $c_id=>$c){?>
                                            <option <?if($c['NAME']==$selected){ $country = $c_id;
                                                    ?>selected="selected"<?}?> value="<?=$c_id?>"><?=$c['NAME']?></option>
                                        <?}?>
                                    </select>
                                </div><!--/form_row-->
                            <?}elseif($p["CODE"]=="CITY"){?>
                                <div class="form_row">
                                    <?$selected=$arAddress[strtolower($p['CODE'])];?>
                                    <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                                    <select name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" class="combobox <?=$required?>">
                                        <?foreach($arResult["COUNTRIES"][$country]["CITIES"] as $c){?>
                                            <option <?if($c['ID']==$selected){?>selected="selected"<?}?> value="<?=$c['ID']?>"><?=$c['NAME']?></option>
                                        <?}?>
                                    </select>
                                </div>
                            <?}elseif($p["CODE"]=="HOUSE" || $p["CODE"]=="CORPUS" || $p["CODE"]=="FLAT"){?>
                                <div class="form_row third_part">
                                    <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                                    <input name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" class="<?=$required?>" type="text" value="<?=$arAddress[strtolower($p['CODE'])]?>" data-default-value="<?=$p['DEFAULT_VALUE']?>">
                                </div><!--/form_row-->
                            <?}else{?>
                                <div class="form_row">
                                    <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                                    <input name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" class="<?=$required?>" type="text" value="<?=$arAddress[strtolower($p['CODE'])]?>" data-default-value="<?=$p['DEFAULT_VALUE']?>">
                                </div><!--/form_row-->
                            <?}?>
                        <?}?>

                        <div class="form_row">
                            <label>&nbsp;</label>
                            <input class="compact" type="submit" value="Сохранить">
                        </div><!--/form_row-->

                    </form>
                </div><!--/std_from-->

            </div><!--/rg_item-->
        <?}?>
    <?}?>

    <!-- Добавление нового -->
    <div id="ADD_ADDRESS" class="rg_item">
        <label class="radio_label"><input type="radio" name="address" value="NEW" <?if(count($arResult['ADDRESSES'])<=0){?>checked="checked"<?}?>> Новый адрес</label>
        <div class="hidden_optoins" <?if(count($arResult['ADDRESSES'])>0){?>style="display: none"<?}?>>
            <div class="std_form input28">
                <form method="post" id="kdx_edit_addr" class="ajax_load" data-ajax-response-wrapper=".cart_reload"  data-validation-function="isValid">
                    <input type="hidden" name="DELIVERY_TYPE" value="ADDRESS">
                    <input type="hidden" id="" name="EDIT_ADDRESS" value="Y"/>
                    <input type="hidden" id="PROFILE_ID" name="PROFILE_ID" value=""/>
                    <?foreach($arResult["ADDRESS_PROPS"] as $p){?>
                        <?if($p["REQUIED"]=="Y"){
                            $required="required";
                        }else{
                            $required="";
                        }?>
                        <?if($p["TYPE"]=="TEXTAREA"){?>
                            <div class="form_row margintop05x">
                                <textarea name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" class="<?=$required?>" placeholder="<?=GetMessage($p["CODE"])?>" data-default-value="<?=$p['DEFAULT_VALUE']?>"><?=$p['DEFAULT_VALUE']?></textarea>
                            </div><!--/form_row-->
                        <?}elseif($p["CODE"]=="COUNTRY"){?>
                            <div class="form_row">
                                <?
                                if(intval($arResult["DEFAULT_COUNTRY"])){
                                    $selected=intval($arResult["DEFAULT_COUNTRY"]);
                                }else{
                                    $selected=array_shift(array_keys($arResult["COUNTRIES"]));
                                }
                                ?>
                                <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                                <select name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" class="ik <?=$required?>">
                                    <?foreach($arResult["COUNTRIES"] as $c_id=>$c){?>
                                        <option value="<?=$c_id?>"  <?if($c['ID']==$selected){ $country = $c_id;?>selected="selected"<?}?>><?=$c['NAME']?></option>
                                    <?}?>
                                </select>
                            </div><!--/form_row-->
                        <?}elseif($p["CODE"]=="CITY"){?>
                            <div class="form_row">
                                <?
                                if(intval($arResult["DEFAULT_CITY"])){
                                    $selected=intval($arResult["DEFAULT_CITY"]);
                                }else{
                                    $selected=array_shift(array_keys($arResult["CITIES"]));
                                }
                                ?>
                                <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                                <select name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" class="combobox <?=$required?>">
                                    <?foreach($arResult["CITIES"] as $c_id=>$c){?>
                                        <option value="<?=$c_id?>" <?if($c_id==$selected){?>selected="selected"<?}?>><?=$c?></option>
                                    <?}?>
                                </select>
                            </div>
                        <?}elseif($p["CODE"]=="HOUSE" || $p["CODE"]=="CORPUS" || $p["CODE"]=="FLAT"){?>
                            <div class="form_row third_part">
                                <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                                <input name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" class="<?=$required?>" type="text" value="<?=$p['DEFAULT_VALUE']?>" data-default-value="<?=$p['DEFAULT_VALUE']?>">
                            </div><!--/form_row-->
                        <?}else{?>
                            <div class="form_row">
                                <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                                <input name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" class="<?=$required?>" type="text" value="<?=$p['DEFAULT_VALUE']?>" data-default-value="<?=$p['DEFAULT_VALUE']?>">
                            </div><!--/form_row-->
                        <?}?>
                    <?}?>

                    <div class="form_row">
                        <label>&nbsp;</label>
                        <input class="compact" type="submit" value="Сохранить">
                    </div><!--/form_row-->

                </form>
            </div>
        </div><!--/hidden_options-->
    </div><!--/rg_item-->


</div><!--/radio_group-->