$(function(){

    $('.btn_profile.mod_add').click(function(){
        $(this).parent().parent().remove();
        return false;
    })

    $(document).on('keyup paste','.location', function(){
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
    })
})