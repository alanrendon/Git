<?php 
$url[0] = "../";
require_once "../class/balance_general.class.php";

if(isset($_POST['inicio']) && isset($_POST['fin'])){
    $balance = new Balance();
    $mes = date('m');
    $anio = date('Y');
    $periodo=array();
    $periodo[0] = $_POST['periodo0'];
    $periodo[1] = $_POST['periodo1'];
    //print_r($periodo);
    $ctas = $balance->get_descripcion_ctas((int)$_POST['inicio'],(int)$_POST['fin'],$periodo);
    echo json_encode($ctas);
}


?>