<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
if($_POST['contador'] > 0){
	for ($i=0; $i<$_POST['contador']; $i++) 
	{
		if($_POST['autorizado'.$i]==1)
		{
			$sql="UPDATE modificacion_pastel set estado = true, fecha_autorizacion ='".date("d/m/Y")."' where id='".$_POST['id'.$i]."'";
			ejecutar_script($sql,$dsn);
		}
	}
}

if($_POST['contador_efe'] > 0){
	for($i=0;$i<$_POST['contador_efe'];$i++){
		if($_POST['revisado'.$i]==1){
			$sql="UPDATE revision_efectivos set estado = true where id=".$_POST['id_efe'.$i];
			ejecutar_script($sql,$dsn);
		}
	}
}
header("location: ./pan_rev_sol_v2.php");
?>