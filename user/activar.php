<?php
require_once('../classes/AutoLoad.php');
$db = new DataBase();
$session = new Session();
$email = Request::get('m');
$hash = Request::get('hash');
$mng = new ManageUser($db);
$ctl = new UserController();
if($hash == $mng->getHash($email)){
    $ctl->active();
}else{
    echo '<div class="container"><h3>Ha habido un error en la activación, intenterlo mas tarde o contacte con el administrador.</h3></div>';
}
