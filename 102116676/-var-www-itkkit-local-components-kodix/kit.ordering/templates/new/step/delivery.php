<div class="checkout_section done">

	<?
		$APPLICATION->IncludeComponent('kodix:personal.addresses','checkout_info');
	?>
</div>
<!-- add class active for current section -->
<div class="checkout_section active">
	<div class="checkout_sec_title">2. <?=GetMessage('Доставка')?></div>

	<div class="tabs_block checkout_tabs">
		<ul class="tabs">
			<li data-tab="1" class="tabs_item selected"><?=GetMessage('Доставка по адресу')?></li>
			<li data-tab="2" class="tabs_item"><?=GetMessage('Самовывоз')?></li>
		</ul>
		<div class="tabs_contents_block">
			<ul class="tabs_contents">
				<li data-content="1" class="tabs_c_item selected">
					<div class="tabs_c_hold content">
						<?
							$APPLICATION->IncludeComponent('kodix:personal.addresses','new_add_address');
						?>
					</div>
				</li>

				<li data-content="2" class="tabs_c_item">
					<div class="tabs_c_hold">
						<div class="checkout_map_info">
							<span class="checkout_map_marker"></span>
							<div><b><?=GetMessage('ADDRESS_RIGA_18')?></b></div>
							<div><?=GetMessage('TIME_WORK_18')?></div>
						</div>
						<div id="checkout_map"></div>
						<div class="checkout_map_note"><i>!</i><?=GetMessage('IN_SHOP_RIGA')?></div>
						<form name="SELF" action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap">
							<input type="hidden" name="DELIVERY" value="kdx_self:courier" class="form_f_rad_v1">
							<div class="txt_center"><input type="submit" class="btn lg_btn" value="<?=GetMessage('Продолжить')?>"></div>
						</form>
					</div>
				</li>
			</ul>
		</div>
	</div>



</div>

<div class="checkout_section">
	<div class="checkout_sec_title">3. <?=GetMessage('Оплата')?></div>
</div>
<div class="checkout_section">
	<div class="checkout_sec_title">4. <?=GetMessage('Комментарий')?></div>
</div>