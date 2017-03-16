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
		$sql="UPDATE control_produccion set";
		if ($_POST['precio_raya'.$i]!="") $sql.=" precio_raya =".$_POST['precio_raya'.$i].",";
		if ($_POST['porc_raya'.$i]!="") $sql.=" porc_raya =".$_POST['porc_raya'.$i].",";
		if ($_POST['orden'.$i]!="") $sql.=" num_orden =".$_POST['orden'.$i].",";
		if ($_POST['precio_venta'.$i]=="") $sql.=" precio_venta = 0 ";
		else if($_POST['precio_venta'.$i]!="") $sql.=" precio_venta =".$_POST['precio_venta'.$i];
		$sql.=" where idcontrol_produccion='".$_POST['id'.$i]."'";
//		echo $sql."<br>";

		ejecutar_script($sql,$dsn);
	}
	else if($_POST['eliminar'.$i]==1)
	{
		$sql="DELETE FROM control_produccion where idcontrol_produccion='".$_POST['id'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
}
	
header("location: ./pan_prec_con.php");

?>