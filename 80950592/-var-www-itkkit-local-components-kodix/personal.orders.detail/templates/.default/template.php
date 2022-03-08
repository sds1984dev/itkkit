<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="container_4 cart_reload">
    <div class="grid_2">

        <h2 class="special"><b>Заказ № <?=$arResult['ORDER']->id?></b> (<?=substr($arResult['ORDER']->date_insert,0,10)?>)</h2>

        <div class="bordered">


            <ul class="cart_product_list simple">
                <?foreach($arResult['ORDER']->cart->getAvailable() as $id=>$item){?>
                    <li>
                        <div class="item clearfix">
                            <a class="img" href="<?=$item['PROPS']["DETAIL_PAGE_URL"]?>"><img alt="<?=$item['NAME']?>" src="<?if(file_exists($_SERVER['DOCUMENT_ROOT'].$item['PROPS']['PREVIEW_PICTURE'])){echo $item['PROPS']['PREVIEW_PICTURE'];}else{echo SITE_TEMPLATE_PATH.'/img/no_photo.png';}?>"></a>
                            <div class="description_wrap">
                                <a class="title" href="<?=$item['PROPS']["DETAIL_PAGE_URL"]?>"><?=$item['NAME']?></a>
                                <div class="articul">Арт.: <?=$item['PROPS']['CML2_ARTICLE']?></div>
                                <div class="descriprion">Цвет: <?=$item['PROPS']['COLOR']?></div>
                                <div class="descriprion">Размер: <?=$item['PROPS']['SIZE']?></div>
                                <div class="descriprion">
                                    <?=intval($item["QUANTITY"])?> шт.
                                </div>
                            </div><!--/description_wrap-->
                            <div class="price_wrap">

                                <div class="price"><?=KDXCurrency::format($item["PRICE"])?></div>
                                <?if($item['PRICE'] < $item['PROPS']['BASE_PRICE_P'] || intval($item['DISCOUNT_PRICE'])){?>
                                    <div class="price old"><?=KDXCurrency::format($item['PROPS']['BASE_PRICE_P'])?></div>
                                <?}?>
                                <?if(intval($item['DISCOUNT_PRICE'])){?>
                                    <?if(trim($item['DISCOUNT_COUPON'])){?>
                                        <div class="price fs12">(-<?=$item["DISCOUNT_VALUE"]?> по коду)</div>
                                    <?}else{?>
                                        <div class="price fs12">(-<?=$item["DISCOUNT_VALUE"]?> по карте)</div>
                                    <?}?>
                                <?}?>

                            </div><!--/price_wrap-->

                        </div><!--/item-->
                    </li>
                <?}?>
            </ul>

            <?/*
            <!-- тут все варианты сразу, но используется только один -->

            <div class="blue_bottom promocode ninja" <?if($_POST['BAD_COUPON']==1){?>style="display: none;"<?}?>>
                Есть промо-код? Введите его здесь:

                <div class="promoform input28">
                    <form>
                        <input type="text" value="<?=$_SESSION["CATALOG_USER_COUPONS"][0]?>"  id="kdxCouponCode">
                        <input type="button" class="compact marginleft10 kdxApplyCoupon" value="Применить">
                    </form>
                </div>

            </div><!--/blue_bottom-->

            <div class="blue_bottom promocode red ninja" <?if($_POST['BAD_COUPON']!=1){?>style="display: none;"<?}?>>

                Указанный вами промо-код не найден. <a href="#" class="toggle_ninja">Ввести другой код</a>

            </div><!--/blue_bottom-->*/?>
            <?if($arResult['ORDER']->discount_value>0){?>
                <div class="blue_bottom promocode">

                    <i class="ico blue_check"></i> Вы получаете скидку <?=$arResult['ORDER']->discount_value?>

                </div><!--/blue_bottom-->
            <?}?>

        </div><!--/bordered-->

        <div class="cart_total">
            <?
            $total_price=$arResult['ORDER']->cart->getPriceAvailable();
            $total_price_with_discount = $arResult['ORDER']->cart->getAvailablePriceWithDiscount();
            $total_discount = $total_price-$total_price_with_discount;
            ?>
            <div class="row">
                <div>Стоимость товаров:</div>
                <div><?=KDXCurrency::format($total_price);?></div>
            </div><!--/row-->
            <?if($total_discount>0){?><div class="row">
                <div>Скидка:</div>
                <div><?=KDXCurrency::format($total_discount);?></div>
                </div><!--/row-->
                <div class="row">
                    <div>Стоимость со скидкой:</div>
                    <div><?=KDXCurrency::format($total_price_with_discount);?></div>
                </div><!--/row-->
            <?}?>
            <div class="row">
                <div>Доставка:</div>
                <div id="kdx-order-delivery-price"><?=KDXCurrency::format($arResult['ORDER']->price_delivery);?></div>
            </div><!--/row-->
            <div class="row">
                <div>Итого:</div>
                <div id="kdx-order-total-price"><b><?=KDXCurrency::format($total_price_with_discount+$arResult['ORDER']->price_delivery);?></b></div>
            </div><!--/row-->

        </div><!--/cart_total-->

    </div>
    <div class="grid_2">
        <table class="order_status">

            <tr>
                <th>Статус заказа</th>
                <td><?=$arResult["STATUSES"][$arResult['ORDER']->status_id]["NAME"]?></td>
            </tr>
            <tr>
                <th>Получатель</th>
                <td><?=(trim($arResult['ORDER']->properties['CONTACT_NAME']['VALUE'].' '.$arResult['ORDER']->properties['CONTACT_LAST_NAME']['VALUE'])?:($arResult['ORDER']->user_name.' '.$arResult['ORDER']->user_last_name))?></td>
            </tr>
            <?if($arResult['ORDER']->properties['STREET']['VALUE']){?>
            <tr>
                <th>Адрес</th>
                <td>
                    <?=$arResult['ORDER']->properties['COUNTRY']['VALUE']?>, <?=$arResult["CITIES"][$arResult['ORDER']->properties['CITY']['VALUE']]?>, <?=$arResult['ORDER']->properties['STREET']['VALUE']?>,<br>
                    д/стр.. <?=$arResult['ORDER']->properties['HOUSE']['VALUE']?>, <?=($arResult['ORDER']->properties['CORPUS']['VALUE']?($arResult['ORDER']->properties['CORPUS']['VALUE'].', '):'')?> кв./офис <?=$arResult['ORDER']->properties['FLAT']['VALUE']?><br>
                    <?=$arResult['ORDER']->properties['PHONE']['VALUE']?>
                </td>
            </tr>
            <?}elseif($arResult['ORDER']->properties['PICKUP_POINT_XML_ID']['VALUE']){?>
                <tr>
                    <th>Пункт самовывоза</th>
                    <td>
                        <?=$arResult['PICKUP_POINT']['PROPERTY_ADDRESS_VALUE']?>
                    </td>
                </tr>
            <?}?>
            <tr>
                <th>Способ доставки</th>
                <td><?=($arResult["DELIVERY"][$arResult['ORDER']->delivery_id]?:$arResult['ORDER']->delivery_id)?></td>
            </tr>
            <?/*<tr>
                <th>Дата доставки</th>
                <td>-<?//todo делать калькуляцию через класс доставки?></td>
            </tr>*/?>
            <tr>
                <th>Способ оплаты</th>
                <td><?=$arResult['PAYMENT'][$arResult['ORDER']->pay_system_id]['NAME']?></td>
            </tr>
            <?if($arResult['ORDER']->canceled == 'N'){?>
            <tr>
                <th>Статус оплаты</th>
                <td>
                    <?if($arResult['ORDER']->payed == 'Y'){?>
                    Оплачен
                    <?}else{
                        ?>Не оплачен<br><?
                        showPayButton($arResult['ORDER']);
                    }?>
                </td>
            </tr>
            <?}?>
            <?if($arResult['ORDER']->payed == 'Y'){?>
            <tr>
                <th>Дата оплаты</th>
                <td><?=$arResult['ORDER']->date_payed?></td>
            </tr>
            <?}?>

        </table>

    </div>
</div>