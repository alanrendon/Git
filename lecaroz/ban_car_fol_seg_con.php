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

if (isset($_GET['id'])) {
	$sql = "SELECT fecha_respuesta, dependencia, responsable, expediente, observaciones FROM cartas_foleadas_seguimiento WHERE id_carta = $_GET[id]";
	$result = $db->query($sql);
	
	$data = $_GET['id'] . '|||';
	
	if (!$result) {
		$data .= '<span style="color:#F00;font-weight:bold;font-size:12pt;">No hay resultados</span>';
	}
	else {
		// Coneccion a la base de datos de las imagenes
		$dbs = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');
		
		$data .= '<table width="100%"><tr><th class="tabla" scope="col">Fecha<br />Respuesta</th><th class="tabla" scope="col">Dependencia</th><th class="tabla" scope="col">Responsable</th><th class="tabla" scope="col">Expediente</th><th class="tabla" scope="col">Observaciones</th><th class="tabla" scope="col">Asociaci&oacute;n</th></tr>';
		foreach ($result as $reg) {
			$data .= "<tr><td class=\"tabla\" style=\"color:#C00;\">$reg[fecha_respuesta]</td><td class=\"vtabla\">$reg[dependencia]</td><td class=\"vtabla\">$reg[responsable]</td><td class=\"vtabla\">" . (trim($reg['expediente']) != '' ? $reg['expediente'] : '&nbsp;') . "</td><td class=\"vtabla\">$reg[observaciones]</td><td class=\"tabla\">&nbsp;</td></tr>";
			
			// [19-Feb-2009] Obtener id's y fechas de documentos escaneados
			$sql = "SELECT id, tsmod::date AS fecha FROM img_doc_car WHERE id_car = $_GET[id] AND fecha_respuesta = '$reg[fecha_respuesta]' ORDER BY fecha_respuesta";
			$scans = $dbs->query($sql);
			
			if ($scans) {
				$data .= '<tr><td colspan="6"><table width="100%"><tr><td class="vtabla">';
				foreach ($scans as $s) {
					$data .= '<img src="img_doc_car.php?id=' . $s['id'] . '&width=90" title="Digitalizado el d&iacute;a ' . $s['fecha'] . '" onmouseover="this.style.cursor=\'pointer\'" onmouseout="this.style.cursor=\'default\'" onclick="show(' . $s['id'] . ')" />&nbsp;&nbsp;';
				}
				$data .= '</td></tr></table></td></tr>';
			}
		}
		$data .= '</table>';
	}
	
	echo $data;
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_car_fol_seg_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	// [07-Jun-2010] Desglosar palabras clave de búsqueda
	$pieces = explode('+', trim($_GET['palabras']));
	if (count($pieces) > 0) {
		foreach ($pieces as $p) {
			$palabras[] = $p;
		}
	}
	
	$sql = 'SELECT id, num_cia, cc.nombre_corto AS nombre_cia, folio, trim(upper(atencion)) AS atencion, trim(upper(cf.referencia)) AS referencia, fecha FROM cartas_foleadas cf LEFT JOIN catalogo_companias cc USING (num_cia) WHERE seguimiento = ' . $_GET['tipo'] . '/* AND (id, fecha) NOT IN (SELECT id_carta, fecha_respuesta FROM cartas_foleadas_seguimiento)*/';
	$sql .= ' AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 1 ? '1 AND 899' : '900 AND 998');
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['folio'] > 0 ? " AND folio = $_GET[folio]" : '';
	$sql .= trim($_GET['atencion']) != '' ? ' AND atencion LIKE \'%' . (strtoupper(trim($_GET['atencion']))) . '%\'' : '';
	$sql .= trim($_GET['referencia']) != '' ? ' AND cf.referencia LIKE \'%' . (strtoupper(trim($_GET['referencia']))) . '%\'' : '';
	$sql .= trim($_GET['dependencia']) != '' ? ' AND (id) IN (SELECT id_carta FROM cartas_foleadas_seguimiento WHERE dependencia LIKE \'%' . (strtoupper(trim($_GET['dependencia']))) . '%\')' : '';
	$sql .= trim($_GET['responsable']) != '' ? ' AND (id) IN (SELECT id_carta FROM cartas_foleadas_seguimiento WHERE responsable LIKE \'%' . (strtoupper(trim($_GET['responsable']))) . '%\')' : '';
	$sql .= count($palabras) > 0 ? ' AND cuerpo ~* \'(' . implode('|', $palabras) . ')\'' : '';
	$sql .= ' ORDER BY num_cia, folio';
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ban_car_fol_seg_con.php'));
	
	$tpl->newBlock('resultado');
	
	foreach ($result as $i => $reg) {
		$tpl->newBlock('carta');
		$tpl->assign('i', $i);
		
		$tpl->assign('id', $reg['id']);
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_cia']);
		$tpl->assign('folio', $reg['folio']);
		$tpl->assign('atencion', $reg['atencion']);
		$tpl->assign('referencia', $reg['referencia']);
		$tpl->assign('fecha', $reg['fecha']);
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');
$tpl->printToScreen();
?>