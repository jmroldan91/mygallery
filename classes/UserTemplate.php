<?php
class UserTemplate extends POJO {
    protected $idUser, $idTemplate, $title, $heading, $content, $footer, $active;
    
    function __construct($idUser=null, $idTemplate=null, $title="", $heading="", $content="", $footer="", $active='0') {
        $this->idUser=$idUser;
        $this->idTemplate=$idTemplate;
        $this->title->$title;
        $this->heading = $heading;
        $this->content = $content;
        $this->footer = $footer;
        $this->active = $active;
    }
    
    function getIdUser(){
        return $this->idUser;
    }
    
    function getIdTemplate(){
        return $this->idTemplate;
    }
    
    function getTitle(){
        return $this->title;
    }
    
    function getHeading(){
        return $this->heading;
    }
    
    function getFooter(){
        return $this->footer;
    }
    
    function getActive(){
        return $this->active;
    }
    
    function setIdUser($idUser){
        $this->idUser = $idUser;
    }
    
    function setIdTemplate($idTemplate){
        $this->idTemplate = $idTemplate;
    }
    
    function setTitle($title){
        $this->title = $title;
    }
    
    function setHeading($heading){
        $this->heading = $heading;
    }
    
    function setFooter($footer){
        $this->footer = $footer;
    }
    
    function setActive($active){
        $this->active = $active;
    }
    
    function getData(){
        $data = [];
        foreach($this as $key => $value){
            if($key != idUser && $key != 'idTemplate'){
                $data[$key] = $value;
            }
        }
        return $data;
    }
    
}