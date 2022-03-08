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

<div class="checkout__step checkout__step--current checkout_section active">
	<div class="checkout__step-header">
		<div class="checkout__step-heading">3. <?=GetMessage('Оплата')?></div>
	</div>

	<div class="checkout__step-content">
		<form name="PAYSYSTEM" action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap">
            <?
            $selectedPayment = $arResult['ORDER']->pay_system_id ?: current($arResult['PAY_SYSTEMS'])['ID'];
            ?>
			<?foreach($arResult['PAY_SYSTEMS'] as $arPaySysten){?>
				<?if($arPaySysten['ID'] == 4) continue;?>
				<?
				global $USER;
				if ($arPaySysten['ID'] == 5 && $USER->GetID() == 403099){?>
					<label for="PAYSYSTEM_<?=$arPaySysten['ID']?>" class="radio">
						<input type="radio" name="PAYSYSTEM" value="<?=$arPaySysten['ID']?>" id="PAYSYSTEM_<?=$arPaySysten['ID']?>" <?if($arPaySysten['ID'] == $selectedPayment){?>checked<?}?> class="radio__input">
						<div class="radio__icon"></div>
						<div class="radio__label"><?=GetMessage('CP_KO_PAY_NAME_'.$arPaySysten['ID'])?></div>
					</label>
				<?} else {?>
					<label for="PAYSYSTEM_<?=$arPaySysten['ID']?>" class="radio">
						<input type="radio" name="PAYSYSTEM" value="<?=$arPaySysten['ID']?>" id="PAYSYSTEM_<?=$arPaySysten['ID']?>" <?if($arPaySysten['ID'] == $selectedPayment){?>checked<?}?> class="radio__input">
						<div class="radio__icon"></div>
						<div class="radio__label"><?=GetMessage('CP_KO_PAY_NAME_'.$arPaySysten['ID'])?></div>
					</label>
				<?}?>
			<?}?>
		</form>
		<form method="post" id="kdx_edit_addr" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap"  data-validation-function="isValid">
			<input type="hidden" id="" name="PAYSYSTEM" value="<?=$selectedPayment?>"/>
<?
		print_r('<pre class="SKdebug" style="display:none;">');
		print_r($arResult['ORDER']);
		print_r('</pre>');
?>
			<input type="hidden" id="" name="DELIVERY" value="<?=$arResult['ORDER']->delivery_id?>"/>
			<input type="hidden" id="" name="NEXT_COMMENT" value="1"/>
			<?
			$APPLICATION->IncludeComponent('kodix:personal.addresses','new_payment_new', array(
				'pay_system_id' => $arResult['ORDER']->pay_system_id
			));
			?>
		</form>
	</div>
</div>

<div class="checkout__step">
	<div class="checkout__step-header">
		<div class="checkout__step-heading">4. <?=GetMessage('Комментарий')?></div>
	</div>
</div>