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
		$sql="UPDATE catalogo_notario set nombre='".$_POST['nombre_notario'.$i]."', num_notario=".$_POST['num_notario'.$i]." where cod_notario='".$_POST['cod_notario'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
	else if($_POST['eliminar'.$i]==1)
	{
		$sql="DELETE FROM catalogo_notario where cod_notario='".$_POST['cod_notario'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
}
header("location: ./ban_notario_con.php");
?>