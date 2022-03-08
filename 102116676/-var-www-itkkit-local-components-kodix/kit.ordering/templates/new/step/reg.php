<!-- add class active for current section -->
<div id="block_register">
	<?
		$APPLICATION->IncludeComponent('kodix:personal.registration','checkout',
			array('FIELDS'=>array('NAME','LAST_NAME','PERSONAL_PHONE','EMAIL')),
			false
		);
	?>
</div>
<div class="checkout_section">
	<div class="checkout_sec_title">2. <?=GetMessage('Доставка')?></div>
</div>
<div class="checkout_section">
	<div class="checkout_sec_title">3. <?=GetMessage('Оплата')?></div>
</div>
<div class="checkout_section">
	<div class="checkout_sec_title">4. <?=GetMessage('Комментарий')?></div>
</div>