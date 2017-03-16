<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla = "inventario_fin_mes";
$numfilas = 15;

$sql = "";
for ($i=0; $i<$_POST['numfilas']; $i++) {
	//if ($_POST['inventario'.$i] == "" || $_POST['inventario'.$i] == 0) {
		if ($id = ejecutar_script("SELECT id FROM inventario_fin_mes WHERE num_cia=".$_POST['num_cia'.$i]." AND codmp=".$_POST['codmp'.$i]." AND fecha='".$_POST['fecha'.$i]."'",$dsn))
			$sql .= "UPDATE inventario_fin_mes SET inventario=".(($_POST['inventario'.$i] != 0)?$_POST['inventario'.$i]:0).",diferencia=".(($_POST['diferencia'.$i] != 0)?$_POST['diferencia'.$i]:0)." WHERE id=".$id[0]['id'].";\n";
		else
			$sql .= "INSERT INTO inventario_fin_mes (num_cia,codmp,fecha,precio_unidad,existencia,inventario,diferencia) VALUES (".$_POST['num_cia'.$i].",".$_POST['codmp'.$i].",'".$_POST['fecha'.$i]."',".(($_POST['precio_unidad'.$i] != 0)?$_POST['precio_unidad'.$i]:0).",".(($_POST['existencia'.$i] != 0)?$_POST['existencia'.$i]:0).",".(($_POST['inventario'.$i] != 0)?$_POST['inventario'.$i]:0).",".(($_POST['diferencia'.$i] != 0)?$_POST['diferencia'.$i]:0).");\n";
		//ejecutar_script($sql,$dsn);
	//}
}
ejecutar_script($sql, $dsn);


if (!isset($_GET['ros'])) {
	for ($i=0; $i<$numfilas; $i++) {echo "<br>CODMP:{$_POST['new_codmp'.$i]}";
		if ($_POST['new_codmp'.$i] > 0/* && $_POST['new_inventario'.$i] > 0*/) {
			if (!($id = ejecutar_script("SELECT id FROM inventario_fin_mes WHERE num_cia=".$_POST['new_num_cia'.$i]." AND codmp=".$_POST['new_codmp'.$i]." AND fecha='".$_POST['new_fecha'.$i]."'",$dsn))) {
				ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_POST['new_fecha'.$i],$fecha);
				$fecha1 = "1/$fecha[2]/$fecha[3]";
				$fecha1 = $_POST['new_fecha'.$i];
				$fecha_historico = date("d/m/Y",mktime(0,0,0,$fecha[2],0,$fecha[3]));

				// Obtener el costo promedio de las facturas de otras panaderias correspondientes al mes
				$sql = "SELECT precio_unidad FROM inventario_real WHERE codmp=".$_POST['new_codmp'.$i]." AND precio_unidad IS NOT NULL LIMIT 1";
				$temp = ejecutar_script($sql,$dsn);
				$precio_unidad = ($temp)?$temp[0]['precio_unidad']:0;

				$sql = "INSERT INTO inventario_fin_mes (num_cia,codmp,fecha,precio_unidad,existencia,inventario,diferencia) VALUES (".$_POST['new_num_cia'.$i].",".$_POST['new_codmp'.$i].",'".$_POST['new_fecha'.$i]."',$precio_unidad,0,".(($_POST['new_inventario'.$i] != 0)?$_POST['new_inventario'.$i]:0).",".(($_POST['new_inventario'.$i] != 0)?-1*$_POST['new_inventario'.$i]:0).")";

				// Insertar registro en historico e inventarios
				if ( ! ejecutar_script("SELECT * FROM historico_inventario WHERE num_cia = {$_POST['new_num_cia'.$i]} AND fecha = '{$fecha_historico}' AND codmp = {$_POST['new_codmp'.$i]}", $dsn))
				{
					$sql .= "INSERT INTO historico_inventario (num_cia,codmp,fecha,existencia,precio_unidad) VALUES (".$_POST['new_num_cia'.$i].",".$_POST['new_codmp'.$i].",'$fecha_historico',0,$precio_unidad);";
					$sql .= "INSERT INTO inventario_real (num_cia,codmp,existencia,precio_unidad) VALUES (".$_POST['new_num_cia'.$i].",".$_POST['new_codmp'.$i].",0,$precio_unidad);";
				}

				ejecutar_script($sql,$dsn);
			}
		}
	}
	header("location: ./pan_invfm_cap.php");
}
else
	header("location: ./ros_invfm_cap.php");
//$db = new Dbclass($dsn,$tabla,$_POST);
//$db->xinsertar();

?>
