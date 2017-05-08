<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$meses1 = 10;
$meses2 = 11;

$sql = "SELECT num_cia, sum(importe) AS importe FROM pagos_imss WHERE anio = 2006 GROUP BY num_cia";
$sql .= " UNION ";
$sql .= "SELECT num_cia, sum(importe) AS importe FROM cheques WHERE fecha BETWEEN '2006/01/01' AND '2006/10/31' AND codgastos = 141 AND importe > 0 AND fecha_cancelacion IS NULL AND num_cia NOT IN";
$sql .= " (SELECT num_cia FROM catalogo_filiales_imss) AND num_cia IN (SELECT num_cia FROM balances_pan WHERE num_cia < 800 AND anio = 2006 GROUP BY num_cia) GROUP BY num_cia";

$result = $db->query($sql);

$sql = "";
foreach ($result as $reg) {
	$importe_mes = round($reg['importe'] / $meses1, -2);
	
	for ($i = 1; $i <= $meses2; $i++) {
		$tmp = $db->query("SELECT importe FROM reservas_cias WHERE num_cia = $reg[num_cia] AND cod_reserva = 4 AND fecha = '01/$i/2006'");
		$sql .= "UPDATE balances_pan SET utilidad_neta = utilidad_neta + {$tmp[0]['importe']}, total_gastos = total_gastos + {$tmp[0]['importe']}, reserva_aguinaldos = reserva_aguinaldos";
		$sql .= " + {$tmp[0]['importe']} WHERE num_cia = $reg[num_cia] AND mes = $i AND anio = 2006;\n";
		$sql .= "UPDATE balances_pan SET utilidad_neta = utilidad_neta - $importe_mes, total_gastos = total_gastos - $importe_mes, reserva_aguinaldos = reserva_aguinaldos";
		$sql .= " - $importe_mes WHERE num_cia = $reg[num_cia] AND mes = $i AND anio = 2006;\n";
		$sql .= "UPDATE reservas_cias SET importe = $importe_mes WHERE num_cia = $reg[num_cia] AND cod_reserva = 4 AND fecha = '01/$i/2006';\n";
	}
}

echo "<pre>$sql</pre>";
$db->query($sql);
?>