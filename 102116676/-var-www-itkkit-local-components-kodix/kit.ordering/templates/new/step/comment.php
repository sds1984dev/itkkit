<?
$lastProfileID = KDXAddress::getLastAddressId();
?>
<div class="checkout_section done">
		<?
			$APPLICATION->IncludeComponent('kodix:personal.addresses','checkout_info');
		?>
</div>
<div class="checkout_section done">
	<div class="checkout_sec_title">2. <?=GetMessage('Доставка')?></div>
	<a class="checkout_edit_link ajax_load" data-ajax-response-wrapper="#checkout_wrap" href="/checkout/?unset=DELIVERY"><?=GetMessage('Изменить')?></a>
	<div class="checkout_sec_info">
		<?
		if($arResult['ORDER']->delivery_id == 'kdx_self:courier') echo 'Самовывоз';
		else echo KDXAddress::makeShortAddress($arResult['ADDRESSES'][$lastProfileID]['DELIVERY'],'DELIVERY');
		?>
	</div>
</div>
<div class="checkout_section done">
	<div class="checkout_sec_title">3. <?=GetMessage('Оплата')?></div>
	<a class="checkout_edit_link ajax_load" data-ajax-response-wrapper="#checkout_wrap" href="/checkout/?unset=PAYSYSTEM"><?=GetMessage('Изменить')?></a>
	<div class="checkout_sec_info">
		<?
			$paySystem = KDXPaySystem::getList();
			echo GetMessage('CP_KO_PAY_NAME_'.$arResult['ORDER']->pay_system_id);
		?>
	</div>
</div>
<!-- add class active for current section -->
<div class="checkout_section active">
	<div class="checkout_sec_title">4. <?=GetMessage('Комментарий')?></div>
	<div class="checkout_sec_row">
		<?=GetMessage('Вы можете добавить комментарий к заказу')?>
	</div>
	<div class="checkout_sec_row">
		<form data-ajax-response-wrapper="#checkout_wrap" class="ajax_load not_show_preloader" method="post" action="" name="COMMENT">
			<textarea name="COMMENT" id="comment" class="full_width f_field_v1 textarea" placeholder="<?=GetMessage('Комментарий к заказу')?>"><?=$arResult['ORDER']->comment?></textarea>
		</form>
	</div>
	<div class="txt_center">
		<form name="ORDER" action="" class="ajax_load" method="post" data-ajax-response-wrapper="#checkout_wrap" data-validation-function="validateOrdering">
			<input type="hidden" name="CREATE_ORDER" value="">
			<input type="hidden" name="STEP" value="<?=$_SESSION["ORDERING"]["STEP"]?>">
			<input type="submit" class="btn" value="<?=GetMessage('Оформить заказ')?>"/>
		</form>
	</div>
</div>