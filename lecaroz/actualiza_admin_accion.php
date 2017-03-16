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
		$sql="UPDATE catalogo_accionistas set nombre='".$_POST['nombre'.$i]."', ap_pat='".$_POST['ap_pat'.$i]."', ap_mat='".$_POST['ap_mat'.$i]."', nombre_corto='".$_POST['nombre_corto'.$i]."' where id='".$_POST['id'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
	else if($_POST['eliminar'.$i]==1)
	{
		$sql="DELETE FROM catalogo_accionistas where id='".$_POST['id'.$i]."'";
//		echo $_POST['id'.$i];
		ejecutar_script($sql,$dsn);
	}
}
header("location: ./admin_accion_con.php");
?>