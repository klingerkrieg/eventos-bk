<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('load_controller'))
{
    function load_controller($controller, $method = 'index',...$args)
    {
        require_once( APPPATH . 'controllers/' . $controller . '.php');

        $controller = new $controller();

        $run = $controller->$method(...$args);
        print validation_errors();
        return $run;
    }
}