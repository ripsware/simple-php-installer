<?php if(!defined('BASEPATH')) exit('No direct script allowed');

/**
 * Created by PhpStorm.
 * User: Rio Permana
 * Date: 05/09/2015
 * Time: 22:05
 */

/**
 * Class PHPInstaller
 */
class PHPInstaller extends CI_Model{

    public function __construct($appName = null, $appVersion = null, $actions = null, $configs = null){
        parent::__construct();
        if(is_array($appName)){
            foreach($appName as $key => $value){
                if(property_exists(get_class($this), $key)){
                    $this->$key = $value;
                }
            }
        }else {
            $this->appName = $appName;
            $this->appVersion = $appVersion;
            $this->actions = $actions;
            $this->configs = $configs;
        }
        $this->initializeActions();
        $this->initializeConfigs();
    }

    private function initializeActions(){
        if(is_array($this->actions)){
            $res = array();
            foreach($this->actions as $action){
                if(is_array($action)){
                    $res[] = new InstallerAction($action);
                }elseif($action instanceof InstallerAction){
                    $res[] = $action;
                }
            }
            $this->actions = $res;
        }else{
            $this->actions = array();
        }
    }

    private function initializeConfigs(){
        if(is_array($this->configs)){
            $res = array();
            foreach($this->configs as $config){
                if(is_array($config)){
                    $res[] = new InstallerConfig($config);
                }elseif($config instanceof InstallerConfig){
                    $res[] = $config;
                }
            }
            $this->configs = $res;
        }else{
            $this->configs = array();
        }
    }

    public function executeAction(){
        $res = array();
        foreach ($this->actions as $index => $act) {
            set_time_limit(0);
            if(isset($act) && $act instanceof InstallerAction){
                if($act->type == InstallerActionType::SQLQuery){
                    if(is_string($act->configs)){
                        preg_match('#\[(.*?)\]#', $act->configs, $match);
                        $match = explode(':', $match[1]);
                        $propName = $match[0];
                        $propVal = $match[1];
                        $act->configs = MyLinq::from($this->configs)
                            ->where(function(InstallerConfig $cfg)use($propName, $propVal){
                                return $cfg->$propName == $propVal;
                            })
                            ->select(function(InstallerConfig $cfg){
                                return $cfg->getValues();
                            })
                            ->first()
                        ;
                    }
                }
                $res[$index] = @$act->execute();
            }
        }

        foreach($this->configs as $cfg){
            set_time_limit(0);
            if(isset($cfg) && $cfg instanceof InstallerConfig){
                $cfg->doConfig();
            }
        }

        return $res;
    }

    public $appName = null;
    public $appVersion = null;
    public $actions = null;
    public $configs = null;

}

/**
 * Class InstallerConfig
 */
class InstallerConfig extends CI_Model{

    public function __construct($group = null, $label = null, $source = null, $dest = null, $fields = null, $icon = null){
        parent::__construct();
        if(is_array($group)){
            foreach($group as $key => $value){
                if(property_exists(get_class($this), $key)){
                    $this->$key = $value;
                }
            }
        }else {
            $this->group = $group;
            $this->label = $label;
            $this->source = $source;
            $this->dest = $dest;
            $this->fields = $fields;
            $this->icon = $icon;
        }
        $this->initializeFields();
    }

    private function initializeFields(){
        if(is_array($this->fields)){
            $res = array();
            foreach($this->fields as $field){
                if(is_array($field)){
                    $res[] = new InstallerConfigValue($field);
                }elseif($field instanceof InstallerConfigValue){
                    $res[] = $field;
                }
            }
            $this->fields = $res;
        }else{
            $this->fields = array();
        }
    }

    public function getValues(){
        $res = array();
        if(isset($this->fields) && is_array($this->fields) && count($this->fields)){
            foreach($this->fields as $field){
                if($field instanceof InstallerConfigValue){
                    $res[$field->name] = $field->value;
                }
            }
        }
        return $res;
    }

    public function doConfig(){
        if(isset($this->source) && is_file($this->source) && isset($this->dest)){
            if(strpos($this->dest, '.') > -1){
                $dir = explode('/', $this->dest);
                array_pop($dir);
                $dir = implode('/', $dir);
                if(!is_dir($dir)){
                    InstallerConfig::xmkdir($dir);
                }
                unset($dir);
            }else{
                if(!file_exists($this->dest)){
                    InstallerConfig::xmkdir($this->dest);
                }
                $dir = explode('/', $this->dest);
                $dir[] = array_pop(explode('/', $this->source));
                $this->dest = implode('/', $dir);
                unset($dir);
            }

            $search = array();
            $replace = array();
            foreach($this->fields as $field){
                if($field instanceof InstallerConfigValue){
                    $search[] = "{".$field->name."}";
                    if(is_array($field->value)){
                        $replace[] = "'".implode('\',\'', $field->value)."'";
                    }else{
                        $replace[] = $field->value;
                    }
                }
            }
            return file_put_contents($this->dest, str_replace($search, $replace, file_get_contents($this->source)));
        }
        return true;
    }

    public $group = null;
    public $label = null;
    public $source = null;
    public $dest = null;
    public $fields = array();
    public $icon = null;

    public static function xmkdir($path){
        if(isset($path)){
            if(!is_array($path)){
                $path = explode('/', $path);
            }
            $curPath = "";
            foreach($path as $pth){
                if(isset($pth) && $pth != ""){
                    $curPath .= "$pth/";
                    if($pth != ".."){
                        if(is_file($curPath)){
                            return false;
                        }elseif(!is_dir($curPath)){
                            if(!mkdir($curPath)){
                                return false;
                            }
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

}

/**
 * Class InstallerConfigValue
 */
class InstallerConfigValue extends CI_Model{

    public function __construct($name = null, $label = null, $type = InstallerActionType::Copy, $required = true, $value = null, $default = null, $valueLists = null, $readonly = false){
        parent::__construct();
        if(is_array($name)){
            foreach($name as $key => $value){
                if(property_exists(get_class($this), $key)){
                    $this->$key = $value;
                }
            }
        }else{
            $this->name = $name;
            $this->label = $label;
            $this->type = $type;
            $this->required = $required;
            $this->value = $value;
            $this->default = $default;
            $this->valueLists = $valueLists;
            $this->readonly = $readonly;
        }
        if(isset($this->default) && is_string($this->default) && strpos($this->default, '{') === 0){
            preg_match('#\{(.*?)\}#', $this->default, $match);
            $this->default = eval("return ".$match[1].";");
        }
        if(!isset($this->value) && isset($this->default)){
            $this->value = $this->default;
        }
    }

    public $name = null;
    public $label = null;
    public $type = null;
    public $required = false;
    public $value = null;
    public $default = null;
    public $valueLists = null;
    public $readonly = false;
}

/**
 * Class InstallerAction
 */
class InstallerAction extends CI_Model{

    public function __construct($type = InstallerActionType::Copy, $source = null, $destination = null, $configs = null){
        parent::__construct();
        if(is_array($type)){
            foreach($type as $key => $value){
                if(property_exists(get_class($this), $key)){
                    $this->$key = $value;
                }
            }
        }else{
            $this->type = $type;
            $this->source = $source;
            $this->destination = $destination;
            $this->configs = $configs;
        }
    }

    public $type = null;
    public $source = null;
    public $destination = null;
    public $configs = null;

    public function execute(){
        switch($this->type){
            case InstallerActionType::Copy:
                return $this->executeCopy();
                break;
            case InstallerActionType::Cut:
                return $this->executeCut();
                break;
            case InstallerActionType::Delete:
                return $this->executeDelete();
                break;
            case InstallerActionType::Extract:
                return $this->executeUnzip();
                break;
            case InstallerActionType::SQLQuery:
                return $this->executeQuery();
                break;
            case InstallerActionType::Run:
                return $this->executeRun();
                break;
        }
    }

    public function executeCopy(){
        if(isset($this->source) && file_exists($this->source) && isset($this->destination)){
            return InstallerAction::xcopy($this->source, $this->destination);
        }
        return false;
    }

    public function executeCut(){
        if(isset($this->source) && file_exists($this->source) && isset($this->destination)){
            return InstallerAction::rmove($this->source, $this->destination);
        }
        return false;
    }

    public function executeDelete(){
        if(isset($this->source) && file_exists($this->source)){
            return unlink($this->source);
        }
        return false;
    }

    public function executeUnzip(){
        InstallerConfig::xmkdir($this->destination);
        if(isset($this->source) && is_file($this->source) && is_dir($this->destination)){
            $zip = new ZipArchive();
            if ($zip->open($this->source)) {
                if($zip->extractTo($this->destination)){
                    return $zip->close();
                }
                $zip->close();
            }
        }
        return false;
    }

    public function executeQuery(){
        if(isset($this->source) && file_exists($this->source) && isset($this->configs) && is_array($this->configs) && count($this->configs) > 0){
            $this->load->database($this->configs);
            $queries = @SqlParser::parse(file_get_contents($this->source));
            $res = array();
            if(isset($queries) && is_array($queries)){
                foreach($queries as $qry){
                    if(isset($qry) && $qry != ""){
                        if($this->db->simple_query($qry)){
                            $res[] = array(
                                'qry'       => $qry,
                                'status'    => true,
                                'message'   => null
                            );
                        }else{
                            $res[] = array(
                                'qry'       => $qry,
                                'status'    => false,
                                'message'   => $this->db->error()
                            );
                        }
                    }
                }
                unset($queries);
                return $res;
            }
        }
        return false;
    }

    public function executeRun($command = null){
        if(!isset($command)){
            $command = $this->source;
        }
        if(isset($command)){
            if(is_array($command)){
                $res = array();
                foreach($command as $cmd){
                    if(isset($cmd)){
                        $res[] = $this->executeRun($cmd);
                    }
                }
                return $res;
            }elseif(is_string($command)){
                if(is_file($command) && strtolower(array_pop(explode('.', $command))) == 'php'){
                    return eval(file_get_contents($command));
                }elseif(!is_file($command)){
                    $res = null;
                    exec($command, $res);
                    return $res;
                }
                return false;
            }
        }
        return false;
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @param       string   $permissions New folder creation permissions
     * @return      bool     Returns true on success, false on failure
     */
    public static function xcopy($source, $dest, $permissions = 0755){
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            InstallerAction::xcopy("$source/$entry", "$dest/$entry", $permissions);
        }

        // Clean up
        $dir->close();
        return true;
    }

    /**
     * Recursively move files from one directory to another
     *
     * @param String $src - Source of files being moved
     * @param String $dest - Destination of files being moved
     */
    public static function rmove($src, $dest){
        // If source is not a directory stop processing
        if(!is_dir($src)) {
            return rename($src, $dest);
        };

        // If the destination directory does not exist create it
        if(!is_dir($dest)) {
            if(!mkdir($dest)) {
                // If the destination directory could not be created stop processing
                return false;
            }
        }

        // Open the source directory to read in files
        $i = new DirectoryIterator($src);
        foreach($i as $f) {
            if($f->isFile()) {
                rename($f->getRealPath(), "$dest/" . $f->getFilename());
            } else if(!$f->isDot() && $f->isDir()) {
                InstallerAction::rmove($f->getRealPath(), "$dest/$f");
            }
        }
        return unlink($src);
    }

}

/**
 * Class InstallerConfigValueType
 */
class InstallerConfigValueType{

    const Text = 'text';
    const Number = 'number';
    const Password = 'password';
    const StringLists = 'string_lists';
    const Boolean = 'bool';
    const Select = 'select';

}

/**
 * Class InstallerActionType
 */
class InstallerActionType{

    const Copy = 'copy';
    const Cut = 'cut';
    const Delete = 'delete';
    const Extract = 'extract';
    const SQLQuery = 'sql_query';
    const Run = 'run';

}