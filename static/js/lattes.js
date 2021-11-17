$(function(){


    $("#radioLattes,#radioCurriculo").click(toggleLattes);
    
    setTimeout(function(){
        if ($("#curriculo").val() != ""){
            $("#radioCurriculo").trigger('click').trigger('click');
        } else {
            $("#radioLattes").trigger('click').trigger('click');
        }
    }, 500);
});

function toggleLattes(){
    if ($(this).prop('checked')){
        $("#"+$(this).attr("show")).show('fast');
        $("#"+$(this).attr("hide")).hide('fast');
    }
}