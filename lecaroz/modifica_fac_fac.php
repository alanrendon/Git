<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
//print_r($_POST);
//******************************************************************************************  PRIMERO CANCELA LA FACTURA
$total=0;
for ($i=0;$i<$_POST['contador'];$i++) 
{
	$total=0;
	if($_POST['cantidad_ant'.$i]!=""){
		$invReal = obtener_registro("inventario_real",array("num_cia","codmp"),array($_POST['num_cia'], $_POST['codmp'.$i]),"","",$dsn);
	//ACTUALIZAR LOS DATOS DEL INVENTARIO REAL
		$total=$invReal[0]['existencia'] - ($_POST['cantidad_ant'.$i] * $_POST['contenido'.$i]);
		$sql="update inventario_real set existencia=".$total." where num_cia=".$_POST['num_cia']." and codmp=".$_POST['codmp'.$i];
		ejecutar_script($sql,$dsn);
	//BORRA LOS MOVIMIENTOS AL INVENTARIO REAL
		$mInvR="DELETE FROM mov_inv_real WHERE num_cia='".$_POST['num_cia']."' AND codmp='".$_POST['codmp'.$i]."' AND fecha='".$_POST['fecha_mov']."' AND tipo_mov=false and descripcion like '%".$_POST['num_fac']."%'";
		ejecutar_script($mInvR,$dsn);
	}
}
//BORRA DE PASIVO A PROVEEDORES
if($_POST['num_cia']==146) $cia=147;
//else if($_POST['num_cia']==140) $cia=147;
else if($_POST['num_cia']==171) $cia==170;
else $cia=$_POST['num_cia'];


$pP="DELETE FROM pasivo_proveedores WHERE num_cia='".$cia."' AND fecha_mov='".$_POST['fecha_mov']."' AND num_fact='".$_POST['num_fac']."'";
//BORRA DE FACTURAS
$fac="DELETE FROM facturas WHERE num_cia=".$_POST['num_cia']." AND fecha_mov='".$_POST['fecha_mov']."' AND num_fact='".$_POST['num_fac']."' AND num_proveedor=".$_POST['num_proveedor'];
ejecutar_script($pP,$dsn);
ejecutar_script($fac,$dsn);

//$gasto="33";
// BORRA DE MOVIMIENTO A GASTOS
//$mGasto="DELETE FROM movimiento_gastos WHERE num_cia='".$_POST['num_cia']."' AND codgastos='".$gasto."' AND fecha='".$_POST['fecha_mov']."' and concepto like '%".$_POST['num_fac']."%'";
//BORRA DE ENTRADA DE MP
$tFac="DELETE FROM entrada_mp WHERE num_cia='".$_POST['num_cia']."' AND num_documento='".$_POST['num_fac']."' AND fecha='".$_POST['fecha_mov']."'";
//ejecutar_script($mGasto,$dsn);
ejecutar_script($tFac,$dsn);

//**********************************************************************************************INSERTA LA NUEVA FACTURA

$costo_unitario=0;
$cantidad=0;
$nomproveedor = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']),"","",$dsn);
$diascredito=$nomproveedor[0]['diascredito'];
$_dt=explode("/",$_POST['fecha_mov']);
$d2 = $_dt[0];
$m2 = $_dt[1];
$y2 = $_dt[2];
$d2 =$d2+$diascredito;
$fecha2=date( "d/m/Y", mktime(0,0,0,$m2,$d2,$y2));

// Reconstruir datos para la tabla de entrada_mp
$count = 0;
for ($i=0; $i<$_POST['contador']; $i++) {
	if($_POST['cantidad'.$i] /*!= ""*/> 0){
		$datos['num_cia'.$count] = $_POST['num_cia'];
		$datos['num_proveedor'.$count] = $_POST['num_proveedor'];
		$datos['num_documento'.$count] = $_POST['num_fac'];
		$datos['fecha'.$count] = $_POST['fecha_mov'];
		$datos['cantidad'.$count] = $_POST['cantidad'.$i];
		$datos['codmp'.$count] = $_POST['codmp'.$i];
		$datos['contenido'.$count] = $_POST['contenido'.$i];
		$datos['porciento_desc_normal'.$count] = $_POST['desc1'.$i];
		$datos['porciento_desc_adicional2'.$count] = $_POST['desc2'.$i];
		$datos['porciento_desc_adicional3'.$count] = $_POST['desc3'.$i];
		$datos['ieps'.$count] = $_POST['ieps'.$i];
		$datos['costo_unitario'.$count] = $_POST['total'.$i];
		$datos['costo_total'.$count] = $_POST['total_factura'];
		$datos['porciento_impuesto'.$count] = $_POST['iva'.$i];
		$datos['pagado'.$count] = 'FALSE';
		$datos['fecha_pago'.$count] = $fecha2;
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
	$mov['fecha'.$i]         = $_POST['fecha_mov'];
	$mov['cod_turno'.$i]     = "";
	$mov['tipo_mov'.$i]      = "FALSE";
	$mov['cantidad'.$i]      = $datos['cantidad'.$i] * $datos['contenido'.$i];
	$mov['existencia'.$i]    = $temp[0]['existencia'];
	$mov['precio'.$i]        = $datos['precio'.$i];
	$mov['total_mov'.$i]     = $datos['costo_unitario'.$i];
	$mov['precio_unidad'.$i] = $mov['total_mov'.$i] / $mov['cantidad'.$i];
	$mov['descripcion'.$i]   = "COMPRA F. NO. ".$datos['num_documento'.$i];
	
	// actualizar inventarios
	if ($temp[0]['existencia'] >= 0) {
		@$inv['precio_unidad'.$i] = ($datos['costo_unitario'.$i] + ($temp[0]['existencia'] * $temp[0]['precio_unidad'])) / (($datos['cantidad'.$i] * $datos['contenido'.$i]) + $temp[0]['existencia']);
		@$costo_unitario=($datos['costo_unitario'.$i] + ($temp[0]['existencia'] * $temp[0]['precio_unidad'])) / (($datos['cantidad'.$i] * $datos['contenido'.$i]) + $temp[0]['existencia']);
	}
	else {
		@$inv['precio_unidad'.$i] = $datos['costo_unitario'.$i] / ($datos['cantidad'.$i] * $datos['contenido'.$i]);
		@$costo_unitario=$datos['costo_unitario'.$i] / ($datos['cantidad'.$i] * $datos['contenido'.$i]);
	}
	$cantidad=$datos['cantidad'.$i] * $datos['contenido'.$i];
	// movimiento_gastos
	
	
	// Actualizar inventario real (entrada)
	if (existe_registro("inventario_real",array("num_cia","codmp"),array($_POST['num_cia'],$datos['codmp'.$i]),$dsn)) {
		$sql="update inventario_real set existencia=existencia + ".$cantidad." where num_cia=".$_POST['num_cia']." and codmp=".$datos['codmp'.$i];
		ejecutar_script($sql,$dsn);
	}
	else{
		$sql="INSERT INTO inventario_real(num_cia,codmp,fecha_entrada,fecha_salida,existencia,precio_unidad) VALUES(".$_POST['num_cia'].",".$datos['codmp'.$i].",'".date("d/m/Y")."','".date("d/m/Y")."',".$cantidad.",".$costo_unitario.")";
		ejecutar_script($sql,$dsn);
		$sql="INSERT INTO historico_inventario (num_cia,codmp,fecha,existencia,precio_unidad) VALUES($_POST[num_cia],".$datos['codmp'.$i].",'$fecha_historico',0,$costo_unitario)";
		ejecutar_script($sql,$dsn);
	}
}

// Facturas
$fac1['num_proveedor']     = $_POST['num_proveedor'];
$fac1['num_cia']           = $_POST['num_cia'];
$fac1['num_fact']          = $_POST['num_fac'];
$fac1['fecha_mov']         = $_POST['fecha_mov'];
$fac1['fecha_ven']         = $fecha2;
$fac1['concepto']          = "FACTURA MATERIA PRIMA ".$_POST['num_fac'];

$importe = 0;
$iva = 0;
for ($i=0; $i<$count; $i++) {
	if($auxiliar['bandera'.$i]==0){ //SE AGREGO ESTA LINEA PARA ALMACENAR EL EL TOTAL DE LOS PRODUCTOS NO REGALADOS
		$importe += $datos['costo_unitario'.$i];
		$iva += $datos['porciento_impuesto'.$i];
	}
}

$fac1['imp_sin_iva']       = $importe - $iva;
$fac1['porciento_iva']     = $datos['porciento_impuesto0'];
$fac1['importe_iva']       = $iva;
$fac1['porciento_ret_isr'] = "0";
$fac1['porciento_ret_iva'] = "0";
$fac1['codgastos']         = 33;
$fac1['importe_total']     = $importe;
$fac1['tipo_factura']      = "0";
$fac1['fecha_captura']     = date("d/m/Y");
$fac1['iduser'] = $_SESSION['iduser'];

// pasivo_proveedores

if($_POST['num_cia']==146) $pas['num_cia']=147;
else if($_POST['num_cia']==171) $pas['num_cia']==170;
else $pas['num_cia']=$_POST['num_cia'];

//$pas['num_cia']       = $_POST['num_cia'];
$pas['num_fact']      = $_POST['num_fac'];
$pas['total']         = $_POST['total_factura'];
$pas['descripcion']   = "COMPRA FACTURA MATERIA PRIMA";
$pas['fecha_mov']     = $_POST['fecha_mov'];
$pas['fecha_pago']    = $fecha2;
$pas['num_proveedor'] = $_POST['num_proveedor'];
$pas['codgastos']     = 33;


// Hacer un nuevo objeto DBclass
$db = new DBclass($dsn,"entrada_mp",$datos);
$db->xinsertar();

// Insertar datos en mov_inv_real y mov_inv_virtual
$db_mov_real = new DBclass($dsn,"mov_inv_real",$mov);
$db_mov_real->xinsertar();

// Insertar datos en facturas
$db_fac = new DBclass($dsn,"facturas",$fac1);
$db_fac->generar_script_insert("");
$db_fac->ejecutar_script();

// Insertar datos en pasivo_proveedores
$db_pas = new DBclass($dsn,"pasivo_proveedores",$pas);
$db_pas->generar_script_insert("");
$db_pas->ejecutar_script();

header("location: ./fac_fac_ad.php");
?>
