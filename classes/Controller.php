<?php
class Controller{
    private $view, $userAlias, $table, $db, $mng, $campo, $filter, $nrpp, $order, $page, $op, $step, $result, $content, $secLevel;
    
    public function __construct(){
        $this->session = new Session();
        $this->db = new DataBase();
        $this->userAlias = Request::req('userAlias');
        /*Renderizado*/
        $this->view = Request::req('view');
        if($this->view == null){
            $this->view = "frontend"; //frontend | backend
        }
        /*Manager*/
        $this->table = Request::req('table');
        if($this->table == null){
            $this->table = 'template';
        }
        $this->mng = $this->getManager($this->table);
        /*Busqueda o filtrado */
        $this->campo = Request::req('campo');
        $this->filter = Request::req('filter');
        $this->arrayWhere =[];
        if($this->filter!=null && $this->filter!=""){
            $this->arrayWhere[$this->campo] = "%".$this->filter."%";
        }
        /*Listado paginado*/
        $this->nrpp = Request::req('nrpp');
        if($this->nrpp==null || $this->nrpp==""){
            $this->nrpp= Constant::_NRPP;
        }
        $this->order = Request::req('order');
        if($this->order==null || $this->order==""){
            $this->order = 1;
        }
        $this->page = Request::req('page');
        if($this->page==null || $this->page==""){
            $this->page = 1;
        }
        /* Operaciones */
        $this->op = Request::req('op');
        if($this->op==null || $this->op==""){
            $this->op = 'view';
        }
        $this->step = Request::req('step');
        if($this->step==null || $this->step==""){
            $this->step = '1';
        }
        $this->result = Request::req('result');
        if($this->result==null){
            $this->result = '';
        }
        $this->content = "";
        if(isset($_SESSION['user'])){
            $mngUser = new ManageUser($this->db);
            $this->secLevel=$mngUser->getLevel($_SESSION['user']);
        }else{
            $this->secLevel=-1;
        }
    }
    
    function getManager($table){
        $manager = "Manage".ucfirst(strtolower($table));
        if(class_exists($manager)){
            return new $manager(new DataBase());
        }else{
            return null;
        }
        
    }
    
    function getObject($table){
        $object = ucfirst(strtolower($table));
        if(class_exists($object)){
            return new $manager();
        }else{
            return null;
        }
    }
    
    function checkUser(){
        if($this->secLevel <= 1){
            echo '{ "result" : "Permision denied" }';
            exit();
        }
    }
    
    function load(){
        $metodo = $this->op;
        $controller = ucfirst(strtolower($this->table)).'Controller';
        if(class_exists($controller)){
            $ctl = new $controller();
            if(method_exists($ctl, $metodo)){
                $ctl->$metodo();
            }else{
                $this->view();
            }
        }else{
            if(method_exists($this, $metodo)){
                $this->$metodo();
            }else{
                $this->view();
            }
        }
    }
    
    function read(){
        $pages = $this->mng->getNumReg($this->arrayWhere);
        echo '{ "resultset" : ' . $this->mng->getListJSON($this->page, $this->nrpp, $this->order, $this->arrayWhere) . ', "pages" : '.$pages.'}';
    }

    function get(){
        $pkid = Request::req('pkid');
        $obj = $this->mng->get($pkid);
        if($obj != null){
            echo '{ "result" : 1 , "resultset" : '.$obj->toJSON().' }';
        }else{
            echo '{ "result" : -1 }';
        }
    }
    
    function insert(){
        $this->checkUser();
        $object = $this->getObject($this->table);
        $object->read();
        $r = $this->mng->insert($object);
        echo '{ "result" : ' . $r . ', "obj" : '.$object->toJSON().' }';
    }
    
    function set(){
        $this->checkUser();
        $object = $this->getObject($this->table);
        $object->read();
        $r = $this->mng->set($object, Request::req('pkid'));
        echo '{ "result" : ' . $r . ' }';
    }
    
    function delete(){
        $this->checkUser();
        $r = $this->mng->delete(Request::req('pkid'));
        echo '{ "result" : "' . $r .'"}';
    }
    
    function view(){ //Carga la vista de la pagina (front (lista de galerias o galeria individual) | back)
        if($this->view == 'frontend'){
            $mngUserTpl = new ManageUserTemplate($this->db);
            $dataArray = [];
            if($_SESSION['user']!=null) $dataArray['singin'] = '<li><a class="page-scroll" href="?op=view&view=backend">Control panel</a></li><li><a id="btnsingout" class="page-scroll" href="#">Sing out</a>';
            else $dataArray['singin'] = '<li><a class="page-scroll" href="?op=view&view=singin">Sing in</a></li>';
            //Lista de galerias
            $str ="";
            foreach($mngUserTpl->getList() as $key => $gallery){
                $str .= '<div class="col-lg-4 col-md-6 col-ns-12">
                            <h3>'.$gallery->getTitle().'</h3>
                            <figure>
                                <a href="index.php?op=view&userAlias='.$gallery->getIdUser().'"><img class="img-responsive" src="tpl/gallery1/gallery1.jpg" /></a>
                                <figcaption>
                                    <h4><small>by '.$gallery->getIdUser().'</small></h4>
                                </figcaption>
                            </figure>
                        </div>';
            }
            $dataArray['galleries'] = $str;    
            if($this->userAlias == null){
                $view = new View('tpl/front-list.php', $dataArray);
                echo $view->render();
            }else{
                $this->getUserGallery();
            }
        }else if($this->view == 'backend'){
            $this->getBackend();
        }else if($this->view == 'singin'){
            //login page
             if($_SESSION['user']==null){
                $view = new View('tpl/login.tpl', array());
            echo $view->render();
            }else{
                Utils::redirect('?op=view&view=backend');
            }
        }
    }
    
    function getUserGallery(){
        //galeria de un usuario
        $dataArray = [];
        $single="";
        $list="<div class='col-lg-12'>";
        $mmgUser = new ManageUser($this->db);
        $user = $mmgUser->get($this->userAlias) != null ?  $mmgUser->get($this->userAlias) :  $mmgUser->getByAlias($this->userAlias);
        $mngUserTpl = new ManageUserTemplate($this->db);
        $mngTpl = new ManageTemplate($this->db);
        $userTemplate = $mngUserTpl->getTplActive($user->getEmail());
        $dataArray = $userTemplate->toArray();
        if($_SESSION['user']!=null) $dataArray['singin'] = '<li><a class="page-scroll" href="?op=view&view=backend">Control panel</a></li><li><a id="btnsingout" class="page-scroll" href="#">Sing out</a>';
        else $dataArray['singin'] = '<li><a class="page-scroll" href="?op=view&view=singin">Sing in</a></li>';
        //Post
        $mngEntry = new ManageEntry($this->db);
        $entries = $mngEntry->getList("1", Constant::_NRPP, "1", array('idUser' => Request::req('userAlias')));
        $single .= '<div class=""col-lg-12>';
        $single .= '<figure><img class="img-responsive" src="img/'.$entries[0]->getImage().'" />';
        $single .= '<figcaption><h5>'.$entries[0]->getTitle().'</h5><p>'.$entries[0]->getContent().'</p></figcaption></figure>';
        $single .= "</div>";
        foreach($entries as $key => $entry){
            $list .= '<div class=""col-lg-6 col-md-6 col-xs-6>';
            $list .= '<figure><img class="img-responsive" src="img/'.$entry->getImage().'" />';
            $list .= '<figcaption><h5>'.$entry->getTitle().'</h5></figcaption></figure>';
            $list .= "</div>";
        }
        $list .= "</div>";
        $dataArray['single'] = $single;
        $dataArray['list'] = $list;
        if($user != null && $userTemplate != null){
            $idTpl = $userTemplate->getIdTemplate();
            $tpl = $mngTpl->get($idTpl);
            unset($dataArray['galleries']);
            if(file_exists($tpl->getPath())){
                $view = new View($tpl->getPath().'index.html', $dataArray);
                echo $view->render();
            }else{
                Utils::redirect('?op=view&view=frontend');
            }
        }else{
            Utils::redirect('?op=view&view=frontend');
        }
    }
    
    function getBackend(){
        //backend
        if($_SESSION['user'] !=null){
            $user = $_SESSION['user'];
            $hideUser = ($user->getAdministrador() == 1) ? "" : "hidden";
            $templates = ""; $users = ""; $entries = ""; 
            //Tempates
            $mngTpl = new ManageTemplate($this->db);
            $tpls = $mngTpl->getList();
            $dataArray = [];
            foreach($tpls as $k => $tpl){
                $temp = $tpl->toArray();
                if($user->getTemplate()!=null){
                    $temp['selected'] = ($user->getTemplate()->getIdTemplate() == $tpl->getId()) ? 'success' : 'danger';
                }else{
                    $temp['selected'] = 'danger';
                }
                $viewTpl = new View('tpl/backend/template.php', $temp);
                $templates .= $viewTpl->render();
            }
            //Entries 
            $mngEnt = new ManageEntry($this->db);
            $ents = $mngEnt->getList("1", Constant::_NRPP, "1", array('idUser' => $user->getEmail()));
            foreach($ents as $k => $entry){
                $temp = $entry->toArray();
                $viewTpl = new View('tpl/backend/entries.php', $temp);
                $entries .= $viewTpl->render();
            }
            $dataArray = array('user_alias'=>$user->getAlias(), 'hideuser'=>$hideUser, 'user_email'=>$user->getEmail(), 'template'=>$templates, 'entries'=>"", 'users'=>$users );
            $view = new View('tpl/backend/index.html', $dataArray);
            echo $view->render();
        }
    }
    
    function setTemplate(){
        $tplId = Request::req('idTemplate');
        $user = $_SESSION['user'];
        $mngUsTpl = new ManageUserTemplate($this->db);
        $userTemplate = $mngUsTpl->get($user->getEmail(), $tplId);
        if($userTemplate == null){
            $cl = $mngUsTpl->deActiveAll($user->getEmail(), $tplId);
            $userTemplate = new UserTemplate($user->getEmail(), $tplId);
            $userTemplate->setActive(1);
            $r = $mngUsTpl->insert($userTemplate);
        }else{
            if($userTemplate->getActive() == 1){
                $r=1;
            }else{
                $cl = $mngUsTpl->deActiveAll($user->getEmail(), $tplId);
                $userTemplate->setActive('1');
                $r = $mngUsTpl->set($userTemplate, $user->getEmail());   
            }
        }
        echo '{"result": '.$r.', "error": '.json_encode($this->db->getQueryError()).', "tpl" : '.$userTemplate->toJSON().'}';
    }
    
    function saveTemplate(){
        $user = $_SESSION['user'];
        $mngUsTpl = new ManageUserTemplate($this->db);
        $userTemplate = new UserTemplate();
        $userTemplate->read();
        $userTemplate->setIdUser($user->getEmail());
        $userTemplate->setActive('1');
        $r = $mngUsTpl->set($userTemplate, $user->getEmail());
        echo '{"result": '.$r.', "error": '.json_encode($this->db->getQueryError()).', "tpl" : '.$userTemplate->toJSON().'}';
    }
    
    function insertEntry(){
        $this->checkUser();
        $mng = new ManageEntry($this->db);
        $object = new Entry();
        $object->read();
        $object->setIdUser($_SESSION['user']->getEmail());
        $r = $mng->insert($object);
        $object->setId($r);
        echo '{ "result" : ' . $r . ',"error": '.json_encode($this->db->getQueryError()).', "obj" : '.$object->toJSON().' }';
    }
    
    function uploadFile(){
         $alias = $_SESSION['user']->getAlias();
         $file = $_FILES['image'];
         $up = new UploadFile($file);
         $up->setName($alias.'-'.$up->getName());
         $r = $up->upload();
         echo '{"result" : '.$r.', "filename" : "'.$up->getName().'.'.$up->getExt().'"}';
     }
    function install(){
        /*Creacion de tablas de la base datos*/
        $manage = new ManageUser($this->db);
        $manage->createTable();
        var_dump($this->db->getQueryError());
        $manage = new ManageEntry($this->db);
        $manage->createTable();
        var_dump($this->db->getQueryError());
        $manage = new ManageTemplate($this->db);
        $manage->createTable();
        var_dump($this->db->getQueryError());
        $manage = new ManageUserTemplate($this->db);
        $manage->createTable();
        var_dump($this->db->getQueryError());
    }
}
