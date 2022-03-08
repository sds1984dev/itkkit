<form data-ajax-response-wrapper=".modal_box_3 .block_entry" method="post" class="ajax_load" action="/local/app/auth.php?login=yes">
	<input type="hidden" value="Y" name="AUTH_FORM">
	<input type="hidden" value="AUTH" name="TYPE">
	<!-- add class active for current section -->
	<div class="checkout_section active">
		<div class="checkout_sec_title">1. <?=GetMessage('Данные о покупателе')?></div>
		<a href="/checkout/?action=reg" data-ajax-response-wrapper="#checkout_wrap" class="ajax_load checkout_auth_btn"><?=GetMessage('Регистрация')?></a>
		<div class="checkout_sec_row mt">
			<div class="checkout_sec_column_1">
				<input type="email" class="f_field_v1" placeholder="E-mail" name="USER_LOGIN">
			</div>
			<div class="checkout_sec_column_2">
				<input type="password" placeholder="<?=GetMessage('Пароль')?>" class="f_field_v1" name="USER_PASSWORD" id="USER_PASSWORD">
			</div>
		</div>
		<div class="checkout_sec_row">
			<div class="checkout_sec_column_1">
				<div class="check_item_v1">
					<input type="checkbox" id="check_1" class="form_f_check_v1">
					<label for="check_1" name="USER_REMEMBER" id="USER_REMEMBER" class="form_lbl_check_v1 check_label"><?=GetMessage('Запомнить меня')?></label>
				</div>
			</div>
			<div class="checkout_sec_column_2">
				<div class="txt_right">
					<a href="#" title="<?=GetMessage('Забыли пароль?')?>" class="js_modal_ctrl_1" class="forgot_link"><?=GetMessage('Забыли пароль?')?></a>
				</div>
			</div>

		</div>
		<div class="txt_center">
			<button class="btn lg_btn"><?=GetMessage('Войти')?></button>
		</div>
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
</form>