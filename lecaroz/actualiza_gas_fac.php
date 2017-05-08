<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
for ($i=0;$i<$_POST['contador'];$i++) 
{
	//ACTUALIZAR LOS DATOS DEL INVENTARIO REAL
	$invReal = obtener_registro("inventario_real",array("num_cia","codmp"),array($_POST['num_cia'], 90),"","",$dsn);
	$invVirtual = obtener_registro("inventario_virtual",array("num_cia","codmp"),array($_POST['num_cia'], 90),"","",$dsn);
	$cantIR=$invReal[0]['existencia'] - $_POST['litros'.$i];
	$cantIV=$invVirtual[0]['existencia'] - $_POST['litros'.$i];
	$sql="update inventario_real set existencia=".$cantIR." where num_cia=".$_POST['num_cia']." and codmp=90";
	ejecutar_script($sql,$dsn);
	$sql="update inventario_virtual set existencia=".$cantIV." where num_cia=".$_POST['num_cia']." and codmp=90";
	ejecutar_script($sql,$dsn);

	//BORRA LOS MOVIMIENTOS AL INVENTARIO REAL
	$mInvR="DELETE FROM mov_inv_real WHERE num_cia='".$_POST['num_cia']."' AND codmp='90' AND fecha='".$_POST['fecha_mov']."' AND tipo_mov=false and descripcion like '%".$_POST['num_fac']."%'";
	ejecutar_script($mInvR,$dsn);
	$mInvV="DELETE FROM mov_inv_virtual WHERE num_cia='".$_POST['num_cia']."' AND codmp='90' AND fecha='".$_POST['fecha_mov']."' AND tipo_mov=false and descripcion like '%".$_POST['num_fac']."%'";
	ejecutar_script($mInvV,$dsn);

}
//BORRA DE PASIVO A PROVEEDORES
if($_POST['num_cia']==146) $cia=147;
else if($_POST['num_cia']==171) $cia==170;
else $cia=$_POST['num_cia'];


$pP="DELETE FROM pasivo_proveedores WHERE num_cia='".$cia."' AND fecha_mov='".$_POST['fecha_mov']."' AND num_fact='".$_POST['num_fac']."'";
//BORRA DE FACTURAS
$fac="DELETE FROM facturas WHERE num_cia=".$_POST['num_cia']." AND fecha_mov='".$_POST['fecha_mov']."' AND num_fact='".$_POST['num_fac']."' AND num_proveedor=".$_POST['num_proveedor'];
$fac_gas="DELETE FROM factura_gas WHERE num_cia=".$_POST['num_cia']." AND num_fact='".$_POST['num_fac']."' AND num_proveedor=".$_POST['num_proveedor'];
ejecutar_script($pP,$dsn);
ejecutar_script($fac,$dsn);
ejecutar_script($fac_gas,$dsn);

$gasto="33";
// BORRA DE MOVIMIENTO A GASTOS
$mGasto="DELETE FROM movimiento_gastos WHERE num_cia='".$_POST['num_cia']."' AND codgastos='".$gasto."' AND fecha='".$_POST['fecha_mov']."' and concepto like '%".$_POST['num_fac']."%'";
//BORRA DE ENTRADA DE MP
//$tFac="DELETE FROM entrada_mp WHERE num_cia='".$_POST['num_cia']."' AND num_fac='".$_POST['num_fac']."' AND fecha='".$_POST['fecha_mov']."'";
ejecutar_script($mGasto,$dsn);
//ejecutar_script($tFac,$dsn);
header("location: ./fac_gas_can.php");
?>
