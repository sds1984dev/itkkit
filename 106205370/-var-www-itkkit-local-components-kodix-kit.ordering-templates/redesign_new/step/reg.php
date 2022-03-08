<!-- add class active for current section -->
<div id="block_register">
	<?
		$APPLICATION->IncludeComponent('kodix:personal.registration','checkout_new',
			array('FIELDS'=>array('NAME','LAST_NAME','PERSONAL_PHONE','EMAIL')),
			false
		);
	?>
</div>
<div class="checkout__step">
	<div class="checkout__step-header">
		<div class="checkout__step-heading">2. <?=GetMessage('Доставка')?></div>
	</div>
</div>

<div class="checkout__step">
	<div class="checkout__step-header">
		<div class="checkout__step-heading">3. <?=GetMessage('Оплата')?></div>
	</div>
</div>

<div class="checkout__step">
	<div class="checkout__step-header">
		<div class="checkout__step-heading">4. <?=GetMessage('Комментарий')?></div>
	</div>
</div>