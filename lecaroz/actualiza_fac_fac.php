<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
//print_r($_POST);
$total=0;
for ($i=0;$i<$_POST['contador'];$i++) 
{
	$invReal = obtener_registro("inventario_real",array("num_cia","codmp"),array($_POST['num_cia'], $_POST['codmp'.$i]),"","",$dsn);
//ACTUALIZAR LOS DATOS DEL INVENTARIO REAL
	$total=$invReal[0]['existencia'] - ($_POST['cantidad'.$i] * $_POST['contenido'.$i]);
	$sql="update inventario_real set existencia=".$total." where num_cia=".$_POST['num_cia']." and codmp=".$_POST['codmp'.$i];
	ejecutar_script($sql,$dsn);
//BORRA LOS MOVIMIENTOS AL INVENTARIO REAL
	$mInvR="DELETE FROM mov_inv_real WHERE num_cia='".$_POST['num_cia']."' AND codmp='".$_POST['codmp'.$i]."' AND fecha='".$_POST['fecha_mov']."' AND tipo_mov=false and descripcion like '%".$_POST['num_fac']."%'";
	ejecutar_script($mInvR,$dsn);
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

$gasto="33";
// BORRA DE MOVIMIENTO A GASTOS
$mGasto="DELETE FROM movimiento_gastos WHERE num_cia='".$_POST['num_cia']."' AND codgastos='".$gasto."' AND fecha='".$_POST['fecha_mov']."' and concepto like '%".$_POST['num_fac']."%'";
//BORRA DE ENTRADA DE MP
$tFac="DELETE FROM entrada_mp WHERE num_cia='".$_POST['num_cia']."' AND num_documento='".$_POST['num_fac']."' AND fecha='".$_POST['fecha_mov']."'";
ejecutar_script($mGasto,$dsn);
ejecutar_script($tFac,$dsn);
header("location: ./fac_fac_can.php");
?>
