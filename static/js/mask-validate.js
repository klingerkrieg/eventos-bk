$(function(){
    $('.telefone').mask('(00)00000-0000');

    $('.cpf').mask('000.000.000-00', {reverse: true});
    $('.cpf').keyup(function(){
        if ($(this).val().length == 14){
            if (validarCpf($(this).val())){
                $(this).parent().removeClass("error");
            } else {
                $(this).parent().addClass("error");
            }
        }
    });

});

function validarCpf(strCPF){

    strCPF = strCPF.replace(".","").replace(".","").replace("-","");

    var Soma;
    var Resto;
    Soma = 0;
    if (strCPF == "00000000000") return false;
    //if (strCPF == "11111111111") return false;
    if (strCPF == "22222222222") return false;
    if (strCPF == "33333333333") return false;
    if (strCPF == "44444444444") return false;
    if (strCPF == "55555555555") return false;
    if (strCPF == "66666666666") return false;
    if (strCPF == "77777777777") return false;
    if (strCPF == "88888888888") return false;
    if (strCPF == "99999999999") return false;

    for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11))  Resto = 0;
    if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;

    Soma = 0;
    for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11))  Resto = 0;
    if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;
    return true;
}

/*
function submitForm(form){
    $("[required]").each(function(i, el){
        if (el.value == ""){
            $(el).parent().addClass("error");
        } else {
            $(el).parent().removeClass("error");
        }
    });
}*/