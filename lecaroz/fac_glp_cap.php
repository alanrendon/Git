<?php
// CAPTURA DE FACTURAS DE GAS
// Tabla 'factura_gas'
// Menu

//define ('IDSCREEN',1212); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";
$descripcion_error[2] = "La compañía no tiene tanques de gas";
$descripcion_error[3] = "El número de factura ya existe en la Base de Datos";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
//if ($_SESSION['iduser'] != 1) die('Modificando');
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Capturar datos ----------------------------------------------------------
$numcia=0;
if (isset($_GET['tabla'])) {
	$count = 0;
	$ids = array();
	// Ordenar datos para factura de gas
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['num_fact'.$i] != '' && $_POST['litros'.$i] > 0 && $_POST['total'.$i] > 0) {
			$fact['num_cia'.$count] = $_POST['num_cia'];
			$fact['num_proveedor'.$count] = $_POST['num_proveedor'];
			$fact['fecha'.$count] = $_POST['fecha'];
			$fact['num_fact'.$count] = $_POST['num_fact'.$i];
			$fact['precio_unit'.$count] = $_POST['precio_unit'.$i];
			$fact['porc_inic'.$count] = $_POST['porc_inic'.$i];
			$fact['porc_final'.$count] = $_POST['porc_final'.$i];
			$fact['total'.$count] = $_POST['total'.$i];
			$fact['litros'.$count] = $_POST['litros'.$i];
			$fact['num_tanque'.$count] = $_POST['num_tanque'.$i];
			$fact['capacidad'.$count] = $_POST['capacidad'.$i];
			$fact['pago_proveedor'.$count] = "true";
			$fact['fecha_captura'.$count] = date("d/m/Y");
			$fact['iduser'.$count] = $_SESSION['iduser'];
			
			if (ejecutar_script("SELECT id FROM factura_gas WHERE num_proveedor = {$fact['num_proveedor'.$count]} AND num_fact = '{$fact['num_fact'.$count]}'", $dsn)) {
			//if (existe_registro("factura_gas",array("num_cia","num_proveedor","num_fact"),array($fact['num_cia'.$count],$fact['num_proveedor'.$count],$fact['num_fact'.$count]),$dsn)) {
				header("location: ./fac_glp_cap.php?codigo_error=3");
				die;
			}
			
			$count++;
		}
	}
	//print_r($fact);
	$proveedor = ejecutar_script("SELECT diascredito FROM catalogo_proveedores WHERE num_proveedor = $_POST[num_proveedor]",$dsn);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})",$_POST['fecha'],$fecha);
	$fecha1 = $fecha[0];
	$fecha2 = date("d/m/Y",mktime(0,0,0,$fecha[2],$fecha[1]+$proveedor[0]['diascredito'],$fecha[3]));
	
	// Reconstruir datos para las demas tablas
	for ($i=0; $i<$count; $i++) {
		$temp = ejecutar_script("SELECT * FROM inventario_real WHERE num_cia=".$fact['num_cia'.$i]." AND codmp=90",$dsn);
		// mov_inv_real y mov_inv_virtual
		$mov['num_cia'.$i]       = $fact['num_cia'.$i];
		$mov['codmp'.$i]         = 90;
		$mov['fecha'.$i]         = $fecha1;
		$mov['cod_turno'.$i]     = "";
		$mov['tipo_mov'.$i]      = "FALSE";
		$mov['cantidad'.$i]      = $fact['litros'.$i];
		$mov['precio'.$i]        = $fact['precio_unit'.$i];
		$mov['total_mov'.$i]     = $fact['total'.$i];
		$mov['precio_unidad'.$i] = $mov['total_mov'.$i] / $mov['cantidad'.$i];
		$mov['descripcion'.$i]   = "COMPRA F. NO. ".$fact['num_fact'.$i];
		$mov['num_proveedor'.$i] = $fact['num_proveedor'.$i];
		
		// actualizar inventarios
		$inv['num_cia'.$i]       = $fact['num_cia'.$i];
		$inv['codmp'.$i]         = 90;
		$inv['fecha_entrada'.$i] = $fecha1;
		$inv['fecha_salida'.$i]  = $fecha1;
		$inv['existencia'.$i]    = $temp[0]['existencia'] + $fact['litros'.$i];
		$inv['precio_unidad'.$i] = ($fact['total'.$i] + ($temp[0]['existencia'] * $temp[0]['precio_unidad'])) / ($fact['litros'.$i] + $temp[0]['existencia']);
		
		// movimiento_gastos
		$gas['codgastos'.$i] = 33;
		$gas['num_cia'.$i]   = $fact['num_cia'.$i];
		$gas['fecha'.$i]     = $fecha1;
		$gas['importe'.$i]   = $fact['total'.$i];
		$gas['concepto'.$i]  = "COMPRA GAS F. NO. ".$fact['num_fact'.$i];
		$gas['captura'.$i]	  = "true";
		
		// pasivo_proveedores
		$pas['num_cia'.$i]       = $fact['num_cia'.$i];
		$pas['codmp'.$i]         = 90;
		$pas['num_fact'.$i]      = $fact['num_fact'.$i];
		$pas['total'.$i]         = $fact['total'.$i];
		$pas['descripcion'.$i]   = "COMPRA FACTURA GAS";
		$pas['fecha'.$i]     = $fecha1;
		$pas['num_proveedor'.$i] = $fact['num_proveedor'.$i];
		$pas['codgastos'.$i]     = 33;
		$pas['copia_fac'.$i]     = ($val = ejecutar_script("SELECT id FROM facturas_validacion WHERE num_cia = {$fact['num_cia'.$i]} AND num_pro = {$fact['num_proveedor'.$i]} AND num_fact = '{$fact['num_fact'.$i]}' AND tsbaja IS NULL", $dsn)) ? 'TRUE' : 'FALSE';

		if ($val)
		{
			$ids[] = $val[0]['id'];
		}
		
		// Facturas
		$pro_prov = ejecutar_script("SELECT iva FROM catalogo_productos_proveedor WHERE num_proveedor=".$fact['num_proveedor'.$i],$dsn);
		$fac['num_proveedor'.$i]     = $fact['num_proveedor'.$i];
		$fac['num_cia'.$i]           = $fact['num_cia'.$i];
		$fac['num_fact'.$i]          = $fact['num_fact'.$i];
		$fac['fecha'.$i]            = $fecha1;
		$fac['concepto'.$i]          = "FACTURA GAS";
		$importe = $fact['litros'.$i] * $fact['precio_unit'.$i];
		$fac['importe'.$i]       = $importe;
		$fac['piva'.$i]     = $pro_prov[0]['iva'];
		$fac['iva'.$i]       = $importe * ($pro_prov[0]['iva'] / 100);
		$fac['pretencion_isr'.$i] = "0";
		$fac['pretencion_iva'.$i] = "0";
		$fac['retencion_isr'.$i] = "0";
		$fac['retencion_iva'.$i] = "0";
		$fac['codgastos'.$i]         = 33;
		$fac['total'.$i]     = $fac['importe'.$i] + $fac['iva'.$i];
		$fac['tipo_factura'.$i]      = "0";
		$fac['fecha_captura'.$i]     = date("d/m/Y");
		$fac['iduser'.$i] = $_SESSION['iduser'];
		
		// Actualizar inventario real (entrada)
		$db_inv = new DBclass($dsn,"inventario_real",$inv);
		if (existe_registro("inventario_real",array("num_cia","codmp"),array($inv['num_cia'.$i],$inv['codmp'.$i]),$dsn)) {
			$db_inv->generar_script_update($i,array("num_cia","codmp"),array($inv['num_cia'.$i],$inv['codmp'.$i]));
			$db_inv->ejecutar_script();
		}
		else {
			$db_inv->generar_script_insert($i);
			$db_inv->ejecutar_script();
		}
		
		// Actualizar inventario virtual (entrada)
		$db_inv = new DBclass($dsn,"inventario_virtual",$inv);
		if (existe_registro("inventario_virtual",array("num_cia","codmp"),array($inv['num_cia'.$i],$inv['codmp'.$i]),$dsn)) {
			$db_inv->generar_script_update($i,array("num_cia","codmp"),array($inv['num_cia'.$i],$inv['codmp'.$i]));
			$db_inv->ejecutar_script();
		}
		else {
			$db_inv->generar_script_insert($i);
			$db_inv->ejecutar_script();
		}
	}
	
	$db_fact = new DBclass($dsn,$_GET['tabla'],$fact);
	$db_fact->xinsertar();
	
	// Insertar datos en facturas
	$db_fac = new DBclass($dsn,"facturas",$fac);
	$db_fac->xinsertar();
	
	// Insertar datos en mov_inv_real y mov_inv_virtual
	$db_mov_real = new DBclass($dsn,"mov_inv_real",$mov);
	$db_mov_real->xinsertar();
	
	//$db_mov_virtual = new DBclass($dsn,"mov_inv_virtual",$mov);
	//$db_mov_virtual->xinsertar();
	
	// Insertar datos en pasivo_proveedores	
	$db_pas = new DBclass($dsn,"pasivo_proveedores",$pas);
	$db_pas->xinsertar();

	if ($ids)
	{
		ejecutar_script("
			UPDATE
				facturas_validacion
			SET
				tsvalid = NOW(),
				idvalid = {$_SESSION['iduser']}
			WHERE
				id IN (" . implode(', ', $ids) . ");
		", $dsn);
	}

	// Insertar datos en movimiento_gastos
//	$db_gas = new DBclass($dsn,"movimiento_gastos",$gas);
//	$db_gas->xinsertar();
	
	$_SESSION['glp']['num_pro'] = $_POST['num_proveedor'];
	
	header("location: ./fac_glp_cap.php");
	die;
}

// --------------------------------- Buscar datos ------------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_glp_cap.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("fecha",date("d/m/Y",mktime(0,0,0,date("m"),date("d")-1,date("Y"))));
	
	if (isset($_SESSION['glp']['num_pro'])) {
		$tpl->assign('num_pro', $_SESSION['glp']['num_pro']);
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
	die();
}

$tpl->newBlock("captura");

// Seleccionar tabla
$tpl->assign("tabla","factura_gas");

$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
$pro = ejecutar_script("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[num_proveedor]",$dsn);
$result = ejecutar_script("SELECT precio, iva FROM catalogo_productos_proveedor WHERE num_proveedor = $_GET[num_proveedor] AND codmp = 90",$dsn);
$precio = number_format($result[0]['precio'],8,'.','');

if ($result[0]['iva'] > 0)
	$iva = $result[0]['iva'];
else
	$iva = 0;

$tanques = ejecutar_script("SELECT * FROM catalogo_tanques WHERE num_cia = $_GET[num_cia] ORDER BY num_tanque ASC",$dsn);

$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("num_proveedor",$_GET['num_proveedor']);
$tpl->assign("nombre_proveedor",$pro[0]['nombre']);
$tpl->assign("fecha",$_GET['fecha']);
$tpl->assign("numfilas",count($tanques));
$tpl->assign("iva",$iva);

for ($i=0; $i<count($tanques); $i++) {
	$tpl->newBlock("tanque");
	$tpl->assign("i",$i);
	if ($i < count($tanques) - 1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",count($tanques)-1);
	$tpl->assign("num_tanque",$tanques[$i]['num_tanque']);
	$tpl->assign("capacidad",$tanques[$i]['capacidad']);
	$tpl->assign("precio_unit",$precio);
	$tpl->assign("fprecio_unit",number_format($precio,8,".",","));
	$tpl->assign("iva",$iva);
}

$tpl->printToScreen();
?>