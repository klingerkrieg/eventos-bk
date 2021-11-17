<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'testes/clear.php';
require_once 'testes/home.php';
require_once 'testes/minicursos.php';

$restos = [];

function redirect($url){
    global $restos;
    array_push($restos, "[redirect-to:$url]");
}

function enviar_email($emailCadastro,$msg){
    global $restos;
    array_push($restos, "[email-to:$emailCadastro]");
}

function view($view){
    global $restos;
    array_push($restos, "[view:$view]");
    return 'blank.html';
}

class Testes extends CI_Controller {


    public function __construct(){
        parent::__construct();

        $this->load->helper('test');
    }
	
	
	public function clear(){
		$email = 'integracao@gmail.com';

        /**
         * Deleta coautores e orientadores do trabalho
         */
        $sql = "delete from log where iduser in 
            (select id from usuarios where email in 
            ('coautortrab01@gmail.com','coautortrab02@gmail.com','orient01@gmail.com','orient02@gmail.com'))";
        $this->db->query($sql);

        $sql = "delete from coautores where id in(select coautores.id from coautores

			inner join trabalhos ON 
			coautores.idtrabalho = trabalhos.id 

			inner join usuarios on
			trabalhos.idusuario = usuarios.id

			where usuarios.email = '$email')";
		$this->db->query($sql);

        $sql = "delete from usuarios where email in 
                    ('coautortrab01@gmail.com','coautortrab02@gmail.com','orient01@gmail.com','orient02@gmail.com')";
        $this->db->query($sql);

        /**
         * Deleta o trabalho
         */
		$sql = "delete from trabalhos where id in(select trabalhos.id from trabalhos
		
        inner join usuarios on
        trabalhos.idusuario = usuarios.id

        where usuarios.email = '$email')";
        $this->db->query($sql);

        /**
         * Deleta os coautores do minicurso
         */
        $sql = "delete from log where iduser in 
            (select id from usuarios where email in 
            ('coautor01@gmail.com','coautor02@gmail.com'))";
        $this->db->query($sql);

        $sql = "delete from minicursos_coautores where id in(select minicursos_coautores.id from minicursos_coautores

			inner join minicursos ON 
			minicursos_coautores.idminicurso = minicursos.id 

			inner join usuarios on
			minicursos.idusuario = usuarios.id

			where usuarios.email = '$email')";
		$this->db->query($sql);

        $sql = "delete from usuarios where email in 
                    ('coautor01@gmail.com','coautor02@gmail.com')";
        $this->db->query($sql);
		
		
        /**
         * Deleta o minicurso
         */
		$sql = "delete from minicursos where id in(select minicursos.id from minicursos
		
			inner join usuarios on
			minicursos.idusuario = usuarios.id

			where usuarios.email = '$email')";
		$this->db->query($sql);

        /**
         * Deleta o usuário teste
         */
		$sql = "delete from log where id in(select log.id from log

			inner join usuarios on
			log.iduser = usuarios.id

			where usuarios.email = '$email')";
		$this->db->query($sql);

		$this->db->delete("usuarios",["email"=>$email]);
	}


    public function index(){
        global $restos;
        $this->config->set_item('envio_emails_ativo',false);
        
        #limpa os testes do banco
        clear($this);

        #gambiarra para nao exibir saida das chamadas
        print "<div style='display:;'>";
        
        #home
        cad_usuario($this);
        recuperar_senha($this);
        login($this);
        
        #minicursos
        $curso = submeter_minicurso($this);
        
        print "</div>";

        #*******************
        $this->report();

        
    }


    private $itens = [];

    public function test($item, $expected, $name){
        global $restos;
        
        $res = false;
        if ($expected == "is_object" && is_object($item)){
            $res = true;
            $item = gettype($item);
            $expected = "object";
        } else
        if ($expected == "is_array" && is_array($item)){
            $res = true;
            $item = gettype($item);
            $expected = "array";
        } else
        if (is_array($expected) && is_array($item)){
            
            $diff = [];
            foreach($expected as $key=>$value){
                if (!isset($item[$key]) || $item[$key] != $value){
                    $diff[$key] = $value;
                }
            }
            
            $expected   = print_r($diff,true);
            if (count($diff) == 0){
                $res = true;
                $item       = gettype($item);
            } else {
                $item = print_r($item,true);
            }

        } else
        if ($expected == $item){
            $res = true;
        }

        array_push($this->itens,["name"=>$name,
                                        "result"=>$res,
                                        "item"=>$item,
                                        "expected"=>$expected,
                                        "restos"=>$restos]);
        $restos = [];
    }

    private function report(){
        print "<link rel='stylesheet' type='text/css' href='".base_url('static\semantic-ui\semantic.css')."'></link>";
        print "<style>.red{color:red;} .green{color:green;}</style>";
        print "<table class='ui celled table'>";
        print "<thead>";
        print "<tr>";
            print "<th>Nome</th>"
                 ."<th>Resultado</th>"
                 ."<th>Recebido</th>"
                 ."<th>Esperado diff</th>";
        print "</tr>";
        print "</thead>";
        foreach($this->itens as $item){
            $result = "<span class='red'>fail</span>";
            if ($item['result']){
                $result = "<span class='green'>ok</span>";
            }
            print "<tr>";
            print "<td>{$item['name']}</td>"
                 ."<td>{$result}</td>"
                 ."<td>{$item['item']}</td>"
                 ."<td>{$item['expected']}</td>";
            print "</tr>";
            if (count($item['restos']) > 0){
                print "<tr>";
                print "<td colspan='4'>".implode("<br>",$item['restos'])."</td>";
            print "</tr>";
            }
        }
        print "</table>";
    }

    






}