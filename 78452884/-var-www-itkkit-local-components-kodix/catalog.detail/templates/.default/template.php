<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?$item=$arResult;?>

<h1><?=$item["NAME"]?></h1>
<div class="price"><strong><?=format_price($item["RETAIL_PRICE"]["PRICE"])?></strong></div>

<div class="tabs jquery_tabs">
    <ol class="markers">
        <li><a href="#description">Описание</a></li>
        <li><a href="#delivery">Доставка</a></li>
        <li><a href="#return">Возврат</a></li>
    </ol>

    <div id="description" class="tab current">
        <p><?=$item["DETAIL_TEXT"]?></p>

        <div class="specs">
            <ul class="specs">
                <li>Состав: 100% вискоза. Производство: Италия</li>
                <li>Размер 32, 34, 36</li>
                <li>Высота 168 см, ширина 34 см</li>
                <li>Машинная стирка согласно инструкции на платье</li>
            </ul>
        </div>
        <!-- //specs -->
    </div>
    <!-- //tab -->

    <div id="delivery" class="tab">
    </div>
    <!-- //tab -->

    <div id="return" class="tab">
    </div>
    <!-- //tab -->
</div>
<!-- //tabs -->

<form action="#" class="buy"><fieldset>
        <div class="size line">
            <select name="size" id="size" class="kdxProductSelect">
                <option value="">Выбрать размер</option>
                <?foreach($item["REAL_SIZES"] as $size_id=>$size){?>
                    <option value="<?=$size_id?>"><?=$size["PROPERTY_SIZE_VALUE"]?></option>
                <?}?>
            </select>

            <?if($item["SIZE_GRID"]){?>
                <a href="#" class="go show_sizes_grid" grid_iblock="<?=$arParams["GRIDS_IBLOCK_ID"]?>" grid="<?=$item["SIZE_GRID"]?>">Смотреть сетку размеров</a>
            <?}?>

        </div>

        <div class="qty line">
            <label for="qty">Количество:</label>
            <input name="qty-1234" value="1" id="kdxQuantity<?=$item["ID"]?>" data-product-id="<?=$item["ID"]?>" class="text qty kdxQuantity" type="text">
        </div>

        <div class="buttons line">
            <input class="buy button kdxAddToCart" data-product-id="<?=$item["ID"]?>" value="В КОРЗИНУ" type="submit" product_id="">
            <input class="buy button kdxAddToWishList" data-product-id="<?=$item["ID"]?>" value="В избранное" type="submit">
            <input class="aux button" value="ОТЛОЖИТЬ" type="button">
        </div>
    </fieldset></form>

<div class="media">

    <div class="product_images">
        <div class="iosSlider_product">
            <div class="slider">
                <?foreach($item["GALLERY"] as $i){?>
                    <?
                    $file_small = kdxCFile::ResizeImageGet($i, array('width'=>422, 'height'=>422), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                    $file_big = CFile::GetPath($i);
                    ?>
                    <a class="slide fancybox" data-fancybox-group="gallery_catalog"  href="<?=$file_big?>">
                        <img class="zoom" src="<?=$file_small["src"]?>" alt="" data-zoom-image="<?=$file_big?>">
                    </a><!--/slide-->
                <?}?>
            </div><!--/slider-->
        </div><!--/iosSlider_product-->
        <div class='productSelectors clearfix'><!--
                    <?foreach($item["GALLERY"] as $i){
                        $file_very_small = kdxCFile::ResizeImageGet($i, array('width'=>77, 'height'=>77), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                        --><div class='item'><img src="<?=$file_very_small["src"]?>" alt=""></div><!--
                    <?}?>
        --></div>
    </div>


    <ul class="social">
        <li><a title="Отправить ссылку по E-mail" class="button small" href="#">Email</a></li>
        <li class="tweet"><a title="Запостить в Twitter" href="#">Запостить в Twitter</a></li>
        <li class="facebook"><a title="Лайкнуть на Facebook" href="#">Лайкнуть на Facebook</a></li>
    </ul>
</div>

<div class="additional">
    <?$APPLICATION->IncludeComponent(
        "kodix:catalog.viewed.products",
        ".default",
        array(
            "PAGE_ELEMENT_COUNT" => "10",
            "SECTION_ELEMENT_ID" => $arResult["ID"]
        ),
        false
    );?>



    <?$APPLICATION->IncludeComponent("kodix:catalog.recommended", "", array(
        "IBLOCK_TYPE"=>$arParams["IBLOCK_TYPE"],
        "IBLOCK_ID"=>$arParams["IBLOCK_ID"],
        "ELEMENT_ID"=>$item["ID"],
        "ITEMS_COUNT"=>4,
    ));?>

    <?$APPLICATION->IncludeComponent(
	"kodix:social", 
	".default", 
	array(
		"TYPE" => "comments",
		"SERVICES" => array(
		),
		"_APP_ID" => ""
	),
	false
);?>
</div>
