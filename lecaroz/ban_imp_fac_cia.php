<?php
// CONSULTA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'

define ('IDSCREEN',1241); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

function buscar_dep($array, $fecha) {
	if (!$array)
		return FALSE;
	
	for ($i=0; $i<count($array); $i++)
		if ($array[$i]['fecha'] == $fecha)
			return $i;
	
	return FALSE;
}

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay facturas";
$descripcion_error[2] = "No hay registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Procesar datos
$num_cia = $_GET['num_cia'];
$mes = $_GET['mes'];
$anio = $_GET['anio'];
$dia = ($_GET['dia'] > 0 && $_GET['dia'] < 32) ? $_GET['dia'] : 1;

// Fechas d inicio y fin
$fecha1 = "$dia/$mes/$anio";
$fecha2 = date("d/m/Y",mktime(0,0,0,$mes+1,0,$anio));

// Seleccionar plantilla
$plantilla = ($_GET['tamano'] == "carta")?"factura_carta.tpl":"factura_oficio.tpl";

// Depositos del mes
$sql = "SELECT fecha,SUM(importe) AS importe FROM estado_cuenta WHERE ((num_cia = $num_cia AND num_cia_sec IS NULL) OR num_cia_sec = $num_cia) AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov IN (1,16,44) GROUP BY fecha ORDER BY fecha";
$result = ejecutar_script($sql,$dsn);

// Depositos alternativos del mes
$sql = "SELECT fecha,dep1,dep2 FROM depositos_alternativos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha,dep1,dep2 ORDER BY fecha";
$alt = ejecutar_script($sql,$dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/$plantilla" );
$tpl->prepare();

// Obtener porcentajes por factura
$sql = "SELECT * FROM catalogo_porcentajes_facturas WHERE num_cia = $num_cia ORDER BY porcentaje DESC";
$porc = ejecutar_script($sql,$dsn);

// Conjuntar depositos normales y alternativos
$count = $result ? count($result) : 0;
$numfilas = $alt ? count($alt) : 0;
if (!isset($_GET['alt']))
	for ($i=0; $i<$numfilas; $i++)
		if ($index = buscar_dep($result,$alt[$i]['fecha']))
			$result[$index]['importe'] += $alt[$i]['dep1'] + $alt[$i]['dep2'];
		else {
			$result[$count]['fecha'] = $alt[$i]['fecha'];
			$result[$count]['importe'] = $alt[$i]['dep1'] + $alt[$i]['dep2'];
			$count++;
		}
else {
	$count = 0;
	$result = array();
	for ($i=0; $i<$numfilas; $i++) {
		$result[$count]['fecha'] = $alt[$i]['fecha'];
		$result[$count]['importe'] = $alt[$i]['dep1'] + $alt[$i]['dep2'];
		$count++;
	}
}

if (count($result) < 1) {
	$tpl->newBlock("cerrar");
	$tpl->assign("No hay facturas por imprimir");
	$tpl->printToScreen();
	die;
}

// Aplica IVA?
$sql = "SELECT aplica_iva FROM catalogo_companias WHERE num_cia = $num_cia";
$temp = ejecutar_script($sql,$dsn);
$iva = ($temp && $temp[0]['aplica_iva'] == "t") ? 0.16 : 0.00;

/*for ($i=0; $i<count($result); $i++) {
	$sql = "SELECT SUM(importe_total) AS importe FROM facturas_clientes WHERE num_cia = $num_cia AND fecha = '{$result[$i]['fecha']}'";
	$dif = ejecutar_script($sql,$dsn);
	
	$total = $result[$i]['importe'] - $dif[0]['importe'];
	$subtotal = $total / (1 + $iva);
	
	if ($total == 0)
		continue;
	
	$tpl->newBlock("factura");
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$result[$i]['fecha'],$fecha);
	
	$tpl->assign("dia1",$fecha[1]);
	$tpl->assign("dia2",$fecha[1]);
	$tpl->assign("mes",mes_escrito($fecha[2]));
	$tpl->assign("anio",$fecha[3]);
	
	$tpl->assign("nombre","PUBLICO EN GENERAL");
	$tpl->assign("direccion","Conocida");
	$tpl->assign("rfc","&nbsp;");
	
	// Desglozar por porcentajes
	if (!$porc) {
		$tpl->assign("cantidad1","1");
		$tpl->assign("descripcion1","Venta del día: ".$result[$i]['fecha']);
		$tpl->assign("pu1","&nbsp;");
		$tpl->assign("importe1",number_format($total,2,".",","));
	}
	else {
		for ($j=0; $j<count($porc); $j++) {
			$tpl->assign("cantidad".($j+1),"1");
			$tpl->assign("descripcion".($j+1),"Venta del día: ".$result[$i]['fecha']);
			$tpl->assign("pu".($j+1),"&nbsp;");
			$tpl->assign("importe".($j+1),number_format(($subtotal*$porc[$j]['porcentaje']/100),2,".",","));
		}
	}
	
	$tpl->assign("total_escrito",num2string($total));
	$tpl->assign("subtotal",number_format($subtotal,2,".",","));
	$tpl->assign("iva",($iva == 0.15)?number_format($total-$subtotal,2,".",","):"&nbsp;");
	$tpl->assign("total",number_format($total,2,".",","));
}*/

// Obtener todas las facturas de los clientes
$sql = "SELECT SUM(importe_total) AS importe FROM facturas_clientes WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
$fac_cli = ejecutar_script($sql, $dsn);

$facs = array();
$cont = 0;
for ($i=0; $i<count($result); $i++) {
	if ($fac_cli[0]['importe'] > 0 && $result[$i]['importe'] - $fac_cli[0]['importe'] > 0) {
		$total = $result[$i]['importe'] - $fac_cli[0]['importe'];
		$fac_cli[0]['importe'] = 0;
	}
	else if ($fac_cli[0]['importe'] > 0 && $result[$i]['importe'] - $fac_cli[0]['importe'] <= 0) {
		$total = $result[$i]['importe'] / 2;
		$fac_cli[0]['importe'] -= $result[$i]['importe'] / 2;
	}
	else
		$total = $result[$i]['importe'];
	
	$subtotal = $total / (1 + $iva);
	
	if ($total == 0)
		continue;
	
	$facs[$cont]['fecha'] = $result[$i]['fecha'];
	$facs[$cont]['total'] = $total;
	$facs[$cont]['subtotal'] = $subtotal;
	$cont++;
}

if ($_GET['orden'] > 0) $facs = array_reverse($facs);

$dia = /*1*/$_GET['dia'];
$ok = true;
foreach ($facs as $fac) {
	$tpl->newBlock("factura");
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",/*$result[$i]['fecha']*/$fac['fecha'],$fecha);
	
	if ($dia != $fecha[1]) $ok = false;
	
	$tpl->assign("dia1",$fecha[1]);
	$tpl->assign("dia2",$fecha[1]);
	$tpl->assign("mes",mes_escrito($fecha[2]));
	$tpl->assign("anio",$fecha[3]);
	
	$tpl->assign("nombre","PUBLICO EN GENERAL");
	$tpl->assign("direccion","Conocida");
	$tpl->assign("rfc","&nbsp;");
	
	// Desglozar por porcentajes
	if (!$porc) {
		$tpl->assign("cantidad1","1");
		$tpl->assign("descripcion1","Venta del día: "./*$result[$i]['fecha']*/$fac['fecha']);
		$tpl->assign("pu1","&nbsp;");
		$tpl->assign("importe1",number_format(/*$total*/$fac['total'],2,".",","));
	}
	else {
		for ($j=0; $j<count($porc); $j++) {
			$tpl->assign("cantidad".($j+1),"1");
			$tpl->assign("descripcion".($j+1),"Venta del día: "./*$result[$i]['fecha']*/$fac['fecha']);
			$tpl->assign("pu".($j+1),"&nbsp;");
			$tpl->assign("importe".($j+1),number_format((/*$subtotal*/$fac['subtotal']*$porc[$j]['porcentaje']/100),2,".",","));
		}
	}
	
	$tpl->assign("total_escrito",num2string(/*$total*/$fac['total']));
	$tpl->assign("subtotal",number_format(/*$subtotal*/$fac['subtotal'],2,".",","));
	$tpl->assign("iva",($iva == 0.15)?number_format(/*$total-$subtotal*/$fac['total'] - $fac['subtotal'],2,".",","):"&nbsp;");
	$tpl->assign("total",number_format(/*$total*/$fac['total'],2,".",","));
}

if (!$ok) {
	$script = "function alerta(){if (confirm('Hay días sin depósitos, ¿desea continuar imprimiendo?')) return; else self.close();} window.onload=alerta();";
	$tpl->assign('_ROOT.otros', $script);
}

$tpl->printToScreen();
?>