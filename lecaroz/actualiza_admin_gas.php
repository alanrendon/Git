<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
//print_r ($_POST);
//echo "<br>".count($_POST);
for ($i=0; $i<$_POST['contador']; $i++) 
{
	if($_POST['revisado'.$i]==1)
	{
		$sql="UPDATE movimiento_gastos_cancelados set revisado=true where id=".$_POST['id'.$i];
		ejecutar_script($sql,$dsn);
	}
}
header("location: ./admin_gas_con.php");
?>