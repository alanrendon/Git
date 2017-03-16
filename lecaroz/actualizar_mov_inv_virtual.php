<?php
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

$sql = "SELECT * FROM mov_inv_real WHERE num_cia=65 AND tipo_mov='TRUE' AND cod_turno=2";
$mov = ejecutar_script($sql,$dsn);
for ($i=0; $i<count($mov); $i++) {
	$sql = "SELECT id,cantidad FROM mov_inv_real WHERE num_cia=65 AND codmp=".$mov[$i]['codmp']." AND tipo_mov='TRUE' AND cod_turno=8 AND fecha='".$mov[$i]['fecha']."'";
	$pic = ejecutar_script($sql,$dsn);
	if ($pic) {
		$sql = "UPDATE mov_inv_real SET cantidad=cantidad+".$pic[0]['cantidad']." WHERE id=".$mov[$i]['id'];
		ejecutar_script($sql,$dsn);
	}
}
?>