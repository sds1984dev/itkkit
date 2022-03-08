<?php
/**
 * Created by:  KODIX 03.03.2018 11:19
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Vantsov Ivan
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if(is_array($arResult["ITEMS"]) && !empty($arResult["ITEMS"])){?>
    <div class="grid-container grid-container--center helper--md-margin-bottom">
        <div class="grid-row">
            <div class="col-sm-12 pdng-sm-top-1 pdng-lg-top-0 pdng-sm-btm-1 pdng-lg-btm-0">
                <div class="carousel carousel--no-full" style="background-image:url();">
                    <div class="title title--block <?=$arParams['SALE'] == 'Y' ? 'txt--red' : '';?>">
                        <?=$arParams["TITLE"]?>
                    </div>
                    <div class="carousel__slider carousel__slider--no-full" data-slick="goods">
                        <?foreach($arResult["ITEMS"] as $item) {
                            $img = CFile::ResizeImageGet($item['~DETAIL_PICTURE'],array('width'=>429,'height'=>429),BX_RESIZE_IMAGE_EXACT);
                            $img_2x = CFile::ResizeImageGet($item['~DETAIL_PICTURE'],array('width'=>858,'height'=>858),BX_RESIZE_IMAGE_EXACT);
                            $img_mob = CFile::ResizeImageGet($item['~DETAIL_PICTURE'],array('width'=>256,'height'=>256),BX_RESIZE_IMAGE_EXACT);
                            ?>
                            <a class="carousel__item" href="<?=$item["DETAIL_PAGE_URL"]?>">
                                <div class="carousel__item-inner">
                                    <picture>
                                        <source srcset="<?=$img_mob['src']?>" media="(max-width: 767px)">
                                        <source srcset="<?=$img['src']?> 1x, <?=$img_2x['src']?> 2x">
                                        <img class="carousel__img"
                                             src=""
                                             data-src="<?=$img['src']?>"
                                             alt="<?=$item['NAME']?>"
                                             data-object-fit="contain"
                                             data-lazy="<?=$img['src']?>"
                                        >
                                    </picture>
                                </div>
                            </a>
                        <?}?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?}?>