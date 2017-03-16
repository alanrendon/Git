<?php
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

$sql = "SELECT * FROM mov_expendios WHERE rezago_anterior IS NULL AND num_cia < 100";
$mov = ejecutar_script($sql,$dsn);

for ($i=0; $i<count($mov); $i++) {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$mov[$i]['fecha'],$fecha);
	$sql = "SELECT rezago FROM mov_expendios WHERE num_cia = ".$mov[$i]['num_cia']." AND num_expendio = ".$mov[$i]['num_expendio']." AND fecha < '".$mov[$i]['fecha']."' ORDER BY fecha DESC LIMIT 1";
	$rezago_anterior = ejecutar_script($sql,$dsn);
	if ($rezago_anterior) {
		$sql = "UPDATE mov_expendios SET rezago_anterior = ".(($rezago_anterior[0]['rezago'] != 0)?$rezago_anterior[0]['rezago']:"NULL")." WHERE idmov_expendios = ".$mov[$i]['idmov_expendios'];
		ejecutar_script($sql,$dsn);
		echo "$sql<br>";
		unset($rezago_anterior);
	}
}
//echo "Actualizados $i registros";
?>