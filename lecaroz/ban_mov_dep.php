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
$tpl->assignInclude("body","./plantillas/ban/ban_mov_dep.tpl");
$tpl->prepare();

// Si ya se modificaron los datos, actualizar la base de datos
if (isset($_POST['numfilas_ban'])/* && isset($_POST['numfilas_lib'])*/) {
	if (isset($_POST['id_ban0'])) {
		// Obtener la compañia antes de modificar el registro
		$cia = ejecutar_script("SELECT num_cia,fecha,cod_banco FROM mov_banorte WHERE id = $_POST[id_ban0]",$dsn);
		
		// Checar movimientos de libros
		$count = 0;
		if (isset($_POST['numfilas_lib']))
			for ($i=0; $i<$_POST['numfilas_lib']; $i++)
				if (isset($_POST['id'.$i])) {
					ejecutar_script("UPDATE estado_cuenta SET fecha_con = '".$cia[0]['fecha']."' WHERE id = ".$_POST['id'.$i],$dsn);
					$temp = ejecutar_script("SELECT cod_mov FROM estado_cuenta WHERE id = ".$_POST['id'.$i],$dsn);
					$count++;
				}
		// Código de movimiento
		if ($count > 0)
			$cod_mov = $temp[0]['cod_mov'];
		else {
			$temp = ejecutar_script("SELECT cod_mov FROM catalogo_mov_bancos WHERE cod_banco = ".$cia[0]['cod_banco']." GROUP BY cod_mov ORDER BY cod_mov ASC",$dsn);
			$cod_mov = $temp[0]['cod_mov'];
		}
			/*if (($cia[0]['num_cia'] > 100 && $cia[0]['num_cia'] < 200) || $cia[0]['num_cia'] == 702 || $cia[0]['num_cia'] == 703 || $cia[0]['num_cia'] == 704)
				$cod_mov = 16;
			else
				$cod_mov = 1;*/
		
		// Poner fecha de conciliación en los registros de bancos
		$total = 0;
		for ($i=0; $i<$_POST['numfilas_ban']; $i++)
			if (isset($_POST['id_ban'.$i])) {
				// Ejecutar proceso inverso
				if (isset($_POST['inv'.$i])) {
					$temp = ejecutar_script("SELECT num_cia,num_documento,importe FROM mov_banorte WHERE id = ".$_POST['id_ban'.$i],$dsn);
					$total += $temp[0]['importe'];
					
					// Quitar fecha de conciliacion en libros
					ejecutar_script("UPDATE estado_cuenta SET fecha_con = NULL WHERE num_cia = ".$temp[0]['num_cia']." AND folio = ".$temp[0]['num_documento']." AND importe = ".$temp[0]['importe'],$dsn);
					// Insertar los movimientos de anulacion en libros (conciliados)
					$sql = "INSERT INTO estado_cuenta (num_cia,fecha,fecha_con,tipo_mov,importe,cod_mov,folio,concepto,cuenta) SELECT num_cia,fecha,fecha AS fecha_con,tipo_mov,importe,25 AS cod_mov,num_documento AS folio,concepto,1 FROM mov_banorte WHERE id = ".$_POST['id_ban'.$i];
					ejecutar_script($sql,$dsn);
					$sql = "INSERT INTO estado_cuenta (num_cia,fecha,fecha_con,tipo_mov,importe,cod_mov,folio,concepto,cuenta) SELECT num_cia,fecha,fecha AS fecha_con,'TRUE' AS tipo_mov,importe,5 AS cod_mov,num_documento AS folio,concepto,1 FROM mov_banorte WHERE id = ".$_POST['id_ban'.$i];
					ejecutar_script($sql,$dsn);
					
					// Actualiza saldo en bancos (YA LO ACTUALIZA ABAJO)
					//ejecutar_script("UPDATE saldos SET saldo_bancos = saldo_bancos + ".$temp[0]['importe']." WHERE num_cia = ".$temp[0]['num_cia'],$dsn);
					// Poner fecha de conciliacion a todos los movimientos de bancos
					//$sql = "DELETE FROM mov_banorte WHERE fecha_con IS NULL AND num_cia = ".$temp[0]['num_cia']." AND num_documento = ".$temp[0]['num_documento']." AND importe = ".$temp[0]['importe'];
					//ejecutar_script($sql,$dsn);
					// Quitar fecha de conciliado al cheque
					$sql = "UPDATE mov_banorte SET fecha_con = fecha,imprimir = 'TRUE' WHERE num_cia = ".$temp[0]['num_cia']." AND num_documento = ".$temp[0]['num_documento']." AND importe = ".$temp[0]['importe'];
					ejecutar_script($sql,$dsn);
				}
				else {
					ejecutar_script("UPDATE mov_banorte SET fecha_con = fecha,cod_mov = $cod_mov,imprimir = 'TRUE' WHERE id = ".$_POST['id_ban'.$i],$dsn);
					$temp = ejecutar_script("SELECT num_cia,num_documento,importe FROM mov_banorte WHERE id = ".$_POST['id_ban'.$i],$dsn);
					$total += $temp[0]['importe'];
					// Si no se seleccionaron movimientos de libros, insertar los de bancos en libros
					if ($count == 0) {
						$sql = "INSERT INTO estado_cuenta (num_cia,fecha,fecha_con,tipo_mov,importe,cod_mov,concepto,cuenta) SELECT num_cia,fecha,fecha_con,tipo_mov,importe,cod_mov,concepto,1 FROM mov_banorte WHERE id = ".$_POST['id_ban'.$i];
						ejecutar_script($sql,$dsn);
						
						// Obtener ID del registro insertado
						$sql = "SELECT id FROM estado_cuenta WHERE (num_cia,fecha,fecha_con,tipo_mov,importe,cod_mov,concepto,cuenta) IN (SELECT num_cia,fecha,fecha_con,tipo_mov,importe,cod_mov,concepto,1 FROM mov_banorte WHERE id = ".$_POST['id_ban'.$i].")";
						$id = ejecutar_script($sql,$dsn);
					}
				}
			}
		
		// Actualizar saldo en bancos (y en libros, si se da el caso)
		if ($count > 0)
			ejecutar_script("UPDATE saldos SET saldo_bancos = saldo_bancos + $total WHERE num_cia = ".$cia[0]['num_cia']." AND cuenta = 1",$dsn);
		else
			ejecutar_script("UPDATE saldos SET saldo_bancos = saldo_bancos + $total,saldo_libros = saldo_libros + $total WHERE num_cia = ".$cia[0]['num_cia']." AND cuenta = 1",$dsn);
	}
	
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
	$sql = "SELECT id,fecha,importe,cod_mov,concepto FROM estado_cuenta WHERE fecha_con IS NULL AND tipo_mov = 'FALSE' AND num_cia = ".$dep_ban[0]['num_cia']." AND cuenta = 1 ORDER BY fecha ASC";
	// Obtener depósitos de 'estado_cuenta'
	$dep_lib = ejecutar_script($sql,$dsn);
	
	$tpl->assign("numfilas_ban",count($dep_ban));
	
	// Mostrar registros de 'mov_banorte'
	$importe = 0;
	for ($i=0; $i<count($dep_ban); $i++) {
		$tpl->newBlock("dep_ban");
		$tpl->assign("i",$i);
		$tpl->assign("id_ban",$dep_ban[$i]['id']);
		$tpl->assign("fecha",$dep_ban[$i]['fecha']);
		$tpl->assign("cod_ban",$dep_ban[$i]['cod_banco']);
		$tpl->assign("concepto",$dep_ban[$i]['concepto']);
		$tpl->assign("importe",$dep_ban[$i]['importe']);
		$tpl->assign("fimporte",number_format($dep_ban[$i]['importe'],2,".",","));
		$importe += $dep_ban[$i]['importe'];
	}
	$tpl->assign("depositos_banco.importe_bancos",$importe);
	
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
			$tpl->assign("importe",$dep_lib[$i]['importe']);
			$tpl->assign("fimporte",number_format($dep_lib[$i]['importe'],2,".",","));
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