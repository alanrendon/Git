<?php
// CAPTURA DE OTROS DEPOSITOS
// Tabla 'estado_cuenta'
// Menu

define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Error en la fecha de captura";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

$users = array(28, 29, 30, 31);
if ($_SESSION['tipo_usuario'] != 1) die('Modificando');
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dep_otros.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// --------------------------------- Almacenar datos ---------------------------------------------------------

if (isset($_POST['numfilas'])) {
	// Almacenar registros temporalmente
	$_SESSION['car']['mes'] = $_POST['mes'];
	$_SESSION['car']['anio'] = $_POST['anio'];
	$_SESSION['car']['numfilas'] = $_POST['numfilas'];
	
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		$_SESSION['car']['num_cia'.$i] = $_POST['num_cia'.$i];
		$_SESSION['car']['nombre_cia'.$i] = $_POST['nombre'.$i];
		$_SESSION['car']['dia'.$i] = $_POST['dia'.$i];
		$_SESSION['car']['importe'.$i] = $_POST['importe'.$i];
		$_SESSION['car']['concepto'.$i] = $_POST['concepto'.$i];
	}
	
	$count = 0;
	$fecha_cap = date("d/m/Y");
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['num_cia'.$i] > 0 && $_POST['dia'.$i] > 0 && $_POST['importe'.$i] != "") {
			$otros['num_cia'.$count] = $_POST['num_cia'.$i];
			$otros['fecha'.$count] = $_POST['dia'.$i]."/".$_POST['mes']."/".$_POST['anio'];
			$otros['importe'.$count] = get_val($_POST['importe'.$i]);
			$otros['fecha_cap'.$count] = $fecha_cap;
			$otros['acumulado'.$count] = "true";
			$otros['concepto'.$count] = $_POST['concepto'.$i];
			$otros['iduser'.$count] = $_SESSION['iduser'];
			$otros['tsins'.$count] = date('d/m/Y H:i:s');
			$count++;
		}
	}
	
	if (isset($otros)) {
		$db = new DBclass($dsn,"otros_depositos",$otros);
		$db->xinsertar();
	}
	
	if ($_POST['con'] == 1) {
		unset($_SESSION['car']);
		header("location: ./ban_con_dep_v2.php");
	}
	else
		header("location: ./ban_dep_otros.php?listado=1&mes=$_POST[mes]&anio=$_POST[anio]&idnombre=");
	die;
}
//----------------------------------------------------------------GENERA LISTADO DESPUES DE LA INSERCION	
if (isset($_GET['listado'])) {
	$tpl->newBlock("listado");
	
	$tpl->assign("dia",date("d"));
	$tpl->assign("anio",date("Y"));
	switch (date("m")) {
		case 1: $tpl->assign("mes","Enero"); break;
		case 2: $tpl->assign("mes","Febrero"); break;
		case 3: $tpl->assign("mes","Marzo"); break;
		case 4: $tpl->assign("mes","Abril"); break;
		case 5: $tpl->assign("mes","Mayo"); break;
		case 6: $tpl->assign("mes","Junio"); break;
		case 7: $tpl->assign("mes","Julio"); break;
		case 8: $tpl->assign("mes","Agosto"); break;
		case 9: $tpl->assign("mes","Septiembre"); break;
		case 10: $tpl->assign("mes","Octubre"); break;
		case 11: $tpl->assign("mes","Noviembre"); break;
		case 12: $tpl->assign("mes","Diciembre"); break;
	}
	
	$sql = "SELECT num_cia,cc.nombre,fecha,concepto,importe,fecha_cap,num_cia_primaria,0 as status FROM otros_depositos";
	$sql .= " LEFT JOIN catalogo_companias AS cc USING(num_cia) WHERE acumulado = 'TRUE'" . ($_SESSION['iduser'] != 1 ? " AND iduser = $_SESSION[iduser]" : '') . " AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 998" : "1 AND 899");
	$sql .= " ORDER BY num_cia_primaria,fecha,num_cia";
	$result = ejecutar_script($sql,$dsn);
	
	if ($result) {
		$gran_total = 0;
		
		$fecha = NULL;
		$num_cia = NULL;
		$rows = 0;
		$status_cantera = FALSE;
		for ($i=0; $i<count($result); $i++) {
			if ($fecha != $result[$i]['fecha'] || $num_cia != $result[$i]['num_cia_primaria']) {
				if ($rows > 1) {
					$tpl->newBlock("total");
					$tpl->assign("total",number_format($total,2,".",","));
				}
				
				$fecha = $result[$i]['fecha'];
				$num_cia = $result[$i]['num_cia_primaria'];
				
				// [20-Mar-2009] Si no hay compañía Cantera (16) buscar los depositos de la 44 con concepto CANTERA y moverlo de lugar
				if ($num_cia > 16 && !$status_cantera) {
					$status_cantera = TRUE;
					
					$tpl->newBlock("grupo");
					
					$rows = 0;
					$total = 0;
					
					for ($j = 0; $j < count($result); $j++)
						if ($result[$j]['status'] == 0 && $result[$j]['num_cia'] == 44 && $result[$i]['fecha'] == $result[$j]['fecha'] && strpos($result[$j]['concepto'], 'CANTERA') !== FALSE) {
							$tpl->newBlock("fila_lis");
							$tpl->assign("num_cia",$result[$j]['num_cia']);
							$tpl->assign("nombre_cia",$result[$j]['nombre']);
							$tpl->assign("fecha",$result[$j]['fecha']);
							$tpl->assign("concepto",$result[$j]['concepto']);
							$tpl->assign("deposito",number_format($result[$j]['importe'],2,".",","));
							
							$total += $result[$j]['importe'];
							$gran_total += $result[$j]['importe'];
							$result[$j]['status'] = 1;
							$rows++;
						}
					
					if ($rows > 1) {
						$tpl->newBlock("total");
						$tpl->assign("total",number_format($total,2,".",","));
					}
				}
				
				$rows = 0;
				$total = 0;
				
				$tpl->newBlock("grupo");
			}
			if ($result[$i]['status'] == 0) {
				$tpl->newBlock("fila_lis");
				$tpl->assign("num_cia",$result[$i]['num_cia']);
				$tpl->assign("nombre_cia",$result[$i]['nombre']);
				$tpl->assign("fecha",$result[$i]['fecha']);
				$tpl->assign("concepto",$result[$i]['concepto']);
				$tpl->assign("deposito",number_format($result[$i]['importe'],2,".",","));
				
				$total += $result[$i]['importe'];
				$gran_total += $result[$i]['importe'];
				$result[$i]['status'] = 1;
				$rows++;
				
				// [2007/04/26] Si la compañía es Cantera (16), buscar en los depositos de la 44 con concepto CANTERA y moverlo de lugar
				if ($result[$i]['num_cia'] == 16) {
					$status_cantera = TRUE;
					
					for ($j = 0; $j < count($result); $j++)
						if ($result[$j]['status'] == 0 && $result[$j]['num_cia'] == 44 && $result[$i]['fecha'] == $result[$j]['fecha'] && strpos($result[$j]['concepto'], 'CANTERA') !== FALSE) {
							$tpl->newBlock("fila_lis");
							$tpl->assign("num_cia",$result[$j]['num_cia']);
							$tpl->assign("nombre_cia",$result[$j]['nombre']);
							$tpl->assign("fecha",$result[$j]['fecha']);
							$tpl->assign("concepto",$result[$j]['concepto']);
							$tpl->assign("deposito",number_format($result[$j]['importe'],2,".",","));
							
							$total += $result[$j]['importe'];
							$gran_total += $result[$j]['importe'];
							$result[$j]['status'] = 1;
							$rows++;
						}
				}
			}
		}
		if ($rows > 1) {
			$tpl->newBlock("total");
			$tpl->assign("total",number_format($total,2,".",","));
		}
		
		$tpl->assign("listado.gran_total",number_format($gran_total,2,".",","));
		$total_mes = ejecutar_script("SELECT SUM(importe) FROM otros_depositos WHERE fecha >= '1/$_GET[mes]/$_GET[anio]' AND fecha <= '".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 998" : "1 AND 899"),$dsn);
		$total_mes_ant = ejecutar_script("SELECT SUM(importe) FROM otros_depositos WHERE fecha >= '" . date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] - 1, 1, $_GET['anio'])) . "' AND fecha <= '".date("d/m/Y",mktime(0,0,0,$_GET['mes'],0,$_GET['anio']))."' AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 998" : "1 AND 899"),$dsn);
		$tpl->assign("listado.total_mes_ant", number_format($total_mes_ant[0]['sum'], 2, ".", ","));
		$tpl->assign("listado.total_mes",number_format($total_mes[0]['sum'],2,'.',','));
		
		$tmp = ejecutar_script("SELECT tipo_mov, sum(importe) FROM gastos_caja WHERE fecha BETWEEN '1/$_GET[mes]/$_GET[anio]' AND '" . date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio'])) . "' AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 998" : "1 AND 899") . " GROUP BY tipo_mov", $dsn);
		$tmp_ant = ejecutar_script("SELECT tipo_mov, sum(importe) FROM gastos_caja WHERE fecha BETWEEN '" . date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] - 1, 1, $_GET['anio'])) . "' AND '" . date("d/m/Y",mktime(0,0,0,$_GET['mes'],0,$_GET['anio'])) . "' AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 998" : "1 AND 899") . " GROUP BY tipo_mov", $dsn);
		
		$gastos = 0;
		if ($tmp)
			foreach ($tmp as $reg)
				$gastos += $reg['tipo_mov'] == 'f' ? $reg['sum'] : -$reg['sum'];
		
		$gastos_ant = 0;
		if ($tmp_ant)
			foreach ($tmp_ant as $reg)
				$gastos_ant += $reg['tipo_mov'] == 'f' ? $reg['sum'] : -$reg['sum'];
		
		$tpl->assign('listado.total_mes_ant_gas', number_format($gastos_ant, 2, '.', ','));
		$tpl->assign('listado.total_mes_gas', number_format($gastos, 2, '.', ','));
		
		$tpl->printToScreen();
	}
	else
		header("location: ./ban_dep_otros.php");
	
	die;
}

// Pedir datos iniciales
if (!isset($_GET['numfilas'])) {
	if (isset($_SESSION['car']))
		unset($_SESSION['car']);
	
	$tpl->newBlock("datos");
	$tpl->assign("anio", date("d") < 3 ? date("Y", mktime(0, 0, 0, date("n"), 0, date("Y"))) : date("Y"));
	$tpl->assign(date("d") < 3 ? date("n", mktime(0, 0, 0, date("n"), 0, date("Y"))) : date("n"),"selected");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	$tpl->printToScreen();
	die;
}
//----------------------------------------------------------------------------CAPTURA DE LOS DEPOSITOS
$tpl->newBlock("captura");
$tpl->assign("tabla","estado_cuenta");
$tpl->assign("mes",$_GET['mes']);
$tpl->assign("anio",$_GET['anio']);
$tpl->assign("con", isset($_GET['con']) ? 1 : "");
$tpl->assign("regresar", isset($_GET['con']) ? "./ban_con_dep_v2.php" : "./ban_dep_otros.php");

$tpl->assign("numfilas",$_GET['numfilas']);

// En caso de generar solo el listado
if ($_GET['gen_listado'] == 'TRUE') {
	header("location: ./ban_dep_otros.php?listado=1&mes=$_GET[mes]&anio=$_GET[anio]");
	die;
}

// [13-Ene-2010] Solo permitir capturar movimientos dentro de los ultimos 2 meses
$ts_tope = mktime(0, 0, 0, date('n') - 1, 1, date('Y'));
$ts_cap = mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio']);
if ($ts_cap < $ts_tope) {
	header('location: ./ban_dep_otros.php?codigo_error=1');
	die;
}

// Si se empieza con un nuevo listado, poner bandera de acumulado en 'FALSE'
if ($_GET['tipo_con'] == 'FALSE')
	ejecutar_script("UPDATE otros_depositos SET acumulado = 'FALSE' WHERE acumulado = 'TRUE' AND iduser = $_SESSION[iduser]",$dsn);

switch ($_GET['mes']) {
	case 1: $tpl->assign("nombre_mes","ENERO"); break;
	case 2: $tpl->assign("nombre_mes","FEBRERO"); break;
	case 3: $tpl->assign("nombre_mes","MARZO"); break;
	case 4: $tpl->assign("nombre_mes","ABRIL"); break;
	case 5: $tpl->assign("nombre_mes","MAYO"); break;
	case 6: $tpl->assign("nombre_mes","JUNIO"); break;
	case 7: $tpl->assign("nombre_mes","JULIO"); break;
	case 8: $tpl->assign("nombre_mes","AGOSTO"); break;
	case 9: $tpl->assign("nombre_mes","SEPTIEMBRE"); break;
	case 10: $tpl->assign("nombre_mes","OCTUBRE"); break;
	case 11: $tpl->assign("nombre_mes","NOVIEMBRE"); break;
	case 12: $tpl->assign("nombre_mes","DICIEMBRE"); break;
}
// Número máximo de días
switch ($_GET['mes']) {
	case 1:  $maxdias = 31; break;
	case 2:  $maxdias = ($_GET['anio'] % 4 == 0)?29:28; break;
	case 3:  $maxdias = 31; break;
	case 4:  $maxdias = 30; break;
	case 5:  $maxdias = 31; break;
	case 6:  $maxdias = 30; break;
	case 7:  $maxdias = 31; break;
	case 8:  $maxdias = 31; break;
	case 9:  $maxdias = 30; break;
	case 10: $maxdias = 31; break;
	case 11: $maxdias = 30; break;
	case 12: $maxdias = 31; break;
}
$tpl->assign("maxdias",$maxdias);

$mov = ejecutar_script("SELECT DISTINCT ON (cod_mov) cod_mov,descripcion FROM catalogo_mov_bancos WHERE tipo_mov='TRUE' ORDER BY cod_mov",$dsn);

$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 950" : "1 AND 800") . " ORDER BY num_cia ASC",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
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
	$tpl->assign("dia",isset($_SESSION['car'])?$_SESSION['car']['dia'.$i]:"");
	$tpl->assign("importe",isset($_SESSION['car'])?$_SESSION['car']['importe'.$i]:"");
	$tpl->assign("concepto",isset($_SESSION['car']) ? $_SESSION['car']['concepto'.$i] : "");
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