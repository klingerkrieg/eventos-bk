var coautores = 0;
var orientadores = 0;

var limite_coautores = 1;
var limite_orientadores = 2;

$(function(){
    setTimeout(function(){ toggleRows(); toggleButtons(); }, 100);

    $(".emailLocate").blur(localizarEmail);
});


function localizarEmail(){

    if ($(this).val().length > 8){
        var input = $('#'+$(this).attr("to"));
        $.ajax({
            url:site_url+"/ajax/email_locate",
            method:'get',
            data:{'email':$(this).val()}
        }).done(function(resp){
			if (resp != ""){
				input.val(resp);
			}
        });
    }
}


function toggleRows(){
    
    //mostra todos que tiverem algo preenchido
    $(".coautor, .orientador").each(function(k, el){
        $(el).find("input").each(function(y, inp){
            if ($(inp).val() != ""){
                $(el).show();
            }
        });
    });

    //mostra os primeiros
    $(".coautor").eq(0).show();
    $(".orientador").eq(0).show();
}

function toggleButtons(){
    if ($(".coautor:hidden").size() > 0){
        $("#addCoautores").show();
    } else {
        $("#addCoautores").hide();
    }

    if ($(".orientador:hidden").size() > 0){
        $("#addOrientadores").show();
    } else {
        $("#addOrientadores").hide();
    }
}

function addCoautor(tipoN){
    if (tipoN == 0){
        classe = "coautor";
    } else
    if (tipoN == 1){
        classe = "orientador";
    }
    el = $("."+classe+":hidden").eq(0);
    el.show();
    el.find("input").eq(0).focus();
    toggleButtons();
}

function remover_coautor(id){
    $("#"+id).hide();
    $("#"+id).find("input").val('')
    toggleButtons();
}