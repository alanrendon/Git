<?php
// LISTADO DE EFECTIVOS (COMPLETO)
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

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/ban_efe_red.tpl" );
$tpl->prepare();

if (isset($_GET['alert']))
	$fecha_tmp = $_GET['fecha'];
else
	$fecha_tmp = $_POST['fecha'];

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha_tmp, $fecha);

// Dias por mes
$diasxmes[1] = 31;
$diasxmes[2] = $fecha[3] % 4 == 0 ? 29 : 28;
$diasxmes[3] = 31;
$diasxmes[4] = 30;
$diasxmes[5] = 31;
$diasxmes[6] = 30;
$diasxmes[7] = 31;
$diasxmes[8] = 31;
$diasxmes[9] = 30;
$diasxmes[10] = 31;
$diasxmes[11] = 30;
$diasxmes[12] = 31;

$numbloques = 2;	// Número de bloques por hoja
$bloque = 1;		// Número de bloques actual

// Rangos de fecha
$fecha1 = "1/$fecha[2]/$fecha[3]";
$fecha2 = $fecha_tmp;

$num_cias = 0;
// Obtener listado de compañías
for ($i=0; $i<30; $i++)
	if (isset($_POST['cia' . $i]) && $_POST['cia' . $i] > 0)
		$cia[$num_cias++] = $_POST['cia' . $i];

if (isset($_GET['alert'])) {
	$cias = $db->query('SELECT num_cia FROM alerta_efectivos ORDER BY num_cia');
	if ($cias)
		foreach ($cias as $c)
			$cia[$num_cias++] = $c['num_cia'];
}

// Obtener listado de compañías que no se tomaran sus depósitos reales
if (!in_array($_SESSION['iduser'], $users))
	for ($i=1; $i<=10; $i++)
		if (isset($_POST['num_cia'.($i-1)]))
			$num_cia[$i] = $_POST['num_cia'.($i-1)];

// Obtener todas las compañias
$sql = "SELECT num_cia, num_cia_primaria FROM catalogo_companias WHERE";
if ($num_cias > 0) {
	$sql .= " num_cia IN (";
	for ($i=0; $i<$num_cias; $i++)
		$sql .= $cia[$i].($i < $num_cias-1?",":") AND");
	
	if (in_array($_SESSION['iduser'], $users))
		$sql .= " num_cia BETWEEN 900 AND 998 AND";
}
if ($_POST['a_partir'])
	$sql .= " num_cia >= $_POST[a_partir] AND";
else if (in_array($_SESSION['iduser'], $users))
	$sql .= " num_cia BETWEEN 900 AND 998 AND";
if (isset($_POST['idadmin']) && $_POST['idadmin'] > 0)
	$sql .= " idadministrador = $_POST[idadmin] AND";
$sql .= " num_cia != 999 ORDER BY num_cia_primaria, num_cia";

$cia = $db->query($sql);

if (!$cia) {
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

function buscarDep($dia) {
	global $depositos, $alternativos;
	
	if (!$depositos && !$alternativos)
		return FALSE;
	
	$dep = array();
	$count = 0;
	for ($i = 0; $i < count($depositos); $i++)
		if ($dia == $depositos[$i]['dia']) {
			$dep[] = array('importe' => $depositos[$i]['importe'], 'fecha_con' => $depositos[$i]['fecha_con']);
			$count++;
		}
	
	for ($i = 0; $i < count($alternativos); $i++)
		if ($dia == $alternativos[$i]['dia']) {
			if ($alternativos[$i]['dep1'] > 0) {
				$dep[] = array('importe' => $alternativos[$i]['dep1'], 'fecha_con' => $alternativos[$i]['fecha']);
				$count++;
			}
			if ($alternativos[$i]['dep2'] > 0) {
				$dep[] = array('importe' => $alternativos[$i]['dep2'], 'fecha_con' => $alternativos[$i]['fecha']);
				$count++;
			}
		}
	
	return $count > 0 ? $dep : FALSE;
}

$num_cia_primaria = NULL;
for ($c=0; $c<count($cia); $c++) {
	// Obtener efectivos de la compañía para el mes dado (dependiendo de si es panaderia o rosticería)
	if (($cia[$c]['num_cia'] >= 301 && $cia[$c]['num_cia'] <= 599) || $cia[$c]['num_cia'] == 704 || $cia[$c]['num_cia'] == 702 || $cia[$c]['num_cia'] == 705)
		$sql = "SELECT num_cia, efectivo, extract(day FROM fecha) AS dia, fecha, 't' AS efe, 't' AS exp, 't' AS gas, 't' AS pro, 't' AS pas FROM total_companias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
	else if ($cia[$c]['num_cia'] >= 900 && $cia[$c]['num_cia'] <= 998)
		$sql = "SELECT num_cia, efectivo, extract(day from fecha) AS dia, fecha, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS efe, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS exp, 't' AS gas, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS pro, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS pas FROM total_zapaterias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '{$fecha2}' ORDER BY fecha";
	else
		$sql = "SELECT num_cia, efectivo, extract(day FROM fecha) AS dia, fecha, efe, exp, gas, pro, pas FROM total_panaderias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
	$efectivo = $db->query($sql);
	
	if (!$efectivo && $db->query('
		SELECT
			id
		FROM
			gastos_caja
		WHERE
			num_cia = ' . $cia[$c]['num_cia'] . '
			AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
	')) {
		$efectivo = array();
		
		for ($dia = 1; $dia <= $fecha[1]; $dia++) {
			$efectivo[] = array(
				'num_cia' => $cia[$c]['num_cia'],
				'efectivo' => 0,
				'dia' => $dia,
				'fecha' => date('d/m/Y', mktime(0, 0, 0, $fecha[2], $dia, $fecha[3])),
				'efe' => 'f',
				'exp' => 'f',
				'gas' => 'f',
				'pro' => 'f',
				'pas' => 'f'
			);
		}
	}
	
	if ($efectivo) {
		// [14-Feb-2007] Obtener efectivos directos posteriores al último día de efectivos
		$last_date = $efectivo[count($efectivo) - 1]['fecha'];
		$sql = "SELECT num_cia, importe AS efectivo, extract(day from fecha) AS dia, fecha, 'FALSE' AS efe, 'FALSE' AS exp, 'FALSE' AS gas, 'FALSE' AS pro, 'FALSE' AS pas";
		$sql .= " FROM importe_efectivos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha > '$last_date' AND fecha <= '$fecha2' ORDER BY fecha";
		$direct = $db->query($sql);
		if ($direct)
			foreach ($direct as $reg)
				$efectivo[] = $reg;
		
		if ($num_cia_primaria != $cia[$c]['num_cia_primaria']) {
			if ($bloque == 2 && $c > 0)
				$tpl->newBlock("blanco");
			
			$num_cia_primaria = $cia[$c]['num_cia_primaria'];
			$bloque = 1;
		}
		
		if ($bloque == 1) {
			/*if ($c > 0)
				$tpl->newBlock("salto");*/
			
			$tpl->newBlock("hoja");
			$bloque = 2;
		}
		else if ($bloque == 2)
			$bloque = 1;
		
		$tpl->newBlock("mitad");
		
		$tpl->assign("num_cia",$cia[$c]['num_cia']);
		$nombre_cia = $db->query("SELECT nombre,nombre_corto FROM catalogo_companias WHERE num_cia = {$cia[$c]['num_cia']}");
		$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
		$tpl->assign("nombre_corto",$nombre_cia[0]['nombre_corto']);
		$tpl->assign('mes', substr(mes_escrito($fecha[2], TRUE), 0, 3));
		$tpl->assign('anio', substr($fecha[3], 2));
		
		// [2006/07/10] Mancomunar cias. 100 a 200 con 201 a 300
		/*$tmp = $cia[$c]['num_cia'] >= 100 && $cia[$c]['num_cia'] <= 200 ? "IN ({$cia[$c]['num_cia']}, " . ($cia[$c]['num_cia'] + 100) . ")" : "= {$cia[$c]['num_cia']}";
		$sql = "SELECT importe, fecha_con, extract(day FROM fecha) AS dia FROM estado_cuenta WHERE cod_mov IN (1,16,44) AND num_cia $tmp AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha, importe DESC";
		$depositos = $db->query($sql);*/
	//	$tmp = $cia[$c]['num_cia'] >= 301 && $cia[$c]['num_cia'] <= 599 ? "(num_cia IN ({$cia[$c]['num_cia']}, " . ($cia[$c]['num_cia'] + 100) . ") AND num_cia_sec IS NULL) OR num_cia_sec IN ({$cia[$c]['num_cia']}, " . ($cia[$c]['num_cia'] + 100) . ")" : "(num_cia = {$cia[$c]['num_cia']} AND num_cia_sec IS NULL) OR num_cia_sec = {$cia[$c]['num_cia']}";
		//$sql = "SELECT importe, fecha_con, extract(day FROM fecha) AS dia FROM estado_cuenta WHERE cod_mov IN (1, 16, 44, 99) AND ($tmp) AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha, importe DESC";
		//$depositos = $db->query($sql);
		/*$sql = "SELECT importe, fecha_con, extract(day FROM fecha) AS dia FROM estado_cuenta WHERE cod_mov IN (1,16,44) AND num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha, importe DESC";
		$depositos = $db->query($sql);*/
		
		if (mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]) <= mktime(0, 0, 0, 5, 31, 2011) && in_array($cia[$c]['num_cia'], array(11, 303, 353, 355))) {
			$cias = array(
				11  => '11, 810',
				303 => '303, 811',
				353 => '353, 812',
				355 => '355, 813'
			);
			
			$sql = "
				SELECT
					importe,
					fecha_con,
					extract(day FROM fecha)
						AS dia
				FROM
					estado_cuenta
				WHERE
					cod_mov IN (1, 16, 44, 99)
					AND (
						(
							num_cia IN ({$cias[$cia[$c]['num_cia']]})
							AND num_cia_sec IS NULL
						)
						OR num_cia_sec IN ({$cias[$cia[$c]['num_cia']]})
					)
					AND fecha BETWEEN '$fecha1' AND '$fecha2'
				ORDER BY
					fecha,
					importe DESC
			";
		}
		else {
			$sql = "
				SELECT
					importe,
					fecha_con,
					extract(day FROM fecha)
						AS dia
				FROM
					estado_cuenta
				WHERE
					cod_mov IN (1, 16, 44, 99)
					AND (
						(
							num_cia = {$cia[$c]['num_cia']}
							AND num_cia_sec IS NULL
						)
						OR num_cia_sec = {$cia[$c]['num_cia']}
					)
					AND fecha BETWEEN '$fecha1' AND '$fecha2'
				ORDER BY
					fecha,
					importe DESC
			";
		}
		$depositos = $db->query($sql);
		
		$sql = "SELECT dep1, dep2, extract(day FROM fecha) AS dia, fecha FROM depositos_alternativos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
		$alternativos = $db->query($sql);
		
		// Obtener el total de otros depósitos del mes
		$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$temp = $db->query($sql);
		$otros_depositos = $temp[0]['sum'] != 0 ? $temp[0]['sum'] : 0;
		
		// [22-Nov-2007] Obtener otros depósitos por día
		$sql = "SELECT sum(importe) AS importe, extract(day from fecha) AS dia FROM otros_depositos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY dia ORDER BY dia";
		$otros_dep_dia = array();
		if ($tmp = $db->query($sql))
			foreach ($tmp as $reg)
				$otros_dep_dia[$reg['dia']] = $reg['importe'];
		
		// En caso de que los depositos sean alternativos
		if (isset($num_cia) && $key = array_search($cia[$c]['num_cia'], $num_cia)) {
			$depositos = FALSE;
			$num_dep = 0;
		}
		
		// Trazar datos
		$total_efectivos = 0;
		$total_otros = 0;
		$total_diferencias = 0;
		$gran_total = 0;
		$total_depositos = 0;
		$total_mayoreo = 0;
		
		for ($i = 0; $i < count($efectivo); $i++) {
			// Buscar los depositos para x día
			$deposito = buscarDep($efectivo[$i]['dia']);
			
			// Trazar nueva fila
			$tpl->assign("dia$i", (int)$efectivo[$i]['dia']);
			// Efectivo incompleto
			if (!($efectivo[$i]['efe'] == "t" &&
				  $efectivo[$i]['exp'] == "t" &&
				  $efectivo[$i]['gas'] == "t" &&
				  $efectivo[$i]['pro'] == "t" &&
				  $efectivo[$i]['pas'] == "t")) {
				// Buscar efectivo directo y asignarlo si lo hay
				$sql = "SELECT importe FROM importe_efectivos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha = '{$efectivo[$i]['fecha']}'";
				if ($directo = $db->query($sql))
					$efectivo[$i]['efectivo'] = $directo[0]['importe'];
			}
			
			
			$tpl->assign("efectivo$i", $efectivo[$i]['efectivo'] /*>*/!= 0 ? number_format($efectivo[$i]['efectivo'], 2, ".", ",") : "&nbsp;");
			
			// Si hay depositos
			$depositos_total = 0;
			$mayoreo_total = 0;
			if ($deposito) {
				$tpl->assign("deposito$i", number_format($deposito[0]['importe'], 2, ".", ","));
				$total_depositos += $deposito[0]['importe'];
				$depositos_total += $deposito[0]['importe'];
				
				if (count($deposito) > 1) {
					for ($j = 1; $j < count($deposito); $j++) {
						$mayoreo_total += $deposito[$j]['importe'];
						$total_mayoreo += $deposito[$j]['importe'];
						$depositos_total += $deposito[$j]['importe'];
					}
					$tpl->assign("mayoreo$i", number_format($mayoreo_total, 2, ".", ","));
				}
			}
			
			// Diferencia de efectivo contra depositos
			$diferencia = $efectivo[$i]['efectivo'] - $depositos_total;
			/*if (!isset($_GET['alert'])) {
				// Si la diferencia es mayor a 0, tomar de otros depósitos la diferencia para compensar
				$otro_deposito = 0;
				if ($diferencia > 0) {
					// Si los depósitos cubren la diferencia y no se ha llegado al último día...
					if (($otros_depositos - $diferencia) > 0 && $i < count($efectivo) - 1) {
						// Si no es el último efectivo, hacer la diferencia, si lo es, asignar el resto de depósitos
						if ($i < count($efectivo) - 1) {
							$otro_deposito = $diferencia;
							$otros_depositos -= $diferencia;
						}
						else {
							$otro_deposito = $otros_depositos;
							$otros_depositos = 0;
						}
					}
					else {
						$otro_deposito = $otros_depositos;
						$otros_depositos = 0;
					}
					
					$tpl->assign("oficina$i", $otro_deposito > 0 ? number_format($otro_deposito, 2, ".", ",") : "&nbsp;");
				}
				else if ($i == count($efectivo) - 1) {
					$otro_deposito = $otros_depositos;
					$otros_depositos = 0;
					$tpl->assign("oficina$i", $otro_deposito > 0 ? number_format($otro_deposito, 2, ".", ",") : "&nbsp;");
				}
				else
					$tpl->assign("oficina$i", "&nbsp;");
			}
			else {*/
				$tpl->assign("oficina$i", isset($otros_dep_dia[(int)$efectivo[$i]['dia']]) ? number_format($otros_dep_dia[(int)$efectivo[$i]['dia']], 2, '.', ',') : '&nbsp;');
				$otro_deposito = isset($otros_dep_dia[(int)$efectivo[$i]['dia']]) ? $otros_dep_dia[(int)$efectivo[$i]['dia']] : 0;
			//}
			
			// Mostrar diferencia
			$depositos_total += $otro_deposito;
			$tpl->assign("diferencia$i", number_format($efectivo[$i]['efectivo'] - $depositos_total, 2, ".", "") != 0 ? number_format($efectivo[$i]['efectivo'] - $depositos_total, 2, ".", ",") : "&nbsp;");
			if (number_format($efectivo[$i]['efectivo'] - $depositos_total, 2, ".", "") >= 0)
				$tpl->assign("dif_color$i", "000000");
			else
				$tpl->assign("dif_color$i", "FF0000");
				
			$tpl->assign("total$i", number_format($depositos_total, 2, ".", ","));
			$total_diferencias += $efectivo[$i]['efectivo'] - $depositos_total;
			$gran_total += $depositos_total;
			
			// Sumar total de efectivos
			$total_efectivos += $efectivo[$i]['efectivo'];
			// Sumar total de otros depositos
			$total_otros += $otro_deposito;
		}
		
		$tpl->gotoBlock("tabla");
		// Trazar totales
		$tpl->assign("total_efectivos", number_format($total_efectivos, 2, ".", ","));
		$tpl->assign("total_depositos", number_format($total_depositos, 2, ".", ","));
		$tpl->assign("total_mayoreo", number_format($total_mayoreo, 2, ".", ","));
		$tpl->assign("total_oficina", number_format($total_otros, 2, ".", ","));
		$tpl->assign("total_diferencias", number_format($total_diferencias, 2, ".", ","));
		if ($total_diferencias >= 0)
			$tpl->assign("color_dif","0000FF");
		else
			$tpl->assign("color_dif","FF0000");
		$tpl->assign("gran_total", number_format($gran_total, 2, ".", ","));
		
		// Trazar promedios
		$dias = count($efectivo);
		$tpl->assign("promedio_efectivos", number_format($total_efectivos / $dias, 2, ".", ","));
		$tpl->assign("promedio_depositos", number_format($total_depositos / $dias, 2, ".", ","));
		$tpl->assign("promedio_mayoreo", number_format($total_mayoreo / $dias, 2, ".", ","));
		$tpl->assign("promedio_oficina", number_format($total_otros / $dias, 2, ".", ","));
		$tpl->assign("promedio_total", number_format($gran_total / $dias, 2, ".", ","));
	}
}
if ($bloque == 2 && $c > 0)
	$tpl->newBlock("blanco");

$tpl->printToScreen();
?>
