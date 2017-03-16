<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die("LO SENTIMOS, PANTALLA EN REMODELACION  ^_^");

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_com_cli.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$mes1 = $_GET['mes1'];
	$mes2 = $_GET['mes2'];
	$anio = $_GET['anio'];
	
	$mes = $mes1 == $mes2 ? "mes = $mes1" : "mes BETWEEN $mes1 AND $mes2";
	
	$sql = "SELECT num_cia, nombre_corto, (SELECT sum(clientes) FROM historico WHERE $mes AND anio = $anio - 1 AND num_cia = catalogo_companias.num_cia) AS clientes_ant, (SELECT sum(clientes) FROM historico WHERE $mes AND anio = $anio AND num_cia = catalogo_companias.num_cia) AS clientes_act";
	$sql .= " FROM catalogo_companias WHERE num_cia" . ($_GET['num_cia'] > 0 ? " = $_GET[num_cia]" : " < 300");
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./bal_com_cli.php?codigo_error=1");
		die;
	}
	
	// Calcular porcentaje de incremento
	for ($i = 0; $i < count($result); $i++)
		$result[$i]['por'] = $result[$i]['clientes_ant'] > 0 ? $result[$i]['clientes_act'] * 100 / $result[$i]['clientes_ant'] - 100 : 0;
	
	// Función de comparacion para ordenar los datos
	function cmp($a, $b) {
		// Si las compañías son iguales
		if ($a['por'] == $b['por'])
			return 0;
		else
			return ($a['por'] > $b['por']) ? -1 : 1;
	}
	
	usort($result, "cmp");
	
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
	
	$dias = 0;
	for ($i = $mes1; $i <= $mes2; $i++)
		$dias += $diasxmes[$i];
	
	$tpl->newBlock("listado");
	$tpl->assign("mes", $mes1 == $mes2 ? mes_escrito($mes1) : substr(mes_escrito($mes1), 0, 3) . "-" . substr(mes_escrito($mes2), 0, 3));
	$tpl->assign("anio_ant", $_GET['anio'] - 1);
	$tpl->assign("anio_act", $_GET['anio']);
	
	for ($i = 0; $i < count($result); $i++) {
		if ($result[$i]['clientes_act'] > 0) {
			$tpl->newBlock("fila");
			$tpl->assign("num_cia", $result[$i]['num_cia']);
			$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
			$tpl->assign("clientes_ant", $result[$i]['clientes_ant'] != 0 ? number_format($result[$i]['clientes_ant']) : "&nbsp;");
			$tpl->assign("diario_ant", $result[$i]['clientes_ant'] != 0 ? number_format($result[$i]['clientes_ant'] / $dias, 0) : "&nbsp;");
			$tpl->assign("clientes_act", $result[$i]['clientes_act'] != 0 ? number_format($result[$i]['clientes_act']) : "&nbsp;");
			$tpl->assign("diario_act", $result[$i]['clientes_act'] != 0 ? number_format($result[$i]['clientes_act'] / $dias, 0) : "&nbsp;");
			$tpl->assign("por", $result[$i]['por'] != 0 ? "<font color='#" . ($result[$i]['por'] >= 0 ? "0000FF" : "FF0000") . "'>" . number_format($result[$i]['por'], 3, ".", ",") . "</font>" : "&nbsp;");
		}
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$tpl->assign(date("n"), "selected");
$tpl->assign("anio", date("Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $mensaje[$_GET['mensaje']]);
}

// Imprimir el resultado
$tpl->printToScreen();
?>