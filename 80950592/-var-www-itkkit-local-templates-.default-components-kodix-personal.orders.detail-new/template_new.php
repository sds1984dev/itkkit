<?php
/**
 * Date: 28.03.2015
 * Time: 18:20
 */
?>
<?
echo "<pre>";
print_r($arResult);
echo "</pre>";
$vat_price = $arResult['ORDER']->vat_price;
$total_price= $arResult['ORDER']->price - $arResult['ORDER']->price_delivery + $arResult['ORDER']->discount_value;
$total_price_with_discount = $total_price - $arResult['ORDER']->discount_value;
$total_discount = $total_price-$total_price_with_discount;

$orderFields = CSaleOrder::GetByID($arResult['ORDER']->id);
$tracking_number = ($orderFields['TRACKING_NUMBER'] ? $orderFields['TRACKING_NUMBER'] : "");
?>
<div class="grid-row">
    <div class="col-xl-offset-1 col-xl-10">
        <div class="tab__body">
            <div class="tab__content">
<section class="cart-section">
    <div class="cart-section__scroll-wrapper">
        <?foreach($arResult['ORDER']->cart->getAvailable() as $arItem){
            $arImg = CFile::ResizeImageGet($arItem['PROPS']['PREVIEW_PICTURE'],array("width"=>173, "height"=>200));
            ?>
            <div class="cart__item  cart__item--not-editable">
                <a class="cart__image-wrapper" href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>">
                    <picture>
                        <source srcset="<?=CFile::GetPath($arImg["src"])?> 1x, <?=CFile::GetPath($arImg["src"])?> 2x">
                        <img class="lazyload img--lazyload" src=""
                             data-src="<?=CFile::GetPath($arImg["src"])?>" alt="<?=$arItem['NAME']?>" data-object-fit="contain">
                    </picture>
                </a>
                <div class="cart__props-wrapper">
                    <div class="cart__props">
                        <div class="cart__props-left">
                            <a class="link link--primary cart__title" href="<?=$arItem['PROPS']['DETAIL_PAGE_URL']?>">
                                <?=$arItem['PROPS']['BRAND']?$arItem['PROPS']['BRAND'].' - ':''?><?=$arItem['NAME']?>
                            </a>
<!--                            <div class="cart__article">Артикул: <span>49JEHDN93U</span></div>-->
                        </div>
                        <div class="cart__props-right">
                            <div class="cart__quantity">
                                <span><?=intval($arItem['QUANTITY'])?></span>
                            </div>
                            <div class="cart__size">
                                <span class="cart__size-text">Размер:</span>
                                <span><?=$arItem['PROPS']['SIZE']?></span>
                            </div>
                        </div>
                    </div>
                    <div class="cart__price">
                        <div class="price__wrapper">
                            <div class="price <?if($arItem['DISCOUNT_PRICE'] > 0){?>price--has-discount<?}?>">
                                <span class="price__block">
                                    <span class="price--current"><?=KDXCurrency::convertAndFormat($arItem['PRICE'],KDXCurrency::$CurrentCurrency,$arItem['CURRENCY'])?></span>
                                </span>
                                <?if($arItem['DISCOUNT_PRICE'] > 0){?>
                                    <span class="price--old">
                                        <span><?=KDXCurrency::convertAndFormat($arItem['PROPS']['BASE_PRICE_P'],KDXCurrency::$CurrentCurrency,$arItem['CURRENCY'])?></span>
                                    </span>
                                <?}?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?}?>
    </div>
</section>
<?
//echo '<pre>';
//var_dump ($arResult['ORDER']);
//echo '</pre>';
?>
<div class="account-summary__wrapper">
    <div class="grid-row">
        <div class="col-md-6">
            <table class="account-summary__table account-summary__table--sm-margin">
                <tbody>
                    <tr>
                        <td>ID:</td>
                        <td><?=$arResult['ORDER']->id?></td>
                    </tr>
                    <tr>
                        <td><?=GetMessage('ADDRESS_FIELD')?>:</td>
                        <?
                        foreach($arResult['ADDRESS_GROUPS'] as $groupID => $groupCode){
                            if($groupCode == 'DELIVERY'){?>
                                <td><?=KDXAddress::makeShortAddress($arResult['ORDER_PROPS'][ $groupID ],$groupCode)?></td>
                            <?}
                        }?>
                    </tr>
                    <tr>
                        <td><?=GetMessage('CP_OD_DELIVERY')?>:</td>
                        <td><?=$arResult['DELIVERY'][ $arResult['ORDER']->delivery_id ]?></td>
                    </tr>
                    <tr>
                        <td><?=GetMessage('STATUS_FIELD')?>:</td>
                        <td><?=GetMessage('ORDER_STATUS_' . $arResult['ORDER']->status_id)?></td>
                    </tr>
                    <?if($tracking_number){?>
                        <tr>
                            <td><?=GetMessage('CP_OD_TRACKING_NUM')?>:</td>
                            <td><?=$tracking_number?></td>
                        </tr>
                    <?}?>
                </tbody>
            </table>
        </div>
        <div class="col-md-4 col-md-offset-2">
            <table class="account-summary__table">
                <tbody>
                <tr>
                    <td><?=GetMessage('CP_OD_PRICE')?>:</td>
                    <td>
                        <div class="price__wrapper">
                            <div class="price">
                                <span class="price__block">
                                    <?=KDXCurrency::convertAndFormat($total_price,KDXCurrency::$CurrentCurrency,$arResult['ORDER']->currency)?>
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
                <?if($vat_price != 0){?>
                    <tr>
                        <td><?=GetMessage('VAT')?>:</td>
                        <td>
                            <div class="price__wrapper">
                                <div class="price">
                                <span class="price__block">
                                    <?=KDXCurrency::convertAndFormat($vat_price,KDXCurrency::$CurrentCurrency,$arResult['ORDER']->currency);?>
                                </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?=GetMessage('PRICE_NOT_VAT')?>:</td>
                        <td>
                            <div class="price__wrapper">
                                <div class="price">
                                <span class="price__block">
                                    <?=KDXCurrency::convertAndFormat($total_price - $vat_price,KDXCurrency::$CurrentCurrency,$arResult['ORDER']->currency);?>
                                </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?}?>
                <?if($total_discount>0){?>
                    <tr>
                        <td><?=GetMessage('CP_OD_DISCOUNT')?>:</td>
                        <td>
                            <div class="price__wrapper">
                                <div class="price">
                                    <span class="price__block">
                                        <?=KDXCurrency::convertAndFormat($total_discount,KDXCurrency::$CurrentCurrency,$arResult['ORDER']->currency);?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td><?=GetMessage('CP_OD_DISCOUNT_PRICE')?>:</td>
                        <td>
                            <div class="price__wrapper">
                                <div class="price">
                                    <span class="price__block">
                                        <?=KDXCurrency::convertAndFormat($total_price_with_discount,KDXCurrency::$CurrentCurrency,$arResult['ORDER']->currency);?>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?}?>
                <tr>
                    <td><?=GetMessage('CP_OD_DELIVERY')?>:</td>
                    <td>
                        <div class="price__wrapper">
                            <div class="price">
                                <span class="price__block">
                                    <?=KDXCurrency::convertAndFormat($arResult['ORDER']->price_delivery,KDXCurrency::$CurrentCurrency,$arResult['ORDER']->currency);?>
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="account-summary__table-total">
                    <td><?=GetMessage('CP_OD_TOTAL')?>:</td>
                    <td>
                        <div class="price__wrapper">
                            <div class="price">
                                <span class="price__block">
                                    <?=KDXCurrency::convertAndFormat($total_price_with_discount+$arResult['ORDER']->price_delivery,KDXCurrency::$CurrentCurrency,$arResult['ORDER']->currency);?>
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?
    ?>
    <div class="grid-row">
        <div class="col-md-4">
            <a href="../" class="btn btn--secondary ninja--tm ninja--sm"><?=GetMessage('CP_OD_BACK')?></a>
        </div>
        <div class="col-md-4">
            <?if($arResult['ORDER']->payed == 'Y') {?>
                <a href="/bitrix/admin/sale_print.php?doc=waybillnew_2&ORDER_ID=<?=$arResult['ORDER']->id?>" target="_blank" class="btn btn--secondary ninja--tm ninja--sm"><?=GetMessage('SHOW_INVOICE')?></a>
            <?}?>
        </div>
        <?if($arResult['ORDER']->payed == 'N' && $arResult['ORDER']->canceled == 'N'){?>
            <?showPayButton($arResult['ORDER']);?>
        <?}?>
    </div>
</div>
            </div>
        </div>
    </div>
</div>