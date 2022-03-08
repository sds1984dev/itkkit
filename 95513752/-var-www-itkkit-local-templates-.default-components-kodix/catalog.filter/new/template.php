<?php
/**
 * Created by:  KODIX 17.03.2015 11:22
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
// echo "<pre>";
// print_r($arResult);
// echo "</pre>";die();
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
$min = intval($arResult['PRICE_VALUES']['MIN']);
$max = ceil($arResult['PRICE_VALUES']['MAX']);

$jsValueMin = intval($arResult['FILTRATED_VALUES']['PRICE']['MIN']) ?: $min;
$jsValueMax = ceil($arResult['FILTRATED_VALUES']['PRICE']['MAX']) ?: $max;

$valueMin = $arResult['FILTRATED_VALUES']['PRICE']['MIN'] ?: '';
$valueMax = $arResult['FILTRATED_VALUES']['PRICE']['MAX'] ?: '';

$scale = intval(1 / $arResult['CURRENCY']['AMOUNT']);

$showClearBtn = count($arResult['FILTRATED_VALUES']);
if ($showClearBtn) {
    $showClearBtn = false;
    foreach ($arResult['FILTRATED_VALUES']['PROP'] as $code => $val) {
        if (array_key_exists($code, $arResult['PROPERTIES']) && $arResult['PROPERTIES'][$code]['PROPERTY_TYPE'] != 'N') {
            $showClearBtn = true;
            break;
        }
        if (array_key_exists($code, $arResult['PROPERTY_VALUES']) &&
            ($arResult['PROPERTY_VALUES'][$code]['MIN'] != $val['MIN'] || $arResult['PROPERTY_VALUES'][$code]['MAX'] != $val['MAX'])) {
            $showClearBtn = true;
            break;
        }
    }
}

?>
<? if (isAjax() && !isRestoreHistory('ALL')) { ?>
    <? if (intval($arResult['FILTER'])) { ?>
        <span class="ajax_remove_in_new_before_append"
              data-href="<?= $APPLICATION->GetCurPageParam('FILTER=' . intval($arResult['FILTER']), array('FILTER')) ?>"
              id="new_addres_push"></span>
    <? } else { ?>
        <span class="ajax_remove_in_new_before_append"
              data-href="<?= $APPLICATION->GetCurPageParam('', array('FILTER')) ?>"
              id="new_addres_push"></span>
    <? } ?>
<? } ?>
<? if ((!isAjax() || isRestoreHistory('ALL')) || intval($arResult['FILTER'])) { ?>
    <? if (intval($arResult['FILTER'])) { ?>
        <span class="ajax_remove_in_new_before_append"
              data-href="<?= $APPLICATION->GetCurPageParam('FILTER=' . intval($arResult['FILTER']), array('FILTER')) ?>"
              id="new_addres_push"></span>
    <? } else { ?>
        <span class="ajax_remove_in_new_before_append"
              data-href="<?= $APPLICATION->GetCurPageParam('', array('FILTER')) ?>"
              id="new_addres_push"></span>
    <? } ?>
<? } ?>
<? if ((!isAjax() || isRestoreHistory('ALL')) || intval($arResult['FILTER'])) { ?>
    <?$curPage = explode('?', $_SERVER['REQUEST_URI'], 2)[0];?>
    <form action="<?= $APPLICATION->GetCurPageParam('', array('FILTER')) ?>"
          name="form_brand"
          method="post"
          id="catalog_filter"
          class="ajax_load push_history ajax_remove_in_new_before_append"
          data-ajax-response-wrapper="#catalog">
        <div class="grid-row">
            <? if ($arParams['FROM_SEARCH'] == 'Y') { ?>
                <? $APPLICATION->IncludeComponent(
                    "bitrix:search.form",
                    "new_catalog_search",
                    array(
                        "PAGE" => "#SITE_DIR#search/"
                    ),
                    false
                ); ?>
            <? } else { ?>
                <div class="col-md-6">
                    <h1><?= $APPLICATION->ShowTitle(false) ?></h1>
                    <? //$APPLICATION->ShowProperty("description")?>
                    <? $APPLICATION->ShowViewContent('SECTION_DESCR') ?>
                </div>
            <? } ?>
            <div class="<? if ($arParams['FROM_SEARCH'] != 'Y') { ?>col-md-6<? } else { ?>col-md-4<? } ?>">
                <div class="catalog-filter__open ninja--tm ninja--sm" 
                     style="
                            margin-top: -5px;
                            -webkit-box-pack: end;
                            justify-content: flex-end;
                            display: flex;
                        ">
                <!--<div>-->
                    <a class="link catalog-filter__open__close js-catalog-filter-clear<?=isset($_GET['FILTER']) && $_GET['FILTER'] !== '' ? ' _active' : ''?>" href="<?=$curPage?>" title="<?=LANGUAGE_ID == 'en' ? 'Clear filter' : 'Очистить фильтр';?>">
                        <?if (isset($_GET['FILTER']) && $_GET['FILTER'] !== ''){?>
                            <svg class="icon icon-cross_pop-up"><use xlink:href="<?=SITE_TEMPLATE_PATH?>/resources/svg/app.svg#cross_pop-up"></use></svg>
                        <?}?>
                    </a>
                    <a class="link link--lg js_catalog-filter__open js-catalog-filter-open<?=isset($_GET['FILTER']) && $_GET['FILTER'] !== '' ? ' _active' : '';?>" href="#"><?= GetMessage("FILTER_TITLE") ?></a>
                </div>
                <div class="section-bar" style="display: none">
                    <div class="sort">
                      <div class="sort-btn js-btn-sort"><?=LANGUAGE_ID == 'en' ? 'Sort by' : 'Сортировка';?></div>
                      <ul class="sort-wrap js-wrap-sort">
                        <li><a href="#"<?=$sort == 'sort_date' ? ' class="_active"' : '';?> data-type="sort_date" data-order="desc"><?=LANGUAGE_ID == 'en' ? 'New in' : 'По умолчанию'?></a></li>
                        <li><a href="#"<?=$sort == 'low_price' ? ' class="_active"' : '';?> data-type="low_price" data-order="asc"><?=LANGUAGE_ID == 'en' ? 'Price (Low)' : 'Возрастанию цены'?></a></li>
                        <li><a href="#"<?=$sort == 'high_price' ? ' class="_active"' : '';?> data-type="high_price" data-order="desc"><?=LANGUAGE_ID == 'en' ? 'Price (High)' : 'Убыванию цены'?></a></li>
                      </ul>
                    </div>
                  </div>
                <?/*<div class="ninja--tm ninja--sm"
                     <? if (!$showClearBtn){ ?>style="display: none"<? } ?>>
                    <div class="catalog-filter__use-list">
                        <? foreach ($arResult['FILTRATED_VALUES'] as $type => $props) { ?>
                            <? if ($type == 'PROP') {
                                foreach ($props as $propCode => $propValues) {
                                    if (strpos($propCode, 'PRICE_MIN'))
                                        continue;
                                    if ($propCode == 'KDX_BRAND' && empty($arResult['PROPERTIES'][$propCode]['VALUES'])) {
                                        $arResult['PROPERTIES'][$propCode]['VALUES'] = KDXSaleDataCollector::getBrands();
                                    } ?>
                                    <div class="catalog-filter__use-block">
                                        <span class="heading--h5"><?= GetMessage('FILTER_NAME_' . $propCode) ?>:&nbsp;</span>
                                        <? foreach ($propValues as $value_id) {
                                            ?>
                                            <span class="catalog-filter__use-item-wrapper">
                                        <a class="link catalog-filter__use-item filter__remove-link js_reset_property"
                                           data-prop-code="<?= $propCode ?>"
                                           data-id="<?= $value_id ?>">
                                            <?= ($arResult['PROPERTIES'][$propCode]['VALUES'][$value_id]['NAME']) ?: $value_id; ?>
                                            <svg class="icon icon-cross_pop-up">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                     xlink:href="resources/svg/app.svg#cross_pop-up"></use>
                                            </svg>
                                        </a>
                                    </span>
                                        <? } ?>
                                    </div>
                                <? } ?>
                            <? } ?>
                        <? } ?>
                        <a class="link link--primary link--bold js_catalog-filter__clear kdx_clear_filter"><?= GetMessage('FILTER_RESET') ?></a>
                    </div>
                </div>*/?>
            </div>
        </div>
        <div class="catalog-filter__wrapper">
            <div class="catalog-filter__open ninja--md ninja--lg ninja--xl">
                <a class="link link--lg js_catalog-filter__open<?=isset($_GET['FILTER']) && $_GET['FILTER'] !== '' ? ' _active' : '';?>"
                   href="#"><?= GetMessage("FILTER_TITLE") ?></a>
                <div class="catalog-filter__view">
                    <div class="catalog-filter__view-item js_catalog-view">
                        <svg class="icon icon-list1">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/resources/svg/app.svg#list1"></use>
                        </svg>
                    </div>
                    <div class="catalog-filter__view-item js_catalog-view js_catalog-view-compact catalog-filter__view-item--active">
                        <svg class="icon icon-list2">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/resources/svg/app.svg#list2"></use>
                        </svg>
                    </div>
                </div>
                <div class="section-bar section-bar-mobile">
                    <div class="sort">
                      <div class="sort-btn js-btn-sort"><?=LANGUAGE_ID == 'en' ? 'Sort by' : 'Сортировка';?></div>
                      <ul class="sort-wrap js-wrap-sort">
                        <li><a href="#"<?=$sort == 'sort_date' ? ' class="_active"' : '';?> data-type="sort_date" data-order="desc"><?=LANGUAGE_ID == 'en' ? 'New in' : 'По умолчанию'?></a></li>
                        <li><a href="#"<?=$sort == 'low_price' ? ' class="_active"' : '';?> data-type="low_price" data-order="asc"><?=LANGUAGE_ID == 'en' ? 'Price (Low)' : 'Возрастанию цены'?></a></li>
                        <li><a href="#"<?=$sort == 'high_price' ? ' class="_active"' : '';?> data-type="high_price" data-order="desc"><?=LANGUAGE_ID == 'en' ? 'Price (High)' : 'Убыванию цены'?></a></li>
                      </ul>
                    </div>
                </div>
            </div>
            <div class="catalog-filter" style="display: none;">
                <div class="grid-container">
                    <div class="catalog-filter__content">
                        <a class="link link--lg catalog-filter__close js_catalog-filter__close"
                           href="#"><?= GetMessage("FILTER_CLOSE") ?></a>
                        <div class="grid-row">  <?
                            $prop = $arResult['PROPERTIES']['CML2_MANUFACTURER'];
                            if (count($arResult['PROPERTY_VALUES'][$prop['CODE']]) > 1) { ?>
                                <div class="col-xl-3 col-lg-4 col-md-4 col-xs-12 col-padding catalog-filter--brands">
                                    <div class="catalog-filter__accordion accordion">
                                        <div class="accordion__item js-accordion__item">
                                            <div class="heading--h5 catalog-filter__heading ninja--sm ninja--tm">
                                                <?= GetMessage('FILTER_NAME_' . $prop['CODE']) ?>
                                            </div>
                                            <div class="accordion__title js-accordion__title ninja--md ninja--lg ninja--xl">
                                                <?= GetMessage('FILTER_NAME_' . $prop['CODE']) ?>
                                            </div>
                                            <div class="accordion__content accordion__content--visible-md js-accordion__content"
                                                 data-prop="<?= $prop['CODE']; ?>">
                                                <div class="catalog-filter__list catalog-filter__list--medium customscroll js_customscroll">
                                                    <ul class="ul--reset ul--reset-list">
                                                        <?
                                                        foreach ($prop['VALUES'] as $val_id => $val) {
                                                            if (isset($arResult['PROPERTY_VALUES'][$prop['CODE']][$val_id])) {
                                                                ?>
                                                                <li class="catalog-filter__list-item">
                                                                    <label for="check_<?= str_replace(' ', '-', $prop['CODE'] . $val_id) ?>"
                                                                           class="checkbox">
                                                                        <input type="checkbox"
                                                                               name="FILTER[PROP][<?= $prop['CODE'] ?>][]"
                                                                               data-val-id="<?= $val_id; ?>"
                                                                               value="<?= $val_id ?>"
                                                                               id="check_<?= str_replace(' ', '-', $prop['CODE'] . $val_id) ?>"
                                                                               class="checkbox__input" <?
                                                                        if (in_array($val_id, $arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']])) {
                                                                            echo 'checked="checked" ';
                                                                        } ?>>
                                                                        <span class="checkbox__icon"></span>
                                                                        <span class="checkbox__label">
                                                                            <span><?= $val['NAME'] ?></span>
                                                                        </span>
                                                                    </label>
                                                                </li>
                                                            <?
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <? } ?>
                            <? if (is_array($arResult['CHILDREN_SECTIONS']) && count($arResult['CHILDREN_SECTIONS'])) { ?>
                                <div class="col-xl-3 col-lg-4 col-md-4 col-xs-12 col-padding catalog-filter--sections">
                                    <div class="catalog-filter__accordion accordion">
                                        <? foreach ($arResult['CHILDREN_SECTIONS'] as $arSection) { ?>
                                            <? if ($arSection['CNT'] == 0) continue;
                                            $needShow = $arSection['CODE'] == 'clothing' ? true : false; ?>
                                            <div class="accordion__item js-accordion__item <? if ($needShow) { ?>accordion__item--opened<? } ?>">
                                                <div class="accordion__title js-accordion__title"><?= LANGUAGE_ID == 'en' ? $arSection['UF_EN_NAME'] : $arSection['NAME'] ?></div>
                                                <div class="accordion__content js-accordion__content">
                                                    <div class="catalog-filter__list catalog-filter__list--short customscroll js_customscroll">
                                                        <ul class="ul--reset ul--reset-list">
                                                            <? foreach ($arSection['SUBSECTIONS'] as $arSubSection) {
                                                                if ($arSubSection['CNT'] == 0) continue;
                                                                $checked = in_array($arSubSection['ID'], $arResult['FILTRATED_VALUES']['SECTION'], true);
                                                                ?>
                                                                <li class="catalog-filter__list-item">
                                                                    <label for="check_<?= str_replace(' ', '-', $arSubSection['CODE'] . $arSubSection['ID']) ?>"
                                                                           class="checkbox">
                                                                        <input type="checkbox"
                                                                               name="FILTER[SECTION][]"
                                                                               data-val-id="<?= $val_id; ?>"
                                                                               value="<?= $arSubSection['ID'] ?>"
                                                                               id="check_<?= str_replace(' ', '-', $arSubSection['CODE'] . $arSubSection['ID']) ?>"
                                                                               class="checkbox__input" <? if ($checked) {
                                                                            echo 'checked="checked" ';
                                                                        } ?>>
                                                                        <span class="checkbox__icon"></span>
                                                                        <span class="checkbox__label">
                                                                            <span><?= LANGUAGE_ID == 'en' ? $arSubSection['UF_EN_NAME'] : $arSubSection['NAME'] ?></span>
                                                                        </span>
                                                                    </label>
                                                                </li>
                                                            <? } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <? } ?>
                                    </div>
                                </div>
                            <? } ?>
                            <? foreach ($arResult['PROPERTIES'] as $prop) {
                                if ($prop['CODE'] == 'SELECTIONS' || $prop['CODE'] == 'CML2_MANUFACTURER' || $prop['CODE'] == 'SIZES_TRAINERS' || $prop['CODE'] == 'SIZES_CLOTHING') continue;
                                if (count($arResult['PROPERTY_VALUES'][$prop['CODE']]) > 1) {
                                    $needShow = count($arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']]);
                                    if ($prop['CODE'] == 'COLORS'){?>
                                        <div class="col-xl-3 col-lg-4 col-md-4 col-xs-12 col-padding catalog-filter--colors">
                                            <div class="catalog-filter__accordion accordion">
                                                <?
                                                switch ($prop['PROPERTY_TYPE']) {
                                                    case 'E':
                                                    case 'S': ?>
                                                        <div class="accordion__item js-accordion__item">
                                                            <div class="heading--h5 catalog-filter__heading ninja--sm ninja--tm">
                                                                <?=LANGUAGE_ID == 'en' ? 'Colors' : 'Цвет';?>
                                                            </div>
                                                            <div class="accordion__title js-accordion__title ninja--md ninja--lg ninja--xl">
                                                                <?=LANGUAGE_ID == 'en' ? 'Colors' : 'Цвет';?>
                                                            </div>
                                                            <div class="accordion__content accordion__content--visible-md js-accordion__content" data-prop="<?= $prop['CODE']; ?>">
                                                                <div class="catalog-filter__list catalog-filter__list--medium customscroll js_customscroll">
                                                                    <ul class="ul--reset ul--reset-list">
                                                                        <?
                                                                        foreach ($arResult['PROPERTY_VALUES'][$prop['CODE']] as $val_id => $val) {
                                                                            if (trim($val_id)) {
                                                                                ?>
                                                                                <li class="catalog-filter__list-item">
                                                                                    <label for="check_<?= str_replace(' ', '-', $prop['CODE'] . $val_id) ?>"
                                                                                           class="checkbox">
                                                                                        <input type="checkbox"
                                                                                               name="FILTER[PROP][<?= $prop['CODE'] ?>][]"
                                                                                               data-val-id="<?= $val_id; ?>"
                                                                                               value="<?= $val_id ?>"
                                                                                               id="check_<?= str_replace(' ', '-', $prop['CODE'] . $val_id) ?>"
                                                                                               class="checkbox__input" <?
                                                                                        if (in_array($val_id, $arResult['FILTRATED_VALUES']['PROP'][$prop['CODE']])) {
                                                                                            echo 'checked="checked" ';
                                                                                        } ?>>
                                                                                        <span class="checkbox__icon"></span>
                                                                                        <span class="checkbox__label">
                                                                                            <span><?= $val_id == "ONE_SIZE" ? "OS" : $val_id ?></span>
                                                                                        </span>
                                                                                    </label>
                                                                                </li>
                                                                            <?
                                                                            }
                                                                        } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?
                                                        break;
                                                    case 'P': //Подборки

                                                        break;
                                                        ?>

                                                    <?
                                                } ?>
                                            </div>
                                        </div>
                                    <?}
                                }
                            } ?>
                            <div class="col-xl-3 col-lg-12 col-md-12 col-xs-12 catalog-filter--prices">
                                <? if (intval($arResult['PRICE_VALUES']['MIN']) < intval($arResult['PRICE_VALUES']['MAX'])) { ?>
                                    <div class="catalog-filter__range-wrapper"></div>
                                    <div class="heading--h5 catalog-filter__heading"><?= GetMessage('FILTER_NAME_PRICE') ?></div>
                                    <div class="range-wrapper js_range-wrapper js_slider_wrapper">
                                        <? if ($scale > 0) {
                                            $degree = intval(log10(($scale)));
                                            $MAX = $arResult['PRICE_VALUES']['MAX'];
                                            $arResult['PRICE_VALUES']['MAX'] = round($MAX, -1 * $degree);
                                            $powed = pow(10, $degree);
                                            $arResult['PRICE_VALUES']['MIN'] = floor($arResult['PRICE_VALUES']['MIN'] / $powed) * ($powed);
                                            if ($MAX > $arResult['PRICE_VALUES']['MAX']) {
                                                $arResult['PRICE_VALUES']['MAX'] += $powed;
                                            }
                                        }
                                        ?>
                                        <div class="range-wrapper__connector">
                                            <div class="sliderConnector"
                                                 data-code="price"></div>
                                            <div id="priceSlider"
                                                 data-min-val="<?= $arResult['PRICE_VALUES']['MIN'] ?>"
                                                 data-max-val="<?= $arResult['PRICE_VALUES']['MAX'] ?>"
                                                 data-step="<?= $scale ?>"></div>
                                        </div>
                                        <div class="range-wrapper__fields">
                                            <div class="grid-row">
                                                <div class="col-sm-6">
                                                    <div class="range-wrapper__fields--left">
                                                        <input class="form-input range-wrapper__fields-input"
                                                               id="price-from"
                                                               type="text"
                                                               name="FILTER[PRICE][MIN]"
                                                               value="<?= $arResult['FILTRATED_VALUES']['PRICE']['MIN'] ?: $jsValueMin ?>">
                                                        <span class="range-wrapper__currency"><?= trim(str_replace('#', '', $arResult['CURRENCY']['FORMAT_STRING'])) ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="range-wrapper__fields--right">
                                                        <input class="form-input range-wrapper__fields-input"
                                                               id="price-to"
                                                               type="text"
                                                               name="FILTER[PRICE][MAX]"
                                                               value="<?= $arResult['FILTRATED_VALUES']['PRICE']['MAX'] ?: $jsValueMax ?>">
                                                        <span class="range-wrapper__currency"><?= trim(str_replace('#', '', $arResult['CURRENCY']['FORMAT_STRING'])) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input id="amount_1"
                                               type="text"
                                               readonly=""
                                               name="money_man"
                                               class="range_inp hidden">

                                    </div>
                                <? } ?>
                                <div class="catalog-filter__link">
                                    <button class="btn btn--primary"
                                            type="submit"><?= GetMessage('FILTER_SET') ?></button>
                                </div>
                                <div class="catalog-filter__link">
                                    <a class="link link--secondary<?/* js_catalog-filter__clear kdx_clear_filter*/?>" href="<?=$curPage?>"><?= GetMessage('FILTER_RESET') ?></a>
                                </div>
                            </div>

                            <?
                            $page = $APPLICATION->GetCurPage(true);
                            if (strstr($page, '/footwear/') || strstr($page, '/accessories/')){
                                unset($arResult['PROPERTIES']['SIZES_CLOTHING']);
                            }
                            if (strstr($page, '/clothing/')){
                                unset($arResult['PROPERTIES']['SIZES_TRAINERS']);
                            }
                            if (count($arResult['PROPERTY_VALUES']['SIZES_CLOTHING']) > 1) {
                                $needShow = count($arResult['FILTRATED_VALUES']['PROP']['SIZES_CLOTHING']);?>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-xs-12 catalog-filter--size catalog-filter--size2">
                                    <div class="catalog-filter__size-wrapper">
                                        <div class="accordion">
                                            <div class="accordion__item js-accordion__item">
                                                <div class="heading--h5 catalog-filter__heading ninja--sm ninja--tm">
                                                    <?= GetMessage('FILTER_NAME_SIZES_CLOTHING') ?>
                                                </div>
                                                <div class="accordion__title js-accordion__title ninja--md ninja--lg ninja--xl">
                                                    <?= GetMessage('FILTER_NAME_SIZES_CLOTHING') ?>
                                                </div>
                                                <div class="accordion__content accordion__content--visible-md js-accordion__content"
                                                     <?
                                                     if ($needShow){ ?>style="display: block"<?
                                                } ?>
                                                     data-prop="SIZES_CLOTHING_TABLE">
                                                    <div class="catalog-filter__size mod_1">
                                                        <?foreach ($arResult['SIZES_CLOTHING_TABLE'] as $size => $val) {
                                                            if (trim($size)) {?>
                                                                <div class="catalog-filter__size-item">
                                                                    <label class="checkbox js-clothing-select">
                                                                        <?foreach ($val as $v){?>
                                                                            <input type="checkbox" data-val-id="<?= $size; ?>" name="FILTER[PROP][SIZES_CLOTHING][]" value="<?=$v?>" class="checkbox__input" <?if (in_array($size, $arResult['FILTRATED_VALUES']['PROP']['SIZES_CLOTHING'])) { echo 'checked="checked" '; }?>>
                                                                        <?}?>
                                                                        <span class="checkbox__icon"></span>
                                                                        <span class="checkbox__label">
                                                                            <?= $size == "ONE_SIZE" ? "OS" : $size ?>
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            <?}
                                                        }?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?}?>
                            <?if (count($arResult['PROPERTY_VALUES']['SIZES_TRAINERS']) > 1) {
                                $needShow = count($arResult['FILTRATED_VALUES']['PROP']['SIZES_TRAINERS']);?>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-xs-12 catalog-filter--size">
                                    <div class="catalog-filter__size-wrapper">
                                        <div class="accordion">
                                            <div class="accordion__item js-accordion__item">
                                                <?
                                                $curSize = 'US';
                                                if (isset($_COOKIE['filterShoesSize']) && $_COOKIE['filterShoesSize'] !== ''){
                                                    $curSize = $_COOKIE['filterShoesSize'];
                                                }
                                                ?>
                                                <div class="heading--h5 catalog-filter__heading ninja--sm ninja--tm">
                                                    <?= GetMessage('FILTER_NAME_SIZES_TRAINERS') ?>
                                                    <?if (!empty($arResult['SIZES_EU_TRAINERS'])){?>
                                                        <div class="catalog-filter__tabs">
                                                            <a class="js-filter-btn <?=$curSize == 'US' ? '_active' : ''?>" href="#filterShoesUS" data-size="US">US</a>
                                                            <span>/</span>
                                                            <a class="js-filter-btn <?=$curSize == 'EU' ? '_active' : ''?>" href="#filterShoesEU" data-size="EU">EU</a>
                                                        </div>
                                                    <?}?>
                                                </div>
                                                <div class="accordion__title js-accordion__title ninja--md ninja--lg ninja--xl">
                                                    <?= GetMessage('FILTER_NAME_SIZES_TRAINERS') ?>
                                                    <?if (!empty($arResult['SIZES_EU_TRAINERS'])){?>
                                                        <div class="catalog-filter__tabs">
                                                            <a class="js-filter-btn <?=$curSize == 'US' ? '_active' : ''?>" href="#filterShoesUS" data-size="US">US</a>
                                                            <span>/</span>
                                                            <a class="js-filter-btn <?=$curSize == 'EU' ? '_active' : ''?>" href="#filterShoesEU" data-size="EU">EU</a>
                                                        </div>
                                                    <?}?>
                                                </div>
                                                <div class="accordion__content accordion__content--visible-md js-accordion__content" <?if ($needShow){ ?>style="display: block"<?}?> data-prop="SIZES_TRAINERS">
                                                    <div class="catalog-filter__size <?=$curSize !== 'US' ? '_hide' : ''?> js-filter-wrap mod_1" id="filterShoesUS">
                                                        <?foreach ($arResult['SIZES_US_TRAINERS'] as $val){
                                                            if (trim($val)){?>
                                                                <div class="catalog-filter__size-item">
                                                                    <label for="check_<?= str_replace(' ', '-', 'SIZES_TRAINERS' . $val) ?>"
                                                                           class="checkbox">
                                                                        <input type="checkbox"
                                                                               data-val-id="<?= $val; ?>"
                                                                               name="FILTER[PROP][SIZES_TRAINERS][]"
                                                                               value="<?= $val ?>"
                                                                               id="check_<?= str_replace(' ', '-', 'SIZES_TRAINERS' . $val) ?>"
                                                                               class="checkbox__input"
                                                                            <?
                                                                            if (in_array($val, $arResult['FILTRATED_VALUES']['PROP']['SIZES_TRAINERS'])) {
                                                                                echo 'checked="checked" ';
                                                                            } ?>
                                                                        >
                                                                        <span class="checkbox__icon"></span>
                                                                        <span class="checkbox__label">
                                                                            <?= $val == "ONE_SIZE" ? "OS" : $val ?>
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            <?}
                                                        }?>
                                                    </div>
                                                    <?if (!empty($arResult['SIZES_EU_TRAINERS'])){?>
                                                        <div class="catalog-filter__size <?=$curSize !== 'EU' ? '_hide' : ''?> js-filter-wrap mod_1" id="filterShoesEU">
                                                            <?foreach ($arResult['SIZES_EU_TRAINERS'] as $val) {
                                                                if (trim($val)) {
                                                                    ?>
                                                                    <div class="catalog-filter__size-item">
                                                                        <label for="check_<?= str_replace(' ', '-', 'SIZES_TRAINERS_EU' . $val) ?>"
                                                                               class="checkbox">
                                                                            <input type="checkbox"
                                                                                   data-val-id="<?= $val; ?>"
                                                                                   name="FILTER[PROP][SIZES_TRAINERS_EU][]"
                                                                                   value="<?= $val ?>"
                                                                                   id="check_<?= str_replace(' ', '-', 'SIZES_TRAINERS_EU' . $val) ?>"
                                                                                   class="checkbox__input"
                                                                                <?
                                                                                if (in_array($val, $arResult['FILTRATED_VALUES']['PROP']['SIZES_TRAINERS_EU'])) {
                                                                                    echo 'checked="checked" ';
                                                                                } ?>
                                                                            >
                                                                            <span class="checkbox__icon"></span>
                                                                            <span class="checkbox__label">
                                                                                <?= $val == "ONE_SIZE" ? "OS" : $val ?>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                <?
                                                                }
                                                            } ?>
                                                        </div>
                                                    <?}?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?}?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<? } ?>

<script>
    $('#catalog_filter').find('.sliderConnector').each(function () {
        if (this.noUiSlider) {
            return;
        }
        let Slider = this;
        let code = $(Slider).attr('data-code');
        let elRange = $('#' + code + 'Slider').eq(0);
        let rangeMin = parseInt(elRange.attr('data-min-val'));
        let rangeMax = parseInt(elRange.attr('data-max-val'));
        let rangeStep = parseInt(elRange.attr('data-step'));
        let parent = $(Slider).closest('.js_range-wrapper');
        let customMin = parent.find('#' + code + '-from').eq(0);
        let customMax = parent.find('#' + code + '-to').eq(0);
        noUiSlider.create(Slider, {
            start: [<?= $arResult['FILTRATED_VALUES']['PRICE']['MIN'] ?: $jsValueMin ?>, <?= $arResult['FILTRATED_VALUES']['PRICE']['MAX'] ?: $jsValueMax ?>],
            connect: true,
            behaviour: 'tap',
            range: {
                'min': rangeMin,
                'max': rangeMax
            },
            step: rangeStep,
            margin: rangeStep
        });

        Slider.noUiSlider.on('update', function (values, handle) {
            handle ? customMax.val(parseInt(values[handle])) : customMin.val(parseInt(values[handle]));
        });

        $(document).on('change', '#' + code + '-from', function () {
            Slider.noUiSlider.set([this.value, null]);
        });
        $(document).on('change', '#' + code + '-to', function () {
            Slider.noUiSlider.set([null, this.value]);
        });

        $(this).addClass('sliderInited');
    });
</script>
