<?php
// CARGOS MANUALES
// Tabla 'estado_cuenta'
// Menu

define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "El código no existe en la Base de Datos.";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_car_cap.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (isset($_GET['tabla'])) {
	// Almacenar registros temporalmente
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		$_SESSION['car']['num_cia'.$i] = $_POST['num_cia'.$i];
		$_SESSION['car']['nombre_cia'.$i] = $_POST['nombre_cia'.$i];
		$_SESSION['car']['cod_mov'.$i] = $_POST['cod_mov'.$i];
		$_SESSION['car']['fecha_mov'.$i] = $_POST['fecha_mov'.$i];
		$_SESSION['car']['importe'.$i] = $_POST['importe'.$i];
		$_SESSION['car']['concepto'.$i] = $_POST['concepto'.$i];
	}
	
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['num_cia'.$i] > 0 && $_POST['fecha_mov'.$i] != "" && $_POST['importe'.$i] > 0) {
			$ret['num_cia'] = $_POST['num_cia'.$i];
			$ret['cod_mov'] = $_POST['cod_mov'.$i];
			$ret['fecha_mov'] = $_POST['fecha_mov'.$i];
			$ret['importe'] = $_POST['importe'.$i];
			if ($_POST['concepto'.$i] != "")
				$ret['concepto'] = $_POST['concepto'.$i];
			else {
				$concepto = ejecutar_script("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = ".$_POST['cod_mov'.$i],$dsn);
				$ret['concepto'] = $concepto[0]['descripcion'];
			}
			$ret['fecha_cap'] = date("d/m/Y");
			$ret['manual'] = "TRUE";
			$ret['imprimir'] = "TRUE";
			
			$cuenta['num_cia'] = $_POST['num_cia'.$i];
			$cuenta['fecha'] = $_POST['fecha_mov'.$i];
			$cuenta['fecha_con'] = "";
			if ($_POST['concepto'.$i] != "")
				$cuenta['concepto'] = $_POST['concepto'.$i];
			else
				$cuenta['concepto'] = $concepto[0]['descripcion'];
			$cuenta['tipo_mov'] = "TRUE";
			$cuenta['importe'] = $_POST['importe'.$i];
			$cuenta['saldo_ini'] = "";
			$cuenta['saldo_fin'] = "";
			$cuenta['cod_mov'] = $_POST['cod_mov'.$i];
			$cuenta['folio'] = "";
			$cuenta['cuenta'] = 1;
			
			$db_ret = new DBclass($dsn,"retiros",$ret);
			$db_ret->generar_script_insert("");
			$db_ret->ejecutar_script();
			
			$db_cuenta = new DBclass($dsn,"estado_cuenta",$cuenta);
			$db_cuenta->generar_script_insert("");
			$db_cuenta->ejecutar_script();
			
			// Actualizar saldo en libros
			if (existe_registro("saldos",array("num_cia"),array($_POST['num_cia'.$i]),$dsn))
				ejecutar_script("UPDATE saldos SET saldo_libros=saldo_libros-".$_POST['importe'.$i]." WHERE num_cia=".$_POST['num_cia'.$i]." AND cuenta = 1",$dsn);
			else
				ejecutar_script("INSERT INTO saldos (num_cia,saldo_libros,saldo_bancos,cuenta) VALUES (".$_POST['num_cia'.$i].",".$_POST['importe'.$i].",0,1)",$dsn);
			
			unset($db_ret);
			unset($db_cuenta);
		}
	}
	// Desplegar listado de depositos
	$tpl->newBlock("listado");
	
	$sql = "SELECT * FROM retiros WHERE fecha_cap = '".date("d/m/Y")."' AND manual = 'TRUE' AND imprimir = 'TRUE' ORDER BY num_cia,fecha_mov";
	$result = ejecutar_script($sql,$dsn);
	
	ejecutar_script("UPDATE retiros SET imprimir = 'FALSE' WHERE fecha_cap = '".date("d/m/Y")."' AND manual = 'TRUE' AND imprimir = 'TRUE'",$dsn);
	
	if ($result) {
		$tpl->assign("dia",date("d"));
		$tpl->assign("anio",date("Y"));
		switch (date("m")) {
			case 1: $mes = "Enero"; break;
			case 2: $mes = "Febrero"; break;
			case 3: $mes = "Marzo"; break;
			case 4: $mes = "Abril"; break;
			case 5: $mes = "Mayo"; break;
			case 6: $mes = "Junio"; break;
			case 7: $mes = "Julio"; break;
			case 8: $mes = "Agosto"; break;
			case 9: $mes = "Septiembre"; break;
			case 10: $mes = "Octubre"; break;
			case 11: $mes = "Noviembre"; break;
			case 12: $mes = "Diciembre"; break;
		}
		$tpl->assign("mes",$mes);
		
		$total = 0;
		for ($i=0; $i<count($result); $i++) {
			$tpl->newBlock("fila_lis");
			
			$cia = ejecutar_script("SELECT nombre,clabe_cuenta FROM catalogo_companias WHERE num_cia = ".$result[$i]['num_cia'],$dsn);
			$tpl->assign("num_cia",$result[$i]['num_cia']);
			$tpl->assign("cuenta",$cia[0]['clabe_cuenta']);
			$tpl->assign("nombre",$cia[0]['nombre']);
			$tpl->assign("cod_mov",$result[$i]['cod_mov']);
			$cod_mov = ejecutar_script("SELECT DISTINCT ON (cod_mov) descripcion FROM catalogo_mov_bancos WHERE cod_mov = ".$result[$i]['cod_mov'],$dsn);
			$tpl->assign("descripcion",$cod_mov[0]['descripcion']);
			$tpl->assign("concepto",$result[$i]['concepto']);
			$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
			$tpl->assign("fecha",$result[$i]['fecha_mov']);
			$total += $result[$i]['importe'];
		}
		$tpl->assign("listado.total",number_format($total,2,".",","));
		$tpl->printToScreen();
		die;
	}
	unset($_SESSION['dep']);
}

if (!isset($_GET['numfilas'])) {
	if (isset($_SESSION['car']))
		unset($_SESSION['car']);
	
	$tpl->newBlock("numfilas");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("captura");
$tpl->assign("tabla","estado_cuenta");
$tpl->assign("numfilas",$_GET['numfilas']);

$mov = ejecutar_script("SELECT DISTINCT ON (cod_mov) cod_mov,descripcion FROM catalogo_mov_bancos WHERE tipo_mov='TRUE' ORDER BY cod_mov",$dsn);

$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias ORDER BY num_cia ASC",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia_ini");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
}

for ($i=0; $i<$_GET['numfilas']; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",$_GET['numfilas']-1);
	
	if ($i < $_GET['numfilas']-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
		
	$tpl->assign("num_cia",isset($_SESSION['car'])?$_SESSION['car']['num_cia'.$i]:"");
	$tpl->assign("nombre_cia",isset($_SESSION['car'])?$_SESSION['car']['nombre_cia'.$i]:"");
	$tpl->assign("fecha_mov",isset($_SESSION['car'])?$_SESSION['car']['fecha_mov'.$i]:date("d/m/Y"));
	$tpl->assign("importe",isset($_SESSION['car'])?$_SESSION['car']['importe'.$i]:"");
	$tpl->assign("concepto",isset($_SESSION['car'])?$_SESSION['car']['concepto'.$i]:"");

	for ($j=0; $j<count($mov); $j++) {
		$tpl->newBlock("mov");
		$tpl->assign("id",$mov[$j]['cod_mov']);
		$tpl->assign("descripcion",$mov[$j]['descripcion']);
		$tpl->assign("selected",isset($_SESSION['car'])?(($_SESSION['car']['cod_mov'.$i] == $mov[$j]['cod_mov'])?"selected":""):"");
	}
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", "La compañía no. $_GET[codigo_error] no tiene saldo inicial");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

// Imprimir el resultado
$tpl->printToScreen();
?>