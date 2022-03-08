$(document).ready(function(){
    $(".show_sizes_grid").on("click", function(){
        var grid_iblock=parseInt($(this).attr("grid_iblock"));
        var grid=parseInt($(this).attr("grid"));
        if(!grid_iblock || !grid)
            return false;

        KDX.ajax("/ajax/catalog.detail/getSizeGrid.php", {
            type:"post",
            data:{
                grid:grid,
                grid_iblock:grid_iblock
            },
            dataType:"json",
            success:function(data){
                console.log(data);
                KDX.popup({
                    content:data.PREVIEW_TEXT
                });
            }
        });
        return false;
    });
});