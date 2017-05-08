<?php
// PROCESO SECUENCIAL
// Tablas 'compra_directa', 'hoja_dia_rost', 'movimiento_gastos', 'total_companias'
// Menu 'No definido'

define ('IDSCREEN',2); // ID de pantalla

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
$descripcion_error[1] = "La fecha de captura ya se encuentra en el sistema";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_pro_sec.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// --------------------------------- PEDIR COMPAÑÍA ----------------------------------------------------------
if (!isset($_GET['num_cia']) && !isset($_GET['tabla'])) {
	// Vaciar variables de sesion anteriores a la captura
	if (isset($_SESSION['cd']) || isset($_SESSION['hd']) || isset($_SESSION['g'])) {
		unset($_SESSION['cd']);
		unset($_SESSION['hd']);
		unset($_SESSION['g']);
		
		unset($_SESSION['num_cia']);
		unset($_SESSION['fecha']);
	}
	$tpl->newBlock("num_cia");
	$tpl->assign("fecha",date("d/m/Y",mktime(0,0,0,date("m"),date("d")-1,date("Y"))));
	// Generar listado de compañías
	$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 100 AND 200 OR num_cia BETWEEN 702 AND 720 ORDER BY num_cia ASC",$dsn);
	for ($i=0; $i<count($cia); $i++) {
		$tpl->newBlock("nombre_cia_ini");
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}
	
	$tpl->printToScreen();
	die;
}
// --------------------------------- COMPRA DIRECTA ----------------------------------------------------------
else if (isset($_GET['tabla']) && $_GET['tabla'] == "compra_directa") {
	if (isset($_GET['num_cia'])) {
		$_SESSION['num_cia'] = $_GET['num_cia'];
		$ultima_fecha = ejecutar_script("SELECT fecha FROM total_companias WHERE num_cia=$_GET[num_cia] ORDER BY fecha DESC LIMIT 1",$dsn);
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$ultima_fecha[0]['fecha'],$temp);
		$_SESSION['fecha']   = date("d/m/Y",mktime(0,0,0,$temp[2],$temp[1]+1,$temp[3]))/*$_GET['fecha']*/;
		
		// Verificar que no exista ya la fecha de captura de proceso secuencial
		/*if (existe_registro("total_companias",array("num_cia","fecha"),array($_GET['num_cia'],$_GET['fecha']),$dsn)) {
			header("location: ./ros_pro_sec.php?codigo_error=1");
			die;
		}*/
	}
	
	if (!isset($_GET['codigo_error'])) {
		if (isset($_POST['tabla']) && $_POST['tabla'] == "hoja_diaria_rost") {
			if (!isset($_SESSION['hd']))
				$_SESSION['hd'] = array(); // Hoja Diaria
			
			// Almacenar valores de la pantalla de hoja diaria ----------------------
			for ($i=0; $i<$_POST['numfilas']; $i++) {
				$_SESSION['hd']['codmp'.$i]           = $_POST['codmp'.$i];
				$_SESSION['hd']['unidades'.$i]        = $_POST['unidades'.$i];
				$_SESSION['hd']['precio_unitario'.$i] = $_POST['precio_unitario'.$i];
				$_SESSION['hd']['precio_total'.$i]    = $_POST['precio_total'.$i];
			}
			$_SESSION['hd']['otros']              = $_POST['precio_total_otros'];
			
			$_SESSION['hd']['numfilas']           = $_POST['numfilas'];
			$_SESSION['hd']['precio_total_otros'] = $_POST['precio_total_otros'];
			$_SESSION['hd']['total']              = $_POST['venta_total'];
		}
	}
	// ----------------------------------------------------------------------

	// Crear bloque para la pantalla de compra directa
	$tpl->newBlock("compra_directa");
	
	$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_SESSION['num_cia']),"","",$dsn);
	$tpl->assign("cd_num_cia",$_SESSION['num_cia']);
	$tpl->assign("cd_nombre_cia",$cia[0]['nombre_corto']);
	// Descomponer fecha
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})",$_SESSION['fecha'], $fecha);
	$tpl->assign("cd_fecha_mov",date("d/m/Y",mktime(0,0,0,$fecha[2],$fecha[1],$fecha[3])));
	$tpl->assign("cd_fecha_pago",date("d/m/Y",mktime(0,0,0,$fecha[2],$fecha[1]+1,$fecha[3])));
	
	// Asignar total de la factura ---------------------------------------
	if (isset($_SESSION['cd']))
		$tpl->assign("cd_total",$_SESSION['cd']['total']);
	else
		$tpl->assign("cd_total","0.00");
	// -------------------------------------------------------------------
	
	// Obtener los listados de compañías, proveedores y materias prima
	$proveedor = obtener_registro("catalogo_proveedores",array(),array(),"num_proveedor","ASC",$dsn);
	$mp = ejecutar_script("SELECT codmp,nombre,precio_compra,precio_unidad FROM catalogo_mat_primas JOIN precios_guerra USING(codmp) JOIN inventario_real USING(codmp) WHERE catalogo_mat_primas.tipo_cia='FALSE' AND precios_guerra.num_cia=$_SESSION[num_cia] AND inventario_real.num_cia=$_SESSION[num_cia] ORDER BY codmp ASC;",$dsn);
	
	// Generar listado de proveedores
	for ($i=0; $i<count($proveedor); $i++) {
		$tpl->newBlock("nombre_proveedor");
		$tpl->assign("num_proveedor",$proveedor[$i]['num_proveedor']);
		$tpl->assign("nombre_proveedor",$proveedor[$i]['nombre']);
	}
	
	// Generar listado de materias prima para rosticerias
	for ($i=0; $i<count($mp); $i++) {
		$tpl->newBlock("nombre_mp");
		$tpl->assign("codmp",$mp[$i]['codmp']);
		$tpl->assign("nombre_mp",$mp[$i]['nombre']);
		$tpl->assign("precio_mp",$mp[$i]['precio_compra']);
		$tpl->assign("precio_min",$mp[$i]['precio_unidad']*0.80);
		$tpl->assign("precio_max",$mp[$i]['precio_unidad']*1.20);
	}
	
	$num_filas = 10;
	for ($i=0; $i<$num_filas; $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
		if ($i < $num_filas-1)
			$tpl->assign("next",$i+1);
		else
			$tpl->assign("next",$num_filas-1);
		
		if ($i > 0)
			$tpl->assign("back",$i-1);
		else
			$tpl->assign("back",0);
		
		if (isset($_SESSION['cd']['codmp'.$i]) && $_SESSION['cd']['codmp'.$i] > 0) {
			$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($_SESSION['cd']['codmp'.$i]),"codmp","ASC",$dsn);
			$pr = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_SESSION['cd']['num_proveedor'.$i]),"","",$dsn);
			
			$tpl->assign("cd_codmp",$_SESSION['cd']['codmp'.$i]);
			$tpl->assign("cd_nombre_mp",$mp[0]['nombre']);
			$tpl->assign("cd_cantidad",$_SESSION['cd']['cantidad'.$i]);
			$tpl->assign("cd_kilos",$_SESSION['cd']['kilos'.$i]);
			$tpl->assign("cd_precio_unit",$_SESSION['cd']['precio_unit'.$i]);
			$tpl->assign("cd_total",$_SESSION['cd']['total'.$i]);
			if ($_SESSION['cd']['aplica_gasto'.$i] == "TRUE")
				$tpl->assign("check","checked");
			$tpl->assign("cd_num_proveedor",$_SESSION['cd']['num_proveedor'.$i]);
			$tpl->assign("cd_nombre_proveedor",$pr[0]['nombre']);
			$tpl->assign("cd_numero_fact",$_SESSION['cd']['numero_fact'.$i]);
		}
		else {
			$tpl->assign("check","checked");
			$tpl->assign("cd_num_proveedor",289);
			$tpl->assign("cd_nombre_proveedor","COMPRAS DIRECTAS");
		}
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		if ($_GET['codigo_error'] == 1) {
			$tpl->newBlock("error");
			$tpl->newBlock("message");
			$tpl->assign( "message", "La factura no. ".$_GET['fac']." ya existe");
		}
		else if ($_GET['codigo_error'] == 2) {
			$tpl->newBlock("error");
			$tpl->newBlock("message");
			$tpl->assign( "message", "Fecha ya capturada");
		}
	}
	
	$tpl->printToScreen();
	die();
}
// --------------------------------- HOJA DIARIA ----------------------------------------------------------
else if (isset($_GET['tabla']) && $_GET['tabla'] == "hoja_diaria_rost") {
	// Almacenar temporalmente movimientos de gastos
	if ($_POST['tabla'] == "movimiento_gastos") {
		if (!isset($_SESSION['g']))
			$_SESSION['g'] = array();
		
		for ($i=0; $i<10; $i++) {
			$_SESSION['g']['codgastos'.$i] = $_POST['codgastos'.$i];
			$_SESSION['g']['concepto'.$i]  = $_POST['concepto'.$i];
			$_SESSION['g']['importe'.$i]   = $_POST['importe'.$i];
		}
		$_SESSION['g']['total_gastos']    = $_POST['total_gastos'];
		$_SESSION['g']['gastos_directos'] = $_POST['gastos_directos'];
		$_SESSION['g']['total']           = $_POST['gastos_dia'];
	}
	// Almacenar temporalmente movimientos de compra
	else if ($_POST['tabla'] == "compra_directa") {
		if (!isset($_SESSION['cd']))
			$_SESSION['cd'] = array();
		
		for ($i=0; $i<10; $i++) {
			$_SESSION['cd']['codmp'.$i]         = $_POST['codmp'.$i];
			$_SESSION['cd']['cantidad'.$i]      = $_POST['cantidad'.$i];
			$_SESSION['cd']['kilos'.$i]         = $_POST['kilos'.$i];
			$_SESSION['cd']['precio_unit'.$i]   = $_POST['precio_unit'.$i];
			$_SESSION['cd']['total'.$i]         = $_POST['total'.$i];
			if (isset($_POST['aplica_gasto'.$i]))
				$_SESSION['cd']['aplica_gasto'.$i] = $_POST['aplica_gasto'.$i];
			else
				$_SESSION['cd']['aplica_gasto'.$i] = "FALSE";
			$_SESSION['cd']['num_proveedor'.$i] = $_POST['num_proveedor'.$i];
			$_SESSION['cd']['numero_fact'.$i]   = $_POST['numero_fact'.$i];
		}
		$_SESSION['cd']['fecha_pago']    = $_POST['fecha_pago'];
		$_SESSION['cd']['total']         = $_POST['total'];
		
		// Consultar si ya hay capturas del dia
		if (existe_registro("total_companias",array("num_cia","fecha"),array($_SESSION['num_cia'],$_SESSION['fecha']),$dsn)) {
			header("location: ./ros_pro_sec.php?tabla=compra_directa&codigo_error=2");
		}
		
		// Consultar si los número de factura existen en la base de datos
		for ($i=0; $i<10; $i++)
			if ($_SESSION['cd']['numero_fact'.$i] > 0)
				if (existe_registro("compra_directa",array("num_cia","numero_fact"),array($_SESSION['num_cia'],$_SESSION['cd']['numero_fact'.$i]),$dsn)) {
					header("location: ./ros_pro_sec.php?tabla=compra_directa&codigo_error=1&fac=".$_SESSION['cd']['numero_fact'.$i]);
					die;
				}
	}
	
	// Crear bloque de captura
	$tpl->newBlock("hoja_diaria");
	
	// Poner datos de la compañía y la fecha
	$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),"","",$dsn);
	$tpl->assign("num_cia_hoja",    $_SESSION['num_cia']);
	$tpl->assign("nombre_cia_hoja", $cia[0]['nombre_corto']);
	$tpl->assign("fecha_hoja",      $_SESSION['fecha']);
	
	if (isset($_SESSION['hd'])) {
		$tpl->assign("hd_precio_total_otros", $_SESSION['hd']['otros']);
		$tpl->assign("hd_venta_total",        $_SESSION['hd']['total']);
		$tpl->assign("hd_precio_total_otros", $_SESSION['hd']['precio_total_otros']);
	}
	else
		$tpl->assign("hd_venta_total","0.00");
	
	// Obtener materias primas para rosticerias
	$mp = obtener_registro("catalogo_mat_primas",array("tipo_cia"),array("FALSE"),"orden","ASC",$dsn);
	$temp_ros = ejecutar_script("SELECT DISTINCT ON (codmp) * FROM inventario_real WHERE num_cia=$_POST[num_cia] AND codmp NOT IN (90,425,194,138,364)",$dsn);
	
	if (existe_registro("inventario_real",array("num_cia","codmp"),array($_SESSION['num_cia'],160),$dsn))
		$num_mp = count($temp_ros);
	else
		$num_mp = count($temp_ros) - 1;
	
	$disabled = FALSE;
	$materia = 0;
	$indice = 0;
	while ($materia < count($mp)) {
		if (existe_registro("inventario_real",array("num_cia","codmp"),array($_SESSION['num_cia'],$mp[$materia]['codmp']),$dsn)) {
			$ros = obtener_registro("inventario_real",array("num_cia","codmp"),array($_SESSION['num_cia'],$mp[$materia]['codmp']),"","",$dsn);
			$precio = ejecutar_script("SELECT precio_venta FROM precios_guerra WHERE num_cia=$_SESSION[num_cia] AND codmp=".$mp[$materia]['codmp'],$dsn);
			$tpl->newBlock("fila_hoja");
			
			$tpl->assign("i",$indice);
			if ($indice == $num_mp)
				$tpl->assign("next",0);
			else
				$tpl->assign("next",$indice+1);
				
			if ($indice == 0)
				$tpl->assign("back",$indice);
			else
				$tpl->assign("back",$indice-1);
			
			// Asignar código y nombre de materia prima
			$tpl->assign("codmp_hoja",$mp[$materia]['codmp']);
			$tpl->assign("nombre_mp_hoja",$mp[$materia]['nombre']);
			if ($precio)
				$tpl->assign("hd_precio_unitario",number_format($precio[0]['precio_venta'],2,".",""));
			else {
				$tpl->assign("hd_precio_unitario","SIN PRECIO");
				$disabled = TRUE;
			}
			
			// Existencia en inventario
			$existencia = $ros[0]['existencia'];
			
			// Restar facturas
			$fac = ejecutar_script("SELECT sum(cantidad) FROM fact_rosticeria WHERE num_cia = $_SESSION[num_cia] AND codmp = ".$mp[$materia]['codmp']." AND fecha_mov > '$_SESSION[fecha]'",$dsn);
			$existencia -= $fac[0]['sum'];
			
			// Buscar alguna actualizacion de las existencias de la materia prima
			for ($k=0; $k<10; $k++) {
				if (isset($_SESSION['cd']['codmp'.$k]) && ($_SESSION['cd']['codmp'.$k] == $mp[$materia]['codmp']))
					$existencia += $_SESSION['cd']['cantidad'.$k];
			}
			// Asignar existencia
			$tpl->assign("existencia",$existencia);
			
			// Restaurar valores anteriores
			if (isset($_SESSION['hd'])) {
				if ($_SESSION['hd']['unidades'.$indice] > 0)
					$tpl->assign("hd_unidades",number_format($_SESSION['hd']['unidades'.$indice],0,"",""));
				if ($_SESSION['hd']['precio_unitario'.$indice])
					$tpl->assign("hd_precio_unitario",number_format($_SESSION['hd']['precio_unitario'.$indice],2,".",""));
				if ($_SESSION['hd']['precio_total'.$indice])
					$tpl->assign("hd_precio_total",number_format($_SESSION['hd']['precio_total'.$indice],2,".",""));
			}
			$indice++;
			
			// Caso exclusivo para pollos normales, se debe incluir un renglon aparte para pollos adobados
			if ($mp[$materia]['codmp'] == 160) {
				$precio = ejecutar_script("SELECT precio_venta FROM precios_guerra WHERE num_cia=$_SESSION[num_cia] AND codmp=1601",$dsn);
				$tpl->newBlock("fila_hoja");
			
				$tpl->assign("i",$indice);
				
				if ($indice == $num_mp)
					$tpl->assign("next",0);
				else
					$tpl->assign("next",$indice+1);
					
				if ($indice == 0)
					$tpl->assign("back",$indice);
				else
					$tpl->assign("back",$indice-1);
				
				// Asignar código y nombre de materia prima
				$tpl->assign("codmp_hoja",$mp[$materia]['codmp']);
				$tpl->assign("nombre_mp_hoja","POLLOS ADOBADOS");
				if ($precio && $precio[0]['precio_venta'] > 0)
					$tpl->assign("hd_precio_unitario",number_format($precio[0]['precio_venta'],2,".",""));
				else if ($precio && $precio[0]['precio_venta'] <= 0)
					$disabled = TRUE;
				else {
					$tpl->assign("hd_precio_unitario","SIN PRECIO");
					$disabled = TRUE;
				}
				
				// Asignar existencia
				$tpl->assign("existencia",$existencia);
				
				// Restaurar valores anteriores
				if (isset($_SESSION['hd'])) {
					if ($_SESSION['hd']['unidades'.$indice] > 0)
						$tpl->assign("hd_unidades",number_format($_SESSION['hd']['unidades'.$indice],0,"",""));
					if ($_SESSION['hd']['precio_unitario'.$indice] > 0)
						$tpl->assign("hd_precio_unitario",number_format($_SESSION['hd']['precio_unitario'.$indice],2,".",""));
					if ($_SESSION['hd']['precio_total'.$indice] > 0)
						$tpl->assign("hd_precio_total",number_format($_SESSION['hd']['precio_total'.$indice],2,".",""));
				}
				$indice++;
			}
		}
		$materia++;
	}
	
	// Asignar número de filas
	$tpl->gotoBlock("hoja_diaria");
	$tpl->assign("numfilas",$indice);
	if ($disabled)
		$tpl->assign("disabled","disabled");
	
	$tpl->printToScreen();
	die();
}

// --------------------------------- GASTOS ----------------------------------------------------------------
else if (isset($_GET['tabla']) && $_GET['tabla'] == "movimiento_gastos") {
	// Almacenar temporalmente movimientos de hoja diaria
	if ($_POST['tabla'] == "hoja_diaria_rost") {
		if (!isset($_SESSION['hd']))
			$_SESSION['hd'] = array(); // Hoja Diaria
		
		for ($i=0; $i<$_POST['numfilas']; $i++) {
			$_SESSION['hd']['codmp'.$i]           = $_POST['codmp'.$i];
			$_SESSION['hd']['unidades'.$i]        = $_POST['unidades'.$i];
			$_SESSION['hd']['precio_unitario'.$i] = $_POST['precio_unitario'.$i];
			$_SESSION['hd']['precio_total'.$i]    = $_POST['precio_total'.$i];
		}
		$_SESSION['hd']['otros']              = $_POST['precio_total_otros'];
		
		$_SESSION['hd']['numfilas']           = $_POST['numfilas'];
		$_SESSION['hd']['precio_total_otros'] = $_POST['precio_total_otros'];
		$_SESSION['hd']['total']              = $_POST['venta_total'];
	}
	
	$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_SESSION['num_cia']),"","",$dsn);
	
	// Crear bloque de captura
	$tpl->newBlock("gastos");
	
	$tpl->assign("num_cia_gastos",$_SESSION['num_cia']);
	$tpl->assign("nombre_cia_gastos",$cia[0]['nombre_corto']);
	$tpl->assign("fecha_gastos",$_SESSION['fecha']);
	
	// Calcular gastos directos a partir de las compras ke aplican gastos
	$gastos_directos = 0;
	for ($i=0; $i<10; $i++) {
		if ($_SESSION['cd']['aplica_gasto'.$i] == "TRUE")
			$gastos_directos += $_SESSION['cd']['total'.$i];
	}
	
	// Asignar valores anteriores
	if (isset($_SESSION['g'])) {
		$tpl->assign("g_total_gastos",number_format($_SESSION['g']['total_gastos'],2,".",""));
		$tpl->assign("g_gastos_dia",number_format($_SESSION['g']['total_gastos']+$gastos_directos,2,".",""));
		$tpl->assign("gastos_directos",number_format($gastos_directos,2,".",""));
	}
	else {
		$tpl->assign("g_total_gastos","0.00");
		$tpl->assign("gastos_directos",number_format($gastos_directos,2,".",""));
		$tpl->assign("g_gastos_dia",number_format($gastos_directos,2,".",""));
	}
	
	$gasto = obtener_registro("catalogo_gastos",array(),array(),"codgastos","ASC",$dsn);
	for ($i=0; $i<count($gasto); $i++) {
		$tpl->newBlock("nombre_gasto");
		$tpl->assign("codgasto",$gasto[$i]['codgastos']);
		$tpl->assign("nombregasto",$gasto[$i]['descripcion']);
	}
	
	$tpl->assign("num_cia_gastos",$_SESSION['num_cia']);
	$tpl->assign("fecha_gastos",$_SESSION['fecha']);
	
	for ($i=0; $i<10; $i++) {
		$tpl->newBlock("fila_gastos");
		
		$tpl->assign("i",$i);
		if ($i < 10-1)
			$tpl->assign("next",$i+1);
		else
			$tpl->assign("next",0);
		
		if ($i > 0)
			$tpl->assign("back",$i-1);
		else
			$tpl->assign("back",10-1);
		
		if (isset($_SESSION['g']) && $_SESSION['g']['codgastos'.$i] > 0) {
			$gasto = obtener_registro("catalogo_gastos",array("codgastos"),array($_SESSION['g']['codgastos'.$i]),"codgastos","ASC",$dsn);
			
			$tpl->assign("g_codgastos",$_SESSION['g']['codgastos'.$i]);
			$tpl->assign("g_nombregasto",$gasto[0]['descripcion']);
			$tpl->assign("g_importe",$_SESSION['g']['importe'.$i]);
			$tpl->assign("g_concepto",$_SESSION['g']['concepto'.$i]);
		}
	}
	$tpl->gotoBlock("gastos");
	
	$tpl->printToScreen();
	die();
}

// --------------------------------- TOTALES -------------------------------------------------------------
if (isset($_GET['tabla']) && $_GET['tabla'] == "total_companias") {
// Almacenar temporalmente movimientos de gastos
	if ($_POST['tabla'] == "movimiento_gastos") {
		if (!isset($_SESSION['g']))
			$_SESSION['g'] = array();
		
		for ($i=0; $i<10; $i++) {
			$_SESSION['g']['codgastos'.$i] = $_POST['codgastos'.$i];
			$_SESSION['g']['concepto'.$i]  = $_POST['concepto'.$i];
			$_SESSION['g']['importe'.$i]   = $_POST['importe'.$i];
		}
		$_SESSION['g']['total_gastos']    = $_POST['total_gastos'];
		$_SESSION['g']['gastos_directos'] = $_POST['gastos_directos'];
		$_SESSION['g']['total']           = $_POST['gastos_dia'];
	}
		
	$pago_pre = ejecutar_script("SELECT sum(importe) FROM prestamos WHERE num_cia=$_SESSION[num_cia] AND fecha='$_SESSION[fecha]' AND tipo_mov='TRUE'",$dsn);
	$pre = ejecutar_script("SELECT sum(importe) FROM prestamos WHERE num_cia=$_SESSION[num_cia] AND fecha='$_SESSION[fecha]' AND tipo_mov='FALSE'",$dsn);
	
	// Encabezado
	$tpl->newBlock("totales");
	$tpl->assign("num_cia_total",$_SESSION['num_cia']);
	$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_SESSION['num_cia']),"","",$dsn);
	$tpl->assign("nombre_cia_total",$cia[0]['nombre_corto']);
	$tpl->assign("fecha_total",$_SESSION['fecha']);
	
	// Totales
	$efectivo = $_SESSION['hd']['total'] + $pago_pre[0]['sum'] - $_SESSION['g']['total'] - $pre[0]['sum'];
	$tpl->assign("venta",$_SESSION['hd']['total']  + $pago_pre[0]['sum']);
	$tpl->assign("gastos",$_SESSION['g']['total'] + $pre[0]['sum']);
	$tpl->assign("efectivo",$efectivo);

	$tpl->assign("ventaf",number_format($_SESSION['hd']['total'],2,".",","));
	$tpl->assign("pago_pre",number_format($pago_pre[0]['sum'],2,".",","));
	$tpl->assign("gastosf",number_format($_SESSION['g']['total'],2,".",","));
	$tpl->assign("pre",number_format($pre[0]['sum'],2,".",","));
	$tpl->assign("efectivof",number_format($efectivo,2,".",","));
	
	$tpl->printToScreen();
}

// --------------------------------- ALMACENAR -------------------------------------------------------------
if (isset($_GET['tabla']) && $_GET['tabla'] == "insertar") {
	// Ordenar datos de compra directa
	if (isset($_SESSION['cd'])) {
		$count = 0;
		for ($i=0; $i<10; $i++) {
			if ($_SESSION['cd']['codmp'.$i] > 0 && $_SESSION['cd']['total'.$i] > 0 && $_SESSION['cd']['numero_fact'.$i] > 0) {
				$cd['codmp'.$count] = $_SESSION['cd']['codmp'.$i];
				$cd['num_proveedor'.$count] = $_SESSION['cd']['num_proveedor'.$i];
				$cd['num_cia'.$count] = $_SESSION['num_cia'];
				$cd['numero_fact'.$count] = $_SESSION['cd']['numero_fact'.$i];
				$cd['fecha_mov'.$count] = $_SESSION['fecha'];
				$cd['cantidad'.$count] = $_SESSION['cd']['cantidad'.$i];
				$cd['kilos'.$count] = $_SESSION['cd']['kilos'.$i];
				$cd['precio_unit'.$count] = $_SESSION['cd']['precio_unit'.$i];
				$cd['aplica_gasto'.$count] = $_SESSION['cd']['aplica_gasto'.$i];
				$cd['total'.$count] = $_SESSION['cd']['total'.$i];
				$cd['fecha_pago'.$count] = $_SESSION['cd']['fecha_pago'];
				$cd['precio_unidad'.$count] = $cd['total'.$count] / $cd['cantidad'.$count];
				$count++;
			}
		}
		$cd_numfilas = $count;
		// Ordenar datos de movimiento gastos
		$count = 0;
		for ($i=0; $i<10; $i++) {
			if ($_SESSION['cd']['codmp'.$i] > 0 && $_SESSION['cd']['total'.$i] > 0 && $_SESSION['cd']['aplica_gasto'.$i] == "TRUE") {
				$gas['codgastos'.$count] = 23;
				$gas['num_cia'.$count] = $_SESSION['num_cia'];
				$gas['fecha'.$count] = $_SESSION['fecha'];
				$gas['importe'.$count] = $_SESSION['cd']['total'.$i];
				$gas['concepto'.$count] = "COMPRA F. NO. ".$_SESSION['cd']['numero_fact'.$i];
				$gas['captura'.$count]="false";
				$count++;
			}
		}
		$gas_numfilas = $count;
		
		// Insertar movimientos en compra_directa
		if ($cd_numfilas > 0) {
			$db_cd = new DBclass($dsn,"compra_directa",$cd);
			$db_cd->xinsertar();
		}
		// Insertar movimientos en movimiento_gastos
		if ($gas_numfilas > 0) {
			$db_gas = new DBclass($dsn,"movimiento_gastos",$cd);
			$db_gas->xinsertar();
		}
	}
	
	// Ordenar datos de hoja diaria
	if (isset($_SESSION['hd'])) {
		$count = 0;
		for ($i=0; $i<$_SESSION['hd']['numfilas']; $i++) {
			if ($_SESSION['hd']['precio_total'.$i] > 0) {
				$hd['num_cia'.$count] = $_SESSION['num_cia'];
				$hd['codmp'.$count] = $_SESSION['hd']['codmp'.$i];
				$hd['unidades'.$count] = $_SESSION['hd']['unidades'.$i];
				$hd['precio_unitario'.$count] = $_SESSION['hd']['precio_unitario'.$i];
				$hd['precio_total'.$count] = $_SESSION['hd']['precio_total'.$i];
				$hd['fecha'.$count] = $_SESSION['fecha'];
				$count++;
			}
		}
		$hd_numfilas = $count;
		// Insertar movimientos en hoja_diaria_rost
		if ($hd_numfilas > 0) {
			$db_hd = new DBclass($dsn,"hoja_diaria_rost",$hd);
			$db_hd->xinsertar();
		}
		
		if ($_SESSION['hd']['otros'] > 0) {
			$otros['num_cia'] = $_SESSION['num_cia'];
			$otros['importe'] = $_SESSION['hd']['otros'];
			
			$db_otros = new DBclass($dsn,"hoja_diaria_ros_otros",$otros);
			$db_otros->generar_script_insert("");
			$db_otros->ejecutar_script();
		}
	}
	
	// Ordenar datos de gastos
	if (isset($_SESSION['g'])) {
		$count = 0;
		for ($i=0; $i<10; $i++) {
			if ($_SESSION['g']['codgastos'.$i] > 0 && $_SESSION['g']['importe'.$i] > 0) {
				$g['codgastos'.$count] = $_SESSION['g']['codgastos'.$i];
				$g['num_cia'.$count] = $_SESSION['num_cia'];
				$g['fecha'.$count] = $_SESSION['fecha'];
				$g['importe'.$count] = $_SESSION['g']['importe'.$i];
				$g['concepto'.$count] = $_SESSION['g']['concepto'.$i];
				$g['captura'.$count] = "FALSE";
				$count++;
			}
		}
		$g_numfilas = $count;
		// Insertar movimientos en movimiento_gastos
		if ($g_numfilas > 0) {
			$db_g = new DBclass($dsn,"movimiento_gastos",$g);
			$db_g->xinsertar();
		}
	}

	// Insertar totales en total_companias
	$db_totales = new DBclass($dsn,"total_companias",$_POST);
	$db_totales->generar_script_insert("");
	$db_totales->ejecutar_script();
	
	// ------------------------- ACTUALIZAR INVENTARIOS ---------------------------
	
	// ---------------------------------- ENTRADA ---------------------------------
	if ($cd_numfilas > 0) {
		// Ordenar los datos de entrada en inventario real y virtual
		for ($i=0; $i<$cd_numfilas; $i++) {
			$inv_real = obtener_registro("inventario_real",array("num_cia","codmp"),array($_SESSION['num_cia'],$cd['codmp'.$i]),"","",$dsn);
			$inv_virtual = obtener_registro("inventario_virtual",array("num_cia","codmp"),array($_SESSION['num_cia'],$cd['codmp'.$i]),"","",$dsn);
	
			// Movimientos de inventario real y virtual
			$mov_inv_in['num_cia'.$i] = $_SESSION['num_cia'];
			$mov_inv_in['codmp'.$i] = $cd['codmp'.$i];
			$mov_inv_in['fecha'.$i] = $_SESSION['fecha'];
			$mov_inv_in['cod_turno'.$i] = 11; // Turno rosticeria
			$mov_inv_in['tipo_mov'.$i] = "FALSE"; // Entrada
			$mov_inv_in['cantidad'.$i] = $cd['cantidad'.$i];
			$mov_inv_in['existencia'.$i] = $inv_real[0]['existencia'];
			$mov_inv_in['precio'.$i] = $cd['precio_unit'.$i];
			$mov_inv_in['total_mov'.$i] = $cd['total'.$i];
			$mov_inv_in['descripcion'.$i] = "COMPRA F. NO. ".$cd['numero_fact'.$i];
			$mov_inv_in['precio_unidad'.$i] = $cd['total'.$i] / $cd['cantidad'.$i];
			
			// Inventario real
			$inv_real_in['num_cia'.$i] = $_SESSION['num_cia'];
			$inv_real_in['codmp'.$i] = $cd['codmp'.$i];
			$inv_real_in['fecha_entrada'.$i] = $_SESSION['fecha'];
			$inv_real_in['fecha_salida'.$i] = $inv_real[0]['fecha_salida'];
			$inv_real_in['existencia'.$i] = $inv_real[0]['existencia'] + $cd['cantidad'.$i];
			$inv_real_in['precio_unidad'.$i] = ($cd['total'.$i] + ($inv_real[0]['existencia'] * $inv_real[0]['precio_unidad'])) / ($cd['cantidad'.$i] + $inv_real[0]['existencia']);
			
			// Inventario virtual
			$inv_virtual_in['num_cia'.$i] = $_SESSION['num_cia'];
			$inv_virtual_in['codmp'.$i] = $cd['codmp'.$i];
			$inv_virtual_in['fecha_entrada'.$i] = $_SESSION['fecha'];
			$inv_virtual_in['fecha_salida'.$i] = $inv_virtual[0]['fecha_salida'];
			$inv_virtual_in['existencia'.$i] = $inv_virtual[0]['existencia'] + $cd['cantidad'.$i];
			$inv_virtual_in['precio_unidad'.$i] = ($cd['total'.$i] + ($inv_virtual[0]['existencia'] * $inv_virtual[0]['precio_unidad'])) / ($cd['cantidad'.$i] + $inv_virtual[0]['existencia']);
			
			// Actualizar inventario real (entrada)
			$db_inv_real_in = new DBclass($dsn,"inventario_real",$inv_real_in);
			if (existe_registro("inventario_real",array("num_cia","codmp"),array($_SESSION['num_cia'],$inv_real_in['codmp'.$i]),$dsn)) {
				$db_inv_real_in->generar_script_update($i,array("num_cia","codmp"),array($_SESSION['num_cia'],$inv_real_in['codmp'.$i]));
				$db_inv_real_in->ejecutar_script();
			}
			else {
				$db_inv_real_in->generar_script_insert($i);
				$db_inv_real_in->ejecutar_script();
			}
		
			// Actualizar inventario virtual (entrada)
			$db_inv_virtual_in = new DBclass($dsn,"inventario_virtual",$inv_virtual_in);
			if (existe_registro("inventario_virtual",array("num_cia","codmp"),array($_SESSION['num_cia'],$inv_real_in['codmp'.$i]),$dsn)) {
				$db_inv_virtual_in->generar_script_update($i,array("num_cia","codmp"),array($_SESSION['num_cia'],$inv_real_in['codmp'.$i]));
				$db_inv_virtual_in->ejecutar_script();
			}
			else {
				$db_inv_virtual_in->generar_script_insert($i);
				$db_inv_virtual_in->ejecutar_script();
			}
		}
		
		// Guardar movimientos de entrada en la tabla de movimientos de inventario real
		$db_mov_inv_real_in = new DBclass($dsn,"mov_inv_real",$mov_inv_in);
		$db_mov_inv_real_in->xinsertar();
		
		// Guardar movimientos de entrada en la tabla de movimientos de inventario virtual
		$db_mov_inv_virtual_in = new DBclass($dsn,"mov_inv_virtual",$mov_inv_in);
		$db_mov_inv_virtual_in->xinsertar();
	}
	
	// ---------------------------------- SALIDA ----------------------------------
	if ($hd_numfilas > 0) {
		// Ordenar los datos de salida de inventario real y virtual
		$count = 0;
		for ($i=0; $i<$hd_numfilas; $i++) {
			$inv_real = obtener_registro("inventario_real",array("num_cia","codmp"),array($_SESSION['num_cia'],$hd['codmp'.$i]),"","",$dsn);
			$inv_virtual = obtener_registro("inventario_virtual",array("num_cia","codmp"),array($_SESSION['num_cia'],$hd['codmp'.$i]),"","",$dsn);
			
			// Movimientos de inventario real y virtual
			$mov_inv_out['num_cia'.$i] = $_SESSION['num_cia'];
			$mov_inv_out['codmp'.$i] = $hd['codmp'.$i];
			$mov_inv_out['fecha'.$i] = $_SESSION['fecha'];
			$mov_inv_out['cod_turno'.$i] = 11; // Turno de rosticerias
			$mov_inv_out['tipo_mov'.$i] = "TRUE"; // Salida
			$mov_inv_out['cantidad'.$i] = $hd['unidades'.$i];
			$mov_inv_out['existencia'.$i] = $inv_real[0]['existencia'];
			$mov_inv_out['precio'.$i] = $hd['precio_unitario'.$i];
			$mov_inv_out['total_mov'.$i] = $hd['precio_total'.$i];
			$mov_inv_out['precio_unidad'.$i] = $hd['precio_total'.$i] / $hd['unidades'.$i];
			$mov_inv_out['descripcion'.$i] = "CONSUMO DEL DIA";
			
			// Inventario real
			$inv_real_out['num_cia'.$i] = $_SESSION['num_cia'];
			$inv_real_out['codmp'.$i] = $hd['codmp'.$i];
			$inv_real_out['fecha_entrada'.$i] = $_SESSION['fecha'];
			$inv_real_out['fecha_salida'.$i] = $inv_real[0]['fecha_salida'];
			$inv_real_out['existencia'.$i] = $inv_real[0]['existencia'] - $hd['unidades'.$i];
			$inv_real_out['precio_unidad'.$i] = $inv_real[0]['precio_unidad'];
			
			// Inventario real
			$inv_virtual_out['num_cia'.$i] = $_SESSION['num_cia'];
			$inv_virtual_out['codmp'.$i] = $hd['codmp'.$i];
			$inv_virtual_out['fecha_entrada'.$i] = $_SESSION['fecha'];
			$inv_virtual_out['fecha_salida'.$i] = $inv_virtual[0]['fecha_salida'];
			$inv_virtual_out['existencia'.$i] = $inv_virtual[0]['existencia'] - $hd['unidades'.$i];
			$inv_virtual_out['precio_unidad'.$i] = $inv_virtual[0]['precio_unidad'];
			
			// Actualizar inventario real (salida)
			$db_inv_real_out = new DBclass($dsn,"inventario_real",$inv_real_out);
			if (existe_registro("inventario_real",array("num_cia","codmp"),array($_SESSION['num_cia'],$inv_real_out['codmp'.$i]),$dsn)) {
				$db_inv_real_out->generar_script_update($i,array("num_cia","codmp"),array($_SESSION['num_cia'],$inv_real_out['codmp'.$i]));
				$db_inv_real_out->ejecutar_script();
			}
			else {
				$db_inv_real_out->generar_script_insert($i);
				$db_inv_real_out->ejecutar_script();
			}
			
			// Actualizar inventario virtual (salida)
			$db_inv_virtual_out = new DBclass($dsn,"inventario_virtual",$inv_virtual_out);
			if (existe_registro("inventario_virtual",array("num_cia","codmp"),array($_SESSION['num_cia'],$inv_virtual_out['codmp'.$i]),$dsn)) {
				$db_inv_virtual_out->generar_script_update($i,array("num_cia","codmp"),array($_SESSION['num_cia'],$inv_virtual_out['codmp'.$i]));
				$db_inv_virtual_out->ejecutar_script();
			}
			else {
				$db_inv_virtual_out->generar_script_insert($i);
				$db_inv_virtual_out->ejecutar_script();
			}
		}
		
		// Guardar movimientos de salida en la tabla de movimientos de inventario real
		$db_mov_inv_real_out = new DBclass($dsn,"mov_inv_real",$mov_inv_out);
		$db_mov_inv_real_out->xinsertar();
		
		// Guardar movimientos de salida en la tabla de movimientos de inventario virtual
		$db_mov_inv_virtual_out = new DBclass($dsn,"mov_inv_virtual",$mov_inv_out);
		$db_mov_inv_virtual_out->xinsertar();
	}
	
	header("location: ./ros_pro_sec.php");
}
?>