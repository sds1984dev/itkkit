<?php
/**
 * Created by PhpStorm.
 * User: Kodix
 * Date: 02.08.2017
 * Time: 18:02
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

<?
if(!function_exists('drawMenuItemMobie')){
    /**
     * Функция рекурсивно выводит подготовленные в result_modifier.php пункты меню, размечая её в зависимости от уровня вложенности и параметров
     * */
    function drawMenuItemMobie($arItem, $depthLevel, $type = false){
        if (($arItem['item']["PERMISSION"] <= "D" || $arItem['item']['PARAMS']['type'] == "mobile") && $depthLevel != 3) return;
        $haveChilrens = count($arItem['children']);
        switch ($depthLevel){
            case 1:
                ;?>

                    <?if($haveChilrens){?>
                        <div class="js-accordion__item">
                            <div class="accordion__title js-accordion__title nav-panel__item nav-panel__item--border
                                 <?if($arItem['item']['SELECTED'] == '1'){?>current<?}?> <?=$arItem['item']['PARAMS']['CLASS']?>"><?=$arItem['item']['TEXT']?></div>
                            <div class="accordion__content js-accordion__content 111">
                                <?
                                $i=1;
                                foreach($arItem['children'] as $children){
                                    $localDepth = $children['item']['PARAMS']['DEPTH_LEVEL']?:$children['item']['DEPTH_LEVEL'];
                                    // for brands
                                    if($arItem['item']['PARAMS']['BRANDS'] == "Y")
                                        $localDepth = 3;

                                    drawMenuItemMobie($children, $localDepth, 'desktop');
                                    $i++;
                                    // if($i==5)
                                        // break;
                                }?>
                                <a class="link link--primary" href="<?=$arItem['item']['LINK']?>"><b><?=GetMessage('VIEW_ALL')?></b></a>
                            </div>
                        </div>
                    <?} else {?>
                        <a class="nav-panel__item nav-panel__item--border link--bold
                            <?if($arItem['item']['SELECTED'] == '1'){?>current<?}?> <?=$arItem['item']['PARAMS']['CLASS']?>
                                " href="<?=$arItem['item']['LINK']?>"><?=$arItem['item']['TEXT']?></a>
                    <?}?>
                <?break;
            case 2:?>
                <?//if($type=='desktop'){?>
                    <a class="link link--primary <?if($arItem['item']['SELECTED'] == '1'){?> link--active<?}?>" href="<?=$arItem['item']['LINK']?>"><?=$arItem['item']['TEXT'];?></a>
                <?//}?>
                <?break;
            case 3:?>
                <a class="link link--primary <?if($arItem['item']['SELECTED'] == '1'){?> link--active<?}?>" href="<?=$arItem['item']['DETAIL_PAGE_URL']?>">
                    <?=LANGUAGE_ID == 'en' ? $arItem['item']['PROPERTY_EN_NAME_VALUE'] : $arItem['item']['NAME'];?>
                </a>
                <?break;
            default:
                break;
        }
    }
}?>

<?if (!empty($arResult)){?>
    <div class="nav-panel__top-menu accordion">
        <?foreach($arResult as $key => $arItem){
            $current_depth = $arItem['item']['PARAMS']['DEPTH_LEVEL']?:$arItem['item']['DEPTH_LEVEL'];
            drawMenuItemMobie($arItem, $current_depth);
        }?>
    </div>
<?}?>
