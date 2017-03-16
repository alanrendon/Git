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

function calcula_aguinaldo($fecha_alta,$ultimo_aguinaldo,$incremento/*,$salario*/) {
	$nuevo_aguinaldo = 0;
	
	// Validar fecha de alta
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha_alta,$fecha))
		return FALSE;
	
	// Si tuvo aguinaldo anterior
	if ($ultimo_aguinaldo > 0) {
		// Calcular por porcentaje
		$aguinaldo_por = $ultimo_aguinaldo * (1 + $incremento / 100);
		// Calcular por antigüedad (PENDIENTE)
		$aguinaldo_ant = 0;
		
		// El nuevo aguinaldo sera siempre el mayor de los dos calculos
		$nuevo_aguinaldo = $aguinaldo_por >= $aguinaldo_ant ? $aguinaldo_por : $aguinaldo_ant;
	}
	// Si no ha tenido aguinaldos anteriores, calcularlo a partir de la antigüedad (PENDIENTE)
	else {
		$nuevo_aguinaldo = 0;
	}
	
	return $nuevo_aguinaldo;
}

if (isset($_GET['id'])) {
	$fecha = date("d/m/Y");
	
	$sql = "UPDATE catalogo_trabajadores SET fecha_baja = '$fecha' WHERE id = $_GET[id]";
	ejecutar_script($sql,$dsn);
	
	if (isset($_GET['baja'])) {
		$sql = "UPDATE catalogo_trabajadores SET baja_pendiente = '$fecha', imp_baja = 'TRUE' WHERE id = $_GET[id]";
		ejecutar_script($sql,$dsn);
	}
	
	$tpl->newBlock("reload");
	$tpl->printToScreen();
	die;
}

// Actualizar datos de los empleados
if (isset($_POST['numfilas'])) {
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['fecha_alta'.$i] != "") {
			$sql = "UPDATE catalogo_trabajadores SET cod_turno = ".$_POST['cod_turno'.$i].",cod_puestos = ".$_POST['cod_puestos'.$i].",fecha_alta = '".$_POST['fecha_alta'.$i]."' WHERE id = ".$_POST['id'.$i];
			ejecutar_script($sql,$dsn);
		}
	}
	
	header("location: ./fac_tra_con2.php");
	die;
}

// RESULTADOS DE LA BUSQUEDA PARA LISTADO
if (isset($_GET['tipo']) && $_GET['tipo'] == "lis") {
	// Construir script SQL para la busqueda
	$sql = "SELECT id,num_emp,ap_paterno,ap_materno,nombre,num_cia,catalogo_puestos.descripcion AS puesto,catalogo_turnos.descripcion AS turno,fecha_alta,fecha_baja,fecha_alta_imss,fecha_baja_imss,pendiente_alta,pendiente_baja,solo_aguinaldo,salario FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING(cod_puestos) LEFT JOIN catalogo_turnos USING(cod_turno) WHERE";
	//$sql = "SELECT id,num_emp,ap_paterno,ap_materno,nombre,num_cia,cod_turno,cod_puestos,fecha_alta,solo_aguinaldo FROM catalogo_trabajadores";
	if ($_GET['num_emp'] > 0)
		$sql .= " num_emp = $_GET[num_emp] AND";
	else {
		if ($_GET['num_cia'] > 0) {
			$sql .= " num_cia = $_GET[num_cia] AND";
			if ($_GET['ap_paterno'] != "" || $_GET['ap_materno'] != "" || $_GET['nombre'] != "")
				$sql .= " AND";
		}
		if ($_GET['ap_paterno'] != "") {
			$sql .= " ap_paterno LIKE '%".strtoupper($_GET['ap_paterno'])."%'";
			if ($_GET['ap_materno'] != "" || $_GET['nombre'] != "")
				$sql .= " AND";
		}
		if ($_GET['ap_materno'] != "") {
			$sql .= " ap_materno LIKE '%".strtoupper($_GET['ap_materno'])."%'";
			if ($_GET['nombre'] != "")
				$sql .= " AND";
		}
		if ($_GET['nombre'] != "") {
			$sql .= " nombre LIKE '%".strtoupper($_GET['nombre'])."%' AND";
		}
	}
	if ($_GET['filtro'] == "imss")
		$sql .= " num_afiliacion IS NOT NULL AND";
	if ($_GET['filtro'] == "no_imss")
		$sql .= " num_afiliacion IS NULL AND";
	if ($_GET['filtro'] == "solo_aguinaldo")
		$sql .= " solo_aguinaldo = 'TRUE' AND";
	$sql .= " fecha_baja IS NULL ORDER BY num_cia,cod_turno,ap_paterno,ap_materno,nombre";
	$result = ejecutar_script($sql,$dsn);
	
	$tpl = new TemplatePower( "./plantillas/fac/fac_tra_lis.tpl" );
	$tpl->prepare();
	
	if (!$result) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	// Obtener porcentaje de incremento de aguinaldo
	$sql = "SELECT * FROM porcentaje_aguinaldo ORDER BY id DESC LIMIT 1";
	$temp = ejecutar_script($sql,$dsn);
	$incremento = $temp ? $temp[0]['porcentaje'] : 0;
	
	$numfilas_x_hoja = 45;
	
	$num_cia = NULL;
	for ($i=0; $i<count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("hoja");
			$tpl->assign("num_cia",$num_cia);
			$nombre_cia = ejecutar_script("SELECT nombre,nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia",$dsn);
			$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
			$tpl->assign("nombre_corto",$nombre_cia[0]['nombre_corto']);
			$tpl->assign("dia",(int)date("d"));
			$tpl->assign("mes",mes_escrito(date("n")));
			$tpl->assign("anio",date("Y"));
			$numfilas = 0;
		}
		if ($numfilas == $numfilas_x_hoja) {
			$tpl->newBlock("hoja");
			$tpl->assign("num_cia",$num_cia);
			$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
			$tpl->assign("nombre_corto",$nombre_cia[0]['nombre_corto']);
			$tpl->assign("dia",(int)date("d"));
			$tpl->assign("mes",mes_escrito(date("n")));
			$tpl->assign("anio",date("Y"));
			$numfilas = 0;
		}
		
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
		$tpl->assign("id",$result[$i]['id']);
		$tpl->assign("num_emp",$result[$i]['num_emp']);
		$tpl->assign("nombre",$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']." ".$result[$i]['nombre']);
		$tpl->assign("turno",$result[$i]['turno'] != "" ? $result[$i]['turno'] : "&nbsp;");
		$tpl->assign("puesto",$result[$i]['puesto'] != "" ? $result[$i]['puesto'] : "&nbsp;");
		$tpl->assign("solo_aguinaldo",$result[$i]['solo_aguinaldo'] == "t" ? "SOLO AGUINALDO" : "&nbsp;");
		$tpl->assign("antiguedad",$result[$i]['fecha_alta']." (".calcula_antiguedad($result[$i]['fecha_alta']).")");
		// Obtener ultimo aguinaldo
		$sql = "SELECT importe FROM aguinaldos WHERE id_empleado = ".$result[$i]['id']." ORDER BY fecha DESC LIMIT 1";
		$ultimo_aguinaldo = ejecutar_script($sql,$dsn);
		$tpl->assign("ultimo_aguinaldo",$ultimo_aguinaldo ? number_format($ultimo_aguinaldo[0]['importe'],2,".",",") : "&nbsp;");
		// Nuevo Aguinaldo (PENDIENTE)
		//$nuevo_aguinaldo = calcula_aguinaldo($result[$i]['fecha_alta'],$ultimo_aguinaldo ? $ultimo_aguinaldo[0]['importe'] : 0, $incremento);
		//$tpl->assign("nuevo_aguinaldo",$nuevo_aguinaldo ? number_format($nuevo_aguinaldo,2,".",",") : "&nbsp;");
		$tpl->assign("nuevo_aguinaldo","&nbsp;");
		$numfilas++;
	}
	$tpl->printToScreen();
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_con2.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// RESULTADOS DE LA BUSQUEDA PARA MODIFICACION
if (isset($_GET['tipo']) && $_GET['tipo'] == "mod") {
	// Construir script SQL para la busqueda
	//$sql = "SELECT id,num_emp,ap_paterno,ap_materno,nombre,num_cia,catalogo_puestos.descripcion AS puesto,catalogo_turnos.descripcion AS turno,fecha_alta,solo_aguinaldo FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING(cod_puestos) LEFT JOIN catalogo_turnos USING(cod_turno)";
	$sql = "SELECT id,num_emp,ap_paterno,ap_materno,nombre,num_cia,cod_turno,cod_puestos,fecha_alta,fecha_baja,fecha_alta_imss,fecha_baja_imss,pendiente_alta,pendiente_baja,solo_aguinaldo,credito_infonavit,num_afiliacion FROM catalogo_trabajadores";
	if ($_GET['num_emp'] > 0 || $_GET['num_cia'] > 0 || $_GET['ap_paterno'] != "" || $_GET['ap_materno'] != "" || $_GET['nombre'] != "")
		$sql .= " WHERE";
	if ($_GET['num_emp'] > 0)
		$sql .= " num_emp = $_GET[num_emp]";
	else {
		if ($_GET['num_cia'] > 0) {
			$sql .= " num_cia = $_GET[num_cia]";
			if ($_GET['ap_paterno'] != "" || $_GET['ap_materno'] != "" || $_GET['nombre'] != "")
				$sql .= " AND";
		}
		if ($_GET['ap_paterno'] != "") {
			$sql .= " ap_paterno LIKE '%".strtoupper($_GET['ap_paterno'])."%'";
			if ($_GET['ap_materno'] != "" || $_GET['nombre'] != "")
				$sql .= " AND";
		}
		if ($_GET['ap_materno'] != "") {
			$sql .= " ap_materno LIKE '%".strtoupper($_GET['ap_materno'])."%'";
			if ($_GET['nombre'] != "")
				$sql .= " AND";
		}
		if ($_GET['nombre'] != "") {
			$sql .= " nombre LIKE '%".strtoupper($_GET['nombre'])."%'";
		}
	}
	if ($_GET['turno'] > 0)
		$sql .= " AND cod_turno = $_GET[turno]";
	if ($_GET['puesto'] > 0)
		$sql .= " AND cod_puestos = $_GET[puesto]";
	
	$sql_bajas = $sql;
	
	if ($_GET['filtro'] == "imss")
		$sql .= " AND num_afiliacion IS NOT NULL";
	if ($_GET['filtro'] == "no_imss")
		$sql .= " AND num_afiliacion IS NULL";
	if ($_GET['filtro'] == "solo_aguinaldo")
		$sql .= " AND solo_aguinaldo = 'TRUE'";
	if ($_GET['filtro'] == "pen_altas")
		$sql .= " AND pendiente_alta IS NOT NULL";
	if ($_GET['filtro'] == "pen_bajas")
		$sql .= " AND pendiente_baja IS NOT NULL";
	
	if ($_GET['filtro'] != "pen_bajas")
		$sql .= " AND fecha_baja IS NULL";
	
	if (isset($_GET['bajas']) && $_GET['filtro'] == "todos") {
		$sql_bajas .= " AND fecha_baja >= CURRENT_DATE - INTERVAL '4 months'";
		$sql = "($sql) UNION ($sql_bajas) ORDER BY num_cia,ap_paterno,ap_materno,nombre";
	}
	else
		$sql .= " ORDER BY num_cia,ap_paterno,ap_materno,nombre";
	//echo $sql;
	$result = ejecutar_script($sql,$dsn);
		
	if (!$result) {
		header("location: ./fac_tra_con2.php?codigo_error=1");
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
			$tpl->assign("anio_ant",date("Y")-1);
			$tpl->assign("anio_act",date("Y"));
		}
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
		$tpl->assign("id",$result[$i]['id']);
		$tpl->assign("num_emp",$result[$i]['num_emp']);
		$tpl->assign("nombre",$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']." ".$result[$i]['nombre']);
		$tpl->assign("num_afiliacion",$result[$i]['num_afiliacion'] != "" ? $result[$i]['num_afiliacion'] : "&nbsp;");
		//$tpl->assign("turno",$result[$i]['turno'] != "" ? $result[$i]['turno'] : "&nbsp;");
		//$tpl->assign("puesto",$result[$i]['puesto'] != "" ? $result[$i]['puesto'] : "&nbsp;");
		//$tpl->assign("solo_aguinaldo",$result[$i]['solo_aguinaldo'] == "t" ? "SOLO AGUINALDO" : "&nbsp;");
		//$tpl->assign("antiguedad",$result[$i]['fecha_alta']." (".calcula_antiguedad($result[$i]['fecha_alta']).")");
		$tpl->assign("fecha_alta",$result[$i]['fecha_alta']);
		// Obtener ultimo aguinaldo
		$sql = "SELECT importe FROM aguinaldos WHERE id_empleado = ".$result[$i]['id']." ORDER BY fecha DESC LIMIT 1";
		$ultimo_aguinaldo = ejecutar_script($sql,$dsn);
		$tpl->assign("ultimo_aguinaldo",$ultimo_aguinaldo ? number_format($ultimo_aguinaldo[0]['importe'],2,".",",") : "&nbsp;");
		$aguinaldo_ant += $ultimo_aguinaldo[0]['importe'];
		
		// Verificar si tiene prestamos
		$sql = "SELECT id FROM prestamos WHERE id_empleado = {$result[$i]['id']} AND pagado = 'FALSE' LIMIT 1";
		$prestamo = ejecutar_script($sql,$dsn) ? TRUE : FALSE;
		$tpl->assign("pre",$prestamo ? "true" : "false");
		
		// Verificar si tiene seguro
		$tpl->assign("imss",$result[$i]['num_afiliacion'] != "" ? "true" : "false");
		
		// Verificar si tiene crédito infonavit
		$tpl->assign("inf",$result[$i]['credito_infonavit'] == "t" ? "true" : "false");
		
		// Deshabilitar boton de baja si el empleado ya tiene este estatus
		$tpl->assign("disabled",$result[$i]['fecha_baja'] != "" ? "disabled" : "");
		
		// STATUS
		if ($result[$i]['pendiente_baja'] != "") $status = "PENDIENTE BAJA";
		else if ($result[$i]['fecha_baja'] != "") $status = "BAJA";
		else if ($result[$i]['pendiente_alta'] != "") $status = "PENDIENTE ALTA";
		else if (/*$result[$i]['fecha_alta']*/$result[$i]['num_afiliacion'] != "") $status = "IMSS";
		else if ($result[$i]['solo_aguinaldo'] == "t") $status = "SOLO AGUINALDO";
		else $status = "&nbsp;";
		$tpl->assign("status",$status);
		
		// Nuevo Aguinaldo (PENDIENTE)
		//$nuevo_aguinaldo = calcula_aguinaldo($result[$i]['fecha_alta'],$ultimo_aguinaldo ? $ultimo_aguinaldo[0]['importe'] : 0, $incremento);
		//$tpl->assign("nuevo_aguinaldo",$nuevo_aguinaldo ? number_format($nuevo_aguinaldo,2,".",",") : "&nbsp;");
		$tpl->assign("nuevo_aguinaldo","&nbsp;");
		for ($j=0; $j<count($turno); $j++) {
			$tpl->newBlock("turno");
			$tpl->assign("id",$turno[$j]['cod_turno']);
			$tpl->assign("nombre",$turno[$j]['descripcion']);
			if ($turno[$j]['cod_turno'] == $result[$i]['cod_turno']) $tpl->assign("selected"," selected");
		}
		for ($j=0; $j<count($puesto); $j++) {
			$tpl->newBlock("puesto");
			$tpl->assign("id",$puesto[$j]['cod_puestos']);
			$tpl->assign("nombre",$puesto[$j]['descripcion']);
			if ($puesto[$j]['cod_puestos'] == $result[$i]['cod_puestos']) $tpl->assign("selected"," selected");
		}
	}
	$tpl->assign("cia.ultimo_aguinaldo",number_format($aguinaldo_ant,2,".",","));
	
	$tpl->printToScreen();
	die;
}

// DATOS PARA LA BUSQUEDA
$tpl->newBlock("datos");

if ($_SESSION['iduser'] != 0 && $_SESSION['iduser'] != 1 && $_SESSION['iduser'] != 4 && $_SESSION['iduser'] != 25)
	$tpl->assign("disabled","disabled");

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