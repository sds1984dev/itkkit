<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<script>
    var addresses=<?=json_encode($arResult["ADDRESSES"])?>;
</script>
<h1>Мои адреса доставки</h1>
<form method="post" action=""><fieldset>
        <table class="likely">
            <thead>
            <tr>
                <th class="meaning">Адрес и получатель</th>
                <th class="buttons"></th>
            </tr>
            </thead>
            <tbody>
            <?$last_addr=KDXAddress::getLastAddressId();?>
            <?foreach($arResult["ADDRESSES"] as $k=>$addr){?>
            <tr>
                <td>
                    <div class="switch">
                        <input type="radio" class="for_order"  <?=$last_addr==$addr->profile_id ? "checked" : ""?> value="<?=$addr->profile_id?>" name="address" id="address<?=$k?>">
                        <label for="address<?=$k?>"><?=$addr->getFullAddress()?></label>
                    </div>
                </td>
                <td>
                    <a href="#" class="small button edit_addr" profile_id="<?=$addr->profile_id?>">Редактировать</a>
                    <a href="#" class="delete delete_address"  profile_id="<?=$addr->profile_id?>">Удалить</a>
                </td>
            </tr>
            <?}?>
            </tbody>

            <tfoot  class="for_order">
                <tr class="actions">
                    <td>Персональная информация клиента используется исключительно внутри сайта для оформления заказов и связи с клиентом.</td>
                    <td><input class="aux button add_addr" value="ДОБАВИТЬ АДРЕС" type="submit"></td>
                </tr>
                <tr class="continue">
                    <td></td>
                    <td class="buttons"><input type="submit" value="СОХРАНИТЬ И ПРОДОЛЖИТЬ" name="next_step" class="aux button"></td>
                </tr>
            </tfoot>

            <tfoot  class="for_cabinet">
            <tr class="actions">
                <td>Вы можете указать несоклько адресов получения для вашего удобства.<br>Отмеченный адрес является адресом по умолчанию.</td>
                <td><input class="aux button add_addr" value="ДОБАВИТЬ АДРЕС" type="submit"></td>
            </tr>
            </tfoot>
        </table>
    </fieldset></form>

<table class="likely">
    <tr class="add-address">
        <td colspan="2">
            <form method="post" id="kdx_edit_addr" style="display:none">
                <input type="hidden" id="PROFILE_ID" name="PROFILE_ID" value=""/>
                <?foreach($arResult["ADDRESS_PROPS"] as $p){?>
                    <?if($p["TYPE"]=="TEXTAREA"){?>
                        <div class="line">
                            <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                            <textarea name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>"></textarea>
                        </div><!--/form_row-->
                    <?}elseif($p["CODE"]=="COUNTRY"){?>
                        <div class="line">
                            <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                            <select name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>">
                                <?foreach($arResult["COUNTRIES"] as $c_id=>$c){?>
                                    <option value="<?=$c_id?>"><?=$c?></option>
                                <?}?>
                            </select>
                        </div><!--/form_row-->
                    <?}elseif($p["CODE"]=="CITY"){?>
                        <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                        <select name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>">
                            <?foreach($arResult["CITIES"] as $c_id=>$c){?>
                                <option value="<?=$c_id?>"><?=$c?></option>
                            <?}?>
                        </select>
                    <?}else{?>
                        <div class="line">
                            <label for="<?=$p["CODE"]?>"><?=GetMessage($p["CODE"])?></label>
                            <input name="<?=$p["CODE"]?>" id="<?=$p["CODE"]?>" type="text" value="">
                        </div><!--/form_row-->
                    <?}?>
                <?}?>
                <div class="line">
                    <input value="<?=GetMessage("SAVE_ADDRESS");?>"  type="submit" name="save_addr">
                </div><!--/form_row-->
            </form>
        </td>
    </tr>
</table>
