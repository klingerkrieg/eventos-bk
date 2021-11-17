<?php
function cad_usuario($class){
    session_destroy();

    $post["nome_completo"] = "Teste 01 Silva";
    $post["email"] = "teste01@gmail.com";
    $post["emailConfirm"] = "teste01@gmail.com";
    $post["tipoInscricao"] = "4";
    $post["password1"] = "1234568";
    $post["password2"] = "1234568";
    $post["cpf"] = "111.111.111-11";
    $post["instituicao"] = "outra";
    $post["outra_instituicao"] = "UFPE";
    $post["telefone"] = "(84) 91234-1234";
    
    $_SERVER["REQUEST_METHOD"] = "POST";
    $_POST = $post;

    load_controller('home', 'salvar_registro');
    
    
    $usr = $class->db->get_where("usuarios",["email"=>"teste01@gmail.com"])->row_array();
    $_GET['u']      = $usr["id"];
    $_GET['hash']   = $usr["hash"];
    load_controller('home', 'confirmacao');
    
    $usr = $class->db->get_where("usuarios",["email"=>"teste01@gmail.com"])->row_array();
    
    
    unset($post['emailConfirm']);
    unset($post['password1']);
    unset($post['password2']);
    unset($post['instituicao']);
    $post['email_confirmado']   = 1;
    $post['pago']               = 1;
    $post['password']           = 'c7b6b845668130956f8768d3f1ce3d391ca881d6';
    $post['nivel']              = NIVEL_PARTICIPANTE;
    $class->test($usr, $post, "Cadastro de usuário");
}


function recuperar_senha($class){
    
    $_SERVER["REQUEST_METHOD"] = "POST";
    $_POST["cpf"]   = "111.111.111-11";
    $_POST["email"] = "teste01@gmail.com";

    #$class->load->library('../controllers/Home');
    #$class->home->enviar_email_rec();
    load_controller('home', 'enviar_email_rec');
    

    $usr = $class->db->get_where("usuarios",["email"=>"teste01@gmail.com"])->row_array();
    #$class->home->alterar_senha($usr["hash"],$usr["id"]);
    load_controller('home', 'alterar_senha',$usr["hash"],$usr["id"]);

    
    $_SERVER["REQUEST_METHOD"] = "POST";
    $_POST["password1"] = "123456";
    $_POST["password2"] = "123456";
    #$class->home->salvar_alteracao_senha();
    load_controller('home', 'salvar_alteracao_senha');
    
    $usr = $class->db->get_where("usuarios",["email"=>"teste01@gmail.com"])->row_array();
    
    $class->test($usr['password'], "7c4a8d09ca3762af61e59520943dc26494f8941b", "Recuperar senha");
}


function login($class){
    
    $_SERVER["REQUEST_METHOD"] = "POST";
    $_POST["email"]     = "teste01@gmail.com";
    $_POST["password"]  = "123456";

    
    load_controller('home', 'logar');
    $class->test(_v(_v($_SESSION,'user'),'email'), "teste01@gmail.com", "Login");
}
