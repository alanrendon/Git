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
		$sql="UPDATE catalogo_puestos set descripcion='".$_POST['descripcion'.$i]."', sueldo=".$_POST['sueldo'.$i]." where cod_puestos='".$_POST['cod_puestos'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
	else if($_POST['eliminar'.$i]==1)
	{
		$sql="DELETE FROM catalogo_puestos where cod_puestos='".$_POST['cod_puestos'.$i]."'";
//		echo $_POST['id'.$i];
		ejecutar_script($sql,$dsn);
	}
}
header("location: ./fac_pue_con.php");
?>