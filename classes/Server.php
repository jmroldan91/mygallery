<?php

class Server {
    
    static function getRemoteAddr(){
        return $_SERVER['REMOTE_ADDR'];
    }
    
    static function getUserAgent(){
        return $_SERVER['HTTP_USER_AGENT'];
    }
    
    static function getRoot(){
        return $_SERVER['DOCUMENT_ROOT'];
    }
    
    static function getSelf(){
        return $_SERVER['PHP_SELF'];
    }
    
    static function getMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }
    
     static function getQuery(){
        return $_SERVER['QUERY_STRING'];
    }
    
    static function getReqTime(){
        return $_SERVER['REQUEST_TIME_FLOAT'];
    }
    
    static function getReqUri(){
        return $_SERVER['REQUEST_URI'];
    }

}
