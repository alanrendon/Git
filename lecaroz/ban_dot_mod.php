<?php
// LISTADO DE OTROS DEPOSITOS
// Tabla 'estado_cuenta'
// Menu

define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

$users = array(28, 29, 30, 31);

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dot_mod.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['mes'])) {
	$tpl->newBlock("fecha");
	$tpl->assign(date("n"),"selected");
	$tpl->assign("anio",date("Y"));
	$tpl->printToScreen();
	die;
}

//$fecha_cap = $_GET['fecha'];
$fecha1 = "1/$_GET[mes]/$_GET[anio]";
$fecha2 = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

// Verificar si hay registros de la fecha de captura
//$result = ejecutar_script("SELECT * FROM otros_depositos WHERE fecha_cap='$fecha_cap' LIMIT 1",$dsn);
$result = ejecutar_script("SELECT * FROM otros_depositos WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 950" : "1 AND 800") ." LIMIT 1",$dsn);
if (!$result) {
	$tpl->newBlock("no_listado");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("listado");

$tpl->newBlock("desglozado");

// Generar listado de depositos capturados
$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE";
if ($_GET['num_cia'] > 0)
	$sql .= " num_cia = $_GET[num_cia]";
else
	$sql .= " (num_cia < 100 OR num_cia > 200 OR num_cia IN (108,126))";
if (in_array($_SESSION['iduser'], $users))
	$sql .= " AND num_cia BETWEEN 900 AND 950";
else
	$sql .= " AND num_cia BETWEEN 1 AND 800";
$sql .= " ORDER BY num_cia";

$cia = ejecutar_script($sql,$dsn);

if ($cia) {
	for ($i=0; $i<count($cia); $i++) {
		// Consultar si la panaderia tiene rosticerias
		$dep = ejecutar_script("SELECT * FROM dependencia_cia WHERE cia_primaria = {$cia[$i]['num_cia']} AND cia_secundaria != {$cia[$i]['num_cia']} ORDER BY cia_secundaria",$dsn);
		
		// Consultar depositos de la compañía primaria
		//$sql = "SELECT * FROM otros_depositos WHERE num_cia = ".$cia[$i]['num_cia']." AND fecha_cap = '$fecha_cap' AND acumulado = 'TRUE'";
		$sql = "SELECT * FROM otros_depositos WHERE num_cia = ".$cia[$i]['num_cia']." AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$pan = ejecutar_script($sql,$dsn);
		
		// Consultar depositos de la compañia secundaria
		if ($dep) {
			$sql = "SELECT * FROM otros_depositos WHERE num_cia IN (";
			for ($j=0; $j<count($dep); $j++) {
				$sql .= $dep[$j]['cia_secundaria'];
				if ($j < count($dep)-1) $sql .= ",";
			}
			//$sql .= ") AND fecha_cap = '$fecha_cap' AND acumulado = 'TRUE' ORDER BY num_cia";
			$sql .= ") AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY num_cia";
			$ros = ejecutar_script($sql,$dsn);
		}
		else
			$ros = FALSE;
		
		if ($pan || $ros) {
			$tpl->newBlock("grupo_des");
			$rows = 0;
			$total = 0;
			if ($pan) {
				for ($c=0; $c<count($pan); $c++) {
					$tpl->newBlock("fila_des");
					$tpl->assign("id",$pan[$c]['id']);
					$tpl->assign("num_cia",$cia[$i]['num_cia']);
					$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
					$tpl->assign("fecha",$pan[$c]['fecha']);
					$tpl->assign("fecha_cap", $pan[$c]['fecha_cap']);
					$tpl->assign("concepto", $pan[$c]['concepto'] != "" ? $pan[$c]['concepto'] : "&nbsp;");
					$tpl->assign("deposito",number_format($pan[$c]['importe'],2,".",","));
					if ($pan[$c]['idnombre'] > 0) $nombre = ejecutar_script("SELECT nombre FROM catalogo_nombres WHERE id = {$pan[$c]['idnombre']}",$dsn);
					$tpl->assign("nombre", $pan[$c]['idnombre'] > 0 ? $nombre[0]['nombre'] : '&nbsp;');
					if ($pan[$c]['acre'] > 0) $acre = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$pan[$c]['acre']}",$dsn);
					$tpl->assign("acre", $pan[$c]['acre'] > 0 ? $acre[0]['nombre_corto'] : '&nbsp;');
					
					$total += $pan[$c]['importe'];
					$rows++;
				}
			}
			if ($ros) {
				for ($r=0; $r<count($ros); $r++) {
					$tpl->newBlock("fila_des");
					$tpl->assign("id",$ros[$r]['id']);
					$temp = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = ".$ros[$r]['num_cia'],$dsn);
					$tpl->assign("num_cia",$ros[$r]['num_cia']);
					$tpl->assign("nombre_cia",$temp[0]['nombre_corto']);
					$tpl->assign("fecha",$ros[$r]['fecha']);
					$tpl->assign("fecha_cap", $ros[$r]['fecha_cap']);
					$tpl->assign("concepto", $ros[$r]['concepto'] != "" ? $ros[$r]['concepto'] : "&nbsp;");
					$tpl->assign("deposito",number_format($ros[$r]['importe'],2,".",","));
					$total += $ros[$r]['importe'];
					$rows++;
				}
			}
			if ($rows > 1) {
				$tpl->newBlock("total_des");
				$tpl->assign("total",number_format($total,2,".",","));
			}
		}
	}
}
$tpl->printToScreen();
?>