<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Obtener nombre de compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$result = $db->query($sql);
	
	die(trim($result[0]['nombre']));
}

// [AJAX] Obtener empleados
if (isset($_GET['ce'])) {
	$sql = "SELECT id, num_emp, ap_paterno, ap_materno, nombre FROM catalogo_trabajadores WHERE num_cia = $_GET[ce] AND (fecha_baja IS NULL OR fecha_baja > now()::date - interval '1 month') ORDER BY ap_paterno, ap_materno, nombre";
	$result = $db->query($sql);
	
	if (!$result) die("-1");
	
	$data = "";
	foreach ($result as $i => $reg)
		$data .= "$reg[id]/$reg[num_emp]-$reg[ap_paterno] $reg[ap_materno] $reg[nombre]" . ($i < count($result) - 1 ? '|' : '');
	
	die($data);
}


if (isset($_POST['ids'])) {
	
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_doc_emp_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	// Coneccion a la base de datos de las imagenes
	$dbs = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");
	
	if ($_GET['id_emp'] > 0) {
		$sql = 'SELECT * FROM img_doc_emp WHERE id_emp = ' . $_GET['id_emp'];
		$sql .= $_GET['tipo'] != '' ? " AND tipo = $_GET[tipo]" : '';
		
		if (!($tmp = $dbs->query($sql)))
			die(header('location: fac_doc_emp_con.php?codigo_error=1'));
		
		$sql = 'SELECT ct.id, num_cia, cc.nombre_corto AS nombre_cia, num_emp, ct.nombre || \' \' || ap_paterno || \' \' || ap_materno AS nombre_emp FROM catalogo_trabajadores ct LEFT JOIN catalogo_companias cc USING (num_cia) WHERE id = ' . $_GET['id_emp'];
		
		$result = $db->query($sql);
	}
	else {
		$sql = 'SELECT id_emp FROM img_doc_emp';
		$sql .= $_GET['tipo'] != '' ? " WHERE tipo = $_GET[tipo]" : '';
		$sql .= ' GROUP BY id_emp';
		
		if (!($tmp = $dbs->query($sql)))
			die(header('location: fac_doc_emp_con.php?codigo_error=1'));
		
		$emp = array();
		foreach ($tmp as $t)
			$emp[] = $t['id_emp'];
		
		$sql = 'SELECT ct.id, num_cia, cc.nombre_corto AS nombre_cia, num_emp, ct.nombre || \' \' || ap_paterno || \' \' || ap_materno AS nombre_emp FROM catalogo_trabajadores ct LEFT JOIN catalogo_companias cc USING (num_cia) WHERE id IN (' . implode(', ', $emp) . ') ORDER BY num_cia, nombre_emp';
		
		$result = $db->query($sql);
	}
	
	if (!$result)
		die(header('location: fac_doc_emp_con.php?codigo_error=1'));
	
	$tpl->newBlock('consulta');
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre', $reg['nombre_cia']);
		}
		$tpl->newBlock('emp');
		$tpl->assign('num_emp', $reg['num_emp']);
		$tpl->assign('nombre', $reg['nombre_emp']);
		
		$sql = 'SELECT id FROM img_doc_emp WHERE id_emp = ' . $reg['id'];
		$sql .= $_GET['tipo'] != '' ? " AND tipo = $_GET[tipo]" : '';
		$sql .= ' ORDER BY tipo DESC';
		$docs = $dbs->query($sql);
		
		$ids = array();
		foreach ($docs as $doc) {
			$tpl->newBlock('doc');
			$tpl->assign('id', $doc['id']);
			
			$ids[] = $doc['id'];
		}
		$tpl->assign('emp.ids', implode(',', $ids));
	}
	
	die($tpl->printToScreen());
}

if (isset($_GET['num_cia'])) {
	$tpl->newBlock('scan');
	$tpl->assign('id_emp', $_GET['id_emp']);
	$tpl->assign('tipo', $_GET['tipo']);
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>