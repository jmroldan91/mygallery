<?php

class Filter {
    static function isEmail($param){
        return filter_var($param, FILTER_VALIDATE_EMAIL);
    }
    static function isIP($param){
        return filter_var($param, FILTER_VALIDATE_IP);
    } 
    static function isURL($param){
        return filter_var($param, FILTER_VALIDATE_URL);
    }
    static function isInt($param){
        return filter_var($param, FILTER_VALIDATE_INT);
    }
    static function isNumber($param){
        return (self::isInt($param) || self::isFloat($param));
    }
    static function isFloat($param){
        return filter_var($param, FILTER_VALIDATE_FLOAT);
    }
    static function isMAC($param){
        return filter_var($param, FILTER_VALIDATE_MAC);
    }
    static function isBoolean($param){
        return filter_var($param, FILTER_VALIDATE_BOOLEAN);
    }
    static function isMinLength($param, $len){
        return strlen($param)>=$len;
    }
    static function isMaxLength($param, $len){
        return strlen($param)<=$len;
    }
    static function isPrimo($num){
        for($i=2;$i<$num/2;$i++){
            if($num%$i==0){
                return false;
            }
        }
        return true;
    }
    static function isLogin($param){
        return preg_match('/^[A-Za-z][A-za-z0-9]$/', $param);
    }
    
    static function isDate($param){
        $pasts = explode('-', $param);
        if(count($pasts)!=3){
            return false;
        }
        if(strlen($pasts[0]) === 4 && self::isInt($pasts[0])){
            if(strlen($pasts[1]) === 2 && self::isInt($pasts[1])){
                if(strlen($pasts[2]) === 2 && self::isInt($pasts[0])){
                    return true;
                }
            }
        }
        return false;
    }
    
    static function isGender($paran){
        return $param="H" || $paran="M";
    }

    static function haveValue($param){
        return ($param !== null && $param !== "");
    }

    static function validateUser(Usuario $user){
        //validamos el nombre (requerido)
        if( !(self::isMaxLength($user->getName(),40) && self::haveValue($user->getName())) ){ return 1; }
        //validamos los apellidos (requerido)
        if( !(self::isMaxLength($user->getSurname(),80) && self::haveValue($user->getSurname())) ){ return 2; }
        //validamos los email (requerido)
        if( !(self::isEmail($user->getMail()) && self::isMaxLength($user->getMail(),100) && self::haveValue($user->getSurname())) ){ return 3; }
        //validamos los email (requerido)
        if( !(self::isEmail($user->getMail()) && self::isMaxLength($user->getMail(),100) && self::haveValue($user->getSurname())) ){ return 4; }
        //validamos la fecha de nacimiento
        if( !(self::isDate($user->getBdate()))){ return 5; }
        //validamos el sexo
        if( !(self::isGender($user->getGender()))){ return 6; }
        //validamos el login
        if( !(self::isMaxLength($user->getLogin(),40) && self::isLogin($user->getLogin())) ){ return 7; }
        //validamos el alias
        if( !(self::isMaxLength($user->getAlias(),40))){ return 8; }
        //validamos la contraseña
        if( !(self::isMinLength($user->getPass(),5) && self::isMaxLength($user->getPass(),20))){ return 9; }
        //validamos el nivel de seguridad
        $leves = array(0,1,2);
        if( !(self::isInt($user->getLevel()))){ return 10; }
        foreach ($leves as $value) {
            if($user->getLevel() !== $value){
                return 10;
                break;
            }
        }
        //validamos el estado
        if( !(self::isBoolean($user->getAct()))){ return 11; }
    }
}
