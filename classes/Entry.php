<?php

class Entry extends POJO {
    protected $id, $idUser, $title, $content, $image;
    
    function __construct($id=null, $idUser=null, $title=null, $content=null, $image=null) {
        $this->id=$id;
        $this->idUser = $idUser;
        $this->title=$title;
        $this->content=$content;
        $this->image=$image;
    }
    
    function getId(){
        return $this->id;
    }
    
    function getIdUser(){
        return $this->idUser;
    }
    
    function getTitle(){
        return $this->title;
    }
    
    function getContent(){
        return $this->content;
    }
    
    function getImage(){
        return $this->image;
    }
    
    function setId($id){
        $this->id = $id;
    }
    
    function setidUser($idUser){
        $this->idUser = $idUser;
    }
    
    function setTitle($title){
        $this->title = $title;
    }
    
    function setContent($content){
        $this->content = $content;
    }
    
    function setImage($image){
        $this->image = $image;
    }
    
}