<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];
//$fecha = getdate();
$var=0;
$var1=0;
$nomproveedor = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']),"","",$dsn);
$diascredito=$nomproveedor[0]['diascredito'];
$_dt=explode("/",$_POST['fecha']);
$d2 = $_dt[0];
$m2 = $_dt[1];
$y2 = $_dt[2];
$d2 =$d2+$diascredito;
$fecha2=date( "d/m/Y", mktime(0,0,0,$m2,$d2,$y2));
$bandera=false;

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_POST['fecha'],$fecha);
$fecha_historico = date("d/m/Y",mktime(0,0,0,$fecha[2],0,$fecha[3]));


for ($i=0; $i<10; $i++) 
{
	if($_POST['codmp'.$i] != ""){
		if (!(existe_registro("inventario_real",array("num_cia","codmp"),array($_POST['num_cia'],$_POST['codmp'.$i]), $dsn)))
		{
			$sql="INSERT INTO inventario_real (num_cia, codmp, fecha_entrada, fecha_salida, existencia, precio_unidad) VALUES (".$_POST['num_cia'].", ".$_POST['codmp'.$i].", '".$_POST['fecha']."', '".$_POST['fecha']."',0,0)"; 
			ejecutar_script($sql,$dsn); 
		}
		if (!(existe_registro("inventario_virtual",array("num_cia","codmp"),array($_POST['num_cia'],$_POST['codmp'.$i]), $dsn)))
		{
			$sql2="INSERT INTO inventario_virtual (num_cia, codmp, fecha_entrada, fecha_salida, existencia, precio_unidad) VALUES (".$_POST['num_cia'].", ".$_POST['codmp'.$i].", '".$_POST['fecha']."', '".$_POST['fecha']."',0,0)"; 
			ejecutar_script($sql2,$dsn); 
		}
		
		if (!(existe_registro("historico_inventario",array("num_cia","codmp","fecha"),array($_POST['num_cia'],$_POST['codmp'.$i],$fecha_historico),$dsn)))
		{
			$sql3="INSERT INTO historico_inventario (num_cia,codmp,fecha,existencia,precio_unidad) VALUES (".$_POST['num_cia'].", ".$_POST['codmp'.$i].", '".$fecha_historico."', 0,0)";
			ejecutar_script($sql3,$dsn);
		}
		
		
	}
}

$_SESSION['fac_cap']['num_pro'] = $_POST['num_proveedor'];

for ($i=0;$i<10;$i++) 
{
	$calculo=0;
	if($_POST['codmp'.$i] != "")
	{
		$sql = "SELECT * FROM inventario_real WHERE num_cia=".$_POST['num_cia']." AND codmp=".$_POST['codmp'.$i];
		$mp = ejecutar_script($sql,$dsn);
		
		$datos['num_proveedor'.$var]=$_POST['num_proveedor'];
		$datos['num_cia'.$var]=$_POST['num_cia'];
		$datos['num_documento'.$var]=$_POST['num_documento'];
		$datos['fecha'.$var]=$_POST['fecha'];
		$datos['fecha_pago'.$var]=$fecha2;
		$datos['porciento_desc_normal'.$var] = $_POST['desc1'.$i];
		$datos['porciento_desc_adicional2'.$var] = $_POST['desc2'.$i];
		$datos['porciento_desc_adicional3'.$var] = "0";
		$datos['ieps'.$var]="0";
		$datos['porciento_impuesto'.$var]=$_POST['iva'.$i];
		$datos['codgasto'.$var]="33";
		$datos['fecha_captura'.$var]=date("d/m/Y");
		$datos['iduser'.$var]=$_SESSION['iduser'];
		$datos['regalado'.$var]=$_POST['pago_proveedor'.$i];
		$datos['cantidad'.$var] = $_POST['cantidad'.$i];
		$datos['precio'.$var] = $_POST['precio'.$i];
//		$datos['costo_total'.$var]=$_POST['costo_total'];
		$datos['costo_unitario'.$var]=$_POST['total'.$i];
		$datos['costo_unitario'.$var]=$_POST['costo_unitario'.$i];
		$datos['contenido'.$var] = $_POST['contenido'.$i];
		$datos['codmp'.$var] = $_POST['codmp'.$i];
		$datos['pagado'.$var] = 'FALSE';
		$auxiliar['regalado'.$var]=$_POST['pago_proveedor'.$i];

		$mov_inv['num_cia'.$var]=$_POST['num_cia'];
		$mov_inv['codmp'.$var]=$_POST['codmp'.$i];
		$mov_inv['fecha'.$var]=$_POST['fecha'];
		$mov_inv['cod_turno'.$var]="";
		$mov_inv['tipo_mov'.$var]="FALSE";
	
		$mov_inv['existencia'.$var] = $_POST['cantidad'.$i] * $_POST['contenido'.$i] + $mp[0]['existencia'];
		$mov_inv['total_mov'.$var] = $_POST['costo_unitario'.$i];
		$mov_inv['descripcion'.$var] = "COMPRA F. NO. ".$_POST['num_documento'];
		$mov_inv['num_proveedor'.$var] = $_POST['num_proveedor'];
		
		if($_POST['cantidad'.$i] == 0) 
		{	
			$existencia = $_POST['costo_unitario'.$i] + $mp[0]['existencia'];
			$precio_unidad=1;
			$mov_inv['cantidad'.$var]=$_POST['costo_unitario'.$i];
			$mov_inv['precio'.$var]=$_POST['costo_unitario'.$i];
			$mov_inv['precio_unidad'.$var]=1;
		}

		else{ 
			if($mp[0]['existencia'] < 0) {
				$precio_unidad = $_POST['costo_unitario'.$i] / ($_POST['cantidad'.$i] * $_POST['contenido'.$i]);
				$mov_inv['precio'.$var] = $_POST['costo_unitario'.$i] / ($_POST['cantidad'.$i] * $_POST['contenido'.$i]);
				$mov_inv['precio_unidad'.$var] = $mov_inv['precio'.$var];
			}	
			else{
				$precio_unidad = ($_POST['costo_unitario'.$i] + (round($mp[0]['precio_unidad'],4) * round($mp[0]['existencia'],4))) / (round($mp[0]['existencia'],4) + ($_POST['cantidad'.$i] * $_POST['contenido'.$i]));
				$mov_inv['precio'.$var] = ($_POST['costo_unitario'.$i] + (round($mp[0]['precio_unidad'],4) * round($mp[0]['existencia'],4))) / (round($mp[0]['existencia'],4) + ($_POST['cantidad'.$i] * $_POST['contenido'.$i]));					
				$mov_inv['precio_unidad'.$var] = $mov_inv['precio'.$var];
			}
			$mov_inv['cantidad'.$var] = $_POST['cantidad'.$i] * $_POST['contenido'.$i];
			
			$existencia = ($_POST['cantidad'.$i] * $_POST['contenido'.$i]) + round($mp[0]['existencia'],4);
		}
		
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_POST['fecha'],$fecha);
		$fecha_historico = date("d/m/Y",mktime(0,0,0,$fecha[2],0,$fecha[3]));
		if ($mp) {
			$sql="UPDATE inventario_real SET existencia = ".$existencia.", fecha_entrada ='".$_POST['fecha']."', precio_unidad=".($precio_unidad > 0 ? round($precio_unidad,4) : "precio_unidad")." WHERE idinv=".$mp[0]['idinv'];
			ejecutar_script($sql,$dsn);
		}
		else {
			$sql="INSERT INTO inventario_real(num_cia,codmp,existencia,precio_unidad) VALUES($_POST[num_cia],".$_POST['codmp'.$i].",".$existencia.",".($precio_unidad > 0 ? round($precio_unidad,4) : 0).")";
			ejecutar_script($sql,$dsn);
			$sql="INSERT INTO historico_inventario (num_cia,codmp,fecha,existencia,precio_unidad) VALUES($_POST[num_cia],".$_POST['codmp'.$i].",'$fecha_historico',0,".($precio_unidad > 0 ? round($precio_unidad,4) : 0).")";
			ejecutar_script($sql,$dsn);
		}
		$var++;
	}
}

// Facturas
$fac['num_proveedor']     = $_POST['num_proveedor'];
$fac['num_cia']           = $_POST['num_cia'];
$fac['num_fact']          = $_POST['num_documento'];
$fac['fecha_mov']         = $_POST['fecha'];
$fac['fecha_ven']         = $fecha2;
$fac['concepto']          = "FACTURA DE MATERIA PRIMA ESPECIAL NO. ".$_POST['num_documento'];
$importe = 0;
$iva = 0;
for ($i=0; $i<$var; $i++) {
	if($auxiliar['regalado'.$i]==0){ //SE AGREGO ESTA LINEA PARA ALMACENAR EL EL TOTAL DE LOS PRODUCTOS NO REGALADOS
		$importe += $datos['costo_unitario'.$i];
		$iva += $_POST['importe_iva'.$i];
	}
}
$fac['imp_sin_iva']       = $importe - $iva;
$fac['porciento_iva']     = $_POST['iva0'];
$fac['importe_iva']       = $iva;
$fac['porciento_ret_isr'] = "0";
$fac['porciento_ret_iva'] = "0";
$fac['codgastos']         = 33;
$fac['importe_total']     = number_format($importe,2,'.','');
$fac['tipo_factura']      = "0";
$fac['fecha_captura']     = date("d/m/Y");
$fac['iduser'] = $_SESSION['iduser'];


if($_POST['num_cia']==146) $pasivo['num_cia']=147;
else if($_POST['num_cia']==171) $pasivo['num_cia']==170;
else $pasivo['num_cia']=$_POST['num_cia'];

$pasivo['num_fact']      = $_POST['num_documento'];
$pasivo['total']         = $_POST['costo_total'];
$pasivo['descripcion']   = "FACTURA MATERIA PRIMA ESPECIAL NO. ".$_POST['num_documento'];
$pasivo['fecha_mov']     = $_POST['fecha'];
$pasivo['fecha_pago']    = $fecha2;
$pasivo['num_proveedor'] = $fac['num_proveedor'];
$pasivo['codgastos']     = "33";
$pasivo['copia_fac']     = "FALSE";

$gas['codgastos'] = 33;
$gas['num_cia']   = $_POST['num_cia'];
$gas['fecha']     = $_POST['fecha'];
$gas['importe']   = $_POST['costo_total'];
$gas['concepto']  = "COMPRA F. NO. ".$_POST['num_documento'];
$gas['captura']	  = "TRUE";

// [5-Sep-2007] Registro para factura pendiente de aclarar
if (isset($_POST['aclaracion'])) {
	$acla['num_proveedor'] = $_POST['num_proveedor'];
	$acla['num_fact'] = $_POST['num_documento'];
	$acla['fecha_solicitud'] = date('d/m/Y');
	$acla['obs'] = trim(strtoupper($_POST['obs']));
}

//echo '<pre>' . print_r($acla, TRUE) . '</pre>';die();

$db = new DBclass($dsn, "entrada_mp", $datos);
$db1 = new DBclass($dsn, "pasivo_proveedores", $pasivo);
$db2 = new DBclass($dsn, "mov_inv_real", $mov_inv);
$db3 = new DBclass($dsn, "facturas", $fac);
if (isset($_POST['aclaracion'])) $db5 = new DBclass($dsn, 'facturas_pendientes', $acla);
//$db4 = new DBclass($dsn, "movimiento_gastos",$gas);


$db->xinsertar();
//$db1->xinsertar();
$db2->xinsertar();
//$db3->xinsertar();

$db1->generar_script_insert("");
$db1->ejecutar_script();

$db3->generar_script_insert("");
$db3->ejecutar_script();

if (isset($_POST['aclaracion'])) {
	$db5->generar_script_insert("");
	$db5->ejecutar_script();
}
/*
$db4->generar_script_insert("");
$db4->ejecutar_script();
*/
header("location: ./fac_esp_cap.php");
?>