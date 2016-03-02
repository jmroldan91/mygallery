<?php

class ManageUserTemplate extends ManagePOJO{
    protected $table='userTemplate';
    
    public function get($idUser, $idTemplate) {
        $params = [];
        $params['iduser'] = $idUser;
        $params['idTemplate'] = $idTemplate;
        $r = $this->db->query($this->table, '*', $params);
        if($r!=-1){
            $row = $this->db->getRow();
            $ut = new UserTemplate();
            $ut->set($row);
            return $ut;
        }else{
            return null;
        }
    }
    
    public function getTplActive($idUser) {
        $params = [];
        $params['idUser'] = $idUser;
        $params['active'] = 1;
        $r = $this->db->query($this->table, '*', $params);
        if($r!=-1){
            $row = $this->db->getRow();
            $ut = new UserTemplate();
            $ut->set($row);
            return $ut;
        }else{
            return null;
        }
    }
    
    function set(POJO $pojo, $pkid){
        $paramsWhere = array();
        $paramsWhere['idUser'] = $pkid;
        $paramsWhere['idTemplate'] = $pojo->getIdTemplate();
        return $this->db->update($this->table, $pojo->toArray(), $paramsWhere);
    }
    
    function getList($page = "1", $nrpp = Constant::_NRPP, $order = "1", $params = []) {
        $limit = ($page-1)*$nrpp . ',' . $nrpp;
        $this->db->query($this->table, "*", array('active' => 1), $order, $limit);
        $r = [];
        while($row=$this->db->getRow()){
            $tmp = new UserTemplate();
            $tmp->set($row);
            $r[] = $tmp;
        }
        return $r;
    }
    
    function deActiveAll($idUser, $tpl){
        $sql = "update userTemplate set active='0' where iduser = '".$idUser."' and idTemplate != ".$tpl." ";
        $params = [];
        $s = $this->db->send($sql, $params);
        return $sql .' '.$s;
    }
    
    function  createTable(){
        $sql = "create table if not exists `userTemplate` ( "
             . " iduser varchar(80) not null,"
             . " idTemplate int not null,"
             . " title varchar(40),"
             . " heading varchar(80),"
             . " content text,"
             . " footer varchar(80),"
             . " active tinyint default 0,"
             . " primary key (iduser, idTemplate, active),"
             . " FOREIGN KEY (iduser) 
                    REFERENCES user(email)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,"
             . " FOREIGN KEY (idTemplate) 
                    REFERENCES template(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE"
             . ") engine=INNODB;
             CREATE UNIQUE INDEX userTemplate_index ON userTemplate (iduser, idTemplate)";
     return $this->db->send($sql);
    } 

}