/**
 * Created by:  KODIX 20.03.2015 14:43
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
$(function(){
    $(document).on('change','.form_select_v1',function(){
        var productID = parseInt(this.value);
        $('.kdxAddToCart').attr('data-product-id','');

        if(productID > 0){
            $('.kdxAddToCart').attr('data-product-id',productID);

            $('.pr_f_price div[data-product-price]').hide();
            $('.pr_f_price div[data-product-price="'+productID+'"]').show();
        }
    })

    //selectStyling('.form_select_v1', 'mes_select', BX.message('CHOOSE_SIZE'));
    selectStyling('.form_select_v1', 'mes_select');
    $('.form_select_v1').trigger('change');

    $(document).on('kdxCartAddItem', function(){
        var rq = KDX.getQueryVariable('rq');
        if (rq!==undefined){
            try {rrApi.recomAddToCart(KDXSale.PROD_ID, {methodName: rq})} catch (e) {}
        }
    });
});