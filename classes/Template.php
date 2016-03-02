<?php

class Template extends POJO {
    protected $id, $path, $thumbnail;
    
    function __construct($id=null, $path=null, $htumbnail=null) {
        $this->id=$id;
        $this->path=$path;
        $this->thumbnail->$tumbnail;
    }
    
    function getId(){
        return $this->id;
    }
    
    function getPath(){
        return $this->path;
    }
    
    function getThumbnail(){
        return $this->thumbnail;
    }
    
    function setId($id){
        return $this->id=$id;
    }
    
    function setPath($path){
        return $this->path=$path;
    }
    
    function setThumbnail($thumbnail){
        return $this->thumbnail=$thumbnail;
    }
    
}