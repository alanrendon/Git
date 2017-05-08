<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
//ASIGNACION GENERAL DE COMPAÑIAS
if($_POST['status']==0){
	for($i=0;$i<$_POST['contador'];$i++){
		$sql="UPDATE catalogo_companias set idoperadora=".$_POST['operadora'.$i]." WHERE num_cia=".$_POST['num_cia'.$i];
		ejecutar_script($sql,$dsn);
	}
}
//ASIGNACION POR OPERADORA
else if($_POST['status']==1){
	for($i=0;$i<20;$i++){
		if($_POST['num_cia'.$i]!=""){
			$sql="UPDATE catalogo_companias set idoperadora=".$_POST['operadora']." WHERE num_cia=".$_POST['num_cia'.$i];
			ejecutar_script($sql,$dsn);
		}
	}
}
//ASIGNACION POR TRASPASO
else if($_POST['status']==2){
	for($i=0;$i<$_POST['contador'];$i++){
		$sql="UPDATE catalogo_companias set idoperadora=".$_POST['operadora'.$i]." WHERE num_cia=".$_POST['num_cia'.$i];
		ejecutar_script($sql,$dsn);
	}
}
header("location: ./admin_opera_asign.php");
?>