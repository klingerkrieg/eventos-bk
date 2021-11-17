<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class CaptchaLib {

    protected $CI;
    private $seconds = 5;
    private $path = './static/captcha/';
    private $active = false;

    function __construct() {
        $this->CI =& get_instance();
        if (!isset($_SESSION["last_access"])){
            $_SESSION["last_access"] = null;
        }
    }

    public function create_by_access(){
        if (!$this->active){
            unset($_SESSION["captcha"]);
            return;
        }

        #caso apenas atualize a pagina
        if ($_SESSION["last_access"] != null){
            $this->create();
            $_SESSION["last_access"] = time();
        } else
        #quando for o primeiro acesso nao tem captcha
        if ($_SESSION["last_access"] == null){
            $_SESSION["last_access"] = time();
            
        } else {
            #verifica o intervalo de acesso, se for muito rapido tera captcha
            $last = $_SESSION["last_access"];
            if (time() - $last < $this->seconds){
                $this->create();
            }
            $_SESSION["last_access"] = time();

        }

    }


    public function create(){
        

        $vals = array(
            'img_path'      => $this->path,
            'img_url'       => base_url().$this->path,
            'font_path'     => './static/Streetvertising.ttf',
            'img_width'     => '250',
            'img_height'    => 100,
            'expiration'    => 7200,
            'word_length'   => 6,
            'font_size'     => 50,
            'img_id'        => 'Imageid',
            'pool'          => '0123456789ABCDEFGHIJKLMNOPQRSTUVXZKWY',
    
            // White background and border, black text and red grid
            'colors'        => array(
                    'background' => array(255, 255, 255),
                    'border' => array(255, 255, 255),
                    'text' => array(0, 0, 0),
                    'grid' => array(0, 40, 40)
            )
        );

        $this->CI->load->helper('captcha');
        $cap = create_captcha($vals);

        $_SESSION["captcha"] = $cap;
        return $cap;
    }

    public function form_validation($formValidation){

        if (_v($_SESSION,"captcha") != null){

            $_POST["captcha_correct"] = strtolower($_SESSION["captcha"]["word"]);
            $_POST["captcha"] = strtolower($_POST["captcha"]);
            $formValidation->set_rules('captcha',
                                        'Captcha',
                                        'required|matches[captcha_correct]',
                                        array("matches"=>"O captcha digitado está errado."));
            $formValidation->set_rules('captcha_correct',
                                        '',
                                        'required');

            $_SESSION["captcha"] = null;
        }
        
    }

    public function clear_dir(){
        $files = scandir($this->path);
        
        foreach ($files as $file) {
            if (file_exists($this->path.$file) && strstr($file,".jpg")) {
                unlink($this->path.$file);
            }
        }
    }


}


