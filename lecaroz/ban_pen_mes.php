<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_pen_mes.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['fecha'])) {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $tmp);
	$fecha1 = "01/$tmp[2]/$tmp[3]";
	$fecha2 = $_GET['fecha'];
	$mes = $tmp[2];
	$anio = $tmp[3];
	$dias_por_mes = $tmp[2] < date('n') ? date('d', mktime(0, 0, 0, $tmp[2] + 1, 0, $tmp[3])) : $tmp[1];
	
	$cias = array();
	if (isset($_GET['num_cia']))
		foreach ($_GET['num_cia'] as $cia)
			if ($cia > 0)
				$cias[] = $cia;
	
	$sql = "(SELECT num_cia, num_cia_primaria, nombre_corto, idadministrador AS admin, extract(day from fecha) AS dia, efectivo FROM total_panaderias LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2'/* AND efectivo != 0*/";
	if (in_array($_SESSION['iduser'], $users))
		$sql .= " AND num_cia BETWEEN 900 AND 998";
	else
		$sql .= " AND num_cia BETWEEN 1 AND 800";
	if (count($cias) > 0) {
		$sql .= " AND num_cia NOT IN (";
		foreach ($cias as $i => $cia)
			$sql .= $cia . ($i < count($cias) - 1 ? ", " : ")");
	}
	$sql .= isset($_GET['idadmin']) && $_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : "";
	$sql .= " GROUP BY num_cia, num_cia_primaria, nombre_corto, idadministrador, fecha, efectivo UNION";
	$sql .= " SELECT num_cia, num_cia_primaria, nombre_corto, idadministrador AS admin, extract(day from fecha) AS dia, efectivo FROM total_companias LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND efectivo != 0";
	if ($_SESSION['tipo_usuario'] == 2)
		$sql .= " AND num_cia BETWEEN 900 AND 998";
	else
		$sql .= " AND num_cia BETWEEN 1 AND 800";
	if (count($cias) > 0) {
		$sql .= " AND num_cia NOT IN (";
		foreach ($cias as $i => $cia)
			$sql .= $cia . ($i < count($cias) - 1 ? ", " : ")");
	}
	$sql .= isset($_GET['idadmin']) && $_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : "";
	$sql .= " GROUP BY num_cia, num_cia_primaria, nombre_corto, idadministrador, fecha, efectivo)";
	$sql .= isset($_GET['idadmin']) && $_GET['idadmin'] == -1 ? " ORDER BY admin, num_cia_primaria, num_cia, dia" : " ORDER BY num_cia_primaria, num_cia, dia";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_pen_mes.php?codigo_error=1");
		die;
	}
	
	// [18-Abr-2007] Completar dias que no esten en el listado
	$tmp_result = array();
	$num_cia = NULL;
	$num_cia_primaria = NULL;
	$admin = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL && $dias < $dias_por_mes) {
				$dias_a_agregar = $dias_por_mes - $dias;
				
				for ($i = 1; $i <= $dias_a_agregar; $i++)
					$tmp_result[] = array('num_cia' => $num_cia, 'num_cia_primaria' => $num_cia_primaria, 'nombre_corto' => $nombre_corto, 'admin' => $admin, 'dia' => $dias + $i, 'efectivo' => $efectivo);
			}
			
			$num_cia = $reg['num_cia'];
			$num_cia_primaria = $reg['num_cia_primaria'];
			$admin = $reg['admin'];
			$nombre_corto = $reg['nombre_corto'];
			$efectivo = $reg['efectivo'];
			$dias = 0;
		}
		$tmp_result[] = $reg;
		$dias++;
	}
	if ($num_cia != NULL && $dias < $dias_por_mes) {
		$dias_a_agregar = $dias_por_mes - $dias;
		
		for ($i = 1; $i <= $dias_a_agregar; $i++)
			$tmp_result[] = array('num_cia' => $num_cia, 'num_cia_primaria' => $num_cia_primaria, 'nombre_corto' => $nombre_corto, 'admin' => $admin, 'dia' => $dias + $i, 'efectivo' => $efectivo);
	}
	$result = $tmp_result;
	
	function buscarDia($dia, $dias) {
		if (!$dias)
			return FALSE;
		
		foreach ($dias as $d)
			if ($dia == $d['dia'])
				return TRUE;
		
		if ($GLOBALS['mes'] == 1 && $dia == 1)
			return TRUE;
		
		return FALSE;
	}
	
	$cias = array();
	$cont = 0;
	$num_cia = NULL;
	foreach ($result as $cia) {
		if ($num_cia != $cia['num_cia']) {
			$num_cia = $cia['num_cia'];
			
			$sql = "SELECT extract(day from fecha) AS dia FROM estado_cuenta WHERE ((num_cia = $num_cia AND num_cia_sec IS NULL) OR num_cia_sec = $num_cia) AND cod_mov IN (1, 16, 44, 99) AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$sql .= " GROUP BY fecha ORDER BY fecha";
			$dep = $db->query($sql);
		}
		if (!buscarDia($cia['dia'], $dep) && $cia['efectivo'] > 0) {
			$cias[$cont]['num_cia'] = $num_cia;
			$cias[$cont]['nombre'] = $cia['nombre_corto'];
			$cias[$cont]['admin'] = $cia['admin'];
			$cias[$cont]['dia'] = $cia['dia'];
			$cont++;
		}
	}
	
	// [05-Mar-2007] Si se consultan pendientes del mes pasado y ya pasaron 8 dias del mes corriente, descartar como pendiente las compañías
	// que tengan el mes completo sin depositos
	$limit_day = mktime(0, 0, 0, date('n'), 8, date('Y'));
	$ts_corte = mktime(0, 0, 0, $tmp[2], $tmp[1], $tmp[3]);
	if ($cont > 0/* && $ts_corte < $limit_day*/ && (date('d') > 8 || date('n') > $tmp[2])) {
		$num_cia = NULL;
		$no_contar = array();
		foreach ($cias as $cia) {
			if ($num_cia != $cia['num_cia']) {
				if ($num_cia != NULL && $dias >= $dias_por_mes)
					$no_contar[] = $num_cia;
				
				$num_cia = $cia['num_cia'];
				$dias = 0;
			}
			$dias++;
		}
		if ($num_cia != NULL && $dias >= $dias_por_mes)
			$no_contar[] = $num_cia;
		
		$cias_tmp = $cias;
		$cias = array();
		$cont = 0;
		foreach ($cias_tmp as $reg)
			if (!in_array($reg['num_cia'], $no_contar))
				$cias[$cont++] = $reg;
	}
	
	if ($cont > 0) {
		if ($_GET['idadmin'] != -1) {
			$tpl->newBlock("listado");
			$tpl->assign("dia", $tmp[1]);
			$tpl->assign("mes", mes_escrito($tmp[2]));
			$tpl->assign("anio", $tmp[3]);
		}
		
		$num_cia = NULL;
		$admin = NULL;
		foreach ($cias as $cia) {
			if ($_GET['idadmin'] == -1 && $admin != $cia['admin']) {
				if ($admin != NULL)
					$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
				
				$admin = $cia['admin'];
				
				$tpl->newBlock("listado");
				$tpl->assign("dia", $tmp[1]);
				$tpl->assign("mes", mes_escrito($tmp[2]));
				$tpl->assign("anio", $tmp[3]);
			}
			if ($num_cia != $cia['num_cia']) {
				$num_cia = $cia['num_cia'];
				
				$tpl->newBlock("fila");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("nombre_cia", $cia['nombre']);
				
				$str = "";
			}
			$str .= $cia['dia'] . "&nbsp;&nbsp;";
			$tpl->assign("dias", $str);
		}
		$tpl->printToScreen();
		die;
	}
	else {
		header("location: ./ban_pen_mes.php?codigo_error=1");
		die;
	}
}

$tpl->newBlock("datos");

$tpl->assign("fecha", date("d/m/Y", mktime(0, 0, 0, date('n'), date('d') - 2, date('Y'))));

if (!in_array($_SESSION['iduser'], $users)) {
	$tpl->newBlock("pan");
	
	$admins = $db->query("SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY idadministrador");
	foreach ($admins as $admin) {
		$tpl->newBlock("admin");
		$tpl->assign("id", $admin['id']);
		$tpl->assign("nombre", $admin['nombre']);
	}
}

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
?>