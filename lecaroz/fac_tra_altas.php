<?php
// ALTA DE TRABAJADORES
// Tabla 'catalogo_trabajadores'
// Menu Proveedores y facturas -> Trabajadores

define ('IDSCREEN',3311); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existe el número de compañia";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

if (isset($_GET['n'])) {
	$sql = 'SELECT num_cia, nombre_corto, num_emp FROM catalogo_trabajadores ct LEFT JOIN catalogo_companias USING (num_cia) WHERE ct.nombre = \'' . strtoupper(trim($_GET['n'])) . '\' AND ap_paterno = \'' . strtoupper(trim($_GET['ap'])) . '\' AND ap_materno = \'' . strtoupper(trim($_GET['am'])) . '\' AND fecha_baja IS NULL';
	$result = ejecutar_script($sql, $dsn);
	
	if ($result)
		echo 'El empleado ya esta dado de alta en la compañía "' . $result[0]['num_cia'] . ' - ' . $result[0]['nombre_corto'] . '" con número de empleado' . $result[0]['num_emp'];
	
	die;
}

//if ($_SESSION['iduser'] != 1) die("MODIFICANDO LA PANTALLA... GOMEN ^_^");
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// ************************** INSERTAR DATOS *******************************
if (isset($_POST['num_cia'])) {
	// Actualizar ultimos
	$sql = "UPDATE catalogo_trabajadores SET imp_alta = 'FALSE', ultimo = 'FALSE' WHERE ultimo = 'TRUE' AND imp_alta = 'TRUE'";
	ejecutar_script($sql,$dsn);
	
	$datos = $_POST;
	
	// Obtener número de empleado disponible para la compañía
	$datos['num_emp'] = nextID3("catalogo_trabajadores","num_emp",$dsn,"fecha_baja IS NULL AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899'));
	$datos['nombre_completo'] = "";
	$datos['solo_aguinaldo'] = isset($_POST['solo_aguinaldo']) ? "TRUE" : "FALSE";
	$datos['imp_alta'] = !isset($_POST['solo_aguinaldo']) && $_POST['num_afiliacion'] != "" ? "TRUE" : "FALSE";
	$datos['imp_baja'] = "FALSE";
	$datos['pendiente_alta'] = !isset($_POST['solo_aguinaldo']) && $_POST['num_afiliacion'] != "" ? date("d/m/Y") : "";
	$datos['pendiente_baja'] = "";
	$datos['ultimo'] = "FALSE";
	$datos['no_baja'] = isset($_POST['no_baja']) ? "TRUE" : "FALSE";
	$datos['observaciones'] = strtoupper(substr(trim($_POST['observaciones']), 0, 255));
	$datos['num_cia_emp'] = $_POST['num_cia_emp'] > 0 ? $_POST['num_cia_emp'] : $_POST['num_cia'];
	$datos['control_bata'] = isset($_POST['control_bata']) ? 'TRUE' : 'FALSE';
	$datos['deposito_bata'] = get_val($_POST['deposito_bata']);
	// [13-Jun-2008] Actualizar para zapaterias la fecha de alta del imss
	if ($_SESSION['tipo_usuario'] == 2)
		$datos['fecha_alta_imms'] = $datos['fecha_alta'];
	
	$tpl->newBlock("num_emp");
	$tpl->assign("num_cia",$_POST['num_cia']);
	$tpl->assign("nombre",strtoupper("$_POST[ap_paterno] $_POST[ap_materno] $_POST[nombre]"));
	$tpl->assign("num_emp",$datos['num_emp']);
	
	$db = new DBclass($dsn,"catalogo_trabajadores",$datos);
	$db->generar_script_insert("");
	$db->ejecutar_script();
	
	$sql = "UPDATE catalogo_trabajadores SET nombre_completo = ap_paterno || (CASE WHEN ap_materno IS NOT NULL AND TRIM(ap_materno) <> '' THEN ' ' || TRIM(ap_materno) ELSE '' END) || (CASE WHEN nombre IS NOT NULL AND TRIM(nombre) <> '' THEN ' ' || TRIM(nombre) ELSE '' END) WHERE id = (SELECT last_value FROM catalogo_trabajadores_id_seq)";
	ejecutar_script($sql, $dsn);
	
	if ($_POST['aguinaldo'] > 0) {
		$sql = "INSERT INTO aguinaldos (importe, fecha, id_empleado, tipo) VALUES ($_POST[importe], (SELECT fecha FROM aguinaldos WHERE fecha < '01/01/" . date("Y") . "' ORDER BY fecha DESC LIMIT 1), (SELECT last_value FROM catalogo_trabajadores_id_seq), 3)";
		
		ejecutar_script($sql, $dsn);
	}
}
else
	$tpl->newBlock("seleccionar");

$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " ORDER BY num_cia",$dsn);
$puesto = ejecutar_script("SELECT cod_puestos,descripcion FROM catalogo_puestos WHERE giro = " . ($_SESSION['tipo_usuario'] == 2 ? '2' : '1') . " ORDER BY cod_puestos",$dsn);
$horario = ejecutar_script("SELECT cod_horario,descripcion FROM catalogo_horarios ORDER BY cod_horario",$dsn);
$turno = ejecutar_script("SELECT cod_turno,descripcion FROM catalogo_turnos WHERE giro = " . ($_SESSION['tipo_usuario'] == 2 ? '2' : '1') . " ORDER BY cod_turno",$dsn);

$tpl->gotoBlock("_ROOT");
$tpl->assign("admin", isset($_GET['admin']) ? 1 : 0);
$tpl->assign("iduser", $_SESSION['iduser']);

if (isset($_GET['num_cia'])) $nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);

$tpl->assign("num_cia", isset($_GET['num_cia']) ? $_GET['num_cia'] : "");
$tpl->assign("nombre_cia", isset($_GET['num_cia']) ? $nombre_cia[0]['nombre_corto'] : "");
$tpl->assign("num_cia_readonly", isset($_GET['num_cia']) ? "readonly" : "");

$tpl->assign("fecha", date("d") < 15 ? date("01/m/Y") : date("15/m/Y"));
$tpl->assign("fecha_readonly", isset($_GET['admin']) || $_SESSION['iduser'] == 1 || $_SESSION['tipo_usuario'] == 2 ? "" : "readonly");

for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
}

for ($i=0; $i<count($puesto); $i++) {
	$tpl->newBlock("puesto");
	$tpl->assign("id",$puesto[$i]['cod_puestos']);
	$tpl->assign("nombre",$puesto[$i]['descripcion']);
}

for ($i=0; $i<count($horario); $i++) {
	$tpl->newBlock("horario");
	$tpl->assign("id",$horario[$i]['cod_horario']);
	$tpl->assign("nombre",$horario[$i]['descripcion']);
}

for ($i=0; $i<count($turno); $i++) {
	$tpl->newBlock("turno");
	$tpl->assign("id",$turno[$i]['cod_turno']);
	$tpl->assign("nombre",$turno[$i]['descripcion']);
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

if (isset($_GET['admin'])) $tpl->newBlock("boton_cerrar");

if (isset($_POST['admin']) && $_POST['admin'] == 1) {
	$tpl->newBlock("cerrar");
	die;
}

// Imprimir el resultado
$tpl->printToScreen();
?>