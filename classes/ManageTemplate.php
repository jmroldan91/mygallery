<?php

class ManageTemplate extends ManagePOJO{
    protected $table='template';
    
    public function get($id) {
        $params = [];
        $params['id'] = $id;
        $r = $this->db->query($this->table, '*', $params);
        if($r!=-1){
            $row = $this->db->getRow();
            $template = new Template();
            $template->set($row);
            return $template;
        }else{
            return null;
        }
    }
    
    function getList($page = "1", $nrpp = Constant::_NRPP, $order = "1", $params = []) {
        $limit = ($page-1)*$nrpp . ',' . $nrpp;
        $this->db->query($this->table, "*", $params, $order, $limit);
        $r = [];
        while($row=$this->db->getRow()){
            $tmp = new Template();
            $tmp->set($row);
            $r[] = $tmp;
        }
        return $r;
    }
    
        function  createTable(){
        $sql = "create table if not exists `template` ( "
             . " id int not null auto_increment,"
             . " path varchar(80) not null,"
             . " thumbnail varchar(80),"
             . " primary key (id)"
             . ") engine=INNODB";
     return $this->db->send($sql);
    } 

}