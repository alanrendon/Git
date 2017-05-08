<?php
// CONCILIACION MANUAL DE DEPOSITOS Y CHEQUES
// Tablas 'estados'
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
$tpl->assignInclude("body","./plantillas/ban/ban_conciliacion.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['accion']) && $_GET['accion'] == "terminar") {
	// Generar listado de movimientos conciliados
	if ($_SESSION['con']['id_con_count'] > 0) {
		// Construir script sql para obtener todos los movimientos conciliados manualmente durante todo el proceso
		$sql = "SELECT * FROM estado_cuenta WHERE id IN (";
		for ($i=0; $i<$_SESSION['con']['id_con_count']; $i++) {
			$sql .= $_SESSION['con']['id_con'.$i];
			if ($i < $_SESSION['con']['id_con_count'] - 1)
				$sql .= ",";
		}
		$sql .= ") ORDER BY num_cia,fecha ASC";
		$mov_lib = ejecutar_script($sql,$dsn);
		
		$tpl->newBlock("listado_final");
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
				$tpl->assign("folio",($mov_lib[$i]['folio'] > 0)?$mov_lib[$i]['folio']:"&nbsp;");
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
				$tpl->assign("folio",($mov_lib[$i]['folio'] > 0)?$mov_lib[$i]['folio']:"&nbsp;");
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
		$tpl->assign("listado_final.gran_total_deposito",number_format($gran_total_deposito,2,".",","));
		$tpl->assign("listado_final.gran_total_retiro",number_format($gran_total_retiro,2,".",","));
	} else {
		unset($_SESSION['con']);
		header("location: ./ban_conciliacion.php");
		die;
	}
	$tpl->printToScreen();
	
	unset($_SESSION['con']);
	die;
}

// Si se creo una nueva fecha de conciliación, obtener las compañías a conciliar
if (isset($_GET['fecha_con'])) {
	$_SESSION['con']['fecha_con'] = $_GET['fecha_con'];
	$_SESSION['con']['id_con_count'] = 0;
	// Obtener listado de compañías sin conciliar
	$cia = ejecutar_script("SELECT num_cia,nombre,clabe_cuenta FROM estado_cuenta JOIN catalogo_companias USING(num_cia) WHERE fecha_con IS NULL AND cuenta = 1 GROUP BY num_cia,nombre,clabe_cuenta ORDER BY num_cia ASC",$dsn);
	// Almacenar compañías en variables de sesión
	for ($i=0; $i<count($cia); $i++) {
		$_SESSION['con']['num_cia'.$i] = $cia[$i]['num_cia'];
		$_SESSION['con']['nombre_cia'.$i] = $cia[$i]['nombre'];
		$_SESSION['con']['cuenta'.$i] = $cia[$i]['clabe_cuenta'];
	}
	$_SESSION['con']['num_cias'] = count($cia); // Número de compañías
	$_SESSION['con']['next'] = 0; // Próxima compañía
}

// Si no existe la fecha de conciliación, crearla
if (!isset($_SESSION['con']['fecha_con'])) {
	$tpl->newBlock("fecha_con");
	
	$tpl->assign("fecha",date("d/m/Y",mktime(0,0,0,date("m"),date("d")-1,date("Y"))));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
	die;
}

// Trazar la pantalla de conciliacion para la compañía dada por $_SESSION[next] o por $_GET[num_cia]
if (isset($_SESSION['con']['next']) || isset($_GET['num_cia'])) {
	if (isset($_GET['accion']) && $_GET['accion'] == "siguiente")
		if ($_SESSION['con']['next'] < $_SESSION['con']['num_cias'] - 1) {
			$_SESSION['con']['next'] = $_SESSION['con']['next'] + 1;
			if (isset($_SESSION['check']))
				unset($_SESSION['check']);
		}
		else {
			$_SESSION['con']['next'] = 0;
			if (isset($_SESSION['check']))
				unset($_SESSION['check']);
		}
	else if (isset($_GET['accion']) && $_GET['accion'] == "ir_a") {
		$_SESSION['con']['next'] = $_GET['num_cia'];
		if (isset($_SESSION['check']))
				unset($_SESSION['check']);
	}
	
	// Crear el bloque para la conciliación
	$tpl->newBlock("conciliacion");
	
	// Obtener los cheques sin conciliar de una compañía especifica
	$che = ejecutar_script("SELECT id,fecha,folio,importe FROM estado_cuenta WHERE num_cia=".$_SESSION['con']['num_cia'.$_SESSION['con']['next']]." AND tipo_mov='TRUE' AND fecha_con IS NULL AND cuenta = 1 ORDER BY fecha ASC,importe DESC",$dsn);
	// Obtener los despositos sin conciliar de una compañía especifica
	$dep = ejecutar_script("SELECT estado_cuenta.id AS id,fecha,cod_mov,descripcion,importe FROM estado_cuenta LEFT JOIN catalogo_mov_bancos USING(cod_mov) WHERE cuenta = 1 AND num_cia=".$_SESSION['con']['num_cia'.$_SESSION['con']['next']]." AND estado_cuenta.tipo_mov='FALSE' AND fecha_con IS NULL GROUP BY importe,estado_cuenta.id,fecha,cod_mov,descripcion ORDER BY fecha ASC,importe DESC",$dsn);
	
	// Trazar datos de encabezado
	$tpl->assign("num_cia",   $_SESSION['con']['num_cia'.$_SESSION['con']['next']]);
	$tpl->assign("nombre_cia",$_SESSION['con']['nombre_cia'.$_SESSION['con']['next']]);
	$tpl->assign("cuenta",    $_SESSION['con']['cuenta'.$_SESSION['con']['next']]);
	$tpl->assign("fecha_con", $_SESSION['con']['fecha_con']);
	
	// Trazar columna de cheques
	if (!$che)
		$tpl->newBlock("sin_cheques");
	else {
		$tpl->newBlock("cheques");
		$tpl->assign("num_che",count($che));
		for ($i=0; $i<count($che); $i++) {
			$tpl->newBlock("fila_cheque");
			$tpl->assign("i",         $i);
			$tpl->assign("id",        $che[$i]['id']);
			$tpl->assign("fecha",     $che[$i]['fecha']);
			$tpl->assign("num_cheque",$che[$i]['folio']);
			$tpl->assign("monto",     $che[$i]['importe']);
			$tpl->assign("fmonto",    number_format($che[$i]['importe'],2,".",","));
			// Checar si no se ha marcado anteriormente
			if (isset($_SESSION['check']))
				for ($j=0; $j<count($_SESSION['check']); $j++)
					if ($che[$i]['id'] == $_SESSION['check']['id'.$j])
						$tpl->assign("checked","checked");
		}
	}
	
	// Trazar columna de depositos
	if (!$dep)
		$tpl->newBlock("sin_depositos");
	else {
		$tpl->newBlock("depositos");
		$tpl->assign("num_dep",count($dep));
		for ($i=0; $i<count($dep); $i++) {
			$tpl->newBlock("fila_deposito");
			$tpl->assign("i",          $i);
			$tpl->assign("id",         $dep[$i]['id']);
			$tpl->assign("fecha",      $dep[$i]['fecha']);
			$tpl->assign("cod_mov",    $dep[$i]['cod_mov']);
			$tpl->assign("descripcion",$dep[$i]['descripcion']);
			$tpl->assign("importe",    $dep[$i]['importe']);
			$tpl->assign("fimporte",   number_format($dep[$i]['importe'],2,".",","));
			// Checar si no se ha marcado anteriormente
			if (isset($_SESSION['check']))
				for ($j=0; $j<count($_SESSION['check']); $j++)
					if ($dep[$i]['id'] == $_SESSION['check']['id'.$j])
						$tpl->assign("checked","checked");
		}
	}
	
	// Generar listado desplegable de compañías
	for ($i=0; $i<$_SESSION['con']['num_cias']; $i++) {
		$tpl->newBlock("cia");
		$tpl->assign("index",$i);
		$tpl->assign("num_cia",$_SESSION['con']['num_cia'.$i]);
		$tpl->assign("nombre_cia",$_SESSION['con']['nombre_cia'.$i]);
		if ($_SESSION['con']['next'] == $i) $tpl->assign("selected","selected");
	}
	
	$tpl->printToScreen();
}
?>