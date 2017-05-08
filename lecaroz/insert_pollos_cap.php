<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);

$tabla = $_GET['tabla'];

for($i=0;$i<$_POST['contador'];$i++){
	if(existe_registro("control_pollos",array("num_cia","codmp"),array($_POST['num_cia'.$i],$_POST['codmp'.$i]),$dsn))
		ejecutar_script("update control_pollos set lunes=".$_POST['lunes'.$i].", martes =".$_POST['martes'.$i].", miercoles =".$_POST['miercoles'.$i].", jueves =".$_POST['jueves'.$i].", viernes = ".$_POST['viernes'.$i].", sabado=".$_POST['sabado'.$i].", domingo=".$_POST['domingo'.$i]." WHERE num_cia=".$_POST['num_cia'.$i]." and codmp=".$_POST['codmp'.$i],$dsn);
	else
		ejecutar_script("insert into control_pollos(num_cia,codmp,lunes,martes,miercoles,jueves,viernes,sabado,domingo) values(".$_POST['num_cia'.$i].",".$_POST['codmp'.$i].",".$_POST['lunes'.$i].",".$_POST['martes'.$i].",".$_POST['miercoles'.$i].",".$_POST['jueves'.$i].",".$_POST['viernes'.$i].",".$_POST['sabado'.$i].",".$_POST['domingo'.$i].")",$dsn);
}

header("location: ./ros_control_pollo2.php");
?>