<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fac_env_pan.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['print'])) {
	$sql = "SELECT id, num_cia, nombre_corto AS nombre_cia, folio_ini, folio_fin, cantidad, idadministrador AS idadmin, nombre_administrador AS nombre_admin FROM facturas_enviadas LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_administradores USING (idadministrador) WHERE imp = 'TRUE' ORDER BY idadmin, num_cia, folio_ini";
	$result = $db->query($sql);
	
	if (!$result) {
		$tpl->newBlock('cerrar');
		die($tpl->printToScreen());
	}
	
	$dia = date('d');
	$mes = mes_escrito(date('n'), TRUE);
	$anio = date('Y');
	
	// Obtener ultimo folio
	$tmp = $db->query('SELECT folio FROM facturas_enviadas WHERE folio IS NOT NULL ORDER BY folio DESC LIMIT 1');
	$folio = $tmp ? $tmp[0]['folio'] + 1 : 1;
	
	$sql = '';
	$admin = NULL;
	foreach ($result as $reg) {
		if ($admin != $reg['idadmin']) {
			if ($admin != NULL) {
				$tpl->assign('carta.salto', '<br style="page-break-after:always;">');
				$folio++;
			}
			
			$admin = $reg['idadmin'];
			
			$tpl->newBlock('carta');
			$tpl->assign('dia', $dia);
			$tpl->assign('mes', $mes);
			$tpl->assign('anio', $anio);
			
			$tpl->assign('folio', $folio);
			
			$tpl->assign('admin', $reg['nombre_admin']);
		}
		$tpl->newBlock('rango');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_cia']);
		$tpl->assign('ini', $reg['folio_ini']);
		$tpl->assign('fin', $reg['folio_fin']);
		$tpl->assign('cantidad', $reg['cantidad']);
		
		$tpl->newBlock('portada');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_cia']);
		$tpl->assign('ini', $reg['folio_ini']);
		$tpl->assign('fin', $reg['folio_fin']);
		$tpl->assign('cantidad', $reg['cantidad']);
		$tpl->assign('tipo', $reg['num_cia'] <= 300 ? 'PAN' : 'POLLO');
		$tpl->assign('mes', mes_escrito(date('n', mktime(0, 0, 0, date('d') > 23 ? date('n') + 1 : date('n'), 1, date('Y'))), TRUE));
		$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('d') > 23 ? date('n') + 1 : date('n'), 1, date('Y'))));
		
		$sql .= "UPDATE facturas_enviadas SET folio = $folio WHERE id = $reg[id];\n";
	}
	$sql .= "UPDATE facturas_enviadas SET imp = 'FALSE' WHERE imp = 'TRUE';\n";
	$db->query($sql);
	
	die($tpl->printToScreen());
}

if (isset($_POST['num_cia'])) {
	$sql = '';
	foreach ($_POST['num_cia'] as $i => $v)
		if (get_val($v) > 0 && get_val($_POST['cantidad'][$i]) > 0)
			$sql .= "INSERT INTO facturas_enviadas (num_cia, folio_ini, folio_fin, cantidad) VALUES ($v, {$_POST['folio_ini'][$i]}, {$_POST['folio_fin'][$i]}, {$_POST['cantidad'][$i]});\n";
	
	if ($sql != '') $db->query($sql);
	
	die(header('location: ./ban_fac_env_pan.php'));
}

$tpl->newBlock('captura');

$numfilas = 20;

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
}

$result = $db->query('SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia < 900 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

$tpl->printToScreen();
?>