<?php
	include './includes/class.db3.inc.php';
	include './includes/dbstatus.php';
	
	for ($i=31; $i>=1; $i--) {
		$sql = "update historico_inventario set precio_unidad = mov_inv_real.precio_unidad where num_cia = mov_inv_real.num_cia and codmp = mov_inv_real.codmp and mov_inv_real.tipo_mov = 'FALSE' and mov_inv_real.fecha = '$i/08/2004'";
		ejecutar_script($sql,$dsn);
	}
?>