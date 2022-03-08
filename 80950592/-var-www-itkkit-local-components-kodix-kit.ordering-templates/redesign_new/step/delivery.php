<div class="checkout__step checkout__step--completed">

	<?
		$APPLICATION->IncludeComponent('kodix:personal.addresses','checkout_info_new');
	?>
</div>
<!-- add class active for current section -->
<div class="checkout__step checkout__step--current checkout_section active">
	<div class="checkout__step-header">
		<div class="checkout__step-heading1">2. <?=GetMessage('Доставка')?></div>
		<div class="delivery_info"><?=GetMessage['DELIVERY_COST_INFO']?></div>
<!--		<button class="btn link link--secondary checkout__step-edit js_checkout-edit" type="button">Изменить</button>-->
	</div>

       
	<div class="checkout__step-content">
		<div class="tab__head">
			<a class="tab__link" href="#" data-tab-name="product" data-tab-for="1" data-tab-current="true"><?=GetMessage('Доставка по адресу')?></a>
			<a class="tab__link js_map-trigger" href="#" data-tab-name="product" data-tab-for="2"><?=GetMessage('Самовывоз')?></a>
		</div>
		<div class="tab__body">
			<div class="tab__content" data-tab-name="product" data-tab-id="1" data-tab-show="true">
				<?
				$APPLICATION->IncludeComponent('kodix:personal.addresses','new_add_address_new');
				?>
			</div>

			<div class="tab__content" data-tab-name="product" data-tab-id="2" data-tab-show="false">
				<div class="checkout__map" id="styled_map" data-marker="56.950475, 24.111249"></div>
				<div class="checkout__map-details">
					<div><?=GetMessage('ADDRESS_RIGA_18')?></div>
					<div><?=GetMessage('TIME_WORK_18')?></div>
				</div>
				<div class="checkout__map-msg"><?=GetMessage('IN_SHOP_RIGA')?></div>

				<form name="SELF" action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap">
					<input type="hidden" name="DELIVERY" value="kdx_self:courier" class="form_f_rad_v1">
					<input type="submit" class="btn btn--primary btn--inline-lg btn--block-md checkout__btn--step" value="<?=GetMessage('Продолжить')?>">
				</form>
			</div>

		</div>
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