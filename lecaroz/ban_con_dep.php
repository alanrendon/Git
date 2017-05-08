<?php
// CONCILIACION DE EFECTIVOS
// Tablas 'estado_cuenta'
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
$tpl->assignInclude("body","./plantillas/ban/ban_con_dep.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['accion']) && $_GET['accion'] == "terminar") {
	unset($_SESSION['efe']);
	unset($_SESSION['no_efe']);
}

// Inicializar fecha y listado de compañías
if (isset($_GET['fecha'])) {
	//$_SESSION['efe']['fecha1'] = date("d/m/Y",mktime(0,0,0,$_GET['mes'],1,$_GET['anio']));
	//$_SESSION['efe']['fecha2'] = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$_SESSION['efe']['fecha1'] = "1/$fecha[2]/$fecha[3]";
	$_SESSION['efe']['fecha2'] = $_GET['fecha'];
	
	for ($i=0; $i<10; $i++)
		if ($_GET['num_cia'.$i] > 0)
			$_SESSION['no_efe']['num_cia'.$i] = $_GET['num_cia'.$i];
	
	$cia = ejecutar_script("SELECT num_cia,nombre_corto AS nombre_cia,catalogo_operadoras.nombre AS operadora FROM catalogo_companias LEFT JOIN catalogo_operadoras USING(idoperadora) ORDER BY num_cia ASC",$dsn);
	for ($i=0; $i<count($cia); $i++) {
		$_SESSION['efe']['num_cia'.$i] = $cia[$i]['num_cia'];
		$_SESSION['efe']['nombre_cia'.$i] = $cia[$i]['nombre_cia'];
		$_SESSION['efe']['operadora'.$i] = $cia[$i]['operadora'];
	}
	$_SESSION['efe']['num_cias'] = count($cia);
	$_SESSION['efe']['next'] = 0;
}

// Si no existe mes de conciliación, inicializarlo
if (!isset($_SESSION['efe']['fecha1'])) {
	$tpl->newBlock("mes");
	$tpl->assign("fecha", date("d/m/Y"));
	
	//$tpl->assign(date("n"),"selected");
	//$tpl->assign("anio",date("Y"));
	
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
	if (($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] > 100 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 200) /*|| $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 702 || $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 703*/ || $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 704)
		$sql = "SELECT num_cia,efectivo,fecha,'t' AS efe,'t' AS exp,'t' AS gas,'t' AS pro,'t' AS pas FROM total_companias WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha>='".$_SESSION['efe']['fecha1']."' AND fecha<='".$_SESSION['efe']['fecha2']."' ORDER BY fecha";
	else
		$sql = "SELECT num_cia,efectivo,fecha,efe,exp,gas,pro,pas FROM total_panaderias WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha>='".$_SESSION['efe']['fecha1']."' AND fecha<='".$_SESSION['efe']['fecha2']."' ORDER BY fecha";
	$efectivo = ejecutar_script($sql,$dsn);
	
	if ($efectivo) {
		$tpl->newBlock("tabla");
		
		// Obtener el total de otros depósitos del mes
		$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha>='".$_SESSION['efe']['fecha1']."' AND fecha<='".$_SESSION['efe']['fecha2']."'";
		$temp = ejecutar_script($sql,$dsn);
		$otros_depositos = ($temp[0]['sum'] != 0)?$temp[0]['sum']:0;
		
		$num_dep = 0;
		// Obtener el número de columnas para los depósitos
		for ($i=0; $i<count($efectivo); $i++) {
			// Buscar los depositos para x día
			//$sql = "SELECT importe FROM estado_cuenta WHERE tipo_mov='FALSE' AND num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha='".$efectivo[$i]['fecha']."' ORDER BY importe DESC";
			$sql = "SELECT importe FROM estado_cuenta WHERE cod_mov IN (1,16) AND num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha='".$efectivo[$i]['fecha']."' ORDER BY importe DESC";
			$deposito = ejecutar_script($sql,$dsn);
			
			if ($deposito)
				for ($j=0; $j<count($deposito); $j++)
					if ($j+1 > $num_dep) $num_dep++;
		}
		
		if (isset($_SESSION['no_efe']) && $key = array_search($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']],$_SESSION['no_efe']))
			$num_dep = 0;
		
		$temp = ejecutar_script("SELECT SUM(dep1) AS dep1,SUM(dep2) AS dep2 FROM depositos_alternativos WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha>='".$_SESSION['efe']['fecha1']."' AND fecha<='".$_SESSION['efe']['fecha2']."'",$dsn);
		if ($temp[0]['dep1'] > 0) $num_dep++;
		if ($temp[0]['dep2'] > 0) $num_dep++;
		
		
		// Trazar datos
		$total_efectivos = 0;
		$total_otros = 0;
		$total_diferencias = 0;
		$gran_total = 0;
		$total_depositos = array();
		
		$num_depositos = array();
		$num_otros = 0;
		
		for ($i=0; $i<$num_dep; $i++) {
			$total_depositos[$i] = 0;
			$num_depositos[$i] = 0;
		}
		
		for ($i=0; $i<count($efectivo); $i++) {
			// Buscar los depositos para x día
			//$sql = "SELECT importe,fecha_con FROM estado_cuenta WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha='".$efectivo[$i]['fecha']."' AND tipo_mov='FALSE' ORDER BY importe DESC";
			$sql = "SELECT importe,fecha_con FROM estado_cuenta WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha='".$efectivo[$i]['fecha']."' AND cod_mov IN (1,16) ORDER BY importe DESC";
			$deposito = ejecutar_script($sql,$dsn);
			
			// En caso de que los depositos sean alternativos
			if (isset($_SESSION['no_efe']) && $key = array_search($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']],$_SESSION['no_efe']))
				unset($deposito);
			
			$sql = "SELECT dep1,dep2,fecha FROM depositos_alternativos WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha='".$efectivo[$i]['fecha']."'";
			$temp = ejecutar_script($sql,$dsn);
			
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
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$efectivo[$i]['fecha'],$fecha);
			$dia = $fecha[1];
			
			// Trazar nueva fila
			$tpl->newBlock("fila");
			$tpl->assign("dia",(int)$dia);
			// Efectivo incompleto
			if (!($efectivo[$i]['efe'] == "t" &&
				  $efectivo[$i]['exp'] == "t" &&
				  $efectivo[$i]['gas'] == "t" &&
				  $efectivo[$i]['pro'] == "t" &&
				  $efectivo[$i]['pas'] == "t")) {
				// Buscar efectivo directo y asignarlo si lo hay
				$sql = "SELECT importe FROM importe_efectivos WHERE num_cia = {$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]} AND fecha = '{$efectivo[$i]['fecha']}'";
				if ($directo = ejecutar_script($sql,$dsn))
					$efectivo[$i]['efectivo'] = $directo[0]['importe'];
				
				$tpl->assign("bgcolor",$directo ? "bgcolor=\"#FFFF00\"" : "bgcolor=\"#66CC00\"");
				$tpl->assign("font1","<font color=\"#000000\" size=\"+1\">");
				$tpl->assign("font2","</font>");
			}
			// Efectivo completo
			else {
				$tpl->assign("bgcolor",/*"bgcolor=\"#73A8B7\""*/"");
				$tpl->assign("font1","<font color=\"#000000\" size=\"+1\">");
				$tpl->assign("font2","</font>");
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
			
			// Diferencia de efectivo contra depositos
			$diferencia = $efectivo[$i]['efectivo']-$depositos_total;
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
				$por_dep = array_sum($total_depositos) * 100 / $total_efectivos;
				$tpl->assign("por_dep", number_format($por_dep, 2, ".", ",") . "%");
			}
			else
				$tpl->assign("por_dep", "&nbsp;");
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
		
		// Datos para los enlaces
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_SESSION['efe']['fecha2'],$temp);
		
		// Estado de cuenta
		$tpl->assign("tabla.num_cia",$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]);
		$tpl->assign("tabla.dia",/*$temp[1]*/date("d", mktime(0,0,0,$temp[2]+1,0,$temp[3])));
		$tpl->assign("tabla.mes",$temp[2]);
		$tpl->assign("tabla.anio",$temp[3]);
	}
	else {
		//$tpl->newBlock("vacia");
		header("location: ./ban_con_dep.php?accion=siguiente&idcia=&num_cia=");
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