
<div class="checkout__step checkout__step--current">
	<div class="checkout__step-header">
		<div class="checkout__step-heading">1. <?=GetMessage('Данные о покупателе')?></div>
		<button class="btn link link--secondary checkout__step-edit js_checkout-edit" type="button">
			Изменить
		</button>
	</div>
	<div class="checkout__step-content">
		<div data-tab-name="checkout-auth" data-tab-id="2">
			<form data-ajax-response-wrapper=".modal_box_3 .block_entry" method="post" class="ajax_load" action="/local/app/auth.php?login=yes">
				<input type="hidden" value="Y" name="AUTH_FORM">
				<input type="hidden" value="AUTH" name="TYPE">
				<div class="checkout__auth-toggle-wrapper">
					<span><?=GetMessage('NO_ACCOUNT')?></span>
					<a href="/checkout/?action=reg" data-ajax-response-wrapper="#checkout_wrap" class="link link--secondary ajax_load checkout_auth_btn"><?=GetMessage('Регистрация')?></a>
				</div>
				<div class="grid-row">
					<div class="col-md-6">
						<div class="form-row form-row--lg-gap">
							<input type="email" class="form-input" placeholder="E-mail" name="USER_LOGIN">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-row form-row--lg-gap">
							<input type="password" placeholder="<?=GetMessage('Пароль')?>" class="form-input" name="USER_PASSWORD" id="USER_PASSWORD">
						</div>
					</div>
				</div>
				<div class="checkout__sign-in-actions">
					<label for="check_1" name="USER_REMEMBER" id="USER_REMEMBER" class="checkbox check_label">
						<input class="checkbox__input" id="check_1" type="checkbox" name="remember-user" checked>
						<div class="checkbox__icon"></div>
						<div class="checkbox__label">
							<?=GetMessage('Запомнить меня')?>
							<span class="checkbox__label"></span>
						</div>
					</label>
					<a href="/personal/?forgot_password=yes" title="<?=GetMessage('Забыли пароль?')?>" class="link link--secondary" class="forgot_link"><?=GetMessage('Забыли пароль?')?></a>
				</div>

				<div class="txt_center">
					<button class="btn btn--primary btn--inline-lg checkout__btn--step lg_btn">
						<?=GetMessage('Войти')?>
					</button>
				</div>
				<?=$APPLICATION->arAuthResult['MESSAGE'];?>
			</form>
		</div>
	</div>
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