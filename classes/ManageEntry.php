<?php

class ManageEntry extends ManagePOJO{
    protected $table='entry';
    
    public function get($id) {
        $params = [];
        $params['id'] = $id;
        $r = $this->db->query($this->table, '*', $params);
        if($r!=-1){
            $row = $this->db->getRow();
            $entry = new Entry();
            $entry->set($row);
            return $entry;
        }else{
            return null;
        }
    }
    
    function getList($page = "1", $nrpp = Constant::_NRPP, $order = "1", $params = []) {
        $limit = '';
        $this->db->query($this->table, "*", $params, $order, $limit);
        $r = [];
        while($row=$this->db->getRow()){
            $tmp = new Entry();
            $tmp->set($row);
            $r[] = $tmp;
        }
        return $r;
    }
    
    function insert(POJO $pojo){
        return $this->db->insert($this->table, $pojo->toArray(), true);
    }
    
    function  createTable(){
        $sql = "create table if not exists `entry` ( "
             . " id int not null auto_increment,"
             . " idUser varchar(80) not null,"
             . " title varchar(40),"
             . " content text,"
             . " image varchar(60),"
             . " primary key (id),"
             . " FOREIGN KEY (iduser) 
                    REFERENCES user(email)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE"
             . ") engine=INNODB";
     return $this->db->send($sql);
    } 

}