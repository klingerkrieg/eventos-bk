<?php

if (!function_exists("enviar_email")){
    function enviar_email($para_email, $msg){
        $email = 'viiexpotec@gmail.com';
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_user' => $email,
            'smtp_pass' => 'ifrn2021',
            'mailtype'  => 'html', 
			'smtp_crypto'=>'tls',
            'validate'  => true,
            'charset'   => 'utf-8',
            'newline'		=> "\r\n"
        );

        $CI =& get_instance();
        $CI->load->library('email', $config);

        $CI->email->from($email, nome_evento());
        $CI->email->to($para_email);

        $assunto = nome_evento();
        $CI->email->subject($assunto);
        $CI->email->message($msg);

        if ($CI->config->item('envio_emails_ativo')) {
            $CI->email->send();
        } else {
            print "<hr/>";
            print "Assunto:" . $assunto . "<br/>";
            print "To:". $para_email . "<br>";
            print $msg;
            print "<hr/>";
        }
    }
}

if (!function_exists("view")){
    function view($view){
        $view = str_ireplace(".php","",$view);
        return "application/views/$view.php";
    }
}

if (!function_exists("val")){
    function val($arr,$key){
        if (isset($arr[$key])){
            return $arr[$key];
        } else {
            return null;
        }
    }

    function _v($arr,$key){
        return val($arr,$key);
    }
}

if (!function_exists("temMinicursos")){
    function temMinicursos(){
        $CI =& get_instance();
        $CI->load->model('Minicurso_model');
        $evt = $CI->Minicurso_model->temMinicursosDisponiveis();
        return $evt;
    }
}


if (!function_exists("get_evento_data")){
    function get_evento_data(){
        
        $CI =& get_instance();
        $CI->load->model('Evento_model');
        $evt = $CI->Evento_model->getEvento();
        $_SESSION['nome_evento']        = $evt['evento'];
        $_SESSION['email_evento']       = $evt['email'];
        $_SESSION['data_certificado']   = $evt['data_certificado'];
        $_SESSION['data_inicio']        = $evt['data_inicio'];
        $_SESSION['data_fim']           = $evt['data_fim'];
        $_SESSION['evento_encerrado']   = $evt['evento_encerrado'];
        $_SESSION['aceitando_matriculas_minicursos']   = $evt['aceitando_matriculas_minicursos'];
        
    }
}

if (!function_exists("evento_encerrado")){
    function evento_encerrado(){
        if (!isset($_SESSION['evento_encerrado'])){
            get_evento_data();
        }

        return $_SESSION['evento_encerrado'];
    }
}

if (!function_exists("matriculas_encerradas")){
    function matriculas_encerradas(){
        if (!isset($_SESSION['aceitando_matriculas_minicursos'])){
            get_evento_data();
        }

        return !$_SESSION['aceitando_matriculas_minicursos'];
    }
}

if (!function_exists("datas_evento")){
    function datas_evento(){
        if (!isset($_SESSION['data_inicio'])){
            get_evento_data();
        }

        return [$_SESSION['data_inicio'],$_SESSION['data_fim'] ];
    }
}

if (!function_exists("nome_evento")){
    function nome_evento(){
        if (!isset($_SESSION['nome_evento'])){
            get_evento_data();
        }

        return $_SESSION['nome_evento'];
    }
}

if (!function_exists("email_evento")){
    function email_evento(){
        if (!isset($_SESSION['email_evento'])){
            get_evento_data();
        }

        return $_SESSION['email_evento'];
    }
}

if (!function_exists("data_certificado")){
    function data_certificado(){
        if (!isset($_SESSION['data_certificado'])){
            get_evento_data();
        }

        return $_SESSION['data_certificado'];
    }
}


if (!function_exists("cpf_check")){
    function cpf_check($cpf){

        if ($cpf == ""){
            return true;
        }
        
        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
        
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        /*if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }*/

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
}


if (!function_exists("order_by_link")){
    function order_by_link($url,$field){
        $get = $_GET;
        if (_v($get,"order_by") == $field){
            if (_v($get,"order") == "asc" || !isset($get["order"])){
                $get["order"] = "desc";
            } else {
                $get["order"] = "asc";
            }
        } else {
            $get["order_by"] = $field;
        }
        $get = http_build_query($get);
        return site_url($url)."?".$get;
    }
}

if (!function_exists("order_by_img")){
    function order_by_img($field){
        if (_v($_GET,"order_by") == $field){
            if (_v($_GET,"order") == "desc"){
                return "<i class='sort amount up icon'></i>";
            } else {
                return "<i class='sort amount down icon'></i>";
            }
        }
    }
}



if (!function_exists("limitar")){
    function limitar($text, $size){
        if (strlen($text) > $size){
            $text = substr($text,0,$size);
            return $text . "...";
        }
        return $text;
    }
}


if (!function_exists("coautorHTMLField")){
function coautorHTMLField($id,$tipo, $dados){

    if ($dados == false){
      $dados = [];
    }
  
    $html = "<div class='row $tipo' id='$tipo$id' style='display:none;'>
        <div class='fields'>
          <div class='nine wide field'>
            <label>Nome do $tipo <input type='text' id='nomeLocate$tipo$id' name='nome_{$tipo}[]' maxlength='250' value='". _v($dados,'nome_completo'). "' /></label>
          </div>
  
          <div class='field'>
            <label>E-mail do $tipo 
            <input type='email' class='emailLocate' to='nomeLocate$tipo$id' name='email_{$tipo}[]' maxlength='250' value='". _v($dados,'email'). "'/></label>
          </div>
  
          <div class='wide one field'>
            <i class='big trash icon lineHeight' onclick='remover_coautor(\"$tipo$id\")'></i>
          </div>
        </div>
      </div>";
  
      return $html;
  }
}

if (!function_exists("statusColor")){
    function statusColor($statusid,$status){
        
        $html = "";
        
            if ($statusid == PENDENTE):
                $html .= '<div id="status" class="ui grey label"  style="text-align:center;">';
            elseif ($statusid == APROVADO):
                $html .= '<div class="ui green label"  style="text-align:center;">';
            elseif ($statusid == APROVADO_CORRECOES_PENDENTES):
                $html .= '<div class="ui yellow label" style="text-align:center;">';
            elseif ($statusid == APROVADO_CORRECOES):
                $html .= '<div class="ui green label" style="text-align:center;">';
            else:
                $html .= '<div class="ui red label"  style="text-align:center;">';
            endif;
            $html .= $status[$statusid] . '</div>';
            return $html;
    }
}


if (!function_exists("exibirHorarios")){
function exibirHorarios($horarios){
    $html = "";
    $data_ant = null;
    sort($horarios);
    $qtdHorarios = count($horarios);

    if ($qtdHorarios > 0){

        for ($i = 0; $i < $qtdHorarios; $i++){
        $horario = $horarios[$i];
        $data = date('d/m/Y', strtotime($horario));
        $hora_de = date('H:i', strtotime($horario));
        $hora_ate = date('H:i', strtotime($horario ." +1 hours +30 minutes"));
        $data_hora_ate = date('Y-m-d H:i', strtotime($horario ." +1 hours +30 minutes"));

        if ($i+1 < $qtdHorarios) {
            $next_datahora_de = date('Y-m-d H:i', strtotime($horarios[$i+1]));
            while ($i+1 < $qtdHorarios && $next_datahora_de == $data_hora_ate){
            $hora_ate = date('H:i', strtotime($horarios[$i+1]." +1 hours +30 minutes"));
            $i++;
            if ($i+1 == $qtdHorarios){
                break;
            }
            $next_datahora_de = date('Y-m-d H:i', strtotime($horarios[$i+1]));
            $data_hora_ate = date('Y-m-d H:i', strtotime($horario ." +1 hours +30 minutes"));
            }
        }


        if ($data_ant != $data){
            if ($data_ant != null){
                $html .= "</div>";
            }
            $html .= "<div><b>$data</b><br>";
            $data_ant = $data;
        }
        $html .= "$hora_de - $hora_ate<br>";
        }
        $html .= "</div>";
    }
    return $html;
}
}


if (!function_exists("cleanString")){
function cleanString($text) {
    $utf8 = array(
        '/[áàâãªä]/u'   =>   'a',
        '/[ÁÀÂÃÄ]/u'    =>   'A',
        '/[ÍÌÎÏ]/u'     =>   'I',
        '/[íìîï]/u'     =>   'i',
        '/[éèêë]/u'     =>   'e',
        '/[ÉÈÊË]/u'     =>   'E',
        '/[óòôõºö]/u'   =>   'o',
        '/[ÓÒÔÕÖ]/u'    =>   'O',
        '/[úùûü]/u'     =>   'u',
        '/[ÚÙÛÜ]/u'     =>   'U',
        '/ç/'           =>   'c',
        '/Ç/'           =>   'C',
        '/ñ/'           =>   'n',
        '/Ñ/'           =>   'N',
        '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
        '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
        '/[“”«»„]/u'    =>   ' ', // Double quote
        '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
    );
    return preg_replace(array_keys($utf8), array_values($utf8), $text);
}
}