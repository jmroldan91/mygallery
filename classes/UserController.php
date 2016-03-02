<?php 
class UserController extends Controller{
    
    private $view, $userAlias, $table, $db, $mng, $campo, $filter, $nrpp, $order, $page, $op, $step, $result, $content, $secLevel;
    
    public function __construct(){
        $this->db = new DataBase();
        $this->userAlias = Request::req('userAlias');
        /*Renderizado*/
        $this->view = Request::req('view');
        if($view == null){
            $view = "frontend"; //frontend | backend
        }
        /*Manager*/
        $this->table = "user";
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
        header('Content-type: application/json');
    }

    function read(){
        $pages = $this->mng->getNumReg($this->arrayWhere);
        echo '{ "users" : ' . $this->mng->getListJSON($this->page, $this->nrpp, $this->order, $this->arrayWhere) . ', "pages" : '.$pages.' }';
    }
    
    function register(){
        $email = Request::req('mail');
        $pass1 = Request::req('pass1');
        $pass2 = Request::req('pass2');
        if($pass1 !== $pass2){
            echo '{ "result" : "Incorrect password", "type" : "warning"}';
        }
        $r = $this->mng->newUserGuest($email, $pass1);
        if($r!=-1){
            $hash = $this->mng->getHash($email);
            $url = "https://mygallery-jmroldan00.c9users.io/user/activar.php?m=$email&hash=$hash";
            $subject = "Confirmación de alta";
            $to = $email;
            $message = $url;
            $mail = new Mail($to, $subject, $message);
            $r = $mail->send();
            if($r===1){
                echo '{ "result" : "User Registered", "type" : "success"}';
            }
            echo '{ "result" : "User Registered, but error sending activation mail", "type" : "error"}';
        }else{
            echo '{ "result" : "Can not reegister the user, database error", "type" : "error"}';
        }
    }
    
    function login(){
        $login = Request::req('login');
        $pass1 = Request::req('pass1');
        $user = $this->mng->get($login);
        if($user->getEmail() == null){
            $user = $this->mng->getByAlias($login);
        }
        if($user !== null && $user->getPass() === sha1($pass1) && $user->getActivo() == 1){
            $_SESSION['user']=$user;
            echo '{ "result" : "logged", "type" : "success"}';
        }else{
            echo '{ "result" : "login", "type" : "error"}';
        }
    }
    
    function logout(){
        if(isset($_SESSION)){
            session_destroy();
        }
        echo '{ "result" : "logout", "type" : "success"}';
    }
    
    function recovery(){
        $email = Request::req(mail);
        $user = $this->mng->get($email);
        if($user!=null){
            $hash = $this->mng->getHash($email);
            $url = "https://mygalley-jmroldan00.c9users.io/user/recuperar.php?m=$email&hash=$hash";
            $subject = "Recuperación de contraseña";
            $to = $email;
            $message = $url;
            $mail = new Mail($to, $subject, $message);
            $r = $mail->send();
            if($r===1){
                echo '{ "result" : "Recovery mail sended", "type" : "success"}';
            }else{
                echo '{ "result" : "Error sending email", "type" : "warning"}';
            }
        }else{
            echo '{ "result" : "user does not exists", "type" : "error"}';
        }
    }
    
    function delete(){
        $this->checkUser();
        $email = Request::req('pkid');
        $user = $this->get($email);
        $paramsWhere = [];
        $paramsWhere['email'] = $email;
        $paramsSet = [];
        $paramsSet['activo'] = 0;
        if($user->getEmail()!='' || $user->getEmail()!=null){
            $r = $this->db->update($this->table, $paramsSet, $paramsWhere);
            if($r!=-1){
                echo '{ "result" : "User deleted", "type" : "success"}';
            }
            if($user->getEmail() == $_SESSION['user']->getEmail()){
                $this->userLogOut();
            }
        }else{
            echo '{ "result" : "User does not exists", "type" : "error"}';
        }
    }
    
    function update(){
        $this->checkUser();
        $paramsWhere = [];
        $paramsWhere['email'] = Request::req('pkid');    
        $paramsSet = [];
        $paramsSet['email'] = Request::req('email');
        $paramsSet['alias'] = Request::req('alias');
        unset($paramsSet['pass']);
        $r = $this->db->update($this->table, $paramsSet, $paramsWhere);
        if($r=1){
            $_SESSION['user'] = $this->mng->get(Request::req('email'));
            echo '{ "result" : "User updated", "type" : "success"}';
        }else{
            echo '{ "result" : "Error updating the user updated", "type" : "error"}';
        }
    }
    
    function active(){
        $email = Request::req('pkid');
        if($email == null){
            $email = Request::req('m');
        }
        $user = $this->mng->get($email);
        $paramsWhere = [];
        $paramsWhere['email'] = $email;
        $paramsSet = [];
        $paramsSet['activo'] = 1;
        if($user->getEmail()!=''){
            $r = $this->db->update($this->table, $paramsSet, $paramsWhere);
            if($r != -1){
                echo '{ "result" : "User active", "type" : "success"}';
            }else{
                echo '{ "result" : "Error activating", "type" : "error"}';
            }
        }else{
            echo '{ "result" : "User does not exists", "type" : "error"}';
        }
    }
    
    function create(){
        $this->checkUser();
        $pass1 = Request::req('pass');
        $pass2 = Request::req('pass2');
        if($this->secLevel == 3){
            if($pass1 !== $pass2){
                echo '{ "result" : "Passwords does not match", "type" : "warning"}';
            }else{
                $user = new User();
                $user->read();
                $user->setFechaAlta(date_create()->format('Y-m-d H:i:s'));
                $user->setPass(sha1($pass1));
                if($user->getActivo() == null) $user->setActivo('0');
                if($user->getAdministrador() == null) $user->setAdministrrador('0');
                if($user->getPersonal() == null) $user->setPersonal('0');
                $v = $this->mng->validate($user);
                if( $v == 1){
                    $r = $this->mng->insert($user);
                    if($r!=-1){
                        echo '{ "result" : "User created", "type" : "success"}';
                    }else{
                        echo '{ "result" : "Error creating user", "type" : "error"}';
                    }
                }else{
                    echo '{ "result" : "User is not valid", "type" : "warning"}';
                }
            }
        }else if($this->secLevel == 2){
            if($pass1 !== $pass2){
                echo '{ "result" : "Passwords does not match", "type" : "warning"}';
            }else{
                $user = new User();
                $user->read();
                $user->setFechaAlta(date_create()->format('Y-m-d H:i:s'));
                $user->setActivo('1');
                $user->setPass(sha1($pass1));
                $user->setAdministrrador('0');
                $user->setPersonal('0');
                $v = $this->mng->validate($user);
                if( $v == 1){
                    $r = $this->mng->insert($user);
                     if($r!=-1){
                        echo '{ "result" : "User created", "type" : "success"}';
                    }else{
                        echo '{ "result" : "Error creating user", "type" : "error"}';
                    }
                }else{
                    echo '{ "result" : "User is not valid", "type" : "warning"}';
                }
            }
        }
    }
    
    function setAdmin(){
        $this->checkUser();
        $user = $this->mng->get(Request::req('pkid'));
        if($user->getEmail() != null){
            if($user->getAdministrador()=='1'){
            $user->setAdministrador('0');
            }else{
                $user->setAdministrador('1');
            }
            echo '{ "result" : "Admin changed", "type" : "success"}';
        }else{
            echo '{ "result" : "User is not valid", "type" : "warning"}';
        }
    }
    
    function setStaff(){
        $this->checkUser();
        $user = $this->mng->get(Request::req('pkid'));
        if($user->getEmail() != null){
            if($user->getPersonal()=='1'){
            $user->setPersonal('0');
            }else{
                $user->setPersonal('1');
            }
            echo '{ "result" : "Staff changed", "type" : "success"}';
        }else{
            echo '{ "result" : "User is not valid", "type" : "warning"}';
        }  
    }
    
    function setActive(){
        $this->checkUser();
        $user = $this->mng->get(Request::req('pkid'));
        if($user->getEmail() != null){
            if($user->getActivo()=='1'){
            $user->setActivo('0');
            }else{
                $user->setActivo('1');
            }
            echo '{ "result" : "State changed", "type" : "success"}';
        }else{
            echo '{ "result" : "User is not valid", "type" : "warning"}';
        }  
    }
    
    function reactive(){
        $email = Request::req('pkid');
        $hash = $this->mng->getHash($email);
        $url = "https://mygallery-jmroldan00.c9users.io/user/activar.php?m=$email&hash=$hash";
        $subject = "Confirmación de alta";
        $to = $email;
        $message = $url;
        $mail = new Mail($to, $subject, $message);
        $r = $mail->send();
        if($r!=-1){
            echo '{ "result" : "Email sended", "type" : "success"}';
        }else{
            echo '{ "result" : "error sended the mail", "type" : "error"}';
        }
    }
    
    function changePass(){
        $email = Request::req('mail');
        $pass1 = Request::req('pass1');
        $pass2 = Request::req('pass2');
        if($pass1 !== $pass2){
            echo '{ "result" : "Passwords does not match", "type" : "warning"}';
        }else{
            $paramsWhere = [];
            $paramsWhere['email'] = $email;
            $paramsSet = [];
            $paramsSet['pass'] = sha1($pass1);
            $r = $this->db->update($this->table, $paramsSet, $paramsWhere);
            if($r!=-1){
                echo '{ "result" : "Password changed", "type" : "success"}';
            }else{
                echo '{ "result" : "Error changing the password", "type" : "error"}';
            }
        }
    }
    
    function uploadImage(){
        $this->checkUser();
        $file = new UploadFile($_FILES['image']);
        $file->setName(Request::req('pkid'));
        $file->setDestination('../img/profiles/');
        $file->upload();
        $r = $file->getError_message();
        echo '{ "result" : '.$r.', "type" : "alert"}';
    }

    function getToken(){
        $response  = json_decode($this->mng->authenticate_google_OAuthtoken(), true);
        if($response['email_verified'] == 'true'){
            $user = $this->mng->get($response['email']);
            if($user->getEmail()!=null){
                if($user->getActivo() == 1){
                    $_SESSION['user'] = $user;
                    $_SESSION['google_user'] = '1';
                    $response['result'] = "1";
                }else{
                    $response['result'] = 'user-disabled';
                }
            }else{
                $user = new User($response['email'], $mng->getHash($response['email']));
                $r = $this->mng->googleUserRegister($user);
                if($r != -1){
                    $user = $this->mng->get($response['email']);
                    $session->set('user', $user);
                    $session->set('google_user', '1');
                    $response['result'] = 'Registro realizado';
                }else{
                    $response['result'] = "Error a crear el usuario - ".$email;
                }
            }
        }
        echo json_encode($response);
    }
}
