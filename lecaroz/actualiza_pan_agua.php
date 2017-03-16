<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
//print_r ($_POST);
for ($i=0; $i<$_POST['cont2']; $i++) 
{
	if($_POST['eliminar'.$i]==0)
	{
		if($_POST['medida1'.$i]=="")
			$medida1=0;
		else
			$medida1=number_format($_POST['medida1'.$i],2,'.','');

		if($_POST['medida2'.$i]=="")
			$medida2=0;
		else
			$medida2=number_format($_POST['medida2'.$i],2,'.','');

		if($_POST['medida3'.$i]=="")
			$medida3=0;
		else
			$medida3=number_format($_POST['medida3'.$i],2,'.','');

		if($_POST['medida4'.$i]=="")
			$medida4=0;
		else
			$medida4=number_format($_POST['medida4'.$i],2,'.','');

		$sql="UPDATE medidor_agua set medida1=$medida1, medida2=$medida2, medida3=$medida3, medida4=$medida4 where id=".$_POST['idagua'.$i];
		ejecutar_script($sql,$dsn);
	}
	else if($_POST['eliminar'.$i]==1)
	{
		$sql="DELETE FROM medidor_agua where id=".$_POST['idagua'.$i];
		ejecutar_script($sql,$dsn);
	}
}
header("location: ./pan_agu_mod.php");
?>