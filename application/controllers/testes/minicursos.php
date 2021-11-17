<?php


function submeter_minicurso($class){

    $post = [];
    $post["telefone"]       = "(84) 91234-1234";
    $post["titulo"]         = "Mini teste integracao";
    $post["lattes"]         = "www.teste.com.br";
    $post["curriculo"]      = "Currículo teste";
    $post["resumo"]         = "Resumo teste";
    $post["objetivo"]       = "Objetivo teste";
    $post["nome_coautor"]   = ["integração teste02","integração teste03"];
    $post["email_coautor"]  = ["intteste02@gmail.com","intteste03@gmail.com"];
    $post["idarea"]         = "1";
    $post["vagas"]          = "5";
    $post["ch"]             = "1";
    $post["horarios_preferenciais"] = ['2021-04-12 13:30','2021-04-13 13:30'];
    $post["informacoes_adicionais"] = "informacoes adicionais";
    $post["lattesC"]        = "lattes";

    $_POST = $post;
    load_controller('minicursos', 'submeter');
    

    $curso = $class->db->get_where("minicursos",["titulo"=>$post['titulo']])->row_array();

    unset($post["telefone"]);
    unset($post["lattes"]);
    unset($post["curriculo"]);
    unset($post["email_coautor"]);
    unset($post["nome_coautor"]);
    unset($post["lattesC"]);
    $post["horarios_preferenciais"] = '["2021-04-12 13:30","2021-04-13 13:30"]';

    $class->test($curso, $post, "Login");
}