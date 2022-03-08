/**
 * Created by kodix on 08.07.14.
 */
$(document).ready(function(){

    $('#priceSlider').rangeSlider({
        arrows:false,
        bounds:{
            min: $('#priceSlider').attr('data-min-val'),
            max: $('#priceSlider').attr('data-max-val')
        },
        defaultValues:{
            min: 0,
            max: $('#priceSlider').attr('data-max-val')
        },
        step:100


    })
    $(document).on("valuesChanging", "#priceSlider", function(e, data){
        //console.log("Something moved. min: " + data.values.min + " max: " + data.values.max);
        $('input[name="FILTER[PRICE][MIN]"]').val(data.values.min)
        $('input[name="FILTER[PRICE][MAX]"]').val(data.values.max)
    });
    $(document).on('change','form#catalog_filter input',function(){
        var url = KDX.removeURLParameter(location.pathname+location.search,'FILTER');
        KDX.ajax(url,{
            data:$(this).closest('form#catalog_filter').serialize(),
            type:'POST',
            dataType:'html',
            cache:false,
            success:function(data){
                try {
                    var content =ffff= data;
                    var trimmed = $($.trim(content));
                    if(trimmed.length==1 && trimmed.hasClass('wrap_catalog')){
                        $('.wrap_catalog').html(trimmed.html())
                    }
                    else{
                        $('.wrap_catalog').html(content)
                    }
                    /*var new_url =  $('.wrap_catalog').eq(0).find('#new_addres_push[data-href]').eq(0).attr('data-href')
                    if(new_url.length>2){
                        KDX.setPage(new_url);
                    }

                    if (ajax_append) {
                        $(ajax_wrapper).find(KDX.getOption('NeedRemoveInBaseBeforeAppend')).remove()
                        $(ajax_wrapper).append($($.trim(content)).html())
                        if (history_need) {
                            KDX.setPage(url)
                        }
                    }
                    else {
                        $(ajax_wrapper).html(content)
                        if (history_need) {
                            KDX.setPage(url)
                        }
                    }*/
                    KDX.initPlugins();
                    $('#priceSlider').rangeSlider({
                        arrows:false,
                        bounds:{
                            min: $('#priceSlider').attr('data-min-val'),
                            max: $('#priceSlider').attr('data-max-val')
                        },
                        defaultValues:{
                            min: $('input[name="FILTER[PRICE][MIN]"]').val(),
                            max: $('input[name="FILTER[PRICE][MAX]"]').val()
                        },
                        step:100

                    })
                }catch (e){
                    console.error(e)
                    KDX.hidePreloader()
                }
            }
        })

    })
})
