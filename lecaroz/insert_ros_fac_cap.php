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

function toInt($value) {
	return intval($value, 10);
}

for ($i=0; $i < $db->numfilas; $i++)
	{
		$invReal = obtener_registro("inventario_real",array("num_cia","codmp"),array($_POST['num_cia'.$i], $_POST['codmp'.$i]),"","",$dsn);
		$invVirtual = obtener_registro("inventario_virtual",array("num_cia","codmp"),array($_POST['num_cia'.$i], $_POST['codmp'.$i]),"","",$dsn);

		if (ejecutar_script("SELECT
			id
		FROM
			catalogo_rosticerias_distribucion_semanal
		WHERE
			num_cia = {$_POST['num_cia' . $i]}
			AND num_proveedor = {$_POST['num_proveedor' . $i]}
			AND '{$_POST['fecha_mov' . $i]}'::DATE BETWEEN fecha_inicio_semana AND fecha_inicio_semana + INTERVAL '6 DAYS'
			AND tsbaja IS NULL
		LIMIT 1", $dsn))
		{
			list($dia, $mes, $anio) = array_map('toInt', explode('/', $_POST['fecha_mov' . $i]));

			$dia_semana = date('w', mktime(0, 0, 0, $mes, $dia, $anio));echo "DIA SEMANA: {$dia_semana}<br/>";

			if (ejecutar_script("SELECT
				id
			FROM
				catalogo_rosticerias_distribucion_semanal
			WHERE
				num_cia = {$_POST['num_cia' . $i]}
				AND num_proveedor = {$_POST['num_proveedor' . $i]}
				AND '{$_POST['fecha_mov' . $i]}'::DATE BETWEEN fecha_inicio_semana AND fecha_inicio_semana + INTERVAL '6 DAYS'
				AND tsbaja IS NULL
				AND dia_semana = {$dia_semana}
			LIMIT 1", $dsn))
			{
				$porcentajes[0] = array(
					'porcentaje_13'		=> 0,
					'porcentaje_795'	=> 100
				);// echo "100% CREDITO: " . print_r($porcentajes[0], TRUE);
			}
			else
			{
				$porcentajes[0] = array(
					'porcentaje_13'		=> 100,
					'porcentaje_795'	=> 0
				);// echo "100% CONTADO" . print_r($porcentajes[0], TRUE);
			}
		}
		else
		{
			$porcentajes = obtener_registro("porcentajes_facturas",array("num_cia"),array($_POST['num_cia'.$i]),"","",$dsn);
		}// die;

		$sql="SELECT * FROM inventario_real WHERE num_cia='".$_POST['num_cia'.$i]."' AND codmp='".$_POST['codmp'.$i]."'";
		$mp=ejecutar_script($sql,$dsn);
		$datosIR['num_cia'.$i]=$_POST['num_cia'.$i];
		$datosIR['codmp'.$i]=$_POST['codmp'.$i];
		$datosIR['existencia'.$i]=number_format($_POST['cantidad'.$i],2,'.','') + number_format($invReal[0]['existencia'],2,'.','');
		$datosIR['fecha_entrada'.$i]=$_POST['fecha_mov'.$i];
		$datosIR['fecha_salida'.$i]=$_POST['fecha_mov'.$i];

		//MODIFICACION 20 DE MAYO DEL 2005
//		if($mp)
			$datosIR['precio_unidad'.$i] = ($_POST['total'.$i] + $mp[0]['existencia'] * $mp[0]['precio_unidad']) / ($mp[0]['existencia'] + $_POST['cantidad'.$i]);
//		else
//			$datosIR['precio_unidad'.$i] = $_POST['cantidad'.$i];

		$datosMR['num_cia'.$i]=$_POST['num_cia'.$i];
		$datosMR['codmp'.$i]=$_POST['codmp'.$i];
		$datosMR['existencia'.$i]=$_POST['cantidad'.$i] + $invReal[0]['existencia'];
		$datosMR['fecha'.$i]=$_POST['fecha_mov'.$i];
		$datosMR['cantidad'.$i]=number_format($_POST['cantidad'.$i],2,'.','');
		$datosMR['cod_turno'.$i]="11";
		$datosMR['tipo_mov'.$i]= "false";
		$datosMR['precio'.$i]=$_POST['precio'.$i];
		$datosMR['total_mov'.$i]=$_POST['total'.$i];
		$datosMR['precio_unidad'.$i]=$_POST['precio_unidad'.$i];
		$datosMR['descripcion'.$i]="COMPRA F. NO. ".$_POST['num_fac'.$i];
		$datosMR['num_proveedor'.$i]=$_POST['num_proveedor'.$i];

		$datosIV['num_cia'.$i]=$_POST['num_cia'.$i];
		$datosIV['codmp'.$i]=$_POST['codmp'.$i];
		$datosIV['existencia'.$i]=number_format($_POST['cantidad'.$i],2,'.','') + number_format($invReal[0]['existencia'],2,'.','');
		$datosIV['fecha_entrada'.$i]=$_POST['fecha_mov'.$i];
		$datosIV['fecha_salida'.$i]=$_POST['fecha_mov'.$i];
		$datosIV['precio_unidad'.$i]=$_POST['precio_unidad'.$i];

		$datosMV['num_cia'.$i]=$_POST['num_cia'.$i];
		$datosMV['codmp'.$i]=$_POST['codmp'.$i];
		$datosMV['existencia'.$i]=number_format($_POST['cantidad'.$i],2,'.','') + number_format($invVirtual[0]['existencia'],2,'.','');
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
		$datosT['num_proveedor']=$_POST['num_proveedor'.$i];

		/*if($_POST['num_cia'.$i]==146)
			$datosPP['num_cia']=147;
		else if($_POST['num_cia'.$i]==171)
			$datosPP['num_cia']=170;
		else*/
			$datosPP['num_cia']=$_POST['num_cia'.$i];

		$datosPP['num_fact']=$_POST['num_fac'.$i];
		$datosPP['fecha']=$_POST['fecha_mov'.$i];
//		$datosPP['fecha_pago']=$_POST['fecha_pago'.$i];
		$datosPP['num_proveedor']=$_POST['num_proveedor'.$i];
	}
$aux=0;
$contado_redondeo=0;
$credito_redondeo=0;
$total_redondeo=0;

$total_redondeo=number_format($total_fac,2,'.','');

$datosT['total_fac']=$total_redondeo;
$datosT['porc13']=$porcentajes[0]['porcentaje_13'];
$datosT['porc795']=$porcentajes[0]['porcentaje_795'];
$aux=($porcentajes[0]['porcentaje_13'] / 100) * $total_redondeo;
$contado_redondeo=number_format($aux,2,'.','');
$datosT['contado']=$contado_redondeo;
$credito_redondeo=$total_redondeo-$contado_redondeo;
$datosT['credito']=number_format($credito_redondeo,2,'.','');
$datosT['pagado']= "false";

//$datosPP['num_proveedor'] = "13";
$datosPP['total'] = number_format($credito_redondeo,2,'.','');
$datosPP['descripcion'] = "Captura de Facturas de Rosticerias";
$datosPP['codgastos']="33";

$datosMG['num_cia'] = $_POST['num_cia0'];
$datosMG['codgastos'] = "33";
$datosMG['fecha'] = $_POST['fecha_mov0'];
$datosMG['importe'] = /*$total_redondeo*/$contado_redondeo;	// [16-Feb-2010] Solo se debe guardar lo pagado en contado
$datosMG['concepto'] = "COMPRA F. NO. ".$_POST['num_fac0']/* . ' (CONTADO)'*/;
$datosMG['captura'] = "false";


$dbinvReal= new Dbclass($dsn,"inventario_real",$datosIR); //OBJETO DIRIGIDO A ACTUALIZAR EL INVENTARIO REAL
$dbmovReal= new Dbclass($dsn,"mov_inv_real",$datosMR); //OBJETO DIRIGIDO A INSERTAR EL INVENTARIO REAL
$dbinvVirtual= new Dbclass($dsn,"inventario_virtual",$datosIV); //OBJETO DIRIGIDO A ACTUALIZAR EL INVENTARIO REAL
$dbmovVirtual= new Dbclass($dsn,"mov_inv_virtual",$datosMV); //OBJETO DIRIGIDO A INSERTAR EL INVENTARIO REAL
$dbmovGastos= new Dbclass($dsn,"movimiento_gastos",$datosMG); //OBJETO DIRIGIDO A INSERTAR MOVIMIENTO DE GASTOS

if ($datosPP['total'] > 0)
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

$dbmovGastos->generar_script_insert("");
$dbmovGastos->ejecutar_script();

$dbtotalFac->generar_script_insert("");
$dbtotalFac->ejecutar_script();

if($porcentajes[0]['porcentaje_795'] > 0)
{
	$dbPasivos->generar_script_insert("");
	$dbPasivos->ejecutar_script();
}
unset($_SESSION['factura_ros']);

header("location: ./ros_fac_cap.php");

?>
