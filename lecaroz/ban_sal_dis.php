<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no se encuentra en la Base de Datos";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_sal_dis.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['cuenta'])) {
	$clabe_cuenta = $_GET['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";
	
	// Obtener pendientes por pagar
	$sql = "SELECT num_cia, nombre, $clabe_cuenta, sum(importe) AS pendientes, saldo_bancos FROM estado_cuenta LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= " LEFT JOIN saldos USING (num_cia, cuenta) WHERE tipo_mov = 'TRUE' AND fecha_con IS NULL AND cod_mov IN (5, 41) AND cuenta = $_GET[cuenta]";
	$sql .= in_array($_SESSION['iduser'], $users) ? " AND num_cia BETWEEN 900 AND 998" : "";
	$sql .= " GROUP BY num_cia, nombre, $clabe_cuenta, saldo_bancos ORDER BY num_cia";
	$pendiente = $db->query($sql);
	
	if ($pendiente) {
		$tpl->newBlock("listado");
		$tpl->assign("dia", date("d"));
		$tpl->assign("anio", date("Y"));
		$tpl->assign("mes", mes_escrito(date("n")));
		$tpl->assign("hora", date('h:ia'));
		
		if (date("d") > 6) {
			$fecha1 = date("1/m/Y");
			$fecha2 = date("d/m/Y");
		}
		else {
			$fecha1 = date("d/m/Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
			$fecha2 = date("d/m/Y", mktime(0, 0, 0, date("m"), 0, date("Y")));
		}
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha2, $temp);
		$dia = $temp[1];
		$mes = $temp[2];
		$anio = $temp[3];
		
		$anio = date("Y");
		
		for ($i=0; $i<count($pendiente); $i++) {
			if ($pendiente[$i]['saldo_bancos'] < $pendiente[$i]['pendientes']) {
				$tpl->newBlock("fila");
				$tpl->assign("dia", $dia);
				$tpl->assign("mes", $mes);
				$tpl->assign("anio", $anio);
				$tpl->assign("cuenta_url", $_GET['cuenta']);
				$tpl->assign("num_cia", $pendiente[$i]['num_cia']);
				$tpl->assign("cuenta", $pendiente[$i][$clabe_cuenta]);
				$tpl->assign("nombre", $pendiente[$i]['nombre']);
				$tpl->assign("saldo", number_format($pendiente[$i]['saldo_bancos'], 2, ".", ","));
				$tpl->assign("pendientes", number_format($pendiente[$i]['pendientes'], 2, ".", ","));
				$tpl->assign("diferencia", number_format($pendiente[$i]['pendientes'] - $pendiente[$i]['saldo_bancos'], 2, ".", ","));
			}
		}
	}
	else
		$tpl->newBlock("no_listado");
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

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
?>