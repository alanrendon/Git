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
$tpl = new TemplatePower( "./plantillas/ban/ban_con_dep_print.tpl" );
$tpl->prepare();

// Seleccionar script para menu

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

// Obtener efectivos de la compañía para el mes dado (dependiendo de si es panaderia o rosticería)
if (($_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] > 100 && $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] <= 200) /*|| $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 702*/ || $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 703 || $_SESSION['efe']['num_cia'.$_SESSION['efe']['next']] == 704)
	$sql = "SELECT num_cia,efectivo,fecha,'t' AS efe,'t' AS exp,'t' AS gas,'t' AS pro,'t' AS pas FROM total_companias WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha>='".$_SESSION['efe']['fecha1']."' AND fecha<='".$_SESSION['efe']['fecha2']."' ORDER BY fecha";
else {
	$sql = "SELECT num_cia,efectivo,fecha,efe,exp,gas,pro,pas FROM total_panaderias WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha>='".$_SESSION['efe']['fecha1']."' AND fecha<='".$_SESSION['efe']['fecha2']."' ORDER BY fecha";
}
$efectivo = ejecutar_script($sql,$dsn);

if ($efectivo) {
	$tpl->newBlock("tabla");
	
	$tpl->assign("num_cia",$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]);
	$nombre_cia = ejecutar_script("SELECT nombre,nombre_corto FROM catalogo_companias WHERE num_cia = ".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']],$dsn);
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
	$tpl->assign("nombre_corto",$nombre_cia[0]['nombre_corto']);
	
	// Obtener el total de otros depósitos del mes
	$sql = "SELECT SUM(importe) FROM otros_depositos WHERE num_cia=".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha>='".$_SESSION['efe']['fecha1']."' AND fecha<='".$_SESSION['efe']['fecha2']."'";
	$temp = ejecutar_script($sql,$dsn);
	$otros_depositos = ($temp[0]['sum'] > 0)?$temp[0]['sum']:0;

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
	$total_depositos = 0;
	$total_mayoreo = 0;
	
	for ($i=0; $i<count($efectivo); $i++) {
		// Buscar los depositos para x día
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
		$tpl->assign("dia$i",(int)$dia);
		$tpl->assign("efectivo$i",($efectivo[$i]['efectivo'] > 0)?number_format($efectivo[$i]['efectivo'],2,".",","):"&nbsp;");
		
		// Si hay depositos
		$depositos_total = 0;
		$mayoreo_total = 0;
		if ($deposito) {
			$tpl->assign("deposito$i",number_format($deposito[0]['importe'],2,".",","));
			$total_depositos += $deposito[0]['importe'];
			$depositos_total += $deposito[0]['importe'];
			
			if (count($deposito) > 1) {
				for ($j=1; $j<count($deposito); $j++) {
					$mayoreo_total += $deposito[$j]['importe'];
					$total_mayoreo += $deposito[$j]['importe'];
					$depositos_total += $deposito[$j]['importe'];
				}
				$tpl->assign("mayoreo$i",number_format($total_mayoreo,2,".",","));
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
			
			$tpl->assign("oficina$i",($otro_deposito > 0)?number_format($otro_deposito,2,".",","):"&nbsp;");
		}
		else if ($i == count($efectivo)-1) {
			$otro_deposito = $otros_depositos;
			$otros_depositos = 0;
			$tpl->assign("oficina$i",($otro_deposito > 0)?number_format($otro_deposito,2,".",","):"&nbsp;");
		}
		else
			$tpl->assign("oficina$i","&nbsp;");
		
		// Mostrar diferencia
		$depositos_total += $otro_deposito;
		$tpl->assign("diferencia$i",number_format($efectivo[$i]['efectivo']-$depositos_total,2,".",","));
		if (number_format($efectivo[$i]['efectivo']-$depositos_total,2,".","") >= 0)
			$tpl->assign("dif_color$i","0000FF");
		else
			$tpl->assign("dif_color$i","FF0000");
			
		$tpl->assign("total$i",number_format($depositos_total,2,".",","));
		$total_diferencias += $efectivo[$i]['efectivo']-$depositos_total;
		$gran_total += $depositos_total;
		
		// Sumar total de efectivos
		$total_efectivos += $efectivo[$i]['efectivo'];
		// Sumar total de otros depositos
		$total_otros += $otro_deposito;
	}
	
	$tpl->gotoBlock("tabla");
	// Trazar totales
	$tpl->assign("total_efectivos",number_format($total_efectivos,2,".",","));
	$tpl->assign("total_depositos",number_format($total_depositos,2,".",","));
	$tpl->assign("total_mayoreo",number_format($total_mayoreo,2,".",","));
	$tpl->assign("total_oficina",number_format($total_otros,2,".",","));
	$tpl->assign("total_diferencias",number_format($total_diferencias,2,".",","));
	$tpl->assign("gran_total",number_format($gran_total,2,".",","));
	
	// Trazar promedios
	$dias = count($efectivo);
	$tpl->assign("promedio_efectivos",number_format($total_efectivos/$dias,2,".",","));
	$tpl->assign("promedio_depositos",number_format($total_depositos/$dias,2,".",","));
	$tpl->assign("promedio_mayoreo",number_format($total_mayoreo/$dias,2,".",","));
	$tpl->assign("promedio_oficina",number_format($total_otros/$dias,2,".",","));
	$tpl->assign("promedio_total",number_format($gran_total/$dias,2,".",","));
	
	// Datos para los enlaces
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_SESSION['efe']['fecha2'],$temp);
	// Estado de cuenta
	$tpl->assign("tabla.num_cia",$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]);
	$tpl->assign("tabla.dia",$temp[1]);
	$tpl->assign("tabla.mes",$temp[2]);
	$tpl->assign("tabla.anio",$temp[3]);
	
	// Bloque de gastos y promedios
	$tpl->assign("porcentaje_depositos",number_format((($total_depositos+$total_mayoreo)/$total_efectivos)*100,2,".",","));
	$tpl->assign("porcentaje_oficinas",number_format(($total_otros/$total_efectivos)*100,2,".",","));
	$tpl->assign("suma_porcentajes",number_format(((($total_depositos+$total_mayoreo)/$total_efectivos)+($total_otros/$total_efectivos))*100,2,".",","));
	
	// Bloque de gastos
	$sql = "SELECT * FROM gastos_caja JOIN catalogo_gastos_caja ON(catalogo_gastos_caja.id=cod_gastos) WHERE num_cia = ".$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]." AND fecha >= '".$_SESSION['efe']['fecha1']."' AND fecha <= '".$_SESSION['efe']['fecha2']."'";
	$gasto = ejecutar_script($sql,$dsn);
	
	$total_ingreso = 0;
	$total_egreso = 0;
	$total_gastos = 0;

	if ($gasto && count($gasto) < 20) {
		for ($i=0; $i<count($gasto); $i++) {
			$tpl->assign("mov$i",$i+1);
			$tpl->assign("concepto$i",$gasto[$i]['descripcion']);
			if ($gasto[$i]['tipo_mov'] == "t") {
				$tpl->assign("ingreso$i",number_format($gasto[$i]['importe'],2,".",","));
				$total_ingreso += $gasto[$i]['importe'];
			}
			else if ($gasto[$i]['tipo_mov'] == "f") {
				$tpl->assign("egreso$i",number_format($gasto[$i]['importe'],2,".",","));
				$total_egreso += $gasto[$i]['importe'];
			}
		}
		$tpl->assign("total_ingreso",number_format($total_ingreso,2,".",","));
		$tpl->assign("total_egreso",number_format($total_egreso,2,".",","));
		$total_gastos = $total_ingreso - $total_egreso;
		$tpl->assign("total_gastos",number_format($total_gastos,2,".",","));
	}
	$repartido = $total_otros - ($total_gastos);
	$tpl->assign("repartido",number_format($repartido,2,".",","));
}

$tpl->printToScreen();
?>
