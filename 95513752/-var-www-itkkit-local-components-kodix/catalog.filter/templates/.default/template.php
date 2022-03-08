<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
/**
 * Created by:  KODIX 07.07.14 12:49
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Salnikov Dmitry
 */
$arResult=$arResult;
global $APPLICATION;
//printr($arResult);
?>
<div class="sidebar" style="border: red 1px solid">
    <?if(intval($arResult['FILTER'])){
        $arPagen=array();
        foreach($_GET as $key=>$val){
            if(preg_match('/^PAGEN_\d+$/i',$key)){
                $arPagen[]=$key;
            }
        }
        ?>
        <span data-href="<?=$APPLICATION->GetCurPageParam('FILTER='.intval($arResult['FILTER']),array_merge($arPagen,array('FILTER')))?>" id="new_addres_push"></span>
    <?}?>
    <form action="#" class="filter" id="catalog_filter">
        <input type="hidden" name="FILTER[FORM]" value="CATALOG">
        <?if($arParams['FILTER_PRICE']){?>
            <fieldset class="open">
                <h3><?=GetMessage('KODIX_FILTER_PRICE')?></h3>
                <div class="line">
                        <div class="line ">
                            <input type="text" name="FILTER[PRICE][MIN]" value="<?=intval(str_replace(' ','',$arResult['FILTRATED_VALUES']['PRICE']['MIN']))?>"><?=str_replace(' ','',str_replace('#','',$arResult['CURRENCY']['FORMAT_STRING']))?>
                            <input type="text" name="FILTER[PRICE][MAX]" value="<?=intval(str_replace(' ','',$arResult['FILTRATED_VALUES']['PRICE']['MAX']))?>"><?=str_replace(' ','',str_replace('#','',$arResult['CURRENCY']['FORMAT_STRING']))?>
                        </div>
                </div>
                <div id="priceSlider" data-max-val="<?=intval($arResult['PRICE_VALUES']['MAX'])?>" data-min-val="<?=intval($arResult['PRICE_VALUES']['MIN'])?>" ></div>
            </fieldset>
        <?}?>
            <fieldset class="open">
                <h3><?=GetMessage('KODIX_FILTER_CHILDREN_SECTIONS')?></h3>
                <div class="line checkbox">

                    <?if(is_array($arResult['CHILDREN_SECTIONS']) && count($arResult['CHILDREN_SECTIONS'])){?>
                    <?foreach ($arResult['CHILDREN_SECTIONS'] as $section) {?>
                        <div class="line checkbox2">
                            <?/*<label for="cat<?=$section['ID']?>"><?=$section['NAME']?></label>
                            <div class="checkbox2">
                                <input name="FILTER[SECTION]" id="cat<?=$section['ID']?>" type="checkbox" value="<?=$section['ID']?>"/>
                            </div>*/?>
                            <a href="<?=$section['SECTION_PAGE_URL']?>"><?=$section['NAME']?></a>
                        </div>
                    <?}?>
                    <?}?>
                    <?if(!$arResult['IS_TOP_SECTION']){?>
                        <div class="line checkbox2">
                            <a href="../"><?=GetMessage('KODIX_FILTER_LINK_UP')?></a>
                        </div>
                    <?}?>
                </div>
            </fieldset>
        <?foreach ($arResult['PROPERTIES'] as $prop) {
            if(/*count($prop['VALUES']) || */( /*!count($prop['VALUES']) &&*/ count($arResult['PROPERTY_VALUES'][$prop['CODE']])>1 ) ){
            ?>
            <fieldset class="open">
                <h3><?=$prop['NAME']?></h3>
                <div class="line checkbox">
                    <?switch($prop['PROPERTY_TYPE']){
                        case 'E':
                            foreach($prop['VALUES'] as $val_id => $val){?>
                            <div class="line checkbox2">
                                <label for="cat<?=$val_id?>"><?=$val['NAME']?></label>
                                <div class="checkbox2">
                                    <input name="FILTER[PROP][<?=$prop['CODE']?>][]" id="cat<?=$val_id?>" type="checkbox" value="<?=$val_id?>" <?if(in_array($val_id,$arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']])){echo 'checked="checked" ';}?>/>
                                </div>
                            </div>
                            <?}
                            break;
                        case 'L':
                            foreach($prop['VALUES'] as $val_id => $val){
                                if(isset($arResult['PROPERTY_VALUES'][$prop['CODE']][$val_id])){?>
                                    <div class="line checkbox2">
                                    <label for="cat<?=$val_id?>"><?=$val['NAME']?></label>
                                    <div class="checkbox2">
                                        <input name="FILTER[PROP][<?=$prop['CODE']?>][]" id="cat<?=$val_id?>" type="checkbox" value="<?=$val_id?>" <?if(in_array($val_id,$arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']])){echo 'checked="checked" ';}?>/>
                                    </div>
                                </div>
                            <?}
                            }
                            break;
                        case 'S':

                            //dump($prop);
                            if($prop['USER_TYPE'] === 'directory'){
                                foreach($prop['VALUES'] as $val_id => $val){
                                    //проверяем естьли такие значения в выборке
                                    if(isset($arResult['PROPERTY_VALUES'][$prop['CODE']][$val_id])){?>
                                    <div class="line checkbox2">
                                        <label for="cat<?=$val_id?>"><?=$val['NAME']?></label>
                                        <div class="checkbox2">
                                            <input name="FILTER[PROP][<?=$prop['CODE']?>][]" id="cat<?=$val_id?>" type="checkbox" value="<?=$val_id?>" <?if(in_array($val_id,$arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']])){echo 'checked="checked" ';}?>/>
                                        </div>
                                    </div>
                                <?}
                                }
                            }
                            elseif($prop['CODE'] === 'CML2_MANUFACTURER'){
                                foreach($prop['VALUES'] as $val_id => $val){
                                    //проверяем естьли такие значения в выборке
                                    if(isset($arResult['PROPERTY_VALUES'][$prop['CODE']][$val_id])){?>
                                    <div class="line checkbox2">
                                        <label for="cat<?=$val_id?>"><?=$val['NAME']?></label>
                                        <div class="checkbox2">
                                            <input name="FILTER[PROP][<?=$prop['CODE']?>][]" id="cat<?=$val_id?>" type="checkbox" value="<?=$val_id?>" <?if(in_array($val_id,$arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']])){echo 'checked="checked" ';}?>/>
                                        </div>
                                    </div>
                                <?}
                                }
                            }
                            elseif(isset($arResult['PROPERTY_VALUES'][$prop['CODE']])){
                                foreach($arResult['PROPERTY_VALUES'][$prop['CODE']] as $val_id => $val){
                                    if(trim($val_id)){?>
                                    <div class="line checkbox2">
                                        <label for="cat<?=$val_id?>"><?=$val_id?></label>
                                        <div class="checkbox2">
                                            <input name="FILTER[PROP][<?=$prop['CODE']?>][]" id="cat<?=$val_id?>" type="checkbox" value="<?=$val_id?>" <?if(in_array($val_id,$arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']])){echo 'checked="checked" ';}?>/>
                                        </div>
                                    </div>
                                <?}
                                }
                            }
                            break;
                        default:
                            //варианты реальных значений
                            if(isset($arResult['PROPERTY_VALUES'][$prop['CODE']])){
                                foreach($arResult['PROPERTY_VALUES'][$prop['CODE']] as $val_id => $val){?>
                                        <div class="line checkbox2">
                                            <label for="cat<?=$val_id?>"><?=$val?></label>
                                            <div class="checkbox2">
                                                <input name="FILTER[PROP][<?=$prop['CODE']?>][]" id="cat<?=$val_id?>" type="checkbox" value="<?=$val_id?>" <?if(in_array($val_id,$arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']])){echo 'checked="checked" ';}?>/>
                                            </div>
                                        </div>
                                    <?}
                            }
                            break;
                    }?>
                </div>
            </fieldset>
            <?
        }
        }
        ?>
    </form>
</div>