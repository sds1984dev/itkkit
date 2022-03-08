$(function(){
    $(document).on('submit', '.text_validation_eng_char', function(event){
        validate_form_eng_char(this, event);
    });

    function validate_form_eng_char(form, event){
        var lang_errors = $(form).find('.language_error');
        var phone_errors = $(form).find('.phone_error');
        if (lang_errors.length > 0 || phone_errors.length > 0) {
            if ($(form).hasClass(KDX.getOption('NeedAjaxLoadClass'))){
                return false;
            } else if(typeof event != 'undefined') {
                event.preventDefault();
            }
        }
    }

    $('.account-section__edit.mod_add').on('click', function(){
        var form = $('#new_addr_form');
        if($(form).parent().attr('data-toggle-show')!='false')
            $('html,body').animate({scrollTop: form.offset().top});
    });
});