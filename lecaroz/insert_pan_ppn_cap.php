<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = "prueba_pan";
$var=0;
for ($i=0;$i<$_POST['cont'];$i++) 
{
	if(existe_registro("prueba_pan",array("num_cia","fecha"),array($_POST['num_cia'.$i],$_POST['fecha_mov']),$dsn)){
		if(number_format($_POST['importe'.$i],2,'.','') == 0){
			$sql="DELETE from prueba_pan WHERE num_cia=".$_POST['num_cia'.$i]." and fecha='".$_POST['fecha_mov']."'";
			ejecutar_script($sql,$dsn);
		}
		else{
			$sql="UPDATE prueba_pan SET importe=".$_POST['importe'.$i]." WHERE num_cia=".$_POST['num_cia'.$i]." and fecha='".$_POST['fecha_mov']."'";
			ejecutar_script($sql,$dsn);
		}
	}
	else{
		if(number_format($_POST['importe'.$i],2,'.','') == 0)
			continue;
		$datos['num_cia'.$var]=$_POST['num_cia'.$i];
		$datos['fecha'.$var]=$_POST['fecha_mov'];
		$datos['importe'.$var]=$_POST['importe'.$i];
		$datos['produccion'.$var]=$_POST['produccion'.$i];
		$var++;
	}
}

if($var>0){
	$db = new DBclass($dsn, $tabla, $datos);
	$db->xinsertar();
}

unset($_SESSION['ppn']);
header("location: ./pan_ppn_cap.php");
?>