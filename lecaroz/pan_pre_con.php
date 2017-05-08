<?php
// CONSULTA DE PRESTAMOS (PANADERIAS)
// Tablas 'prestamos'
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
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
$numfilas = 10;	// Número de filas en la captura

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_pre_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['tipo'])) {
	$tpl->newBlock("datos");
	
	if ($_SESSION['iduser'] == 1 || $_SESSION['iduser'] == 4 || $_SESSION['iduser'] == 62)
		$tpl->newBlock("admin");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		if ($_GET['codigo_error'] > 0)
			$tpl->assign("message","El empleado no. $_GET[codigo_error] ya tiene un prestamo");
		else
			$tpl->assign("message","No hay empleados para la compañía");
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
	die;
}

if ($_GET['tipo'] == "emp") {
	$sql = "SELECT id_empleado,num_emp,nombre,ap_paterno,ap_materno FROM prestamos LEFT JOIN catalogo_trabajadores ON (catalogo_trabajadores.id = prestamos.id_empleado) WHERE prestamos.num_cia = $_GET[num_cia] AND pagado = 'FALSE' GROUP BY id_empleado,num_emp,catalogo_trabajadores.nombre,catalogo_trabajadores.ap_paterno,catalogo_trabajadores.ap_materno ORDER BY id_empleado";
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		header("location: ./pan_pre_con.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("por_emp");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
	$tpl->assign("dia",(int)date("d"));
	$tpl->assign("mes",mes_escrito(date("n")));
	$tpl->assign("anio",date("Y"));
	
	$saldo_total = 0;
	$abonos_total = 0;
	for ($i=0; $i<count($result); $i++) {
		$id_empleado = $result[$i]['id_empleado'];
		
		$tpl->newBlock("fila_emp");
		$tpl->assign("num_emp",$result[$i]['num_emp']);
		$tpl->assign("nombre",$result[$i]['nombre']." ".$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']);
		// Obtener último prestamo
		$sql = "SELECT fecha,importe FROM prestamos WHERE id_empleado = $id_empleado AND tipo_mov = 'FALSE' AND pagado = 'FALSE'";
		$alta = ejecutar_script($sql,$dsn);
		$tpl->assign("fecha",$alta[0]['fecha']);
		// Obtener abonos
		$sql = "SELECT SUM(importe) FROM prestamos WHERE id_empleado = $id_empleado AND tipo_mov = 'TRUE' AND pagado = 'FALSE'";
		$abonos = ejecutar_script($sql,$dsn);
		$tpl->assign("saldo",number_format($alta[0]['importe']-$abonos[0]['sum'],2,".",","));
		$tpl->assign("abonos",$abonos[0]['sum'] > 0 ? number_format($abonos[0]['sum'],2,".",",") : "&nbsp;");
		// Obtener último abono
		$sql = "SELECT fecha,importe FROM prestamos WHERE id_empleado = $id_empleado AND tipo_mov = 'TRUE' AND pagado = 'FALSE' ORDER BY fecha DESC LIMIT 1";
		$ultimo = ejecutar_script($sql,$dsn);
		$tpl->assign("fecha_ultimo",$ultimo ? $ultimo[0]['fecha'] : "&nbsp;");
		$tpl->assign("importe",$ultimo ? number_format($ultimo[0]['importe'],2,".",",") : "&nbsp;");
		
		// MOD. 16/Mar/2006
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $ultimo ? $ultimo[0]['fecha'] : $alta[0]['fecha'], $fecha_ultimo);
		
		$current_ts = mktime(0, 0, 0, date("n"), date("d"), date("Y"));
		$ultimo_ts = mktime(0, 0, 0, $fecha_ultimo[2], $fecha_ultimo[1], $fecha_ultimo[3]);
		
		$dias = round(($current_ts - $ultimo_ts) / 86400);
		
		$tpl->assign("dias", $dias);
		
		$saldo_total += $alta[0]['importe']-$abonos[0]['sum'];
		$abonos_total += $abonos[0]['sum'];
	}
	$tpl->assign("por_emp.saldo_total", number_format($saldo_total, 2, ".", ","));
	//$tpl->assign("por_emp.abonos_total", number_format($abonos_total, 2, ".", ","));
	$tpl->printToScreen();
	die;
}
else if ($_GET['tipo'] == "cia") {
	// Saldo para una sola compañía
	if ($_GET['num_cia'] > 0) {
		$sql = "SELECT id_empleado,num_emp,nombre,ap_paterno,ap_materno FROM prestamos LEFT JOIN catalogo_trabajadores ON (catalogo_trabajadores.id = prestamos.id_empleado) WHERE prestamos.num_cia = $_GET[num_cia] AND pagado = 'FALSE' GROUP BY id_empleado,num_emp,catalogo_trabajadores.nombre,catalogo_trabajadores.ap_paterno,catalogo_trabajadores.ap_materno ORDER BY id_empleado";
		$result = ejecutar_script($sql,$dsn);
		
		if (!$result) {
			header("location: ./pan_pre_con.php?codigo_error=1");
			die;
		}
		
		$tpl->newBlock("saldo_cia");
		$tpl->assign("num_cia",$_GET['num_cia']);
		$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
		$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
		$tpl->assign("dia",(int)date("d"));
		$tpl->assign("mes",mes_escrito(date("n")));
		$tpl->assign("anio",date("Y"));
		
		$total = 0;
		for ($i=0; $i<count($result); $i++) {
			$id_empleado = $result[$i]['id_empleado'];
			
			$tpl->newBlock("fila_cia");
			$tpl->assign("num_emp",$result[$i]['num_emp']);
			$tpl->assign("nombre",$result[$i]['nombre']." ".$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']);
			// Obtener último prestamo
			$sql = "SELECT fecha,importe FROM prestamos WHERE id_empleado = $id_empleado AND tipo_mov = 'FALSE' AND pagado = 'FALSE'";
			$alta = ejecutar_script($sql,$dsn);
			// Obtener abonos
			$sql = "SELECT SUM(importe) FROM prestamos WHERE id_empleado = $id_empleado AND tipo_mov = 'TRUE' AND pagado = 'FALSE'";
			$abonos = ejecutar_script($sql,$dsn);
			$tpl->assign("saldo",number_format($alta[0]['importe']-$abonos[0]['sum'],2,".",","));
			$total += $alta[0]['importe']-$abonos[0]['sum'];
		}
		$tpl->assign("saldo_cia.total",number_format($total,2,".",","));
		$tpl->printToScreen();
		die;
	}
	else {
		$sql = "SELECT num_cia,nombre FROM prestamos LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia < 300 AND pagado = 'FALSE' GROUP BY num_cia,nombre ORDER BY num_cia";
		$result = ejecutar_script($sql,$dsn);
		
		if (!$result) {
			header("location: ./pan_pre_con.php?codigo_error=1");
			die;
		}
		
		$tpl->newBlock("saldo_all");
		$tpl->assign("dia",(int)date("d"));
		$tpl->assign("mes",mes_escrito(date("n")));
		$tpl->assign("anio",date("Y"));
		
		$total = 0;
		for ($i=0; $i<count($result); $i++) {
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("fila_all");
			$tpl->assign("num_cia",$result[$i]['num_cia']);
			$tpl->assign("nombre_cia",$result[$i]['nombre']);
			// Obtener último prestamo
			$sql = "SELECT SUM(importe) FROM prestamos WHERE num_cia = $num_cia AND tipo_mov = 'FALSE' AND pagado = 'FALSE'";
			$alta = ejecutar_script($sql,$dsn);
			// Obtener abonos
			$sql = "SELECT SUM(importe) FROM prestamos WHERE num_cia = $num_cia AND tipo_mov = 'TRUE' AND pagado = 'FALSE'";
			$abonos = ejecutar_script($sql,$dsn);
			$tpl->assign("saldo",number_format($alta[0]['sum']-$abonos[0]['sum'],2,".",","));
			$total += $alta[0]['sum']-$abonos[0]['sum'];
		}
		$tpl->assign("saldo_all.total",number_format($total,2,".",","));
		$tpl->printToScreen();
		die;
	}
}
else if ($_GET['tipo'] == "mov_emp") {
	// Obtener ID del empleado
	$id = ejecutar_script("SELECT id,ap_paterno,ap_materno,nombre,num_cia FROM catalogo_trabajadores WHERE num_emp = $_GET[num_emp] AND fecha_baja IS NULL AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 992': '1 AND 899'),$dsn);
	
	if (!$id) {
		header("location: ./pan_pre_con.php?codigo_error=1");
		die;
	}
	
	// Obtener movimientos de prestamos
	$result = ejecutar_script("SELECT * FROM prestamos WHERE id_empleado = {$id[0]['id']} AND pagado = 'FALSE' ORDER BY tipo_mov, fecha",$dsn);
	
	if (!$result) {
		header("location: ./pan_pre_con.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("saldo_emp");
	$tpl->assign("num_cia",$id[0]['num_cia']);
	$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = {$id[0]['num_cia']}",$dsn);
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
	$tpl->assign("num_emp", $_GET['num_emp']);
	$tpl->assign("nombre_emp", trim("{$id[0]['ap_paterno']} {$id[0]['ap_materno']} {$id[0]['nombre']}"));
	
	$total = 0;
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila_mov");
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("tipo", $result[$i]['tipo_mov'] == "f" ? "<font color='#FF0000'>PRESTAMO</font>" : "<font color='#0000FF'>ABONO</font>");
		$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
		
		$total += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : -$result[$i]['importe'];
	}
	$tpl->assign("saldo_emp.total", number_format($total, 2, ".", ","));
	
	$tpl->printToScreen();
	die;
}
/*else if ($_GET['tipo'] == "esc") {
	// 
}*/

?>