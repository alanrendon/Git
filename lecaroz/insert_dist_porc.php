<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$sql="DELETE FROM distribuciones where num_cia=".$_POST['num_cia'];
ejecutar_script($sql,$dsn);

$j=0;
for ($i=0;$i<$_POST['contador'];$i++) 
{
	if($_POST['num_accionista'.$i] != "")
	{
		$datos['num_cia'.$j]=$_POST['num_cia'];
		$datos['accionista'.$j]=$_POST['num_accionista'.$i];
		$datos['porcentaje'.$j]=$_POST['porcentaje'.$i];
		$j++;
	}
}

$db = new DBclass($dsn, "distribuciones", $datos);

$db->xinsertar();
header("location: ./admin_dist_altas.php");
?>