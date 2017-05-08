<?php
	$url[0] = "../";
	require_once "../class/datos_empresa_P3.class.php";

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

        if(
            isset($_POST['empresa_step3']) && isset( $_POST['pais_step3'])  &&
            isset($_POST['ciudad_step3']) && isset( $_POST['cp_step3'])  &&
            isset($_POST['telefono_step3'])  && isset($_POST['cp_step3']) &&
            isset($_POST['fk'])
        ){
            $empresa_step3      =addslashes(trim($_POST['empresa_step3']));
            $pais_step3         =addslashes(trim($_POST['pais_step3']));
            $ciudad_step3       =addslashes(trim($_POST['ciudad_step3']));
            $cp_step3           =addslashes(trim($_POST['cp_step3']));
            $telefono_step3     =addslashes(trim($_POST['telefono_step3']));
            $cp_step3           =addslashes(trim($_POST['cp_step3']));
            $fk                 =addslashes(trim($_POST['fk']));
            
                
            if(  
                !empty($empresa_step3) && !empty($pais_step3) && 
                !empty($ciudad_step3) && !empty($cp_step3) && 
                !empty($telefono_step3) && !empty($cp_step3) && 
                !empty($fk) 
              ){
                
                $registr_empresa = new Datos_Empresa_P3();
                
                $registr_empresa->empresa                 = $empresa_step3;
                $registr_empresa->pais                    = $pais_step3;
                $registr_empresa->ciudad                  = $ciudad_step3;
                $registr_empresa->cp                      = $cp_step3;
                $registr_empresa->tel                     = $telefono_step3;
                $registr_empresa->direccion               = $cp_step3;
                $registr_empresa->fk_datos_contacto_paso2 = $fk;
                
                if(($id = $registr_empresa->registro())>0){
                    exit(json_encode( array('error' =>'Registro realizado correctamente')));
                }
                 exit(json_encode( array('error' =>'No se puede terminar  el registro')));
            }
        }else{
            exit(json_encode( array('error' =>'Faltan datos')));
        }
	}
	
?>

