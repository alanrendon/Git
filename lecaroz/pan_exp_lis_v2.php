<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, 'autocommit=yes');

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";


// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/pan/pan_exp_lis_v2.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = 'SELECT num_cia, cc.nombre AS nombre_cia, cc.nombre_corto, num_expendio AS exp, num_referencia AS ref, ce.nombre, upper(te.descripcion) AS tipo, porciento_ganancia AS por, CASE WHEN aut_dev = \'TRUE\' THEN \'SI\' ELSE \'&nbsp;\' END AS dev FROM catalogo_expendios ce LEFT JOIN tipo_expendio te ON (tipo_expendio = idtipoexpendio) LEFT JOIN catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_operadoras co USING (idoperadora)';
	if ($_GET['num_cia'] > 0 || !in_array($_SESSION['iduser'], array(1, 4))) {
		$sql .= ' WHERE ';
		
		$opt = array();
		
		if ($_GET['num_cia'] > 0)
			$opt[] = "num_cia >= $_GET[num_cia]";
		if (!in_array($_SESSION['iduser'], array(1, 4)))
			$opt[] = "iduser = $_SESSION[iduser]";
		
		$sql .= implode(' AND ', $opt);
	}
	$sql .= ' ORDER BY num_cia, exp';
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./pan_exp_lis_v2.php?codigo_error=1'));
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL)
				$tpl->assign('listado.salto', '<br style="page-break-after:always;">');
			
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('listado');
			
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $reg['nombre_cia']);
			$tpl->assign('nombre_corto', $reg['nombre_corto']);
		}
		$tpl->newBlock('fila');
		
		if ($reg['por'] > 20)
			$tpl->assign('class', ' highlight');
		
		$tpl->assign('exp', $reg['exp']);
		$tpl->assign('ref', $reg['ref']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('tipo', $reg['tipo']);
		$tpl->assign('por', $reg['por'] > 0 ? number_format($reg['por'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('dev', $reg['dev']);
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>