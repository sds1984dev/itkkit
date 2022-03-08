<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="profile_box mod_1">
    <h2 class="profile_title"><?=GetMessage('ADDRESS_TITLE')?></h2>
    <?foreach($arResult['ADDRESSES'] as $profileID => $arAddress){?>
    <div class="list_holder">
        <?foreach($arResult['ADDRESS_GROUPS'] as $groupID => $groupCode){?>
        <?$arAddressProps = $arResult['ADDRESS_PROPS'][ $groupCode ];?>
        <div class="profile_b_item">
            <h3 class="profile_title_v2"><?=GetMessage('ADDRESS_TYPE_'.$groupCode)?></h3>
            <ul class="profile_data_list mod_1">
            <?foreach($arParams['FIELD_MAP'] as $rows){?>
                <?foreach($rows as $row){?>
                    <?$row = $groupCode.'_'.$row?>
                <?$prop = $arAddressProps[$row]?>
                <li class="profile_d_l_item"><?=$arAddress[$groupCode][ $prop['CODE'] ]?></li>
                <?}?>
            <?}?>
            </ul>

        </div>
        <?}?>
    </div>
    <?}?>
    <div class="btn_hold"><a href="/personal/address/" class="btn_profile mod_edit"><?=GetMessage('CP_AL_EDIT')?></a></div>
</div>