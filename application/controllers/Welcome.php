<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

    private $phpInstaller;

    public function __construct(){
        parent::__construct();

        // Loading configs
        $this->phpInstaller = new PHPInstaller($this->getConfig());
    }

	public function index(){
        $this->load->view('welcome', array('phpInstaller' => $this->phpInstaller));
	}

    public function configs(){
        $this->outputJson($this->phpInstaller->configs);
    }

    public function execute(){
        $configs = $this->input->post('configs');
        if(isset($configs) && is_array($configs)){
            foreach($configs as $key => $conf){
                if(is_array($conf)){
                    $configs[$key] = new InstallerConfig($conf);
                }
            }
            $this->phpInstaller->configs = $configs;
            $res = $this->phpInstaller->executeAction();
            $stat = true;
            foreach($res as $s){
                $stat = $stat && $s;
            }
            $this->outputJson($res, $stat);
        }else{
            $this->outputJson(null, false, 'Configs is empty');
        }
    }

    public function checkdb(){
        $cfg = $this->input->get_post('config');
        if(is_array($cfg)){
            $this->load->database($cfg);
            $this->load->dbutil();
            $this->outputJson($this->dbutil->list_databases());
        }else{
            $this->outputJson(null, false, 'Database config not available');
        }
    }
}
