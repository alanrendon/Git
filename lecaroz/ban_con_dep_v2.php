<?php
// CONCILIACION DE EFECTIVOS
// Tablas 'estado_cuenta'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/ban/ban_con_dep_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

function toInt($value) {
	return intval($value, 10);
}

if (isset($_GET['accion']) && $_GET['accion'] == "terminar") {
	unset($_SESSION['efe']);
	unset($_SESSION['no_efe']);
}

// Inicializar fecha y listado de compañías
if (isset($_GET['fecha'])) {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$_SESSION['efe']['fecha1'] = "1/$fecha[2]/$fecha[3]";
	$_SESSION['efe']['fecha2'] = $_GET['fecha'];
	$_SESSION['efe']['dia'] = intval($fecha[1], 10);
	$_SESSION['efe']['mes'] = intval($fecha[2], 10);
	$_SESSION['efe']['anio'] = intval($fecha[3], 10);
	$_SESSION['efe']['tipo_otros'] = $_GET['tipo_otros'];
	
	for ($i=0; $i<10; $i++)
		if ($_GET['num_cia'.$i] > 0)
			$_SESSION['no_efe']['num_cia'.$i] = $_GET['num_cia'.$i];
	
	$cia = $db->query("SELECT num_cia,nombre_corto AS nombre_cia,catalogo_operadoras.nombre AS operadora, turno_cometra FROM catalogo_companias LEFT JOIN catalogo_operadoras USING(idoperadora)" . (in_array($_SESSION['iduser'], $users) ? " WHERE num_cia BETWEEN 900 AND 998" : "") . " ORDER BY num_cia ASC");
	for ($i=0; $i<count($cia); $i++) {
		$_SESSION['efe']['num_cia'.$i] = $cia[$i]['num_cia'];
		$_SESSION['efe']['nombre_cia'.$i] = $cia[$i]['nombre_cia'];
		$_SESSION['efe']['operadora'.$i] = $cia[$i]['operadora'];
		$_SESSION['efe']['turno_cometra'.$i] = $cia[$i]['turno_cometra'];
	}
	$_SESSION['efe']['num_cias'] = count($cia);
	$_SESSION['efe']['next'] = 0;
}

// Si no existe mes de conciliación, inicializarlo
if (!isset($_SESSION['efe']['fecha1'])) {
	$tpl->newBlock("mes");
	$tpl->assign("fecha", date("d/m/Y", mktime(0, 0, 0, date('n'), date('d') - 2, date('Y'))));
	
	$tpl->printToScreen();
	die;
}

if (isset($_SESSION['efe']['fecha1']) || isset($_GET['num_cia'])) {
	if (isset($_GET['accion']) && $_GET['accion'] == "siguiente")
		if ($_SESSION['efe']['next'] < $_SESSION['efe']['num_cias'] - 1) {
			$_SESSION['efe']['next'] = $_SESSION['efe']['next'] + 1;
		}
		else {
			$_SESSION['efe']['next'] = 0;
		}
	else if (isset($_GET['accion']) && $_GET['accion'] == "ir_a") {
		if (isset($_SESSION['efe']['num_cia'.$_GET['idcia']]))
			$_SESSION['efe']['next'] = $_GET['idcia'];
	}
	
	// Crear bloque para la compañía actual
	$tpl->newBlock("cia");
	
	$tpl->assign("num_cia",$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]);
	$tpl->assign("nombre_cia",$_SESSION['efe']['nombre_cia'.$_SESSION['efe']['next']]);
	$tpl->assign("operadora",$_SESSION['efe']['operadora'.$_SESSION['efe']['next']]);
	$enc = $db->query("SELECT nombre_fin FROM encargados WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} ORDER BY anio DESC, mes DESC LIMIT 1");
	$tpl->assign("encargado", strtoupper($enc[0]['nombre_fin']));
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_SESSION['efe']['fecha1'],$temp);
	switch ($temp[2]) {
		case 1: $mes = "ENERO"; break;
		case 2: $mes = "FEBRERO"; break;
		case 3: $mes = "MARZO"; break;
		case 4: $mes = "ABRIL"; break;
		case 5: $mes = "MAYO"; break;
		case 6: $mes = "JUNIO"; break;
		case 7: $mes = "JULIO"; break;
		case 8: $mes = "AGOSTO"; break;
		case 9: $mes = "SEPTIEMBRE"; break;
		case 10: $mes = "OCTUBRE"; break;
		case 11: $mes = "NOVIEMBRE"; break;
		case 12: $mes = "DICIEMBRE"; break;
	}
	$tpl->assign("mes_escrito",$mes);
	
	// Generar listado de companias
	for ($i=0; $i<$_SESSION['efe']['num_cias']; $i++) {
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$_SESSION['efe']['num_cia'.$i]);
		$tpl->assign("nombre_cia",$_SESSION['efe']['nombre_cia'.$i]);
		$tpl->assign("idcia",$i);
	}
	$tpl->gotoBlock("cia");
	
	// Obtener efectivos de la compañía para el mes dado (dependiendo de si es panaderia o rosticería)
	// [10-Septiembre-2008] Nuevos rangos: Panaderias 1 a 300, Rosticerias 301 a 599
	if (($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] >= 301 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 599) || $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 704 || $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 702 || $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 705)
		$sql = "SELECT num_cia, efectivo, extract(day FROM fecha) AS dia, fecha, 't' AS efe, 't' AS exp, 't' AS gas, 't' AS pro, 't' AS pas, 1 AS status FROM total_companias WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha";
	else if ($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] >= 900 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 998)
		$sql = "SELECT num_cia, efectivo, extract(day from fecha) AS dia, fecha, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS efe, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS exp, 't' AS gas, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS pro, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS pas, 1 AS status FROM total_zapaterias WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN'{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha";
	else
		$sql = "SELECT num_cia, efectivo + (CASE WHEN num_cia IN (31, 32, 33, 34, 73, 121) AND EXTRACT(year FROM fecha) = 2012 AND EXTRACT(month FROM fecha) = 9 THEN COALESCE((SELECT importe FROM cometra WHERE comprobante IN (41355658, 40759126) AND num_cia = total_panaderias.num_cia AND fecha = total_panaderias.fecha), 0) ELSE 0 END) AS efectivo, extract(day FROM fecha) AS dia, fecha, efe, exp, gas, pro, pas, status FROM total_panaderias WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha";
	$efectivo = $db->query($sql);//echo '<pre>' . print_r($efectivo, TRUE) . '</pre>';
	
	// [06-Feb-2008] Si no hay efectivos, buscar los capturados y ponerlos como 0
	if (!$efectivo) {
		$sql = "SELECT num_cia, 0 AS efectivo, extract(day FROM fecha) AS dia, fecha, 'FALSE' AS efe, 'FALSE' AS exp, 'FALSE' AS gas, 'FALSE' AS pro, 'FALSE' AS pas FROM importe_efectivos WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha";
		$efectivo = $db->query($sql);
	}
	
	if ($efectivo) {
		// [14-Feb-2007] Obtener efectivos directos posteriores al último día de efectivos
		$last_date = $efectivo[count($efectivo) - 1]['fecha'];
		
		$sql = "SELECT num_cia, importe AS efectivo, extract(day from fecha) AS dia, fecha, 'FALSE' AS efe, 'FALSE' AS exp, 'FALSE' AS gas, 'FALSE' AS pro, 'FALSE' AS pas";
		$sql .= " FROM importe_efectivos WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha > '$last_date' AND fecha <= '{$_SESSION['efe']['fecha2']}' ORDER BY fecha";
		$direct = $db->query($sql);//echo '<pre>' . print_r($direct, TRUE) . '</pre>';
		if ($direct)
			foreach ($direct as $reg)
				$efectivo[] = $reg;
		
		/*
		* Agregar un día para las compañías donde cometra recoge en la tarde
		*/
		
		list($last_day, $last_month, $last_year) = array_map('toInt', explode('/', $efectivo[count($efectivo) - 1]['fecha']));
		
		if ($_SESSION['efe']['turno_cometra'.$_SESSION['efe']['next']] == 2 && $last_day < date('j', mktime(0, 0, 0, $last_month + 1, 0, $last_year))) {
			$efectivo[] = array(
				'num_cia' => $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']],
				'efectivo' => 0,
				'dia' => $last_day + 1,
				'fecha' => date('d/m/Y', mktime(0, 0, 0, $last_month, $last_day + 1, $last_year)),
				'efe' => 't',
				'exp' => 't',
				'gas' => 't',
				'pro' => 't',
				'pas' => 't',
				'extra' => TRUE
			);
			
			$last_date = date('d/m/Y', mktime(0, 0, 0, $last_month, $last_day + 1, $last_year));
		}
		
		$tpl->newBlock("tabla");
		
		// [2006/07/10] Mancomunar cias. 100 a 200 con 201 a 300
		/*$tmp = $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] >= 100 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 200 ? "IN ({$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}, " . ($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] + 100) . ")" : "= {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}";
		$sql = "SELECT id, importe, fecha_con, extract(day FROM fecha) AS dia, cod_mov, CASE WHEN concepto LIKE '%DEPOSITO COMETRA%' THEN 1 ELSE 0 END AS con FROM estado_cuenta WHERE cod_mov IN (1, 16, 44) AND num_cia $tmp AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha, importe DESC";*/
		
		// [27-Mar-2008] Modificada la consulta de depositos para incluir los que estan en otras cuentas y omitir los que han sido movidos
		/*$tmp = $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] >= 100 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 200 ? "(num_cia IN ({$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}, " . ($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] + 100) . ") AND num_cia_sec IS NULL) OR num_cia_sec IN ({$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}, " . ($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] + 100) . ")" : "(num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND num_cia_sec IS NULL) OR num_cia_sec = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}";
		$sql = "SELECT id, importe, fecha_con, extract(day FROM fecha) AS dia, cod_mov, CASE WHEN concepto LIKE '%DEPOSITO COMETRA%' THEN 1 ELSE 0 END AS con, CASE WHEN num_cia_sec IS NULL THEN 0 WHEN num_cia <> num_cia_sec THEN 1 END AS sec FROM estado_cuenta WHERE cod_mov IN (1, 16, 44, 99) AND ($tmp) AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha, importe DESC";*/
		
		/*
		@ [23-May-2011] Para las compañías 11, 303, 353, 355 tomar en cuenta los depósitos de las compañías 810, 811, 812, 813 respectivamente cuando el periodo solicitado es menor al 31 de mayo de 2011
		*/
		if (mktime(0, 0, 0, $_SESSION['efe']['mes'], $_SESSION['efe']['dia'], $_SESSION['efe']['anio']) <= mktime(0, 0, 0, 5, 31, 2011) && in_array($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']], array(11, 303, 353, 355))) {
			$cias = array(
				11  => '11, 810',
				303 => '303, 811',
				353 => '353, 812',
				355 => '355, 813'
			);
			
			$sql = "
				SELECT
					id,
					importe,
					fecha_con,
					extract(day FROM fecha)
						AS dia,
					cod_mov,
					CASE
						WHEN concepto LIKE '%DEPOSITO COMETRA%' THEN
							1
						ELSE
							0
					END
						AS con,
					CASE
						WHEN num_cia_sec IS NULL THEN
							0
						WHEN num_cia <> num_cia_sec THEN
							1
					END
						AS sec
				FROM
					estado_cuenta
				WHERE
					cod_mov IN (1, 16, 44, 99)
					AND (
						(
							num_cia IN ({$cias[$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]]})
							AND num_cia_sec IS NULL
						)
						OR num_cia_sec IN ({$cias[$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]]})
					)
					AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}'
				ORDER BY
					fecha,
					importe DESC";
		}
		else {
			$sql = "
				SELECT
					id,
					importe,
					fecha_con,
					extract(day FROM fecha)
						AS dia,
					cod_mov,
					CASE
						WHEN concepto LIKE '%DEPOSITO COMETRA%' THEN
							1
						ELSE
							0
					END
						AS con,
					CASE
						WHEN num_cia_sec IS NULL THEN
							0
						WHEN num_cia <> num_cia_sec THEN
							1
					END
						AS sec
				FROM
					estado_cuenta
				WHERE
					cod_mov IN (1, 16, 44, 99)
					AND (
						(
							num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}
							AND num_cia_sec IS NULL
						)
						OR num_cia_sec = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}
					)
					AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}'::DATE AND '{$_SESSION['efe']['fecha2']}'::DATE" . ($_SESSION['efe']['turno_cometra'.$_SESSION['efe']['next']] == 2 && $last_day < date('j', mktime(0, 0, 0, $last_month + 1, 0, $last_year)) ? ' + INTERVAL \'1 DAY\'' : '') . "
				ORDER BY
					fecha,
					importe DESC";
		}
		
		$depositos = $db->query($sql);
		/*$sql = "SELECT importe, fecha_con, extract(day FROM fecha) AS dia FROM estado_cuenta WHERE cod_mov IN (1,16,44) AND num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha, importe DESC";
		$depositos = $db->query($sql);*/
		
		$sql = "SELECT dep1, dep2, extract(day FROM fecha) AS dia, fecha, 1 AS cod_mov FROM depositos_alternativos WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha";
		$alternativos = $db->query($sql);
		
		$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}'";
		$temp = $db->query($sql);
		$otros_depositos = $temp[0]['sum'] != 0 ? $temp[0]['sum'] : 0;
		
		// [22-Nov-2007] Obtener otros depósitos por día
		$sql = "SELECT sum(importe) AS importe, extract(day from fecha) AS dia FROM otros_depositos WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' GROUP BY dia ORDER BY dia";
		$otros_dep_dia = array();
		if ($tmp = $db->query($sql))
			foreach ($tmp as $reg)
				$otros_dep_dia[$reg['dia']] = $reg['importe'];
		
		$num_dep = 0;
		$temp_dia = NULL;
		$temp_dep = 0;
		// Obtener el número de columnas para los depósitos
		for ($i=0; $i<count($depositos); $i++) {
			if (in_array($depositos[$i]['cod_mov'], array(1, 16, 99))) {
				if ($temp_dia != $depositos[$i]['dia']) {
					if ($temp_dep > $num_dep) $num_dep = $temp_dep;
					
					$temp_dia = $depositos[$i]['dia'];
					$temp_dep = 0;
				}
				$temp_dep++;
			}
		}
		if ($temp_dep > $num_dep) $num_dep = $temp_dep;
		
		$num_tar = 0;
		$temp_dia = NULL;
		$temp_tar = 0;
		// [06-Ago-2007] Obtener el número de columnas para las tarjetas de crédito
		for ($i=0; $i<count($depositos); $i++) {
			if (in_array($depositos[$i]['cod_mov'], array(44))) {
				if ($temp_dia != $depositos[$i]['dia'] && in_array($depositos[$i]['cod_mov'], array(44))) {
					if ($temp_tar > $num_tar) $num_tar = $temp_tar;
					
					$temp_dia = $depositos[$i]['dia'];
					$temp_tar = 0;
				}
				$temp_tar++;
			}
		}
		if ($temp_tar > $num_tar) $num_tar = $temp_tar;//echo '<pre>' . print_r($depositos, TRUE) . '</pre>';
		
		// En caso de que los depositos sean alternativos
		if (isset($_SESSION['no_efe']) && $key = array_search($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']],$_SESSION['no_efe'])) {
			$depositos = FALSE;
			$num_dep = 0;
		}
		
		$temp = $db->query("SELECT SUM(dep1) AS dep1,SUM(dep2) AS dep2 FROM depositos_alternativos WHERE num_cia={$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}'");
		if ($temp[0]['dep1'] > 0) $num_dep++;
		if ($temp[0]['dep2'] > 0) $num_dep++;
		
		// Trazar datos
		$total_efectivos = 0;
		$total_otros = 0;
		$total_diferencias = 0;
		$gran_total = 0;
		$total_depositos = array();
		$total_tarjetas = array();
		
		$num_depositos = array();
		$num_tarjetas = array();
		$num_otros = 0;
		
		for ($i=0; $i<$num_dep; $i++) {
			$total_depositos[$i] = 0;
			$num_depositos[$i] = 0;
		}
		
		for ($i = 0; $i < $num_tar; $i++) {
			$total_tarjetas[$i] = 0;
			$num_tarjetas[$i] = 0;
		}
		
		function buscarDep($dia) {
			global $depositos, $alternativos;
			
			if (!$depositos && !$alternativos)
				return FALSE;
			
			$dep = array();
			$count = 0;
			for ($i = 0; $i < count($depositos); $i++)
				if ($dia == $depositos[$i]['dia'] && in_array($depositos[$i]['cod_mov'], array(1, 16, 99))) {
					$dep[] = array('id' => $depositos[$i]['id'], 'importe' => $depositos[$i]['importe'], 'fecha_con' => $depositos[$i]['fecha_con'], 'cod_mov' => $depositos[$i]['cod_mov'], 'con' => $depositos[$i]['con'], 'sec' => $depositos[$i]['sec']);
					$count++;
				}
			
			for ($i = 0; $i < count($alternativos); $i++)
				if ($dia == $alternativos[$i]['dia']) {
					if ($alternativos[$i]['dep1'] > 0) {
						$dep[] = array('id' => 0, 'importe' => $alternativos[$i]['dep1'], 'fecha_con' => $alternativos[$i]['fecha'], 'cod_mov' => 1, 'con' => 1, 'sec' => 0);
						$count++;
					}
					if ($alternativos[$i]['dep2'] > 0) {
						$dep[] = array('id' => 0, 'importe' => $alternativos[$i]['dep2'], 'fecha_con' => $alternativos[$i]['fecha'], 'cod_mov' => 1, 'con' => 1, 'sec' => 0);
						$count++;
					}
				}
			
			return $count > 0 ? $dep : FALSE;
		}
		
		function buscarTar($dia) {
			global $depositos;
			
			if (!$depositos)
				return FALSE;
			
			$dep = array();
			$count = 0;
			for ($i = 0; $i < count($depositos); $i++)
				if ($dia == $depositos[$i]['dia'] && in_array($depositos[$i]['cod_mov'], array(44))) {
					$dep[] = array('id' => $depositos[$i]['id'], 'importe' => $depositos[$i]['importe'], 'fecha_con' => $depositos[$i]['fecha_con'], 'cod_mov' => $depositos[$i]['cod_mov'], 'sec' => $depositos[$i]['sec']);
					$count++;
				}
			
			return $count > 0 ? $dep : FALSE;
		}
		
		for ($i=0; $i<count($efectivo); $i++) {
			
			// Buscar los depositos para x día
			$deposito = buscarDep($efectivo[$i]['dia']);
			$tarjeta = buscarTar($efectivo[$i]['dia']);
			
			// Trazar nueva fila
			$tpl->newBlock("fila");
			$tpl->assign("dia",(int)$efectivo[$i]['dia']);
			// Efectivo incompleto
			if (!($efectivo[$i]['efe'] == "t" &&
				  $efectivo[$i]['exp'] == "t" &&
				  $efectivo[$i]['gas'] == "t" &&
				  $efectivo[$i]['pro'] == "t" &&
				  $efectivo[$i]['pas'] == "t")) {
				// Buscar efectivo directo y asignarlo si lo hay
				$sql = "SELECT importe FROM importe_efectivos WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha = '{$efectivo[$i]['fecha']}'";
				if ($directo = $db->query($sql))
					$efectivo[$i]['efectivo'] = $directo[0]['importe'];
				
				$tpl->assign("bgcolor", $directo ? "bgcolor=\"#FFFF00\"" : "bgcolor=\"#66CC00\"");
				$tpl->assign("font1","<font color=\"#000000\" size=\"+1\">");
				$tpl->assign("font2","</font>");
			}
			else if (isset($efectivo[$i]['extra'])) {
				$tpl->assign("bgcolor", 'bgcolor="#6699FF"');
				$tpl->assign("font1","<font color=\"#000000\" size=\"+1\">");
				$tpl->assign("font2","</font>");
			}
			// [11-May-2010] Errores en el efectivo
			else if ($efectivo[$i]['status'] == -1) {
				$tpl->assign("bgcolor", 'bgcolor="#FF3333"');
				$tpl->assign("font1","<font color=\"#000000\" size=\"+1\">");
				$tpl->assign("font2","</font>");
			}
			// [21-Oct-2010] Errores en clientes
			else if ($efectivo[$i]['status'] <= -2) {
				$tpl->assign("bgcolor", 'bgcolor="#FF9999"');
				$tpl->assign("font1","<font color=\"#000000\" size=\"+1\">");
				$tpl->assign("font2","</font>");
			}
			// Efectivo completo
			else {
				$tpl->assign("bgcolor",/*"bgcolor=\"#73A8B7\""*/"");
				$tpl->assign("font1","<font color=\"#000000\" size=\"+1\">");
				$tpl->assign("font2","</font>");
			}
			
			$tpl->assign("efectivo",$efectivo[$i]['efectivo'] != 0 ? number_format($efectivo[$i]['efectivo'],2,".",",") : '&nbsp;');
			/*$tpl->assign("otro_deposito",($otro_deposito > 0)?number_format($otro_deposito,2,".",","):"&nbsp;");
			
			if ($otro_deposito > 0)
				$num_otros++;*/
			
			// Si hay depositos
			$depositos_total = 0;
			if ($deposito) {
				for ($j=0; $j<count($deposito); $j++) {
					$tpl->newBlock("depositos");
					$tpl->assign('id_dep', $deposito[$j]['id']);
					$tpl->assign("deposito",number_format($deposito[$j]['importe'],2,".",","));
					$tpl->assign("color", $deposito[$j]['con'] == 0 ? "style=\"color:#3399CC;\"" : '');
					if ($deposito[$j]['sec'] > 0)
						$tpl->assign("color", "style=\"color:#999;\"");
					
					if (!isset($efectivo[$i]['extra'])) {
						$total_depositos[$j] += $deposito[$j]['importe'];
						$depositos_total += $deposito[$j]['importe'];
					}
					
					//$num_depositos[$j]++;
					
					// Si el deposito no esta conciliado, marcar la celda de otro color
					if ($deposito[$j]['fecha_con'] == "")
						$tpl->assign("bgcolor","bgcolor=\"#FF6600\"");
				}
				for ($j=0; $j<$num_dep-count($deposito); $j++) {
					$tpl->newBlock("depositos");
					$tpl->assign('id_dep', -1);
					$tpl->assign("deposito","&nbsp;");
				}
			}
			else {
				for ($j=0; $j<$num_dep; $j++) {
					$tpl->newBlock("depositos");
					$tpl->assign('id_dep', -1);
					$tpl->assign("deposito","&nbsp;");
				}
			}
			
			if ($tarjeta) {
				for ($j=0; $j<count($tarjeta); $j++) {
					$tpl->newBlock("tarjetas");
					$tpl->assign('id_dep', $tarjeta[$j]['id']);
					$tpl->assign("tarjeta",number_format($tarjeta[$j]['importe'],2,".",","));
					$tpl->assign("color", $tarjeta[$j]['cod_mov'] == 44 ? "style=\"color:#3333CC;\"" : '');
					if ($tarjeta[$j]['sec'] > 0)
						$tpl->assign("color", "style=\"color:#999;\"");
					
					if (!isset($efectivo[$i]['extra'])) {
						$total_tarjetas[$j] += $tarjeta[$j]['importe'];
						$depositos_total += $tarjeta[$j]['importe'];
					}
					
					//$num_depositos[$j]++;
					
					// Si el deposito no esta conciliado, marcar la celda de otro color
					if ($tarjeta[$j]['fecha_con'] == "")
						$tpl->assign("bgcolor","bgcolor=\"#FF6600\"");
				}
				for ($j=0; $j<$num_tar-count($tarjeta); $j++) {
					$tpl->newBlock("tarjetas");
					$tpl->assign('id_dep', -1);
					$tpl->assign("tarjeta","&nbsp;");
				}
			}
			else {
				for ($j=0; $j<$num_tar; $j++) {
					$tpl->newBlock("tarjetas");
					$tpl->assign('id_dep', -1);
					$tpl->assign("tarjeta","&nbsp;");
				}
			}
			
			// Diferencia de efectivo contra depositos
			if (!isset($efectivo[$i]['extra'])) {
				$diferencia = $efectivo[$i]['efectivo']-$depositos_total;
			}
			else {
				$diferencia = 0;
			}
			
			// [22-Nov-2007]
			$tpl->assign('fila.num_cia', $_SESSION['efe']['num_cia' . $_SESSION['efe']['next']]);
			$tpl->assign('fila.fecha', date('d/m/Y', mktime(0, 0, 0, $_SESSION['efe']['mes'], $efectivo[$i]['dia'], $_SESSION['efe']['anio'])));
			if ($_SESSION['efe']['tipo_otros'] == 1) {
				// Si la diferencia es mayor a 0, tomar de otros depósitos la diferencia para compensar
				$otro_deposito = 0;
				if ($diferencia > 0) {
					// Si los depósitos cubren la diferencia y no se ha llegado al último día...
					if (($otros_depositos - $diferencia) > 0 && $i < count($efectivo)-1) {
						// Si no es el último efectivo, hacer la diferencia, si lo es, asignar el resto de depósitos
						if ($i < count($efectivo)-1) {
							$otro_deposito = $diferencia;
							$otros_depositos -= $diferencia;
							//$num_otros++;
						}
						else {
							$otro_deposito = $otros_depositos;
							$otros_depositos = 0;
							//$num_otros++;
						}
					}
					else {
						$otro_deposito = $otros_depositos;
						$otros_depositos = 0;
						//$num_otros++;
					}
					
					$tpl->assign("fila.otro_deposito",($otro_deposito > 0)?number_format($otro_deposito,2,".",","):"&nbsp;");
				}
				else if ($i == count($efectivo)-1) {
					$otro_deposito = $otros_depositos;
					$otros_depositos = 0;
					$tpl->assign("fila.otro_deposito",($otro_deposito > 0)?number_format($otro_deposito,2,".",","):"&nbsp;");
				}
				else
					$tpl->assign("fila.otro_deposito","&nbsp;");
			}
			else {
				$tpl->assign('fila.otro_deposito', isset($otros_dep_dia[(int)$efectivo[$i]['dia']]) ? number_format($otros_dep_dia[(int)$efectivo[$i]['dia']], 2, '.', ',') : '&nbsp;');
				$otro_deposito = isset($otros_dep_dia[(int)$efectivo[$i]['dia']]) ? $otros_dep_dia[(int)$efectivo[$i]['dia']] : 0;
			}
			
			// Mostrar diferencia
			$depositos_total += $otro_deposito;
			$tpl->assign("fila.diferencia",number_format($efectivo[$i]['efectivo']-$depositos_total,2,".",","));
			if (number_format($efectivo[$i]['efectivo']-$depositos_total,2,".","") >= 0)
				$tpl->assign("fila.dif_color","0000FF");
			else
				$tpl->assign("fila.dif_color","FF0000");
				
			$tpl->assign("fila.total",number_format($depositos_total,2,".",","));
			$total_diferencias += $efectivo[$i]['efectivo']-$depositos_total;
			$gran_total += $depositos_total;
			
			// Sumar total de efectivos
			if (!isset($efectivo[$i]['extra'])) {
				$total_efectivos += $efectivo[$i]['efectivo'];
				// Sumar total de otros depositos
				$total_otros += $otro_deposito;
			}
		}
		
		// Trazar titulos para los depositos
		$tpl->gotoBlock("tabla");
		for ($j=0; $j<$num_dep; $j++) {
			$tpl->newBlock("num_dep");
			$tpl->assign("num_dep",$j+1);
		}
		
		// Trazar titulos para las tarjetas
		$tpl->gotoBlock("tabla");
		for ($j=0; $j<$num_tar; $j++) {
			$tpl->newBlock("num_tar");
			$tpl->assign("num_tar",$j+1);
		}
		
		$tpl->gotoBlock("tabla");
		// Trazar totales
		$tpl->assign("total_efectivos",number_format($total_efectivos,2,".",","));
		$tpl->assign("total_otros_depositos",number_format($total_otros,2,".",","));
		@$tpl->assign("por_otros", number_format($total_otros * 100 / $total_efectivos, 2, ".", ","));
		$tpl->assign("total_diferencias",number_format($total_diferencias,2,".",","));
		$tpl->assign("gran_total",number_format($gran_total,2,".",","));
		for ($i=0; $i<$num_dep; $i++) {
			$tpl->newBlock("total_depositos");
			$tpl->assign("total_depositos",number_format($total_depositos[$i],2,".",","));
			$tpl->newBlock("por_dep");
			if ($i == 0) {
				@$por_dep = array_sum($total_depositos) * 100 / $total_efectivos;
				$tpl->assign("por_dep", number_format($por_dep, 2, ".", ",") . "%");
			}
			else
				$tpl->assign("por_dep", "&nbsp;");
		}
		for ($i=0; $i<$num_tar; $i++) {
			$tpl->newBlock("total_tarjetas");
			$tpl->assign("total_tarjetas",number_format($total_tarjetas[$i],2,".",","));
			$tpl->newBlock("por_tar");
			if ($i == 0) {
				@$por_tar = array_sum($total_tarjetas) * 100 / $total_efectivos;
				$tpl->assign("por_tar", number_format($por_tar, 2, ".", ",") . "%");
			}
			else
				$tpl->assign("por_tar", "&nbsp;");
		}
		$tpl->gotoBlock("tabla");
		// Trazar promedios
		$dias = count($efectivo);
		$tpl->assign("promedio_efectivos",number_format($total_efectivos/($dias - ($_SESSION['efe']['turno_cometra'.$_SESSION['efe']['next']] == 2 ? 1 : 0)),2,".",","));
		if ($num_otros > 0)
			$tpl->assign("promedio_otros_depositos",number_format($total_otros/($dias - ($_SESSION['efe']['turno_cometra'.$_SESSION['efe']['next']] == 2 ? 1 : 0)),2,".",","));
		$tpl->assign("promedio_total",number_format($gran_total/($dias - ($_SESSION['efe']['turno_cometra'.$_SESSION['efe']['next']] == 2 ? 1 : 0)),2,".",","));
		for ($i=0; $i<$num_dep; $i++) {
			$tpl->newBlock("promedio_depositos");
			$tpl->assign("promedio_depositos",number_format($total_depositos[$i]/($dias - ($_SESSION['efe']['turno_cometra'.$_SESSION['efe']['next']] == 2 ? 1 : 0)),2,".",","));
		}
		if (array_sum($total_tarjetas) > 0) {
			$tpl->newBlock("promedio_tarjetas");
			$tpl->assign("promedio_tarjetas",number_format(array_sum($total_tarjetas),2,".",","));
		}
		for ($i=/*0*/1; $i<$num_tar; $i++) {
			$tpl->newBlock("promedio_tarjetas");
			$tpl->assign("promedio_tarjetas",/*number_format($total_tarjetas[$i]/$dias,2,".",",")*/'&nbsp;');
		}
		 
		// Datos para los enlaces
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_SESSION['efe']['fecha2'],$temp);
		
		// Estado de cuenta
		$tpl->assign("tabla.num_cia",$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]);
		$tpl->assign("tabla.dia",date("d", mktime(0,0,0,$temp[2]+1,0,$temp[3])));
		$tpl->assign("tabla.mes",$temp[2]);
		$tpl->assign("tabla.anio",$temp[3]);
		$tpl->assign("tabla.fecha",$_SESSION['efe']['fecha2']);
	}
	else {
		if ($_SESSION['iduser'] != 28) {
			header("location: ./ban_con_dep_v2.php?accion=siguiente");
			die;
		}
	}
}

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