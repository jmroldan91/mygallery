<?php

class Pager {
     private $registros, $nrpp, $paginaActual;
     
     function __construct($registros, $nrpp=Constant::_NRPP, $paginaActual=1) {
         if($nrpp===null || !is_numeric($nrpp)){
             $nrpp=Constant::_NRPP;
         }
         if($paginaActual===null || !is_numeric($paginaActual)){
             $paginaActual=1;
         }
         $this->registros = $registros;
         $this->nrpp = $nrpp;
         $this->paginaActual = $paginaActual;
    }         

    function getRegistros() {
        return $this->registros;
    }

    function getRpp() {
        return $this->nrpp;
    }

    function getPaginaActual() {
        return $this->paginaActual;
    }

    function getPrimera(){
        return 1;
    }
    
    function getAnterior(){
        return max(1, $this->paginaActual-1);
    }
    
    function getSiguiente(){
        return min($this->getPaginas(), $this->getPaginaActual()+1);
    }

    function getPaginas(){
        return ceil($this->registros/$this->nrpp);
    }
    
    function getSelect($id='nrpp', $name = null){        
        if($name===null){
            $name="name='".$id."'";
        }else{
            $name = "name='".$name."'";
        }
        $str = "<select id='".$id."' $name>";
        for($i=10; $i<=50;$i=$i+10){
            $selected = "";
            if($this->nrpp === $i){$selected="selected";}
            $str .= "<option value='".$i."' $selected>$i</option>";
        }
        $str .= "</select>";        
        return $str;
    }    

    function setRegistros($registros) {
        $this->registros->$registros;
    }

    function setNrpp($nrpp) {
        $this->nrpp=$nrpp;
    }

    function setPaginaActual($paginaActual) {
        $this->paginaActual = $paginaActual;
    }   

    function getEnlacesPaginas($enlace, $pagina = 1){
        $query = new QueryString();
        $parametrosAdd = ['page'=>$pagina];
        $parametrosDelete = ['page'=>$pagina];
        return $enlace.'?'.$query->getParams($parametrosAdd, $parametrosDelete); 
    }
    
    function getHTMLPager($enlace, $rango = 'all'){ /*$rango nummero de enlaces amostrar en el paginador o all para todos , none o 0 para ninguno*/
        if($this->getPaginas() == 1){
            return '';
        }
        $numEnlaces; $ini; $fin;
        switch($rango){
            case 'all': $numEnlaces = $this->getPaginas(); 
                        $ini = 1;
                        $fin = $this->getPaginas();
                        break;
            case 'none': $numEnlaces = 0; 
                         $ini = 0;
                         $fin = 0;
                         break;
            default: $numEnlaces = $rango; 
                     $ini = max(1,$this->getPaginaActual()-ceil($rango/2));
                     $fin = min($this->getPaginas(), $ini+$rango);
                     break;
        }
        $disPrev = $this->getPaginaActual() == 1 ? 'disable' : ' ';
        $disNext = $this->getPaginaActual() == $this->getPaginas() ? 'disable' : ' ';
        $str = '<ul class="pagination">
                    <li title="Primera Página">
                       <a href="'.$this->getEnlacesPaginas($enlace, $this->getPrimera()).'" aria-label="Previous">
                        <span aria-hidden="true">&laquo;&laquo;</span>
                      </a>
                    </li>
                    <li class="'.$disPrev.'" title="Anterior">
                       <a href="'.$this->getEnlacesPaginas($enlace, $this->getAnterior()).'" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                      </a>
                    </li>';
        $i=$ini;
        while($i<=$this->getPaginas() && $i<=$fin){
            $activo = $this->getPaginaActual() == $i ? 'active' : ' ';
            $str .= '<li class="'.$activo.'" title="Página '.$i.' de '.$this->getPaginas().'"><a href="'.$this->getEnlacesPaginas($enlace, $i).'">'.$i.'</a></li>';
            $i++;
        }
        $str .= '   <li class="'.$disNext.'" title="Siguiente">
                      <a href="'.$this->getEnlacesPaginas($enlace, $this->getSiguiente()).'" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                      </a>
                    </li>
                     <li title ="Última página">
                      <a href="'.$this->getEnlacesPaginas($enlace, $this->getPaginas()).'" aria-label="Next">
                        <span aria-hidden="true">&raquo;&raquo;</span>
                      </a>
                    </li>
                  </ul>';
        return $str;
    }
}
