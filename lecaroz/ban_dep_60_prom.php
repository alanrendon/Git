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
$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/ban/ban_dep_60_prom.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_POST['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n", mktime(0,0,0,date("m"),0,date("Y"))), "selected");
	$tpl->assign("anio", date("Y"));
	
	$result = $db->query("SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre");
	foreach ($result as $reg) {
		$tpl->newBlock("admin");
		$tpl->assign("id", $reg['id']);
		$tpl->assign("nombre", $reg['nombre']);
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
	die;
}

// Dias por mes
$diasxmes[1] = 31;
$diasxmes[2] = ($_POST['anio'] % 4 == 0) ? 29 : 28;
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

// Rangos de fecha
$fecha1 = "1/$_POST[mes]/$_POST[anio]";
$fecha2 = "{$diasxmes[$_POST['mes']]}/$_POST[mes]/$_POST[anio]";

// Obtener todas las compañias
$sql = "SELECT num_cia, idadministrador, nombre_administrador FROM catalogo_companias LEFT JOIN catalogo_administradores USING (idadministrador) WHERE";
$sql .= in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 900 AND 998 AND" : " num_cia BETWEEN 1 AND 899 AND";
$sql .= $_POST['rango'] > 0 ? ($_POST['rango'] == 1 ? " num_cia BETWEEN 1 AND 300 AND" : " (num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705)) AND") : "";
$sql .= $_POST['num_cia'] > 0 ? " num_cia = $_POST[num_cia] AND" : "";
$sql .= $_POST['admin'] > 0 ? " idadministrador = $_POST[admin] AND" : "";
$sql .= " num_cia != 999 ORDER BY idadministrador, num_cia";

$cia = $db->query($sql);

if (!$cia) {
	header("location: ./ban_dep_60_prom.php?codigo_error=1");
	die;
}

// Arreglo que contiene todos los depositos
$datos = array();
$promedios = array();
$status_dia = array();
$count = 0;

for ($c = 0; $c < count($cia); $c++) {
	// Obtener efectivos de la compañía para el mes dado (dependiendo de si es panaderia o rosticería)
	if (($cia[$c]['num_cia'] > 300 && $cia[$c]['num_cia'] < 600) || ($cia[$c]['num_cia'] >= 900 && $cia[$c]['num_cia'] <= 998) || in_array($cia[$c]['num_cia'], array(702, 704, 705)))
		$sql = "SELECT num_cia,efectivo,fecha,'t' AS efe,'t' AS exp,'t' AS gas,'t' AS pro,'t' AS pas FROM total_companias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
	else
		$sql = "SELECT num_cia,efectivo,fecha,efe,exp,gas,pro,pas FROM total_panaderias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
	$efectivo = $db->query($sql);
	
	if ($efectivo) {
		// Obtener el total de otros depósitos del mes
		$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$temp = $db->query($sql);
		$otros_depositos = ($temp[0]['sum'] != 0) ? $temp[0]['sum'] : 0;
		
		$total_depositos = 0;
		
		for ($i = 0; $i < count($efectivo); $i++) {
			// Buscar los depositos para x día
			$sql = "SELECT sum(importe) AS importe,fecha_con FROM estado_cuenta WHERE num_cia = {$cia[$c]['num_cia']} AND fecha = '{$efectivo[$i]['fecha']}' AND cod_mov IN (1,16,44) GROUP BY fecha_con ORDER BY importe DESC";
			$deposito = $db->query($sql);
			
			// En caso de que los depositos sean alternativos
			if ($key = array_search($cia[$c]['num_cia'], $_POST['alt']) !== FALSE)
				unset($deposito);
			
			$sql = "SELECT dep1,dep2,fecha FROM depositos_alternativos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha = '{$efectivo[$i]['fecha']}'";
			$temp = $db->query($sql);
			
			if ($temp) {
				if (!isset($deposito)) {
					$deposito = array();
					$temp_index = 0;
				}
				else
					if ($deposito)
						$temp_index = count($deposito);
					else
						$temp_index = 0;
	
				if ($temp[0]['dep1'] > 0) $deposito[$temp_index++] = array('importe' => $temp[0]['dep1'], 'fecha_con' => $temp[0]['fecha']);
				if ($temp[0]['dep2'] > 0) $deposito[$temp_index++] = array('importe' => $temp[0]['dep2'], 'fecha_con' => $temp[0]['fecha']);
			}
			else
				if (!isset($deposito))
					$deposito = FALSE;
			
			// Desglozar fecha
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $efectivo[$i]['fecha'], $fecha);
			$dia = $fecha[1];
			
			// Almacenar estado del efectivo
			$status_dia[$cia[$c]['num_cia']][$dia] = $efectivo[$i]['efe'] == "t" && $efectivo[$i]['exp'] == "t" && $efectivo[$i]['gas'] == "t" && $efectivo[$i]['pro'] == "t" && $efectivo[$i]['pas'] == "t" ? TRUE : FALSE;
			
			$datos[$count]['idadministrador'] = $cia[$c]['idadministrador'];
			$datos[$count]['nombre_administrador'] = $cia[$c]['nombre_administrador'];
			$datos[$count]['num_cia'] = $cia[$c]['num_cia'];
			$datos[$count]['dia'] = $dia;
			$datos[$count]['efectivo'] = $efectivo[$i]['efectivo'];
			
			// Si hay depositos
			$depositos_total = 0;
			$mayoreo_total = 0;
			if ($deposito) {
				$datos[$count]['deposito'] = $deposito[0]['importe'];
				$depositos_total += $deposito[0]['importe'];
				$total_depositos += $deposito[0]['importe'];
				
				if (count($deposito) > 1) {
					$mayoreo = 0;
					for ($j = 1; $j < count($deposito); $j++)
						$mayoreo += $deposito[$j]['importe'];
					$datos[$count]['mayoreo'] = $mayoreo;
					$depositos_total += $mayoreo;
				}
				else
					$datos[$count]['mayoreo'] = 0;
			}
			else {
				$datos[$count]['deposito'] = 0;
				$datos[$count]['mayoreo'] = 0;
			}
			
			// Diferencia de efectivo contra depositos
			$diferencia = $efectivo[$i]['efectivo'] - $depositos_total;
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
				
				$datos[$count]['oficina'] = $otro_deposito;
			}
			else if ($i == count($efectivo) - 1) {
				$otro_deposito = $otros_depositos;
				$otros_depositos = 0;
				$datos[$count]['oficina'] = $otro_deposito;
			}
			else
				$datos[$count]['oficina'] = 0;
			
			// Mostrar diferencia
			$depositos_total += $otro_deposito;
			$datos[$count]['diferencia'] = $efectivo[$i]['efectivo'] - $depositos_total;
			$datos[$count]['total_depositos'] = $depositos_total;
			
			$count++;
		}
		
		// Calcular promedio
		$dias = count($efectivo);
		$promedios[$cia[$c]['num_cia']] = $total_depositos / $dias;
	}
}

// Buscar movimientos por abajo de 60% del promedio de depositos diarios
$result = FALSE;
for ($i = 0; $i < count($datos); $i++)
	if ($status_dia[$datos[$i]['num_cia']][$datos[$i]['dia']] && ($datos[$i]['deposito'] + $datos[$i]['mayoreo']) > 0 && ($datos[$i]['deposito'] + $datos[$i]['mayoreo']) < $promedios[$datos[$i]['num_cia']] * 0.60) {
		if (!$result)
			$result = array();
		$result[] = $datos[$i];
	}

if (!$result) {
	header("location: ./ban_dep_60_prom.php?codigo_error=1");
	die;
}

$admin = NULL;
$num_cia = NULL;
for ($i = 0; $i < count($result); $i++) {
	if ($admin != $result[$i]['idadministrador']) {
		$admin = $result[$i]['idadministrador'];
		
		$tpl->newBlock("listado");
		$tpl->assign("dia", date("d"));
		$tpl->assign("mes", mes_escrito(date("n"), TRUE));
		$tpl->assign("anio", date("Y"));
		$tpl->assign("admin", $result[$i]['nombre_administrador']);
	}
	if ($num_cia != $result[$i]['num_cia']) {
		$num_cia = $result[$i]['num_cia'];
		
		$tpl->newBlock("cia");
		//$tpl->assign("num_cia", $num_cia);
		$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");
		$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
		$tpl->assign("promedio", number_format($promedios[$result[$i]['num_cia']], 2, ".", ","));
	}
	$tpl->newBlock("dia");
	$tpl->assign("dia", $result[$i]['dia']);
	$tpl->assign("efectivo", $result[$i]['efectivo'] != 0 ? number_format($result[$i]['efectivo'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("deposito", $result[$i]['deposito'] != 0 ? number_format($result[$i]['deposito'], 2, ".", ",") : "&nbsp;");
	//$tpl->assign("mayoreo", $result[$i]['mayoreo'] != 0 ? number_format($result[$i]['mayoreo'], 2, ".", ",") : "&nbsp;");
	//$tpl->assign("oficina", $result[$i]['oficina'] != 0 ? number_format($result[$i]['oficina'], 2, ".", ",") : "&nbsp;");
	//$tpl->assign("diferencia", $result[$i]['diferencia'] != 0 ? number_format($result[$i]['diferencia'], 2, ".", ",") : "&nbsp;");
	//$tpl->assign("total_depositos", $result[$i]['total_depositos'] != 0 ? number_format($result[$i]['total_depositos'], 2, ".", ",") : "&nbsp;");
}

$tpl->printToScreen();
?>
