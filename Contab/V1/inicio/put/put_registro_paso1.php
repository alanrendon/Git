<?php
	$url[0] = "../";
	require_once "../class/dominio_P1.class.php";

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

        if(isset($_POST['url_step1']) && isset( $_POST['cupon_step1'])){
            
            $url_step1   =addslashes(trim($_POST['url_step1']));
            $cupon_step1 =addslashes(trim($_POST['cupon_step1']));
            
            if(!empty($url_step1) && !empty($cupon_step1)){
                $registro_dominio = new Dominio_P1();
                $registro_dominio->dominio  = $url_step1;
                $registro_dominio->cupon    = $cupon_step1;
                
                if(($id = $registro_dominio->registro())>0){
                    exit(json_encode( array('id' =>$id)));
                }
                 exit(json_encode( array('error' =>'No se puede continuar con el registro')));
            }
        }else{
            exit(json_encode( array('error' =>'Faltan datos')));
        }
	}
	
?>

