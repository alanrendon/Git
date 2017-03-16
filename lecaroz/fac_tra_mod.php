<?php
// MODIFICACION DE TRABAJADORES
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
//if ($_SESSION['iduser'] != 1) die("MODIFICANDO LA PANTALLA... GOMEN ^_^");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_mod.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// ************************** INSERTAR DATOS *******************************
// MODIFICACION EN LA BASE DE DATOS
if (isset($_POST['id'])) {
	// Si no hay cambio de compañía, solo cambiar los datos
	if ($_POST['num_cia'] == $_POST['num_cia_ant']) {
		$datos = $_POST;
		
		// Obtener número de empleado disponible para la compañía
		$datos['nombre_completo'] = "";
		$datos['solo_aguinaldo'] = isset($_POST['solo_aguinaldo']) ? "TRUE" : "FALSE";
		$datos['fecha_baja'] = "";
		$datos['fecha_baja_imss'] = "";
		$datos['imp_alta'] = isset($_POST['imp_alta']) && $_POST['num_afiliacion'] != "" ? "TRUE" : "FALSE";
		$datos['imp_baja'] = "FALSE";
		$datos['pendiente_alta'] = $_POST['num_afiliacion'] != "" && isset($_POST['imp_alta']) ? date("d/m/Y") : "";
		$datos['pendiente_baja'] = "";
		$datos['ultimo'] = "FALSE";
		$datos['no_baja'] = isset($_POST['no_baja']) ? "TRUE" : "FALSE";
		$datos['observaciones'] = strtoupper(substr(trim($_POST['observaciones']), 0, 255));
		$datos['num_cia_emp'] = $_POST['num_cia_emp'] > 0 ? $_POST['num_cia_emp'] : $_POST['num_cia'];
		$datos['control_bata'] = isset($_POST['control_bata']) ? 'TRUE' : 'FALSE';
		$datos['deposito_bata'] = $_POST['deposito_bata'] > 0 ? $_POST['deposito_bata'] : '0';
		// [4-Jul-2008] Actualizar para zapaterias la fecha de alta del imss
		if ($_SESSION['tipo_usuario'] == 2)
			$datos['fecha_alta_imms'] = $datos['fecha_alta'];
		
		$db = new DBclass($dsn,"catalogo_trabajadores",$datos);
		$db->generar_script_update("",array("id"),array($_POST['id']));//echo '<pre>' . print_r($datos, TRUE) . $db->sql . '</pre>';die;
		$db->ejecutar_script();
		
		$sql = "UPDATE catalogo_trabajadores SET nombre_completo = ap_paterno || (CASE WHEN ap_materno IS NOT NULL AND TRIM(ap_materno) <> '' THEN ' ' || TRIM(ap_materno) ELSE '' END) || (CASE WHEN nombre IS NOT NULL AND TRIM(nombre) <> '' THEN ' ' || TRIM(nombre) ELSE '' END) WHERE id = {$_POST['id']}";
		ejecutar_script($sql, $dsn);
		
		header("location: ./fac_tra_mod.php");
		die;
	}
	// Si cambio de compañía, poner en pendientes de baja del imss y cambiar todos los prestamos del empleado de compañía, e insertar un nuevo registro del empleado
	else {
		$datos = $_POST;
		$fecha = date("d/m/Y");
		$status_baja = $_POST['num_afiliacion_ant'] != "" ? "TRUE" : "FALSE";
		$prestamo = "FALSE";
		
		// Insertar registro del empleado
		$datos['nombre_completo'] = "";
		$datos['solo_aguinaldo'] = isset($_POST['solo_aguinaldo']) ? "TRUE" : "FALSE";
		$datos['fecha_alta_imss'] = "";
		$datos['fecha_baja'] = "";
		$datos['fecha_baja_imss'] = "";
		$datos['imp_alta'] = $_POST['num_afiliacion'] != "" ? "TRUE" : "FALSE";
		$datos['imp_baja'] = "FALSE";
		$datos['pendiente_alta'] = $_POST['num_afiliacion'] != "" ? date("d/m/Y") : "";
		$datos['pendiente_baja'] = "";
		$datos['ultimo'] = "FALSE";
		$datos['no_baja'] = isset($_POST['no_baja']) ? "TRUE" : "FALSE";
		$datos['observaciones'] = strtoupper(substr(trim($_POST['observaciones']), 0, 255));
		$datos['num_cia_emp'] = $_POST['num_cia_emp'] > 0 ? $_POST['num_cia_emp'] : $_POST['num_cia'];
		$datos['control_bata'] = isset($_POST['control_bata']) ? 'TRUE' : 'FALSE';
		$datos['deposito_bata'] = $_POST['deposito_bata'] > 0 ? $_POST['deposito_bata'] : '0';
		
		$db = new DBclass($dsn,"catalogo_trabajadores",$datos);
		$db->generar_script_insert("");
		$db->ejecutar_script();
		
		// Actualizar registro anterior del empleado
		$sql = "UPDATE catalogo_trabajadores SET fecha_baja = '$fecha',imp_baja = '$status_baja',pendiente_baja = ".($status_baja == "TRUE" ? "'$fecha'" : "NULL")." WHERE id = $_POST[id]";
		ejecutar_script($sql,$dsn);
		
		// Obtener nuevo ID del empleado
		$sql = "SELECT id FROM catalogo_trabajadores WHERE num_emp = $datos[num_emp] and num_cia = $datos[num_cia]";
		$id = ejecutar_script($sql,$dsn);
		
		$sql = "UPDATE catalogo_trabajadores SET nombre_completo = ap_paterno || (CASE WHEN ap_materno IS NOT NULL AND TRIM(ap_materno) <> '' THEN ' ' || TRIM(ap_materno) ELSE '' END) || (CASE WHEN nombre IS NOT NULL AND TRIM(nombre) <> '' THEN ' ' || TRIM(nombre) ELSE '' END) WHERE id = {$id[0]['id']}";
		ejecutar_script($sql, $dsn);
		
		// Verificar ultimo aguinaldo
		$sql = "UPDATE aguinaldos SET id_empleado = {$id[0]['id']} WHERE id_empleado = $_POST[id]";
		ejecutar_script($sql, $dsn);
		
		// Verificar si el empleado tiene prestamos pendientes (YA NO SE VA A HACER)
		/*$sql = "SELECT * FROM prestamos WHERE num_cia = $_POST[num_cia_ant] AND id_empleado = $_POST[id] AND pagado = 'FALSE'";
		if ($adeudo = ejecutar_script($sql,$dsn)) {
			// Obtener saldo de prestamos y meterlo en la otra compañia
			$sql = "SELECT SUM(importe) FROM prestamos WHERE id_empleado = $_POST[id] AND pagado = 'FALSE' AND tipo_mov = 'FALSE'";
			$pre = ejecutar_script($sql,$dsn);
			$sql = "SELECT SUM(importe) FROM prestamos WHERE id_empleado = $_POST[id] AND pagado = 'FALSE' AND tipo_mov = 'TRUE'";
			$abo = ejecutar_script($sql,$dsn);
			$saldo = $pre[0]['sum'] - $abo[0]['sum'];
			
			// Insertar registro de prestamo y gasto
			$sql = "INSERT INTO prestamos (num_cia,fecha,importe,tipo_mov,pagado,id_empleado) VALUES ($datos[num_cia],CURRENT_DATE,$saldo,'FALSE','FALSE',{$id[0]['id']})";
			ejecutar_script($sql,$dsn);
			$sql = "INSERT INTO movimiento_gastos (codgastos,num_cia,fecha,importe,concepto,captura) VALUES (41,$datos[num_cia],CURRENT_DATE,$saldo,'TRASPASO PRESTAMO','FALSE')";
			ejecutar_script($sql,$dsn);
			// Actualizar efectivo del dia
			
			// Actualizar número de compañía para los prestamos del empleado
			$sql = "UPDATE prestamos SET num_cia = $_POST[num_cia] WHERE num_cia = $_POST[num_cia_ant] AND id_empleado = {$id[0]['id']} AND pagado = 'FALSE'";
			ejecutar_script($sql,$dsn);
			$prestamo = "TRUE";
		}*/
		
		// Verificar si el empleado tiene credito infonavit
		if ($inf = ejecutar_script("SELECT id FROM infonavit WHERE id_emp = $_POST[id] LIMIT 1",$dsn)) {
			$sql = "UPDATE infonavit SET id_emp = {$id[0]['id']} WHERE id_emp = $_POST[id]";
			ejecutar_script($sql,$dsn);
		}
		
		header("location: ./fac_tra_mod.php?cambio=1&prestamo=$prestamo");
		die;
	}
}
// MODIFICACION EN PANTALLA
if (isset($_GET['id'])) {
	$tpl->newBlock("modificar");
	// Obtener datos
	$sql = "SELECT * FROM catalogo_trabajadores WHERE id = $_GET[id]";
	$result = ejecutar_script($sql,$dsn);
	
	$tpl->assign("id",$result[0]['id']);
	$tpl->assign("num_cia",$result[0]['num_cia']);
	$nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = ".$result[0]['num_cia'],$dsn);
	$nombre_cia_emp = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = ".$result[0]['num_cia_emp'],$dsn);
	
	// Verificar si el empleado tiene prestamos
	$sql = "SELECT * FROM prestamos WHERE id_empleado = $_GET[id] AND pagado = 'FALSE' LIMIT 1";
	if ($adeudo = ejecutar_script($sql,$dsn)) {
		$tpl->assign("readonly","readonly");
		$tpl->assign("bgcolor","bgcolor=\"#FFFF00\"");
		$tpl->assign("mensaje","El empleado tiene prestamos, no puede ser cambiado de compañía");
	}
	else
		$tpl->assign("mensaje","&nbsp;");
	
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
	$tpl->assign("num_cia_emp",$result[0]['num_cia_emp']);
	$tpl->assign("nombre_cia",$nombre_cia_emp[0]['nombre_corto']);
	$tpl->assign("nombre",$result[0]['nombre']);
	$tpl->assign("ap_paterno",$result[0]['ap_paterno']);
	$tpl->assign("ap_materno",$result[0]['ap_materno']);
	$tpl->assign("fecha_nac",$result[0]['fecha_nac']);
	$tpl->assign("lugar_nac",$result[0]['lugar_nac']);
	$tpl->assign($result[0]['sexo'] == "t" ? "sexo_true" : "sexo_false", "checked");
	$tpl->assign("rfc",$result[0]['rfc']);
	$tpl->assign("calle",$result[0]['calle']);
	$tpl->assign("colonia",$result[0]['colonia']);
	$tpl->assign("cod_postal",$result[0]['cod_postal']);
	$tpl->assign("del_mun",$result[0]['del_mun']);
	$tpl->assign("entidad",$result[0]['entidad']);
	$tpl->assign("num_afiliacion",$result[0]['num_afiliacion']);
	$tpl->assign("salario",number_format($result[0]['salario'],2,".",""));
	$tpl->assign('salario_integrado', number_format($result[0]['salario_integrado'],2,".",""));
	$tpl->assign("num_emp",$result[0]['num_emp']);
	$tpl->assign("fecha_alta",$result[0]['fecha_alta']);
	$tpl->assign("fecha_alt_imss",$result[0]['fecha_alta_imss']);
	if ($result[0]['solo_aguinaldo'] == "t") $tpl->assign("aguinaldo_checked","checked");
	if ($result[0]['imp_alta'] == "t") $tpl->assign("carta_checked","checked");
	$tpl->assign($result[0]['credito_infonavit'] == "t"?"infonavit_true":"infonavit_false","checked");
	$tpl->assign("no_baja_checked", $result[0]['no_baja'] == "t" ? "checked" : "");
	$tpl->assign("observaciones", $result[0]['observaciones']);
	$tpl->assign('tipo_' . $result[0]['tipo'], ' selected');
	$tpl->assign('uniforme', $result[0]['uniforme']);
	$tpl->assign('talla_' . $result[0]['talla'], ' selected');
	$tpl->assign('control_bata_checked', $result[0]['control_bata'] == 't' ? ' checked' : '');
	$tpl->assign('deposito_bata', $result[0]['deposito_bata'] > 0 ? number_format($result[0]['deposito_bata'], 2, '.', '') : '');
	
	$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " ORDER BY num_cia",$dsn);
	$puesto = ejecutar_script("SELECT cod_puestos,descripcion FROM catalogo_puestos WHERE giro = " . ($_SESSION['tipo_usuario'] == 2 ? '2' : '1') . " ORDER BY cod_puestos",$dsn);
	$horario = ejecutar_script("SELECT cod_horario,descripcion FROM catalogo_horarios ORDER BY cod_horario",$dsn);
	$turno = ejecutar_script("SELECT cod_turno,descripcion FROM catalogo_turnos WHERE giro = " . ($_SESSION['tipo_usuario'] == 2 ? '2' : '1') . " ORDER BY cod_turno",$dsn);
	$tpl->assign($result[0]['credito_infonavit']?"infonavit_true":"infonavit_false","checked");
	
	for ($i=0; $i<count($cia); $i++) {
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
	}
	
	for ($i=0; $i<count($puesto); $i++) {
		$tpl->newBlock("puesto");
		$tpl->assign("id",$puesto[$i]['cod_puestos']);
		$tpl->assign("nombre",$puesto[$i]['descripcion']);
		if ($result[0]['cod_puestos'] == $puesto[$i]['cod_puestos']) $tpl->assign("selected","selected");
	}
	
	for ($i=0; $i<count($horario); $i++) {
		$tpl->newBlock("horario");
		$tpl->assign("id",$horario[$i]['cod_horario']);
		$tpl->assign("nombre",$horario[$i]['descripcion']);
		if ($result[0]['cod_horario'] == $horario[$i]['cod_horario']) $tpl->assign("selected","selected");
	}
	
	for ($i=0; $i<count($turno); $i++) {
		$tpl->newBlock("turno");
		$tpl->assign("id",$turno[$i]['cod_turno']);
		$tpl->assign("nombre",$turno[$i]['descripcion']);
		if ($result[0]['cod_turno'] == $turno[$i]['cod_turno']) $tpl->assign("selected","selected");
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
	die;
}
// RESULTADOS DE LA BUSQUEDA
if (isset($_GET['num_cia']) || isset($_GET['num_emp']) || isset($_GET['nombre']) || isset($_GET['ap_paterno']) || isset($_GET['ap_materno'])) {
	// Construir script SQL para la busqueda
	$sql = "SELECT id,ap_paterno,ap_materno,nombre,num_cia,cod_turno,cod_puestos FROM catalogo_trabajadores WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	if ($_GET['num_emp'] > 0)
		$sql .= " AND num_emp = $_GET[num_emp]";
	else {
		if ($_GET['num_cia'] > 0) {
			$sql .= " AND num_cia = $_GET[num_cia]";
			//if ($_GET['ap_paterno'] != "" || $_GET['ap_materno'] != "" || $_GET['nombre'] != "")
				//$sql .= " AND";
		}
		if ($_GET['ap_paterno'] != "") {
			$sql .= " AND ap_paterno LIKE '%".strtoupper($_GET['ap_paterno'])."%'";
			//if ($_GET['ap_materno'] != "" || $_GET['nombre'] != "")
				//$sql .= " AND";
		}
		if ($_GET['ap_materno'] != "") {
			$sql .= " AND ap_materno LIKE '%".strtoupper($_GET['ap_materno'])."%'";
			//if ($_GET['nombre'] != "")
				//$sql .= " AND";
		}
		if ($_GET['nombre'] != "") {
			$sql .= " AND nombre LIKE '%".strtoupper($_GET['nombre'])."%'";
		}
	}
	$sql .= " AND fecha_baja IS NULL ORDER BY num_cia, ap_paterno, ap_materno, nombre";
	$result = ejecutar_script($sql,$dsn);
		
	if (!$result) {
		header("location: ./fac_tra_mod.php?codigo_error=1");
		die;
	}
	$tpl->newBlock("lista");
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("id",$result[$i]['id']);
		$tpl->assign("nombre",$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']." ".$result[$i]['nombre']);
		$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = ".$result[$i]['num_cia'],$dsn);
		$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
		if ($result[$i]['cod_turno'] != "") {
			$turno = ejecutar_script("SELECT descripcion FROM catalogo_turnos WHERE cod_turno = ".$result[$i]['cod_turno'],$dsn);
			$tpl->assign("turno",$turno[0]['descripcion']);
		}
		else
			$tpl->assign("turno","NO DEFINIDO");
		if ($result[$i]['cod_puestos'] != "") {
			$puesto = ejecutar_script("SELECT descripcion FROM catalogo_puestos WHERE cod_puestos = ".$result[$i]['cod_puestos'],$dsn);
			$tpl->assign("puesto",$puesto[0]['descripcion']);
		}
		else
			$tpl->assign("puesto","NO DEFINIDO");
	}
	$tpl->printToScreen();
	die;
}
// DATOS PARA LA BUSQUEDA
$tpl->newBlock("datos");

if (isset($_GET['cambio']))
	$tpl->assign("mensaje","alert('Se cambio al empleado de compañía.".($_GET['prestamo'] == "TRUE"?" Se han traspasado tambien los prestamos.":"")."');");

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