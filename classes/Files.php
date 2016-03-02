<?php

class Files {
    
    static function mkDir($pathname){
        if(!is_dir($filename)){
            return mkdir($pathname);
        }
        return null;
    }
    
    static function delDir($pathname){
        if(is_dir($filename)){
            return rmdir($pathname);
        }
        return null;
    }
    
    static function getDirContent($pathname){
        if(is_dir($pathname)){
            $content = scandir($pathname, 1);
            array_pop($content);
            array_pop($content);
            return $content;
        }else            
        return null;
    }

    static function mkFile($name){
        return fopen($name,'w+');
    }
    
    static function  delFile($name){
        if(is_file($name)){
            return unlink($name);
        }
        return null;
    }
    
    static function saveFile($file){
        return fclose($file);
    }
    
    static function renameFile($old, $new){
        if(is_file($old)){
            return rename($old, $new);
        }
        return null;
    }
    
    static function copyFile($old, $new){
        if(is_file($old)){
            return copy($old, $new);
        }
        return null;
    }
    
    static function getFileName($path){
        return pathinfo($path, PATHINFO_FILENAME);
    }
    
    static function getFileDir($path){
        return pathinfo($path, PATHINFO_DIRNAME);
    }
    
    static function getFileExtension($path){
        return pathinfo($path, PATHINFO_EXTENSION);
    }
    
    static function getFileFullName($path){
        return pathinfo($path, PATHINFO_BASENAME);
    }
}
