<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);

//BORRA DE PASIVO A PROVEEDORES
if($_POST['num_cia']==146) $cia=147;
//else if($_POST['num_cia']==140) $cia=147;
else if($_POST['num_cia']==171) $cia==170;
else $cia=$_POST['num_cia'];

//print_r($_POST);
$pP="DELETE FROM pasivo_proveedores WHERE num_cia=".$cia." AND fecha_mov='".$_POST['fecha_mov']."' AND num_fact=".$_POST['num_fac']." AND num_proveedor=".$_POST['num_proveedor'];
//echo $pP."<br>";
ejecutar_script($pP,$dsn);
$fac="DELETE FROM facturas WHERE num_cia=".$_POST['num_cia']." AND fecha_mov='".$_POST['fecha_mov']."' AND num_fact=".$_POST['num_fac']." AND num_proveedor=".$_POST['num_proveedor'];
//echo $fac."<br>";
ejecutar_script($fac,$dsn);


// BORRA DE MOVIMIENTO A GASTOS
$mGasto="DELETE FROM movimiento_gastos WHERE num_cia=".$_POST['num_cia']." AND codgastos=".$_POST['codgastos']." AND fecha='".$_POST['fecha_mov']."' and concepto like '%".$_POST['num_fac']."%'";
//echo $mGasto."<br>";
ejecutar_script($mGasto,$dsn);

header("location: ./fac_prov_can.php");
?>