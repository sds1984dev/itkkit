function check_regexp_eng_char(input){
    var re = /^[\w\d\s|á|é|í|ó|ú|à|è|ì|ò|ù|ä|ë|ï|ö|ü|Á|É|Í|Ó|Ú|À|È|Ì|Ò|Ù|Ä|Ë|Ï|Ö|Ü|ñ|Ñ|@.',-/]*$/;
    if (!re.test(input.val())){
        input.addClass('language_error');
    } else {
        input.removeClass('language_error');
    }
}
function check_regexp_phone_number(input){
    var phone_re = /^[0-9\+\-\(\)\s]*$/;
    if (!phone_re.test(input.val())){
        input.addClass('phone_error');
    } else {
        input.removeClass('phone_error');
    }
}

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

$(function(){

    $(document).on('click', '.trigger_focus', function(){
        $('input.validate_eng:text').focusout();
        $('input.phone_validate:text').focusout();
    });

    $(document).on('focusout', 'input:text, input:password', function() {
        $(this).removeClass('alarm');
    });

    $(document).on('focusout', 'input.validate_eng:text', function() {
        check_regexp_eng_char($(this));
        var wrapper = $(this).closest('.base_wrapper_eng_char');
        var form = (wrapper.length > 0) ? wrapper : $(this).closest('form');
        var messages = form.find('.lang_error_msg');
        var errors = form.find('.language_error');
        if (messages.length == 1 && errors.length == 0){
            messages.fadeOut();
        }
        if (!$(this).hasClass('language_error')) {
            $(this).prev('.lang_error_msg').fadeOut();
        }
        if (errors.length > 0) {
            errors.each(function () {
                if (messages.length > 1) {
                    $(this).prev('.lang_error_msg').fadeIn();
                } else {
                    messages.fadeIn();
                }
            });
        }

    });

    $(document).on('focusout', 'input.phone_validate:text', function() {
        check_regexp_phone_number($(this));
        var wrapper = $(this).closest('.base_wrapper_eng_char');
        var form = (wrapper.length > 0) ? wrapper : $(this).closest('form');
        var message = form.find('.phone_error_msg');
        var errors = form.find('.phone_error');
        if (errors.length > 0){
            message.fadeIn();
        } else {
            message.fadeOut();
        }
    });

    $(document).on('change', '.js_block_address_ctrl', function () {
        var wrapper = $(this).closest('.base_wrapper_eng_char');
        var form = (wrapper.length > 0) ? wrapper : $(this).closest('form');
        var lang_errors = form.find('.language_error');
        var phone_errors = form.find('.phone_error');
        if (this.checked) {
            lang_errors.removeClass('language_error');
            phone_errors.removeClass('phone_error');
        } else {
            form.find('input.validate_eng:text').each(function(){
                check_regexp_eng_char($(this));
            });
            form.find('input.phone_validate:text').each(function(){
                check_regexp_phone_number($(this));
            });
        }
    });

    $(document).on('submit', '.text_validation_eng_char', function(event){
        validate_form_eng_char(this, event);
    });
});

//$(document).ready(function(){
    function loadImg() {
        var picCont = $(this).find('.catalog-item__img--hover');
        if (picCont.length && !picCont.hasClass('catalog-item__img--loaded')) {
          var imgSource = $(this).find('.catalog-item__img--hover source');
          var imgSrc = $(this).find('.catalog-item__img--hover img');
          imgSource.each(function() {
            $(this).attr('srcset', $(this).attr('data-srcset'));
          });

          imgSrc.attr('src', imgSrc.attr('data-src'));
          picCont.addClass('catalog-item__img--loaded');
        }
    }

    $('body').on('mouseover', '.catalog-item__img-list', loadImg);
    $('body').on('touchmove', '.catalog-item__img-list', loadImg);

    $('.catalog-item__img-list').mouseenter(function() {
        $(this).css('transform','translateX(-50%)');
    });

    $('.catalog-item__img-list').mouseleave(function() {
         $(this).css('transform','none');
    });


  $('body').on('click', '.js-catalog-item-arrow-next', function(){
    var picCont = $(this).parent().find('.catalog-item__img--hover');
    if (picCont.length && !picCont.hasClass('catalog-item__img--loaded')) {
      var imgSource = $(picCont).find('source');
      var imgSrc = $(picCont).find('img');
      imgSource.each(function() {
        $(this).attr('srcset', $(this).attr('data-srcset'));
      });

      imgSrc.attr('src', imgSrc.attr('data-src'));
      picCont.addClass('catalog-item__img--loaded')
    }
  })
//});


$(function(){
    // Refresh page
    (function(){
        $('.js-refresh').on('click', function(){
            location.reload();
        });
    })();

    // Rating
    (function(){
        if ($('.js-rating').length){
            $('.js-rating').mousemove(function(e){
                var rating = $(this),
                    ratingFull = rating.find('._full'),
                    ratingPos = rating.offset(),
                    w = e.pageX - ratingPos.left
                ;

                votes = Math.ceil(w/18);
                ratingFull.width(votes*18);
            }).mouseout(function(){
                var rating = $(this),
                    ratingFull = rating.find('._full'),
                    ratingTotal = rating.find('.js-rating-val')
                ;

                if (ratingTotal.val() !== ''){
                    ratingFull.width(ratingTotal.val()*18);
                } else {
                    ratingFull.width(0);
                }
            }).on('click', function(e){
                var rating = $(this),
                    ratingFull = rating.find('._full'),
                    ratingTotal = rating.find('.js-rating-val'),
                    ratingPos = rating.offset(),
                    w = e.pageX - ratingPos.left
                ;

                votes = Math.ceil(w/18);
                ratingFull.width(votes*18);
                ratingTotal.val(votes);
            });
        }
    })();

    // Section sort
    (function(){
      var btn = $('.js-sort-btn');

      btn.on('change', function(e){
        e.preventDefault();
        var type = $(this).find('option:selected').data('type');
        var order = $(this).find('option:selected').data('order');

        setCookie('sectionItemsSort', type);
        setCookie('sectionItemsOrder', order);

        location.reload();
      });
    })();
});

$(window).on('load', function(){
    $('.js-logo-preloader').fadeOut(300);
});
window.onbeforeunload = function (event) {
    $('.js-logo-preloader').fadeIn(300);
}

var showAjaxPreloader = function(){
    $(this).html('<div class="ajax-preloader"><span></span></div>');
    $(this).find('.ajax-preloader').css({'width':$(this).width(),'height':$(this).height()+2});
}
$(document).on('click', '.js-ajax-btn', showAjaxPreloader);

var submitReviewsForm = function(e){
    e.preventDefault();
    var $form = $(this),
        formData = new FormData($form.get(0))
    ;

    $form.find('.required').each(function(){
        $(this).removeClass('_error');
    });
    $form.find('label.error').each(function(){
        $(this).remove();
    });
    $('.required').on('change', function(){
        $(this).removeClass('_error').next('label.error').remove();
    });

    $.ajax({
        url: '/local/app/ajax.php',
        type: $form.attr('method'),
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'json',
        success: function(data){
            console.log(data);
            if(data.result == 'success'){
                $form.html(data.text);
            } else {
                for (var errorField in data.text_error){
                    $form.find('.' + errorField).addClass('_error');
                }
            }
        }
    });
}
$(document).on('submit', '.js-review-form', submitReviewsForm);

var submitRegistration = function(e){
    e.preventDefault();
    var $form = $(this),
        btn = $form.find('.js-registration-submit'),
        formData = new FormData($form.get(0))
    ;

    $form.find('.error').each(function(){
        $(this).removeClass('error');
    });
    $form.find('.form-row__error').each(function(){
        $(this).remove();
    });
    $('.js-registration-error').html('');
    btn.attr('disabled', true);

    $.ajax({
        url: '/ajax/auth/registration.php',
        type: $form.attr('method'),
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'json',
        success: function(data){
            console.log(data);
            if (data.result == 'success'){
                location.reload();
            } else {
                btn.attr('disabled', false);
                if (data.text_msg){
                    $('.js-registration-error').html(data.text_msg['MESSAGE']);
                }
                for (var errorField in data.text_error){
                    if (errorField == 'NAME' || errorField == 'LAST_NAME' && data.text_error[errorField] == 'eng'){
                        $form.find('.reg_' + errorField).parent().find('юpopup_lang_err').fadeIn(200);
                    } else {
                        $form.find('.reg_' + errorField).parent().addClass('error').append('<div class="form-row__error">' + data.text_error[errorField] + '</div>');
                    }
                }
            }
        }
    });
}
$(document).on('submit', '.js-registration', submitRegistration);

var clearTrackingForm = function(e){
    $('.js-tracking-result').html('');
}
var submitTrackingForm = function(e){
    e.preventDefault();
    var $form = $(this),
        btn = $form.find('.js-tracking-submit'),
        formData = new FormData($form.get(0))
    ;

    $form.find('.error').each(function(){
        $(this).removeClass('error');
    });
    $form.find('.form-row__error').each(function(){
        $(this).remove();
    });
    $('.js-tracking-result').html('');
    btn.attr('disabled', true);

    $.ajax({
        url: '/ajax/tracking.php',
        type: $form.attr('method'),
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'json',
        success: function(data){
            console.log(data);
            if (data.result == 'success'){
                btn.attr('disabled', false);
                $('.js-tracking-result').html(data.msg);
            } else if (data.result == 'warning'){
                btn.attr('disabled', false);
                $('.js-tracking-result').html(data.msg);
            } else {
                btn.attr('disabled', false);
                for (var errorField in data.text_error){
                    $form.find('.' + errorField).parent().addClass('error').append('<div class="form-row__error">' + data.text_error[errorField] + '</div>');
                }
            }
        }
    });
}
$(document).on('click', '.js-tracking-nav', clearTrackingForm);
$(document).on('submit', '.js-tracking-form', submitTrackingForm);

var showFilterSizes = function(e){
    e.preventDefault();
    var filterBtn = $('.js-filter-btn'),
        filterWrap = $('.js-filter-wrap')
    ;

    if (!$(this).hasClass('_active')){
        filterBtn.removeClass('_active');
        filterWrap.addClass('_hide');
        $(this).addClass('_active');
        $($(this).attr('href')).removeClass('_hide');
        setCookie('filterShoesSize', $(this).data('size'));
    }
}

$(document).on('click', '.js-filter-btn', showFilterSizes);

// уcтанавливает cookie
function setCookie(name, value, props) {
    props = props || {}
    var exp = props.expires
    if (typeof exp == "number" && exp) {
    var d = new Date()

    d.setTime(d.getTime() + exp*1000)
    exp = props.expires = d
    }
    if(exp && exp.toUTCString) { props.expires = exp.toUTCString() }
    value = encodeURIComponent(value)
    var updatedCookie = name + "=" + value
    for(var propName in props){
    updatedCookie += "; " + propName
    var propValue = props[propName]
    if(propValue !== true){ updatedCookie += "=" + propValue }
    }
    document.cookie = updatedCookie
}

// удаляет cookie
function deleteCookie(name) {
    setCookie(name, null, { expires: -1 });
}

var openSizePopup = function(e){
    e.preventDefault();
    console.log('1');
}
var closePopup = function(){
    $('.popup').attr('data-popup-show', 'init');
}
$(document).on('click', '.js-popup-size', openSizePopup);
$(document).on('click', '.popup__close, .popup__overlay', closePopup);

var openSort = function(){
    $(this).toggleClass('_active');
    $('.js-wrap-sort').toggleClass('_active');
}
var changeSort = function(e){
    e.preventDefault();
    var type = $(this).data('type');
    var order = $(this).data('order');

    setCookie('sectionItemsSort', type);
    setCookie('sectionItemsOrder', order);

    location.reload();
}
var closeSort = function(e){
    if ($(e.target).closest('.js-btn-sort').length){
        return;
    } else {
        if ($('.js-btn-sort').hasClass('_active')){
            $('.js-btn-sort').removeClass('_active');
            $('.js-wrap-sort').removeClass('_active');
        }
    }
    e.stopPropagation();
}
var showMore = function(e){
    e.preventDefault();
    $(this).hide();
    $('.js-more-hide').show();
    $('.js-brandtext-hide').addClass('active');
}
var hideMore = function(e){
    e.preventDefault();
    $(this).hide();
    $('.js-more-show').show();
    $('.js-brandtext-hide').removeClass('active');
}
$(document).on('click', '.js-more-show', showMore);
$(document).on('click', '.js-more-hide', hideMore);
$(document).on('click', '.js-btn-sort', openSort);
$(document).on('click', '.js-wrap-sort a', changeSort);
$(document).on('click', closeSort);

$(document).ready(function() {

    var panel = $('#bx-panel');
    let main = $('.main--full-height');
    let top = parseInt(panel.height()) + parseInt($('.header').height());

    main.css('padding-top', top);

    $(document).scroll(function() {

        let headerTop = parseInt($('.header').css('top'));

        headerTop = panel.height() - window.scrollY;
        $('.header').css('top', headerTop);

        if (window.scrollY >= 40) {
            $('.header').css('top', '0');
        }

    });

});