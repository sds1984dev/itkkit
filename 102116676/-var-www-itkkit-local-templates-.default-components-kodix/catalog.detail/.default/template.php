<?php
/**
 * Created by:  KODIX 19.03.2015 14:24
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$i=0;

$APPLICATION->SetPageProperty("PAGE_CLASS", "product_page");
$arResult['OG_IMAGE'] = false;
?>
<div class="product_block">

    <div class="product_view">
        <div class="product_v_full">
            <div class="slider slider-for">
                <?if(empty($arResult['GALLERY'])){
                    $file = kdxCFile::ResizeImageGet($arResult['DETAIL_PICTURE'],array('width'=>920,'height'=>1060),BX_RESIZE_IMAGE_EXACT);
                    $file_big = kdxCFile::ResizeImageGet($arResult['DETAIL_PICTURE'],array('width'=>920,'height'=>1060),BX_RESIZE_IMAGE_EXACT);
                    $arResult['OG_IMAGE'] = $file;?>
                    <div><a href="<?=$file_big['src']?>" class="jqzoom"><img width="460" height="530" src="<?=$file['src']?>" alt=""></a></div>
                <?}?>
                <?foreach($arResult['GALLERY'] as $pic){
                    $file = kdxCFile::ResizeImageGet($pic,array('width'=>920,'height'=>1060),BX_RESIZE_IMAGE_EXACT);
                    $file_big = kdxCFile::ResizeImageGet($pic,array('width'=>920,'height'=>1060),BX_RESIZE_IMAGE_EXACT);
                    if(!$arResult['OG_IMAGE']){
                        $OG_IMAGE = kdxCFile::ResizeImageGet($pic,array('width'=>280,'height'=>280));
                        $arResult['OG_IMAGE'] = $OG_IMAGE;
                    }?>
                    <div><a href="<?=$file_big['src']?>" class="jqzoom"><img width="460" height="530" src="<?=$file['src']?>" alt=""></a></div>
                    <?if(++$i > 50){break;}}?>
            </div>
        </div>
        <?if(count($arResult['GALLERY']) > 3){?>
            <span class="js-slick-prev custom-slick-prev"></span>
            <span class="js-slick-next custom-slick-next"></span>
        <?}?>
        <ul class="product_thumbs<?if(count($arResult['GALLERY']) <= 3){?> prod3<?}?>">
            <?$i=0;
            foreach($arResult['GALLERY'] as $pic){
                $file = kdxCFile::ResizeImageGet($pic,array('width'=>316,'height'=>312),BX_RESIZE_IMAGE_EXACT);?>
                <li><img width="158" height="156" src="<?=$file['src']?>" alt=""></li>
            <?if(++$i > 50){break;}}?>
        </ul>

    </div>
    <script>
        $(function(){
            $(document).on('click', '.js-slick-prev', function(){
                $('.slick-prev').click();
            });
            $(document).on('click', '.js-slick-next', function(){
                $('.slick-next').click();
            });

        $('.slider-for').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.slider-nav'
        });
        $('.product_thumbs').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            asNavFor: '.slider-for',
            dots: false,
            centerMode: false,
            vertical: true,
            focusOnSelect: true
        });
        });
    </script>

    <div class="product_data">
        <h2 class="product_title tovar"><a href="<?=$arResult['CML2_MANUFACTURER']['DETAIL_PAGE_URL']?>"><?=$arResult['CML2_MANUFACTURER']['NAME']?></a></h2>
        <h1 class="product_subtitle tovar"><?=$arResult['NAME']?></h1>

        <div class="product_func_v2 <?if(!$arResult['HAVE_REAL_SIZES'] || $arResult['CANT_ORDER']){?>mod_sold_out<?}?>">
            <div class="pr_func_row">

                <div class="pr_f_price <?if($arResult['HAVE_REAL_SIZES'] && $arResult['BASE_PRICE_MIN'] > $arResult['RETAIL_PRICE_MIN']){?>mod_sale<?}?>">
                    <?if($arResult['HAVE_REAL_SIZES']){?>
                        <?$cur_type = (LANGUAGE_ID == 'ru') ? 'RUB': KDXCurrency::$CurrentCurrency;?>
                        <?$cur_name = (LANGUAGE_ID == 'ru') ? GetMessage('APPROX_RUB'): KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?>
                        <?foreach($arResult['REAL_SIZES'] as $arSKU){?>
                        <div data-product-price="<?=$arSKU['ID']?>" style="display: none">
                            <?if($arSKU['BASE_PRICE'] > $arSKU['RETAIL_PRICE']){?>
                                <div class="old_price_v2">
                                    <span class="dt"><?printf("%3.2f",KDXCurrency::convert($arSKU['BASE_PRICE'], KDXCurrency::$CurrentCurrency))?></span>
                                    <span class="dd"><?=KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?></span>
                                </div>
                            <?}?>
                            <div class="current_price_v2">
                                <span class="dt"><?printf("%3.2f",KDXCurrency::convert($arSKU['RETAIL_PRICE'], KDXCurrency::$CurrentCurrency))?></span>
                                <span class="dd"><?=KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?></span>
                            </div>
                            <?$approx_rub_price = $arSKU['RETAIL_PRICE'] / 1.21;?>
                            <div class="approx_rub_price">
                                <?/*<span class="approx_label"><?=GetMessage('APPROX')?></span>*/?>
                                <span class="dt"><?printf("%3.2f",KDXCurrency::convert($approx_rub_price, $cur_type))?></span>
                                <span class="dd"><?=$cur_name?></span>
                                <span class="dd ex-vat"><?=GetMessage('APPROX_EX_VAT')?></span>
                            </div>
                        </div>
                        <?}?>
                        <div data-product-price="ALL">
                            <?if($arResult['BASE_PRICE_MIN'] > $arResult['RETAIL_PRICE_MIN']){?>
                            <div class="old_price_v2">
                                <span class="dt"><?printf("%3.2f",KDXCurrency::convert($arResult['BASE_PRICE_MIN'], KDXCurrency::$CurrentCurrency))?></span>
                                <span class="dd"><?=KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?></span>
                            </div>
                            <?}?>
                            <div class="current_price_v2">
                                <span class="dt"><?printf("%3.2f",KDXCurrency::convert($arResult['RETAIL_PRICE_MIN'], KDXCurrency::$CurrentCurrency))?></span>
                                <span class="dd"><?=KDXCurrency::GetCurrencyName(KDXCurrency::$CurrentCurrency)?></span>
                            </div>
                            <?$approx_rub_price = $arResult['RETAIL_PRICE_MIN'] / 1.21;?>
                            <div class="approx_rub_price">
                                <?/*<span class="approx_label"><?=GetMessage('APPROX')?></span>*/?>
                                <span class="dt"><?printf("%3.2f",KDXCurrency::convert($approx_rub_price, $cur_type))?></span>
                                <span class="dd"><?=$cur_name?></span>
                                <span class="dd ex-vat"><?=GetMessage('APPROX_EX_VAT')?></span>
                            </div>

                        </div>
                    <?}else{?>
                        <span class="sold_out"><?=GetMessage('SOLD')?></span>
                    <?}?>
                </div>
                <div class="pr_f_price_note">
                    <span><?=GetMessage('VAT')?></span>
                    <span><?=GetMessage('EU')?></span>
                </div>
            </div>
            <div class="pr_func_row">
                <div class="pr_f_size">
                    <?$productID='';
                    if(count($arResult['REAL_SIZES']) == 1){
                        $arSKU = reset($arResult['REAL_SIZES']);
                        $productID = $arSKU['ID'];
                        if(!in_array($arSKU['PROPERTY_SIZE_VALUE'],KDXSettings::getSetting('NOT_SHOW_SIZES'))){?>
                        <div style="margin-right: 10px;float: left;"><?=GetMessage('CHOOSE_SIZE_BR')?></div>
                        <span class="sod_select mes_select focus">
                            <span class="sod_label"><?=$arSKU['PROPERTY_SIZE_VALUE']?></span>
                        </span>
                        <?}
                    }elseif(count($arResult['REAL_SIZES']) > 1){?>
                    <div style="text-align:left;margin-right: 10px;float: left;"><?=GetMessage('CHOOSE_SIZE_BR')?></div>
                    <select name="product_size" class="form_select_v1">
                        <?foreach($arResult['REAL_SIZES'] as $arSKU){?>
                        <option value="<?=$arSKU['ID']?>" <?if($arSKU['CATALOG_QUANTITY'] <= 0){?>disabled="disabled" <?}?>><?=$arSKU['PROPERTY_SIZE_VALUE']?></option>
                        <?}?>
                    </select>
                    <?}?>
                </div>
                <div class="pr_f_bth_hold">
                    <a href="#" title="#" class="btn_buy kdxAddToCart" data-product-id="<?=$productID?>" data-quantity="1" data-ok-text="<?=GetMessage('IN_CART')?>" data-error-text="<?=GetMessage('ADD_ERROR')?>" data-origin-text="<?=GetMessage('TO_CART')?>" data-rr-id="<?=intval($arResult["ID"])?>"><?=GetMessage('TO_CART')?></a>
                </div>
            </div>
            <div class="pr_func_row information"></div>
        </div>

        <div class="product_func">
            <?if(trim($arResult['CML2_ARTICLE'])){?>
                <dl class="product_code">
                    <dt class="dt"><?=GetMessage('ARTICLE')?>:</dt>
                    <dd class="dd"><?=$arResult['CML2_ARTICLE']?></dd>
                </dl>
            <?}?>
            <div class="socials mod_2">
                <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
                <script src="//yastatic.net/share2/share.js"></script>
                <div class="ya-share2"
                     data-lang="ru"
                     data-services="vkontakte,facebook,twitter,pinterest"
                     data-counter
                     data-size="s"
                     data-image="https://<?=SITE_SERVER_NAME.kdxCFile::ResizeImageGet($arResult['GALLERY'][0],array('width'=>920,'height'=>1060))['src']?>">
                </div>
            </div>
        </div>

        <div class="tabs_block">
            <ul class="tabs">
                <li data-tab="1" class="tabs_item selected"><?=GetMessage('DESCRIPTION')?></li>
                <li <?if(!empty($arResult['SIZE_GRID'])){?>data-tab="2"<?}?> class="tabs_item <?if(empty($arResult['SIZE_GRID'])){?>disabled<?}?>"><?=GetMessage('GIDE')?></li>
                <li data-tab="3" class="tabs_item"><?=GetMessage('RETURN')?></li>
            </ul>
            <div class="tabs_contents_block">
                <ul class="tabs_contents">
                    <li data-content="1" class="tabs_c_item selected">
                        <div class="tabs_c_hold content">
                            <?if(!empty($arResult['~DETAIL_TEXT'])) {
                                echo htmlspecialchars_decode($arResult['~DETAIL_TEXT']);
                            }else {
                                $APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    ".default",
                                    array(
                                        "AREA_FILE_SHOW" => "sect",
                                        "AREA_FILE_SUFFIX" => "contacts",
                                        "AREA_FILE_RECURSIVE" => "Y",
                                    ),
                                    false
                                );
                            }?>
                        </div>
                    </li>
                    <li data-content="2" class="tabs_c_item">
                        <div class="tabs_c_hold_sizes">
                            <?if(!empty($arResult['SIZE_GRID'])) {?>
                                <table class="size_grid_box">
                                    <tr>
                                        <?if(!empty($arResult['SIZE_GRID']['GRID_PIC']['SRC'])){?>
                                            <td><img src="<?=$arResult['SIZE_GRID']['GRID_PIC']['SRC']?>"></td>
                                        <?}?>
                                        <td valign="middle"><?=$arResult['SIZE_GRID']['DETAIL_TEXT']?></td>
                                    </tr>
                                </table>
                            <?}?>
                        </div>
                    </li>
                    <li data-content="3" class="tabs_c_item">
                        <div class="tabs_c_hold">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                ".default",
                                array(
                                    "AREA_FILE_SHOW" => "sect",
                                    "AREA_FILE_SUFFIX" => "return",
                                    "AREA_FILE_RECURSIVE" => "Y",
                                ),
                                false
                            );?>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="related_items_detail_page">
</div>

<?$APPLICATION->IncludeComponent(
    "kodix:catalog.viewed.products",
    ".default",
    array(
        "PAGE_ELEMENT_COUNT" => "10",
        "SECTION_ELEMENT_ID" => $arResult["ID"]
    ),
    false
);?>
<?$APPLICATION->IncludeComponent('kodix:blog.interesting','',array('CACHE_TYPE' => 'Y', 'CACHE_TIME' => 3600));?>
<script>
BX.message['CHOOSE_SIZE']='<?=GetMessage('CHOOSE_SIZE')?>';
BX.message['SOLD']='<?=GetMessage('SOLD')?>';
BX.message['TO_CART']='<?=GetMessage('TO_CART')?>';
BX.message['IN_CART']='<?=GetMessage('IN_CART')?>';
BX.message['MAX_QUANTITY_ADDED']='<?=GetMessage('MAX_QUANTITY_ADDED')?>';
</script>

<script type="text/javascript">
    KDXSale.PROD_ID = <?=$arResult['ID']?>;
    rrApiOnReady.push(function() {
        try{ rrApi.view(KDXSale.PROD_ID); } catch(e) {}
    });

    $(document).ready(function(){
        $.ajax({
            type: "post",
            url: "/ajax/RetailRocket.php",
            data: {query: 'UpSellItemToItems', rr_params: KDXSale.PROD_ID},
            success: function(data){
                $('.related_items_detail_page').html(data);
            }
        });
    })
</script>