/**
 * Created by:  KODIX 09.10.14 12:14
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */

function isValid(form){
    var is_valid=true;
    $.each($(form).find($("input.required")), function(i, inp){
        $(inp).focusout();
    });
    $.each($(form).find($("select.required")), function(i, select){
        if(!$(select).val()){
            $(select).closest(".form_row").removeClass("ok").addClass("not_ok");
            is_valid=false;
        }else{
            $(select).closest(".form_row").removeClass("not_ok").addClass("ok");
        }
    });
    if($(form).find(".not_ok").length)
        is_valid=false;
    return is_valid;
}

function clearForm(form)
{
    $.each(form.find("input[type=text], textarea"), function(i, field){
        $(field).val($(field).attr('data-default-value'));
    });
}

$(function(){

    $(document).on("change", "select[name=COUNTRY]", function(){

        cities = KDXSale.addresses[$(this).val()].CITIES;

        var options="";
        for(id in cities){
            if($(this).attr('id') == 'S_COUNTRY' && $.inArray(String(cities[id].ID),KDXSale.cities_pickup_points)==-1){
                continue
            }
            options+="<option value='"+cities[id].ID+"'>"+cities[id].NAME+"</option>";
        }

        try
        {
            $(this).parents('form').find('select[name=CITY]').html(options).combobox('destroy')
        }
        catch(e){}
        finally
        {
            $(this).parents('form').find('select[name=CITY]').combobox();
        }
    });

    $(document).on('click','[data-target=cancel_address]',function(e){

        profile_id = parseInt($(this).attr("data-id"));

        if(!profile_id)
            return false;

        $('[data-option=current_address][data-id='+profile_id+']').show().find('label').click();
        $(this).parent().hide();

        return false;
    })

    $(document).on('click','[data-target=change_address]',function(e){

        var profile_id=parseInt($(this).attr("data-profile_id"));

        if(!profile_id)
            return false;

        $(this).parent().hide();
        $('[data-option=change_address]').hide()
        $('[data-option=change_address][data-id='+profile_id+']').show().find('label').click();

        $('#ADD_ADDRESS .hidden_optoins').hide();

        return false;
    })

    $(document).on("focusout", "input.required", function(){
        var is_valid=true;

        if(!$(this).val()){
            is_valid=false;
        }
        if( ($(this).attr("name")=="CONTACT_NAME" || $(this).attr("name")=="CONTACT_LAST_NAME") && ( !$.trim($(this).val()) || !String($(this).val()).match(/^[А-Яа-яЁёA-z ]+$/)) )
            is_valid=false;

        if($(this).attr("name")=="PHONE" && $(this).val()){

            if($(this).hasClass('notValid')){
                is_valid=false;
            }
        }

        if(!is_valid)
            $(this).closest(".form_row").removeClass("ok").addClass("not_ok");
        else if(is_valid!="default")
            $(this).closest(".form_row").removeClass("not_ok").addClass("ok");
    });

    $(document).on('change','[name=address]',function(){

        $('[name=delivery]').removeAttr('checked')

        if(this.value == 'NEW')
        {
            clearForm($('#ADD_ADDRESS .hidden_optoins'));
            $('#ADD_ADDRESS .hidden_optoins').show()//.find('[name=COUNTRY]').change();

        }
        else if($(this).hasClass('no_change'))
        {
            return false;
        }
        else
        {
            $('#ADD_ADDRESS .hidden_optoins').hide();
            $('[name=STEP_2]').click()
        }
    });
})