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
		$nombre = ejecutar_script("SELECT nombre FROM catalogo_mat_primas WHERE codmp = {$_POST['codmp' . $i]}", $dsn);
		$sql="UPDATE precios_guerra set precio_compra='".$_POST['precio_compra'.$i]."', precio_venta='".$_POST['precio_venta'.$i]."', nombre_alt = '" . (trim($_POST['nombre_alt' . $i]) != '' ? strtoupper(trim($_POST['nombre_alt' . $i])) : $nombre) . "' where id='".$_POST['id'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
	else if($_POST['eliminar'.$i]==1)
	{
		$sql="DELETE FROM precios_guerra where id='".$_POST['id'.$i]."'";
//		echo $_POST['id'.$i];
		ejecutar_script($sql,$dsn);
	}
}
	
header("location: ./ros_precios_con.php");

?>