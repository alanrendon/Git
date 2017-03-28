<?php
	require '../main.inc.php';
	if( $_POST ) {
		$id = $_POST['id'];
		$sql = "UPDATE ".MAIN_DB_PREFIX."factory SET fk_statut_entrpot = 1 WHERE rowid = ".$id;
		$respuesta = ( $db->query($sql) ) ? 1: 0;
		print $respuesta;
	}
?>
