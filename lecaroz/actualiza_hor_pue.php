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
		$sql="UPDATE catalogo_horarios set descripcion='".$_POST['descripcion'.$i]."', horaentrada='".$_POST['horaentrada'.$i]."', horasalida='".$_POST['horasalida'.$i]."' where cod_horario='".$_POST['cod_horario'.$i]."'";
		ejecutar_script($sql,$dsn);
	}
	else if($_POST['eliminar'.$i]==1)
	{
		$sql="DELETE FROM catalogo_horarios where cod_horario='".$_POST['cod_horario'.$i]."'";
//		echo $_POST['id'.$i];
		ejecutar_script($sql,$dsn);
	}
}
header("location: ./fac_hor_con.php");
?>