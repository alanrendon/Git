<?php
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);

$sql1="SELECT p.num_cia, p.mes, p.anio_anterior, s.anio_actual, s.ventas, p.fecha_anio_anterior, p.fecha_anio_actual FROM historico as p, historico as s WHERE p.num_cia=183 AND s.num_cia=183 AND p.num_cia=s.num_cia AND p.mes=s.mes and p.anio_actual=0 and p.ventas=0 and (s.anio_anterior = 0 and p.anio_anterior<>0)order by mes";

$historico=ejecutar_script($sql1,$dsn);


for($i=0;$i<count($historico);$i++)
{
	$datos['num_cia'.$i]=$historico[$i]['num_cia'];
	$datos['mes'.$i]=$historico[$i]['mes'];
	$datos['anio_anterior'.$i]=$historico[$i]['anio_anterior'];
	$datos['anio_actual'.$i]=$historico[$i]['anio_actual'];
	$datos['ventas'.$i]=$historico[$i]['ventas'];
	$datos['fecha_anio_anterior'.$i]=$historico[$i]['fecha_anio_anterior'];
	$datos['fecha_anio_actual'.$i]=$historico[$i]['fecha_anio_actual'];
}

$dbhistorico= new Dbclass($dsn,"historico",$datos);

$dbhistorico->xinsertar();

print_r ($historico);
?>
