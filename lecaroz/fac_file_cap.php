<?php
include 'DB.php';

$user = "root";
$pass = "pobgnj";
$host = "127.0.0.1";
$db_name = "lecaroz";
$dsn = "pgsql://$user:$pass@$host:5432/$db_name";

$users = array(28, 29, 30, 31, 32);

$db = DB::connect($dsn);
if (DB::isError($db)) {
	die($db->getUserInfo());
}

$diasxmes[1]=31;
if ($_GET['anio']%4 == 0)
	$diasxmes[2] = 29; // Febrero año bisiesto
else
	$diasxmes[2] = 28; // Febrero
$diasxmes[3] = 31; // Marzo
$diasxmes[4] = 30; // Abril
$diasxmes[5] = 31; // Mayo
$diasxmes[6] = 30; // Junio
$diasxmes[7] = 31; // Julio
$diasxmes[8] = 31; // Agosto
$diasxmes[9] = 30; // Septiembre
$diasxmes[10] = 31; // Octubre
$diasxmes[11] = 30; // Noviembre
$diasxmes[12] = 31; // Diciembre


$fecha_inicio=$_GET['anio']."/".$_GET['mes']."/1";

$fecha_final=$_GET['anio']."/".$_GET['mes']."/".$diasxmes[$_GET['mes']];

/*$sql="
SELECT 
facturas.num_cia, 
catalogo_companias.nombre_corto, 
facturas.num_fact, 
facturas.num_proveedor, 
catalogo_proveedores.nombre,
upper(catalogo_proveedores.rfc) as rfc,
facturas.imp_sin_iva,
facturas.importe_total, 
facturas.importe_iva, 
facturas.porciento_iva,
(facturas.imp_sin_iva * facturas.porciento_ret_iva)/100 as iva_retenido,
(facturas.imp_sin_iva * facturas.porciento_ret_isr)/100 as isr_retenido,
facturas.fecha_mov, 
facturas_pagadas.folio_cheque,
facturas.concepto,
estado_cuenta.fecha_con 
FROM 
facturas 
JOIN catalogo_companias using(num_cia)
JOIN catalogo_proveedores on(facturas.num_proveedor=catalogo_proveedores.num_proveedor)
LEFT JOIN facturas_pagadas on(facturas.num_fact=facturas_pagadas.num_fact and facturas.num_proveedor=facturas_pagadas.num_proveedor) 
LEFT JOIN estado_cuenta ON(facturas_pagadas.folio_cheque=estado_cuenta.folio and facturas.num_cia=estado_cuenta.num_cia and facturas.importe_total=estado_cuenta.importe)
WHERE 
facturas.fecha_mov between '$fecha_inicio' AND '$fecha_final' 
ORDER BY facturas.num_cia, facturas.num_proveedor, facturas.fecha_mov 
";*/

if ($_GET['tipo'] == 1) {
	if ($_GET['cia'] == 1) {
		$sql = "SELECT f.num_cia, cc.nombre_corto, f.num_fact, f.num_proveedor, cp.nombre, upper(cp.rfc) AS rfc, f.imp_sin_iva, f.importe_total, f.importe_iva, f.porciento_iva, (f.imp_sin_iva * f.porciento_ret_iva) / 100 AS iva_retenido, (f.imp_sin_iva * f.porciento_ret_isr) / 100 AS isr_retenido, f.fecha_mov, fp.folio_cheque, ec.fecha_con, f.concepto FROM facturas AS f LEFT JOIN facturas_pagadas AS fp USING (num_cia, num_proveedor, num_fact) LEFT JOIN estado_cuenta AS ec ON (ec.num_cia = fp.num_cia AND ec.folio = fp.folio_cheque AND ec.cuenta = fp.cuenta) LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = f.num_cia) WHERE f.fecha_mov BETWEEN '$fecha_inicio' AND '$fecha_final'" . ($_GET['contador'] > 0 ? " AND idcontador = $_GET[contador]" : '') . " ORDER BY f.num_cia, f.num_proveedor, f.fecha_mov";
	}
	else {
		$sql = "SELECT f.num_cia,cc.nombre_corto, f.num_fact, f.num_proveedor, cp.nombre, upper(cp.rfc) AS rfc, f.importe, total AS importe_total, iva AS importe_iva,  iva AS importe_iva, CASE WHEN iva > 0 THEN 15 ELSE 0 END AS porciento_iva, ivaret AS iva_retenido, isr AS isr_retenido, f.fecha AS fecha_mov, folio AS folio_cheque, ec.fecha_con, f.concepto FROM facturas_zap AS f LEFT JOIN estado_cuenta AS ec USING (num_cia, folio, cuenta) LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = f.num_cia) WHERE clave = 0 AND f.fecha BETWEEN '$fecha_inicio' AND '$fecha_final'" . ($_GET['contador'] > 0 ? " AND idcontador = $_GET[contador]" : '') . " ORDER BY f.num_cia, f.num_proveedor, f.fecha";
	}
}
else if ($_GET['tipo'] == 2) {
	if ($_GET['cia'] == 1) {
		$sql = "SELECT f.num_cia, cc.nombre_corto, f.num_fact, f.num_proveedor, cp.nombre, upper(cp.rfc) AS rfc, f.imp_sin_iva, f.importe_total, f.importe_iva, f.porciento_iva, (f.imp_sin_iva * f.porciento_ret_iva) / 100 AS iva_retenido, (f.imp_sin_iva * f.porciento_ret_isr) / 100 AS isr_retenido, f.fecha_mov, f.concepto FROM facturas AS f LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = f.num_cia) WHERE (f.num_cia, f.num_proveedor, f.num_fact) IN (SELECT num_cia, num_proveedor, num_fact FROM historico_proveedores WHERE fecha_arc = '$fecha_final')" . ($_GET['contador'] > 0 ? " AND idcontador = $_GET[contador]" : '') . " ORDER BY f.num_cia, f.num_proveedor, f.fecha_mov";
	}
	else {
		$sql = "SELECT f.num_cia,cc.nombre_corto, f.num_fact, f.num_proveedor, cp.nombre, upper(cp.rfc) AS rfc, f.importe, total AS importe_total, iva AS importe_iva, CASE WHEN iva > 0 THEN 15 ELSE 0 END AS porciento_iva, ivaret AS iva_retenido, isr AS isr_retenido, f.fecha AS fecha_mov, folio AS folio_cheque, ec.fecha_con, f.concepto FROM facturas_zap AS f LEFT JOIN estado_cuenta AS ec USING (num_cia, folio, cuenta) LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = f.num_cia) WHERE clave = 0 AND f.fecha <= '$fecha_final' AND (ec.fecha > '$fecha_final' OR folio IS NULL)" . ($_GET['contador'] > 0 ? " AND idcontador = $_GET[contador]" : '') . " ORDER BY f.num_cia, f.num_proveedor, f.fecha";
	}
}
else if ($_GET['tipo'] == 3) {
	if ($_GET['cia'] == 1) {
		$sql = "SELECT f.num_cia, cc.nombre_corto, f.num_fact, f.num_proveedor, cp.nombre, upper(cp.rfc) AS rfc, f.imp_sin_iva, f.importe_total, f.importe_iva, f.porciento_iva, (f.imp_sin_iva * f.porciento_ret_iva)";
		$sql .= " / 100 AS iva_retenido, (f.imp_sin_iva * f.porciento_ret_isr) / 100 AS isr_retenido, f.fecha_mov, f.concepto, fp.folio_cheque, ec.fecha_con FROM facturas AS f LEFT JOIN facturas_pagadas AS fp USING (num_cia, num_proveedor, num_fact) LEFT JOIN estado_cuenta AS ec ON (ec.num_cia = fp.num_cia AND ec.folio = fp.folio_cheque AND ec.cuenta = fp.cuenta) LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = f.num_cia) WHERE f.fecha_mov BETWEEN '$fecha_inicio' AND '$fecha_final'" . ($_GET['contador'] > 0 ? " AND idcontador = $_GET[contador]" : '');
		
		$sql .= ' UNION ';
		
		$sql .= "SELECT f.num_cia, cc.nombre_corto, f.num_fact, f.num_proveedor, cp.nombre, upper(cp.rfc) AS rfc, f.imp_sin_iva, f.importe_total, f.importe_iva, f.porciento_iva, (f.imp_sin_iva * f.porciento_ret_iva) / 100 AS iva_retenido, (f.imp_sin_iva * f.porciento_ret_isr) / 100 AS isr_retenido, f.fecha_mov, f.concepto, NULL AS folio_cheque, NULL AS fecha_con FROM facturas AS f LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = f.num_cia) WHERE (f.num_cia, f.num_proveedor, f.num_fact) IN (SELECT num_cia, num_proveedor, num_fact FROM historico_proveedores WHERE fecha_arc = '$fecha_final'" . ($_GET['contador'] > 0 ? " AND idcontador = $_GET[contador]" : '') . ") ORDER BY num_cia, num_proveedor, fecha_mov";
	}
	else {
		$sql = "SELECT f.num_cia,cc.nombre_corto, f.num_fact, f.num_proveedor, cp.nombre, upper(cp.rfc) AS rfc, f.importe, total AS importe_total, iva AS importe_iva,  iva AS importe_iva, CASE WHEN iva > 0 THEN 15 ELSE 0 END AS porciento_iva, ivaret AS iva_retenido, isr AS isr_retenido, f.fecha AS fecha_mov, folio AS folio_cheque, ec.fecha_con, f.concepto FROM facturas_zap AS f LEFT JOIN estado_cuenta AS ec USING (num_cia, folio, cuenta) LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = f.num_cia) WHERE clave = 0 AND f.fecha BETWEEN '$fecha_inicio' AND '$fecha_final'" . ($_GET['contador'] > 0 ? " AND idcontador = $_GET[contador]" : '') . " ORDER BY f.num_cia, f.num_proveedor, f.fecha";
	}
}

/*$sql = "SELECT f.num_cia, cc.nombre_corto, f.num_fact, f.num_proveedor, cp.nombre, upper(cp.rfc) AS rfc, f.imp_sin_iva, f.importe_total, f.importe_iva, f.porciento_iva, (f.imp_sin_iva * f.porciento_ret_iva)
 / 100 AS iva_retenido, (f.imp_sin_iva * f.porciento_ret_isr) / 100 AS isr_retenido, f.fecha_mov, f.concepto FROM facturas AS f LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = f.num_cia) WHERE (f.num_cia, f.num_proveedor, f.num_fact) IN (SELECT num_cia, num_proveedor, num_fact FROM historico_proveedores WHERE fecha_arc = '2006/12/31') ORDER BY f.num_cia, f.num_proveedor, f.fecha_mov";*/

$result = $db->query($sql);

if (DB::isError($result)) {
	$db->disconnect();
	die($result->getUserInfo());
}
$db->disconnect();

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=facturas.csv");

echo "Número de compañía,";
echo "Nombre cia,";
echo "Número de factura,";
echo "Número de proveedor,";
echo "Nombre proveedor,";
echo "R.F.C. proveedor,";
echo "Importe factura,";
echo "Importe I.V.A.,";
echo "Importe I.V.A. retenido,";
echo "Importe I.S.R. retenido,";
echo "Fecha del movimiento,";
if ($_GET['tipo'] == 1 || $_GET['tipo'] == 3) {
	echo "Folio de cheque,";
	echo "Fecha conciliación\n";
}
else
	echo "\n";
$espacio=" ";
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	echo "\"".$row->num_cia."\",";
	echo "\"".$row->nombre_corto."\",";
	echo "\"".$row->num_fact."\",";
	echo "\"".$row->num_proveedor."\",";
	echo "\"".$row->nombre."\",";
	echo "\"".$row->rfc."\",";
	echo "\"".(stristr($row->concepto, "ESPECIAL") !== FALSE ? ($row->importe_iva > 0 ? number_format($row->importe_total * 1.15, 2, ".", ",") : number_format($row->importe_total, 2, ".", ",")) : number_format($row->importe_total,2,'.',','))."\",";
	if(round($row->importe_iva, 2) /*!= ""*/ > 0) echo "\""./*number_format($row->importe_iva,4,'.',',')*/(stristr($row->concepto, "ESPECIAL") !== FALSE ? round($row->importe_total * 0.15, 2) : (stristr($row->concepto, "FACTURA MATERIA PRIMA") !== FALSE ? round($row->importe_iva / 1.15, 2) : round($row->importe_iva, 2)))."\",";
	else echo "\"".$row->importe_iva."\",";
	if($row->iva_retenido > 0) echo "\"".number_format($row->iva_retenido,4,'.',',')."\",";
	else echo "\"".$espacio."\",";
	if($row->isr_retenido > 0) echo "\"".number_format($row->isr_retenido,4,'.',',')."\",";
	else echo "\"".$espacio."\",";
	echo "\"".$row->fecha_mov."\"";
	if ($_GET['tipo'] == 1 || $_GET['tipo'] == 3) {
		echo ",\"".$row->folio_cheque."\",";
		echo "\"".$row->fecha_con."\"\n";
	}
	else
		echo "\n";
}
?>