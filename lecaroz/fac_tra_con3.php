<?php
// CONSULTA DE TRABAJADORES
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

// --------------------------------- Funciones ---------------------------------------------------------------
function calcula_antiguedad($fecha_alta) {
	// Desglozar elementos de la fecha
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha_alta,$fecha))
		return FALSE;
	
	// Obtener TIMESTAMP de la fecha de alta
	$ts1 = mktime(0,0,0,$fecha[2],$fecha[1],$fecha[3]);
	// Obtener TIMESTAMP de la fecha actual
	$ts2 = time();
	
	// Obtener el número de días
	$dias = ceil(($ts2 - $ts1) / 86400);
	
	// Calcular años
	$years = floor($dias / 365);
	// Calcular meses
	$months = floor(($dias % 365) / 30.5);
	// Dias restantes
	$days = ($dias % 365) % 12;
	
	// Construir cadena
	$cadena = "";
	$cadena .= $years > 0 ? ($years == 1 ? "$years Año " : "$years Años ") : "";
	$cadena .= $months > 0 ? ($months == 1 ? "$months Mes " : "$months Meses ") : "";
	$cadena .= $days > 0 ? ($days == 1 ? "$days Día" : "$days Días") : "";
	
	return $cadena;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_con3.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// RESULTADOS DE LA BUSQUEDA PARA MODIFICACION
if (isset($_GET['buscar'])) {
	
	$condiciones = array();
	
	$condiciones[] = $_SESSION['tipo_usuario'] == 2 ? 'num_cia BETWEEN 900 AND 998' : 'num_cia BETWEEN 1 AND 899';
	
	if ($_REQUEST['num_emp'] > 0) {
		$condiciones[] = 'num_emp = ' . $_REQUEST['num_emp'];
	}
	
	if ($_REQUEST['num_cia'] > 0) {
		$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];
	}
	
	if ($_REQUEST['ap_paterno'] != '') {
		$condiciones[] = 'ap_paterno LIKE \'%' . $_REQUEST['ap_paterno'] . '%\'';
	}
	
	if ($_REQUEST['ap_materno'] != '') {
		$condiciones[] = 'ap_materno LIKE \'%' . $_REQUEST['ap_materno'] . '%\'';
	}
	
	if ($_REQUEST['nombre'] != '') {
		$condiciones[] = 'nombre LIKE \'%' . $_REQUEST['nombre'] . '%\'';
	}
	
	if ($_REQUEST['turno'] > 0) {
		$condiciones[] = 'cod_turno = ' . $_REQUEST['cod_turno'];
	}
	
	if ($_REQUEST['puesto']) {
		$condiciones[] = 'cod_puestos = ' . $_REQUEST['puesto'];
	}
	
	if ($_REQUEST['filtro'] == 'imss') {
		$condiciones[] = 'num_afiliacion IS NOT NULL';
	}
	
	if ($_REQUEST['filtro'] == 'no_imss') {
		$condiciones[] = 'num_afiliacion IS NULL';
	}
	
	if ($_REQUEST['filtro'] == 'solo_aguinaldo') {
		$condiciones[] = 'solo_aguinaldo = TRUE';
	}
	
	if ($_REQUEST['filtro'] == 'pen_altas') {
		$condiciones[] = 'pendiente_alta IS NOT NULL';
	}
	
	if ($_REQUEST['filtro'] == 'pen_bajas') {
		$condiciones[] = 'pendiente_baja IS NOT NULL';
	}
	
	$sql = '
		SELECT
			id,
			num_emp,
			ap_paterno,
			ap_materno,
			nombre,
			num_cia,
			catalogo_turnos.descripcion
				AS turno,
			catalogo_puestos.descripcion
				AS puesto,
			fecha_alta,
			fecha_baja,
			fecha_alta_imss,
			fecha_baja_imss,
			pendiente_alta,
			pendiente_baja,
			solo_aguinaldo,
			credito_infonavit,
			num_afiliacion
		FROM
			catalogo_trabajadores
			LEFT JOIN catalogo_puestos
				USING (cod_puestos)
			LEFT JOIN catalogo_turnos
				USING (cod_turno)
		WHERE
			' . implode(' AND ', $condiciones) . '
	';
	
	if (isset($_GET['bajas']) && $_GET['filtro'] == 'todos') {
		$condiciones[] = 'fecha_baja >= now()::date - INTERVAL \'4 MONTHS\'';
		
		$sql .= '
			
			UNION
			
			SELECT
				id,
				num_emp,
				ap_paterno,
				ap_materno,
				nombre,
				num_cia,
				catalogo_turnos.descripcion
					AS turno,
				catalogo_puestos.descripcion
					AS puesto,
				fecha_alta,
				fecha_baja,
				fecha_alta_imss,
				fecha_baja_imss,
				pendiente_alta,
				pendiente_baja,
				solo_aguinaldo,
				credito_infonavit,
				num_afiliacion
			FROM
				catalogo_trabajadores
				LEFT JOIN catalogo_puestos
					USING (cod_puestos)
				LEFT JOIN catalogo_turnos
					USING (cod_turno)
			WHERE
				' . implode(' AND ', $condiciones) . '
		';
	}
	
	$sql .= '
		ORDER BY
			num_cia,
			ap_paterno,
			ap_materno,
			nombre
	';
	
	$result = ejecutar_script($sql,$dsn);
		
	if (!$result) {
		header("location: ./fac_tra_con3.php?codigo_error=1");
		die;
	}
	
	// Obtener porcentaje de incremento de aguinaldo
	$sql = "SELECT * FROM porcentaje_aguinaldo ORDER BY id DESC LIMIT 1";
	$temp = ejecutar_script($sql,$dsn);
	$incremento = $temp ? $temp[0]['porcentaje'] : 0;
	
	$tpl->newBlock("hoja");
	$tpl->assign("numfilas",count($result));
	// Obtener catalogos de turnos y puestos
	$turno = ejecutar_script("SELECT * FROM catalogo_turnos ORDER BY cod_turno",$dsn);
	$puesto = ejecutar_script("SELECT * FROM catalogo_puestos ORDER BY cod_puestos",$dsn);
	
	$num_cia = NULL;
	$aguinaldo_ant = 0;
	$aguinaldo_nuevo = 0;
	for ($i=0; $i<count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia",$num_cia);
			$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia",$dsn);
			$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
		}
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
		$tpl->assign("id",$result[$i]['id']);
		$tpl->assign("num_emp",$result[$i]['num_emp']);
		$tpl->assign("nombre",$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']." ".$result[$i]['nombre']);
		$tpl->assign("num_afiliacion",$result[$i]['num_afiliacion'] != "" ? $result[$i]['num_afiliacion'] : "&nbsp;");
		$tpl->assign("turno",$result[$i]['turno'] != "" ? $result[$i]['turno'] : "&nbsp;");
		$tpl->assign("puesto",$result[$i]['puesto'] != "" ? $result[$i]['puesto'] : "&nbsp;");
		$tpl->assign("fecha_alta",$result[$i]['fecha_alta']);
		
		// STATUS
		if ($result[$i]['pendiente_baja'] != "") $status = "PENDIENTE BAJA [" . $result[$i]['pendiente_baja'] . "]";
		else if ($result[$i]['fecha_baja'] != "") $status = "BAJA DEFINITIVA";
		else if ($result[$i]['pendiente_alta'] != "") $status = "PENDIENTE ALTA [" . $result[$i]['pendiente_alta'] . "]";
		else if ($result[$i]['num_afiliacion'] != "") $status = "EN NOMINA";
		else if ($result[$i]['solo_aguinaldo'] == "t") $status = "SOLO AGUINALDO";
		else $status = "&nbsp;";
		$tpl->assign("status", $status);
	}
	
	$tpl->printToScreen();
	die;
}

// DATOS PARA LA BUSQUEDA
$tpl->newBlock("datos");

// Obtener catalogos de turnos y puestos
$turno = ejecutar_script("SELECT * FROM catalogo_turnos ORDER BY cod_turno",$dsn);
$puesto = ejecutar_script("SELECT * FROM catalogo_puestos ORDER BY cod_puestos",$dsn);

for ($j=0; $j<count($turno); $j++) {
	$tpl->newBlock("cod_turno");
	$tpl->assign("cod_turno",$turno[$j]['cod_turno']);
	$tpl->assign("turno",$turno[$j]['descripcion']);
}
for ($j=0; $j<count($puesto); $j++) {
	$tpl->newBlock("cod_puesto");
	$tpl->assign("cod_puesto",$puesto[$j]['cod_puestos']);
	$tpl->assign("puesto",$puesto[$j]['descripcion']);
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

// Imprimir el resultado
$tpl->printToScreen();
?>