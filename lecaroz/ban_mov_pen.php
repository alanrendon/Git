<?php
// MOVMIENTOS PENDIENTES DE PALOMEAR
// Tabla 'estado_cuenta'
// Menu 'Banco->Conciliación automática'

//define ('IDSCREEN',1221); // ID de pantalla

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
$descripcion_error[1] = "El archivo ya fue cargado en el sistema";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_mov_pen.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// En caso de ya imprimir el listado
if (isset($_GET['imp'])) {
	// Generar listado de movimientos conciliados
	// Construir script sql para obtener todos los movimientos conciliados manualmente durante todo el proceso
	$sql = "SELECT * FROM mov_banorte WHERE imprimir = 'TRUE' ORDER BY num_cia,fecha";
	$mov_lib = ejecutar_script($sql,$dsn);
	
	if (!$mov_lib) {
		header("location: ./ban_mov_pen.php");
		die;
	}
	
	// Quitar marcas de impresión
	$sql = "UPDATE mov_banorte SET imprimir='FALSE' WHERE imprimir='TRUE'";
	ejecutar_script($sql,$dsn);
	
	$tpl->newBlock("listado");
	$tpl->assign("dia",date("d"));
	$tpl->assign("anio",date("Y"));
	switch (date("m")) {
		case 1: $tpl->assign("mes","Enero"); break;
		case 2: $tpl->assign("mes","Febrero"); break;
		case 3: $tpl->assign("mes","Marzo"); break;
		case 4: $tpl->assign("mes","Abril"); break;
		case 5: $tpl->assign("mes","Mayo"); break;
		case 6: $tpl->assign("mes","Junio"); break;
		case 7: $tpl->assign("mes","Julio"); break;
		case 8: $tpl->assign("mes","Agosto"); break;
		case 9: $tpl->assign("mes","Septiembre"); break;
		case 10: $tpl->assign("mes","Octubre"); break;
		case 11: $tpl->assign("mes","Noviembre"); break;
		case 12: $tpl->assign("mes","Diciembre"); break;
	}
	
	$cia = NULL;
	$gran_total_deposito = 0;
	$gran_total_retiro = 0;
	
	$total_deposito = 0;
	$total_retiro = 0;
	for ($i=0; $i<count($mov_lib); $i++) {
		if ($mov_lib[$i]['num_cia'] != $cia) {
			if ($cia != NULL) {
				$tpl->assign("cia_con.total_deposito",number_format($total_deposito,2,".",","));
				$tpl->assign("cia_con.total_retiro",number_format($total_retiro,2,".",","));
				$total_deposito = 0;
				$total_retiro = 0;
			}
			
			$tpl->newBlock("cia_con");
			$result = ejecutar_script("SELECT nombre,nombre_corto,clabe_cuenta FROM catalogo_companias WHERE num_cia=".$mov_lib[$i]['num_cia'],$dsn);
			$tpl->assign("num_cia",$mov_lib[$i]['num_cia']);
			$tpl->assign("cuenta",$result[0]['clabe_cuenta']);
			$tpl->assign("nombre_cia",$result[0]['nombre']);
			$tpl->assign("nombre_corto",$result[0]['nombre_corto']);
			$cia = $mov_lib[$i]['num_cia'];
			
			$tpl->newBlock("fila_con");
			$tpl->assign("fecha",$mov_lib[$i]['fecha']);
			$result = ejecutar_script("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov=".$mov_lib[$i]['cod_mov'],$dsn);
			$tpl->assign("codigo",$mov_lib[$i]['cod_mov']);
			$tpl->assign("descripcion",$result[0]['descripcion']);
			$tpl->assign("deposito",($mov_lib[$i]['tipo_mov'] == "f")?number_format($mov_lib[$i]['importe'],2,".",","):"&nbsp;");
			$tpl->assign("retiro",($mov_lib[$i]['tipo_mov'] == "t")?number_format($mov_lib[$i]['importe'],2,".",","):"&nbsp;");
			$tpl->assign("folio",($mov_lib[$i]['num_documento'] > 0)?$mov_lib[$i]['num_documento']:"&nbsp;");
			$tpl->assign("concepto",$mov_lib[$i]['concepto']);
			
			if ($mov_lib[$i]['tipo_mov'] == "f") {
				$total_deposito += $mov_lib[$i]['importe'];
				$gran_total_deposito += $mov_lib[$i]['importe'];
			}
			else {
				$total_retiro += $mov_lib[$i]['importe'];
				$gran_total_retiro += $mov_lib[$i]['importe'];
			}
		}
		else {
			$tpl->newBlock("fila_con");
			$tpl->assign("fecha",$mov_lib[$i]['fecha']);
			$result = ejecutar_script("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov=".$mov_lib[$i]['cod_mov'],$dsn);
			$tpl->assign("codigo",$mov_lib[$i]['cod_mov']);
			$tpl->assign("descripcion",$result[0]['descripcion']);
			$tpl->assign("deposito",($mov_lib[$i]['tipo_mov'] == "f")?number_format($mov_lib[$i]['importe'],2,".",","):"&nbsp;");
			$tpl->assign("retiro",($mov_lib[$i]['tipo_mov'] == "t")?number_format($mov_lib[$i]['importe'],2,".",","):"&nbsp;");
			$tpl->assign("folio",($mov_lib[$i]['num_documento'] > 0)?$mov_lib[$i]['num_documento']:"&nbsp;");
			$tpl->assign("concepto",$mov_lib[$i]['concepto']);
			
			if ($mov_lib[$i]['tipo_mov'] == "f") {
				$total_deposito += $mov_lib[$i]['importe'];
				$gran_total_deposito += $mov_lib[$i]['importe'];
			}
			else {
				$total_retiro += $mov_lib[$i]['importe'];
				$gran_total_retiro += $mov_lib[$i]['importe'];
			}
		}
	}
	if ($cia != NULL) {
		$tpl->assign("cia_con.total_deposito",number_format($total_deposito,2,".",","));
		$tpl->assign("cia_con.total_retiro",number_format($total_retiro,2,".",","));
	}
	$tpl->assign("listado.gran_total_deposito",number_format($gran_total_deposito,2,".",","));
	$tpl->assign("listado.gran_total_retiro",number_format($gran_total_retiro,2,".",","));
	
	$tpl->printToScreen();
	
	die;
}

// Obtener todos los movimientos (por archivo)
$mov_ban = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con IS NULL AND num_cia > 0 ORDER BY num_cia,fecha ASC",$dsn);

// Listar movimientos no conciliados
if ($mov_ban) {
	$tpl->newBlock("no_conciliados");
	$cia = NULL;
	$gran_total_deposito = 0;
	$gran_total_retiro = 0;
	
	$total_deposito = 0;
	$total_retiro = 0;
	for ($i=0; $i<count($mov_ban); $i++) {
		if ($mov_ban[$i]['num_cia'] != $cia) {
			if ($cia != NULL) {
				if ($count_depositos > 0) {
					$tpl->newBlock("boton_d");
					$tpl->assign("numfilas",$count_depositos);
				}
				
				$tpl->assign("cia_nocon.total_deposito",number_format($total_deposito,2,".",","));
				$tpl->assign("cia_nocon.total_retiro",number_format($total_retiro,2,".",","));
				$total_deposito = 0;
				$total_retiro = 0;
			}
			
			$count_depositos = 0;
			$count_retiros = 0;
			
			$tpl->newBlock("cia_nocon");
			$result = ejecutar_script("SELECT nombre,nombre_corto,clabe_cuenta FROM catalogo_companias WHERE num_cia=".$mov_ban[$i]['num_cia'],$dsn);
			$tpl->assign("num_cia",$mov_ban[$i]['num_cia']);
			$tpl->assign("cuenta",$result[0]['clabe_cuenta']);
			$tpl->assign("nombre_cia",$result[0]['nombre']);
			$tpl->assign("nombre_corto",$result[0]['nombre_corto']);
			$cia = $mov_ban[$i]['num_cia'];
			
			$tpl->newBlock("fila_nocon");
			$tpl->assign("id",$mov_ban[$i]['id']);
			$tpl->assign("fecha",$mov_ban[$i]['fecha']);
			$tpl->assign("codigo",$mov_ban[$i]['cod_banco']);
			$tpl->assign("deposito",($mov_ban[$i]['tipo_mov'] == "f")?number_format($mov_ban[$i]['importe'],2,".",","):"&nbsp;");
			$tpl->assign("retiro",($mov_ban[$i]['tipo_mov'] == "t")?number_format($mov_ban[$i]['importe'],2,".",","):"&nbsp;");
			$tpl->assign("folio",($mov_ban[$i]['num_documento'] > 0)?$mov_ban[$i]['num_documento']:"&nbsp;");
			$tpl->assign("concepto",$mov_ban[$i]['concepto']);
			
			if ($mov_ban[$i]['tipo_mov'] == "f") {
				$total_deposito += $mov_ban[$i]['importe'];
				$gran_total_deposito += $mov_ban[$i]['importe'];
				
				// Crear checkbox de modificación de depósito
				$tpl->newBlock("modifica_depositos");
				$tpl->assign("i",$count_depositos);
				$tpl->assign("id",$mov_ban[$i]['id']);
				$count_depositos++;
			}
			else {
				$total_retiro += $mov_ban[$i]['importe'];
				$gran_total_retiro += $mov_ban[$i]['importe'];
				
				// Crear boton de modificación de retiros
				$tpl->newBlock("modifica_retiros");
				$tpl->assign("id",$mov_ban[$i]['id']);
				$count_retiros++;
			}
		}
		else {
			$tpl->newBlock("fila_nocon");
			$tpl->assign("id",$mov_ban[$i]['id']);
			$tpl->assign("fecha",$mov_ban[$i]['fecha']);
			$tpl->assign("codigo",$mov_ban[$i]['cod_banco']);
			$tpl->assign("deposito",($mov_ban[$i]['tipo_mov'] == "f")?number_format($mov_ban[$i]['importe'],2,".",","):"&nbsp;");
			$tpl->assign("retiro",($mov_ban[$i]['tipo_mov'] == "t")?number_format($mov_ban[$i]['importe'],2,".",","):"&nbsp;");
			$tpl->assign("folio",($mov_ban[$i]['num_documento'] > 0)?$mov_ban[$i]['num_documento']:"&nbsp;");
			$tpl->assign("concepto",$mov_ban[$i]['concepto']);
			
			if ($mov_ban[$i]['tipo_mov'] == "f") {
				// Asignación de importe
				$total_deposito += $mov_ban[$i]['importe'];
				$gran_total_deposito += $mov_ban[$i]['importe'];
				
				// Crear checkbox de modificación de depósito
				$tpl->newBlock("modifica_depositos");
				$tpl->assign("i",$count_depositos);
				$tpl->assign("id",$mov_ban[$i]['id']);
				$count_depositos++;
			}
			else {
				// Asignación de importe
				$total_retiro += $mov_ban[$i]['importe'];
				$gran_total_retiro += $mov_ban[$i]['importe'];
				
				// Crear boton de modificación de retiros
				$tpl->newBlock("modifica_retiros");
				$tpl->assign("id",$mov_ban[$i]['id']);
				$count_retiros++;
			}
		}
	}
	if ($cia != NULL) {
		if ($count_depositos > 0) {
			$tpl->newBlock("boton_d");
			$tpl->assign("numfilas",$count_depositos);
		}
	
		$tpl->assign("cia_nocon.total_deposito",number_format($total_deposito,2,".",","));
		$tpl->assign("cia_nocon.total_retiro",number_format($total_retiro,2,".",","));
	}
	$tpl->assign("no_conciliados.gran_total_deposito",number_format($gran_total_deposito,2,".",","));
	$tpl->assign("no_conciliados.gran_total_retiro",number_format($gran_total_retiro,2,".",","));
}
else {
	$tpl->newBlock("no_result");
}

$tpl->printToScreen();
?>