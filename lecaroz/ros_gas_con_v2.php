<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

$users = array(1, 4, 5, 8, 18, 19, 20, 28, 30, 42, 62);
$zap_users = array(28, 29, 30, 31, 32);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ros/ros_gas_con_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['id'])) {
	// Obtener totales a borrar a efectivo por dia
	$sql = "SELECT num_cia, fecha, sum(importe) AS importe FROM movimiento_gastos WHERE idmovimiento_gastos IN (";
	foreach ($_POST['id'] as $i => $id)
		$sql .= $id . ($i < count($_POST['id']) - 1 ? ", " : ")");
	$sql .= " GROUP BY num_cia, fecha ORDER BY fecha";
	$result = $db->query($sql);
	
	$sql = "";
	$tabla = $result[0]['num_cia'] <= 300 ? 'total_panaderias' : ($result[0]['num_cia'] >= 900 ? 'total_zapaterias' : 'total_companias');
	foreach ($result as $reg)
		$sql .= "UPDATE $tabla SET efectivo = efectivo + $reg[importe], gastos = gastos - $reg[importe] WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]';\n";
	
	$sql .= "DELETE FROM movimiento_gastos WHERE idmovimiento_gastos IN (";
	foreach ($_POST['id'] as $i => $id)
		$sql .= $id . ($i < count($_POST['id']) - 1 ? ", " : ");\n");
	
	$db->query($sql);
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $result[0]['fecha'], $fecha);
	$tpl->newBlock("reload");
	$tpl->assign("num_cia", $result[0]['num_cia']);
	$tpl->assign("codgastos", $_POST['codgastos']);
	$tpl->assign("mes", $fecha[2]);
	$tpl->assign("anio", $fecha[3]);
	$tpl->assign("tipo", $_POST['tipo']);
	$tpl->printToScreen();
	die;
}

if (isset($_GET['num_cia'])) {
	if (isset($_GET['fecha'])) {
		$fecha1 = $_GET['fecha'];
		$fecha2 = $_GET['fecha'];
		ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha'], $tmp);
		$mes = $tmp[2];
		$anio = $tmp[3];
	}
	else {
		$fecha1 = "01/$_GET[mes]/$_GET[anio]";
		$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
		$mes = $_GET['mes'];
		$anio = $_GET['anio'];
	}
	
	if (!isset($_GET['con']) || $_GET['con'] == 1) {
		$sql = "SELECT idmovimiento_gastos AS id, num_cia, cc.nombre_corto, codgastos, cg.descripcion, fecha, importe, concepto, captura, ct.descripcion AS turno FROM movimiento_gastos LEFT JOIN catalogo_gastos AS cg";
		$sql .= " USING (codgastos) LEFT JOIN catalogo_companias AS cc USING (num_cia) LEFT JOIN catalogo_turnos AS ct USING (cod_turno) WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= $_SESSION['tipo_usuario'] == 2 ? " AND num_cia BETWEEN 900 AND 998" : "";
		$sql .= isset($_GET['codgastos']) && $_GET['codgastos'] > 0 ? " AND codgastos = $_GET[codgastos]" : "";
		$sql .= isset($_GET['tipo']) && $_GET['tipo'] != "" ? " AND codigo_edo_resultados = $_GET[tipo]" : "";
		$sql .= !in_array($_SESSION['iduser'], $users) || isset($_GET['fecha']) ? " AND captura = 'FALSE'" : "";
		$sql .= " ORDER BY codgastos, fecha, importe DESC";
		$result = $db->query($sql);
		
		if (!$result) {
			header("location: ./ros_gas_con_v2.php?codigo_error=1");
			die;
		}
		
		$tmp = $db->query("SELECT id FROM balances_pan WHERE num_cia = $_GET[num_cia] AND mes = $mes AND anio = $anio");
		$mod = $tmp && !in_array($_SESSION['iduser'], $users) ? FALSE : TRUE;
		
		$tpl->newBlock("desglose");
		$tpl->assign("num_cia", $_GET['num_cia']);
		$tpl->assign("nombre", $result[0]['nombre_corto']);
		$tpl->assign("mes", mes_escrito($mes, TRUE));
		$tpl->assign("anio", $anio);
		$tpl->assign("tipo", isset($_GET['tipo']) ? $_GET['tipo'] : "");
		$tpl->assign("codgastos", isset($_GET['codgastos']) ? $_GET['codgastos'] : '');
		
		$tpl->assign('disabled', !in_array($_SESSION['iduser'], array(1, 4, 28, 42, 57, 62)) ? ' disabled' : '');
		
		$cod = NULL;
		$gran_total = 0;
		foreach ($result as $reg) {
			if ($cod != $reg['codgastos']) {
				$cod = $reg['codgastos'];
				
				$tpl->newBlock("gasto");
				$tpl->assign("codgastos", $reg['codgastos']);
				$tpl->assign("concepto", $reg['descripcion']);
				$tpl->assign("span1", $_GET['num_cia'] >= 900 ? 5 : 6);
				$tpl->assign("span2", $_GET['num_cia'] >= 900 ? 3 : 4);
				if ($_GET['num_cia'] <= 899) $tpl->newBlock("tturno");
				$total = 0;
			}
			$tpl->newBlock("fila");
			$tpl->assign("id", $reg['id']);
			$tpl->assign("disabled", $mod && $reg['captura'] == "f" ? "" : " disabled");
			$tpl->assign("fecha", $reg['fecha']);
			$tpl->assign("concepto", $reg['concepto'] != "" ? $reg['concepto'] : "&nbsp;");
			$tpl->assign("importe", "<span style=\"color:#" . ($reg['importe'] < 0 ? "CC0000" : "000000") . "\">" . number_format($reg['importe'], 2, ".", ",") . "</span>");
			$total += $reg['importe'];
			$gran_total += $reg['importe'];
			$tpl->assign("gasto.total", number_format($total, 2, ".", ","));
			if ($_GET['num_cia'] <= 899) {
				$tpl->newBlock("turno");
				$tpl->assign("turno", $reg['turno'] != "" ? $reg['turno'] : "&nbsp;");
			}
		}
		$tpl->assign("desglose.span2", $_GET['num_cia'] >= 900 ? 3 : 4);
		$tpl->assign('desglose.gran_total', number_format($gran_total, 2, '.', ','));
		
		if (isset($_GET['fecha']))
			$tpl->newBlock('botones2');
		else
			$tpl->newBlock('botones1');
	}
	else {
		$sql = "SELECT num_cia, nombre_corto, codgastos, descripcion, sum(importe) AS importe, codigo_edo_resultados AS tipo FROM movimiento_gastos LEFT JOIN catalogo_gastos";
		$sql .= " USING (codgastos) LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= $_GET['codgastos'] > 0 ? " AND codgastos = $_GET[codgastos]" : "";
		$sql .= $_GET['tipo'] != "" ? " AND codigo_edo_resultados = $_GET[tipo]" : "";
		$sql .= " GROUP BY num_cia, nombre_corto, codgastos, descripcion, tipo ORDER BY codigo_edo_resultados DESC, codgastos";
		$result = $db->query($sql);
		
		if (!$result) {
			header("location: ./ros_gas_con_v2.php?codigo_error=1");
			die;
		}
		
		$tpl->newBlock("totales");
		$tpl->assign("num_cia", $_GET['num_cia']);
		$tpl->assign("nombre", $result[0]['nombre_corto']);
		$tpl->assign("mes", mes_escrito($_GET['mes'], TRUE));
		$tpl->assign("anio", $_GET['anio']);
		
		$nombre = array(0 => "GASTOS NO INCLUIDOS", 1 => "GASTOS DE OPERACION", 2 => "GASTOS GENERALES");
		$tipo = NULL;
		$gran_total = 0;
		foreach ($result as $reg) {
			if ($tipo != $reg['tipo']) {
				$tipo = $reg['tipo'];
				
				$tpl->newBlock("tipo");
				$tpl->assign("tipo", $nombre[$reg['tipo']]);
				$total = 0;
			}
			$tpl->newBlock("concepto");
			$tpl->assign("cod", $reg['codgastos']);
			$tpl->assign("concepto", $reg['descripcion']);
			$tpl->assign("importe", number_format($reg['importe'], 2, ".", ","));
			$total += $reg['importe'];
			$gran_total += $reg['importe'];
			$tpl->assign("tipo.total", number_format($total, 2, ".", ","));
		}
	}
	//$tpl->assign("totales.total", number_format($gran_total, 2, ".", ","));
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

if (in_array($_SESSION['iduser'], $users))
	$tpl->newBlock("opt_ext");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>