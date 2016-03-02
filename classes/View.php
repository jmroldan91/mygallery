<?php
class View{
    private $path, //Ruta a la plantilla 
            $data; //Array de datos a mostrar en la plantilla
    public function __construct($path = null, $data = []){
        $this->path = $path;
        $this->data = $data;
    }
    function getPath(){
        return $this->path;
    }
    function getData(){
        return $this->data;
    }
    function setPath($path){
        $this->path = $path;
    }
    function setdata($data = []){
        $this->data = $data;
    }
    function render(){
        $content = file_get_contents($this->path);
        if($content){
            foreach ($this->data as $key => $value) {
                $content = str_replace('{'.$key.'}', $value, $content);
            }
            return $content;
        }else{
            return -1;
        }
    }
}