<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
$total_fac=0;
$session = new sessionclass($dsn);
$tabla = "fact_rosticeria";
//$tabla=$_GET['tabla'];
//print_r($_POST);
$db = new Dbclass($dsn,$tabla,$_POST);//INSERTA DATOS A LA TABLA DE "FACT_ROSTICERIAS"
$numero=0;
$num_pro = NULL;
$total = 0;
for ($i=0; $i < $db->numfilas; $i++) 
	{
		$invReal = obtener_registro("inventario_real",array("num_cia","codmp"),array($_POST['num_cia'.$i], $_POST['codmp'.$i]),"","",$dsn);
		$invVirtual = obtener_registro("inventario_virtual",array("num_cia","codmp"),array($_POST['num_cia'.$i], $_POST['codmp'.$i]),"","",$dsn);

		$datosIR['num_cia'.$i]=$_POST['num_cia'.$i];
		$datosIR['codmp'.$i]=$_POST['codmp'.$i];
		$datosIR['existencia'.$i] = $invReal[0]['existencia'] - $_POST['cantidad'.$i];
		$datosIR['fecha_entrada'.$i]=date("d/m/Y");
		$datosIR['fecha_salida'.$i]=date("d/m/Y");
		$datosIR['precio_unidad'.$i]=$invReal[0]['precio_unidad'];

		$datosIV['num_cia'.$i]=$_POST['num_cia'.$i];
		$datosIV['codmp'.$i]=$_POST['codmp'.$i];
		$datosIV['existencia'.$i]= $invReal[0]['existencia'] - $_POST['cantidad'.$i];
		$datosIV['fecha_entrada'.$i]=date("d/m/Y");
		$datosIV['fecha_salida'.$i]=date("d/m/Y");
		$datosIV['precio_unidad'.$i]=$invVirtual[0]['precio_unidad'];

		if($_POST['num_cia'.$i]==146)
			$numero=147;
		else if($_POST['num_cia'.$i]==140)
			$numero=147;
		else if($_POST['num_cia'.$i]==171)
			$numero=170;
		else $numero=$_POST['num_cia'.$i];
		
		$mInvR="DELETE FROM mov_inv_real WHERE num_cia='".$_POST['num_cia'.$i]."' AND codmp='".$_POST['codmp'.$i]."' AND fecha='".$_POST['fecha_mov'.$i]."' AND tipo_mov=false and descripcion like '%".$_POST['num_fac'.$i]."%'";
		$mInvV="DELETE FROM mov_inv_virtual WHERE num_cia='".$_POST['num_cia'.$i]."' AND codmp='".$_POST['codmp'.$i]."' AND fecha='".$_POST['fecha_mov'.$i]."' AND tipo_mov=false and descripcion like '%".$_POST['num_fac'.$i]."%'";
		//$pP="DELETE FROM pasivo_proveedores WHERE num_cia='".$numero."' AND fecha_mov='".$_POST['fecha_mov'.$i]."' AND num_fact='".$_POST['num_fac'.$i]."'";
		
		$pP = "DELETE FROM pasivo_proveedores WHERE (num_proveedor, num_fact) IN (SELECT num_proveedor, num_fact FROM fact_rosticeria WHERE num_cia = {$_POST['num_cia' . $i]} AND fecha_mov = '{$_POST['fecha_mov' . $i]}' AND num_fact = '{$_POST['num_fac' . $i]}' AND num_proveedor = {$_POST['num_pro' . $i]})";
		
		$fac="DELETE FROM fact_rosticeria WHERE num_cia='".$_POST['num_cia'.$i]."' AND codmp='".$_POST['codmp'.$i]."' AND fecha_mov='".$_POST['fecha_mov'.$i]."' AND num_fac='".$_POST['num_fac'.$i]."' AND num_proveedor = {$_POST['num_pro' . $i]}";
		ejecutar_script($mInvR,$dsn);
		ejecutar_script($mInvV,$dsn);
		ejecutar_script($pP,$dsn);
		ejecutar_script($fac,$dsn);
		
		//datos para total_fac_rosticeria
		
		$num_pro = $_POST['num_pro' . $i];
		$total += $_POST['total' . $i];
	}

$cia=$_POST['num_cia0'];
$fac=$_POST['num_fac0'];
$fech=$_POST['fecha_mov0'];
//$mp=$_POST['codmp'.$i];


$gasto="33";
$mGasto="DELETE FROM movimiento_gastos WHERE num_cia='".$cia."' AND codgastos='".$gasto."' AND fecha='".$fech."' and concepto like '%".$_POST['num_fac0']."%' AND importe = " . number_format($total, 2, '.', '');
$tFac="DELETE FROM total_fac_ros WHERE num_cia='".$cia."' AND num_fac='".$fac."' AND fecha='".$fech."' AND num_proveedor = $num_pro";


ejecutar_script($mGasto,$dsn);
ejecutar_script($tFac,$dsn);

$dbinvReal= new Dbclass($dsn,"inventario_real",$datosIR); //OBJETO DIRIGIDO A ACTUALIZAR EL INVENTARIO REAL
$dbinvVirtual= new Dbclass($dsn,"inventario_virtual",$datosIV); //OBJETO DIRIGIDO A ACTUALIZAR EL INVENTARIO REAL

for($i=0; $i < $db->numfilas; $i++)
{
	$dbinvReal->generar_script_update($i,array("num_cia","codmp"),array($datosIR['num_cia'.$i],$datosIR['codmp'.$i]));
	$dbinvVirtual->generar_script_update($i,array("num_cia","codmp"),array($datosIV['num_cia'.$i],$datosIV['codmp'.$i]));
	$dbinvReal->ejecutar_script();
	$dbinvVirtual->ejecutar_script();
}

header("location: ./ros_fac_can.php");

?>
