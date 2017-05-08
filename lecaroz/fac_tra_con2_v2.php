<?php
// CONSULTA DE TRABAJADORES
// Tabla 'catalogo_trabajadores'
// Menu Proveedores y facturas -> Trabajadores

//define ('IDSCREEN',3311); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die("LO SENTIMOS, PANTALLA EN REMODELACION  ^_^");

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Funciones ---------------------------------------------------------------
function antiguedad($fecha_alta) {
	// Desglozar elementos de la fecha
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha_alta,$fecha))
		return FALSE;
	
	// Timestamp de la fecha de alta
	$ts_alta = mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]);
	// Timestamp actual
	$ts_current = /*time()*/mktime(0, 0, 0, date("n"), date("d") + 2, date("Y"));
	// Diferencia
	$diferencia = $ts_current - $ts_alta;
	// Calcular antiguedad
	$antiguedad[0] = date("Y", $diferencia) - 1970;	// Años
	$antiguedad[1] = date("n", $diferencia) - 1;	// Meses
	$antiguedad[2] = date("d", $diferencia) - 1;	// Días
	
	return $antiguedad;
}

function mostrar_antiguedad($fecha_alta) {
	// Desglozar elementos de la fecha
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha_alta,$fecha))
		return FALSE;
	
	$antiguedad = antiguedad($fecha_alta);
	
	// Construir cadena
	$cadena = "";
	$cadena .= $antiguedad[0] > 0 ? ($antiguedad[0] == 1 ? "$antiguedad[0] Año " : "$antiguedad[0] Años ") : "";
	$cadena .= $antiguedad[1] > 0 ? ($antiguedad[1] == 1 ? "$antiguedad[1] Mes " : "$antiguedad[1] Meses ") : "";
	$cadena .= $antiguedad[2] > 0 ? ($antiguedad[2] == 1 ? "$antiguedad[2] Día" : "$antiguedad[2] Días") : "";
	
	return $cadena;
}

function calcula_aguinaldo($antiguedad, $sueldo_diario) {
	if (!$antiguedad)
		return FALSE;
	
	$aguinaldo = 0;
	$vacaciones = 0;
	
	// Calculo de aguinaldo
	// Antigüedad menor o igual a 1 año
	if ($antiguedad[0] <= 1) {
		$meses = $antiguedad[0] == 1 ? 12 : $antiguedad[1];
		$aguinaldo = 0.80 * (15 / 12 * ($sueldo_diario * $meses));
	}
	// Antigüedad mayor a 1 año
	else if ($antiguedad[0] > 1)
		$aguinaldo = $sueldo_diario * 15;
	
	// Calculo de vacaciones
	// Antigüedad de mas de 1 año y menor a 2
	if ($antiguedad[0] == 1 && $antiguedad[1] > 0)
		$vacaciones = (7 + ((3 / 12) * $antiguedad[1])) * $sueldo_diario;
	// Antigüedad de mas de 2 años y menor a 3
	else if ($antiguedad[0] == 2)
		$vacaciones = (10 + ((3 / 12) * $antiguedad[1])) * $sueldo_diario;
	// Antigüedad de mas de 3 años y menor a 4
	else if ($antiguedad[0] == 3)
		$vacaciones = (12 + ((3 / 12) * $antiguedad[1])) * $sueldo_diario;
	// Antigüedad de mas de 4 años
	else if ($antiguedad[0] > 3)
		$vacaciones = (15 + (($antiguedad[0] - 4) / 5) * 3) * $sueldo_diario;
	
	$total_aguinaldo = ($aguinaldo + $vacaciones) * 1.10;
	
	return round($total_aguinaldo);
}

function nuevo_aguinaldo($antiguedad, $ultimo_aguinaldo, $incremento, $sueldo_diario) {
	$nuevo_aguinaldo['importe'] = 0;
	$nuevo_aguinaldo['tipo'] = 2;
	
	// Validar fecha de alta
	if (!$antiguedad)
		return FALSE;
	
	// Si tuvo aguinaldo anterior
	if ($ultimo_aguinaldo > 0) {
		// Calcular aguinaldo por porcentaje
		$aguinaldo_por = $ultimo_aguinaldo * (1 + $incremento / 100);
		// Calcular por antigüedad
		$aguinaldo_ant = calcula_aguinaldo($antiguedad, $sueldo_diario);
		
		// El nuevo aguinaldo sera siempre el mayor de los dos calculos
		$nuevo_aguinaldo['importe'] = $aguinaldo_por >= $aguinaldo_ant ? $aguinaldo_por : $aguinaldo_ant;
		$nuevo_aguinaldo['tipo'] = $aguinaldo_por >= $aguinaldo_ant ? 1 : 2;
	}
	// Si no ha tenido aguinaldos anteriores, calcularlo a partir de la antigüedad
	else {
		$nuevo_aguinaldo['importe'] = calcula_aguinaldo($antiguedad, $sueldo_diario);
		$nuevo_aguinaldo['tipo'] = 2;
	}
	
	return $nuevo_aguinaldo;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_con2_v2.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['id'])) {
	$fecha = date("d/m/Y");
	
	$sql = "UPDATE catalogo_trabajadores SET fecha_baja = '$fecha' WHERE id = $_GET[id]";
	$db->query($sql);
	
	if (isset($_GET['baja'])) {
		$sql = "UPDATE catalogo_trabajadores SET pendiente_baja = '$fecha', imp_baja = 'TRUE' WHERE id = $_GET[id]";
		$db->query($sql);
	}
	
	$tpl->newBlock("reload");
	$tpl->printToScreen();
	die;
}

// Actualizar datos de los empleados
if (isset($_POST['numfilas'])) {
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['fecha_alta'.$i] != "") {
			$sql = "UPDATE catalogo_trabajadores SET cod_turno = {$_POST['cod_turno'.$i]},cod_puestos = {$_POST['cod_puestos'.$i]},fecha_alta = '{$_POST['fecha_alta'.$i]}' WHERE id = {$_POST['id'.$i]}";
			$db->query($sql);
		}
	}
	
	header("location: ./fac_tra_con2_v2.php");
	die;
}

// RESULTADOS DE LA BUSQUEDA PARA LISTADO
if (isset($_GET['tipo']) && $_GET['tipo'] == "lis") {
	// Construir script SQL para la busqueda
	$sql = "SELECT id, num_emp, ap_paterno, ap_materno, nombre, num_cia, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno, fecha_alta, fecha_baja,";
	$sql .= " fecha_alta_imss, fecha_baja_imss, pendiente_alta, pendiente_baja, solo_aguinaldo, sueldo, observaciones, num_cia_emp FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING(cod_puestos)";
	$sql .= " LEFT JOIN catalogo_turnos USING(cod_turno) WHERE";
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
	if (!isset($_GET['bajas']))
		$sql .= " fecha_baja IS NULL AND";
	$sql .= " AND num_cia <= 800 ORDER BY num_cia,cod_turno,ap_paterno,ap_materno,nombre";
	$result = $db->query($sql);
	
	$tpl = new TemplatePower( "./plantillas/fac/fac_tra_lis.tpl" );
	$tpl->prepare();
	
	if (!$result) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	$fecha_ini = date("n") <= 3 ? "01/01/" . (date("Y") - 1) : date("01/01/Y");
	
	// Obtener porcentaje de incremento de aguinaldo
	$sql = "SELECT * FROM porcentaje_aguinaldo ORDER BY id DESC LIMIT 1";
	$temp = $db->query($sql);
	$incremento = $temp ? $temp[0]['porcentaje'] : 0;
	
	$numfilas_x_hoja = 40;
	
	$num_cia = NULL;
	for ($i=0; $i<count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("hoja");
			$tpl->assign("num_cia",$num_cia);
			$nombre_cia = $db->query("SELECT nombre,nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");
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
		$tpl->assign("antiguedad",$result[$i]['fecha_alta']." (".mostrar_antiguedad($result[$i]['fecha_alta']).")");
		// Obtener ultimo aguinaldo
		$sql = "SELECT importe FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha < '$fecha_ini' ORDER BY fecha DESC LIMIT 1";
		$ultimo_aguinaldo = $db->query($sql);
		$tpl->assign("ultimo_aguinaldo", $ultimo_aguinaldo ? number_format($ultimo_aguinaldo[0]['importe'], 2, ".", ",") : "&nbsp;");
		// Nuevo Aguinaldo
		$sql = "SELECT importe, tipo FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha > '$fecha_ini' ORDER BY fecha DESC LIMIT 1";
		$nuevo_aguinaldo = $db->query($sql);
		$tpl->assign("nuevo_aguinaldo", $nuevo_aguinaldo ? "<font color=\"#" . ($nuevo_aguinaldo[0]['tipo'] == 1 ? "FF0000" : ($nuevo_aguinaldo[0]['tipo'] == 2 ? "0000FF" : "00FF00")) . "\">" . number_format($nuevo_aguinaldo[0]['importe'], 2, ".", "") . "</font>" : "&nbsp;");
	}
	$tpl->printToScreen();
	die;
}

// RESULTADOS DE LA BUSQUEDA PARA MODIFICACION
if (isset($_GET['tipo']) && $_GET['tipo'] == "mod") {
	// Criterios de ordenacion
	$ord = array();
	foreach ($_GET['criterio_orden'] as $value)
		if ($value != "") $ord[] = $value;
	
	// Construir script SQL para la busqueda
	//$sql = "SELECT id,num_emp,ap_paterno,ap_materno,nombre,num_cia,catalogo_puestos.descripcion AS puesto,catalogo_turnos.descripcion AS turno,fecha_alta,solo_aguinaldo FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING(cod_puestos) LEFT JOIN catalogo_turnos USING(cod_turno)";
	$sql = "SELECT id, num_emp, ap_paterno, ap_materno, nombre, num_cia, cod_turno, cod_puestos, fecha_alta, fecha_baja, fecha_alta_imss, fecha_baja_imss, pendiente_alta, pendiente_baja, solo_aguinaldo, credito_infonavit, num_afiliacion, observaciones, CASE WHEN num_cia_emp != num_cia THEN 1 ELSE 0 END AS otra_cia, tipo FROM catalogo_trabajadores LEFT JOIN catalogo_turnos USING (cod_turno) LEFT JOIN catalogo_puestos USING (cod_puestos)";
	if ($_GET['num_emp'] > 0 || $_GET['num_cia'] > 0 || $_GET['ap_paterno'] != "" || $_GET['ap_materno'] != "" || $_GET['nombre'] != "" || $_GET['turno'] > 0 || $_GET['puesto'] > 0)
		$sql .= " WHERE num_cia BETWEEN " . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 899') . ' AND';
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
	if ($_GET['turno'] > 0) {
		$sql .= $_GET['num_emp'] > 0 || $_GET['num_cia'] > 0 || $_GET['ap_paterno'] != "" || $_GET['ap_materno'] != "" || $_GET['nombre'] != "" ? " AND" : "";
		$sql .= " cod_turno = $_GET[turno]";
	}
	if ($_GET['puesto'] > 0) {
		$sql .= $_GET['num_emp'] > 0 || $_GET['num_cia'] > 0 || $_GET['ap_paterno'] != "" || $_GET['ap_materno'] != "" || $_GET['nombre'] != "" || $_GET['turno'] > 0 ? " AND" : "";
		$sql .= " cod_puestos = $_GET[puesto]";
	}
	
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
		$sql = "($sql) UNION ($sql_bajas) ORDER BY ";
		for ($i = 0; $i < count($ord); $i++)
			$sql .= $ord[$i] . ($i < count($ord) - 1 ? ", " : "");
	}
	else {
		$sql .= " ORDER BY ";
		for ($i = 0; $i < count($ord); $i++)
			$sql .= $ord[$i] . ($i < count($ord) - 1 ? ", " : "");
	}
	//echo $sql;
	$result = $db->query($sql);
		
	if (!$result) {
		header("location: ./fac_tra_con2_v2.php?codigo_error=1");
		die;
	}
	
	// Obtener porcentaje de incremento de aguinaldo
	$sql = "SELECT * FROM porcentaje_aguinaldo ORDER BY id DESC LIMIT 1";
	$temp = $db->query($sql);
	$incremento = $temp ? $temp[0]['porcentaje'] : 0;
	
	$tpl->newBlock("hoja");
	$tpl->assign("numfilas",count($result));
	// Obtener catalogos de turnos y puestos
	$turno = $db->query("SELECT * FROM catalogo_turnos ORDER BY cod_turno");
	$puesto = $db->query("SELECT * FROM catalogo_puestos ORDER BY cod_puestos");
	
	function buscar($array, $dato, $campo) {
		for ($i = 0; $i < count($array); $i++)
			if ($array[$i][$campo] == $dato)
				return $i;
		
		return FALSE;
	}
	
	$fecha_ini = date("n") <= 3 ? "01/01/" . (date("Y") - 1) : date("01/01/Y");
	
	$num_cia = NULL;
	$aguinaldo_ant = 0;
	$aguinaldo_nuevo = 0;
	for ($i=0; $i<count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia",$num_cia);
			$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
			$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
			$tpl->assign("anio_ant",date("n") <= 3 ? date("Y")-2 : date("Y")-1);
			$tpl->assign("anio_act",date("n") <= 3 ? date("Y")-1 : date("Y"));
		}
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
		$tpl->assign("id",$result[$i]['id']);
		$tpl->assign('bgcolor', strlen(trim($result[$i]['observaciones'])) > 0 ? '#FFFF00' : '');
		$tpl->assign("num_emp",$result[$i]['num_emp']);
		$tpl->assign("nombre",$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']." ".$result[$i]['nombre']);
		$tpl->assign('otra_cia_color', $result[$i]['otra_cia'] == 1 ? ' style="color:#C00;"' : '');
		$tpl->assign("num_afiliacion",$result[$i]['num_afiliacion'] != "" ? $result[$i]['num_afiliacion'] : "&nbsp;");
		
		// [17-Dic-2008] Mostrar el tipo de calculo que se hara al aguinaldo: 0=calculo normal, 1=a 1 año, 2=a 3 meses
		switch ($result[$i]['tipo']) {
			case 0: $tpl->assign('tipo', ''); break;
			case 1: $tpl->assign('tipo', 'A'); break;
			case 2: $tpl->assign('tipo', 'F'); break;
		}
		
		// Antigüedad
		if ($antiguedad = antiguedad($result[$i]['fecha_alta']))
			$tpl->assign("antiguedad", ($antiguedad[0] > 0 ? "$antiguedad[0] A " : "") . ($antiguedad[1] > 0 ? "$antiguedad[1] M " : ""));
		
		// Obtener ultimo aguinaldo
		$sql = "SELECT importe FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha < '$fecha_ini' ORDER BY fecha DESC LIMIT 1";
		$ultimo_aguinaldo = $db->query($sql);
		$tpl->assign("ultimo_aguinaldo", $ultimo_aguinaldo ? number_format($ultimo_aguinaldo[0]['importe'], 2, ".", ",") : "");
		$aguinaldo_ant += $ultimo_aguinaldo[0]['importe'];
		
		// Verificar si tiene prestamos
		$sql = "SELECT id FROM prestamos WHERE id_empleado = {$result[$i]['id']} AND pagado = 'FALSE' LIMIT 1";
		$prestamo = $db->query($sql) ? TRUE : FALSE;
		$tpl->assign("pre",$prestamo ? "true" : "false");
		
		// Verificar si tiene seguro
		$tpl->assign("imss",$result[$i]['num_afiliacion'] != "" ? "true" : "false");
		
		// Verificar si tiene crédito infonavit
		$tpl->assign("inf",$result[$i]['credito_infonavit'] == "t" ? "true" : "false");
		
		// Deshabilitar boton de baja si el empleado ya tiene este estatus
		$tpl->assign("disabled",$result[$i]['fecha_baja'] != "" ? "disabled" : "");
		
		// [28-Nov-2007] 
		
		// STATUS
		if ($result[$i]['pendiente_baja'] != "") $status = "PENDIENTE BAJA";
		else if ($result[$i]['fecha_baja'] != "") $status = "BAJA";
		else if ($result[$i]['pendiente_alta'] != "") $status = "PENDIENTE ALTA";
		else if ($result[$i]['num_afiliacion'] != "") $status = "IMSS";
		else if ($result[$i]['solo_aguinaldo'] == "t") $status = "SOLO AGUINALDO";
		else $status = "&nbsp;";
		$tpl->assign("status",$status);
		
		$tpl->assign("ag", $result[$i]['solo_aguinaldo'] == "t" ? "&sect;" : "");
		
		// Nuevo Aguinaldo
		$sql = "SELECT id, importe FROM aguinaldos WHERE id_empleado = {$result[$i]['id']} AND fecha > '$fecha_ini' ORDER BY fecha DESC LIMIT 1";
		$nuevo_aguinaldo = $db->query($sql);
		$tpl->assign("idaguinaldo", $nuevo_aguinaldo ? $nuevo_aguinaldo[0]['id'] : "");
		$tpl->assign("anio_act", date("n") <= 3 ? date("Y") - 1 : date("Y"));
		$tpl->assign("aguinaldo", $nuevo_aguinaldo ? number_format($nuevo_aguinaldo[0]['importe'], 2, ".", ",") : "&nbsp;");
		$aguinaldo_nuevo += $nuevo_aguinaldo[0]['importe'];
		
		$tpl->assign("puesto", $puesto[buscar($puesto, $result[$i]['cod_puestos'], "cod_puestos")]['descripcion']);
		$tpl->assign("turno", $turno[buscar($turno, $result[$i]['cod_turno'], "cod_turno")]['descripcion']);
	}
	$tpl->assign("cia.ultimo_aguinaldo",number_format($aguinaldo_ant,2,".",","));
	$tpl->assign("cia.total_aguinaldo",number_format($aguinaldo_nuevo,2,".",""));
	
	$tpl->printToScreen();
	die;
}

// DATOS PARA LA BUSQUEDA
$tpl->newBlock("datos");

if (!in_array($_SESSION['iduser'], array(1, 4, 25)))
	$tpl->assign("disabled","disabled");

// Obtener catalogos de turnos y puestos
$turno = $db->query("SELECT * FROM catalogo_turnos ORDER BY cod_turno");
$puesto = $db->query("SELECT * FROM catalogo_puestos ORDER BY cod_puestos");

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