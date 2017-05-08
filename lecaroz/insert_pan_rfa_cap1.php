<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$total_fac=0;
$session = new sessionclass($dsn);
$tabla = $_GET['tabla'];
$db = new Dbclass($dsn,$tabla,$_POST);//INSERTA DATOS A LA TABLA DE "FACT_ROSTICERIAS"

for ($i=0; $i < $db->numfilas; $i++) 
{
	if (!(existe_registro("inventario_real",array("num_cia","codmp"),array($_POST['num_cia'.$i],$_POST['codmp'.$i]), $dsn)))
	{
		$sql="INSERT INTO inventario_real (num_cia, codmp, fecha_entrada, fecha_salida, existencia, precio_unidad) VALUES (".$_POST['num_cia'.$i].", ".$_POST['codmp'.$i].", '".$_POST['fecha_mov'.$i]."', '".$_POST['fecha_mov'.$i]."',0,0)"; 
		ejecutar_script($sql,$dsn); 
	}
	if (!(existe_registro("inventario_virtual",array("num_cia","codmp"),array($_POST['num_cia'.$i],$_POST['codmp'.$i]), $dsn)))
	{
		$sql2="INSERT INTO inventario_virtual (num_cia, codmp, fecha_entrada, fecha_salida, existencia, precio_unidad) VALUES (".$_POST['num_cia'.$i].", ".$_POST['codmp'.$i].", '".$_POST['fecha_mov'.$i]."', '".$_POST['fecha_mov'.$i]."',0,0)"; 
		ejecutar_script($sql2,$dsn); 
	}

}

for ($i=0; $i < $db->numfilas; $i++) 
	{
		$invReal = obtener_registro("inventario_real",array("num_cia","codmp"),array($_POST['num_cia'.$i], $_POST['codmp'.$i]),"","",$dsn);
//		if(!($invReal)){$sql="INSERT INTO inventario_real (num_cia, codmp, fecha_entrada, fecha_salida, existencia, precio_unidad) VALUES (".$_POST['num_cia'.$i].", ".$_POST['codmp'.$i].", '".$_POST['fecha_mov'.$i]."', '".$_POST['fecha_mov'.$i]."',0,0)"; ejecutar_script($sql,$dsn); }
		$invVirtual = obtener_registro("inventario_virtual",array("num_cia","codmp"),array($_POST['num_cia'.$i], $_POST['codmp'.$i]),"","",$dsn);
//		if(!($invVirtual)){$sql="INSERT INTO inventario_virtual (num_cia, codmp,fecha_entrada, fecha_salida,existencia,precio_unidad) VALUES (".$_POST['num_cia'.$i].", ".$_POST['codmp'.$i].", '".$_POST['fecha_mov'.$i]."', '".$_POST['fecha_mov'.$i]."',0,0)"; ejecutar_script($sql,$dsn); }
		$porcentajes = obtener_registro("porcentajes_facturas",array("num_cia"),array($_POST['num_cia'.$i]),"","",$dsn);	
		$datosMG['num_cia'.$i] = $_POST['num_cia'.$i];
		$datosMG['codgastos'.$i] = "33";
		$datosMG['fecha'.$i] = $_POST['fecha_mov'.$i];
		$datosMG['importe'.$i] = $_POST['total'.$i];
		$datosMG['concepto'.$i] = "COMPRA F. NO. ".$_POST['num_fac'.$i];
		
		$datosPP['num_cia'.$i] = $_POST['num_cia'.$i];
		$datosPP['codmp'.$i] = $_POST['codmp'.$i];
		$datosPP['num_proveedor'.$i] = "13";
		$datosPP['num_fact'.$i] = $_POST['num_fac'.$i];
		$datosPP['total'.$i] = $_POST['total'.$i];
		$datosPP['fecha_mov'.$i] = $_POST['fecha_mov'.$i];
		$datosPP['fecha_pago'.$i] = $_POST['fecha_pago'.$i];
		$datosPP['descripcion'.$i] = "Captura de Facturas de Rosticerias";

		$sql="SELECT * FROM inventario_real WHERE num_cia='".$_POST['num_cia'.$i]."' AND codmp='".$_POST['codmp'.$i]."'";
		$mp=ejecutar_script($sql,$dsn);
		$datosIR['num_cia'.$i]=$_POST['num_cia'.$i];
		$datosIR['codmp'.$i]=$_POST['codmp'.$i];
		$datosIR['existencia'.$i]=$_POST['cantidad'.$i] + $invReal[0]['existencia'];
		$datosIR['fecha_entrada'.$i]=$_POST['fecha_mov'.$i];
		$datosIR['fecha_salida'.$i]=$_POST['fecha_mov'.$i];
//		$datosIR['precio_unidad'.$i]=$_POST['precio_unidad'.$i];
		$datosIR['precio_unidad'.$i]=($_POST['total'.$i] + $mp[0]['existencia'] * $mp[0]['precio_unidad']) / ($mp[0]['existencia'] + $_POST['cantidad'.$i]);
	
		$datosMR['num_cia'.$i]=$_POST['num_cia'.$i];
		$datosMR['codmp'.$i]=$_POST['codmp'.$i];
		$datosMR['existencia'.$i]=$_POST['cantidad'.$i] + $invReal[0]['existencia'];
		$datosMR['fecha'.$i]=$_POST['fecha_mov'.$i];
		$datosMR['cantidad'.$i]=$_POST['cantidad'.$i];
		$datosMR['cod_turno'.$i]="11";
		$datosMR['tipo_mov'.$i]= "false";
		$datosMR['precio'.$i]=$_POST['precio'.$i];
		$datosMR['total_mov'.$i]=$_POST['total'.$i];
		$datosMR['precio_unidad'.$i]=$_POST['precio_unidad'.$i];
		$datosMR['descripcion'.$i]="COMPRA F. NO. ".$_POST['num_fac'.$i];

		$datosIV['num_cia'.$i]=$_POST['num_cia'.$i];
		$datosIV['codmp'.$i]=$_POST['codmp'.$i];
		$datosIV['existencia'.$i]=$_POST['cantidad'.$i] + $invReal[0]['existencia'];
		$datosIV['fecha_entrada'.$i]=$_POST['fecha_mov'.$i];
		$datosIV['fecha_salida'.$i]=$_POST['fecha_mov'.$i];
		$datosIV['precio_unidad'.$i]=$_POST['precio_unidad'.$i];

		$datosMV['num_cia'.$i]=$_POST['num_cia'.$i];
		$datosMV['codmp'.$i]=$_POST['codmp'.$i];
		$datosMV['existencia'.$i]=$_POST['cantidad'.$i] + $invVirtual[0]['existencia'];
		$datosMV['fecha'.$i]=$_POST['fecha_mov'.$i];
		$datosMV['cantidad'.$i]=$_POST['cantidad'.$i];
		$datosMV['cod_turno'.$i]="11";
		$datosMV['tipo_mov'.$i]= "false";
		$datosMV['precio'.$i]=$_POST['precio'.$i];
		$datosMV['total_mov'.$i]=$_POST['total'.$i];
		$datosMV['precio_unidad'.$i]=$_POST['precio_unidad'.$i];
		$datosMV['descripcion'.$i]="COMPRA F. NO. ".$_POST['num_fac'.$i];

		$total_fac += $_POST['total'.$i];
		
		$datosT['num_cia']=$_POST['num_cia'.$i];
		$datosT['num_fac']=$_POST['num_fac'.$i];
		$datosT['fecha']=$_POST['fecha_mov'.$i];
	}
$datosT['total_fac']=$total_fac;
$datosT['porc13']=$porcentajes[0]['porcentaje_13'];
$datosT['porc795']=$porcentajes[0]['porcentaje_795'];
$datosT['contado']=($porcentajes[0]['porcentaje_13'] / 100) * $total_fac;
$datosT['credito']=($porcentajes[0]['porcentaje_795'] / 100) * $total_fac;
$datosT['pagado']= "false";

$dbinvReal= new Dbclass($dsn,"inventario_real",$datosIR); //OBJETO DIRIGIDO A ACTUALIZAR EL INVENTARIO REAL
$dbmovReal= new Dbclass($dsn,"mov_inv_real",$datosMR); //OBJETO DIRIGIDO A INSERTAR EL INVENTARIO REAL
$dbinvVirtual= new Dbclass($dsn,"inventario_virtual",$datosIV); //OBJETO DIRIGIDO A ACTUALIZAR EL INVENTARIO REAL
$dbmovVirtual= new Dbclass($dsn,"mov_inv_virtual",$datosMV); //OBJETO DIRIGIDO A INSERTAR EL INVENTARIO REAL
$dbmovGastos= new Dbclass($dsn,"movimiento_gastos",$datosMG); //OBJETO DIRIGIDO A INSERTAR MOVIMIENTO DE GASTOS
$dbPasivos= new Dbclass($dsn,"pasivo_proveedores",$datosPP);

$dbtotalFac= new Dbclass($dsn,"total_fac_ros",$datosT); //OBJETO DIRIGIDO A INSERTAR EL TOTAL DE LA FACTURA

for($i=0; $i < $db->numfilas; $i++)
{
	$dbinvReal->generar_script_update($i,array("num_cia","codmp"),array($datosIR['num_cia'.$i],$datosIR['codmp'.$i]));
	$dbinvVirtual->generar_script_update($i,array("num_cia","codmp"),array($datosIV['num_cia'.$i],$datosIV['codmp'.$i]));
	$dbinvReal->ejecutar_script();
	$dbinvVirtual->ejecutar_script();
}
$db->xinsertar();
$dbmovVirtual->xinsertar();
$dbmovReal->xinsertar();
$dbmovGastos->xinsertar();
$dbPasivos->xinsertar();

$dbtotalFac->generar_script_insert("");
$dbtotalFac->ejecutar_script();
header("location: ./ros_fac_cap.php");
?>