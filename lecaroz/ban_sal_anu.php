<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

$users = array(28, 29, 30, 31);

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_sal_anu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['anio'])) {
	$tpl->newBlock("datos");
	$tpl->assign("anio", date("Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}

$db = new DBclass($dsn);

$sql = "SELECT num_cia, nombre_corto, saldo_libros, saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING (num_cia) WHERE";
if (!in_array($_SESSION['iduser'], $users))
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : " num_cia NOT IN (999)";
else
	$sql .= $_GET['num_cia'] >= 900 && $_GET['num_cia'] <= 950 ? " num_cia = $_GET[num_cia]" : " num_cia BETWEEN 900 AND 950";
$sql .= " ORDER BY num_cia";
$cia = $db->query($sql);

$tpl->newBlock("listado");
$tpl->assign("anio", $_GET['anio']);

$num_meses = $_GET['anio'] < date("Y") ? 12 : date("n", mktime(0, 0, 0, date("n"), 0, $_GET['anio']));

$diasxmes[1] = 31;
$diasxmes[2] = $_GET['anio'] % 4 == 0 ? 29 : 28;
$diasxmes[3] = 31;
$diasxmes[4] = 30;
$diasxmes[5] = 31;
$diasxmes[6] = 30;
$diasxmes[7] = 31;
$diasxmes[8] = 31;
$diasxmes[9] = 30;
$diasxmes[10] = 31;
$diasxmes[11] = 30;
$diasxmes[12] = 31;

$total = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);

for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
	
	for ($j = 1; $j <= $num_meses; $j++) {
		$fecha1 = "1/$j/$_GET[anio]";
		$fecha2 = "$diasxmes[$j]/$j/$_GET[anio]";
		
		$saldo_acu = 0;
		
		for ($k = 1; $k <= $diasxmes[$j]; $k++) {
			$sql = "SELECT tipo_mov, sum(importe) FROM estado_cuenta WHERE num_cia = {$cia[$i]['num_cia']} AND fecha_con > '$k/$j/$_GET[anio]' AND fecha_con IS NOT NULL GROUP BY tipo_mov";
			$temp = $db->query($sql);
			
			$entradas = 0;
			$salidas = 0;
			
			if ($temp)
				for ($l = 0; $l < count($temp); $l++)
					if ($temp[$l]['tipo_mov'] == "f")
						$entradas = $temp[$l]['sum'];
					else
						$salidas = $temp[$l]['sum'];
			
			$saldo_dia = $cia[$i]['saldo_bancos'] - $entradas + $salidas;
			$saldo_acu += $saldo_dia;
		}
		
		// Calcular saldo a ese día
		$saldo_prom = $saldo_acu / $diasxmes[$j];
		
		$total[$j] += $saldo_prom;
		$tpl->assign("listado.$j", number_format($total[$j], 2, ".", ","));
		
		$tpl->assign($j, $saldo_prom > 0 ? number_format($saldo_prom, 2, ".", ",") : "&nbsp;");
	}
}

$tpl->printToScreen();
$db->desconectar();
?>