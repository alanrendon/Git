<?php
// ESTADO DE RESULTADOS DE PANADERIAS
// Tablas 'compra_directa', 'hoja_dia_rost', 'movimiento_gastos', 'total_companias'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

$fecha1 = '01/03/2005';
$fecha2 = '31/03/2005';
$mes = 3;
$anio = 2005;

$sql = "SELECT num_cia FROM catalogo_companias WHERE num_cia < 100 and num_cia not in (1,40,44,16,41,2)";
$cia = ejecutar_script($sql,$dsn);

for ($i=0; $i<count($cia); $i++) {
	$num_cia = $cia[$i]['num_cia'];
	$sql = "SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = 18";
	$temp = ejecutar_script($sql,$dsn);
	$ingresos_ext = $temp[0]['sum'];
	
	echo "num_cia = $num_cia -- ext = $ingresos_ext<br>";
	if ($ingresos_ext != 0) {
		ejecutar_script("UPDATE balances_pan SET ingresos_ext = $ingresos_ext WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio",$dsn);
		echo "Actualizada compañia $num_cia<br>";
	}
}

?>