<?php
	$url[0] = "../";
	require_once "../class/grupo.class.php";
	
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		if (
            isset($_POST['grupo_padre'])        && isset($_POST['grupo']) &&
            isset($_POST['cuenta_inicial']) && isset($_POST['cuenta_final'])       
            && isset($_POST['rowid'])
        ) {
            $grupo_padre        =addslashes(trim($_POST['grupo_padre']));
            $grupo_des          =addslashes(trim($_POST['grupo']));
            $cuenta_inicial     =addslashes(trim($_POST['cuenta_inicial']));
            $cuenta_final       =addslashes(trim($_POST['cuenta_final']));
            $rowid              =addslashes(trim($_POST['rowid']));
            
            if(
                !empty($grupo_padre)        && !empty($grupo_des) &&
                !empty($cuenta_inicial) &&
                !empty($cuenta_final)       && !empty($rowid)
            ){
                $grupo = new Grupo();
                if($grupo->update_grupo($grupo_padre,$grupo_des,'',$cuenta_inicial,$cuenta_final,$rowid)){
                      exit(json_encode( array('msg' =>"Se ha actualizado el grupo.")));
                }
            }
            exit(json_encode( array('msg' =>"Error, hacen falta datos.")));
        }

	}
    exit(json_encode( array('msg' =>"Error, con la petición.")));

?>