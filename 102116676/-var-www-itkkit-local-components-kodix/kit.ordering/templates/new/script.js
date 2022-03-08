/**
 * Created by:  KODIX 03.04.2015 12:53
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
$(function(){

	$(document).on('change','input[type=radio]',function(e){
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
			},3000);
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
})