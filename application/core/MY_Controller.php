<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Rio Permana
 * Date: 05/09/2015
 * Time: 22:05
 */

class MY_Controller extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->loadConfigs();
    }

    protected function loadConfigs(){
        if(is_file('installer-resources/php-installer.conf.php')){
            include('installer-resources/php-installer.conf.php');
            $this->config->config = array_merge($this->config->config, $config);
        }
    }

    protected function getConfig($name = null, $key = null){
        if(isset($name) && isset($key)){
            $conf = $this->config->config[$name];
            if(isset($conf[$key])){
                $conf = $conf[$key];
            }else{
                $conf = null;
            }
            return $conf;
        }else{
            $conf = $this->config->config['php_installer'];
            if(isset($name)){
                if(isset($conf[$name])){
                    $conf = $conf[$name];
                }else{
                    $conf = null;
                }
            }
            return $conf;
        }
    }

    public function outputJson($data, $state = true, $message = null, $additional = null){
        if($additional && is_array($additional)){
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array_merge(array(
                    'status'    => $state,
                    'result'    => $this->formateData($data),
                    'message'   => $message,
                ), $additional)));
        }else{
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status'    => $state,
                    'result'    => $this->formateData($data),
                    'message'   => $message,
                )));
        }
    }

    private function formateData($data){
        if(!isset($data)){
            return $data;
        }
        if(is_string($data) && $data == '0000-00-00 00:00:00'){
            return null;
        }elseif(is_numeric($data)){
            return $data + 0;
        }elseif(MY_Controller::isValidDate($data)){
            return date('Y-m-d\TH:i:s.z\Z', strtotime($data));
        }elseif(is_array($data)){
            foreach($data as $i => $val){
                $data[$i] = $this->formateData($val);
            }
        }elseif(is_object($data)){
            $field = get_object_vars($data);
            foreach($field as $f => $val){
                $data->$f = $this->formateData($val);
            }
        }
        return $data;
    }

    public static function isValidDate($date){
        if($date instanceof DateTime){
            return true;
        }elseif(is_string($date)){
            if($date == '0000-00-00 00:00:00'){
                return false;
            }elseif(DateTime::createFromFormat('Y-m-d G:i:s', $date) === false && DateTime::createFromFormat('Y-m-d\TG:i:s.z\Z', $date) === false){
                return false;
            }
            return true;
        }
        return false;
    }

}