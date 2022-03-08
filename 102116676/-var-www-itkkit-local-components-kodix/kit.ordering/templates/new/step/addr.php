<?
	$lastProfileID = KDXAddress::getLastAddressId();

	$Qprofile = CSaleOrderUserProps::GetList(array(), array("USER_ID" => CUser::GetId(), 'NAME'=>'self'), false, array('nTopCount' => 1));
	$Rprofile = $Qprofile->Fetch();
	$profileSelf = $Rprofile['ID'];
?>
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
			<li data-tab="1" class="tabs_item<?=$lastProfileID?' selected':''?>"><?=GetMessage('Доставка по адресу')?></li>
			<li data-tab="2" class="tabs_item<?=!$lastProfileID?' selected':''?>"><?=GetMessage('Самовывоз')?></li>
		</ul>
		<div class="tabs_contents_block">
			<ul class="tabs_contents">
				<li data-content="1" class="tabs_c_item<?=$lastProfileID?' selected':''?>">
					<div class="tabs_c_hold content">
						<div class="checkout_sub_title"><?=GetMessage('Адрес доставки')?></div>
						<div class="checkout_addr_select">
							<div class="cas_selected_value js_cas_trigger">
								<?=KDXAddress::makeShortAddress($arResult['ADDRESSES'][$lastProfileID]['DELIVERY'],'DELIVERY')?>
							</div>
							<form data-ajax-response-wrapper="#checkout_wrap" class="ajax_load" method="post" action="" name="PROFILE">
								<ul class="cas_list">
									<?
										foreach($arResult['ADDRESSES'] as $key=>$value)
										{
											if($key == $profileSelf) continue;
											?>
											<li class="cas_item js_cas_item">
												<label>
													<input type="radio" class="form_f_rad_v1" <?=$key == $lastProfileID ? 'checked=""' : ''?> id="radio_<?=$key?>" value="<?=$key?>" name="PROFILE">
													<?=KDXAddress::makeShortAddress($value['DELIVERY'],'DELIVERY')?>
												</label>
												<span class="cas_edit">
													<a href="/checkout/?action=delivery&id=<?=$key?>" data-ajax-response-wrapper="#checkout_wrap" class="ajax_load" style="display: block;width: 100%; height: 100%;"></a>
												</span>
											</li>
									<?	}
									?>
								</ul>
							</form>
						</div>
						<div class="checkout_new_addr js_new_addr">
							<a href="/checkout/?action=delivery" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap"><?=GetMessage('Новый адрес')?></a>
						</div>

						<div class="checkout_separator"></div>
						<div class="checkout_sub_title"><?=GetMessage('Службы доставки')?></div>
						<?if(!$arResult['IS_DELIVERIES_AVAILABLE']){?>
							<?if(!$arResult['ORDER']->profile_id){?>
								<p><?=GetMessage('CP_KO_CHOOSE_PROFILE')?></p>
							<?}else{?>
								<p><?=GetMessage('CP_KO_NO_DELIVERY_TO_PROFILE')?></p>
							<?}?>
						<?
						}else{
						?>
                                              
						<form name="DELIVERY" action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap">
							<?foreach($arResult['DELIVERIES'] as $arDelivery){?>
								<?foreach($arDelivery['PROFILES'] as $profile => $arProfileParams){
									if($arDelivery['SID'] == 'kdx_self') continue;
									?>
								<div class="checkout_sec_row">
									<div class="check_item_v1">
										<input type="radio" name="DELIVERY" value="<?=$arDelivery['SID']?>:<?=$profile?>" id="radio_<?=$arDelivery['SID']?>:<?=$profile?>" <?if($arResult['ORDER']->delivery_id == $arDelivery['SID'].':'.$profile){?>checked=""<?}?> class="form_f_rad_v1">
										<label for="radio_<?=$arDelivery['SID']?>:<?=$profile?>" class="form_lbl_rad_v1" title="<?=$arProfileParams['TITLE']?>"><?=$arDelivery['NAME']?> - <?if($arProfileParams['PRICE']['VALUE']){?><?=KDXCurrency::convertAndFormat($arProfileParams['PRICE']['VALUE'],KDXCurrency::$CurrentCurrency,$arDelivery['BASE_CURRENCY'])?><?}else{?><span class="red_txt"><b><?=GetMessage('FREE_SHIP')?></b></span><?}?></label>
									</div>
								</div>
								<?}?>
							<?}?>
						</form>
						<?}?>
                        <div><?=GetMessage("CP_ORDER_NOTE_1")?><a href="/help/delivery/" style="font-weight: 800"><?=GetMessage("CP_ORDER_NOTE_2")?></a></div>
                    </div>
				</li>

				<li data-content="2" class="tabs_c_item<?=!$lastProfileID?' selected':''?>">
					<div class="tabs_c_hold">
						<div class="checkout_map_info">
							<span class="checkout_map_marker"></span>
							<div><b><?=GetMessage('ADDRESS_RIGA_18')?></b></div>
							<div><?=GetMessage('TIME_WORK_18')?></div>
						</div>
						<div id="checkout_map"></div>
						<div class="checkout_map_note">
							<i>!</i><?=GetMessage('IN_SHOP_RIGA')?>
						</div>
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