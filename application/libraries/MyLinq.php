<?php
/**
 * Created by PhpStorm.
 * User: Rio Permana
 * Date: 06/09/2015
 * Time: 1:35
 */

/**
 * LINQ Expr Class
 *
 * Simple LINQ expression class
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Query
 * @author      Rio Permana
 * @email       rio.prmn@gmail.com
 * @link
 */
class MyLinqExpr{

    private $curobj;
    private $result;
    private $className;
    private $classObj;
    private $selectExpr;
    private $conditionalExpr;
    private $_offset;

    public function __construct($obj = null){
        if(is_string($obj)){
            $this->className = $obj;
            $this->classObj = new $this->className();
            $this->redefineProp($this->classObj);
        }else{
            $this->curobj = $obj;
            $this->result = $obj;
        }
    }

    private function redefineProp(&$obj){
        foreach(get_object_vars($obj) as $key => $val){
            if($val instanceof stdClass){
                $this->redefineProp($val);
            }else{
                $obj->$key = "$key";
            }
        }
    }

    public function select($closure = null){
        if(isset($this->result)){
            if(isset($closure)){
                $res = &$this->result;
                array_walk($this->result, function($val, $key)use($closure, &$res){
                    $res[$key] = call_user_func($closure, $val);
                });
            }
        }else{
            $this->selectExpr = call_user_func($closure, $this->classObj);
        }
        return $this;
    }

    public function all(){
        if(isset($this->result)){
            if(is_numeric($this->_offset)){
                return array_slice($this->result, $this->_offset);
            }else{
                return $this->result;
            }
        }else{
            return $this;
        }
    }

    public function walk($closure = null){
        $res = $this->all();
        if($res && isset($closure) && is_callable($closure)){
            array_walk($res, $closure);
        }
        unset($res);
        return $this;
    }

    public function first(){
        if(isset($this->result)){
            if(is_array($this->result) && count($this->result) > 0){
                if(is_numeric($this->_offset)){
                    return array_slice($this->result, $this->_offset, 1);
                }else{
                    return $this->result[0];
                }
            }else{
                return null;
            }
        }else{
            return $this;
        }
    }

    public function last(){
        if(isset($this->result)){
            if(is_array($this->result) && count($this->result) > 0){
                return $this->result[count($this->result) - 1];
            }else{
                return null;
            }
        }else{
            return $this;
        }
    }

    public function count(){
        if(isset($this->result)){
            if(is_array($this->result)){
                return count($this->result) - (is_numeric($this->_offset) ? $this->_offset : 0);
            }else{
                return 0;
            }
        }else{
            return $this;
        }
    }

    public function where($closure){
        if(isset($this->curobj) && is_array($this->curobj)){
            $this->result = array();
            $res = &$this->result;
            array_walk($this->curobj, function($value, $key)use(&$res, $closure){
                if($closure($value, $key)){
                    array_push($res, $value);
                }
            });
        }else{
            $this->conditionalExpr = $closure($this->classObj);
        }
        return $this;
    }

    function orderBy($closure){
        if(isset($this->result) && isset($closure)){
            usort($this->result, function($a, $b) use($closure){
                return $closure($a) > $closure($b);
            });
        }
        return $this;
    }

    function orderByDesc($closure){
        if(isset($this->result) && isset($closure)){
            usort($this->result, function($a, $b) use($closure){
                return $closure($b) > $closure($a);
            });
        }
        return $this;
    }

    public function skip($offset){
        $this->_offset = $offset;
        return $this;
    }

    public function offset($offset){
        $this->_offset = $offset;
        return $this;
    }

    public function take($count){
        if(isset($this->result)){
            return array_slice($this->result, is_numeric($this->_offset) ? $this->_offset : 0, is_numeric($count) ? $count : 1);
        }else{
            return $this;
        }
    }

}

class MyLinq{

    public static function AsQueryable(){
        return new MyLinqExpr(get_called_class());
    }

    public static function from($obj){
        return new MyLinqExpr($obj);
    }

}