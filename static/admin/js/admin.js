$(function(){
    $('.ui.dropdown.topMenu').dropdown({
        on:'hover'
    });

    $("#aprovado_certificado_palestrante").change(togglePalestraMesa);
    $("#aprovado_certificado_mesa_redonda").change(togglePalestraMesa);
    togglePalestraMesa();

    $("#matricula_disponivel").change(toggleStatus);
    toggleStatus();


    $("#evento_encerrado").change(toggleEncerramento);
    toggleEncerramento();

    $("[data-html]").popup({
        variation:"inverted"
    });

    if ($("#areas_avaliador").length > 0){
        if ($("#areas_avaliador").val() == null){
            $("#msg_areas_interesse").show();
        }
    }

    $('.ui.sidebar').sidebar('attach events', '.toc.item');
      
});

function toggleEncerramento(){
    if ($("#evento_encerrado").prop('checked')){
        $("#aceitando_submissoes_minicursos, #aceitando_submissoes, #aceitando_correcoes").attr("disabled","disabled");
    } else {
        $("#aceitando_submissoes_minicursos, #aceitando_submissoes, #aceitando_correcoes").removeAttr("disabled");
    }
}

function toggleStatus(){
    if ($("#matricula_disponivel").prop('checked')){
        $("#status").val(10).attr("disabled","disabled");
    } else {
        $("#status").removeAttr("disabled");
    }
}



function gerarPlanilha(btn){
    oldAction = $("#filtros").attr("action");
    $("#filtros").attr("action",$(btn).attr("src"));
    $("#filtros").submit();
    $("#filtros").attr("action",oldAction);
}

function togglePalestraMesa(){
    if ($("#aprovado_certificado_palestrante").prop('checked')){
        $("#titulo_palestra_label").removeClass("disabled");
    } else {
        $("#titulo_palestra_label").addClass("disabled");
    }

    if ($("#aprovado_certificado_mesa_redonda").prop('checked')){
        $("#titulo_mesa_redonda_label").removeClass("disabled");
    } else {
        $("#titulo_mesa_redonda_label").addClass("disabled");
    }
}