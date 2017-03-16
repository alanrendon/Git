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
		$sql="UPDATE catalogo_sindicatos set nombre_sindicato='".$_POST['nombre_sindicato'.$i]."' where idsindicato='".$_POST['idsindicato'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
	else if($_POST['eliminar'.$i]==1)
	{
		$sql="DELETE FROM catalogo_sindicatos where idsindicato='".$_POST['idsindicato'.$i]."'";
//		echo $_POST['id'.$i];
		ejecutar_script($sql,$dsn);
	}
}
header("location: ./ban_sind_con.php");
?>