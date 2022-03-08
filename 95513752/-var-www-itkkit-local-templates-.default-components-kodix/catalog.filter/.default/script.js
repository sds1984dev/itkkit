/**
 * Created by:  KODIX 17.03.2015 15:41
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
$(function(){
    $(document).on('change','form#catalog_filter input',function(){

        $(this).closest('form#catalog_filter').submit();
    })
    $(document).on('kdxPushHistory',function(){
        var new_url =  $('#catalog').eq(0).find('#new_addres_push[data-href]').eq(0).attr('data-href')
        if(new_url.length>2){
            KDX.setPage(new_url,'#catalog','ALL');
        }
    })

    $(document).on('click','.filter_r_item .delete_btn',function(){

        var type = $(this).parent().attr('data-type');
        var selector = $(this).parent().attr('data-filter');

        if(typeof selector != 'undefined')
        {
            if(type == 'PROP')
                $(selector).removeAttr('checked').trigger('change');
            else if(type == 'PRICE')
                $(selector).val('').trigger('change');
            $(this).parent().remove();
        }
    })


    $(document).on('click','a.form_brand',function(){
        document.form_brand.action = this.href;
        document.form_brand.submit();
        return false;
    })

})
