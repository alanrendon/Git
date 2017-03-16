<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/cheques.inc.php';

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

// [AJAX] Obtener empleados con Infonavit
if (isset($_GET['ce'])) {
	$sql = "SELECT id, num_emp, ap_paterno, ap_materno, nombre FROM catalogo_trabajadores WHERE num_cia = $_GET[ce] AND fecha_baja IS NULL AND num_afiliacion IS NOT NULL AND trim(num_afiliacion) <> '' ORDER BY num_emp ASC";
	$result = $db->query($sql);
	
	if (!$result) die("-1");
	
	$data = "";
	foreach ($result as $i => $reg)
		$data .= "$reg[id]/$reg[num_emp]-$reg[ap_paterno] $reg[ap_materno] $reg[nombre]" . ($i < count($result) - 1 ? '|' : '');
	
	die($data);
}

if (isset($_GET['num_cia'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/fac/carta_patronal.tpl" );
	$tpl->prepare();
	
	$sql = "SELECT cc.num_cia, cc.nombre AS nombre_cia, no_imss, cc.rfc, direccion, ct.nombre, ct.ap_paterno, ct.ap_materno, num_afiliacion, fecha_alta_imss, sexo, COALESCE(ct.calle, 'S/CALLE') || ', ' || COALESCE(ct.colonia, 'S/COLONIA') || ', ' || COALESCE(ct.del_mun, 'S/DELEGACION') || ', ' || COALESCE(ct.entidad, 'S/ENTIDAD') || ', CP ' || COALESCE(ct.cod_postal, 'S/CP') AS dir_emp, COALESCE(LPAD(EXTRACT(HOUR FROM hora_inicio)::VARCHAR, 2, '0') || ':' || LPAD(EXTRACT(MINUTE FROM hora_inicio)::VARCHAR, 2, '0') || ' a ' || LPAD(EXTRACT(HOUR FROM hora_termino)::VARCHAR, 2, '0') || ':' || LPAD(EXTRACT(MINUTE FROM hora_termino)::VARCHAR, 2, '0'), NULL) AS horario FROM catalogo_trabajadores ct LEFT JOIN catalogo_companias cc USING (num_cia) WHERE ct.id = $_GET[id_emp]";
	$result = $db->query($sql);
	
	$tpl->assign('dia', date('d'));
	$tpl->assign('mes', mes_escrito(date('n')));
	$tpl->assign('anio', date('Y'));
	$tpl->assign('sal_int', number_format(get_val($_GET['sal_int']), 2, '.', ','));
	$tpl->assign('sal_int_esc', num2string(get_val($_GET['sal_int'])));
	$tpl->assign('sal', number_format(get_val($_GET['sal']), 2, '.', ','));
	$tpl->assign('sal_esc', num2string(get_val($_GET['sal'])));
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha'], $tmp);
	$tpl->assign('dia_alta', $tmp[1]);
	$tpl->assign('mes_alta', mes_escrito(intval($tmp[2], 10)));
	$tpl->assign('anio_alta', $tmp[3]);
	
	foreach ($result[0] as $tag => $reg) {
		switch ($tag) {
			case 'num_cia': $tpl->assign('num_cia', $reg); break;
			case 'nombre_cia': $tpl->assign('nombre_cia', $reg); break;
			case 'rfc': $tpl->assign('rfc', $reg); break;
			case 'no_imss': $tpl->assign('no_imss', $reg); break;
			case 'direccion': $tpl->assign('dir', $reg); break;
			case 'sexo':
				$tpl->assign('art', $reg == 'f' ? 'el' : 'la');
				$tpl->assign('tit', $reg == 'f' ? 'Sr' : 'Sra');
				break;
			case 'nombre': $tpl->assign('nombre', $reg); break;
			case 'ap_paterno': $tpl->assign('ap_pat', $reg); break;
			case 'ap_materno': $tpl->assign('ap_mat', $reg); break;
			case 'num_afiliacion': $tpl->assign('num_afiliacion', $reg); break;
			case 'dir_emp':
				if (strlen(trim($reg)) > 0) {
					$tpl->newBlock('dir_emp');
					$tpl->assign('dir_emp', $reg);
				}
			break;
			case 'horario':
				if ($reg != '') {
					$tpl->newBlock('horario');
					$tpl->assign('horario', $reg);
				}
			break;
			/*case 'fecha_alta_imss':
				ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $reg, $tmp);
				$tpl->assign('dia_alta', $tmp[1]);
				$tpl->assign('mes_alta', mes_escrito(intval($tmp[2], 10)));
				$tpl->assign('anio_alta', $tmp[3]);
				break;*/
		}
	}
	
	die($tpl->printToScreen());
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_car_pat.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->printToScreen();
?>