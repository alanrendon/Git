<?php
	$url[0] = "../";
	require_once "../class/periodo.class.php";
	
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        
        
		if (isset($_POST['col']) && isset($_POST['id'])) {
			$id= (int)$_POST['id'];
            $col = addslashes(trim($_POST['col']));
			$periodo    = new Periodo();
            
            $periodos = $periodo->get_periodo_id_cerrado($id);
            if( !$periodos  &&  $periodo->cambiar_estado_reporte($id,$col) ){
				exit(true);
			}else{
				exit(json_encode(array('mensaje' =>"Error, no se pudo cambiar el estado. El periodo debe estar abierto")));
			}
		}
        else if(isset($_POST['validar']) ){
            $id= (int)$_POST['validar'];
          
			$periodo    = new Periodo();
            
			if ($periodo->cambiar_todoslosestados_reporte($id)) {
				exit(true);
			}else{
				exit(json_encode(array('mensaje' =>"Error, no se pudo cambiar el estado.")));
			}
        }else if(isset($_POST['estado']) ){
            $id= (int)$_POST['estado'];
          
			$periodo    = new Periodo();
            $periodos = $periodo->validar_nuevo_periodo();
            $periodo_estado = $periodo->get_periodo_id($id);
            
			if ( ($periodo_estado->estado == 1 || !$periodos) && $periodo->cambiar_estado_periodo_inverso($id)) {
				exit(true);
			}else{
				exit(json_encode(array('mensaje' =>"Error, no se pudo cambiar el estado. Solo puede tener un periodo abierto a la vez")));
			}
        }
        else{
            exit(json_encode(array('mensaje' =>"Error,faltan datos.")));
        }
	}

	
?>