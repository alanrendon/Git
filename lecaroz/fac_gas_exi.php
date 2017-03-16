<?php
// CONSUMO DE AVIO
// Tablas varias ''
// Menu 'Panaderías->Producción'

//define ('IDSCREEN',1222); // ID de pantalla

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
$descripcion_error[1] = "La compa&ntilde;&iacute;a no existe en la Base de Datos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_gas_exi.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Capturar compañía -------------------------------------------------------
if (!isset($_GET['mes'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign(date("n"),"selected");
	$tpl->assign("anio",date("Y"));
	
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
	die();
}

$fecha1 = "1/$_GET[mes]/$_GET[anio]";
$fecha2 = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));
$fecha_historico = date("d/m/Y",mktime(0,0,0,$_GET['mes'],0,$_GET['anio']));

$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia < 100 OR num_cia BETWEEN 100 AND 200 OR num_cia BETWEEN 702 AND 799 ORDER BY num_cia";
$cia = ejecutar_script($sql,$dsn);

$numfilas_x_hoja = 59;

$numfilas = $numfilas_x_hoja;
for ($i=0; $i<count($cia); $i++) {
	if ($numfilas == $numfilas_x_hoja) {
		$tpl->newBlock("listado");
		$tpl->assign("mes",mes_escrito($_GET['mes']));
		$tpl->assign("anio",$_GET['anio']);
		
		$numfilas = 0;
	}
	
	$num_cia = $cia[$i]['num_cia'];
	$nombre  = $cia[$i]['nombre_corto'];
	
	// Obtener saldos anteriores de hitorico_inventario
	$sql  = "SELECT codmp,existencia,precio_unidad FROM historico_inventario WHERE num_cia = $num_cia ";
	$sql .= "AND fecha = '$fecha_historico' AND codmp = 90";
	$saldo_anterior = ejecutar_script($sql,$dsn);
	
	// Obtener saldos de materias primas
	$sql  = "SELECT codmp,tipo_mov,cantidad,precio_unidad,total_mov,descripcion,fecha FROM mov_inv_real WHERE num_cia = $num_cia ";
	$sql .= "AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND codmp = 90 ORDER BY fecha,tipo_mov";
	$saldo = ejecutar_script($sql,$dsn);
	
	$unidades_inicio = $saldo_anterior[0]['existencia'];
	$valores_inicio  = $saldo_anterior[0]['existencia'] * $saldo_anterior[0]['precio_unidad'];
	
	$unidades = $saldo_anterior[0]['existencia'];
	$valores  = $saldo_anterior[0]['existencia'] * $saldo_anterior[0]['precio_unidad'];
	
	$num_entradas = 0;
	$entradas = 0;
	$ultima = "";
	$unidades_entrada = 0;
	$valores_entrada  = 0;
	$unidades_salida  = 0;
	$valores_salida   = 0;
	$costo_promedio   = round($saldo_anterior[0]['precio_unidad'],3);
	$cantidad_anterior = 0;
	for ($j=0; $j<count($saldo); $j++) {
		// Salidas
		if ($saldo[$j]['tipo_mov'] == "t") {
			$unidades -= round($saldo[$j]['cantidad'],2);
			if ($unidades < 0)
				$valores = 0;
			else
				$valores  -= round($saldo[$j]['cantidad'] * $costo_promedio,2);
			$cantidad_anterior = $unidades;
			
			$unidades_salida += $saldo[$j]['cantidad'];
			$valores_salida  += $saldo[$j]['cantidad'] * $costo_promedio;
		}
		// Entradas
		else if ($saldo[$j]['tipo_mov'] == "f") {
			@$precio_unidad = round($saldo[$j]['total_mov'] / $saldo[$j]['cantidad'],3);
			$unidades += $saldo[$j]['cantidad'];
			if (round($cantidad_anterior + $saldo[$j]['cantidad'],2) > 0)
				$valores  += round($saldo[$j]['total_mov'],2);
			else
				$valores = 0;
			if (round($cantidad_anterior,2) <= 0)
				$costo_promedio = round($precio_unidad,3);
			else
				$costo_promedio = round($valores / $unidades,3);
			$cantidad_anterior = $unidades;
			
			$unidades_entrada += $saldo[$j]['cantidad'];
			$valores_entrada  += $saldo[$j]['cantidad'] * $precio_unidad;
			
			// Contar entradas por facturas
			if (strpos($saldo[$j]['descripcion'],"COMPRA") !== FALSE) {
				$num_entradas++;
				$ultima = $saldo[$j]['fecha'];
				$entradas += $saldo[$j]['cantidad'];
			}
		}
	}
	
	// Calcular promedio
	$sql = "SELECT sum(total_produccion) FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha1' AND '$fecha2'";
	$produccion = ejecutar_script($sql,$dsn);
	$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia=$num_cia AND codgastos=90 AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$gas = ejecutar_script($sql,$dsn);
	@$gas_pro = ($gas[0]['sum'] / $produccion[0]['sum']) * 100;
	
	$tpl->newBlock("fila");
	$tpl->assign("num_cia",$num_cia);
	$tpl->assign("nombre_cia",$nombre);
	$tpl->assign("inicial",$unidades_inicio != 0 ? number_format($unidades_inicio,2,".",",") : "&nbsp;");
	$tpl->assign("entradas",$entradas != 0 ? number_format($entradas,2,".",",") : "&nbsp;");
	$tpl->assign("final",$unidades != 0 ? number_format($unidades,2,".",",") : "&nbsp;");
	$tpl->assign("no_entradas",$num_entradas != 0 ? $num_entradas : "&nbsp;");
	$diferencia =  $unidades_inicio + $entradas - $unidades;
	$tpl->assign("diferencia",$diferencia != 0 ? "<font color=\"#".($diferencia > 0 ? "0000FF" : "FF0000")."\">".number_format(abs($diferencia),2,".",",")."</font>" : "&nbsp;");
	$tpl->assign("porcentaje",$gas_pro != 0 ? number_format($gas_pro,3,".",",") : "&nbsp;");
	$tpl->assign("ultima",$ultima != "" ? $ultima : "&nbsp;");
	
	$numfilas++;
}

$tpl->printToScreen();
?>