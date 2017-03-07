<?php
	$url[0] = "../";
	require_once "../class/contacto_P2.class.php";

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

        if(
            isset($_POST['nombre_step2']) && isset( $_POST['apellidos_step2'])  &&
            isset($_POST['correo_step2']) && isset( $_POST['telefono_step2'])  &&
            isset($_POST['fk'])
        ){
            $nombre_step2    =addslashes(trim($_POST['nombre_step2']));
            $apellidos_step2 =addslashes(trim($_POST['apellidos_step2']));
            $correo_step2    =addslashes(trim($_POST['correo_step2']));
            $telefono_step2  =addslashes(trim($_POST['telefono_step2']));
            $fk              =addslashes(trim($_POST['fk']));
            
            if(  
                !empty($nombre_step2) && !empty($apellidos_step2) && 
                !empty($correo_step2) && !empty($telefono_step2) && 
                !empty($fk) 
              ){
                
                $registro_contacto = new Contacto_P2();
                
                $registro_contacto->nombres          = $nombre_step2;
                $registro_contacto->apelidos         = $apellidos_step2;
                $registro_contacto->email            = $correo_step2;
                $registro_contacto->tel              = $telefono_step2;
                $registro_contacto->fk_dominio_Paso1 = $fk;
                
                if(($id = $registro_contacto->registro())>0){
                    exit(json_encode( array('id' =>$id)));
                }
                  exit(json_encode( array('error' =>'No se puede continuar con el registro')));
            }
        }else{
            exit(json_encode( array('error' =>'Faltan datos')));
        }
	}
	
?>

