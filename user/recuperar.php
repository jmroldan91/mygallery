<?php
require_once('../classes/AutoLoad.php');
$db = new DataBase();
$session = new Session();
$email = Request::get('m');
$hash = Request::get('hash');
$mng = Utils::getManager('user', $db);
Render::getHeader();
$r=Request::get('r');
if($r!=null){
    if($r=='password-error'){
        echo '<div class="container"><h3>Las contraseñas no coinciden</h3></div>';
    ?>
        <div class="col-md-4 col-md-offset-4">
            <h2>Recuperación de contraseña</h2>
            <form class="form form-horizontal" method="POST" action="./controler.php?op=changePass">
                <label for="pass1">Contraseña nueva</label>
                <input class="form-control" type="password" id="pass1" name="pass1" required/>
                <label for="pass1">Repita la contraseña</label>
                <input class="form-control" type="password" id="pass2" name="pass2" required/>
                <hr/>
                <input class="btn btn-primary" type="submit" value="Cambiar contraseña"/>
            </form>
        </div>
    <?php
    }else{
        if($r!=-1){
            echo '<div class="container"><h3>Contraseña cambiada correctamente | <a class="btn btn-primary btn-lg" href="../index.php">Volver</a></h3></div>';
        }else{
            echo '<div class="container"><h3>Ha habido un error en la guardando la contraseña, intentelo mas tarde o contacte con el administrador.</h3></div>';
        }
    }
}else{
    if($hash == $mng->getHash($email)){ 
        $session->set('user', $mng->get($email)); 
    ?>
        <div class="col-md-4 col-md-offset-4">
            <h2>Recuperación de contraseña</h2>
            <form class="form form-horizontal" method="POST" action="./controler.php?op=changePass">
                <label for="pass1">Contraseña nueva</label>
                <input class="form-control" type="password" id="pass1" name="pass1" required/>
                <label for="pass1">Repita la contraseña</label>
                <input class="form-control" type="password" id="pass2" name="pass2" required/>
                <hr/>
                <input class="btn btn-primary" type="submit" value="Cambiar contraseña"/>
            </form>
        </div>
    <?php
    }else{
        echo '<div class="container"><h3>Ha habido un error en la verificación del ususario, intentelo mas tarde o contacte con el administrador.</h3></div>';
    }
}
Render::getFooter();