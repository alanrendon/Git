<?php
	$url[0] = "../";
	require_once "../class/tipos_pagos.class.php";

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		if (isset($_POST['eliminar'])) {
			$id_pago = (int)$_POST['eliminar'];
			$pago    = new tipos_pagos(); 
			if ($pago=$pago->delete_paiement($id_pago)) {
				exit(json_encode(true));
            }else{
                exit(json_encode(array('mensaje' =>"Error, no se pudo eliminar.")));
            }
		}

	}

	
?>