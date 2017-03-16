<?php
// CANCELACION DE CHEQUES
// Tabla 'cheques,estado_cuenta,pasivo_proveedores,facturas_pagadas'
// Menu

define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existe registro de cheque o ya ha sido cancelado";
$descripcion_error[2] = "No se puede cancelar el cheque debido a que ya ha sido conciliado";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// Cancelar un cheque
if (isset($_POST['num_cia'])) {
	// Verificar que el cheque exista para poder cancelarlo
	if (ejecutar_script("SELECT * FROM cheques WHERE num_cia = $_POST[num_cia] AND folio = $_POST[folio] AND importe = $_POST[importe]",$dsn)) {
		$fecha_con = ejecutar_script("SELECT fecha_con FROM estado_cuenta WHERE num_cia = $_POST[num_cia] AND folio = $_POST[folio] AND importe = $_POST[importe]",$dsn);
		
		// Si el movimiento no esta conciliado, cancelar el cheque
		if ($fecha_con[0]['fecha_con'] == "") {
			// Obtener datos del cheque
			$sql = "SELECT * FROM cheques WHERE num_cia = $_POST[num_cia] AND folio = $_POST[folio] AND importe = $_POST[importe]";
			$cheque = ejecutar_script($sql,$dsn);
			
			// Obtener timestamp de la fecha del cheque, de la de cancelacion y comparar
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$cheque[0]['fecha'],$temp);
			$cheque_ts = mktime(0,0,0,$temp[2],$temp[1],$temp[3]);
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_POST['fecha_cancelacion'],$temp);
			$cancelacion_ts = mktime(0,0,0,$temp[2],$temp[1],$temp[3]);
			$mes_ts = mktime(0,0,0,$temp[2],1,$temp[3]);
			
			// CASO 1. CANCELACION NORMAL
			if ($cheque_ts >= $mes_ts) {
				// Borrar movimiento de gastos
				//$sql = "DELETE FROM movimiento_gastos WHERE num_cia = $_POST[num_cia] AND fecha = '".$cheque[0]['fecha']."' AND importe = $_POST[importe]";
				$sql = "DELETE FROM movimiento_gastos WHERE num_cia = $_POST[num_cia] AND folio = $_POST[folio] AND fecha = '".$cheque[0]['fecha']."'";
				ejecutar_script($sql,$dsn);
			}
			// CASO 2. CANCELACION DE UN CHEQUE DE MESES PASADOS
			else {
				// Insertar un cheque negativo con la fecha de cancelacion como fecha de creación
				$sql  = "INSERT INTO movimiento_gastos (codgastos,num_cia,fecha,importe,concepto,captura,factura,folio) ";
				$sql .= "SELECT codgastos,num_cia,'$_POST[fecha_cancelacion]',-1 * total,descripcion,'TRUE',num_fact,$_POST[folio] FROM facturas_pagadas WHERE num_cia = $_POST[num_cia] AND folio_cheque = $_POST[folio]";
				//$sql = "INSERT INTO movimiento_gastos (codgastos,num_cia,fecha,importe,concepto,captura,folio) SELECT codgastos,num_cia,'$_POST[fecha_cancelacion]',importe * -1,concepto,'TRUE',folio FROM cheques WHERE id = ".$cheque[0]['id'];
				ejecutar_script($sql,$dsn);
				// Insertar un gasto negativo con la fecha de cancelacion como fecha de creación
				$sql = "INSERT INTO cheques (cod_mov,num_proveedor,num_cia,fecha,folio,importe,iduser,a_nombre,imp,concepto,facturas,fecha_cancelacion,codgastos) SELECT cod_mov,num_proveedor,num_cia,'$_POST[fecha_cancelacion]',folio,importe * -1,iduser,a_nombre,imp,concepto,facturas,fecha_cancelacion,codgastos FROM cheques WHERE id = ".$cheque[0]['id'];
				ejecutar_script($sql,$dsn);
			}
			
			// Poner la fecha de cancelación al cheque
			$sql = "UPDATE cheques SET fecha_cancelacion = '$_POST[fecha_cancelacion]', iduser_can = {$_SESSION['iduser']}, tscan = NOW() WHERE id = ".$cheque[0]['id'];
			ejecutar_script($sql,$dsn);
			
			// Borrar movimiento del estado de cuenta
			$sql = "DELETE FROM estado_cuenta WHERE num_cia = $_POST[num_cia] AND folio = $_POST[folio] AND importe = $_POST[importe]";
			ejecutar_script($sql,$dsn);
			
			// Modificar saldo para la cuenta
			$sql = "UPDATE saldos SET saldo_libros = saldo_libros + ".$cheque[0]['importe']." WHERE num_cia = $_POST[num_cia]";
			ejecutar_script($sql,$dsn);
			
			// Si esta habilitada la opción de regresar facturas a pasivo...
			if ($_POST['return'] == "TRUE") {
				// Pasar todas las facturas pagadas con el cheque a pasivo a proveedores
				$sql  = "INSERT INTO pasivo_proveedores (num_cia,num_fact,total,descripcion,fecha_mov,fecha_pago,num_proveedor,codgastos) ";
				$sql .= "SELECT num_cia,num_fact,total,descripcion,fecha_mov,fecha_pago,num_proveedor,codgastos FROM facturas_pagadas WHERE num_cia = $_POST[num_cia] AND folio_cheque = $_POST[folio]";
				ejecutar_script($sql,$dsn);
				// Borrar de facturas pagadas
				$sql = "DELETE FROM facturas_pagadas WHERE num_cia = $_POST[num_cia] AND folio_cheque = $_POST[folio]";
				ejecutar_script($sql,$dsn);
			}
		}
		else {
			header("location: ./ban_che_can.php?codigo_error=2");
			die;
		}
	}
	// Si no, regersar un error
	else {
		header("location: ./ban_che_can.php?codigo_error=1");
		die;
	}
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_can.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Generar pantalla de datos
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("fecha",date("d/m/Y"));
	
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
	
	$tpl->printToScreen();
	die;
}

//$clabe_cuenta = $_GET['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";

// Si existe el cheque, mostrar sus datos, si no, regresar a la pantalla inicial
$sql = "SELECT num_cia,nombre AS nombre_cia,clabe_cuenta AS cuenta,importe,folio,a_nombre,facturas FROM cheques JOIN catalogo_companias USING(num_cia) WHERE num_cia = $_GET[num_cia] AND folio = $_GET[folio] AND fecha_cancelacion IS NULL AND importe > 0";
$cheque = ejecutar_script($sql,$dsn);

if (!$cheque) {
	header("location: ./ban_che_can.php?codigo_error=1");
	die;
}

$tpl->newBlock("info");

	$tpl->assign("num_cia",$_GET['num_cia']);
	$tpl->assign("folio",$_GET['folio']);
	$tpl->assign("fecha_cancelacion",$_GET['fecha_cancelacion']);
	$tpl->assign("return",(isset($_GET['return']))?"TRUE":"FALSE");
	
	$tpl->assign("num_cia",$cheque[0]['num_cia']);
	$tpl->assign("nombre_cia",$cheque[0]['nombre_cia']);
	$tpl->assign("cuenta",$cheque[0]['cuenta']);
	$tpl->assign("importe",$cheque[0]['importe']);
	$tpl->assign("fimporte",number_format($cheque[0]['importe'],2,".",","));
	$tpl->assign("folio",$cheque[0]['folio']);
	$tpl->assign("a_nombre",$cheque[0]['a_nombre']);
	$tpl->assign("facturas",$cheque[0]['facturas']);

$tpl->printToScreen();
?>