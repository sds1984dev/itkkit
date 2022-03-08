$(function(){

    var ShopAddress = {
        'DELIVERY_ZIP' : 'LV-1050',
        'DELIVERY_COUNTRY' : '0000028003',
        'DELIVERY_CITY' : 'Riga',
        'DELIVERY_STREET' : 'Z.A. Meierovica Blvd.',
        'DELIVERY_HOUSE' : '18',
        'DELIVERY_FLAT' : '1'
    }

    $('.btn_profile.mod_add').click(function(){
        $(this).parent().parent().remove();
        return false;
    })

    /*$(document).on('keyup paste','.location', function(){
        var input = $(this);
        $(this).siblings('input[type=hidden]').val('')
        if(this.value.length < 3)
        return;

        var q = this.value;

        if(typeof $.lastAjax != 'undefined')
            $.lastAjax.abort()


        $.lastAjax = $.ajax('/ajax/location.php',
            {
                method:'POST',
                data: {q: q},
                //type: 'json',
                success: function(data){
                    input.siblings('.location-container').html(data);
                    if(data.length > 0)
                        input.siblings('.location-container').show();
                    else
                        input.siblings('.location-container').hide();
                }
            }
        );
    })

    $(document).on('click','.location-container ul li',function(){
        $(this).parent().parent().siblings('input[type=hidden]').val( $(this).attr('data-code') )
        $(this).parent().parent().siblings('input[name=q]').val($.trim($(this).text()) )
        $(this).parent().parent().hide();
    })

    $(document).on('click',function(e){

        if(!$(e.target).hasClass('location-elem'))
        {
            $('.location-container').hide();
        }
    })

    $(document).on('blur','.location',function(){
        setTimeout(function(){
            $('.location-container').hide();
        },250)

    })*/

    $(document).on('click','.address_add, .address_edit',function(){


        if($(this).hasClass('address_add')) {
            $('.profile_form[data-profile!=NEW]').hide();
            $('.profile_form[data-profile=NEW]').toggle();
        }
        else if($(this).hasClass('address_edit')) {
            $('.profile_form[data-profile!="' + $(this).attr('data-profile') + '"]').hide();
            $('.profile_form[data-profile="' + $(this).attr('data-profile') + '"]').toggle();
        }

        return false;
    })

    $(document).on('change','input[name=PICKUP]',function(){

        for(name in ShopAddress)
        {
            $('[name="ADDRESS[NEW][DELIVERY]['+name+']"]').not(':hidden').removeAttr('readonly').removeAttr('disabled').val('')
        }

        if(this.checked)
        {
            for(name in ShopAddress)
            {
                if(name == 'DELIVERY_COUNTRY')
                {
                    $('select[name="ADDRESS[NEW][DELIVERY]['+name+']"]').attr('disabled', 'disabled').val(ShopAddress[name]);
                }
                else
                {
                    $('[name="ADDRESS[NEW][DELIVERY]['+name+']"]').attr('readonly', 'readonly').val(ShopAddress[name]);
                }
            }

            if($('input[name=MATCHES]').is(':checked'))
            {
                $('input[name=MATCHES]').removeAttr('checked').change();
            }
            $('input[name=MATCHES]').parent().hide();
        }
        else{
            $('input[name=MATCHES]').parent().show();
        }
    })

})