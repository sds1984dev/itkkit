<?
	$lastProfileID = KDXAddress::getLastAddressId();

	$Qprofile = CSaleOrderUserProps::GetList(array(), array("USER_ID" => CUser::GetId(), 'NAME'=>'self'), false, array('nTopCount' => 1));
	$Rprofile = $Qprofile->Fetch();
	$profileSelf = $Rprofile['ID'];
?>
<div class="checkout__step checkout__step--completed">

	<?
	$APPLICATION->IncludeComponent('kodix:personal.addresses','checkout_info_new');
	?>
</div>
<!-- add class active for current section -->
<div class="checkout__step checkout__step--current checkout_section active">
	<div class="checkout__step-header">
		<div class="checkout__step-heading">2. <?=GetMessage('Доставка')?></div>
		
	</div>
	<div class="checkout__step-content">
		<div class="tab__head">
			<a class="tab__link" href="#" data-tab-name="product" data-tab-for="1" data-tab-current="true"><?=GetMessage('Доставка по адресу')?></a>
			<a class="tab__link js_map-trigger" href="#" data-tab-name="product" data-tab-for="2"><?=GetMessage('Самовывоз')?></a>
		</div>
		<div class="tab__body">
			<div class="tab__content" data-tab-name="product" data-tab-id="1" <?if($lastProfileID){?>data-tab-show="true"<?}?>>
				<div class="checkout__step-subheading checkout__step-subheading--sm-margin"><?=GetMessage('Адрес доставки')?></div>
				<div class="form-row">
					<form data-ajax-response-wrapper="#checkout_wrap" class="checkout__address-select ajax_load" method="post" action="" name="PROFILE">
						<select name="PROFILE" class="chosen-select js-chosen-select form-input">
							<?foreach($arResult['ADDRESSES'] as $key=>$value)
							{
								//if($key == $profileSelf) continue;
								?>
								<option class="form_f_rad_v1" <?=$key == $lastProfileID ? 'selected="selected"' : ''?> id="radio_<?=$key?>" value="<?=$key?>" >
									<?=KDXAddress::makeShortAddress($value['DELIVERY'],'DELIVERY')?>
								</option>

							<?}?>
						</select>
					</form>
					<div class="grid-row">
						<div class="col-sm-6" data-toggle-id="address">
							<a href="/checkout/?action=delivery&id=<?=$lastProfileID?>" data-ajax-response-wrapper="#checkout_wrap" class="btn link link--primary js-address-edit checkout__address-edit ajax_load">
								<?=GetMessage('EDIT_ADDRESS')?>
							</a>
						</div>
						<div class="col-sm-6">
							<div class="text--right js_new_addr">
<!--								<a class="btn link link--primary link--plus checkout__address-edit" href="#" data-toggle-for="address" data-toggle-id="address">-->
<!--									Новый адрес-->
<!--								</a>-->
								<a href="/checkout/?action=delivery" class="btn link link--primary link--plus checkout__address-edit ajax_load" data-ajax-response-wrapper="#checkout_wrap">
									<?=GetMessage('Новый адрес')?>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="checkout__step-subheading checkout__step-subheading--sm-margin"><?=GetMessage('Службы доставки')?></div>
				<div class="checkout__radio-group">
					<div class="grid-row">
						<!--<div class="col-md-6">-->
							<?if(!$arResult['IS_DELIVERIES_AVAILABLE']){?>
                                                            <?if(!$arResult['ORDER']->profile_id){?>
                                                                    <p><?=GetMessage('CP_KO_CHOOSE_PROFILE')?></p>
                                                            <?}else{?>
                                                                    <p><?=GetMessage('CP_KO_NO_DELIVERY_TO_PROFILE')?></p>
                                                            <?}?>
                                                            <?
							} else {
                                                            $selectedDelivery = $arResult["SELECTED_DELIVERY"] ?: $arResult['FIRST_DELIVERY'];                                                   
								?>
                                                                <?
//                                                                echo '<pre>';
//                                                                print_r ($arResult['DELIVERIES']);
//                                                                echo '</pre>';
                                                                ?>
								<form name="DELIVERY" action="" method="post" class="ajax_load" data-ajax-response-wrapper="#checkout_wrap">
									<?foreach($arResult['DELIVERIES'] as $arDelivery){?>
										<?foreach($arDelivery['PROFILES'] as $profile => $arProfileParams){
                                                                                    //Исключение для жидкостей
//                                                                                    echo '<pre>';
//                                                                                    var_dump ($GLOBALS['LIQUID_IN_ORDER']);
//                                                                                    echo '</pre>';
                                                                                    
                                                                                    $radio_disabled='';
                                                                                    $message_disabled='';
                                                                                    $radio_checked='';
                                                                                    if ($GLOBALS['LIQUID_IN_ORDER']){ //если есть жидкость в заказе (см. \itkkit\local\components\kodix\kit.ordering\templates\redesign_new\template.php) 35 строку
                                                                                        if ($arProfileParams['TITLE']=='FedEx') { //отключаем FedEX и пишем надпись
                                                                                            $radio_disabled='disabled';
                                                                                            $message_disabled=LANGUAGE_ID == 'en' ? 'Liquids are not allowed for the delivery' : 'Доставка жидкостей недоступна';
                                                                                        }
                                                                                    } else {
                                                                                        if ($arProfileParams['TITLE']=='EMS') continue; //Если жидкости в заказе нет - не показывать EMS
                                                                                        if ($arProfileParams['TITLE']=='FedEx') $radio_checked='checked';
                                                                                    }
                                                                                    //Исключение для жидкостей
                                                                                    
											if ($arDelivery['SID'] == 'kdx_self') continue;
											?>
											<label for="radio_<?=$arDelivery['SID']?>:<?=$profile?>" class="radio" title="<?=$arProfileParams['TITLE']?>">
												<input type="radio" name="DELIVERY" value="<?=$arDelivery['SID']?>:<?=$profile?>" id="radio_<?=$arDelivery['SID']?>:<?=$profile?>" <?if($selectedDelivery == $arDelivery['SID'].':'.$profile){?>checked=""<?}?> class="radio__input" <?=$radio_disabled //Исключение для жидкостей?> <?=$radio_checked?>>
												<div class="radio__icon"></div>
												<div class="radio__label">
													<?=$arDelivery['NAME']?> - <?if($arProfileParams['PRICE']['VALUE']){?>
														<?=KDXCurrency::convertAndFormat($arProfileParams['PRICE']['VALUE'],KDXCurrency::$CurrentCurrency,$arDelivery['BASE_CURRENCY'])?>
													<?}else{?>
														<span class="red_txt"><b><?=GetMessage('FREE_SHIP')?></b></span>
													<?}
                                                                                                        
                                                                                                         //добавление сроков доставки https://pyrus.com/t#id81614179
                                                                                                         $message_days=LANGUAGE_ID == 'en' ? declension($arProfileParams['PRICE']['TRANSIT'], 'working days','working days','working days') : declension($arProfileParams['PRICE']['TRANSIT'], 'рабочий день','рабочих дня','рабочих дней');
                                                                                                         echo '('.$message_days.')';
                                                                                                         //Исключение для жидкостей
                                                                                                         echo ' '.$message_disabled;
                                                                                                        ?>
                                                                                                </span>
                                                                                                        
                                                                                                       <? //добавление сроков доставки https://pyrus.com/t#id81614179?>
												</div>
											</label>
										<?}?>
									<?}?>

									<?if(isset($_SESSION['SHOW_DELIVERY_MESSAGE'])):?>
										<?if($_SESSION['SHOW_DELIVERY_MESSAGE'] == true):?>
											<div class="delivery_info"><?=GetMessage('DELIVERY_COST_INFO')?></div>
										<?endif?>
										<?unset($_SESSION['SHOW_DELIVERY_MESSAGE']);?>
									<?endif?>

									<?if(isset($_SESSION['SHOW_DELIVERY_DECKS_MESSAGE'])):?>
										<?if($_SESSION['SHOW_DELIVERY_DECKS_MESSAGE'] == true):?>
											<div class="delivery_info"><?=GetMessage('DELIVERY_COST_DECKS_INFO')?></div>
										<?endif?>
										<?unset($_SESSION['SHOW_DELIVERY_DECKS_MESSAGE']);?>
									<?endif?>

                                    <div class="txt_center">
                                        <button type="submit" class="btn btn--primary btn--inline-lg btn--block-md checkout__btn--step lg_btn"><?=GetMessage('Продолжить')?></button>
                                    </div>
								</form>
							<?}?>
						<!--</div>-->
					</div>
				</div>

			</div>

			<div class="tab__content" data-tab-name="product" data-tab-id="2" data-tab-show=<?=!$lastProfileID ? "true" : "false"?>>
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
<script>
    var val = $('select[name=PROFILE]').find(":selected").val();

    $.ajax({
        method: "POST",
        url: "getCountryCode.php",
        data: {ID : val},
        dataType: "text"
    }).done(function(data) {

        let iconFlag = $(".header-action__dd-for > .icon-flag");
        let arCurrentClassFlag = iconFlag.attr("class").split(" ");
        let newClassName = "icon-flag-" + data;
        let currentClassName = arCurrentClassFlag[1];

        if (data == '' || data == 'Fail') {
            newClassName = "icon-flag-LV";
        }

        iconFlag.removeClass(currentClassName).addClass(newClassName).next('span').html(data);

    })
</script>