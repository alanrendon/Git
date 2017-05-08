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
		$sql="UPDATE pesos_companias set peso_max='".$_POST['peso_maximo'.$i]."', peso_min='".$_POST['peso_minimo'.$i]."' where id='".$_POST['id'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
	else if($_POST['eliminar'.$i]==1)
	{
		$sql="DELETE FROM pesos_companias where id='".$_POST['id'.$i]."'";
//		echo $_POST['id'.$i];
		ejecutar_script($sql,$dsn);
	}
}
	
header("location: ./ros_pesos_con.php");

?>