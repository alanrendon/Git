<html>
<head>
</head>
<body>
<table border="1">
<?php
include './includes/dbstatus.php';
include './includes/class.db3.inc.php';

$cia = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia > 160 AND num_cia < 170 OR num_cia = 702",$dsn);
$mp = ejecutar_script("SELECT * FROM catalogo_mat_primas WHERE tipo_cia = 'FALSE' ORDER BY orden",$dsn);

for ($i=0; $i<count($cia); $i++) {
	echo "<tr><th>Compañía ".$cia[$i]['num_cia']."</th></tr>";
	echo "<tr><th>MP</th><th>Descripcion</th><th>Existencia F1</th><th>Entrada F2</th><th>Salida F2</th><th>Existencia F2</th>";
	for ($j=0; $j<count($mp); $j++) {
		$sql = "SELECT *,catalogo_mat_primas.nombre AS nombre FROM inventario_real WHERE num_cia=".$cia[$i]['num_cia']." AND codmp=".$mp[$j]['codmp']." AND catalogo_mat_primas.codmp=".$mp[$j]['codmp'];
		if (count($result=ejecutar_script($sql,$dsn)) != FALSE) {
			echo "<tr>";
			echo "<td>".$result[0]['codmp']."</td><td>".$result[0]['nombre']."</td>";
			$entradas = ejecutar_script("SELECT sum(cantidad) FROM mov_inv_real WHERE num_cia=".$cia[$i]['num_cia']." AND codmp=".$mp[$j]['codmp']." AND tipo_mov='FALSE'",$dsn);
			$salidas = ejecutar_script("SELECT sum(cantidad) FROM mov_inv_real WHERE num_cia=".$cia[$i]['num_cia']." AND codmp=".$mp[$j]['codmp']." AND tipo_mov='TRUE'",$dsn);
			$existencia_ini = $result[0]['existencia'] - $entradas[0]['sum'] + $salidas[0]['sum'];
			echo "<td>$existencia_ini</td>";
			echo "<td>".$entradas[0]['sum']."</td>";
			echo "<td>".$salidas[0]['sum']."</td>";
			echo "<td>".$result[0]['existencia']."</td>";
			echo "</tr>";
		}
	}
}

?>
</table>
</body>
</html>