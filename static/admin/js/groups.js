$(function(){

    $('#check_all').change(function(){
        if (this.checked){
            $(".checks input").prop("checked",true);
        } else {
            $(".checks input").prop("checked",false);
        }
    });

    $(".check_table").change(function(){
        if (this.checked){
            $("."+this.value+"_check").prop("checked",true);
        } else {
            $("."+this.value+"_check").prop("checked",false);
        }
    });

    $(".perm_checks").change(function(){
        if (this.checked){
            if (this.value.indexOf("_view") == -1){
                view = this.value.replace("_edit","_view").replace("_delete","_view");
                $("#"+view).prop("checked",true);
            }
        } else {
            if (this.value.indexOf("_view") > -1){
                edit = this.value.replace("_view","_edit")
                del = this.value.replace("_view","_delete");

                $("#"+edit).prop("checked",false);
                $("#"+del).prop("checked",false);
            }
        }
    });


    var all_checked = true;
    $(".check_table").each(function(k,el){
        
        console.log("#"+this.value+"_view");
        if ( $("#"+this.value+"_view").prop("checked") == true &&
            $("#"+this.value+"_edit").prop("checked") == true &&
            $("#"+this.value+"_delete").prop("checked") == true){
            el.checked = true;
        } else {
            all_checked = false;
        }
    });

    if (all_checked){
        $('#check_all').prop("checked",true);
    }


});