<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
 

for($cia=101;$cia<184;$cia++)
{
	if (existe_registro("catalogo_companias",array("num_cia"),array($cia), $dsn))
	{
		echo "<br>Esta es la compañía encontrada  -->".$cia;
		$facturas = obtener_registro("fact_rosticeria",array("num_cia","fecha_mov"),array($cia, "04/08/2004"),"","",$dsn);
		$porcentajes = obtener_registro("porcentajes_facturas",array("num_cia"),array($cia),"","",$dsn);
		echo "<br> Factura ".$facturas[0]['num_fac']."<br>";
		
		echo "<br> Fecha movimiento ".$facturas[0]['fecha_mov'];
		$total=0;
		echo "interacciones : ".count($facturas);
		for ($i=0; $i<count($facturas);$i++) 
		{
			
			$total += $facturas[$i]['total'];
		}
		echo "<br> Total factura ".$total;
		
		$datosT['num_cia']=$cia;
		echo "<br>".$datosT['num_cia'] ."<br>";
		
		$datosT['num_fac']=$facturas[0]['num_fac'];
		echo $datosT['num_fac'] ."<br>";
		
		$datosT['total_fac']=$total;
		echo $datosT['total_fac'] ."<br>";
		
		$datosT['porc13']=$porcentajes[0]['porcentaje_13'];
		echo $datosT['porc13'] ."<br>";
		
		$datosT['porc795']=$porcentajes[0]['porcentaje_795'];
		echo $datosT['porc795'] ."<br>";
		
		$datosT['contado']=($porcentajes[0]['porcentaje_13'] / 100) * $total;
		echo $datosT['contado'] ."<br>";
		
		$datosT['credito']=($porcentajes[0]['porcentaje_795'] / 100) * $total;
		echo $datosT['credito'] ."<br>";
		
		$datosT['fecha']='04/08/2004';
		echo $datosT['fecha'] ."<br>";
		$datosT['pagado']=0;
		
		$dbtotalFac = new Dbclass($dsn,"total_fac_ros",$datosT);
		$dbtotalFac->generar_script_insert("");
		$dbtotalFac->ejecutar_script();
	}
	else echo "<br>No existe la compañia --->  ".$cia."<br>" ;
}
?>