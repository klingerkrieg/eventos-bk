$(function(){

    $("#instituicao").change(toggleInstituicao);
    toggleInstituicao();
    
    
});

function toggleInstituicao(){
    if ($("#instituicao").val() == "outra"){
        $("#outra_instituicao").show();
        $("#outra_instituicao").find("input").removeAttr("disabled");
    } else {
        $("#outra_instituicao").hide();
        $("#outra_instituicao").find("input").attr("disabled","disabled");
    }
}