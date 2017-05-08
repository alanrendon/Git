<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$result = $db->query("select num_cia, sum(importe) from barredura where fecha_pago = '29/10/2006' and num_cia not in (33, 59) group by num_cia order by num_cia");

$sql = "";
foreach ($result as $reg)
	$sql .= "update total_panaderias set otros = otros - $reg[sum] * 3, efectivo = efectivo - $reg[sum] * 3 where num_cia = $reg[num_cia] and fecha = '29/10/2006';\n";

$db->query($sql);
?>