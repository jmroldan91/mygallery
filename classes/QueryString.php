<?php

class QueryString {
    private $params;

    function __construct() {
        $this->params = $_GET;
    }
    
    function getParams($parametrosAdd = array(), $parametrosDelete = array()) {
        $copia = $this->params;
        $str = "";
        foreach ($parametrosDelete as $key => $value) {
            unset($copia[$key]);
        }
        foreach ($parametrosAdd as $key => $value) {
            $copia[$key]=$value;
        }        
        return Utils::ArrayToQuery($copia);
    }    
    
    function __toString() {
        $str="";
        foreach ($this as $key => $value) {            
            $str.=$key."=".$value."&";
        }
        return substr($str,0,-1);
    }
    
}
