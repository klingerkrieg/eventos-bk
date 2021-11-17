<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Captcha extends CI_Controller {

    public function index(){
        $this->load->library("CaptchaLib");
        $cap = $this->captchalib->create();
        print base_url()."static/captcha/".$cap["filename"];
    }

}