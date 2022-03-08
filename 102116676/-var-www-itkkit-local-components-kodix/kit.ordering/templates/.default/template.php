<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if(!isAjax()){?>
<div id="checkout_wrap">
<?}?>
<form name="RELOAD" action="" method="get" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap" data-validation-function="validate_form_eng_char"></form>
<div class="<?if(!empty($arResult['AVAILABLE'])){?>checkout_block<?}?>">
    <div class="checkout_wrapper">
        <?if(!empty($arResult['AVAILABLE'])){?>
        <div class="checkout_item_v1">
            <?if($USER->IsAuthorized()){?>
            <h2 class="check_subtitle mod_1"><?=GetMessage('CP_KO_HELLO')?><span class="check_name"><?=$USER->GetFirstName()?></span></h2>
            <?}else{?>
            <h2 class="check_subtitle"><?=GetMessage('CP_KO_USER_EXISTS')?> /&nbsp;<a href="/personal/" title="#" class="js_modal_ctrl_3"><?=GetMessage('CP_KO_USER_ENTER')?></a></h2>
            <?}?>
            <?$component->addressClass->IncludeComponentTemplate();?>

            <div class="order_data_v2">
                <!-- delivery -->
                <div class="title_v4"><?=GetMessage('CP_KO_DELIVERY')?></div>
                <?if(!$arResult['IS_DELIVERIES_AVAILABLE']){?>
                    <?if(!$arResult['ORDER']->profile_id){?>
                        <p><?=GetMessage('CP_KO_CHOOSE_PROFILE')?></p>
                    <?}else{?>
                        <p><?=GetMessage('CP_KO_NO_DELIVERY_TO_PROFILE')?></p>
                    <?}?>
                <?}else{?>
                    <form name="DELIVERY" action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap">

                        <ul class="radio_list">
                            <?foreach($arResult['DELIVERIES'] as $arDelivery){?>
                                <?foreach($arDelivery['PROFILES'] as $profile => $arProfileParams){?>
                                    <li class="radio_item_v1">
                                        <input type="radio" name="DELIVERY" value="<?=$arDelivery['SID']?>:<?=$profile?>" id="radio_<?=$arDelivery['SID']?>:<?=$profile?>" <?if($arResult['ORDER']->delivery_id == $arDelivery['SID'].':'.$profile){?>checked=""<?}?> class="form_f_rad_v1">
                                        <label for="radio_<?=$arDelivery['SID']?>:<?=$profile?>" class="form_lbl_rad_v1" title="<?=$arProfileParams['TITLE']?>"><?=$arDelivery['NAME']?> - <?if($arProfileParams['PRICE']['VALUE']){?><?=KDXCurrency::convertAndFormat($arProfileParams['PRICE']['VALUE'],KDXCurrency::$CurrentCurrency,$arDelivery['BASE_CURRENCY'])?><?}else{?><span class="red_txt"><b><?=GetMessage('FREE_SHIP')?></b></span><?}?></label>
                                    </li>
                                <?}?>
                            <?}?>
                        </ul>
                    </form>
                <?}?>
                <!-- /delivery -->
                <!-- pay -->
                <div class="title_v4"><?=GetMessage('CP_KO_PAY_SYSTEM')?></div>
                <?if(empty($arResult['PAY_SYSTEMS'])){?>
                    <?if(!$arResult['ORDER']->delivery_id){?>
                        <p><?=GetMessage('CP_KO_CHOOSE_DELIVERY')?></p>
                    <?}else{?>
                        <p><?=GetMessage('CP_KO_NO_PAY_TO_DELIVERY')?></p>
                    <?}?>
                <?}?>
                <form name="PAYSYSTEM" action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap">
                    <ul class="radio_list">
                        <?foreach($arResult['PAY_SYSTEMS'] as $key=>$arPaySysten){

                            /*на старом чекауте не показываем paypal*/
                            if($key == 3) continue;
                            ?>
                            <li class="radio_item_v1">
                                <input type="radio" name="PAYSYSTEM" value="<?=$arPaySysten['ID']?>" id="PAYSYSTEM_<?=$arPaySysten['ID']?>" <?if($arPaySysten['ID'] == $arResult['ORDER']->pay_system_id){?>checked<?}?> class="form_f_rad_v1">
                                <label for="PAYSYSTEM_<?=$arPaySysten['ID']?>" class="form_lbl_rad_v1"><?=GetMessage('CP_KO_PAY_NAME_'.$arPaySysten['ID'])?></label>
                            </li>
                        <?}?>
                    </ul>
                </form>
                <!-- /pay -->
                <!-- comment -->
                <div class="title_v4"><?=GetMessage('CP_KO_DELIVERY_COMMENT')?></div>
                <form name="COMMENT" action="" method="post" class="ajax_load not_show_preloader" data-ajax-response-wrapper="#checkout_wrap">
                    <dl class="form_cell_v1 mod_full">
                        <dt class="form_hline_v1">
                            <label for="comment" class="hide"><?=GetMessage('CP_KO_DELIVERY_COMMENT')?></label>
                        </dt>
                        <dd class="form_f_w_v1">
                            <textarea placeholder="<?=GetMessage('CP_KO_DELIVERY_COMMENT')?>" class="f_field_v1 textarea" id="comment" name="COMMENT"><?=$arResult['ORDER']->comment?></textarea>
                        </dd>
                    </dl>
                </form>
                <!-- /comment -->
            </div>
        </div>
        <div class="checkout_item_v2">
            <ul class="product_list_v2">
                <?foreach($arResult["AVAILABLE"] as $arItem){
                    $img = kdxCFile::ResizeImageGet($arItem['PROPS']['PREVIEW_PICTURE'],array('width'=>138,'height'=>162), BX_RESIZE_IMAGE_EXACT)?>
                <li class="product_item_v2">
                    <a href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>" class="product_img_v2">
                        <img src="<?=$img['src']?>" alt="<?=$arItem['NAME']?>">
                    </a>
                </li>
                <?}?>
            </ul>
        </div>
        <div class="checkout_item_v3">
            <form action="" name="CART" class="ajax_load" method="post" data-ajax-response-wrapper="#checkout_wrap">
                <input type="hidden" name="CART" value="Y">
            <table class="profile_table mod_2 type_v1 custom_cart_table">
                <?$i=0;?>
                <?foreach($arResult["AVAILABLE"] as $arItem){?>
                    <tr class="cart_item">
                        <td><?=(++$i)?> <?=$arItem['NAME']?></td>
                        <td><input type="text" maxlength="2" class="f_field_v1" name="QUANTITY[<?=$arItem['ID']?>]" value="<?=intval($arItem['QUANTITY'])?>" data-max="<?=KDXCart::getAvailableQuantityByProduct($arItem['PRODUCT_ID'])?>"></td>
                        <td><?=$arItem['PROPS']['SIZE']?></td>
                        <td>
                            <div class="cart_item_price">
                            <?=KDXCurrency::convertAndFormat($arItem['PRICE'],KDXCurrency::$CurrentCurrency,$arItem['CURRENCY'])?>
                            <?if($arItem['PROPS']['BASE_PRICE_P'] > $arItem['PRICE']){?>
                                <div class="old_price"><?=KDXCurrency::convertAndFormat($arItem['PROPS']['BASE_PRICE_P'],KDXCurrency::$CurrentCurrency,$arItem['CURRENCY'])?></div>
                            <?}?>
                            <div class="kdxDeleteFromCart" data-product-id="<?=$arItem['ID']?>">x</div>
                            </div>
                        </td>
                    </tr>
                <?}?>
            </table>
            </form>
            <div class="order_data">
                <div class="order_row">
                    <div class="dt"><?=GetMessage('CP_KO_PRICE')?>:</div>
                    <div class="dd"><?=KDXCurrency::convertAndFormat($arResult['CART']->getPriceAvailable(),KDXCurrency::$CurrentCurrency);?></div>
                </div>
                <div class="order_row">
                    <div class="dt"><?=GetMessage('CP_KO_DISCOUNT')?>:</div>
                    <div class="dd"><?=KDXCurrency::convertAndFormat($arResult['CART']->getPriceAvailable() - $arResult['ORDER']->calculatedOrder['ORDER_PRICE'],KDXCurrency::$CurrentCurrency);?></div>
                </div>
                <?if($USER->IsAuthorized()){?>
                <?if($arResult['ORDER']->delivery_id){?>
                <div class="order_row">
                    <div class="dt"><?=GetMessage('CP_KO_DELIVERY')?> (<?=$arResult['DELIVERY']->name?>):</div>
                    <div class="dd"><?=KDXCurrency::convertAndFormat($arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'],KDXCurrency::$CurrentCurrency);?></div>
                </div>
                <?}?>
                <?if($arResult['ORDER']->vat_price){?>
                <div class="order_row">
                    <div class="dt">VAT:</div>
                    <div class="dd"><?=KDXCurrency::convertAndFormat($arResult['ORDER']->vat_price,KDXCurrency::$CurrentCurrency);?></div>
                </div>
                <?}?>
                <?}?>
            </div>
            <div class="order_data mod_1">
                <div class="order_row mod_1">
                    <div class="dt"><?=GetMessage('CP_KO_TOTAL')?>:</div>
                    <div class="dd"><?=KDXCurrency::convertAndFormat($arResult['ORDER']->calculatedOrder['ORDER_PRICE'] + $arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'] + $arResult['ORDER']->vat_price,KDXCurrency::$CurrentCurrency);?></div>
                </div>
                <?if($arResult['NEED_CONVERT']){?>
                <div class="order_row mod_1">
                    <div class="dt"><?=GetMessage('CP_KO_TOTAL_IN_EURO')?>:</div>
                    <div class="dd"><?=KDXCurrency::format($arResult['ORDER']->calculatedOrder['ORDER_PRICE'] + $arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'] + $arResult['ORDER']->vat_price);?></div>
                </div>
                <?}?>
                <div class="order_row_v2">
                    <ul class="form_list mod_2 ">
                        <li class="form_item_v1">
                            <dl class="form_cell_v1 mod_1">
                                <dt class="form_hline_v1">

                                </dt>
                                <dd class="form_f_w_v1">
                                    <input id="kdxCouponCode" type="text" id="field_7" name="COUPON" placeholder="<?=GetMessage('CP_KO_COUPON')?>" class="f_field_v1" value="">
                                </dd>
                            </dl>
                            <dl class="form_cell_v1 mod_1">
                                <dt class="form_hline_v1">

                                </dt>
                                <dd class="form_f_w_v1">
                                    <button class="btn_coupon kdxApplyCoupon"><?=GetMessage('CP_KO_APPLY_COUPON')?></button>
                                </dd>
                            </dl>
                        </li>
                    </ul>
                    <?if(!empty($arResult['COUPONS'])){?>
                    <ul class="form_list mod_2 ">
                        <?foreach($arResult['COUPONS'] as $coupon){
                            if(is_array($coupon)){?>
                                <li><?=($_SESSION['CATALOG_USER_COUPON_ALIAS'][$coupon['COUPON']]?:$coupon['COUPON'])?></li>
                            <?}else{?>
                                <li><?=($_SESSION['CATALOG_USER_COUPON_ALIAS'][$coupon]?:$coupon)?></li>
                            <?}?>
                    <?}?>
                    </ul>
                    <?}?>
                    <p class="coupon_error error_text"><?=GetMessage('CP_KO_ERROR_COUPON')?></p>

                </div>
                <div class="order_row_v2">
                    <form name="ORDER" action="" class="ajax_load" method="post" data-ajax-response-wrapper="#checkout_wrap" data-validation-function="validateOrdering">
                        <input type="hidden" name="CREATE_ORDER" value="">
                        <input type="hidden" name="STEP" value="<?=$_SESSION["ORDERING"]["STEP"]?>">
                        <button type="submit" class="btn_pay" style="width:175px;"><?=GetMessage('CP_KO_ORDER')?></button>
                        <? if(isShopManager()){ ?>
                        <a href="/cart/writeoff.php" class="btn_pay" style="width: 135px; margin: 0 20px 0 0; float: left; line-height: 25rem;"><?=GetMessage('CP_KO_WRITEOFF')?></a>
                        <? } ?>
                    </form>

                </div>
<!--                Ссылка на доставку и оплату-->
                <div class="order_note"><?=GetMessage('CP_ORDER_NOTE_1')?><a href="/help/delivery/"><?=GetMessage('CP_ORDER_NOTE_2')?></a></div>
                <?if($arResult['NEED_CONVERT']){?>
                <div class="order_note"><?=GetMessage('CP_KO_CURRENCY_NOTE',array(
                        'EURO_PRICE' => KDXCurrency::format(1.0),
                        'CURRENCY_PRICE' => KDXCurrency::convertAndFormat(1.0,KDXCurrency::$CurrentCurrency),
                    ))?></div>
                <div class="order_note"><?=GetMessage('CP_KO_CURRENCY_ATTENTION')?></div>
                <?}?>
            </div>
        </div>
        <?}?>
        <?if(empty($arResult['AVAILABLE']) && empty($arResult['UNAVAILABLE'])){?>
            <div class="success_block">
                <h1 class="success_title"><?=GetMessage('CP_KO_EMPTY_BASKET')?></h1>
                <span class="logo_row_v2"></span>
            </div>
        <?}?>
    </div>
</div>
<div class="related_items_cart">
</div>
<?if(!empty($arResult['UNAVAILABLE'])){?>
<div class="viewed_block">
    <h2 class="title_v2"><?=GetMessage('CP_KO_NOT_AVAILABLE')?></h2>
    <div class="viewed_list_wrap">
        <ul class="viewed_list js_not_available">
            <?foreach($arResult['UNAVAILABLE'] as $arItem){
                $img = kdxCFile::ResizeImageGet($arItem['PROPS']['PREVIEW_PICTURE'],array('width'=>138,'height'=>162), BX_RESIZE_IMAGE_EXACT)?>
                <li class="unavailable_item cart_item">
                    <div class="viewed_hold"><img src="<?=$img['src']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>"/></div>
                    <div class="splash">
                        <a href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
                        <div><?=$arItem['PROPS']['SIZE']?></div>
                        <div>
                        <?=KDXCurrency::convertAndFormat($arItem['PRICE'],KDXCurrency::$CurrentCurrency,$arItem['CURRENCY'])?>
                        </div>
                        <?if($arItem['PROPS']['BASE_PRICE_P'] > $arItem['PRICE']){?>
                            <div class="old_price"><?=KDXCurrency::convertAndFormat($arItem['PROPS']['BASE_PRICE_P'],KDXCurrency::$CurrentCurrency,$arItem['CURRENCY'])?></div>
                        <?}?>
                        <div class="kdxDeleteFromCart" data-product-id="<?=$arItem['ID']?>">x</div>
                    </div>
                </li>
            <?}?>
        </ul>
    </div>
</div>
<?}?>
<?if(!isAjax()){?>
    <script>
        BX.message['CP_KO_CHOOSE_PROFILE'] = '<?=GetMessage('CP_KO_CHOOSE_PROFILE')?>';
        BX.message['CP_KO_CHOOSE_DELIVERY'] = '<?=GetMessage('CP_KO_CHOOSE_DELIVERY')?>';
        BX.message['CP_KO_CHOOSE_PAY_SYSTEM'] = '<?=GetMessage('CP_KO_CHOOSE_PAY_SYSTEM')?>';
    </script>
</div>
<?}?>
<?
$rr_params = KDXCart::getProdIDsBySkus($arResult['CART']->getAvailable(), $arResult['CART']->getUnavailable(), true);
if(!empty($rr_params)){?>
<script>
    <?if(!isAjax()){?>
    $(function(){
    <?}?>
    initCarousel('.js_not_available');
    <?if(!isAjax()){?>
    })
    <?}?>

    $(document).ready(function(){
        $.ajax({
            type: "post",
            url: "/ajax/RetailRocket.php",
            data: {query: 'CrossSellItemToItems', rr_params: "<?=$rr_params?>"},
            success: function(data){
                $('.related_items_cart').html(data);
            }
        });
    })
</script>
<?} else {?>
        <script type="text/javascript">
            $(document).ready(function(){
                $.ajax({
                    type: "post",
                    url: "/ajax/RetailRocket.php",
                    data: {query: 'ItemsToMain'},
                    success: function(data){
                        $('.related_items_cart').html(data);
                    }
                });
            })
        </script>
<?}?>