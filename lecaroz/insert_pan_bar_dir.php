<?php
// CONEXIONES
// Inserción de registros en una tabla

include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass($dsn);

// Obtener tabla de trabajo
// ------------------------------------------------------------------------------------
//print_r($_POST);
$aux=0;
//print_r($_POST);
for ($i=0;$i<$_POST['contador'];$i++) {
	if(existe_registro("barredura",array("num_cia","fecha_cap"),array($_POST['num_cia'.$i],$_POST['fecha']),$dsn))
		continue;
	else{
		$datos['num_cia'.$aux]=$_POST['num_cia'.$i];
		$datos['fecha_cap'.$aux]=$_POST['fecha'];
		$datos['fecha_pago'.$aux]=$_POST['fecha'];
		$datos['importe'.$aux]=$_POST['importe'.$i];
		$sql="UPDATE total_panaderias set otros = otros + ".$_POST['importe'.$i].", efectivo= efectivo + ".$_POST['importe'.$i]." where num_cia=".$_POST['num_cia'.$i]." and fecha='".$_POST['fecha']."'";
		ejecutar_script($sql,$dsn);
		$aux++;
	}

}
// Hacer un nuevo objeto DBclass
if($aux > 0){
	$db = new DBclass($dsn,"barredura",$datos);
	$db->xinsertar();
}
unset($_SESSION['bar_dir']);
// Regresar al formulario y mandar un mensaje si se inserto registro con exito
header("location: ./pan_bar_dir.php");
?>