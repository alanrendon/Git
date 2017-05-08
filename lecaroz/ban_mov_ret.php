<?php
// CONCILIACIÓN RÁPIDA DE RETIROS
// Tablas 'estado_cuenta,mov_banorte'
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
$tpl->assignInclude("body","./plantillas/ban/ban_mov_ret.tpl");
$tpl->prepare();

// Si ya se modificaron los datos, actualizar la base de datos
if (isset($_POST['id_ban'])) {
	// Obtener la compañía antes de modificar el registro
	$cia = ejecutar_script("SELECT num_cia,fecha FROM mov_banorte WHERE id = $_POST[id_ban]",$dsn);
	
	// Poner fecha de conciliación en los registros
	if (isset($_POST['id'])) {
		$cod_mov = ejecutar_script("SELECT cod_mov FROM estado_cuenta WHERE id = $_POST[id]",$dsn);
		ejecutar_script("UPDATE mov_banorte SET fecha_con = fecha,cod_mov = ".$cod_mov[0]['cod_mov'].",imprimir='TRUE' WHERE id = $_POST[id_ban]",$dsn);
		
		$importe = ejecutar_script("SELECT importe FROM mov_banorte WHERE id = $_POST[id_ban]",$dsn);
		ejecutar_script("UPDATE estado_cuenta SET fecha_con = '".$cia[0]['fecha']."' WHERE id = $_POST[id]",$dsn);
		ejecutar_script("UPDATE saldos SET saldo_bancos = saldo_bancos - ".$importe[0]['importe']." WHERE num_cia = ".$cia[0]['num_cia']." AND cuenta = 1",$dsn);
	}
	else {
		ejecutar_script("UPDATE mov_banorte SET fecha_con = fecha,cod_mov = 5,imprimir='TRUE' WHERE id = $_POST[id_ban]",$dsn);
		$importe = ejecutar_script("SELECT importe,fecha FROM mov_banorte WHERE id = $_POST[id_ban]",$dsn);
		ejecutar_script("UPDATE saldos SET saldo_bancos = saldo_bancos - ".$importe[0]['importe'].",saldo_libros = saldo_libros - ".$importe[0]['importe']." WHERE num_cia = ".$cia[0]['num_cia']." AND cuenta = 1",$dsn);
		ejecutar_script("INSERT INTO estado_cuenta (num_cia,fecha,fecha_con,tipo_mov,importe,cod_mov,folio,concepto,cuenta) SELECT num_cia,fecha,fecha_con,tipo_mov,importe,cod_mov,num_documento,concepto,1 FROM mov_banorte WHERE id = $_POST[id_ban]",$dsn);
		
		// Obtener ID del registro insertado
		$sql = "SELECT id FROM estado_cuenta WHERE (num_cia,fecha,fecha_con,tipo_mov,importe,cod_mov,folio,concepto,cuenta) IN (SELECT num_cia,fecha,fecha_con,tipo_mov,importe,cod_mov,num_documento,concepto,1 FROM mov_banorte WHERE id = $_POST[id_ban])";
		$id = ejecutar_script($sql,$dsn);
	}
	
	$tpl->newBlock("cerrar");
	$tpl->assign("num_cia",$cia[0]['num_cia']);
	
	$tpl->printToScreen();
	die;
}

// Obtener retiro de 'mov_banorte'
$sql = "SELECT id,num_cia,fecha,num_documento AS folio,concepto,importe FROM mov_banorte WHERE id=$_GET[id]";
$ret_ban = ejecutar_script($sql,$dsn);

// Buscar cheques en libros que no esten conciliados y ke tengan el mismo importe
$sql  = "SELECT id,fecha,folio,concepto,importe FROM estado_cuenta WHERE fecha_con IS NULL";
$sql .= " AND num_cia = ".$ret_ban['0']['num_cia'];
$sql .= " AND tipo_mov = 'TRUE'";
$sql .= " AND importe = ".$ret_ban['0']['importe'];
$sql .= " AND cuenta = 1";
$sql .= " ORDER BY fecha ASC";
$ret_lib = ejecutar_script($sql,$dsn);

// Trazar encabezado de pantalla
$tpl->assign("id_ban",$ret_ban[0]['id']);
$tpl->assign("fecha",$ret_ban[0]['fecha']);
$tpl->assign("folio",$ret_ban[0]['folio']);
$tpl->assign("concepto",$ret_ban[0]['concepto']);
$tpl->assign("importe",number_format($ret_ban[0]['importe'],2,".",","));

// Si hay registros parecidos
if ($ret_lib) {
	$tpl->newBlock("cheques");
	
	for ($i=0; $i<count($ret_lib); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("id",$ret_lib[$i]['id']);
		$tpl->assign("fecha",$ret_lib[$i]['fecha']);
		$tpl->assign("folio",($ret_lib[$i]['folio'] > 0)?$ret_lib[$i]['folio']:"&nbsp;");
		$tpl->assign("concepto",$ret_lib[$i]['concepto']);
		$tpl->assign("importe",number_format($ret_lib[$i]['importe'],2,".",","));
	}
}
else {
	$tpl->newBlock("no_cheques");
}

$tpl->printToScreen();
?>