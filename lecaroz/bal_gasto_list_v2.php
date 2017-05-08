<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "No existe el gasto";
$descripcion_error[3] = "No existe la compañía";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_gasto_list_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['anio'])) {
	$tpl->newBlock("datos");
	$tpl->assign("anio", date("Y"));
	
	if (!in_array($_SESSION['iduser'], $users)) {
		$tpl->newBlock("option_admin");
		$admins = $db->query("SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY nombre_administrador");
		foreach ($admins as $admin) {
			$tpl->newBlock("admin");
			$tpl->assign("id", $admin['id']);
			$tpl->assign("admin", $admin['admin']);
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
}

$fecha1 = "01/01/$_GET[anio]";
$fecha2 = "31/12/$_GET[anio]";

$sql = "SELECT num_cia, nombre_corto" . (isset($_GET['idadmin']) && ($_GET['idadmin'] == -1 || $_GET['idadmin'] > 0) ? ", idadministrador" : "") . " FROM movimiento_gastos";
$sql .= " LEFT JOIN catalogo_companias USING (num_cia) WHERE codgastos = $_GET[codgastos] AND fecha BETWEEN '$fecha1' AND '$fecha2'";
$sql .= $_SESSION['iduser'] != 1 ? ($_SESSION['tipo_usuario'] == 2 ? " AND num_cia BETWEEN 900 AND 998" : " AND num_cia BETWEEN 1 AND 899") : '';
if (isset($_GET['com'])) {
	$cias = array();
	foreach ($_GET['cias'] as $cia)
		if ($cia > 0)
			$cias[] = $cia;
	
	if (count($cias) > 0) {
		$sql .= " AND num_cia IN (";
		foreach ($cias as $i => $cia)
			$sql .= $cia . ($i < count($cias) - 1 ? ", " : ")");
	}
}
else
	$sql .= isset($_GET['idadmin']) ? ($_GET['idadmin'] == "" ? ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") : ($_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : "")) : "";
if (isset($_GET['com']) || (isset($_GET['idadmin']) && $_GET['idadmin'] == "") || in_array($_SESSION['iduser'], $users))
	$sql .= " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
else if (isset($_GET['idadmin']))
	$sql .= "GROUP BY num_cia, nombre_corto, idadministrador ORDER BY idadministrador, num_cia";
$cia = $db->query($sql);

if (!$cia) {
	header("location: ./bal_gasto_list_v2.php?codigo_error=1");
	die;
}

$num_meses = $_GET['anio'] < date("Y") ? 12 : date("n", mktime(0, 0, 0, date("n"), ($_SESSION['tipo_usuario'] == 2 ? 1 : 0), $_GET['anio']));
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

$gasto = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = $_GET[codgastos]");
if (isset($_GET['com']) || (isset($_GET['idadmin']) && $_GET['idadmin'] == "") || in_array($_SESSION['iduser'], $users)) {
	$tpl->newBlock("listado");
	$tpl->assign("anio", $_GET['anio']);
	$tpl->assign("nombre_gasto", $gasto[0]['descripcion']);
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
		$tpl->assign("nombre_gasto", $gasto[0]['descripcion']);
		
		for($z = 1; $z <= 12; $z++)
			$t[$z] = 0;
		
		$grantotal = 0;
		$promedio = 0;
	}
	
	$sql = "SELECT extract(month FROM fecha) AS mes, sum(importe) AS importe FROM movimiento_gastos";
	$sql .= " WHERE num_cia = {$cia[$i]['num_cia']} AND codgastos = $_GET[codgastos] AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= " GROUP BY extract(month FROM fecha) ORDER BY extract(month FROM fecha)";
	$result = $db->query($sql);

	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
	
	$total = 0;
	
	$n_meses = 0;
	for ($j = 1; $j <= $num_meses; $j++) {
		$pro = buscar($j);
		
		$total += $pro;
		
		$t[$j] += $pro;
		$tpl->assign($j, round($pro, 3) != 0 ? number_format($pro, 0, ".", ",") : "&nbsp;");
		
		$n_meses += round($pro, 3) != 0 ? 1 : 0;
	}
	$tpl->assign("total", number_format($total, 0, ".", ","));
	$tpl->assign("prom", $n_meses > 0 ? number_format($total / $n_meses, 0, ".", ",") : '&nbsp;');
	$grantotal += $total;
	$promedio += $n_meses > 0 ? $total / $n_meses : 0;
}

$tpl->gotoBlock("listado");

for($z=1;$z<=12;$z++){
	if($t[$z] > 0)
		$tpl->assign("t" . $z, number_format($t[$z], 0, '.', ','));
}

$tpl->assign("total", number_format($grantotal, 0, ".", ","));
$tpl->assign("prom", number_format($promedio, 0, ".", ","));

$tpl->printToScreen();
//$db->desconectar();
?>