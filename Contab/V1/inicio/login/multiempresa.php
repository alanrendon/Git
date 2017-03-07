<?php

    if(!isset($url[0]))
        $url[0] = '../';
    require_once ($url[0]."class/user.class.php");

    if ( is_session_started() === FALSE ) @session_start();

    if(isset($_POST['empresa'])){
        $usuer_empresa = new Usuario();
        $ENTITY = addslashes(trim($_POST['empresa']));
        $_SESSION['ENTITY'] = (int)$ENTITY;
        
        if($empresa = $usuer_empresa->get_multiempresa($ENTITY)){
             header('Location: ../index.php');
        }else{
             header('Location: ../multiempresa.php');
        }
       
        exit();
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