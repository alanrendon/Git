<?php
// CONEXION
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
// Validar usuario
$session = new sessionclass($dsn);
// Obtener tabla de trabajo
$tabla = $_GET['tabla'];
// -------------------------------------------------------------------------------------
// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$_POST);
$costo_unitario=0;
$cantidad=0;
$nomproveedor = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']),"","",$dsn);
$diascredito=$nomproveedor[0]['diascredito'];
$_dt=explode("/",$_POST['fecha']);
$d2 = $_dt[0];
$m2 = $_dt[1];
$y2 = $_dt[2];
$d2 =$d2+$diascredito;
$fecha2=date( "d/m/Y", mktime(0,0,0,$m2,$d2,$y2));

$rows = obtener_registro("catalogo_productos_proveedor",array("num_proveedor"),array($_POST['num_proveedor']),"","", $dsn);
$numrows = count($rows);

// Reconstruir datos para la tabla de entrada_mp
$count = 0;
for ($i=0; $i<$numrows; $i++) {
	if($_POST['cantidad'.$i] != ""){
		$datos['num_cia'.$count] = $_POST['num_cia'];
		$datos['num_proveedor'.$count] = $_POST['num_proveedor'];
		$datos['num_documento'.$count] = $_POST['num_documento'];
		$datos['fecha'.$count] = $_POST['fecha'];
		$datos['cantidad'.$count] = $_POST['cantidad'.$i];
		$datos['codmp'.$count] = $_POST['codmp'.$i];
		$datos['contenido'.$count] = $_POST['contenido'.$i];
		$datos['porciento_desc_normal'.$count] = $_POST['desc1'.$i];
		$datos['porciento_desc_adicional2'.$count] = $_POST['desc2'.$i];
		$datos['porciento_desc_adicional3'.$count] = $_POST['desc3'.$i];
		$datos['ieps'.$count] = $_POST['ieps'.$i];
		$datos['costo_unitario'.$count] = $_POST['costo_unitario'.$i];
		$datos['costo_total'.$count] = $_POST['costo_total'];
		$datos['porciento_impuesto'.$count] = $_POST['iva'.$i];
		$datos['pagado'.$count] = 'FALSE';
		$datos['fecha_pago'.$count] = $fecha2;
		$datos['num_cheque'.$count] = "";
		$datos['codgasto'.$count] = 33;
		$datos['precio'.$count] = $_POST['precio'.$i];
		$datos['fecha_captura'.$count] = date("d/m/Y");
		$datos['iduser'.$count] = $_SESSION['iduser'];
		if($_POST['bandera'.$i]==0)	$datos['regalado'.$count]="false";
		else $datos['regalado'.$count]="true";
		$auxiliar['bandera'.$count] = $_POST['bandera'.$i];
		$count++;
	}
}
// Reconstruir datos para las demas tablas
for ($i=0; $i<$count; $i++) {
	$temp = ejecutar_script("SELECT * FROM inventario_real WHERE num_cia=".$datos['num_cia'.$i]." AND codmp=".$datos['codmp'.$i],$dsn);
	// mov_inv_real y mov_inv_virtual
	$mov['num_cia'.$i]       = $_POST['num_cia'];
	$mov['codmp'.$i]         = $datos['codmp'.$i];
	$mov['fecha'.$i]         = $_POST['fecha'];
	$mov['cod_turno'.$i]     = "";
	$mov['tipo_mov'.$i]      = "FALSE";
	$mov['cantidad'.$i]      = $datos['cantidad'.$i] * $datos['contenido'.$i];
	$mov['existencia'.$i]    = $temp[0]['existencia'];
	$mov['precio'.$i]        = $datos['precio'.$i];
	$mov['total_mov'.$i]     = $datos['costo_unitario'.$i];
	$mov['precio_unidad'.$i] = $mov['total_mov'.$i] / $mov['cantidad'.$i];
	$mov['descripcion'.$i]   = "COMPRA F. NO. ".$datos['num_documento'.$i];
	
	// actualizar inventarios
	$inv['num_cia'.$i]       = $_POST['num_cia'];
	$inv['codmp'.$i]         = $datos['codmp'.$i];
	$inv['fecha_entrada'.$i] = $_POST['fecha'];
	$inv['fecha_salida'.$i]  = $_POST['fecha'];
	$inv['existencia'.$i]    = $temp[0]['existencia'] + $datos['cantidad'.$i] * $datos['contenido'.$i];
	if ($temp && $temp[0]['existencia'] >= 0) {
		@$inv['precio_unidad'.$i] = ($datos['costo_unitario'.$i] + ($temp[0]['existencia'] * $temp[0]['precio_unidad'])) / (($datos['cantidad'.$i] * $datos['contenido'.$i]) + $temp[0]['existencia']);
		@$costo_unitario=($datos['costo_unitario'.$i] + ($temp[0]['existencia'] * $temp[0]['precio_unidad'])) / (($datos['cantidad'.$i] * $datos['contenido'.$i]) + $temp[0]['existencia']);
	}
	else {
		@$inv['precio_unidad'.$i] = $datos['costo_unitario'.$i] / ($datos['cantidad'.$i] * $datos['contenido'.$i]);
		@$costo_unitario=$datos['costo_unitario'.$i] / ($datos['cantidad'.$i] * $datos['contenido'.$i]);
	}
	$cantidad=$datos['cantidad'.$i] * $datos['contenido'.$i];
	// movimiento_gastos
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_POST['fecha'],$fecha);
	$fecha_historico = date("d/m/Y",mktime(0,0,0,$fecha[2],0,$fecha[3]));
	
	// Actualizar inventario real (entrada)
	if ($temp) {
		$sql="UPDATE inventario_real SET existencia=existencia + ".$cantidad.",precio_unidad=".($costo_unitario > 0 ? round($costo_unitario,4) : "precio_unidad")." WHERE idinv=".$temp[0]['idinv'];
		ejecutar_script($sql,$dsn);
	}
	else{
		$sql="INSERT INTO inventario_real(num_cia,codmp,fecha_entrada,fecha_salida,existencia,precio_unidad) VALUES(".$_POST['num_cia'].",".$datos['codmp'.$i].",'".date("d/m/Y")."','".date("d/m/Y")."',".$cantidad.",".($costo_unitario > 0 ? round($costo_unitario,4) : 0).")";
		ejecutar_script($sql,$dsn);
		$sql="INSERT INTO historico_inventario (num_cia,codmp,fecha,existencia,precio_unidad) VALUES($_POST[num_cia],".$datos['codmp'.$i].",'$fecha_historico',0,".($costo_unitario > 0 ? round($costo_unitario,4) : 0).")";
		ejecutar_script($sql,$dsn);
	}
}

// Facturas
$fac['num_proveedor']     = $_POST['num_proveedor'];
$fac['num_cia']           = $_POST['num_cia'];
$fac['num_fact']          = $_POST['num_documento'];
$fac['fecha_mov']         = $_POST['fecha'];
$fac['fecha_ven']         = $fecha2;
$fac['concepto']          = "FACTURA MATERIA PRIMA";
$importe = 0;
$iva = 0;
for ($i=0; $i<$count; $i++) {
	if($auxiliar['bandera'.$i]==0){ //SE AGREGO ESTA LINEA PARA ALMACENAR EL EL TOTAL DE LOS PRODUCTOS NO REGALADOS
		$importe += $datos['costo_unitario'.$i];
		$iva += $datos['porciento_impuesto'.$i];
	}
}

$fac['imp_sin_iva']       = $importe - $iva;
$fac['porciento_iva']     = $datos['porciento_impuesto0'];
$fac['importe_iva']       = $iva;
$fac['porciento_ret_isr'] = "0";
$fac['porciento_ret_iva'] = "0";
$fac['codgastos']         = 33;
$fac['importe_total']     = $importe;
$fac['tipo_factura']      = "0";
$fac['fecha_captura']     = date("d/m/Y");
$fac['iduser'] = $_SESSION['iduser'];

// PASIVO PROVEEDORES

$pas['num_cia']       = $_POST['num_cia'] == 146 ? 147 : ($_POST['num_cia'] == 171 ? 170 : $_POST['num_cia']);
$pas['num_fact']      = $_POST['num_documento'];
$pas['total']         = $_POST['costo_total'];
$pas['descripcion']   = "COMPRA FACTURA MATERIA PRIMA";
$pas['fecha_mov']     = $_POST['fecha'];
$pas['fecha_pago']    = $fecha2;
$pas['num_proveedor'] = $_POST['num_proveedor'];
$pas['codgastos']     = 33;

// Insertar datos en facturas
$db_fac = new DBclass($dsn,"facturas",$fac);
$db_fac->generar_script_insert("");
$db_fac->ejecutar_script();

// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,$tabla,$datos);
$db->xinsertar();

// Insertar datos en mov_inv_real y mov_inv_virtual
$db_mov_real = new DBclass($dsn,"mov_inv_real",$mov);
$db_mov_real->xinsertar();

/*$db_mov_virtual = new DBclass($dsn,"mov_inv_virtual",$mov);
$db_mov_virtual->xinsertar();*/

// Insertar datos en movimiento_gastos
//$db_gas = new DBclass($dsn,"movimiento_gastos",$gas);
//$db_gas->generar_script_insert("");
//$db_gas->ejecutar_script();

// Insertar datos en pasivo_proveedores
$db_pas = new DBclass($dsn,"pasivo_proveedores",$pas);
$db_pas->generar_script_insert("");
$db_pas->ejecutar_script();

// Regresar al formulario y mandar un mensaje si se inserto registro con exito
header("location: ./fac_fac_cap.php");

?>