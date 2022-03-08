<?php
/**
 * Created by:  KODIX 17.03.2015 11:22
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$min = intval($arResult['PRICE_VALUES']['MIN']);
$max = ceil($arResult['PRICE_VALUES']['MAX']);

$jsValueMin = intval($arResult['FILTRATED_VALUES']['PRICE']['MIN'])?:$min;
$jsValueMax = ceil($arResult['FILTRATED_VALUES']['PRICE']['MAX'])?:$max;

$valueMin = $arResult['FILTRATED_VALUES']['PRICE']['MIN']?:'';
$valueMax = $arResult['FILTRATED_VALUES']['PRICE']['MAX']?:'';

$scale = intval(1 / $arResult['CURRENCY']['AMOUNT']);
?>
<?if(isAjax() && !isRestoreHistory('ALL')){?>
<?if(intval($arResult['FILTER'])){?>
    <span class="ajax_remove_in_new_before_append" data-href="<?=$APPLICATION->GetCurPageParam('FILTER='.intval($arResult['FILTER']),array('FILTER'))?>" id="new_addres_push"></span>
<?}else{?>
    <span class="ajax_remove_in_new_before_append" data-href="<?=$APPLICATION->GetCurPageParam('',array('FILTER'))?>" id="new_addres_push"></span>
<?}?>
<?}?>
<?if(!isAjax() || isRestoreHistory('ALL')) {?>




<div class="primary_row">
    <form action="<?=$APPLICATION->GetCurPageParam('',array('FILTER'))?>" name="form_brand" method="post" id="catalog_filter" class="ajax_load push_history" data-ajax-response-wrapper="#catalog .catalog_block">

    <dl class="filter_block">

        <?if(isset($arResult['PROPERTIES']['SELECTIONS']) && count($arResult['PROPERTY_VALUES']['SELECTIONS'])){?>
            <dt class="filter_title open"><?=GetMessage('FILTER_NAME_SELECTIONS')?></dt>
            <dd class="filter_content" style="display: block">
                <ul class="check_list <?if(strpos($prop['CODE'],'SIZES') !== false){?>mod_1<?}?>">
                    <?foreach($arResult['PROPERTY_VALUES']['SELECTIONS'] as $val_id => $val){
                        if(trim($val_id)){?>
                            <li class="check_item_v1">
                                <a href="<?=$val['DETAIL_PAGE_URL']?>" class="form_lbl_check_v1 <?=$val['CLASS']?>"><?=LANGUAGE_ID=='en'?$val['NAME_EN']:$val['NAME']?></a>
                            </li>
                        <?}
                    }?>
                </ul>
            </dd><?
        }?>

        <?if(is_array($arResult['CHILDREN_SECTIONS']) && count($arResult['CHILDREN_SECTIONS'])){?>
            <?foreach($arResult['CHILDREN_SECTIONS'] as $arSection){?>
                <?$needShow = $arSection['OPENED'] == 'Y';?>
        <dt class="filter_title open"><?=LANGUAGE_ID=='en'?$arSection['UF_EN_NAME']:$arSection['NAME']?></dt>
        <dd class="filter_content" style="display:block">
            <ul class="check_list">
                <?foreach($arSection['SUBSECTIONS'] as $arSubSection){?>
                <li class="check_item_v1">
                    <?/*<input type="checkbox" name="FILTER[SECTION][]" value="<?=$arSection['ID']?>" id="check_<?=$arSection['CODE'].$arSection['ID']?>" class="form_f_check_v1" <?if(in_array($arSection['ID'],$arResult['FILTRATED_VALUES']['SECTION'])){echo 'checked="checked" ';}?>>
                    <label for="check_<?=$arSection['CODE'].$arSection['ID']?>" class="form_lbl_check_v1"><?=LANGUAGE_ID=='en'?$arSection['UF_EN_NAME']:$arSection['NAME']?></label>*/?>
                    <a href="<?=$arSubSection['SECTION_PAGE_URL']?>" class="form_lbl_check_v1 <?=$arSubSection['CLASS']?>"><?=LANGUAGE_ID=='en'?$arSubSection['UF_EN_NAME']:$arSubSection['NAME']?></a>
                </li>
                <?}?>
            </ul>
        </dd>
            <?}?>

        <?}?>

<?foreach ($arResult['PROPERTIES'] as $prop) {
    if ($prop['CODE'] == 'SELECTIONS') continue;

    if (count($arResult['PROPERTY_VALUES'][$prop['CODE']]) > 1) {

        $needShow = count($arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']]);
        ?>
            <?switch($prop['PROPERTY_TYPE']){
                case 'E':
                case 'S':?>
            <dt class="filter_title <?if($needShow){?>open<?}?>"><?=GetMessage('FILTER_NAME_'.$prop['CODE'])?></dt>
            <dd class="filter_content" <?if($needShow){?>style="display: block"<?}?>>
                <ul class="check_list <?if(strpos($prop['CODE'],'SIZES') !== false){?>mod_1<?}?>">
                <?if($prop['CODE'] === 'CML2_MANUFACTURER'){
                    foreach($prop['VALUES'] as $val_id => $val){
                        if(isset($arResult['PROPERTY_VALUES'][$prop['CODE']][$val_id])){?>
                            <li class="check_item_v1">
                                <input type="checkbox" name="FILTER[PROP][<?=$prop['CODE']?>][]" value="<?=$val_id?>" id="check_<?=$prop['CODE'].$val_id?>" class="form_f_check_v1" <?if(in_array($val_id,$arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']])){echo 'checked="checked" ';}?>>
                                <label for="check_<?=$prop['CODE'].$val_id?>" class="form_lbl_check_v1"><?=$val['NAME']?></label>
                            </li>
                        <?}
                    }
                }else{
                    foreach($arResult['PROPERTY_VALUES'][$prop['CODE']] as $val_id => $val){

                        if(trim($val_id)){?>
                            <li class="check_item_v1">
                                <input type="checkbox" name="FILTER[PROP][<?=$prop['CODE']?>][]" value="<?=$val_id?>" id="check_<?=$prop['CODE'].$val_id?>" class="form_f_check_v1" <?if(in_array($val_id,$arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']])){echo 'checked="checked" ';}?>>
                                <label for="check_<?=$prop['CODE'].$val_id?>" class="form_lbl_check_v1"><?=$val_id?></label>
                            </li>
                        <?}
                    }
                }
                    break;
                case 'P': //Подборки

                break;
            }?>
            </ul>
        </dd>
    <?}
}?>
        <?if(intval($arResult['PRICE_VALUES']['MIN']) < intval($arResult['PRICE_VALUES']['MAX'])){?>
        <dt class="filter_title open"><?=GetMessage('FILTER_NAME_PRICE')?></dt>
        <dd class="filter_content" style="display: block;">
            <div class="range_wrap">
                <span class="range_text"><?=GetMessage('FILTER_NAME_PRICE_FROM')?></span>
                <span class="range_field inpMin_1"><?=$jsValueMin?></span>
                <span class="range_text"><?=GetMessage('FILTER_NAME_PRICE_TO')?></span>
                <span class="range_field inpMax_1"><?=$jsValueMax?></span>
                <span class="range_currency"><?=trim(str_replace('#','',$arResult['CURRENCY']['FORMAT_STRING']))?></span>
            </div>
            <input id="amount_1" type="text" readonly="" name="money_man" class="range_inp">
            <input id="amount_2" type="text" readonly="" name="FILTER[PRICE][MIN]" class="range_inp" value="<?=$valueMin?>">
            <input id="amount_3" type="text" readonly="" name="FILTER[PRICE][MAX]" class="range_inp" value="<?=$valueMax?>">
            <div class="slider-range-w">
                <div id="slider_range"></div>
            </div>
        </dd>
        <?}?>

    </dl>
    </form>
</div>
<?}?>
<script>
<?if(!isAjax() || isRestoreHistory('ALL')){?>
    $(function(){

    //slider_f(slider, inputMin, inputMax, valMin, valMax, valBegin, valEnd, steps)
    slider_f('#slider_range', '.inpMin_1', '.inpMax_1', <?=$min?>, <?=$max?>, <?=$jsValueMin?>, <?=$jsValueMax?>, <?=$scale?>);

    })
<?}?>
</script>
