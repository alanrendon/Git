<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

$sql = "select id,num_cia,a_nombre,fecha,importe,folio from cheques where id not in (select min(id) from cheques group by num_cia,folio) and fecha='2005/02/09'  order by num_proveedor,num_cia";
$cheque = ejecutar_script($sql,$dsn);

for ($i=0; $i<count($cheque); $i++) {
	// Obtener ultimo folio
	$sql = "select folio from folios_cheque where num_cia=".$cheque[$i]['num_cia']." order by folio desc limit 1";
	$temp = ejecutar_script($sql,$dsn);
	$folio = ($temp)?$temp[0]['folio']+1:1;
	
	// Actualizar cheques
	$sql = "update cheques set folio=$folio,imp='FALSE' where id=".$cheque[$i]['id'];
	ejecutar_script($sql,$dsn);
	// Actualizar estado de cuenta
	$sql = "update estado_cuenta set folio=$folio where num_cia=".$cheque[$i]['num_cia']." and fecha='".$cheque[$i]['fecha']."' and folio=".$cheque[$i]['folio']." and importe=".$cheque[$i]['importe'];
	ejecutar_script($sql,$dsn);
	// Actualizar gastos
	$sql = "update movimiento_gastos set concepto='CHEQUE $folio' where num_cia=".$cheque[$i]['num_cia']." and fecha='".$cheque[$i]['fecha']."' and concepto like '%$folio%' and importe=".$cheque[$i]['importe'];
	ejecutar_script($sql,$dsn);
	// Insertar número de cheque en la base
	$sql = "insert into folios_cheque (folio,num_cia,reservado) values ($folio,".$cheque[$i]['num_cia'].",'FALSE')";
	ejecutar_script($sql,$dsn);
}

?>