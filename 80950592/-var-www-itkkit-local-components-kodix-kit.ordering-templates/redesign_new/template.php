<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
global $USER;
    if(empty($arResult['AVAILABLE']) && empty($arResult['UNAVAILABLE']))
    {
        $APPLICATION->SetPageProperty("MAIN_CLASS", "main--full-height");
        $APPLICATION->SetPageProperty("PAGE_WRAPPER_CLASS", "grid-container--center");
        ?>
        <div id="checkout_wrap">
            <section class="cart-section--empty">
                <div class="cart-section__msg">
                    <svg class="icon icon-question_message">
                        <use xlink:href="/local/templates/kit_new/resources/svg/app.svg#question_message"></use>
                    </svg>
                    <h1 class="heading--h1"><?=GetMessage('CP_KO_EMPTY_BASKET')?></h1>
                    <p><?=GetMessage('CP_KO_DISCLAIMER')?></p>
                    <a class="link btn btn--primary btn--inline btn--block-mobile" href="/catalog/"><?=GetMessage('CP_KO_TO_CATALOG')?></a>
                </div>
            </section>
        </div>
        <?
        return;
    }
    
    //Кейс, когда выбрана страна доставки
    use Bitrix\Highloadblock as HL;
    if (isset($arResult['ORDER']->properties['DELIVERY_COUNTRY']['VALUE'])) {
        $hlblock = HL\HighloadBlockTable::getById(4)->fetch();
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $res = $entity_data_class::getList( array('filter'=>array('UF_COUNTRY_ID'=>$arResult['ORDER']->properties['DELIVERY_COUNTRY']['VALUE'])) ); 
        $is_vat=false;
        while ($item = $res->fetch())
        {
            $_SESSION['LAST_COUNTRY']=$item['UF_CONTRY_CODE'];
        }
    }
    
    //Узнаем есть налог для страны или нет
    $arrCountries = getHlCountries();
    $useVAT = $arrCountries[$_SESSION['LAST_COUNTRY']]['UF_USE_VAT'];
    
    
    $basketCoupon = [];
    $GLOBALS['LIQUID_IN_ORDER']=false;
    foreach ($arResult['ORDER']->calculatedOrder['BASKET_ITEMS'] as $prod){
        //Исключение для жидкостей - определение жидкости
        if ($GLOBALS['LIQUID_IN_ORDER']==false) {
            $db_props = CIBlockElement::GetProperty(1, $prod['PROPS']['PARENT_ID'], array("sort" => "asc"), Array("CODE"=>"LIQUID"));
            if ($ob = $db_props->GetNext())
            {
                $VALUE = $ob['VALUE_ENUM'];
            }
            if ($VALUE=='Y') {
                $GLOBALS['LIQUID_IN_ORDER']=true;
            }
        }
        //Исключение для жидкостей - определение жидкости
        
        if ($prod['DISCOUNT_COUPON'] !== ''){
            $basketCoupon[$prod['DISCOUNT_VALUE']] = $prod['DISCOUNT_COUPON'];
        }
    }
    
    $itemsPriceCurrent = 0;
    $itemsPriceOld = 0;
 
    
    //При рассчете у нас 2 варианта: 
    //1. Пересчитать значения
    //2. Просто высчитать множитель VAT
    //т.к. с VAT вечная путаница - сначала просто все подсчитаем.
    $total_array=[];
    $item_array=[];
    //подсчет данных и формирование массива для вывода
    foreach($arResult["AVAILABLE"] as $arItem){
//        echo "<pre>";
//        print_r($arItem);
//        echo "</pre>";
//        $priceDiff = $arItem['PROPS']['BASE_PRICE_P'] - $arItem['PRICE'];

        if ($useVAT=="Y") { //кейс, есть VAT Цены укаываем С НДС
            $item_array['PRICE_QUANTITY']=round($arItem['PRICE']*$arItem['QUANTITY'],2);
            $item_array['OLD_PRICE_QUANTITY']=round($arItem['PROPS']['BASE_PRICE_P']*$arItem['QUANTITY'],2);
            $item_array['VAT']=$item_array['PRICE_QUANTITY']-round($item_array['PRICE_QUANTITY'] / ($arItem['VAT_RATE']+1),2);
        } else { //кейс, когда нету VAT цены считаем БЕЗ НДС
            $item_array['PRICE_QUANTITY']=round($arItem['PRICE']/($arItem['VAT_RATE']+1)*$arItem['QUANTITY'],2);
            $item_array['OLD_PRICE_QUANTITY']=round(($arItem['PROPS']['BASE_PRICE_P'] / ($arItem['VAT_RATE']+1)*$arItem['QUANTITY']),2);
            $item_array['VAT']=0;
        }
        
        if($arItem['PROPS']['BASE_PRICE_P'] > $arItem['PRICE']) {
            $item_array['PRICE_HAS_DISCOUNT']='price--has-discount';
        } else {
            $item_array['PRICE_HAS_DISCOUNT']='';
        }
        
        $total_array[$arItem['ID']]=$item_array;
        if (isset($total_array['TOTAL']['PRICE_TOTAL'])) {
            $total_array['TOTAL']['PRICE_TOTAL']+=$item_array['PRICE_QUANTITY'];
        } else {
            $total_array['TOTAL']['PRICE_TOTAL']=$item_array['PRICE_QUANTITY'];
        }
        
        if (isset($total_array['TOTAL']['DISCOUNT'])) {
            $total_array['TOTAL']['DISCOUNT']+=$item_array['OLD_PRICE_QUANTITY']-$item_array['PRICE_QUANTITY'];
        } else {
            $total_array['TOTAL']['DISCOUNT']=$item_array['OLD_PRICE_QUANTITY']-$item_array['PRICE_QUANTITY'];
        }
        
        if (isset($total_array['TOTAL']['VAT'])) {
            $total_array['TOTAL']['VAT']+=$item_array['VAT'];
        } else {
            $total_array['TOTAL']['VAT']=$item_array['VAT'];
        }
        if (isset($total_array['TOTAL']['TOTAL'])) {
            $total_array['TOTAL']['TOTAL']+=$item_array['PRICE_QUANTITY'];
        } else {
            $total_array['TOTAL']['TOTAL']=$item_array['PRICE_QUANTITY'];
        }
    }
//    echo '<pre>';
//    print_r ($total_array);
//    echo '</pre>';
//    die();

    function convertCurrentFunc($value)
    {
        switch (KDXCurrency::$CurrentCurrency){
            case 'RUB': $priceCurrency = '₽'; break;
            case 'USD': $priceCurrency = '$'; break;
            case 'GBP': $priceCurrency = '₤'; break;
            case 'EUR': default: $priceCurrency = '€'; break;
        }
        if (KDXCurrency::$CurrentCurrency == 'RUB'){
            return number_format($value, 0, '.', ' ').' '.$priceCurrency;
        } else {
            return $priceCurrency.number_format($value, 2, '.', ' ');
        }
    }
    ?>
<div id="checkout_wrap">
<div class="grid-row">
    <div class="col-xl-offset-1 col-xl-10">
        <form name="RELOAD" action="" method="get" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap" data-validation-function="validate_form_eng_char"></form>
        <div class="js_cart__wrapper cart__wrapper--visible-desktop">
            <h1><?=GetMessage('корзина')?></h1>
            <section class="cart-section">
                <div class="cart-section__scroll-wrapper">
                    <form data-ajax-response-wrapper="#checkout_wrap" method="post" class="ajax_load" name="CART" action="">
                        <input type="hidden" value="Y" name="CART">
                        <?foreach($arResult["AVAILABLE"] as $arItem){
                                $img = CFile::ResizeImageGet($arItem['PROPS']['PREVIEW_PICTURE'],array('width'=>400,'height'=>400), BX_RESIZE_IMAGE_EXACT);
                                $availableCNT = KDXCart::getAvailableQuantityByProduct($arItem['PRODUCT_ID']);
                            ?>
                            <div class="cart__item">
                                <a href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>" class="cart__image-wrapper">
                                    <picture>
                                        <source srcset="<?=$img['src']?> 1x, <?=$img['src']?> 2x">
                                        <img class="lazyload img--lazyload" src="" data-src="<?=$img['src']?>" alt="<?=$arItem['NAME']?>" data-object-fit="contain">
                                    </picture>
                                </a>
                                <div class="cart__props-wrapper">
                                    <div class="cart__props">
                                        <div class="cart__props-left">
                                            <a class="link link--primary cart__title" href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
                                                <div class="cart__brand"><?=$arItem['NAME']?></div>
                                            </a>
                                        </div>
                                        <?
                                        $buyMore1 = 0;
                                        $resProps = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>1, '=ID'=>$arItem['PROPS']['PARENT_ID']), false, array(), array('ID', 'IBLOCK_ID', 'PROPERTY_CANT_ORDER_MORE_ONE'));
                                        if ($arProp = $resProps->Fetch()){
                                            if ($arProp['PROPERTY_CANT_ORDER_MORE_ONE_VALUE'] == 'Да'){
                                                $buyMore1 = 1;
                                            }
                                        }
                                        ?>
                                        <div class="cart__props-right">
                                            <div class="cart__spinner js_number-spinner amount" data-spn_max="<?=KDXCart::getAvailableQuantityByProduct($arItem['PRODUCT_ID'])?>">
                                                <button class="cart__spinner-btn cart__spinner-btn--minus js_item_minus" type="button" data-spn_dir="dwn" data-spn_active="false" <?if(intval($arItem['QUANTITY'] == 1)){?>disabled=""<?}?>>
                                                </button>
                                                <input class="form-input cart__spinner-input amount_numb" type="text" data-spn_input name="QUANTITY[<?=$arItem['ID']?>]" value="<?=intval($arItem['QUANTITY'])?>" data-max="<?=$buyMore1 == 1 ? 1 : $availableCNT?>" maxlength="3"/>
                                                <button class="cart__spinner-btn cart__spinner-btn--plus js_item_plus" type="button" data-spn_dir="up" data-spn_active="true" <?if(intval($arItem['QUANTITY'] == $availableCNT) || $buyMore1 == 1){?>disabled=""<?}?>>
                                                </button>
                                            </div>
                                            <div class="cart__size">
                                                <span class="cart__size-text"><?=GetMessage('CART_SIZE')?>:</span>
                                                <span><?=$arItem['PROPS']['SIZE'] === 'ONE_SIZE' ? 'One Size' : $arItem['PROPS']['SIZE']?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="cart__price item_price">
                                        <div class="price__wrapper">
                                            <div class="price <?=$total_array[$arItem['ID']]['PRICE_HAS_DISCOUNT']?>">
                                                <span class="price__block">
                                                    <span class="price--current">
                                                        <?=KDXCurrency::convertAndFormat($total_array[$arItem['ID']]['PRICE_QUANTITY'], KDXCurrency::$CurrentCurrency);?>
                                                    </span>
                                                </span>
                                                <?if ($total_array[$arItem['ID']]['PRICE_HAS_DISCOUNT']!==''){?>
                                                    <span class="price--old">
                                                        <span>
                                                            <?=KDXCurrency::convertAndFormat($total_array[$arItem['ID']]['OLD_PRICE_QUANTITY'], KDXCurrency::$CurrentCurrency);?>
                                                        </span>
                                                    </span>
                                                <?}?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cart__delete kdxDeleteFromCart" data-product-id="<?=$arItem['ID']?>">
                                        <svg class="icon icon-cross_pop-up">
                                            <use xlink:href="/local/templates/kit_new/resources/svg/app.svg#cross_pop-up"></use>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        <?}?>
                        <?
                        foreach($arResult["UNAVAILABLE"] as $arItem){
                            $img = CFile::ResizeImageGet($arItem['PROPS']['PREVIEW_PICTURE'],array('width'=>400,'height'=>400), BX_RESIZE_IMAGE_EXACT)?>
                            <div class="cart__item cart__item--unavailable">
                                <a href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>" class="cart__image-wrapper">
                                    <picture>
                                        <source srcset="<?=$img['src']?> 1x, <?=$img['src']?> 2x">
                                        <img class="lazyload img--lazyload" src="" data-src="<?=$img['src']?>" alt="<?=$arItem['NAME']?>" data-object-fit="contain">
                                    </picture>
                                </a>
                                <div class="cart__props-wrapper">
                                    <div class="cart__props">
                                        <div class="cart__props-left">
                                            <a class="link link--primary cart__title" href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
                                                <div class="cart__brand"><?=$arItem['NAME']?></div>
                                            </a>
                                        </div>
                                        <div class="cart__props-right">
                                            <div class="cart__msg--unavailable"><?=GetMessage('нет в наличии')?></div>
                                        </div>
                                    </div>
                                    <div class="cart__delete kdxDeleteFromCart" data-product-id="<?=$arItem['ID']?>">
                                        <svg class="icon icon-cross_pop-up">
                                            <use xlink:href="/local/templates/kit_new/resources/svg/app.svg#cross_pop-up"></use>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        <?}?>
                        <div>
                            <button class="btn btn--primary btn--inline-lg btn--block-md checkout__btn--step lg_btn half_buttom kdxClearCart"><?=GetMessage('Очистить корзину')?></button>
                        </div>
                    </form>

                    <? if(false && KDXCurrency::format(1) != KDXCurrency::convertAndFormat(1,KDXCurrency::$CurrentCurrency)){
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
            </section>
        </div>
        <section class="checkout-section">
            <div class="grid-row">
                <div class="col-xl-5 col-xl-push-7">
                    <div class="grid-row">
                        <div class="col-xl-offset-1 col-xl-11">
                            <div class="grid-row">
                                <div class="col-md-12">
                                    <div class="checkout-promo js_checkout-promo">
                                        <form action="">
                                            <div class="form-row form-row--flex">
                                                <div class="form-row__block">
                                                    <input type="text" id="kdxCouponCode" name="COUPON" placeholder="<?=GetMessage('CP_KO_COUPON2')?>" class="form-input promo_input"/>
                                                    <!--<input type="submit"  value="--><?//=GetMessage('CP_KO_APPLY_COUPON')?><!--" class="grey_btn promo_btn kdxApplyCoupon"/>-->
                                                    <button class="btn btn--arrow-right kdxApplyCoupon" type="submit">
                                                        <svg class="icon icon-arrow-right">
                                                            <use xlink:href="/local/templates/kit_new/resources/svg/app.svg#arrow-right"></use>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <?if (!empty($basketCoupon)){?>
                                                    <div class="form-row__block" style="flex-wrap: wrap">
                                                        <?foreach ($basketCoupon as $percent => $coupon){?>
                                                            <p class="form-row__succes"><?=$coupon.' - '.$percent?></p><br>
                                                        <?}?>
                                                    </div>
                                                <?}?>
                                                <div class="form-row__block">
                                                    <p class="form-row__error coupon_error error_text"><?=GetMessage('CP_KO_ERROR_COUPON')?></p>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="checkout-summary__table">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <?if ($useVAT=='N'){?>
                                                    <?=GetMessage('CP_KO_PRICE')?>
                                                <?} else {?>
                                                    <?=GetMessage('CP_KO_PRICE_EU')?>
                                                <?}?>
                                            </td>
                                            <td>
                                                <div class="price__wrapper price">
                                                    <div class="price">
                                                        <span class="price__block">
                                                                <span class="price--current">
                                                                <?=KDXCurrency::convertAndFormat($total_array['TOTAL']['PRICE_TOTAL'],KDXCurrency::$CurrentCurrency);?>
                                                                </span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?if ($total_array['TOTAL']['PRICE_TOTAL'] >= 0):?>
                                            <tr>
                                                <td><?=GetMessage('CP_KO_DISCOUNT')?>:</td>
                                                <td>
                                                    <div class="price__wrapper discount">
                                                        <div class="price">
                                                            <span class="price__block">
                                                                <span class="price--current">
                                                                    <?=KDXCurrency::convertAndFormat($total_array['TOTAL']['DISCOUNT'],KDXCurrency::$CurrentCurrency);?>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?endif?>
                                        <?/*if($USER->IsAuthorized()){*/?>
                                            <?if($arResult['ORDER']->delivery_id){?>
                                            <?
//                                            echo '<pre>';
//                                            print_r ($arResult['ORDER']);
//                                            echo '</pre>';
//                                            die();
                                            ?>
                                                <tr>
                                                    <td><?=GetMessage('CP_KO_DELIVERY')?> (<?=$arResult['DELIVERY']->name?>):</td>
                                                    <td>
                                                        <div class="price__wrapper delivery">
                                                            <div class="price">
                                                            <span class="price__block">
                                                                <span class="price--current">
                                                                    <?=KDXCurrency::convertAndFormat($arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'],KDXCurrency::$CurrentCurrency);?>
                                                                </span>
                                                            </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?}?>
                                            <tr>
                                                <?if (isset($arResult['VAT'])):?>
                                                <td>
                                                    <?foreach($arResult['VAT'] as $key=>$value):?>
                                                        <?if($value > 0):?>
                                                            <p><?=GetMessage('VAT'.strval($key))?>:</p>
                                                        <?endif;?>
                                                    <?endforeach;?>
                                                </td>
                                                <?else:?>
                                                    <td><?=GetMessage('VAT')?>:</td>
                                                <?endif;?>
                                                <td>
                                                    <div class="price__wrapper vat">
                                                        <div class="price">
                                                            <span class="price__block">
                                                                <span class="price--current">
                                                                    <!-- VATtest -->
                                                                    <?//if($arResult['ORDER']->add_vat){
                                                                    //echo KDXCurrency::convertAndFormat($arResult['ORDER']->vat_price,KDXCurrency::$CurrentCurrency);
                                                                    //}else{
                                                                    //echo KDXCurrency::convertAndFormat(0,KDXCurrency::$CurrentCurrency);
                                                                    //}
                                                                    ?>
                                                                    <?if (!$arResult['ORDER']->add_vat){?>
                                                                        <?foreach($arResult['VAT'] as $key => $value):?>
                                                                            <?if($value > 0):?>
                                                                                <p><?=KDXCurrency::convertAndFormat(0, KDXCurrency::$CurrentCurrency);?></p>
                                                                            <?endif;?>
                                                                        <?endforeach;?>
                                                                    <?} else {?>
                                                                        <?if (isset($arResult['VAT'])):?>
                                                                            <?foreach($arResult['VAT'] as $key => $value):?>
                                                                                <?if($value > 0):?>
                                                                                    <p><?=KDXCurrency::convertAndFormat($value,KDXCurrency::$CurrentCurrency);?></p>
                                                                                <?endif;?>
                                                                            <?endforeach;?>
                                                                        <?endif;?>
                                                                        <?//KDXCurrency::convertAndFormat($arResult['ORDER']->vat_price,KDXCurrency::$CurrentCurrency);?>
                                                                    <?}?>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?/*}*/?>

                                        <tr class="checkout-summary__table-total">
                                            <td><?=GetMessage('CP_KO_TOTAL')?>:</td>
                                            <td>
                                                <div class="price__wrapper">
                                                    <div class="price">
                                                        <span class="price__block">
                                                            <span class="price--current">
                                                                <?=KDXCurrency::convertAndFormat($total_array['TOTAL']['TOTAL']+$arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'], KDXCurrency::$CurrentCurrency);?>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?if($arResult['NEED_CONVERT']){?>
                                        <?
                                            $itemsPriceCurrentEur = 0;
                                            foreach($arResult["AVAILABLE"] as $arItem){
                                                $itemsPriceCurrentEur = $itemsPriceCurrentEur + KDXCurrency::convert($useVAT=="N" ? ($arItem['PRICE'] / 1.21) * $arItem['QUANTITY'] : $arItem['PRICE'] * $arItem['QUANTITY'], 'EUR');
                                            }
                                        ?>
                                            <tr class="checkout-summary__table-total">
                                                <td><?=GetMessage('CP_KO_TOTAL_IN_EURO')?>:</td>
                                                <td>
                                                    <div class="price__wrapper">
                                                        <div class="price">
                                                            <span class="price__block">
                                                                <span class="price--current">
                                                                    <?=KDXCurrency::format($itemsPriceCurrentEur + $arResult['ORDER']->calculatedOrder['PRICE_DELIVERY']);?>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?}?>
                                        </tbody>
                                    </table>
                                    <?//text after total https://pyrus.com/t#id81614179
//                                    echo '<pre>';
//                                    print_r (KDXCurrency::$CurrentCurrency);
//                                    echo '</pre>';
                                    if ((LANGUAGE_ID=='ru') and (KDXCurrency::$CurrentCurrency=='RUB')) {?>
                                        <div>
                                            Оплата будет производится в Евро по внутреннему курсу банка держателя карты. Цены в рублях носят ориентировочный характер.
                                            <a href='/help/delivery-and-payment/' target='_blank'>Подробнее</a>
                                        </div>
                                    <?
                                    }
                                    
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-7 col-xl-pull-5">
                    <!--<button class="btn js_checkout-trigger btn--primary checkout__trigger" type="button">
                        Оформить заказ
                    </button>-->
                    <div class="checkout__wrapper js_checkout__wrapper">

                        <?
                            if(!empty($arResult["AVAILABLE"]))
                            {
                                //echo '<div class="checkout_title">'.GetMessage('оформление').'</div>';

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

            </div>
        </section>


    </div>
</div><!-- grid-row -->
</div><!-- checkout_wrap -->

<style>
    #checkout_map { width: 520px; height: 350px; }
</style>
<!--<script src="https://maps.googleapis.com/maps/api/js"></script>-->
<script type="text/javascript">
    BX.message['MAX_QUANTITY_ADDED']='<?=GetMessage('MAX_QUANTITY_ADDED')?>';

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

    //google.maps.event.addDomListener(window, 'load', initialize_map);


    $(function(){
        $(document).on('kdxInitPlugins',function(){

            //$('.js_cas_trigger').on('click', function(){
                //$('.cas_list').show();
            //});

            //initialize_map();

            //tabs();
        });
    });
    $(document).ready(function() {
        $("input[name='DELIVERY']").each(function() {
            if($(this).attr("checked")) {
                console.log($(this).attr("value"));
                switch ($(this).attr("value")) {
                    case 'kdx_dhl:courier':
                        $(".delivery_info").show();
                        break;
                    case 'kdx_fedex:courier':
                        $(".delivery_info").show();
                        break;
                    case 'kdx_ems:courier':
                        $(".delivery_info").show();
                        break;
                    case 'kdx_dpd:courier':
                        $(".delivery_info").show();
                        break;
                    default:
                        $(".delivery_info").hide();
                        break;
                }
                return;
            }
        });
        $("input[name='DELIVERY']").click(function() {
            switch ($(this).attr("value")) {
                case 'kdx_dhl:courier':
                    $(".delivery_info").show();
                    break;
                case 'kdx_fedex:courier':
                    $(".delivery_info").show();
                    break;
               case 'kdx_ems:courier':
                    $(".delivery_info").show();
                    break;
                case 'kdx_dpd:courier':
                    $(".delivery_info").show();
                    break;
                default:
                    $(".delivery_info").hide();
                    break;
            }
        });
    });

    function checkOutInfoDataLayer() {
        dataLayer.push({
            event: 'eec.checkout',
            ecommerce: {
                checkout: {
                    actionField: {
                        step: 1
                    },
                    products: [
                    <?foreach($arResult["AVAILABLE"] as $arItem):?>
                    <?
                        $newName = str_replace("'", "", $arItem['NAME']);
                        $brandId = null;
                        $iterator = CIBlockElement::GetProperty(1, $arItem['PROPS']['PARENT_ID'],array(),array('ID' => 1));
                        while ($row = $iterator->Fetch())
                        {
                          $brandId = $row['VALUE'];
                        }

                        if (!is_null($brandId)) {
                            $brands = '';
                            $dbBrands = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>3,"ID"=>$brandId));
                            while($arBrand = $dbBrands->GetNext())
                            {
                                $brands = $arBrand['NAME'];
                                break;
                            }
                        }

                        $dbGroups = CIBlockElement::GetElementGroups($arItem['PROPS']['PARENT_ID'], true)->Fetch();
                        $dbSection = CIBlockSection::GetList(array(), array("CODE" => $dbGroups['CODE']), false, array("ID"))->Fetch();
                        $chain = CIBlockSection::GetNavChain(1,$dbSection["ID"]);
                        $i = 0;
                        $path = '/catalog/';
                        while ($chainItem =  $chain->GetNext()) {
                            if ($i == 0) {
                                $path .= $chainItem['CODE'];
                            } else {
                                $path .= '/' . $chainItem['CODE'];
                            }
                            $i++;
                        }
                    ?>
                    {
                        id: '<?=$arItem["ID"]?>',        
                        price: '<?=$arItem["PRICE"]?>',       
                        name: '<?=$newName?>',     
                        category: '<?=$path?>',
                        brand: '<?=$brands?>',                
                        quantity: <?=intval($arItem["QUANTITY"])?>,           

                    },
                    <?endforeach?>
                    ]
                }
            }
        })
    }
    checkOutInfoDataLayer();
</script>