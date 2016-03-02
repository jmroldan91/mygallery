<?php
class WebPage{
    private $base, $head, $navBar, $content, $footer, $scripts;
    
    public function __construct(View $base=null, View $head=null, View $navBar=null, $content='', View $footer=null, View $scripts=null){
        $this->base = $base;
        $this->head = $head->render();
        $this->navBar = $navBar->render();
        $this->content = $content;
        $this->footer = $footer->render();
        $this->scripts = $scripts->render();
    }
    
    function setBase($base){
        $this->base = $base;
    }
    
    function setHead($head){
        $this->head = $head;
    }
    
    function setContent($content){
        $this->content = $content;
    }
    
    function setFooter($footer){
        $this->footer = $footer;
    }
    
    function setScripts($scripts){
        $this->scripts = $scripts;
    }
    
    function prepare(){
        $data = [];
        foreach ($this as $key => $value) {
            if($key!='base')
                $data[$key] = $value;
        }
        $this->base->setData($data);
    }
    function render(){
        $this->prepare();
        echo $this->base->render();   
    }
}