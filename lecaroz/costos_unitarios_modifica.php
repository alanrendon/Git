<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
$session = new sessionclass($dsn);
//print_r ($_POST);
$sql="update inventario_real set precio_unidad =".$_GET['importe']." where num_cia=".$_GET['num_cia']." and codmp=".$_GET['codmp'];
//ejecutar_script($sql,$dsn);

$sql="update inventario_virtual set precio_unidad =".$_GET['importe']." where num_cia=".$_GET['num_cia']." and codmp=".$_GET['codmp'];
//ejecutar_script($sql,$dsn);

$sql="update historico_inventario set precio_unidad =".$_GET['importe']." where num_cia=".$_GET['num_cia']." and codmp=".$_GET['codmp']." and fecha='2005-03-31'";
//ejecutar_script($sql,$dsn);

echo "modifique materia prima ".$_GET['codmp']."<br> y cia ".$_GET['num_cia'];

?>