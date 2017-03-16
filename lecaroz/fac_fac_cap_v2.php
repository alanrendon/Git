<?php
// ALTA DE DESCUENTOS MATERIA PRIMAS
// Tabla 'catalogo_productos_proveedor'
// Menu Proveedores y facturas -> 

//define ('IDSCREEN',); //ID de pantalla sin ID


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay productos para el proveedor indicado";
$descripcion_error[2] = "El número de factura ya existe en la Base de Datos.";
$descripcion_error[3] = "La factura es del mes pasado y no puede ser ingresada al sistema";

$db = new DBclass($dsn);

// Insertar datos
if (isset($_POST['num_cia'])) {
	// Almacenar temporalmente proveedor
	$_SESSION['fac_cap']['num_pro'] = $_POST['num_proveedor'];
	
	// Número de elementos
	$numfilas = count($_POST['cantidad']);
	
	// Ordenar datos
	$num_cia = $_POST['num_cia'];
	$num_proveedor = $_POST['num_proveedor'];
	$fecha = $_POST['fecha'];
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $temp);
	$fecha_his = date("d/m/Y", mktime(0, 0, 0, $temp[2], 0, $temp[3]));
	$num_doc = $_POST['num_documento'];
	
	$importe_sin_iva = 0;
	$importe_iva = 0;
	$importe_total = 0;
	$porciento_iva = 0;
	
	// Obtener los dias de credito
	$sql = "SELECT diascredito FROM catalogo_proveedores WHERE num_proveedor = $num_proveedor";
	$dias_credito = $db->query($sql);
	$fecha_pago = date("d/m/Y", mktime(0, 0, 0, $temp[2], $temp[1] + $dias_credito[0]['diascredito'], $temp[3]));
	
	$sql = "";
	for ($i = 0; $i < $numfilas; $i++) {
		if ($_POST['cantidad'][$i] > 0) {
			$codmp = $_POST['codmp'][$i];	// Código de producto
			$cantidad = $_POST['cantidad'][$i] * $_POST['contenido'][$i];	// Cantidad total de producto representada en unidades de consumo
			$precio = $_POST['precio'][$i];	// Precio de venta del producto
			// Costo real del producto, no importando si es regalado
			$importe_real = $_POST['cantidad'][$i] * $precio;
			$importe_real = $importe_real * (1 - $_POST['desc1'][$i] / 100);
			$importe_real = $importe_real * (1 - $_POST['desc2'][$i] / 100);
			$importe_real = $importe_real * (1 - $_POST['desc3'][$i] / 100);
			$importe_real = $importe_real * (1 + $_POST['iva'][$i] / 100);
			$importe_real = $importe_real * (1 + $_POST['ieps'][$i] / 100);
			// *****************************************************
			$precio_unidad = $importe_real / $cantidad;	// Precio por unidad de consumo
			$total_mov = $_POST['costo_unitario'][$i];	// Costo total del producto (0 si es regalado)
			
			// Si el producto no ingresa en el momento que se genero la factura, meterlo en una tabla temporal
			if (isset($_POST['no_inv']) && array_search($i, $_POST['no_inv']) !== FALSE)
				$sql .= "INSERT INTO entrada_mp_temp (num_cia,codmp,fecha,cantidad,precio_unidad,descripcion,num_proveedor) VALUES ($num_cia,$codmp,'$fecha',$cantidad,$precio_unidad,'COMPRA F. NO. $num_doc',$num_proveedor);\n";
			// Si el producto ingresa en el momento que se genero la factura, actualizar inventario y meter movimiento de entrada a inventario
			else {
				$sql .= "INSERT INTO mov_inv_real (num_cia,codmp,fecha,tipo_mov,cantidad,precio,total_mov,precio_unidad,descripcion,num_proveedor) VALUES ($num_cia,$codmp,'$fecha','FALSE',$cantidad,$precio,$importe_real,$precio_unidad,'COMPRA F. NO. $num_doc',$num_proveedor);\n";
				if ($id = $db->query("SELECT idinv, existencia, precio_unidad FROM inventario_real WHERE num_cia = $num_cia AND codmp = $codmp")) {
					// Calcular costo promedio
					if ($id[0]['existencia'] > 0)
						$costo_promedio = ($total_mov + $id[0]['existencia'] * $id[0]['precio_unidad']) / ($cantidad + $id[0]['existencia']);
					else
						$costo_promedio = $precio_unidad;
					
					// Actualizar inventario
					$sql .= "UPDATE inventario_real SET existencia = existencia + $cantidad, precio_unidad = $costo_promedio WHERE idinv = {$id[0]['idinv']};\n";
				}
				else {
					// Ingresar producto en el inventario
					$sql .= "INSERT INTO inventario_real (num_cia,codmp,existencia,precio_unidad) VALUES ($num_cia,$codmp,$cantidad,$precio_unidad);\n";
					// Ingresar datos de histórico
					$sql .= "INSERT INTO historico_inventario (num_cia,codmp,fecha,existencia,precio_unidad) VALUES ($num_cia,$codmp,'$fecha_his',0,0);\n";
				}
			}
			
			// Entrada de materia prima
			$emp['num_documento'] = $num_doc;
			$emp['num_cia'] = $num_cia;
			$emp['codmp'] = $codmp;
			$emp['fecha'] = $fecha;
			$emp['contenido'] = $_POST['contenido'][$i];
			$emp['costo_total'] = $_POST['costo_total'];
			$emp['porciento_desc_normal'] = $_POST['desc1'][$i];
			$emp['porciento_desc_adicional2'] = $_POST['desc2'][$i];
			$emp['porciento_desc_adicional3'] = $_POST['desc3'][$i];
			$emp['porciento_impuesto'] = $_POST['iva'][$i];
			$emp['ieps'] = (float)$_POST['ieps'][$i];
			$emp['num_proveedor'] = $num_proveedor;
			$emp['pagado'] = "FALSE";
			$emp['fecha_pago'] = $fecha_pago;
			$emp['codgasto'] = 33;
			$emp['costo_unitario'] = $_POST['cantidad'][$i] * $precio;
			$emp['precio'] = $precio;
			$emp['fecha_captura'] = date("d/m/Y");
			$emp['iduser'] = $_SESSION['iduser'];
			$emp['cantidad'] = $_POST['cantidad'][$i];
			$emp['regalado'] = isset($_POST['regalado']) && array_search($i, $_POST['regalado']) !== FALSE ? "TRUE" : "FALSE";
			
			// Arrastre de importes de factura
			$importe_sin_iva += $total_mov / (1 + $_POST['iva'][$i] / 100);
			$importe_iva += $total_mov * $_POST['iva'][$i] / 100;
			$importe_total += $total_mov;
			$porciento_iva = $_POST['iva'][$i];
			
			$sql .= $db->preparar_insert("entrada_mp", $emp) . ";\n";
		}
	}
	
	// Datos para factura
	$fac['num_proveedor'] = $num_proveedor;
	$fac['num_cia'] = $num_cia;
	$fac['num_fact'] = $num_doc;
	$fac['fecha_mov'] = $fecha;
	$fac['fecha_ven'] = $fecha_pago;
	$fac['imp_sin_iva'] = $importe_sin_iva;
	$fac['porciento_iva'] = $porciento_iva;
	$fac['importe_iva'] = $importe_iva;
	$fac['codgastos'] = 33;
	$fac['importe_total'] = $importe_total;
	$fac['tipo_factura'] = "0";
	$fac['fecha_captura'] = date("d/m/Y");
	$fac['iduser'] = $_SESSION['iduser'];
	$fac['concepto'] = "FACTURA MATERIA PRIMA";
	
	$sql .= $db->preparar_insert("facturas", $fac) . ";\n";
	
	// Datos para pasivo
	$pas['num_cia'] = $num_cia;
	$pas['num_fact'] = $num_doc;
	$pas['total'] = $_POST['costo_total'];
	$pas['descripcion'] = "FACTURA MATERIA PRIMA";
	$pas['fecha_mov'] = $fecha;
	$pas['fecha_pago'] = $fecha_pago;
	$pas['num_proveedor'] = $num_proveedor;
	$pas['codgastos'] = 33;
	$pas['copia_fac'] = "FALSE";
	
	if (isset($_POST['aclaracion'])) {
		$acla['num_proveedor'] = $num_proveedor;
		$acla['num_fact'] = $num_doc;
		$acla['fecha_solicitud'] = date('d/m/Y');
		$acla['obs'] = trim(strtoupper($_POST['obs']));
		
		$sql .= $db->preparar_insert('facturas_pendientes', $acla) . ";\n";
	}
	
	$sql .= $db->preparar_insert("pasivo_proveedores", $pas) . ";\n";
	
	$db->empezar_transaccion();
	$db->query($sql);
	$db->terminar_transaccion();
	
	$db->desconectar();
	header("location: ./fac_fac_cap_v2.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_fac_cap_v2.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("d/m/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
	if (isset($_SESSION['fac_cap']['num_pro'])) {
		$nombre = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = {$_SESSION['fac_cap']['num_pro']}");
		$tpl->assign('num_pro', $_SESSION['fac_cap']['num_pro']);
		$tpl->assign('nombre_pro', $nombre[0]['nombre']);
	}
	
	
	$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia";
	$cia = $db->query($sql);
	$sql = "SELECT num_proveedor, nombre FROM catalogo_proveedores ORDER BY num_proveedor";
	$pro = $db->query($sql);
	
	for ($i = 0; $i < count($cia); $i++) {
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia", $cia[$i]['num_cia']);
		$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
	}
	
	for ($i = 0; $i < count($pro); $i++) {
		$tpl->newBlock("nombre_pro");
		$tpl->assign("num_pro", $pro[$i]['num_proveedor']);
		$tpl->assign("nombre_pro", $pro[$i]['nombre']);
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
	$db->desconectar();
	die;
}

// Validar fecha de la factura
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
$fecha_lim = mktime(0, 0, 0, date("m"), 7, date("Y"));
if ($fecha[2] < date('n') && time() >= $fecha_lim) {
	$db->desconectar();
	header("location: ./fac_fac_cap_v2.php?codigo_error=3");
	die;
}

// Validar que la factura no se encuentre en la base de datos
if ($db->query("SELECT num_documento FROM entrada_mp WHERE num_proveedor = $_GET[num_proveedor] AND num_documento = $_GET[num_documento]")) {
	$db->desconectar();
	header("location: ./fac_fac_cap_v2.php?codigo_error=2");
	die;
}

// Obtener productos por proveedor
$sql = "SELECT codmp,nombre,contenido,descripcion AS unidad,precio,desc1,desc2,desc3,iva,ieps FROM catalogo_productos_proveedor JOIN catalogo_mat_primas USING(codmp) JOIN tipo_unidad_consumo ON(idunidad = unidadconsumo) WHERE num_proveedor = $_GET[num_proveedor] ORDER BY codmp ASC";
$pro = $db->query($sql);

if (!$pro) {
	$db->desconectar();
	header("location: ./fac_fac_cap_v2.php?codigo_error=1");
	die;
}

// Almacenar temporalmente proveedor
$_SESSION['fac_cap']['num_pro'] = $_GET['num_proveedor'];

$tpl->newBlock("captura");
$tpl->assign("num_cia", $_GET['num_cia']);
$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
$tpl->assign("num_proveedor", $_GET['num_proveedor']);
$nombre_pro = $db->query("SELECT nombre, observaciones FROM catalogo_proveedores WHERE num_proveedor = $_GET[num_proveedor]");
$tpl->assign("observaciones", trim($nombre_pro[0]['observaciones']));
$tpl->assign("nombre_proveedor", $nombre_pro[0]['nombre']);
$tpl->assign("num_documento", $_GET['num_documento']);
$tpl->assign("fecha", $_GET['fecha']);
$tpl->assign("total_fac", number_format($_GET['total_fac'],2,".",""));

$numfilas = count($pro);
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $numfilas > 1 ? "[" . $i . "]" : "");
	$tpl->assign("index", $i);
	$tpl->assign("next", $numfilas > 1 ? ($i < $numfilas - 1 ? "[" . ($i + 1) . "]" : "[0]") : "");
	
	$tpl->assign("codmp", $pro[$i]['codmp']);
	$tpl->assign("descripcion", $pro[$i]['nombre']);
	$tpl->assign("contenido", $pro[$i]['contenido']);
	$tpl->assign("unidad", $pro[$i]['unidad']);
	$tpl->assign("precio", number_format($pro[$i]['precio'], 4, ".", ""));
	$tpl->assign("desc1", number_format($pro[$i]['desc1'], 2, ".", ","));
	$tpl->assign("desc2", number_format($pro[$i]['desc2'], 2, ".", ","));
	$tpl->assign("desc3", number_format($pro[$i]['desc3'], 2, ".", ","));
	$tpl->assign("iva", number_format($pro[$i]['iva'], 2, ".", ","));
	$tpl->assign("ieps", $pro[$i]['ieps'] != 0 ? number_format($pro[$i]['ieps'], 2, ".", ",") : "0.00");
}

$tpl->printToScreen();
$db->desconectar();
?>