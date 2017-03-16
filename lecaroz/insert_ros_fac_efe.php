<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$total_fac=0;
$session = new sessionclass($dsn);
//print_r($_POST)."<br><br>";
$a=0;
$b=0;
$precio_unidad=0;
for ($i=0;$i<$_POST['cont_productos'];$i++)
{
	for ($j=0;$j<$_POST['cont_cias'];$j++)
	{
		if ($_POST['campo'.$i.$j]!="")
			{
			$invReal = obtener_registro("inventario_real",array("num_cia","codmp"),array($_POST['cia'.$j], $_POST['codmp'.$i]),"","",$dsn);
			$invVirtual = obtener_registro("inventario_virtual",array("num_cia","codmp"),array($_POST['cia'.$j], $_POST['codmp'.$i]),"","",$dsn);

			switch ($_POST['cia'.$j])
			{
				case 312: $sufijo = 'RB'; break;
				case 336: $sufijo = 'RC'; break;
				case 359: $sufijo = 'RO'; break;
				case 378: $sufijo = 'RA'; break;
			}

			$precio_unidad=$_POST['total'.$i.$j] / $_POST['campo'.$i.$j];
			$datos['num_cia'.$a]=$_POST['cia'.$j];
			$datos['fecha_mov'.$a]=$_POST['fecha'];
			$datos['cantidad'.$a]=$_POST['campo'.$i.$j];
			$datos['kilos'.$a]=$_POST['kilos'.$i.$j];
			$datos['total'.$a]=$_POST['total'.$i.$j];
			$datos['precio'.$a]=$_POST['precio'.$i];
			$datos['total_gral'.$a]=$_POST['total_gral'.$i];
			$datos['fecha_pago'.$a]=$_POST['fecha'];
			$datos['num_fac'.$a]=$sufijo.$_POST['num_fac'];
			$datos['codmp'.$a]=$_POST['codmp'.$i];
			$datos['num_proveedor'.$a]=$_POST['num_pro'];
			$datos['precio_unidad'.$a]=$precio_unidad;

			$datosMR['num_cia'.$a]=$_POST['cia'.$j];
			$datosMR['codmp'.$a]=$_POST['codmp'.$i];
			$datosMR['existencia'.$a]=$_POST['campo'.$i.$j] + $invReal[0]['existencia'];
			$datosMR['fecha'.$a]=$_POST['fecha'];
			$datosMR['cantidad'.$a]=$_POST['campo'.$i.$j];
			$datosMR['cod_turno'.$a]="11";
			$datosMR['tipo_mov'.$a]= "false";
			$datosMR['precio'.$a]=$_POST['precio'.$i];
			$datosMR['total_mov'.$a]=$_POST['total'.$i.$j];
			$datosMR['precio_unidad'.$a]=$precio_unidad;
			$datosMR['descripcion'.$a]="COMPRA F. NO. ".$sufijo.$_POST['num_fac'];
			$datosMR['num_fact'.$a]=$sufijo.$_POST['num_fac'];
			$datosMR['num_proveedor'.$a]=$_POST['num_pro'];

			$datosMV['num_cia'.$a]=$_POST['cia'.$j];
			$datosMV['codmp'.$a]=$_POST['codmp'.$i];
			$datosMV['existencia'.$a]=$_POST['campo'.$i.$j] + $invVirtual[0]['existencia'];
			$datosMV['fecha'.$a]=$_POST['fecha'];
			$datosMV['cantidad'.$a]=$_POST['campo'.$i.$j];
			$datosMV['cod_turno'.$a]="11";
			$datosMV['tipo_mov'.$a]= "false";
			$datosMV['precio'.$a]=$_POST['precio'.$i];
			$datosMV['total_mov'.$a]=$_POST['total'.$i.$j];
			$datosMV['precio_unidad'.$a]=$precio_unidad;
			$datosMV['descripcion'.$a]="COMPRA F. NO. ".$sufijo.$_POST['num_fac'];
			$datosMV['num_fact'.$a]=$sufijo.$_POST['num_fac'];
			$datosMV['num_proveedor'.$a]=$_POST['num_pro'];

			$exis=$invReal[0]['existencia'] + $_POST['campo'.$i.$j];
			$actualizaIR="UPDATE inventario_real SET existencia='".$exis."' where num_cia='".$_POST['cia'.$j]."' and codmp='".$_POST['codmp'.$i]."'";
			$actualizaIV="UPDATE inventario_virtual SET existencia='".$exis."' where num_cia='".$_POST['cia'.$j]."' and codmp='".$_POST['codmp'.$i]."'";
			ejecutar_script($actualizaIR,$dsn);
			ejecutar_script($actualizaIV,$dsn);
			$a++;
			}
	}
}
$por13=0;
$por795=0;
$tot=0;
$ax=0;
$auxil=0;
$auxiliar=0;
for ($j=0;$j<$_POST['cont_cias'];$j++)
{
	$auxil=0;
	$contado_redondeo=0;
	$total_redondeo=0;
	$credito_redondeo=0;
	$porcentajes = obtener_registro("porcentajes_facturas",array("num_cia"),array($_POST['cia'.$j]),"","",$dsn);
	$por13=$porcentajes[0]['porcentaje_13'];
	$por795=$porcentajes[0]['porcentaje_795'];
	$tot=$_POST['total_gral'.$j];
	$total_redondeo=number_format($tot,2,'.','');
	if($_POST['total_gral'.$j] > 0)
	{
		switch ($_POST['cia'.$j])
		{
			case 312: $sufijo = 'RB'; break;
			case 336: $sufijo = 'RC'; break;
			case 359: $sufijo = 'RO'; break;
			case 378: $sufijo = 'RA'; break;
		}

		$datosMG['num_cia'.$auxiliar] = $_POST['cia'.$j];
		$datosMG['codgastos'.$auxiliar] = "33";
		$datosMG['fecha'.$auxiliar] = $_POST['fecha'];
		$datosMG['importe'.$auxiliar] = $total_redondeo;
		$datosMG['concepto'.$auxiliar] = "COMPRA F. NO. ".$sufijo.$_POST['num_fac'];
		$datosMG['captura'.$auxiliar] = "true";

		$datosT['num_cia'.$auxiliar]=$_POST['cia'.$j];
		$datosT['num_fac'.$auxiliar]=$sufijo.$_POST['num_fac'];
		$datosT['fecha'.$auxiliar]=$_POST['fecha'];
		$datosT['total_fac'.$auxiliar]=$total_redondeo;
		$datosT['porc13'.$auxiliar]=$porcentajes[0]['porcentaje_13'];
		$datosT['porc795'.$auxiliar]=$porcentajes[0]['porcentaje_795'];
		$auxil=($por13 / 100) * $tot;
		$contado_redondeo=number_format($auxil,2,'.','');
		$datosT['contado'.$auxiliar]=$contado_redondeo;
		$credito_redondeo = $total_redondeo - $contado_redondeo;
		$datosT['credito'.$auxiliar]=number_format($credito_redondeo,2,'.','');
		$datosT['pagado'.$auxiliar]= "false";
		$datosT['num_proveedor'.$auxiliar]=$_POST['num_pro'];
		$auxiliar++;
	}
	if($_POST['total_gral'.$j] > 0 and $porcentajes[0]['porcentaje_795'] > 0)
	{
		/*if($_POST['cia'.$j]==146)
			$datosPP['num_cia'.$ax] = 147;
		else if($_POST['cia'.$j]==171)
			$datosPP['num_cia']=170;
		else*/
			$datosPP['num_cia'.$ax] = $_POST['cia'.$j];
		$datosPP['num_proveedor'.$ax] = $_POST['num_pro'];
		$datosPP['num_fact'.$ax] = $sufijo.$_POST['num_fac'];
		$datosPP['total'.$ax] = number_format($credito_redondeo,2,'.','');
//		$datosPP['total'.$ax] = $_POST['total_general'.$j];
		$datosPP['fecha'.$ax] = $_POST['fecha'];
		$datosPP['codgastos'.$ax] = "33";
		$datosPP['descripcion'.$ax] = "Facturas de Rosticerias";
		$ax++;
	}
}

//print_r ($datos);
$tabla="fact_rosticeria";
$db = new Dbclass($dsn,$tabla,$datos);
$dbmovReal= new Dbclass($dsn,"mov_inv_real",$datosMR);
$dbmovVirtual= new Dbclass($dsn,"mov_inv_virtual",$datosMV);
$dbmovGastos= new Dbclass($dsn,"movimiento_gastos",$datosMG);
$dbPasivos= new Dbclass($dsn,"pasivo_proveedores",$datosPP);
$dbTotales= new Dbclass($dsn,"total_fac_ros",$datosT);

$db->xinsertar();
$dbmovVirtual->xinsertar();
$dbmovReal->xinsertar();
$dbmovGastos->xinsertar();
$dbPasivos->xinsertar();
$dbTotales->xinsertar();
unset($_SESSION['factura_ros_esp']);
header("location: ./ros_fac_efe_cap.php");

?>
