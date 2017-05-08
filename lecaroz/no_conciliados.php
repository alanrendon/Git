<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$query = "";

// --------------------------------- Descripción de errores --------------------------------------------------
function buscar_mov($array, $num_cia, $tipo_mov) {
	if ($array === FALSE)
		return 0;
	
	for ($i = 0; $i < count($array); $i++)
		if ($array[$i]['num_cia'] == $num_cia && $array[$i]['tipo_mov'] == $tipo_mov)
			return number_format($array[$i]['sum'], 2, ".", "");
	
	return 0;
}

$cuenta = 2;
$dia = date("d");
$mes = date("n");
$anio = date("Y");

$tabla_saldo = $cuenta == 1 ? "saldo_banorte" : "saldo_santander";
$clabe_cuenta = $cuenta == 1 ? "clabe_cuenta" : "clabe_cuenta2";
$tabla_movs = $cuenta == 1 ? "mov_banorte" : "mov_santander";
$banco = $cuenta == 1 ? "BANORTE" : "SANTANDER";

$sql = "SELECT num_cia, nombre_corto, $clabe_cuenta, saldo_bancos, saldo FROM saldos LEFT JOIN $tabla_saldo USING (num_cia) LEFT JOIN catalogo_companias USING (num_cia)";
$sql .= " WHERE cuenta = $cuenta AND num_cia BETWEEN 1 AND 800 ORDER BY num_cia";
$result = $db->query($sql);

if ($result) {
	$cont = 0;
	
	$sql = "SELECT num_cia, tipo_mov, sum(importe) FROM $tabla_movs WHERE fecha_con IS NULL AND num_cia BETWEEN 1 AND 800 GROUP BY num_cia, tipo_mov ORDER BY num_cia, tipo_mov";
	$mov_pen = $db->query($sql);
	
	echo "<table border=1>";
	echo "<tr><th>CIA</th><th>SALDO CONCILIADO</th><th>DIFERENCIA</th><th>NO CONCILIADOS</th></tr>";
	foreach ($result as $saldo)
		if (round($saldo['saldo_bancos'] + buscar_mov($mov_pen, $saldo['num_cia'], 'f') - buscar_mov($mov_pen, $saldo['num_cia'], 't') - $saldo['saldo'], 2) != 0) {
			$pendientes = buscar_mov($mov_pen, $saldo['num_cia'], 'f') - buscar_mov($mov_pen, $saldo['num_cia'], 't');
			$saldo_final = $saldo['saldo_bancos'] + $pendientes;
			$dif = $saldo_final - $saldo['saldo'];
			
			$sql = "select tipo_mov, sum(importe) from estado_cuenta where num_cia = $saldo[num_cia] and fecha_con is null and fecha between '2006/08/16' and '2006/08/18'";
			$sql .= " and cod_mov in (1, 16, 13, 19, 7, 41) and cuenta = 2 group by tipo_mov";
			$tmp = $db->query($sql);
			$importe1 = 0;
			if ($tmp)
				foreach ($tmp as $r)
					$importe1 += $r['tipo_mov'] == "f" ? -$r['sum'] : $r['sum'];
			
			$query .= "UPDATE estado_cuenta SET fecha_con = '2006/08/18' WHERE num_cia = $saldo[num_cia] AND fecha_con IS NULL AND fecha BETWEEN '2006/08/16' AND '2006/08/18'";
			$query .= " AND cod_mov IN (1, 16, 13, 19, 7, 41) AND cuenta = 2;\n";
			
			$sql = "select sum(importe) from cheques where num_cia = $saldo[num_cia] and fecha in ('2006/08/04', '2006/08/11') and cuenta = 2 and codgastos = 134 and fecha_cancelacion is null";
			$tmp = $db->query($sql);
			$importe2 = $tmp ? $tmp[0]['sum'] : 0;
			
			$query .= "UPDATE estado_cuenta SET fecha_con = '2006/08/18' WHERE (num_cia, folio, cuenta) IN (SELECT num_cia, folio, cuenta FROM cheques WHERE num_cia = $saldo[num_cia]";
			$query .= " AND fecha IN ('2006/08/04', '2006/08/11') AND cuenta = 2 AND codgastos = 134 AND fecha_cancelacion IS NULL);\n";
			
			$sql = "select sum(importe) from estado_cuenta where num_cia = $saldo[num_cia] and fecha_con is null and fecha between '2006/08/10' and '2006/08/15'";
			$sql .= " and cod_mov in (41) and cuenta = 2";
			$tmp = $db->query($sql);
			$importe3 = $tmp ? $tmp[0]['sum'] : 0;
			
			$query .= "UPDATE estado_cuenta SET fecha_con = '2006/08/18' WHERE num_cia = $saldo[num_cia] AND fecha_con IS NULL AND fecha BETWEEN '2006/08/10' AND '2006/08/15'";
			$query .= " AND cod_mov IN (41) AND cuenta = 2;\n";
			
			$no_con = $importe1 + $importe2 + $importe3;
			
			$query .= "UPDATE saldos SET saldo_bancos = saldo_bancos - " . number_format($no_con, 2, ".", "") . " WHERE num_cia = $saldo[num_cia] AND cuenta = 2;\n";
			
			if (round($dif, 2) != round($no_con, 2))
				echo "<tr><td>$saldo[num_cia]</td><td>" . number_format($saldo_final, 2, ".", ",") . "</td><td>" . number_format($dif, 2, ".", ",") . "</td><td>" . number_format($no_con, 2, ".", ",") . "</td></tr>";
		}
	echo "</table>";
	//echo "<pre>$query</pre>";
	$db->query($query);
}
?>