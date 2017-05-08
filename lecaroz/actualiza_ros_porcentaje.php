<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);

//print_r ($_POST);

for ($i=0; $i<$_POST['cont']; $i++) 
{
	if($_POST['eliminar'.$i]==0)
	{
		$sql="UPDATE porcentajes_facturas set porcentaje_795='".$_POST['porcentaje1'.$i]."', porcentaje_13='".$_POST['porcentaje2'.$i]."' where idporcentajes_facturas='".$_POST['id'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
	else if($_POST['eliminar'.$i]==1)
	{
		$sql="DELETE FROM porcentajes_facturas where idporcentajes_facturas='".$_POST['id'.$i]."'";
//		echo $_POST['id'.$i];
		ejecutar_script($sql,$dsn);
	}
}
	
header("location: ./ros_porcentaje_con.php");

?>