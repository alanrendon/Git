<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = "captura_efectivos";
$var=0;
for ($i=0;$i<$_POST['contador'];$i++) {
	if($_POST['num_cia'.$i] != ""){
		if (existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia'.$i]), $dsn)){
			if(!existe_registro("captura_efectivos",array("num_cia","fecha"),array($_POST['num_cia'.$i],$_POST['fecha']),$dsn)){
				$datos['num_cia'.$var]=$_POST['num_cia'.$i];
				$datos['venta_pta'.$var]=$_POST['venta_pta'.$i];
				$datos['pastillaje'.$var]=$_POST['pastillaje'.$i];
				$datos['otros'.$var]=$_POST['otros'.$i];
				$datos['ctes'.$var]=$_POST['ctes'.$i];
				$datos['corte1'.$var]=$_POST['corte1'.$i];
				$datos['corte2'.$var]=$_POST['corte2'.$i];
				$datos['desc_pastel'.$var]=$_POST['desc_pastel'.$i];
				$datos['fecha'.$var]=$_POST['fecha'];
				$datos['am'.$var]=$_POST['am'.$i];
				$datos['am_error'.$var]=$_POST['am_error'.$i];
				$datos['pm'.$var]=$_POST['pm'.$i];
				$datos['pm_error'.$var]=$_POST['pm_error'.$i];
				$datos['pastel'.$var]=$_POST['pastel'.$i];
				$var++;
				
				if(existe_registro("total_panaderias",array("num_cia","fecha"),array($_POST['num_cia'.$i],$_POST['fecha']),$dsn))
				{
					$sql=
					"UPDATE total_panaderias set 
					venta_puerta = venta_puerta +".number_format($_POST['venta_pta'.$i],2,'.','').", 
					pastillaje = pastillaje + ".number_format($_POST['pastillaje'.$i],2,'.','').",
					otros = otros + ".number_format($_POST['otros'.$i],2,'.','').",
					efectivo= efectivo + ".number_format($_POST['venta_pta'.$i],2,'.','')." + ".number_format($_POST['pastillaje'.$i],2,'.','')." + ".number_format($_POST['otros'.$i],2,'.','').", 
					efe=true
					WHERE 
					num_cia=".$_POST['num_cia'.$i]." and 
					fecha='".$_POST['fecha']."'";
					ejecutar_script($sql,$dsn);
				}
				else
				{
					$sql="INSERT INTO total_panaderias 
					(num_cia,fecha,venta_puerta,pastillaje,otros,abono,gastos,raya_pagada,venta_pastel,abono_pastel,efectivo,efe,exp,gas,pro,pas) 
					VALUES
					(".$_POST['num_cia'.$i].", '".$_POST['fecha']."',".number_format($_POST['venta_pta'.$i],2,'.','').",".number_format($_POST['pastillaje'.$i],2,'.','').",".number_format($_POST['otros'.$i],2,'.','').",0,0,0,0,0,".number_format($_POST['venta_pta'.$i],2,'.','')." + ".number_format($_POST['pastillaje'.$i],2,'.','')." + ".number_format($_POST['otros'.$i],2,'.','').",true,false,false,false,false)";
					ejecutar_script($sql,$dsn);
				}
			}
		}
	}
}
if($var>0){
	$db = new DBclass($dsn, $tabla, $datos);
	$db->xinsertar();
}
else{
	header("location: ./pan_efm_cap.php?codigo_error=2");
	die();
}
unset($_SESSION['efm']);
header("location: ./pan_efm_cap.php");
?>
