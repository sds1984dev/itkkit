<?php
/**
 * Created by PhpStorm.
 * User: Kodix
 * Date: 02.08.2017
 * Time: 18:02
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!function_exists('drawMenuItem')){
    /**
     * Функция рекурсивно выводит подготовленные в result_modifier.php пункты меню, размечая её в зависимости от уровня вложенности и параметров
     * */
    function drawMenuItem($arItem, $depthLevel, $type = false){
        if (($arItem['item']["PERMISSION"] <= "D" || $arItem['item']['PARAMS']['type'] == "mobile") && $depthLevel != 3) return;
        $haveChilrens = count($arItem['children']);
        switch ($depthLevel){
            case 1:
                ;?>
                <?
                    $additional_class='';
                    if ($haveChilrens || $arItem['item']['PARAMS']['CLASS'] == 'delivery-item') {
                        $additional_class=' header-nav__dd';
                    } 
                    if ($arItem['item']['PARAMS']['BRANDS'] == 'Y') {
                        $additional_class.=' header-nav__dd--brands';
                    }
                    if ($arItem['item']['LINK'] == '/catalog/clothing/') {
                        $additional_class.=' header-nav__dd--clothes';
                    }
                    if ($arItem['item']['LINK'] == '/catalog/accessories/') {
                        $additional_class.=' header-nav__dd--accessories';
                    }
                ?>
                <div class="header-nav__tab <?=$additional_class?>">
                    <a class="header-nav__link <?if($arItem['item']['SELECTED'] == '1'){?>current<?}?> <?=$arItem['item']['PARAMS']['CLASS']?>" href="<?=$arItem['item']['LINK']?>">
                        <?=$arItem['item']['TEXT']?>
                    </a>
                <? if($haveChilrens){?>
                  <?$additional_class='';
                    if ($arItem['item']['PARAMS']['BRANDS'] == 'Y') {
                        $additional_class=' header-nav__dd-content--brands';
                    } 
                    if ($arItem['item']['LINK'] == '/catalog/accessories/') {
                        $additional_class=' header-nav__dd-content--accessories';
                    }
                    if ($arItem['item']['LINK'] == '/catalog/clothing/') {
                        $additional_class=' header-nav__dd-content--clothes';
                    }
                    ?>
                    <div class="header-nav__dd-content<?=$additional_class;?>">
                        <ul class="ul--reset">
                            <?
                            foreach($arItem['children'] as $children){
                                $localDepth = $children['item']['PARAMS']['DEPTH_LEVEL']?:$children['item']['DEPTH_LEVEL'];
                                // for brands
                                if($arItem['item']['PARAMS']['BRANDS'] == "Y")
                                    $localDepth = 3;

                                drawMenuItem($children, $localDepth, 'desktop');
                            }?>
                            <li><a class="link link--primary header-nav__dd-item" href="<?=$arItem['item']['LINK']?>"><b><?=GetMessage('VIEW_ALL')?></b></a></li>
                        </ul>
                    </div>
                <?} elseif($arItem['item']['PARAMS']['CLASS'] == 'delivery-item') {?>
                    <ul class="ul--reset header-nav__dd-content">
                        <li class="header-nav__dd-item"><?=GetMessage('YOUR_COUNTRY')?>
                            <a class="link link--secondary" href="#" data-popup-for="geo_conf"><span class="country-name_">Италии</span></a>?
                        </li>

                        <?
                        $arResult['DELIVERY'] = getListDelivery();
                        foreach($arResult['DELIVERY'] as $arDelivery){
                            foreach($arDelivery['PROFILES'] as $profile => $arProfileParams){
//                                printr($arProfileParams);
                                if($arDelivery['SID'] == 'kdx_self') continue;
                                ?>
                                <li class="header-nav__dd-item">
                                    <div class="header-nav__dd-flex">
                                        <span class="header-nav__dd-left"><?=$arDelivery['NAME']?> -
                                            <?printf("%3.0f",KDXCurrency::convert($arProfileParams['PRICE']['VALUE'],KDXCurrency::$CurrentCurrency))?> <?=KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?>
                                        </span>

                                        <span class="header-nav__dd-right">
                                        <?=GetMessage('DELIVERY')?> <?=LANGUAGE_ID == 'en' ? declension($arProfileParams['PRICE']['TRANSIT'], 'days','days','days') : declension($arProfileParams['PRICE']['TRANSIT'], 'день','дня','дней')?>
                                            <?=$arDelivery['DESCRIPTION']?></span>
                                    </div>
                                </li>
                            <?}
                        }
                        ?>
                        <li><a class="link link--primary link--bold header-nav__dd-item" href="<?=$arItem['item']['LINK']?>"><?=GetMessage('LEARN_MORE')?></a></li>
                    </ul>
                <?} ?>
                </div>
                <?break;
            case 2:?>
                <?//if($type=='desktop'){?>
                    <li>
                        <a class="link link--primary header-nav__dd-item<?if($arItem['item']['SELECTED'] == '1'){?> link--active<?}?>" href="<?=$arItem['item']['LINK']?>"><?=$arItem['item']['TEXT'];?></a>
                    </li>
                <?//}?>
                <?break;
            case 3:?>
                <li>
                    <a class="link link--primary header-nav__dd-item<?if($arItem['item']['SELECTED'] == '1'){?> link--active<?}?>" href="<?=$arItem['item']['DETAIL_PAGE_URL']?>">
                        <?=LANGUAGE_ID == 'en' ? $arItem['item']['PROPERTY_EN_NAME_VALUE'] : $arItem['item']['NAME'];?>
                    </a>
                </li>
                <?break;
            default:
                break;
        }
    }
}?>
<?if (!empty($arResult)){?>
    <div class="header__center">
        <nav class="header-nav">
            <?foreach($arResult as $key => $arItem){
                $current_depth = $arItem['item']['PARAMS']['DEPTH_LEVEL']?:$arItem['item']['DEPTH_LEVEL'];
                drawMenuItem($arItem, $current_depth);
            }?>
        </nav>
    </div>
<?}?>
