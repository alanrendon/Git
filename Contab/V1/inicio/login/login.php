
<?php

    if(!isset($url[0]))
        $url[0] = '../';
    require_once ($url[0]."class/user.class.php");

    if ( is_session_started() === FALSE ) @session_start();

    if(isset($_GET['key'])){
        $key = addslashes(trim($_GET['key']));
        $_SESSION['key'] = $key;
        
        $usuario=new Usuario();
        if($id=$usuario->comparar_token($key)){
            $_SESSION['usuario'] = $id;
            $multiempresa = $usuario->multiempresa();
   
            if($multiempresa){
                header('Location: multiempresa.php');
            }else{
                header('Location: index.php');
            }
        }
        exit();
    }

    if (isset($_POST['txt_usuario']) && isset($_POST['txt_contra']) && (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
 
        $usuario          =addslashes(trim($_POST['txt_usuario']));
        $contra           =addslashes(trim($_POST['txt_contra']));
        $usuario_conectar =new Usuario();
        $castigo          =true;
 
        $usuario_conectar->usuario =($usuario);
        $usuario_conectar->contra=($contra);
 
        if (isset($_SESSION['tiempo']) && $tiempo_castigo_intentos =$_SESSION['tiempo']) {
            $castigoTime = strtotime("now");
            if ( $castigoTime < $tiempo_castigo_intentos) {
                $castigo =false;
            }
        }
        if ($castigo && $usuario_conectar->validar_UsuarioContra()) {
 

            $_SESSION['usuario'] = $usuario_conectar->rowid;
            
             $multiempresa = $usuario_conectar->multiempresa();
   
            if($multiempresa){
                print json_encode( array('url' => 'multiempresa.php'));
            }else{
                print json_encode( array('url' => 'index.php'));
            }
            exit;
 
        }else if($castigo){
 
            if (isset($_SESSION['intentos']) && $intentos =$_SESSION['intentos']) {
                $intentos++;
            }else{
                $intentos =1;
            }
            if ($intentos == 3) {
                $_SESSION['tiempo'] =strtotime('+3 minutes');
    
            }
            $_SESSION['intentos'] = $intentos;
            print json_encode( array('mensaje' =>  "Usuario/Contraseña erróneos."));
            exit;
 
        }else{
            unset($_SESSION['intentos']);

            print json_encode( array('mensaje' =>  "Debes esperar 180 segundos para volver a intentarlo."));
            exit;
        }
    }elseif(!isset($_SESSION['usuario']) && !isset($ingresar)){
 
        if(  strpos($_SERVER['REQUEST_URI'], 'index.php') !== FALSE || strpos($_SERVER['REQUEST_URI'], 'multiempresa.php')){
            header('Location: ingresar.php');
        }else{
            header('Location: ../index.php');
        }
        exit;
    }

 
    if(isset($_GET['salir'])){
        if ( is_session_started() === FALSE ) @session_start();
        
        unset($_SESSION);
        session_destroy();
        $resultado = strpos($_SERVER['REQUEST_URI'], 'index.php');
 
        header('Location: ../ingresar.php');
         exit;
    }
   

    function is_session_started()
    {
            if ( php_sapi_name() !== 'cli' ) {
                if ( version_compare(phpversion(), '5.4.0', '>=') ) {
                    return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
                } else {
                    return session_id() === '' ? FALSE : TRUE;
                }
            }
            return FALSE;
    }
 
?>