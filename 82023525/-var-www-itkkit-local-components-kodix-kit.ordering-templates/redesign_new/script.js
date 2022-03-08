/**
 * Created by:  KODIX 03.04.2015 12:53
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
$(function(){

	$(document).on('change','form[name=PAYSYSTEM] input[type=radio]',function(e){
		if($(document[this.name]).length > 0)
			$(document[this.name]).submit();
	})

	$(document).on('keyup paste','textarea[name=COMMENT]',function(e){
		if($(document['COMMENT']).length > 0)
		{
			clearTimeout($.commentTimeOut);

			$.commentTimeOut = setTimeout(function(){
				//$(document['COMMENT']).submit();
				if(typeof $.commentAjax != 'undefined')
					$.commentAjax.abort();

				$.commentAjax = $.post(
					document['COMMENT'].action,
					{
						COMMENT: document['COMMENT']['COMMENT'].value
					}
				)
			},200);
		}
	})

	$(document).on('keyup paste change','form[name=CART] input',function(e){
		if($(document['CART']).length > 0)
		{
			if(parseInt(this.value) > $(this).attr('data-max'))
				this.value = $(this).attr('data-max');
			if(parseInt(this.value) <= 0)
				this.value = 1;
			clearTimeout($.quantityTimeOut);
			$.quantityTimeOut = setTimeout(function(){
				$(document['CART']).submit();
			},500);
		}
	});

	$(document).on('kdxCartApplyCoupon',function(e, jqXHR, code){

		data = {};
		if(typeof jqXHR.responseJSON != 'undefined')
			data = jqXHR.responseJSON;
		else
			data = JSON.parse(jqXHR.responseText);

		if(data.STATUS == 'OK')
		{
			$(document.RELOAD).submit()
		}
		else
		{
			$('.coupon_error').show()
			clearTimeout($.coupErrorTm);
			$.coupErrorTm = setTimeout(function(){
				$('.coupon_error').hide()
			},3000);
		}

	});

	$(document).on('kdxCartRemoveItem',function(e, jqXHR, record_id){

		data = {};
		if(typeof jqXHR.responseJSON != 'undefined')
			data = jqXHR.responseJSON;
		else
			data = JSON.parse(jqXHR.responseText);

		if(data.STATUS == 'OK')
		{
			$("[data-product-id=" + record_id + "]").closest(".cart_item").remove();
			$(document.RELOAD).submit()
		}
		else
		{

		}
	})

	window.validateOrdering = function(FORM){

		if($(FORM.STEP).length == 0)
		{
			console.debug('field "STEP" is missed');
			return false;
		}

		if($(FORM.CREATE_ORDER).length == 0)
		{
			console.debug('field "CREATE_ORDER" is missed');
			return false;
		}

		switch(parseInt(FORM.STEP.value))
		{
			case 1:
				KDX.Informers.addWarning(BX.message('CP_KO_CHOOSE_PROFILE'));
				return false;
				break;
			case 2:
				KDX.Informers.addWarning(BX.message('CP_KO_CHOOSE_DELIVERY'));
				return false;
				break;
			case 3:
				KDX.Informers.addWarning(BX.message('CP_KO_CHOOSE_PAY_SYSTEM'));
				return false;
				break;
			case 4:
				FORM.CREATE_ORDER.value = 'Y';
				break;
			default :
				return false;
				break;

		}
	}

	$(".chosen-select").change(function() {

		let val = $(".chosen-single > span");

		$.ajax({
			method: "POST",
			url: "getCountryCode.php",
			data: {COUNTRY_NAME_RU : val[0].outerText},
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
		});
	});

	$(document).on('change', 'select[name=PROFILE]', function(){
		
		var val = $(this).find(":selected").val();

		$('.js-address-edit.checkout__address-edit').attr('href', '/checkout/?action=delivery&id='+val+'');
		$('form[name=PROFILE]').submit();

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

			iconFlag.removeClass(currentClassName).addClass(newClassName);
			iconFlag.next('span').html(data);
		})

	});

	// counter fot cart-items

	$(document).on('click', ".js_item_minus", function(e){
		e.preventDefault();
		var $amountCnt = $(this).closest('.amount').find('.amount_numb');
		var $new_amount = $amountCnt.val();
		$new_amount = $amountCnt.val() == "" ? 0 : parseInt($amountCnt.val()) - 1;
		$new_amount = $new_amount < 1 ? 0: $new_amount;
		//$amountCnt.val($new_amount);
		$amountCnt.trigger('change');
	});

	$(document).on('click', ".js_item_plus", function(e){
		e.preventDefault();
		var $amountCnt = $(this).closest('.amount').find('.amount_numb');
		var $max_amount = $amountCnt.attr('data-max-value');
		var $new_amount = $amountCnt.val();
		$new_amount = $amountCnt.val() == "" ? 0 : parseInt($amountCnt.val()) + 1;
		if($max_amount < $new_amount)
			$new_amount = $max_amount;
		//$amountCnt.val($new_amount);
		$amountCnt.trigger('change');
	});

	$(document).on('keypress', ".amount .amount_numb", function (e) {
		//if the letter is not digit then  don't type anything
		if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			return false;
		}
	});

// end counter fot cart-items
});

var confirmOrderEvent = function(e){
	$(this).hide();
	$(this).closest('form').append('<div class="ajax-preloader"><span></span></div>');
    $(this).closest('form').find('.ajax-preloader').css({'width':$(this).width(),'height':$(this).height()+2});
}
$(document).on('click', '.js-confirm-order', confirmOrderEvent);