<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);

for ($i=0; $i<$_POST['cont']; $i++) 
{
	if($_POST['borrar'.$i]==0)
	{
		$sql="UPDATE gastos_caja set importe='".$_POST['importe'.$i]."', cod_gastos='".$_POST['concepto'.$i]."', clave_balance='".$_POST['clave'.$i]."', tipo_mov='".$_POST['tipo'.$i]."', comentario='".$_POST['comentario'.$i]."' where id='".$_POST['id'.$i]."'";
//		echo "modificar<br>";
		ejecutar_script($sql,$dsn);
	}
	else if($_POST['borrar'.$i]==1)
	{
		$sql="DELETE FROM gastos_caja where id='".$_POST['id'.$i]."'";
//		echo "borrar<br>";
		ejecutar_script($sql,$dsn);
	}
}
header("location: ./bal_gast_caja_mod.php");
?>