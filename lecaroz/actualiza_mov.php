<?php
include './includes/dbstatus.php';
include './includes/class.db3.inc.php';

$mp = ejecutar_script("SELECT * FROM catalogo_mat_primas WHERE tipo_cia = 'FALSE' ORDER BY orden",$dsn);
$cia = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia > 100 AND num_cia < 200 ORDER BY num_cia",$dsn);

// Facturas
for ($i=0; $i<count($cia); $i++) {
	for ($j=0; $j<count($mp); $j++) {
		$fact_ros = ejecutar_script("SELECT * FROM fact_rosticeria WHERE num_cia=".$cia[$i]['num_cia']." AND codmp=".$mp[$j]['codmp'],$dsn);
		if ($fact_ros != FALSE) {
			for ($k=0; $k<count($fact_ros); $k++) {
				$sql = "INSERT INTO mov_inv_real (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,existencia,precio,total_mov) VALUES (".$cia[$i]['num_cia'].",".$fact_ros[$k]['codmp'].",'".$fact_ros[$k]['fecha_mov']."',11,'FALSE',".$fact_ros[$k]['cantidad'].",0,".$fact_ros[$k]['precio'].",".$fact_ros[$k]['total'].");";
				echo $sql."<br>";//ejecutar_script($sql,$dsn);
			}
		}
	}
}
// Hoja
/*for ($i=0; $i<count($cia); $i++) {
	for ($j=0; $j<count($mp); $j++) {
		$hoja = ejecutar_script("SELECT * FROM hoja_diaria_rost WHERE num_cia=".$cia[$i]['num_cia']." AND codmp=".$mp[$j]['codmp'],$dsn);
		if ($hoja != FALSE) {
			for ($k=0; $k<count($hoja); $k++) {
				$sql = "INSERT INTO mov_inv_real (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,existencia,precio,total_mov) VALUES (".$cia[$i]['num_cia'].",".$hoja[$k]['codmp'].",'".$hoja[$k]['fecha']."',11,'TRUE',".$hoja[$k]['unidades'].",0,".$hoja[$k]['precio_unitario'].",".$hoja[$k]['precio_total'].");";
				echo $sql."<br>";//ejecutar_script($sql,$dsn);
			}
		}
	}
}
// Compra
for ($i=0; $i<count($cia); $i++) {
	for ($j=0; $j<count($mp); $j++) {
		$compra = ejecutar_script("SELECT * FROM compra_directa WHERE num_cia=".$cia[$i]['num_cia']." AND codmp=".$mp[$j]['codmp'],$dsn);
		if ($compra != FALSE) {
			for ($k=0; $k<count($compra); $k++) {
				$sql = "INSERT INTO mov_inv_real (num_cia,codmp,fecha,cod_turno,tipo_mov,cantidad,existencia,precio,total_mov) VALUES (".$cia[$i]['num_cia'].",".$compra[$k]['codmp'].",'".$compra[$k]['fecha_mov']."',11,'FALSE',".$compra[$k]['cantidad'].",0,".$compra[$k]['precio_unit'].",".$compra[$k]['total'].");";
				echo $sql."<br>";//ejecutar_script($sql,$dsn);
			}
		}
	}
}*/

?>