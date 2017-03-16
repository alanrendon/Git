<?php
// CONCILIACIÓN RÁPIDA DE DEPOSITOS
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
$tpl->assignInclude("body","./plantillas/ban/ban_mnc_mde.tpl");
$tpl->prepare();

// Si ya se modificaron los datos, actualizar la base de datos
if (isset($_POST['numfilas_ban']) && isset($_POST['numfilas_lib'])) {
	// Obtener la compañia antes de modificar el registro
	$cia = ejecutar_script("SELECT num_cia FROM mov_banorte WHERE id = $_POST[id_ban0]",$dsn);
	
	// Poner fecha de conciliación en los registros
	for ($i=0; $i<$_POST['numfilas_ban']; $i++)
		if (isset($_POST['id_ban'.$i]))
			ejecutar_script("UPDATE mov_banorte SET fecha_con = '".date("d/m/Y")."',cod_mov = 1 WHERE id = ".$_POST['id_ban'.$i],$dsn);
	
	for ($i=0; $i<$_POST['numfilas_lib']; $i++)
		if (isset($_POST['id'.$i]))
			ejecutar_script("UPDATE estado_cuenta SET fecha_con = '".date("d/m/Y")."' WHERE id = ".$_POST['id'.$i],$dsn);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("num_cia",$cia[0]['num_cia']);
	
	$tpl->printToScreen();
	die;
}

// Obtener id's de los depositos no conciliados
$count = 0;
for ($i=0; $i<$_GET['numfilas']; $i++)
	if (isset($_GET['id'.$i]))
		$id[$count++] = $_GET['id'.$i];

// Si hay id's
if ($count > 0) {
	$tpl->newBlock("depositos_banco");
	
	// Construir script sql y obtener depósitos de 'mov_banorte'
	$sql = "SELECT id,num_cia,fecha,concepto,importe,cod_banco FROM mov_banorte WHERE id IN (";
	for ($i=0; $i<$count; $i++) {
		$sql .= $id[$i];
		if ($i < $count - 1)
			$sql .= ",";
	}
	$sql .= ") ORDER BY fecha ASC";
	// Obtener depósitos de 'mov_banorte'
	$dep_ban = ejecutar_script($sql,$dsn);
	
	// Construir script sql y obtener depósitos no conciliados de 'estado_cuenta'
	$sql = "SELECT id,fecha,importe,cod_mov,concepto FROM estado_cuenta WHERE fecha_con IS NULL AND tipo_mov = 'FALSE' AND num_cia = ".$dep_ban[0]['num_cia']." ORDER BY fecha ASC";
	// Obtener depósitos de 'estado_cuenta'
	$dep_lib = ejecutar_script($sql,$dsn);
	
	$tpl->assign("numfilas_ban",count($dep_ban));
	
	// Mostrar registros de 'mov_banorte'
	for ($i=0; $i<count($dep_ban); $i++) {
		$tpl->newBlock("dep_ban");
		$tpl->assign("i",$i);
		$tpl->assign("id_ban",$dep_ban[$i]['id']);
		$tpl->assign("fecha",$dep_ban[$i]['fecha']);
		$tpl->assign("cod_ban",$dep_ban[$i]['cod_banco']);
		$tpl->assign("concepto",$dep_ban[$i]['concepto']);
		$tpl->assign("importe",number_format($dep_ban[$i]['importe'],2,".",","));
	}
	
	// Mostrar registros de 'estado_cuenta' si los hay
	if (!$dep_lib) {
		$tpl->newBlock("no_depositos_libros");
	}
	else {
		$tpl->newBlock("depositos_libros");
		
		$tpl->assign("numfilas_lib",count($dep_lib));
		
		for ($i=0; $i<count($dep_lib); $i++) {
			$tpl->newBlock("dep_lib");
			$tpl->assign("i",$i);
			$tpl->assign("id",$dep_lib[$i]['id']);
			$tpl->assign("fecha",$dep_lib[$i]['fecha']);
			$tpl->assign("cod_mov",$dep_lib[$i]['cod_mov']);
			$tpl->assign("concepto",$dep_lib[$i]['concepto']);
			$tpl->assign("importe",number_format($dep_lib[$i]['importe'],2,".",","));
		}
	}
	
	$tpl->printToScreen();
	die;
}
else {
	$tpl->newBlock("no_depositos");
	$tpl->printToScreen();
	die;
}

?>