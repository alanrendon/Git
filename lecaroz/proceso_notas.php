<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu

//define ('IDSCREEN',1620); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db=new DBclass($dsn,"autocommit=yes");

$sql="select * from venta_pastel where tipo=0 and estado is null order by num_cia, letra_folio, num_remi";
$amarillos=$db->query($sql);

$sql="select * from venta_pastel where tipo=1 order by num_cia, letra_folio, num_remi";
$verdes=$db->query($sql);

function busca_indice($tabla, $num_cia,$letra,$remi){
	for($i=0;$i<count($tabla);$i++)
		if($tabla[$i]['num_cia']==$num_cia and $tabla[$i]['letra_folio']==$letra and $tabla[$i]['num_remi']==$remi)
			return $i;
	return FALSE;
}


for($i=0;$i<count($amarillos);$i++){
	$indice=busca_indice($verdes,$amarillos[$i]['num_cia'],$amarillos[$i]['letra_folio'],$amarillos[$i]['num_remi']);
	
	if($indice !== FALSE){
		if($amarillos[$i]['resta_pagar'] == $verdes[$indice]['resta']){
			$db->query("update venta_pastel set estado=1 where id = ".$amarillos[$i]['id']);
			echo "compañia ".$amarillos[$i]['num_cia']." nota: ".$amarillos[$i]['letra_folio']." ".$amarillos[$i]['num_remi']." <br>";
		}
	}
	
}


?>