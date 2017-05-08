<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn)
;
$tabla="catalogo_limite_gasto";

	for ($i=0;$i<$_POST['contador'];$i++) 
	{
		if($_POST['limite'.$i]!=""){
			if(existe_registro($tabla,array("num_cia","codgastos"),array($_POST['num_cia'.$i],$_POST['codgastos']),$dsn)){
				$sql="UPDATE catalogo_limite_gasto set limite=".number_format($_POST['limite'.$i],2,'.','')." WHERE num_cia=".$_POST['num_cia'.$i]." and codgastos=".$_POST['codgastos'];
				ejecutar_script($sql,$dsn);
			}
			else{
				$sql="INSERT INTO catalogo_limite_gasto(num_cia,codgastos,limite) VALUES(".$_POST['num_cia'.$i].", ".$_POST['codgastos'].",".number_format($_POST['limite'.$i],2,'.','').")";
				ejecutar_script($sql,$dsn);
			}
		}
	}

//$db = new DBclass($dsn, $tabla, $datos);

//$db->xinsertar();
header("location: ./pan_gas_aut.php");
?>