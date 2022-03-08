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
			if($arResult['ORDER']->delivery_id == 'kdx_self:courier') echo GetMessage('Самовывоз').'<br />';
			else echo $arResult['DELIVERY']->name.'<br />';
			echo KDXAddress::makeShortAddress($arResult['ADDRESSES'][$lastProfileID]['DELIVERY'],'DELIVERY');
		?>
	</div>
</div>
<!-- add class active for current section -->
<div class="checkout_section active">
	<div class="checkout_sec_title">3. <?=GetMessage('Оплата')?></div>
	<form name="PAYSYSTEM" action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap">
	<div class="checkout_sec_row">
		<?foreach($arResult['PAY_SYSTEMS'] as $arPaySysten){?>
            <?if(($arPaySysten['ID'] == 4) && ($USER->GetID() != 3672)) continue;?>
			<div class="check_item_v1">
				<input type="radio" name="PAYSYSTEM" value="<?=$arPaySysten['ID']?>" id="PAYSYSTEM_<?=$arPaySysten['ID']?>" <?if($arPaySysten['ID'] == $arResult['ORDER']->pay_system_id){?>checked<?}?> class="form_f_rad_v1">
				<label for="PAYSYSTEM_<?=$arPaySysten['ID']?>" class="form_lbl_rad_v1"><?=GetMessage('CP_KO_PAY_NAME_'.$arPaySysten['ID'])?></label>
			</div>
		<?}?>
	</div>
    <div><?=GetMessage("CP_ORDER_NOTE_1")?><a href="/help/delivery/" style="font-weight: 800"><?=GetMessage("CP_ORDER_NOTE_2")?></a></div>
	</form>
	<div class="checkout_separator"></div>
	<form method="post" id="kdx_edit_addr" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap"  data-validation-function="isValid">
		<input type="hidden" id="" name="PAYSYSTEM" value="<?=$arResult['ORDER']->pay_system_id?>"/>
		<input type="hidden" id="" name="DELIVERY" value="<?=$arResult['ORDER']->delivery_id?>"/>
		<input type="hidden" id="" name="NEXT_COMMENT" value="1"/>
	<?
		$APPLICATION->IncludeComponent('kodix:personal.addresses','new_payment', array(
			'pay_system_id' => $arResult['ORDER']->pay_system_id
		));
	?>
	</form>
</div>
<div class="checkout_section">
	<div class="checkout_sec_title">4. <?=GetMessage('Комментарий')?></div>
</div>