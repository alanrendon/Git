<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);

for ($i=0; $i<$_POST['contador']; $i++) 
{
	if($_POST['autorizado'.$i]==1)
	{
		$sql="UPDATE modificacion_efectivos set revisado = true, fecha_autorizacion ='".date("d/m/Y")."' where id='".$_POST['id'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
}
header("location: ./pan_rev_efm.php");
?>