$(function(){

    $('.cep').mask('00000-000',{
        onComplete:function(cep){
            cep = cep.replace("-","");
            url = "https://viacep.com.br/ws/"+cep+"/json/";
            $.ajax(url,{
                success:function(json){
                    $("#logradouro").val(json.logradouro);
                    $("#bairro").val(json.bairro);
                    $("#cidade").val(json.localidade);
                    $("#uf").val(json.uf);
                }
            });
        }
    });
});