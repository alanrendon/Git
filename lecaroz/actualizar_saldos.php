<?php
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

$cia = ejecutar_script("SELECT * FROM saldos WHERE cuenta = 1",$dsn);
$sql = "";
for ($i=0; $i<count($cia); $i++) {
	$cheques = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=".$cia[$i]['num_cia']." AND cuenta = 1 AND tipo_mov='TRUE' AND fecha_con IS NULL",$dsn);
	$depositos = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=".$cia[$i]['num_cia']." AND cuenta = 1 AND tipo_mov='FALSE' AND fecha_con IS NULL",$dsn);
	$sql .= "UPDATE saldos SET saldo_libros=saldo_bancos-".(($cheques[0]['sum'] > 0)?$cheques[0]['sum']:"0")."+".(($depositos[0]['sum'] > 0)?$depositos[0]['sum']:"0")." WHERE cuenta = 1 AND  num_cia = ".$cia[$i]['num_cia'] . ";\n";
}
echo "<pre>$sql</pre>";
ejecutar_script($sql,$dsn);
?>