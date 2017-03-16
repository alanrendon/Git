<?php
// CONSULTA DE TRABAJADORES DUPLICADOS
// Tabla 'catalogo_trabajadores'
// Menu Proveedores y facturas -> Trabajadores

//define ('IDSCREEN',3311); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_rep.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// RESULTADOS DE LA BUSQUEDA PARA MODIFICACION
if (isset($_GET['buscar'])) {
	// Construir script SQL para la busqueda
	$sql = "SELECT catalogo_trabajadores.id AS id,num_emp,ap_paterno,ap_materno,catalogo_trabajadores.nombre AS nombre,num_cia,catalogo_companias.nombre_corto AS nombre_cia,catalogo_puestos.descripcion AS puesto,catalogo_turnos.descripcion AS turno,num_afiliacion,credito_infonavit,fecha_alta,fecha_baja,pendiente_alta,pendiente_baja,solo_aguinaldo
	FROM catalogo_trabajadores LEFT JOIN catalogo_companias USING(num_cia) LEFT JOIN catalogo_puestos USING(cod_puestos) LEFT JOIN catalogo_turnos USING(cod_turno) WHERE (ap_paterno,ap_materno,catalogo_trabajadores.nombre) IN (
	SELECT ap_paterno,ap_materno,nombre FROM catalogo_trabajadores WHERE id NOT IN (
	SELECT min(id) FROM catalogo_trabajadores WHERE fecha_baja IS NULL GROUP BY ap_paterno,ap_materno,nombre) AND fecha_baja IS NULL) ORDER BY ap_paterno,ap_materno,nombre";
	
	$result = ejecutar_script($sql,$dsn);
		
	if (!$result) {
		header("location: ./fac_tra_rep.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("result");
	
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("num_emp",$result[$i]['num_emp']);
		$tpl->assign("nombre",$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']." ".$result[$i]['nombre']);
		$tpl->assign("num_cia",$result[$i]['num_cia']);
		$tpl->assign("nombre_cia",$result[$i]['nombre_cia']);
		$tpl->assign("num_afiliacion",$result[$i]['num_afiliacion'] != "" ? $result[$i]['num_afiliacion'] : "&nbsp;");
		$tpl->assign("turno",$result[$i]['turno'] != "" ? $result[$i]['turno'] : "&nbsp;");
		$tpl->assign("puesto",$result[$i]['puesto'] != "" ? $result[$i]['puesto'] : "&nbsp;");
		$tpl->assign("fecha_alta",$result[$i]['fecha_alta']);
		$tpl->assign("infonavit",$result[$i]['credito_infonavit'] == "t" ? "SI" : "&nbsp;");
		
		// Buscar si tiene prestamos
		$sql = "SELECT id FROM prestamos WHERE id_empleado = {$result[$i]['id']} AND pagado = 'FALSE' LIMIT 1";
		$pres = ejecutar_script($sql,$dsn);
		$tpl->assign("prestamo",$pres ? "SI" : "&nbsp;");
		
		// STATUS
		if ($result[$i]['pendiente_baja'] != "") $status = "PENDIENTE BAJA";
		else if ($result[$i]['fecha_baja'] != "") $status = "BAJA DEFINITIVA";
		else if ($result[$i]['pendiente_alta'] != "") $status = "PENDIENTE ALTA";
		else if ($result[$i]['num_afiliacion'] != "") $status = "EN NOMINA";
		else if ($result[$i]['solo_aguinaldo'] == "t") $status = "SOLO AGUINALDO";
		else $status = "&nbsp;";
		$tpl->assign("status",$status);
	}
	
	$tpl->printToScreen();
	die;
}

// DATOS PARA LA BUSQUEDA
$tpl->newBlock("datos");

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

// Imprimir el resultado
$tpl->printToScreen();
?>