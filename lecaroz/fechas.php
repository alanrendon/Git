<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);

$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "No hay registros";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";

$tpl = new TemplatePower( "./plantillas/header.tpl" );

$tpl->assignInclude("body","./fechas.tpl");
$tpl->prepare();


$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");



function fecha_insercion($num_cia, $dsn)
{

$num_cia=101;
$sql="SELECT * FROM compra_directa WHERE num_cia='".$num_cia."' order by fecha_mov";
$cias = ejecutar_script($sql,$dsn);
$i=count($cias);
$fecha_trabajo=$cias[$i-1]['fecha_mov'];
echo $fecha_trabajo;
$_dt=explode("/",$fecha_trabajo);
$d2 = $_dt[0];
$m2 = $_dt[1];
$y2 = $_dt[2];
$d2 =$d2+1;
$fecha=date( "d/m/Y", mktime(0,0,0,$m2,$d2,$y2) );
return $fecha; 
}

$tpl->newBlock("fechas");//se va a quitar
$fec=fecha_insercion("101",$dsn);
$tpl->assign("fecha_a_trabajar", $fec);
//$tpl->assign("fecha_a_trabajar",$fecha);

function date_diff($start_date, $end_date, $returntype="d")
{
   if ($returntype == "s")
       $calc = 1;
   if ($returntype == "m")
       $calc = 60;
   if ($returntype == "h")
       $calc = (60*60);
   if ($returntype == "d")
       $calc = (60*60*24);
      
   $_d1 = explode("-", $start_date);
   $y1 = $_d1[0];
   $m1 = $_d1[1];
   $d1 = $_d1[2];
  
   $_d2 = explode("-", $end_date);
   $y2 = $_d2[0];
   $m2 = $_d2[1];
   $d2 = $_d2[2];
  
   if (($y1 < 1970 || $y1 > 2037) || ($y2 < 1970 || $y2 > 2037))
   {
       return 0;
   } else
   {
       $today_stamp    = mktime(0,0,0,$m1,$d1,$y1);
       $end_date_stamp    = mktime(0,0,0,$m2,$d2,$y2);
       $difference    = round(($end_date_stamp-$today_stamp)/$calc);
       return $difference;
   }
}
//echo "<br>";
//echo date_diff("2004-06-6", "2004-07-07", "m");






$tpl->printToScreen();
?>