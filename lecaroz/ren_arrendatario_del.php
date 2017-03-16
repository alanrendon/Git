<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
 
$sql="select * from catalogo_arrendatarios where num_arrendatario = ".$_GET['num_arrendatario'];
$arrendatario = ejecutar_script($sql,$dsn);

$sql="delete from recibos_rentas where num_arrendatario=".$_GET['num_arrendatario'];
ejecutar_script($sql,$dsn);

$sql="delete from catalogo_arrendatarios where num_arrendatario=".$_GET['num_arrendatario'];
ejecutar_script($sql,$dsn);

$sql="select * from catalogo_locales where local = ".$arrendatario[0]['num_local'];
$local=ejecutar_script($sql,$dsn);

if( ($local[0]['locales_ocupados'] -1 ) < $local[0]['locales']){
	$sql="update catalogo_locales set ocupado=false, locales_ocupados = locales_ocupados - 1 where num_local=".$arrendatario[0]['num_local'];
	ejecutar_script($sql,$dsn);
}


header("location: ./ren_arrendatario_del.php");
die();
?>