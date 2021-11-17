$(function(){

    $("textarea[maxlength]").keydown(function(evt){

        cancel = false;
        if ($(this).val().length == $(this).attr("maxlength")){

            if (evt.keyCode >= 48 && evt.keyCode <= 57
                || evt.keyCode >= 96 && evt.keyCode <= 105
                || evt.keyCode >= 65 && evt.keyCode <= 90
                || evt.keyCode >= 186 && evt.keyCode <= 222){
                cancel = true;
            }
            
        }

        contador = "contator"+$(this).attr("name");
        if ($("#"+contador).length == 0){
            $(this).after("<span id='"+contador+"'></span>");
        }

        
        $("#"+contador).html($(this).val().length+"/"+$(this).attr("maxlength"));

        if (cancel){
            evt.preventDefault();
        }


    });

});