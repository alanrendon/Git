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
$tpl = new TemplatePower( "./plantillas/ban/ban_efe_prt.tpl" );
$tpl->prepare();

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
$tpl->assign("mes_escrito", mes_escrito($temp[2], TRUE));

// Obtener efectivos de la compañía para el mes dado (dependiendo de si es panaderia o rosticería)
if (($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] > 300 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 599) || $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 704 || $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 702)
	$sql = "SELECT num_cia, efectivo, extract(day FROM fecha) AS dia, fecha, 't' AS efe, 't' AS exp, 't' AS gas, 't' AS pro, 't' AS pas FROM total_companias WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN'{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha";
else if ($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] >= 900 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 998)
	$sql = "SELECT num_cia, efectivo, extract(day from fecha) AS dia, fecha, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS efe, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS exp, 't' AS gas, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS pro, CASE WHEN venta > 0 THEN 't' ELSE 'f' END AS pas FROM total_zapaterias WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN'{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha";
else
	$sql = "SELECT num_cia, efectivo, extract(day FROM fecha) AS dia, fecha, efe, exp, gas, pro, pas FROM total_panaderias WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha";
$efectivo = $db->query($sql);

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
	$direct = $db->query($sql);
	if ($direct)
		foreach ($direct as $reg)
			$efectivo[] = $reg;
	
	$tpl->newBlock("tabla");
	
	// [2006/07/10] Mancomunar cias. 100 a 200 con 201 a 300
	//$tmp = $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] >= 100 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 200 ? "IN ({$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}, " . ($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] + 100) . ")" : "= {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]}";
	//$sql = "SELECT importe, fecha_con, extract(day FROM fecha) AS dia, cod_mov, CASE WHEN concepto LIKE '%DEPOSITO COMETRA%' THEN 1 ELSE 0 END AS con FROM estado_cuenta WHERE cod_mov IN (1,16,44) AND num_cia $tmp AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha, importe DESC";
	$sql = "SELECT importe, fecha_con, extract(day FROM fecha) AS dia, cod_mov, CASE WHEN concepto LIKE '%DEPOSITO COMETRA%' THEN 1 ELSE 0 END AS con FROM estado_cuenta WHERE cod_mov IN (1, 16, 44, 99) AND ((num_cia = {$_SESSION['efe']['num_cia' . $_SESSION['efe']['next']]} AND num_cia_sec IS NULL) OR num_cia_sec = {$_SESSION['efe']['num_cia' . $_SESSION['efe']['next']]}) AND fecha BETWEEN '{$_SESSION['efe']['fecha1']}' AND '{$_SESSION['efe']['fecha2']}' ORDER BY fecha, importe DESC";
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
	if ($temp_tar > $num_tar) $num_tar = $temp_tar;
	
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
				$dep[] = array('importe' => $depositos[$i]['importe'], 'fecha_con' => $depositos[$i]['fecha_con'], 'cod_mov' => $depositos[$i]['cod_mov'], 'con' => $depositos[$i]['con']);
				$count++;
			}
		
		for ($i = 0; $i < count($alternativos); $i++)
			if ($dia == $alternativos[$i]['dia']) {
				if ($alternativos[$i]['dep1'] > 0) {
					$dep[] = array('importe' => $alternativos[$i]['dep1'], 'fecha_con' => $alternativos[$i]['fecha'], 'cod_mov' => 1, 'con' => 1);
					$count++;
				}
				if ($alternativos[$i]['dep2'] > 0) {
					$dep[] = array('importe' => $alternativos[$i]['dep2'], 'fecha_con' => $alternativos[$i]['fecha'], 'cod_mov' => 1, 'con' => 1);
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
				$dep[] = array('importe' => $depositos[$i]['importe'], 'fecha_con' => $depositos[$i]['fecha_con'], 'cod_mov' => $depositos[$i]['cod_mov']);
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
			
			//$tpl->assign('bgcolor', 'style="color:black; background:' . ($directo ? '#FFFF00' : '#66CC00') . '"');
			
			$tpl->assign("bgcolor",$directo ? "bgcolor=\"#FFFF00\"" : "bgcolor=\"#66CC00\"");
			$tpl->assign("font1","<font color=\"#000000\" size=\"+1\">");
			$tpl->assign("font2","</font>");
		}
		// Efectivo completo
		else {
			//$tpl->assign('bgcolor', 'style=""');
			
			//$tpl->assign("bgcolor",/*"bgcolor=\"#73A8B7\""*/"");
			//$tpl->assign("font1","<font color=\"#000000\" size=\"+1\">");
			//$tpl->assign("font2","</font>");
		}
		
		$tpl->assign("efectivo",number_format($efectivo[$i]['efectivo'],2,".",","));
		/*$tpl->assign("otro_deposito",($otro_deposito > 0)?number_format($otro_deposito,2,".",","):"&nbsp;");
		
		if ($otro_deposito > 0)
			$num_otros++;*/
		
		// Si hay depositos
		$depositos_total = 0;
		if ($deposito) {
			for ($j=0; $j<count($deposito); $j++) {
				$tpl->newBlock("depositos");
				$tpl->assign("deposito",number_format($deposito[$j]['importe'],2,".",","));
				$tpl->assign("color", $deposito[$j]['con'] == 0 ? "style=\"color:#3399CC;\"" : '');
				
				$total_depositos[$j] += $deposito[$j]['importe'];
				$depositos_total += $deposito[$j]['importe'];
				
				//$num_depositos[$j]++;
				
				// Si el deposito no esta conciliado, marcar la celda de otro color
				if ($deposito[$j]['fecha_con'] == "")
					$tpl->assign("bgcolor","bgcolor=\"#FF6600\"");
			}
			for ($j=0; $j<$num_dep-count($deposito); $j++) {
				$tpl->newBlock("depositos");
				$tpl->assign("deposito","&nbsp;");
			}
		}
		else {
			for ($j=0; $j<$num_dep; $j++) {
				$tpl->newBlock("depositos");
				$tpl->assign("deposito","&nbsp;");
			}
		}
		
		if ($tarjeta) {
			for ($j=0; $j<count($tarjeta); $j++) {
				$tpl->newBlock("tarjetas");
				$tpl->assign("tarjeta",number_format($tarjeta[$j]['importe'],2,".",","));
				$tpl->assign("color", $tarjeta[$j]['cod_mov'] == 44 ? "style=\"color:#3333CC;\"" : '');
				
				$total_tarjetas[$j] += $tarjeta[$j]['importe'];
				$depositos_total += $tarjeta[$j]['importe'];
				
				//$num_depositos[$j]++;
				
				// Si el deposito no esta conciliado, marcar la celda de otro color
				if ($tarjeta[$j]['fecha_con'] == "")
					$tpl->assign("bgcolor","bgcolor=\"#FF6600\"");
			}
			for ($j=0; $j<$num_tar-count($tarjeta); $j++) {
				$tpl->newBlock("tarjetas");
				$tpl->assign("tarjeta","&nbsp;");
			}
		}
		else {
			for ($j=0; $j<$num_tar; $j++) {
				$tpl->newBlock("tarjetas");
				$tpl->assign("tarjeta","&nbsp;");
			}
		}
		
		// Diferencia de efectivo contra depositos
		$diferencia = $efectivo[$i]['efectivo']-$depositos_total;
		
		// [22-Nov-2007]
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
		$total_efectivos += $efectivo[$i]['efectivo'];
		// Sumar total de otros depositos
		$total_otros += $otro_deposito;
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
	$tpl->assign("promedio_efectivos",number_format($total_efectivos/$dias,2,".",","));
	if ($num_otros > 0)
		$tpl->assign("promedio_otros_depositos",number_format($total_otros/$dias,2,".",","));
	$tpl->assign("promedio_total",number_format($gran_total/$dias,2,".",","));
	for ($i=0; $i<$num_dep; $i++) {
		$tpl->newBlock("promedio_depositos");
		$tpl->assign("promedio_depositos",number_format($total_depositos[$i]/$dias,2,".",","));
	}
	if (array_sum($total_tarjetas) > 0) {
		$tpl->newBlock("promedio_tarjetas");
		$tpl->assign("promedio_tarjetas",number_format(array_sum($total_tarjetas),2,".",","));
	}
	for ($i=/*0*/1; $i<$num_tar; $i++) {
		$tpl->newBlock("promedio_tarjetas");
		$tpl->assign("promedio_tarjetas",/*number_format($total_tarjetas[$i]/$dias,2,".",",")*/'&nbsp;');
	}
}

$tpl->printToScreen();
?>