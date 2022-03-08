<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
    if(empty($arResult['AVAILABLE']) && empty($arResult['UNAVAILABLE']))
    {
        ?>
        <div id="checkout_wrap"><div class="checkout_wrapper">
            <div class="success_block">
                <h1 class="success_title"><?=GetMessage('CP_KO_EMPTY_BASKET')?></h1>
                <span class="logo_row_v2"></span>
            </div>
            </div>
            </div>
        <?
        return;
    }
?>
<div class="content_row_v2" id="checkout_wrap">
    <div class="checkout_wrap clearfix">
        <form name="RELOAD" action="" method="get" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap" data-validation-function="validate_form_eng_char"></form>
        <div class="checkout_column_1">
            <div class="checkout_title"><?=GetMessage('корзина')?></div>
            <div class="cart_section_list">
                <form data-ajax-response-wrapper="#checkout_wrap" method="post" class="ajax_load" name="CART" action="">
                    <input type="hidden" value="Y" name="CART">
                    <?foreach($arResult["AVAILABLE"] as $arItem){?>
                        <?$img = kdxCFile::ResizeImageGet($arItem['PROPS']['PREVIEW_PICTURE'],array('width'=>138,'height'=>162), BX_RESIZE_IMAGE_EXACT)?>
                    <div class="cart_section">
                        <a href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>" class="cart_section_img">
                            <img src="<?=$img['src']?>" alt="<?=$arItem['NAME']?>">
                        </a>
                        <div class="cart_section_va">
                            <a href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>" class="cart_section_title">
                                <?=$arItem['NAME']?>
                            </a>
                        </div>

                        <div class="cart_quantity">
                            <!-- change goods amount -->
                            <div class="amount clearfix">
                                <span class="item-minus js_item_minus"></span>
                                <input class="amount_numb" type="text" name="QUANTITY[<?=$arItem['ID']?>]" value="<?=intval($arItem['QUANTITY'])?>" data-max="<?=KDXCart::getAvailableQuantityByProduct($arItem['PRODUCT_ID'])?>" maxlength="3"/>
                                <span class="item-plus js_item_plus"></span>
                            </div>
                        </div>
                        <div class="cart_section_size"><?=$arItem['PROPS']['SIZE']?></div>
                        <div class="cart_section_price">
                            <?=KDXCurrency::convertAndFormat($arItem['PRICE'],KDXCurrency::$CurrentCurrency,$arItem['CURRENCY'])?>
                            <?if($arItem['PROPS']['BASE_PRICE_P'] > $arItem['PRICE']){?>
                                <div class="old">
                                    <?=KDXCurrency::convertAndFormat($arItem['PROPS']['BASE_PRICE_P'],KDXCurrency::$CurrentCurrency,$arItem['CURRENCY'])?>
                                </div>
                            <?}?>
                        </div>
                        <i class="remove_icon kdxDeleteFromCart" data-product-id="<?=$arItem['ID']?>"></i>
                    </div>
                    <?}?>
                    <?
                    foreach($arResult["UNAVAILABLE"] as $arItem){?>
                        <?$img = kdxCFile::ResizeImageGet($arItem['PROPS']['PREVIEW_PICTURE'],array('width'=>138,'height'=>162), BX_RESIZE_IMAGE_EXACT)?>
                        <div class="cart_section not_avail">
                            <a href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>" class="cart_section_img">
                                <img src="<?=$img['src']?>" alt="<?=$arItem['NAME']?>"/>
                            </a>
                            <div class="cart_section_va">
                                <a href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>" class="cart_section_title"><?=$arItem['NAME']?></a>
                            </div>

                            <div class="cart_quantity"><?=GetMessage('нет в наличии')?></div>
                            <div class="cart_section_size"><?=$arItem['PROPS']['SIZE']?></div>
                            <div class="cart_section_price">
                                <?=KDXCurrency::convertAndFormat($arItem['PRICE'],KDXCurrency::$CurrentCurrency,$arItem['CURRENCY'])?>
                                <?if($arItem['PROPS']['BASE_PRICE_P'] > $arItem['PRICE']){?>
                                    <div class="old">
                                        <?=KDXCurrency::convertAndFormat($arItem['PROPS']['BASE_PRICE_P'],KDXCurrency::$CurrentCurrency,$arItem['CURRENCY'])?>
                                    </div>
                                <?}?>
                            </div>
                            <i class="remove_icon kdxDeleteFromCart" data-product-id="<?=$arItem['ID']?>"></i>
                        </div>
                    <?}?>
                </form>
            </div>

            <div class="checkout_sec_row">
                <div class="checkout_sec_column_1">
                    <form action="">
                        <input type="text" id="kdxCouponCode" name="COUPON" placeholder="<?=GetMessage('CP_KO_COUPON2')?>" class="f_field_v1 promo_input"/>
                        <input type="submit"  value="<?=GetMessage('CP_KO_APPLY_COUPON')?>" class="grey_btn promo_btn kdxApplyCoupon"/>
                        <p class="coupon_error error_text"><?=GetMessage('CP_KO_ERROR_COUPON')?></p>
                    </form>
                </div>
                <div class="checkout_sec_column_2">
                    <div class="checkout_total">
                        <div class="mb">
                            <span class="checkout_total_label"><?=GetMessage('CP_KO_PRICE')?>:</span>
                            <span class="checkout_total_val"><?=KDXCurrency::convertAndFormat($arResult['CART']->getPriceAvailable(),KDXCurrency::$CurrentCurrency);?></span>
                        </div>
                        <div class="mb">
                            <span class="checkout_total_label"><?=GetMessage('CP_KO_DISCOUNT')?>:</span>
                            <span class="checkout_total_val"><?=KDXCurrency::convertAndFormat($arResult['CART']->getPriceAvailable() - $arResult['ORDER']->calculatedOrder['ORDER_PRICE'],KDXCurrency::$CurrentCurrency);?></span>
                        </div>
                        <?if($USER->IsAuthorized()){?>
                            <?if($arResult['ORDER']->delivery_id){?>
                            <div class="mb">
                                <span class="checkout_total_label"><?=GetMessage('CP_KO_DELIVERY')?> (<?=$arResult['DELIVERY']->name?>):</span>
                                <span class="checkout_total_val"><?=KDXCurrency::convertAndFormat($arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'],KDXCurrency::$CurrentCurrency);?></span>
                            </div>
                            <?}?>
                            <?if($arResult['ORDER']->vat_price){?>
                            <div class="mb">
                                <span class="checkout_total_label">VAT:</span>
                                <span class="checkout_total_val"><?=KDXCurrency::convertAndFormat($arResult['ORDER']->vat_price,KDXCurrency::$CurrentCurrency);?></span>
                            </div>
                            <?}?>
                        <?}?>
                        <div class="mb">
                            <span class="checkout_total_label"><b><?=GetMessage('CP_KO_TOTAL')?>:</b></span>
                            <span class="checkout_total_val"><b><?=KDXCurrency::convertAndFormat($arResult['ORDER']->calculatedOrder['ORDER_PRICE'] + $arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'] + $arResult['ORDER']->vat_price,KDXCurrency::$CurrentCurrency);?></b></span>
                        </div>
                        <?if($arResult['NEED_CONVERT']){?>
                        <div class="mb">
                            <span class="checkout_total_label"><b><?=GetMessage('CP_KO_TOTAL_IN_EURO')?>:</b></span>
                            <span class="checkout_total_val"><b><?=KDXCurrency::format($arResult['ORDER']->calculatedOrder['ORDER_PRICE'] + $arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'] + $arResult['ORDER']->vat_price);?></b></span>
                        </div>
                        <?}?>



                    </div>
                </div>
            </div>
            <? if(KDXCurrency::format(1) != KDXCurrency::convertAndFormat(1,KDXCurrency::$CurrentCurrency)){
            ?>
                <div class="cart_note">
                    <i class="cart_note_icon">!</i>
                    <?
                        echo GetMessage('Все заказы оформляются и оплачиваются в евро.',array( 'EURO_PRICE' => KDXCurrency::format(1.0),
                            'CURRENCY_PRICE' => KDXCurrency::convertAndFormat(1,KDXCurrency::$CurrentCurrency),
                        ));
                    ?>
                </div>
            <?}?>
        </div>
        <div class="checkout_column_2" style="overflow: hidden;">

            <?
                if(!empty($arResult["AVAILABLE"]))
                {
                    echo '<div class="checkout_title">'.GetMessage('оформление').'</div>';

                    switch(true)
                    {
                        case $_SESSION["ORDERING"]["NEXT_COMMENT"] == 1 && $USER->IsAuthorized():
                            $action = 'comment';
                        break;

                        case $arResult['ORDER']->delivery_id && $USER->IsAuthorized():
                            $action = 'payment';
                        break;

                        case !$arResult['ORDER']->delivery_id && count($arResult['ADDRESSES']) > 1 && $USER->IsAuthorized():
                            $action = 'addr';
                        break;

                        case count($arResult['ADDRESSES']) <= 1 && $USER->IsAuthorized():
                            $action = 'delivery';
                        break;

                        case !$USER->IsAuthorized():
                            $action = 'reg';
                            break;
                    }

                    $path = $_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/step/'.$_GET['action'].'.php';

                    if(file_exists($path)) $action = $_GET['action'];

                    include_once('step/'.$action.'.php');
                }
            ?>
        </div>


    </div>
</div><!-- content_row_v2 -->
<style>
    #checkout_map { width: 520px; height: 350px; }
</style>
<script src="https://maps.googleapis.com/maps/api/js"></script>
<script type="text/javascript">
    function initialize_map() {
        var mapCanvas = document.getElementById('checkout_map');

        if(mapCanvas){
        var myLatLng = {lat:56.950519, lng: 24.111206};
        var mapOptions = {
            center: new google.maps.LatLng(56.950519, 24.111206),
            disableDefaultUI: true,
            zoom: 16,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(mapCanvas, mapOptions);
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: 'ITK'
        });
        }
    }

    google.maps.event.addDomListener(window, 'load', initialize_map);


    $(function(){
        $(document).on('kdxInitPlugins',function(){

            $('.js_cas_trigger').on('click', function(){
                $('.cas_list').show();
            });

            initialize_map();

            tabs();
        });
    });
</script>