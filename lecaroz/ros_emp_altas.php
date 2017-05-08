<?php
// ALTA DE EMPLEADOS (SOLO ROSTICERIAS)
// Tablas 'catalogo_empleados'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_emp_altas.tpl");
$tpl->prepare();

if (isset($_GET['tabla'])) {
	// Almacenar valores temporalmente
	for ($i=0; $i<5; $i++) {
		$_SESSION['nombre'.$i]      = $_POST['nombre'.$i];
		$_SESSION['ap_paterno'.$i]  = $_POST['ap_paterno'.$i];
		$_SESSION['ap_materno'.$i]  = $_POST['ap_materno'.$i];
		$_SESSION['cod_puestos'.$i] = $_POST['cod_puestos'.$i];
	}
	
	// Obtener último número de empleado
	$sql = "SELECT num_emp FROM catalogo_trabajadores WHERE fecha_baja IS NULL ORDER BY num_emp DESC LIMIT 1";
	$temp = ejecutar_script($sql,$dsn);
	$num_emp = $temp ? $temp[0]['num_emp'] + 1 : 1;
	
	$count = 0;
	for ($i=0; $i<5; $i++) {
		if ($_POST['nombre'.$i] != "" && $_POST['ap_paterno'.$i] != "") {
			$emp['num_cia'.$count]         = $_SESSION['num_cia'];
			$emp['cod_horario'.$count]     = "";
			$emp['cod_turno'.$count]       = 11;
			$emp['cod_puestos'.$count]     = $_POST['cod_puestos'.$i];
			$emp['ap_paterno'.$count]      = $_POST['ap_paterno'.$i];
			$emp['ap_materno'.$count]      = $_POST['ap_materno'.$i];
			$emp['nombre'.$count]          = $_POST['nombre'.$i];
			$emp['num_emp'.$count]         = $num_emp++;
			$count++;
		}
	}
	$db = new DBclass($dsn,$_GET['tabla'],$emp);
	$db->xinsertar();
	
	$tpl->newBlock("empleados");
	for ($i=0; $i<$count; $i++) {
		$tpl->newBlock("emp");
		$tpl->assign("num_emp",$emp['num_emp'.$i]);
		$tpl->assign("nombre",$emp['nombre'.$i]." ".$emp['ap_paterno'.$i]." ".$emp['ap_materno'.$i]);
	}
	
	//$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia = $_SESSION[num_cia]",$dsn);

$tpl->newBlock("alta");
$tpl->assign("num_cia",$_SESSION['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("tabla","catalogo_trabajadores");

// Generar filas
for ($i=0; $i<5; $i++) {
	$tpl->newBlock("fila");
	
	$tpl->assign("i",$i);
	if ($i < 5-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",5-1);
	
	if (isset($_GET['codigo_error'])) {
		$tpl->assign("nombre",$_SESSION['nombre'.$i]);
		$tpl->assign("ap_paterno",$_SESSION['ap_paterno'.$i]);
		$tpl->assign("ap_mastrno",$_SESSION['ap_materno'.$i]);
		$tpl->assign($_SESSION['cod_puestos'.$i],"selected");
	}
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message","El empleado no. $_GET[codigo_error] ya esta en el catálogo de empleados");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>