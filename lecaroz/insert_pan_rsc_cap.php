<?php
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$db = DB::connect($dsn);
$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];
for ($i=0;$i<10;$i++) {
	if($_POST['num_cia'.$i] != ""){
	
			if (!existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia'.$i]), $dsn)) {
				header("location: ./pan_rsc_cap.php?codigo_error=1&cia=".$_POST['num_cia'.$i]);
				die;
			}
			if (!existe_registro("venta_pastel",array("num_cia","num_remi"),array($_POST['num_cia'.$i],$_POST['num_remi'.$i]), $dsn)){
				header("location: ./pan_rsc_cap.php?codigo_error=2&fac=".$_POST['num_remi'.$i]);
				die;
			 }		
			 else{				
							$sql="Select idventa_pastel from venta_pastel where num_cia=".$_POST['num_cia'.$i]."and num_remi=".$_POST['num_remi'.$i];
							$result = $db->query($sql);
							if(DB::isError($result))
							{ 
								die($result->getUserInfo());
							}
							$row = $result->fetchRow(); 
							$datos['idventa_pastel'.$i] = $row[0];
							$datos['concepto'.$i]=$_POST['concepto'.$i];
				}
		
	}
}


$db1 = new DBclass($dsn, $tabla, $datos);

$db1->xinsertar();

header("location: ./pan_rsc_cap.php?mensaje=se+capturaron+movimientos");
?>
