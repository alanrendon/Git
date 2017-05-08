<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

//if ($_SESSION['iduser'] != 1) die(header('location: ./offline.htm'));

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$cia = $db->query($sql);
	
	if ($cia)
		echo $cia[0]['nombre_corto'];
	
	die;
}

if (isset($_POST['id'])) {
	$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");
	
	$sql = '';
	$sql_scans = '';
	for ($i = 0; $i < count($_POST['id']); $i++) {
		if (ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_POST['fecha_respuesta'][$i])) {
			if (trim($_POST['dependencia'][$i]) != '' && trim($_POST['responsable'][$i]) != '' && trim($_POST['observaciones'][$i]) != '') {
				// [19-Feb-2009] Revisar si tiene relación con algun otro asunto
				if ($_POST['num_cia'][$i] > 0 && $_POST['folio'][$i] > 0) {
					$tmp = $db->query("SELECT id FROM cartas_foleadas WHERE num_cia = {$_POST['num_cia'][$i]} AND folio = {$_POST['folio'][$i]}");
					$relacion = $tmp ? $tmp[0]['id'] : 'NULL';
				}
				else
					$relacion = 'NULL';
				
				$sql .= 'INSERT INTO cartas_foleadas_seguimiento (id_carta, fecha_respuesta, dependencia, responsable, expediente, observaciones, relacion) VALUES (';
				$sql .= "{$_POST['id'][$i]}, '{$_POST['fecha_respuesta'][$i]}',";
				$sql .= ' \'' . strtoupper(trim($_POST['dependencia'][$i])) . '\',';
				$sql .= ' \'' . strtoupper(trim($_POST['responsable'][$i])) . '\',';
				$sql .= ' \'' . strtoupper(trim($_POST['expediente'][$i])) . '\',';
				$sql .= ' \'' . strtoupper(trim($_POST['observaciones'][$i])) . '\',';
				$sql .= ' ' . $relacion . ");\n";
				
				$sql_scans .= 'INSERT INTO img_doc_car (id_car, imagen, iduser, tsmod, fecha_respuesta) SELECT id_car, imagen, iduser, now(), \'' . $_POST['fecha_respuesta'][$i] . '\' FROM img_doc_car_tmp WHERE iduser = ' . $_SESSION['iduser'] . ' AND id_car = ' . $_POST['id'][$i] . ";\n";
				//$sql_scans .= 'DELETE FROM img_doc_car_tmp WHERE iduser = ' . $_SESSION['iduser'] . ' AND id_car = ' . $_POST['id'][$i] . ";\n";
			}
			if ($_POST['fecha'][$i] == $_POST['fecha_respuesta'][$i])
				$sql .= 'UPDATE cartas_foleadas SET seguimiento = 2 WHERE id = ' . $_POST['id'][$i] . ";\n";
		}
	}
	
	$sql_scans .= 'DELETE FROM img_doc_car_tmp WHERE iduser = ' . $_SESSION['iduser'] . ";\n";
	
	if ($_SESSION['iduser'] == 1) {
		echo "<pre>$sql</pre>";
		die;
	}
	else {
		if ($sql != '') {
			$db->query($sql);
			$db_scans->query($sql_scans);
		}
	}
	
	die(header('location: ban_car_fol_seg.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_car_fol_seg.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");
	$db_scans->query('DELETE FROM img_doc_car_tmp WHERE iduser = ' . $_SESSION['iduser']);
	
	// [07-Jun-2010] Desglosar palabras clave de búsqueda
	$pieces = explode('+', trim($_GET['palabras']));
	if (count($pieces) > 0) {
		foreach ($pieces as $p) {
			$palabras[] = $p;
		}
	}
	
	$sql = 'SELECT id, num_cia, cc.nombre_corto AS nombre_cia, folio, trim(upper(atencion)) AS atencion, trim(upper(cf.referencia)) AS referencia, fecha, (SELECT dependencia FROM cartas_foleadas_seguimiento WHERE id_carta = cf.id ORDER BY id DESC LIMIT 1) AS dependencia, (SELECT responsable FROM cartas_foleadas_seguimiento WHERE id_carta = cf.id ORDER BY id DESC LIMIT 1) AS responsable, (SELECT expediente FROM cartas_foleadas_seguimiento WHERE id_carta = cf.id ORDER BY id DESC LIMIT 1) AS expediente FROM cartas_foleadas cf LEFT JOIN catalogo_companias cc USING (num_cia) WHERE seguimiento = 1 AND (id, fecha) NOT IN (SELECT id_carta, fecha_respuesta FROM cartas_foleadas_seguimiento)';
	$sql .= ' AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 1 ? '1 AND 899' : '900 AND 998');
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['folio'] > 0 ? " AND folio = $_GET[folio]" : '';
	$sql .= trim($_GET['atencion']) != '' ? ' AND atencion LIKE \'%' . (strtoupper(trim($_GET['atencion']))) . '%\'' : '';
	$sql .= trim($_GET['referencia']) != '' ? ' AND cf.referencia LIKE \'%' . (strtoupper(trim($_GET['referencia']))) . '%\'' : '';
	$sql .= count($palabras) > 0 ? ' AND cuerpo ~* \'(' . implode('|', $palabras) . ')\'' : '';
	$sql .= ' ORDER BY num_cia, folio';
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ban_car_fol_seg.php'));
	
	$tpl->newBlock('resultado');
	
	foreach ($result as $i => $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('i', $i);
		$tpl->assign('next', $i < count($result) - 1 ? $i + 1 : 0);
		$tpl->assign('back', $i > 0 ? $i - 1 : count($result) - 1);
		
		$tpl->assign('id', $reg['id']);
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_cia']);
		$tpl->assign('folio', $reg['folio']);
		$tpl->assign('atencion', $reg['atencion']);
		$tpl->assign('referencia', $reg['referencia']);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('dependencia', $reg['dependencia']);
		$tpl->assign('responsable', $reg['responsable']);
		$tpl->assign('expediente', $reg['expediente']);
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');
$tpl->printToScreen();
?>