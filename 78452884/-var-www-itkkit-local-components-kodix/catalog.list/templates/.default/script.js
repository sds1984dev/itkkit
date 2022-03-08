$(document).ready(function(){
    $(document).on("change", "#sort-by", function(){
        var value=$(this).val();
        if(value){
            var sort=value.split("::");
            setSearch(sort[0], sort[1]);
        }else{
            setSearch(false, false);
        }
    })
});

function setSearch(sort, direction){
    var loc=location.protocol + '//' + location.host + location.pathname;
    var new_search=KDX.removeURLParameter(location.search, "SORT");
    var new_search=KDX.removeURLParameter(new_search, "ORDER");
    if(sort && direction){
        if(new_search && new_search!="?"){
            new_search+="&"+"SORT="+sort+"&ORDER="+direction;
        }else{
            new_search="?"+"SORT="+sort+"&ORDER="+direction;
        }
        history.pushState(null, null, loc+new_search);
    }else{
        if(new_search=="?")
            new_search="";
        history.pushState(null, null, loc+new_search);
    }

    KDX.ajax(loc+new_search, {
        complete:function(data){
            $("#ajax_wrapper").replaceWith(data.responseText);
        }
    });
}