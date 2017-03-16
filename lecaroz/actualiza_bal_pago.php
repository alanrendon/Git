<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
//echo "Revision";
//echo $_POST['contador']."<br>";
for ($i=0; $i<$_POST['contador']; $i++) 
{
	$reg = obtener_registro("reservas_cias",array("cod_reserva","num_cia","anio"),array($_POST['reserva'],$_POST['num_cia'.$i],date("Y")-1),"","",$dsn);
	$diciembre=$_POST['pagado'.$i]-($reg[0]['importe']*11);
//	echo $diciembre."<br>";
	$sql1="UPDATE reservas_cias set importe=".$diciembre." where num_cia=".$_POST['num_cia'.$i]." and cod_reserva=".$_POST['reserva']." and fecha='01/12/".(date("Y")-1)."'";
//	echo $sql1."<br>";
	$sql="UPDATE reservas_cias set pagado=".$_POST['pagado'.$i]." where num_cia=".$_POST['num_cia'.$i]." and cod_reserva=".$_POST['reserva']." and anio=".(date("Y")-1);
	ejecutar_script($sql,$dsn);
	ejecutar_script($sql1,$dsn);
}
header("location: ./bal_res_pago.php");
?>