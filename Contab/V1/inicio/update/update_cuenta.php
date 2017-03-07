<?php
	$url[0] = "../";
	require_once "../class/cat_cuentas.class.php";
    

		if (
            isset($_POST['codigo_cuenta'])        && isset($_POST['nombre_cuenta']) &&
            isset($_POST['nivel_cuenta'])   && isset($_POST['naturaleza_cuenta']) && 
            isset($_POST['rowid']) 
        ) {
            $codigo_cuenta      =addslashes(trim($_POST['codigo_cuenta']));
            $nombre_cuenta      =addslashes(trim($_POST['nombre_cuenta']));
            $nivel_cuenta       =addslashes(trim($_POST['nivel_cuenta']));
            $naturaleza_cuenta  =addslashes(trim($_POST['naturaleza_cuenta']));
            $rowid              =addslashes(trim($_POST['rowid']));
            $afectada              =addslashes(trim($_POST['afectada']));
            $codigo_sat=0;
            if(isset($_POST['codigo_sat']))
                $codigo_sat             =addslashes(trim($_POST['codigo_sat']));

            $cuenta = new Cuenta();
            if($cuenta->update_cuenta($codigo_cuenta,$nombre_cuenta,$nivel_cuenta,$naturaleza_cuenta,$rowid,$codigo_sat,$afectada)){
                exit(json_encode( array('msg' =>"Se ha actualizado el grupo.")));
            }
           
        }else{
            exit(json_encode( array('msg' =>"errupo.")));
        }



?>

