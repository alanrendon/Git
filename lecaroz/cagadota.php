<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$total_fac=0;
$session = new sessionclass($dsn);
//print_r($_POST)."<br><br>";

$cia[0]=112;
$cia[1]=119;
$cia[2]=136;
$cia[3]=153;
$cia[4]=155;
$cia[5]=159;
$cia[6]=178;

$auxiliar=0;
$ax=0;
$fecha='2004/11/25';
for ($i=0;$i<7;$i++)
{
	$porcentajes = obtener_registro("porcentajes_facturas",array("num_cia"),array($cia[$i]),"","",$dsn);	
	$por13=$porcentajes[0]['porcentaje_13'];
	$por795=$porcentajes[0]['porcentaje_795'];
	

	$sql="select sum(total), num_fac from fact_rosticeria where num_cia=".$cia[$i]." and fecha_mov='".$fecha."' group by num_fac";
	$totales=ejecutar_script($sql,$dsn);
	
	if($totales)
	{
		/*
		$datosT['num_cia'.$auxiliar]=$cia[$i];
		$datosT['num_fac'.$auxiliar]=$totales[0]['num_fac'];
		$datosT['fecha'.$auxiliar]=$fecha;
		$datosT['total_fac'.$auxiliar]=$totales[0]['sum'];
		$datosT['porc13'.$auxiliar]=$porcentajes[0]['porcentaje_13'];
		$datosT['porc795'.$auxiliar]=$porcentajes[0]['porcentaje_795'];*/
		$auxil=($por13 / 100) * $totales[0]['sum'];/*
		$datosT['contado'.$auxiliar]=$auxil;
		$datosT['credito'.$auxiliar]=$totales[0]['sum'] - $auxil;
		$datosT['pagado'.$auxiliar]= "false";
		$auxiliar++;
		*/
		$credito=$totales[0]['sum'] - $auxil;
		if($porcentajes[0]['porcentaje_795'] > 0)
		{
			if($cia[$i]==140)
				$datosPP['num_cia'.$ax] = 147;
			else
				$datosPP['num_cia'.$ax] = $cia[$i];
			$datosPP['num_proveedor'.$ax] = "13";
			$datosPP['num_fact'.$ax] = $totales[0]['num_fac'];
			$datosPP['total'.$ax] = $credito;
			$datosPP['fecha_mov'.$ax] = $fecha;
			$datosPP['fecha_pago'.$ax] = $fecha;
			$datosPP['codgastos'.$ax] = "33";
			$datosPP['descripcion'.$ax] = "Facturas de Rosticerias";
			$ax++;
		}	
	}
}

//print_r ($datos);
$tabla="fact_rosticeria";

$dbPasivos= new Dbclass($dsn,"pasivo_proveedores",$datosPP);
//$dbTotales= new Dbclass($dsn,"total_fac_ros",$datosT);

//$dbPasivos->xinsertar();
//$dbTotales->xinsertar();


?>