<?php
include("includes/class.TemplatePower.inc.php");
include("includes/dbstatus.php");
require_once("DB.php");
//carga de la plantilla
$tpl = new TemplatePower("./plantillas/header.tpl");
$tpl->assignInclude("body", "./plantillas/consult_pan_mga_cap.tpl");
$tpl->prepare();
//se ejecuta el query
$db = DB::connect($dsn);
$sql="SELECT fecha,
movimiento_gastos.codgastos as cod,
catalogo_gastos.descripcion as descripcion,
concepto,
importe
FROM  movimiento_gastos,
catalogo_gastos
WHERE
 catalogo_gastos.codgastos=movimiento_gastos.codgastos order by cod ;
";
$result = $db->query($sql);

if(DB::isError($result))
{
    echo $result->getMessage();
	exit;
}
//se asigna el resultset
$cambio="algo";
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	
	
	$tpl->newBlock("datos");
	if($cambio != $row->descripcion){
	if($row->cod==1){}
		else{
			
		
			$tpl->newBlock("total");
			$tpl->assign("total", number_format($resultado,2,'.',','));
			$tpl->gotoBlock("datos");
			$resultado=0;
			}
	}
	
	
	
	
	
	$tpl->assign("descripcion",$row->descripcion);
	$tpl->assign("cod",$row->cod);
	$tpl->assign("fecha",$row->fecha);
	$tpl->assign("concepto",$row->concepto);
	$tpl->assign("importe",number_format($row->importe,2,'.',','));
	
	
	$cambio= $row->descripcion;
	$resultado+=$row->importe;
}
$tpl->printToScreen();
$db->disconnect();
echo $resultado;
?>
