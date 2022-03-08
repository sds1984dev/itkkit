<?php
/**
 * Created by:  KODIX 17.03.2015 11:19
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if(is_array($arResult["ITEMS"]) && !empty($arResult["ITEMS"])){?>
    <div class="js-fullpage__section fullpage__section" data-section-name="offers">
        <div class="grid-container">
            <div class="grid-row">
                <div class="col-sm-12 pdng-sm-top-1 pdng-lg-top-0 pdng-sm-btm-1 pdng-lg-btm-0">
                    <div class="carousel" style="background-image:url(<?=$arParams["PICTURE"] ? CFile::getPath($arParams["PICTURE"]) : SITE_TEMPLATE_PATH.'/resources/img/background-1.jpg);'?>">
                        <div class="title title--block txt--white"><?=$arParams["TITLE"]?></div>
                        <div class="carousel__slider" data-slick="goods">
                            <?foreach($arResult["ITEMS"] as $item) {
                                $img = kdxCFile::ResizeImageGet($item['~DETAIL_PICTURE'],array('width'=>'440','height'=>'440'),BX_RESIZE_IMAGE_EXACT);
                                ?>
                                <a class="carousel__item" href="<?=$item["DETAIL_PAGE_URL"]?>">
                                    <div class="carousel__item-inner">
                                        <img class="carousel__img" data-lazy="<?=$img["src"]?>" src="<?=$img["src"]?>" alt="<?=$item["NAME"]?>">
                                    </div>
                                </a>
                            <?}?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?}?>