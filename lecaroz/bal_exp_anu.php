<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "No existe el gasto";
$descripcion_error[3] = "No existe la compañía";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_exp_anu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['anio'])) {
	$tpl->newBlock("datos");
	$tpl->assign("anio", date("Y"));
	
	$admins = $db->query("SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY nombre_administrador");
	foreach ($admins as $admin) {
		$tpl->newBlock("admin");
		$tpl->assign("id", $admin['id']);
		$tpl->assign("admin", $admin['admin']);
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
}

$fecha1 = "01/01/$_GET[anio]";
$fecha2 = $_GET['anio'] < date("Y") ? "31/12/$_GET[anio]" : date("d/m/Y", mktime(0, 0, 0, date("n"), 0, $_GET['anio']));
$num_meses = $_GET['anio'] < date("Y") ? 12 : date("n", mktime(0, 0, 0, date("n"), 0, $_GET['anio']));

$fecha_ini = "(";
for ($i = 1; $i <= $num_meses; $i++)
	$fecha_ini .= "'01/$i/$_GET[anio]'" . ($i < $num_meses ? ", " : ")");

$fecha_fin = "(";
for ($i = 1; $i <= $num_meses; $i++)
	$fecha_fin .= "'" . date("d/m/Y", mktime(0, 0, 0, $i + 1, 0, $_GET['anio'])) . ($i < $num_meses ? "', " : "')");

$nombre_campo = array(
"rezago_anterior" => "Rezago Inicial",
"pan_p_venta" => "Pan para Venta",
"pan_p_expendio" => "Pan para Expendio",
"abono" => "Abonos",
"devolucion" => "Devoluciones",
"rezago" => "Rezago Final"
);

if (!isset($_GET['exp'])) {
	$sql = "SELECT num_cia, nombre_corto" . (isset($_GET['idadmin']) && ($_GET['idadmin'] == -1 || $_GET['idadmin'] > 0) ? ", idadministrador" : "") . " FROM mov_expendios";
	$sql .= " LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha";
	if ($_GET['campo'] != "rezago_anterior" && $_GET['campo'] != "rezago")
		$sql .= " BETWEEN '$fecha1' AND '$fecha2'";
	else
		$sql .= " IN " . ($_GET['campo'] == "rezago" ? $fecha_fin : $fecha_ini);
	$sql .= " AND $_GET[campo] > 0";
	$sql .= $_GET['idadmin'] == "" ? ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") : ($_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : "");
	if ($_GET['idadmin'] == "")
		$sql .= " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
	else
		$sql .= " GROUP BY num_cia, nombre_corto, idadministrador ORDER BY idadministrador, num_cia";
	$cia = $db->query($sql);
	
	for($z = 1; $z <= 12; $z++)
		$t[$z] = 0;
	
	$grantotal = 0;
	$promedio = 0;
	
	function buscar($mes) {
		global $result;
		
		for ($i = 0; $i < count($result); $i++)
			if ($mes == $result[$i]['mes'])
				return $result[$i]['importe'];
		
		return 0;
	}
	
	if (isset($_GET['com']) || $_GET['idadmin'] == "") {
		$tpl->newBlock("listado");
		$tpl->assign("anio", $_GET['anio']);
		$tpl->assign("campo", $nombre_campo[$_GET['campo']]);
	}
	
	$idadmin = NULL;
	for ($i = 0; $i < count($cia); $i++) {
		if (isset($_GET['idadmin']) && ($_GET['idadmin'] == -1 || $_GET['idadmin'] > 0) && $cia[$i]['idadministrador'] != $idadmin) {
			if ($idadmin != NULL) {
				$tpl->gotoBlock("listado");
				
				for($z=1;$z<=12;$z++){
					if($t[$z] > 0)
						$tpl->assign("t" . $z, number_format($t[$z], 0, '.', ','));
				}
				
				$tpl->assign("total", number_format($grantotal, 0, ".", ","));
				$tpl->assign("prom", number_format($promedio, 0, ".", ","));
			}
			
			$idadmin = $cia[$i]['idadministrador'];
			
			$tpl->newBlock("listado");
			$tpl->assign("anio", $_GET['anio']);
			$tpl->assign("campo", $nombre_campo[$_GET['campo']]);
			
			for($z = 1; $z <= 12; $z++)
				$t[$z] = 0;
			
			$grantotal = 0;
			$promedio = 0;
		}
		
		$sql = "SELECT extract(month FROM fecha) AS mes, sum($_GET[campo]) AS importe FROM mov_expendios";
		$sql .= " WHERE num_cia = {$cia[$i]['num_cia']} AND fecha";
		if ($_GET['campo'] != "rezago_anterior" && $_GET['campo'] != "rezago")
			$sql .= " BETWEEN '$fecha1' AND '$fecha2' AND $_GET[campo] > 0";
		else
			$sql .= " IN " . ($_GET['campo'] == "rezago" ? $fecha_fin : $fecha_ini);
		$sql .= " GROUP BY extract(month FROM fecha) ORDER BY extract(month FROM fecha)";
		$result = $db->query($sql);
	
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $cia[$i]['num_cia']);
		$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
		
		$total = 0;
		
		$presicion = 0;
		
		for ($j = 1; $j <= $num_meses; $j++) {
			$pro = buscar($j);
			
			$total += $pro;
			
			$t[$j] += $pro;
			$tpl->assign($j, round($pro, $presicion) != 0 ? number_format($pro, $presicion, ".", ",") : "&nbsp;");
		}
		$tpl->assign("total", number_format($total, $presicion, ".", ","));
		$tpl->assign("prom", number_format($total / $num_meses, $presicion, ".", ","));
		$grantotal += $total;
		$promedio += $total / $num_meses;
	}
	
	$tpl->gotoBlock("listado");
	
	for($z=1;$z<=12;$z++){
		if($t[$z] > 0)
			$tpl->assign("t" . $z, number_format($t[$z], $presicion, '.', ','));
	}
	$tpl->assign("total", number_format($grantotal, $presicion, ".", ","));
	$tpl->assign("prom", number_format($promedio, $presicion, ".", ","));
}
else {
	$sql = "SELECT num_cia, nombre, nombre_expendio, extract(month FROM fecha) AS mes, sum($_GET[campo]) AS importe FROM mov_expendios LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= " WHERE fecha";
	if ($_GET['campo'] != "rezago_anterior" && $_GET['campo'] != "rezago")
		$sql .= " BETWEEN '$fecha1' AND '$fecha2'";
	else
		$sql .= " IN " . ($_GET['campo'] == "rezago" ? $fecha_fin : $fecha_ini);
	$sql .= " AND $_GET[campo] > 0";
	$sql .= $_GET['idadmin'] == "" ? ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") : ($_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : "");
	if ($_GET['idadmin'] == "")
		$sql .= " GROUP BY num_cia, nombre, nombre_expendio, mes ORDER BY num_cia, nombre_expendio, mes";
	else
		$sql .= " GROUP BY num_cia, nombre, idadministrador, nombre_expendio, mes ORDER BY idadministrador, num_cia, nombre_expendio, mes";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./bal_exp_anu.php?codigo_error=1");
		die;
	}
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				if ($exp != NULL) {
					$tpl->assign("exp.total", number_format($total, 2, ".", ","));
					$tpl->assign("exp.prom", number_format($total / $num_meses, 2, ".", ","));
				}
				foreach ($total_mes as $m => $t)
					$tpl->assign("expendios.$m", $t != 0 ? number_format($t, 2, ".", ",") : "");
				
				$tpl->assign("expendios.total", array_sum($total_mes) != 0 ? number_format(array_sum($total_mes), 2, ".", ",") : "");
				$tpl->assign("expendios.prom", array_sum($total_mes) != 0 ? number_format(array_sum($total_mes) / $num_meses, 2, ".", ",") : "");
				$tpl->assign("expendios.salto", "<br style=\"page-break-after:always;\">");
			}
			
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock("expendios");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre", $reg['nombre']);
			$tpl->assign("anio", $_GET['anio']);
			$tpl->assign("campo", $nombre_campo[$_GET['campo']]);
			
			$total_mes = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
			$exp = NULL;
		}
		if ($exp != $reg['nombre_expendio']) {
			if ($exp != NULL) {
				$tpl->assign("exp.total", number_format($total, 2, ".", ","));
				$tpl->assign("exp.prom", number_format($total / $num_meses, 2, ".", ","));
			}
			
			$exp = $reg['nombre_expendio'];
			
			$tpl->newBlock("exp");
			$tpl->assign("exp", $exp);
			
			$total = 0;
		}
		$tpl->assign($reg['mes'], $reg['importe'] != 0 ? number_format($reg['importe'], 2, ".", ",") : "");
		$total += $reg['importe'];
		$total_mes[$reg['mes']] += $reg['importe'];
	}
	if ($num_cia != NULL) {
		if ($exp != NULL) {
			$tpl->assign("exp.total", number_format($total, 2, ".", ","));
			$tpl->assign("exp.prom", number_format($total / $num_meses, 2, ".", ","));
		}
		
		foreach ($total_mes as $m => $t)
			$tpl->assign("expendios.$m", $t != 0 ? number_format($t, 2, ".", ",") : "");
		
		$tpl->assign("expendios.total", array_sum($total_mes) != 0 ? number_format(array_sum($total_mes), 2, ".", ",") : "");
		$tpl->assign("expendios.prom", array_sum($total_mes) != 0 ? number_format(array_sum($total_mes) / $num_meses, 2, ".", ",") : "");
	}
}
$tpl->printToScreen();
//$db->desconectar();
?>