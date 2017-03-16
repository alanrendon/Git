<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/fac/listado_trabajadores_1.tpl" );
$tpl->prepare();

$sql = "SELECT num_emp, ap_paterno, ap_materno, nombre, num_cia, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno, fecha_alta FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING (cod_puestos) LEFT JOIN catalogo_turnos USING (cod_turno) WHERE fecha_baja IS NULL";
$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
$sql .= " ORDER BY num_cia,cod_turno,cod_puestos";
$result = $db->query($sql);

if (!$result) {
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

function antiguedad($fecha_alta) {
	// Desglozar elementos de la fecha
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha_alta,$fecha))
		return FALSE;
	
	// Timestamp de la fecha de alta
	$ts_alta = mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]);
	// Timestamp actual
	$ts_current = mktime(0, 0, 0, date("n"), date("d"), date("Y"));
	// Diferencia
	$diferencia = $ts_current - $ts_alta;
	// Calcular antiguedad
	$antiguedad[0] = date("Y", $diferencia) - 1970;	// Años
	$antiguedad[1] = date("n", $diferencia) - 1;	// Meses
	$antiguedad[2] = date("d", $diferencia) - 1;	// Días
	
	return $antiguedad;
}

$numfilas_x_hoja = 28;

$num_cia = NULL;
for ($i = 0; $i < count($result); $i++) {
	if ($num_cia != $result[$i]['num_cia']) {
		if ($num_cia != NULL) {
			$tpl->newBlock("total");
			$tpl->assign("num_trabajadores", $count);
		}
		
		$num_cia = $result[$i]['num_cia'];
		
		$tpl->newBlock("hoja");
		$tpl->assign("num_cia", $num_cia);
		$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
		$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
		
		$numfilas = 0;
		$count = 0;
	}
	if ($numfilas == $numfilas_x_hoja) {
		$tpl->newBlock("hoja");
		$tpl->assign("num_cia", $num_cia);
		$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
		
		$numfilas = 0;
	}
	$tpl->newBlock("fila");
	$tpl->assign("num_emp", $result[$i]['num_emp']); 
	$tpl->assign("nombre", "{$result[$i]['ap_paterno']} {$result[$i]['ap_materno']} {$result[$i]['nombre']}");
	$tpl->assign("puesto", $result[$i]['puesto']);
	$tpl->assign("turno", $result[$i]['turno']);
	$ant = antiguedad($result[$i]['fecha_alta']);
	$tpl->assign("asterisco", $result[$i]['fecha_alta'] == "" || $ant[0] < 1 ? "*****" : "&nbsp;");
	
	$count++;
	$numfilas++;
}
if ($num_cia != NULL) {
	$tpl->newBlock("total");
	$tpl->assign("num_trabajadores", $count);
}

// Imprimir el resultado
$tpl->printToScreen();
?>