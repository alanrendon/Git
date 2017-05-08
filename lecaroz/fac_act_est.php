<?php
// CAMBIO DE ESTADO ALTA/BAJA A TRABAJADORES
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
$tpl->assignInclude("body","./plantillas/fac/fac_act_est.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// ************************** INSERTAR DATOS *******************************
// MODIFICACION EN LA BASE DE DATOS
if (isset($_POST['numfilas'])) {
	$fecha = date("d/m/Y");
	
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if (isset($_POST['id'.$i])) {
			$sql = "UPDATE catalogo_trabajadores SET pendiente_$_POST[tipo] = NULL, fecha_$_POST[tipo]_imss = '$fecha' WHERE id = ".$_POST['id'.$i];
			ejecutar_script($sql,$dsn);
		}
	}
	header("location: ./fac_act_est.php");
	die;
}
// RESULTADOS DE LA BUSQUEDA
if (isset($_GET['tipo'])) {
	$fecha_actual = time();
	
	// Construir script SQL para la busqueda
	$sql = "SELECT id,num_emp,ap_paterno,ap_materno,catalogo_trabajadores.nombre AS nombre,num_cia,catalogo_companias.nombre AS nombre_cia,catalogo_companias.nombre_corto AS nombre_corto,pendiente_$_GET[tipo] FROM catalogo_trabajadores JOIN catalogo_companias USING(num_cia) WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	if ($_GET['num_cia'] > 0)
		$sql .= " AND num_cia = $_GET[num_cia]";
	$sql .= " AND pendiente_$_GET[tipo] IS NOT NULL ORDER BY pendiente_$_GET[tipo]";
	$result = ejecutar_script($sql,$dsn);
		
	if (!$result) {
		header("location: ./fac_act_est.php?codigo_error=1");
		die;
	}
	$tpl->newBlock("listado");
	$tpl->assign("estado",$_GET['tipo'] == "alta" ? "Alta" : "Baja");
	
	$tpl->assign("tipo",$_GET['tipo']);
	$tpl->assign("numfilas",count($result));
	
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
		$tpl->assign("id",$result[$i]['id']);
		$tpl->assign("num_emp",$result[$i]['num_emp']);
		$tpl->assign("nombre",$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']." ".$result[$i]['nombre']);
		$tpl->assign("nombre_cia",$result[$i]['nombre_cia']." (".$result[$i]['nombre_corto'].")");
		$tpl->assign("fecha",$result[$i]["pendiente_$_GET[tipo]"]);
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$result[$i]["pendiente_$_GET[tipo]"],$temp);
		$fecha_aviso = mktime(0,0,0,$temp[2],$temp[1],$temp[3]);
		$dias = ceil(($fecha_actual - $fecha_aviso) / 86400);
		$tpl->assign("dias",$dias);
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