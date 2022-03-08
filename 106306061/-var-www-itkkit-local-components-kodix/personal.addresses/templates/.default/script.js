$(document).ready(function(){
    $("select#COUNTRY").on("change", function(){
        var cities=KDXSale.getAddressCities($(this).val());
        var options="";
        $.each(cities, function(id, city){
            var tmp=city.split("##");
            options+="<option value='"+tmp[0]+"'>"+tmp[1]+"</option>";
        });
        $("select#CITY").html(options);
    });

    $("#kdx_edit_addr").on("submit", function(){
        var fields=$(this).serialize();
        KDXSale.createAddress(fields, {
            complete:function(data){
                KDX.ajax("/ajax/addresses/getAddressesList.php", {
                    type:"post",
                    complete:function(data){
                        $("table.likely:first tbody").html(data.responseText);
                        $("#kdx_edit_addr").hide();
                    }
                });
            }
        });
        return false;
    });

    $(document).on("click", ".add_addr", function(){
        $("#kdx_edit_addr").show();
        $("#kdx_edit_addr").find("#PROFILE_ID").remove();
        $.each($("#kdx_edit_addr").find("input[type=text], textarea"), function(i, field){
            $(field).val("");
        });
        $.each($("#kdx_edit_addr").find("select"), function(i, select){
            $(select).val("");
            $(select).find("option").removeAttr("selected");
        });
        $("select#COUNTRY").change();
        return false;
    });

    $(document).on("click", ".edit_addr", function(){
        var profile_id=parseInt($(this).attr("profile_id"));
        if(!profile_id)
            return false;

        if(!$("#kdx_edit_addr #PROFILE_ID").length){
            $("#kdx_edit_addr").prepend("<input type='hidden' value='' id='PROFILE_ID' name='PROFILE_ID' />");
        }else{
            $("#kdx_edit_addr #PROFILE_ID").val(profile_id);
        }
        $.each(addresses, function(i, addr){
            if(addr.profile_id==profile_id){
                $("#kdx_edit_addr").show();
                for(prop in addr){
                    if(prop=="country"){
                        var option=$("select#COUNTRY option:contains("+addr[prop]+")");
                        $("select#COUNTRY").val($(option).attr("value"));
                        $("select#COUNTRY").change();
                        $("select#CITY").val(addr.city);
                    }else if(prop=="city"){
                        continue;
                    }else{
                        $("#kdx_edit_addr #"+prop.toUpperCase()).val(addr[prop]);
                    }
                }
                return false;
            }
        });
        return false;
    });

    $(document).on("click", ".delete_address", function(){
        var profile_id=parseInt($(this).attr("profile_id"));
        if(!profile_id)
            return false;
        KDXSale.removeAddress(profile_id, {
            complete:function(data){
                KDX.ajax("/ajax/addresses/getAddressesList.php", {
                    type:"post",
                    complete:function(data){
                        $("table.likely:first tbody").html(data.responseText);
                    }
                });
            }
        });
        return false;
    });
});