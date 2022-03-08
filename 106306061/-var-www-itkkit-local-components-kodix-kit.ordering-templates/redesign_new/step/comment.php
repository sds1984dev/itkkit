<?
$lastProfileID = KDXAddress::getLastAddressId();
?>
<div class="checkout__step checkout__step--completed">

	<?
	$APPLICATION->IncludeComponent('kodix:personal.addresses','checkout_info_new');
	?>
</div>
<div class="checkout__step checkout__step--completed">
	<div class="checkout__step-header">
		<div class="checkout__step-heading">2. <?=GetMessage('Доставка')?></div>
		<a class="btn link link--secondary checkout__step-edit ajax_load" data-ajax-response-wrapper="#checkout_wrap" href="/checkout/?unset=DELIVERY"><?=GetMessage('Изменить')?></a>
	</div>
	<div class="checkout__step-summary">
		<?
		if($arResult['ORDER']->delivery_id == 'kdx_self:courier') echo GetMessage('Самовывоз').'<br />';
		else echo $arResult['DELIVERY']->name.'<br />';
		echo KDXAddress::makeShortAddress($arResult['ADDRESSES'][$lastProfileID]['DELIVERY'],'DELIVERY');
		?>
	</div>
</div>
<div class="checkout__step checkout__step--completed">
	<div class="checkout__step-header">
		<div class="checkout__step-heading">3. <?=GetMessage('Оплата')?></div>
		<a class="btn link link--secondary checkout__step-edit ajax_load" data-ajax-response-wrapper="#checkout_wrap" href="/checkout/?unset=PAYSYSTEM"><?=GetMessage('Изменить')?></a>
	</div>
	<div class="checkout__step-summary">
		<?
			$paySystem = KDXPaySystem::getList();
			echo GetMessage('CP_KO_PAY_NAME_'.$arResult['ORDER']->pay_system_id);
		?>
	</div>
</div>
<!-- add class active for current section -->
<div class="checkout__step checkout__step--current checkout_section active">
	<div class="checkout__step-header">
		<div class="checkout__step-heading">4. <?=GetMessage('Комментарий')?></div>
	</div>
        <?
        //добавление блока https://pyrus.com/t#id81614179 шаг 2
        
        
        
        
//        echo '<pre>';
//        print_r ($itemsPriceCurrentEur);
//        echo '</pre>';
//        
//        echo '<pre>';
//        print_r ($arResult['ORDER']->delivery_id);
//        echo '</pre>';
        
        if (LANGUAGE_ID=='ru') {
            $itemsPriceCurrentEur = 0;
            foreach($arResult["AVAILABLE"] as $arItem){
                $itemsPriceCurrentEur = $itemsPriceCurrentEur + KDXCurrency::convert($useVAT=="N" ? $arItem['PRICE'] / 1.21 : $arItem['PRICE'], 'EUR');
            }
            $nds_rus=round($itemsPriceCurrentEur*0.21, 2);
            $order_sum=$itemsPriceCurrentEur + $arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'];
            
        ?>
        <div class="checkout__step-content">
            <p>
                <?if ($useVAT=='N') {?>
                    Вычет НДС в размере <?=$nds_rus?> Евро произведен.<br>
                <?}?>
                <?if ($order_sum>200) {
                    $duty=round(($order_sum-200)*0.15, 2);
                    $address_delivery=KDXAddress::makeShortAddress($arResult['ADDRESSES'][$lastProfileID]['DELIVERY'],'DELIVERY');
                    if (stripos($address_delivery,'Россия')!==false) {?>
                        Пошлина при доставке составит <?=$duty?> Евро.
                        <?if (stripos($arResult['ORDER']->delivery_id,'kdx_fedex')!==false) {?>
                            Фиксированная стоимость услуг брокера 500 рублей.
                        <?}?>
                        
                        <a href="/help/delivery-and-payment/" target="_blank">Подробнее</a>
                    <?}?>    
                <?}?>
            </p>
        </div>
        <?
        }
        ?>
	<div class="checkout__step-content">
		<div class="form-row">
			<form data-ajax-response-wrapper="#checkout_wrap" class="ajax_load not_show_preloader" method="post" action="" name="COMMENT">
				<textarea name="COMMENT" id="comment" class="form-input form-input--textarea" placeholder="<?=GetMessage('Комментарий к заказу')?>"><?=$arResult['ORDER']->comment?></textarea>
			</form>
		</div>

		<table class="checkout-summary__table ninja--md ninja--lg">
			<tbody>
			<?/*<tr>
				<td><?=GetMessage('CP_KO_PRICE')?>:</td>
				<td>
					<div class="price__wrapper">
						<div class="price">
							<span class="price__block">
								<span class="price--current">
									<?
                                    $price = $arResult['CART']->getPriceAvailable();
                                    if(!$arResult['ORDER']->add_vat){
                                        $price -= $arResult['ORDER']->vat_price;
                                    }
                                    ?>
                                    <?=KDXCurrency::convertAndFormat($price,KDXCurrency::$CurrentCurrency);?>
								</span>
							</span>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td><?=GetMessage('CP_KO_DISCOUNT')?>:</td>
				<td>
					<div class="price__wrapper">
						<div class="price">
							<span class="price__block">
								<span class="price--current">
								     <?
                                     $discount = $arResult['CART']->getPriceAvailable() - $arResult['ORDER']->calculatedOrder['ORDER_PRICE'];
                                     //if(!$arResult['ORDER']->add_vat){
                                     //    $discount -= $arResult['ORDER']->vat_price;
                                     //}
                                     ?>
                                     <?=KDXCurrency::convertAndFormat($discount,KDXCurrency::$CurrentCurrency);
                                     ?>
                                </span>
							</span>
						</div>
					</div>
				</td>
			</tr>
			<?if($USER->IsAuthorized()){?>
				<?if($arResult['ORDER']->delivery_id){?>
					<tr>
						<td><?=GetMessage('CP_KO_DELIVERY')?> (<?=$arResult['DELIVERY']->name?>):</td>
						<td>
							<div class="price__wrapper">
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
					<td>VAT:</td>
					<td>
						<div class="price__wrapper">
							<div class="price">
								<span class="price__block">
									<span class="price--current">
                                            <?//if($arResult['ORDER']->add_vat){
                                                echo KDXCurrency::convertAndFormat($arResult['ORDER']->vat_price,KDXCurrency::$CurrentCurrency);
//                                                }else{
//                                                    echo KDXCurrency::convertAndFormat(0,KDXCurrency::$CurrentCurrency);
//                                                }
                                            ?>
									</span>
								</span>
							</div>
						</div>
					</td>
				</tr>
			<?}*/?>

			<?/*<tr class="checkout-summary__table-total">
				<td><?=GetMessage('CP_KO_TOTAL')?>:</td>
				<td style="text-align: right;">
					<div class="price__wrapper">
						<div class="price">
							<span class="price__block">
								<span class="price--current">
									<?if (!$arResult['ORDER']->add_vat){?>
                                        <?=KDXCurrency::convertAndFormat(($arResult['CART']->getPriceAvailable() / 1.21) + $arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'], KDXCurrency::$CurrentCurrency);?>
                                    <?} else {?>
                                        <?=KDXCurrency::convertAndFormat($arResult['ORDER']->calculatedOrder['ORDER_PRICE'] + $arResult['ORDER']->calculatedOrder['PRICE_DELIVERY'], KDXCurrency::$CurrentCurrency);?>
                                    <?}?>
								</span>
							</span>
						</div>
					</div>
				</td>
			</tr>
			<?if($arResult['NEED_CONVERT']){?>
				<tr class="checkout-summary__table-total">
					<td><?=GetMessage('CP_KO_TOTAL_IN_EURO')?>:</td>
					<td style="text-align: right;">
						<div class="price__wrapper">
							<div class="price">
								<span class="price__block">
									<span class="price--current">
										<?=KDXCurrency::format($arResult['ORDER']->calculatedOrder['ORDER_PRICE'] + $arResult['ORDER']->calculatedOrder['PRICE_DELIVERY']);?>
									</span>
								</span>
							</div>
						</div>
					</td>
				</tr>
			<?}?>*/?>

			</tbody>
		</table>

		<form name="ORDER" action="" class="ajax_load" method="post" data-ajax-response-wrapper="#checkout_wrap" data-validation-function="validateOrdering">
			<input type="hidden" name="CREATE_ORDER" value="">
			<input type="hidden" name="STEP" value="<?=$_SESSION["ORDERING"]["STEP"]?>">
			<input style="width: 100%" type="submit" class="btn btn--primary btn--inline-lg btn--block-md js-confirm-order" value="<?=GetMessage('Оформить заказ')?>"/>
		</form>

        <div><?=GetMessage("CP_KO_AGREEMENT")?></div>
	</div>

</div>