<?php
/**
 * Created by:  KODIX 20.03.2015 16:24
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if(!empty($arResult['ITEMS'])){?>
    <?
    $arrCountries = getHlCountries();
    $useVAT = $arrCountries[$_SESSION['LAST_COUNTRY']]['UF_USE_VAT'];
    ?>
    <?if($arParams['NOT_SHOW_SEPARATOR'] != 'Y'){?>
        <div class="separator_v2"></div>
    <?}?>
        <div class="grid-row">
            <?foreach($arResult['ITEMS'] as $arProduct){

                $params = array('width'=>352,'height'=>320);
                if(is_array($arProduct['PROPERTY_GALLERY_VALUE']) && !empty($arProduct['PROPERTY_GALLERY_VALUE'])){
                    $img_id = current($arProduct['PROPERTY_GALLERY_VALUE']);
                    $img = CFile::ResizeImageGet($img_id,array('width'=>$params['width'],'height'=>$params['height']),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $img_2x = CFile::ResizeImageGet($img_id,array('width'=>$params['width']*2,'height'=>$params['height']*2),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $img_mob = CFile::ResizeImageGet($img_id,array('width'=>288,'height'=>302),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $img_mob_2x = CFile::ResizeImageGet($img_id ,array('width'=>576,'height'=>604),BX_RESIZE_IMAGE_PROPORTIONAL);

                    $img2_id = next($arProduct['PROPERTY_GALLERY_VALUE']);
                    $img2 = CFile::ResizeImageGet($img2_id,array('width'=>$params['width'],'height'=>$params['height']),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $img2_2x = CFile::ResizeImageGet($img2_id,array('width'=>$params['width']*2,'height'=>$params['height']*2),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $img2_mob = CFile::ResizeImageGet($img2_id,array('width'=>288,'height'=>302),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $img2_mob_2x = CFile::ResizeImageGet($img2_id,array('width'=>576,'height'=>604),BX_RESIZE_IMAGE_PROPORTIONAL);
                }
                else{
                    $img = CFile::ResizeImageGet($arProduct['~DETAIL_PICTURE'],array('width'=>$params['width'],'height'=>$params['height']),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $img_2x = CFile::ResizeImageGet($arProduct['~DETAIL_PICTURE'],array('width'=>$params['width']*2,'height'=>$params['height']*2),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $img_mob = CFile::ResizeImageGet($arProduct['~DETAIL_PICTURE'],array('width'=>288,'height'=>302),BX_RESIZE_IMAGE_PROPORTIONAL);
                    $img_mob_2x = CFile::ResizeImageGet($arProduct['~DETAIL_PICTURE'] ,array('width'=>576,'height'=>604),BX_RESIZE_IMAGE_PROPORTIONAL);
                }

                $haveExtraImg = false;
                $aloneClass = 'catalog-item__img-top--alone';
                if(is_array($img2) && !empty($img2) && $arProduct['PROPERTY_NOT_SHOW_SCND_PHOTO_VALUE'] != 'Y'){
                    $haveExtraImg = true;
                    $aloneClass = '';
                }
                ?>

                <div class="col-xl-3 col-lg-4 col-md-4 col-tm-6 col-sm-12">
                    <a class="catalog-item link link--primary" href="<?= $arProduct['DETAIL_PAGE_URL'] ?>">

                        <div class="catalog-item__img-wrapper<?if(!$haveExtraImg){?> catalog-item__img-wrapper--single<?}?>"
                             style="<?= $APPLICATION->ShowProperty('SELECTION_PRODUCT_STYLE', '') ?>">
                            <div class="catalog-item__img-list">
                                <div class="catalog-item__img catalog-item__img--active <?= $aloneClass ?>">
                                    <picture>
                                        <?/*<source srcset="<?= $img_mob['src'] ?> 1x, <?= $img_mob_2x['src'] ?> 2x"
                                                media="(max-width: 767px)">
                                        <source srcset="<?= $img['src'] ?> 1x, <?= $img_2x['src'] ?> 2x">*/?>
                                        <img class="lazyload img--lazyload"
                                             src=""
                                             data-src="<?= $img['src'] ?>"
                                             alt="<?= $arProduct['NAME'] ?>"
                                             data-object-fit="contain"
                                             data-lazy="<?= $img['src'] ?>"
                                        >
                                    </picture>
                                </div>
                                <? if ($haveExtraImg) { ?>
                                    <div class="catalog-item__img">
                                        <picture>
                                            <?/*<source srcset="<?= $img2_mob['src'] ?> 1x, <?= $img2_mob_2x['src'] ?> 2x"
                                                    media="(max-width: 767px)">
                                            <source srcset="<?= $img2['src'] ?> 1x, <?= $img2_2x['src'] ?> 2x">*/?>
                                            <img class="lazyload img--lazyload"
                                                 src=""
                                                 data-src="<?= $img2['src'] ?>"
                                                 alt="<?= $arProduct['NAME'] ?>"
                                                 data-object-fit="contain"
                                                 data-lazy="<?= $img2['src'] ?>"
                                            >
                                        </picture>
                                    </div>
                                <? } ?>
                            </div>
                            <div class="catalog-item__img-arrow catalog-item__img-arrow--prev js-catalog-item-arrow-prev"></div>
                            <div class="catalog-item__img-arrow catalog-item__img-arrow--next js-catalog-item-arrow-next"></div>
                            <? if (count($arProduct["PROPERTY_SIZES_VALUE"]) && reset($arProduct["PROPERTY_SIZES_VALUE"]) != "ONE_SIZE") { 
									global $USER;
									//Show sizes only employee (1,6,7)
									$allowed_groups=array(1,6,7);
									$user_id=$USER->GetID();
									$user_groups=CUser::GetUserGroup($user_id);
									$cross_groups=array_uintersect($user_groups,$allowed_groups,"strcasecmp");
									if (count($cross_groups)>0) {?>
										<div class="catalog-item__img-hover">
											<? usort($arProduct["PROPERTY_SIZES_VALUE"], 'KDXDataCollector::sortSizes'); ?>

											<? $str = ''; ?>
											<? foreach ($arProduct['PROPERTY_SIZES_VALUE'] as $size) { ?>
												<? if (in_array($size, KDXSettings::getSetting('NOT_SHOW_SIZES'))) {
													continue;
												} ?>
												<? $str .= $size . ' '; ?>
											<? } ?>
											<div><?=trim($str) ?></div>
										</div>
									<?}?>
                            <? } ?>
                        </div>
                        <?
                        $brandName = '';
                        $resBrand = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>3, 'ID'=>$arProduct['PROPERTY_CML2_MANUFACTURER_VALUE'][0]), false, array(), array('NAME'));
                        if ($arBrand = $resBrand->Fetch()){
                            $brandName = $arBrand['NAME'];
                        }
                        ?>
                        <div class="catalog-item__title">
                            <div class="catalog-item__brand"><?= $brandName ?></div>
                            <div class="catalog-item__title-name"><?= str_ireplace($brandName . ' ', '', $arProduct["NAME"]) ?></div>
                        </div>

                        <div class="catalog-item__price">
                            <div class="price__wrapper">
                                <div class="price price--has-discount">
                                    <?
                                    $arrCountries = empty($params['countries']) ? getHlCountries() : $params['countries'];
                                    $useVAT = $arrCountries[$_SESSION['LAST_COUNTRY']]['UF_USE_VAT'];
                                    ?>

                                    <? if ($arProduct['PROPERTY_BASE_PRICE_MIN_VALUE'] > $arProduct['PROPERTY_RETAIL_PRICE_MIN_VALUE']) { ?>
                                        <?
                                        $saleValuePercent = round(($arProduct['PROPERTY_BASE_PRICE_MIN_VALUE'] - $arProduct['PROPERTY_RETAIL_PRICE_MIN_VALUE']) / $arProduct['PROPERTY_BASE_PRICE_MIN_VALUE'] * 100, 0, PHP_ROUND_HALF_DOWN);
                                        $saleValuePercent = ceil($saleValuePercent / 10) * 10;


                                        $saleValue = $saleValuePercent > 60 ? 'Final Sale' : 'Sale ' . $saleValuePercent . '%';
                                        $APPLICATION->AddViewContent('SALE_SIZE' . $arProduct['ID'], $saleValue);
                                        ?>
                                        <span class="price__block">
                                            <span class="price--current">
                                                <? if ($arProduct['PROPERTY_RETAIL_PRICE_MAX_VALUE'] > $arProduct['PROPERTY_RETAIL_PRICE_MIN_VALUE']) { ?><?= GetMessage('KF_PRICE_FROM') ?><? } ?>
                                                <? printf("%3.2f", KDXCurrency::convert($useVAT == "N" ? $arProduct['RETAIL_PRICE'] / 1.21 : $arProduct['RETAIL_PRICE'], KDXCurrency::$CurrentCurrency)) ?>
                                                <?= KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency) ?>
                                            </span>
                                        </span>
                                        <span class="price--old">
                                            <span>
                                                <? if ($arProduct['PROPERTY_BASE_PRICE_MAX_VALUE'] > $arProduct['PROPERTY_BASE_PRICE_MIN_VALUE']) { ?><?= GetMessage('KF_PRICE_FROM') ?><? } ?>
                                                <? //=KdxCurrency::convertAndFormat($useVAT=="N" ? $arProduct['BASE_PRICE'] / 1.21 : $arProduct['BASE_PRICE'],KDXCurrency::$CurrentCurrency)?>
                                                <? printf("%3.2f", KDXCurrency::convert($useVAT == "N" ? $arProduct['BASE_PRICE'] / 1.21 : $arProduct['BASE_PRICE'], KDXCurrency::$CurrentCurrency)) ?>
                                                <?= KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency) ?>
                                             </span>
                                        </span>
                                    <? } else { ?>
                                        <span class="price__block">
                                            <span>
                                                <? if ($arProduct['PROPERTY_RETAIL_PRICE_MAX_VALUE'] > $arProduct['PROPERTY_RETAIL_PRICE_MIN_VALUE']) { ?><?= GetMessage('KF_PRICE_FROM') ?><? } ?>
                                                <? //=KdxCurrency::convertAndFormat($useVAT=="N" ? $arProduct['RETAIL_PRICE'] / 1.21 : $arProduct['RETAIL_PRICE'],KDXCurrency::$CurrentCurrency)?>
                                                <? printf("%3.2f", KDXCurrency::convert($useVAT == "N" ? $arProduct['RETAIL_PRICE'] / 1.21 : $arProduct['RETAIL_PRICE'], KDXCurrency::$CurrentCurrency)) ?>
                                                <?= KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency) ?>
                                            </span>
                                        </span>
                                    <? } ?>

                                </div>
                            </div>
                        </div>

                    </a>
                </div>
            <?}?>
        </div>
<?}?>