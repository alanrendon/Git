<?php
// CAPTURA DIRECTA DE EFECTIVOS
// Tabla 'importe_efectivos'
// Menu 'Panaderias->Efectivos->Captura directa'
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$tabla="catalogo_avio_autorizado";

	for ($i=0;$i<$_POST['contador'];$i++) 
	{
		if($_POST['fd'.$i]!="" or $_POST['fn'.$i]!="" or $_POST['biz'.$i]!="" or $_POST['rep'.$i]!=""){
			
			if(existe_registro($tabla,array("num_cia","codmp"),array($_POST['num_cia'.$i],$_POST['codmp']),$dsn)){
				$sql="UPDATE catalogo_avio_autorizado set frances_noche=".($_POST['fn'.$i] > 0 ? number_format($_POST['fn'.$i],2,'.','') : 0).", frances_dia=".($_POST['fd'.$i] > 0 ? number_format($_POST['fd'.$i],2,'.','') : 0).", bizcochero=".($_POST['biz'.$i] > 0 ? number_format($_POST['biz'.$i],2,'.','') : 0).",repostero=".($_POST['rep'.$i] > 0 ? number_format($_POST['rep'.$i],2,'.','') : 0).", piconero=".($_POST['pic'.$i] > 0 ? number_format($_POST['pic'.$i],2,'.','') : 0)." WHERE num_cia=".$_POST['num_cia'.$i]." and codmp=".$_POST['codmp'];
				ejecutar_script($sql,$dsn);
			}
			else{
				$sql="INSERT INTO catalogo_avio_autorizado(num_cia,codmp,frances_dia,frances_noche,bizcochero,repostero,piconero) VALUES(".$_POST['num_cia'.$i].", ".$_POST['codmp'].",".($_POST['fd'.$i] > 0 ? number_format($_POST['fd'.$i],2,'.','') : 0).",".($_POST['fn'.$i] > 0 ? number_format($_POST['fn'.$i],2,'.','') : 0).",".($_POST['biz'.$i] > 0 ? number_format($_POST['biz'.$i],2,'.','') : 0).",".($_POST['rep'.$i] > 0 ? number_format($_POST['rep'.$i],2,'.','') : 0).",".($_POST['pic'.$i] > 0 ? number_format($_POST['pic'.$i],2,'.','') : 0).")";
				ejecutar_script($sql,$dsn);
			}
		}
	}

//$db = new DBclass($dsn, $tabla, $datos);

//$db->xinsertar();
header("location: ./pan_av_aut.php");
?>