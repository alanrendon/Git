<?php
// BAJA DE TRABAJADORES
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
$tpl->assignInclude("body","./plantillas/fac/fac_tra_del.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// ************************** INSERTAR DATOS *******************************
// BAJA EN LA BASE DE DATOS
if (isset($_POST['numfilas'])) {
	// Actualizar ultimos
	$sql = "UPDATE catalogo_trabajadores SET imp_baja = 'FALSE', ultimo = 'FALSE' WHERE ultimo = 'TRUE' AND imp_baja = 'TRUE'";
	ejecutar_script($sql, $dsn);
	
	$fecha = date("d/m/Y");
	
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if (isset($_POST['id'.$i])) {
			$sql = "UPDATE catalogo_trabajadores SET fecha_baja = '$fecha'";
			if ($_POST['afiliado'.$i] == "TRUE")
				$sql .= ",imp_baja = 'TRUE',pendiente_baja = '$fecha'";
			$sql .= " WHERE id = ". $_POST['id'.$i];
			ejecutar_script($sql,$dsn);
		}
		else if (isset($_POST['pension'.$i])) {
			$sql = "UPDATE catalogo_trabajadores SET imp_baja = 'TRUE',pendiente_baja = '$fecha' WHERE id = ".$_POST['pension'.$i];
			ejecutar_script($sql,$dsn);
		}
		
	}
	
	header("location: ./fac_tra_del.php");
	die;
}
// RESULTADOS DE LA BUSQUEDA
if (isset($_GET['num_cia']) || isset($_GET['num_emp']) || isset($_GET['nombre']) || isset($_GET['ap_paterno']) || isset($_GET['ap_materno'])) {
	// Construir script SQL para la busqueda
	$sql = "SELECT id,ap_paterno,ap_materno,nombre,num_cia,cod_turno,cod_puestos,num_afiliacion,no_baja FROM catalogo_trabajadores WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
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
	$sql .= " AND fecha_baja IS NULL ORDER BY num_cia";
	$result = ejecutar_script($sql,$dsn);
		
	if (!$result) {
		header("location: ./fac_tra_del.php?codigo_error=1");
		die;
	}
	$tpl->newBlock("lista");
	$tpl->assign("numfilas",count($result));
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
		$tpl->assign("id",$result[$i]['id']);
		$tpl->assign("afiliado",$result[$i]['num_afiliacion'] != "" ? "TRUE" : "FALSE");
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
		// Buscar prestamos sin pagar
		$sql = "SELECT tipo_mov,sum(importe) FROM prestamos WHERE id_empleado = {$result[$i]['id']} AND pagado = 'FALSE' GROUP BY tipo_mov ORDER BY tipo_mov";
		$temp = ejecutar_script($sql,$dsn);
		$temp1 = isset($temp[0]['sum']) ? $temp[0]['sum'] : 0;
		$temp2 = isset($temp[1]['sum']) ? $temp[1]['sum'] : 0;
		$prestamo = $temp1 - $temp2;
		$tpl->assign("prestamo",$prestamo > 0 ? number_format($prestamo,2,".",",") : "&nbsp;");
		$tpl->assign("disabled",$prestamo > 0 || $result[$i]['no_baja'] == "t" ? "disabled=\"true\"" : "");
		$tpl->assign("color",$prestamo > 0 ? "#FFFF00" : ($result[$i]['no_baja'] == "t" ? "#0000CC" : ""));
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