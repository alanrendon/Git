<?php
// CONSULTA DE GASTOS
// Tabla 'Rosticerías'
// Menu 'Rosticerías->Producción'

//define ('IDSCREEN',1241); // ID de pantalla

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
$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "No hay registros";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_gas_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['xcia'])) {
	$tpl->newBlock("obtener_datos");
	
	$tpl->assign(date("n")," selected");
	$tpl->assign("anio_actual",date("Y"));
	
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

// Por compañía
if ($_GET['xcia'] == "una") {
	// Verificar si existe compañía
	if (!existe_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),$dsn)) {
		header("location: ./ros_gas_con.php?codigo_error=1");
	}
	
	// Listado por gastos
	//***************************************************************************************************************************
	if ($_GET['tipo'] == "gastos") {
		$sql = "SELECT * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' ORDER BY codigo_edo_resultados DESC,codgastos,fecha ASC";
		$result = ejecutar_script($sql,$dsn);
		//TOTAL GASTOS NO INCLUIDOS
		$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=0";
		$gastos_no_incluidos= ejecutar_script($sql,$dsn);
		//TOTAL GASTOS OPERACION
		$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=1";
		$gastos_operaciones= ejecutar_script($sql,$dsn);
		//TOTAL GASTOS GENERALES
		$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=2";
		$gastos_generales= ejecutar_script($sql,$dsn);
		

		if ($result) {
			$tpl->newBlock("listado_x_cia_gastos");
			$tpl->assign("num_cia_una",$_GET['num_cia']);
			$nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
			$tpl->assign("nombre_cia_una",$nombre_cia[0]['nombre_corto']);
			$tpl->assign("mes",mes_escrito($_GET['mes'],TRUE));
			$tpl->assign("anio",$_GET['anio']);
			$cod_temp = $result[0]['codgastos'];
			$tpl->newBlock("codigo_gasto");
			$total_gastos = 0;
			$gran_total = 0;
			for ($i=0; $i<count($result); $i++) {
				if ($cod_temp != $result[$i]['codgastos']) {
					$tpl->newBlock("codigo_gasto");
					$cod_temp = $result[$i]['codgastos'];
					$total_gastos = 0;

					$tpl->newBlock("fila_gasto");
					$tpl->assign("id",$result[$i]['idmovimiento_gastos']);
					$tpl->assign("fecha_gasto",$result[$i]['fecha']);
					$tpl->assign("cod_gasto",$result[$i]['codgastos']);
					$tpl->assign("nombre_gasto",$result[$i]['descripcion']);
					$tpl->assign("concepto_gasto",$result[$i]['concepto']);
					$tpl->assign("importe_gasto",number_format($result[$i]['importe'],2,".",","));
					if($result[$i]['captura']=='t')
						$tpl->assign("disabled","disabled");

					$total_gastos += $result[$i]['importe'];
					$gran_total += $result[$i]['importe'];
				}
				else {
					$tpl->newBlock("fila_gasto");
					$tpl->assign("id",$result[$i]['idmovimiento_gastos']);
					$tpl->assign("fecha_gasto",$result[$i]['fecha']);
					$tpl->assign("cod_gasto",$result[$i]['codgastos']);
					$tpl->assign("nombre_gasto",$result[$i]['descripcion']);
					$tpl->assign("concepto_gasto",$result[$i]['concepto']);
					$tpl->assign("importe_gasto",number_format($result[$i]['importe'],2,".",","));
					if($result[$i]['captura']=='t')
						$tpl->assign("disabled","disabled");

					$cod_temp = $result[$i]['codgastos'];
					$total_gastos += $result[$i]['importe'];
					$gran_total += $result[$i]['importe'];
				}
				$tpl->gotoBlock("codigo_gasto");
				$tpl->assign("total_gasto",number_format($total_gastos,2,".",","));
			}
			$tpl->gotoBlock("listado_x_cia_gastos");
			
			$tpl->assign("gastos_no_incluidos",number_format($gastos_no_incluidos[0]['sum'],2,'.',','));
			$tpl->assign("gastos_operacion",number_format($gastos_operaciones[0]['sum'],2,'.',','));
			$tpl->assign("gastos_generales",number_format($gastos_generales[0]['sum'],2,'.',','));
			$tpl->assign("gran_total_gasto",number_format($gran_total,2,".",","));
			$tpl->printToScreen();
		}
		else {
			header("location: ./ros_gas_con.php?codigo_error=2");
			die;
		}
	}
	//***************************************************************************************************************************
	// Listado por totales
	else if ($_GET['tipo'] == "totales") {
		$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=1 ORDER BY codgastos";
		$codigos = ejecutar_script($sql,$dsn);
		if ($codigos) {
			$tpl->newBlock("listado_x_cia_totales");
			$super_gran_total = 0;

			$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=1 ORDER BY codgastos";
			$codigos = ejecutar_script($sql,$dsn);
			if ($codigos) {
				$tpl->newBlock("gastos_operacion");
				$gran_total = 0;
				for ($i=0; $i<count($codigos); $i++) {
					$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=1";
					$result = ejecutar_script($sql,$dsn);
					$tpl->newBlock("fila_totales");
					$tpl->assign("cod_total",$codigos[$i]['codgastos']);
					$tpl->assign("nombre_total",$codigos[$i]['descripcion']);
					$tpl->assign("importe_total",number_format($result[0]['sum'],2,".",","));
					$gran_total += $result[0]['sum'];
					$super_gran_total += $result[0]['sum'];
				}
				$tpl->gotoBlock("gastos_operacion");
				$tpl->assign("gran_total_total",number_format($gran_total,2,".",","));
			}
			
			$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=2 ORDER BY codgastos";
			$codigos = ejecutar_script($sql,$dsn);
			if ($codigos) {
				$tpl->newBlock("gastos_gral");
				$gran_total = 0;
				for ($i=0; $i<count($codigos); $i++) {
					$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=2";
					$result = ejecutar_script($sql,$dsn);
					$tpl->newBlock("fila_totales_gral");
					$tpl->assign("cod_total_gral",$codigos[$i]['codgastos']);
					$tpl->assign("nombre_total_gral",$codigos[$i]['descripcion']);
					$tpl->assign("importe_total_gral",number_format($result[0]['sum'],2,".",","));
					$gran_total += $result[0]['sum'];
					$super_gran_total += $result[0]['sum'];
				}
				$tpl->gotoBlock("gastos_gral");
				$tpl->assign("gran_total_total",number_format($gran_total,2,".",","));
			}
			
			$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=0 ORDER BY codgastos";
			$codigos = ejecutar_script($sql,$dsn);
			if ($codigos) {
				$tpl->newBlock("gastos_otros");
				$gran_total = 0;
				for ($i=0; $i<count($codigos); $i++) {
					$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=$_GET[num_cia] AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=0";
					$result = ejecutar_script($sql,$dsn);
					$tpl->newBlock("fila_totales_otros");
					$tpl->assign("cod_total_otros",$codigos[$i]['codgastos']);
					$tpl->assign("nombre_total_otros",$codigos[$i]['descripcion']);
					$tpl->assign("importe_total_otros",number_format($result[0]['sum'],2,".",","));
					$gran_total += $result[0]['sum'];
					$super_gran_total += $result[0]['sum'];
				}
				$tpl->gotoBlock("gastos_otros");
				$tpl->assign("gran_total_total",number_format($gran_total,2,".",","));
			}
			$tpl->gotoBlock("listado_x_cia_totales");
			$tpl->assign("gran_total",number_format($super_gran_total,2,".",","));

			$tpl->printToScreen();
		}
		else {
			header("location: ./ros_gas_con.php?codigo_error=2");
			die;
		}
	}
}
else if ($_GET['xcia'] == "todas") {
	// Listado por gastos
	if ($_GET['tipo'] == "gastos") {
		$cia = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia BETWEEN 301 AND 599 ORDER BY num_cia ASC",$dsn);
		
		$gran_total = 0;
		// Hacer un barrido de todas las rosticerías
		for ($j=0; $j<count($cia); $j++) {
			$sql = "SELECT * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=".$cia[$j]['num_cia']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' ORDER BY codgastos,fecha ASC";
			$result = ejecutar_script($sql,$dsn);
			if ($result) {
				$tpl->newBlock("listado_all_gastos");
				$tpl->newBlock("cia_all");
				$tpl->assign("num_cia_all",$cia[$j]['num_cia']);
				$tpl->assign("nombre_cia_all",$cia[$j]['nombre_corto']);
				$cod_temp = $result[0]['codgastos'];
				$tpl->newBlock("codigo_all");
				$total_gastos = 0;
				for ($i=0; $i<count($result); $i++) {
					if ($cod_temp != $result[$i]['codgastos']) {
						$tpl->newBlock("codigo_all");
						$cod_temp = $result[$i]['codgastos'];
						$total_gastos = 0;
	
						$tpl->newBlock("fila_all");
						$tpl->assign("fecha_all",$result[$i]['fecha']);
						$tpl->assign("cod_gasto_all",$result[$i]['codgastos']);
						$tpl->assign("nombre_gasto_all",$result[$i]['descripcion']);
						$tpl->assign("concepto_all",$result[$i]['concepto']);
						$tpl->assign("importe_all",number_format($result[$i]['importe'],2,".",","));
						$total_gastos += $result[$i]['importe'];
						$gran_total += $result[$i]['importe'];
					}
					else {
						$tpl->newBlock("fila_all");
						$tpl->assign("fecha_all",$result[$i]['fecha']);
						$tpl->assign("cod_gasto_all",$result[$i]['codgastos']);
						$tpl->assign("nombre_gasto_all",$result[$i]['descripcion']);
						$tpl->assign("concepto_all",$result[$i]['concepto']);
						$tpl->assign("importe_all",number_format($result[$i]['importe'],2,".",","));
						$cod_temp = $result[$i]['codgastos'];
						$total_gastos += $result[$i]['importe'];
						$gran_total += $result[$i]['importe'];
					}
					$tpl->gotoBlock("codigo_all");
					$tpl->assign("total_gasto_all",number_format($total_gastos,2,".",","));
				}
				$tpl->gotoBlock("listado_all_gastos");
				$tpl->assign("gran_total_all",number_format($gran_total,2,".",","));
			}
		}
		$tpl->printToScreen();
	}
	if ($_GET['tipo'] == "totales") {
		$cia = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia BETWEEN 301 AND 599 ORDER BY num_cia ASC",$dsn);
		
		for ($j=0; $j<count($cia); $j++) {
			$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=".$cia[$j]['num_cia']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=1 ORDER BY codgastos";
			$codigos = ejecutar_script($sql,$dsn);
			$super_gran_total = 0;
			if ($codigos) {
				$tpl->newBlock("listado_x_cia_totales");
	
				$tpl->assign("num_cia",$cia[$j]['num_cia']);
				$tpl->assign("nombre_cia",$cia[$j]['nombre']);
				
				switch($_GET['mes']) {
					case 1:  $mes = "Enero"; break;
					case 2:  $mes = "Febrero"; break;
					case 3:  $mes = "Marzo"; break;
					case 4:  $mes = "Abril"; break;
					case 5:  $mes = "Mayo"; break;
					case 6:  $mes = "Junio"; break;
					case 7:  $mes = "Julio"; break;
					case 8:  $mes = "Agosto"; break;
					case 9:  $mes = "Septiembre"; break;
					case 10: $mes = "Octubre"; break;
					case 11: $mes = "Noviembre"; break;
					case 12: $mes = "Diciembre"; break;
				}
				$tpl->assign("mes",$mes);
				$tpl->assign("anio",$_GET['anio']);
				
				$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=".$cia[$j]['num_cia']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=1 ORDER BY codgastos";
				$codigos = ejecutar_script($sql,$dsn);
				if ($codigos) {
					$tpl->newBlock("gastos_operacion");
					$gran_total = 0;
					for ($i=0; $i<count($codigos); $i++) {
						$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=".$cia[$j]['num_cia']." AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=1";
						$result = ejecutar_script($sql,$dsn);
						$tpl->newBlock("fila_totales");
						$tpl->assign("cod_total",$codigos[$i]['codgastos']);
						$tpl->assign("nombre_total",$codigos[$i]['descripcion']);
						$tpl->assign("importe_total",number_format($result[0]['sum'],2,".",","));
						$gran_total += $result[0]['sum'];
						$super_gran_total += $result[0]['sum'];
					}
					$tpl->gotoBlock("gastos_operacion");
					$tpl->assign("gran_total_total",number_format($gran_total,2,".",","));
				}
				
				$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=".$cia[$j]['num_cia']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=2 ORDER BY codgastos";
				$codigos = ejecutar_script($sql,$dsn);
				if ($codigos) {
					$tpl->newBlock("gastos_gral");
					$gran_total = 0;
					for ($i=0; $i<count($codigos); $i++) {
						$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=".$cia[$j]['num_cia']." AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=2";
						$result = ejecutar_script($sql,$dsn);
						$tpl->newBlock("fila_totales_gral");
						$tpl->assign("cod_total_gral",$codigos[$i]['codgastos']);
						$tpl->assign("nombre_total_gral",$codigos[$i]['descripcion']);
						$tpl->assign("importe_total_gral",number_format($result[0]['sum'],2,".",","));
						$gran_total += $result[0]['sum'];
						$super_gran_total += $result[0]['sum'];
					}
					$tpl->gotoBlock("gastos_gral");
					$tpl->assign("gran_total_total",number_format($gran_total,2,".",","));
				}
				
				$sql = "SELECT DISTINCT ON (codgastos) * FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=".$cia[$j]['num_cia']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=0 ORDER BY codgastos";
				$codigos = ejecutar_script($sql,$dsn);
				if ($codigos) {
					$tpl->newBlock("gastos_otros");
					$gran_total = 0;
					for ($i=0; $i<count($codigos); $i++) {
						$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia=".$cia[$j]['num_cia']." AND codgastos=".$codigos[$i]['codgastos']." AND fecha>='1/$_GET[mes]/$_GET[anio]' AND fecha<='".date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']))."' AND codigo_edo_resultados=0";
						$result = ejecutar_script($sql,$dsn);
						$tpl->newBlock("fila_totales_otros");
						$tpl->assign("cod_total_otros",$codigos[$i]['codgastos']);
						$tpl->assign("nombre_total_otros",$codigos[$i]['descripcion']);
						$tpl->assign("importe_total_otros",number_format($result[0]['sum'],2,".",","));
						$gran_total += $result[0]['sum'];
						$super_gran_total += $result[0]['sum'];
					}
					$tpl->gotoBlock("gastos_otros");
					$tpl->assign("gran_total_total",number_format($gran_total,2,".",","));
				}
				$tpl->gotoBlock("listado_x_cia_totales");
				$tpl->assign("gran_total",number_format($super_gran_total,2,".",","));
			}
		}
		$tpl->printToScreen();
	}
}
?>