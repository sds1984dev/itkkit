/**
 * Created by kodix on 28.07.14.
 */
$(document).ready(function () {
    $(document).on('click', 'a.country-item', function (event) {
        event.preventDefault();
        let country = $(this).attr('href').substr(1);
        let that = $(this);
        if (country.length) {
            KDX.ajax('/ajax/country.selector/change.php', {
                data: {NEW_COUNTRY: country},
                type: 'post',
                success: function (data) {
                    console.log(data);
                    //debugger;
                    //var myTimeout = setTimeout(setLocation, 900);
                    //location.reload();
                    //function setLocation(){
                    if (country == " RU") {
                        window.location.href = addParam(that.data('url'), 'cur', country);
                        //console.log(that.data('url'));
                    } else {
                        window.location.href = addParam($('#UK.country-item').data('url'), 'cur', country);
                        //console.log($('#UK.country-item').data('url'));
                    }
                    //}


                }
            });
        }
        return false;
    });

    $(document).on('click', 'a.country__name', function () {
        if ($(this).data('country-name')) {
            let country = $(this).data('country-name');
            KDX.ajax('/ajax/country.selector/change.php', {
                data: {NEW_COUNTRY: country},
                type: 'post',
                success: function (data) {
                    //location.reload();
                }
            })
        }
        return false;
    });

    $(document).on('change', '.country-item-select', function () {
        let country = $(this).val().substr(1);
        let that = $(this);
        KDX.ajax('/ajax/country.selector/change.php', {
            data: {NEW_COUNTRY: country},
            type: 'post',
            success: function (data) {
                //location.reload();
                if (country == "RU") {
                    window.location.href = addParam(that.data('url'), 'cur', country);
                    //console.log(that.data('url'));
                } else {
                    window.location.href = addParam($('#UK.country-item').data('url'), 'cur', country);
                    //console.log($('#UK.country-item').data('url'));
                }
            }
        });
        return false;
    });

    window.addParam = function (url, param, value) {
        if (url.indexOf('?') < 0) {
            url = url + '?'
        } else {
            url = url + '&'
        }

        url = url + param + '=' + value;

        return url;
    }

});
