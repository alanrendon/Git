<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

if ( ! isset($_REQUEST['fecha1']) || ! isset($_REQUEST['fecha2']))
{
	if(!$argv[1]){
	echo "No especifico el periodo de consulta";
	$Fecha = date("Y-m-d");
	$_REQUEST['fecha1']=$Fecha;
	$_REQUEST['fecha2']=$Fecha;
	}
	else
	{
		$_REQUEST['fecha1']=$argv[1];
		$_REQUEST['fecha2']=$argv[2];
	}
}


$condiciones = array();

$condiciones[] = "f.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";

if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
	$cias = array();

	$pieces = explode(',', $_REQUEST['cias']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$cias[] =  implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$cias[] = $piece;
		}
	}

	if (count($cias) > 0) {
		$condiciones[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
	}
}

if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
	$pros = array();

	$pieces = explode(',', $_REQUEST['pros']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$pros[] =  implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$pros[] = $piece;
		}
	}

	if (count($pros) > 0) {
		$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
	}
}

$sql = "
	SELECT
		num_cia,
		num_proveedor
			AS num_pro,
		cp.nombre
			AS nombre_pro,
		cp.rfc
			AS rfc_pro,
		f.num_fact,
		f.fecha,
		cp.calle,
		cp.no_exterior,
		cp.no_interior,
		cp.colonia,
		cp.localidad,
		cp.referencia,
		cp.municipio,
		cp.estado,
		cp.pais,
		cp.codigo_postal,
		cp.email1
			AS email,
		COALESCE(cmp.nombre, f.concepto)
			AS descripcion,
		COALESCE(d.precio, f.importe)
			AS precio,
		COALESCE(d.cantidad, 1)
			AS cantidad,
		COALESCE(d.piva, f.piva)
			AS iva,
		CASE
			WHEN d.codmp IS NOT NULL THEN
				'C'
			WHEN f.codgastos IS NOT NULL THEN
				'G'
		END
			AS tipo_registro,
		CASE
			WHEN d.codmp IS NOT NULL THEN
				'MP'
			WHEN f.codgastos IS NOT NULL THEN
				'G'
		END
			AS tipo_producto,
		COALESCE(d.codmp, f.codgastos)
			AS codigo,
		COALESCE(d.ieps)
			AS ieps
	FROM
		facturas f
		LEFT JOIN entrada_mp d
			USING (num_cia, num_proveedor, num_fact)
		LEFT JOIN catalogo_mat_primas cmp
			USING (codmp)
		LEFT JOIN catalogo_proveedores cp
			USING (num_proveedor)
	WHERE
		" . implode(' AND ', $condiciones) . "
	ORDER BY
		f.fecha,
		num_cia,
		num_pro,
		num_fact,
		COALESCE(d.ieps, 0) DESC,
		f.id
";

$db = new DBclass($dsn, 'autocommit=yes');

$datos_fac = $db->query($sql);

if ( ! $datos_fac)
{
	echo "No hay datos";
	die;
}

$num_cia = NULL;

$header = "INSERT INTO i_invoice ( i_invoice_id, ad_client_id, ad_org_id, createdby, updatedby, c_currency_id, bpartnervalue, name, postal, regionname, c_region_id, c_country_id, email, contactname, phone, doctypename, documentno, productvalue, linedescription, priceactual, dateinvoiced, qtyinvoiced, calle, noexterior, nointerior, colonia, localidad, referencia, municipio, bpartner_rfc, description, paymentrule, numctapago, paymenttermvalue, aduana, pedimento, fecha_pedimento, facturaefilename, isready, uomname, issotrx, poreference) VALUES";

$dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

echo count($datos_fac);
$numregistros = 0;
$totalregistros =count($datos_fac);
$porcentaje100=$totalregistros;
$tiempo=650000;
$cuantos=0;

foreach ($datos_fac as $i => $df)
{
	$sqlcuantos = "
		SELECT
			*
		FROM
			i_invoice
		WHERE
			issotrx = 'N'
			AND dateinvoiced > '2014-04-27 00:00:00'
			AND i_isimported != 'Y'
		ORDER BY
			created
	";

	$cuantos = $dbf->query($sqlcuantos);

	while (count($cuantos) > 10){
		sleep(2);
		$cuantos= $dbf->query($sqlcuantos);
	}

	if ($numregistros % 50 == 0 && $numregistros > 2)
	{
		echo " Vamos a Descansar 20 segundos \n";
		sleep(180);
	}

	if ($numregistros % 200 == 0 && $numregistros > 100)
	{
		echo " Vamos a Descansar 180 segundos";
		sleep(180);
	}

	if ($numregistros % 1000 == 0 && $numregistros > 100)
	{
		echo " Vamos a Descansar 80 segundos";
		sleep(80);
	}

	$numregistros++;
	$totalregistros--;
	$tiempotermino = ($totalregistros * ($tiempo / 1000000)) / 60;
	$tiempotermino = round($tiempotermino, 2);
	$porcentajefaltante = ($numregistros / $porcentaje100) * 100;
	$porcentajefaltante = round($porcentajefaltante, 2);

	//echo"\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	//echo "Fecha a Procesar: '{$tmp['fecha']}' \nProcesando Registro: $numregistros\n FALTAN: --- $totalregistros\n Tiempo Para Termino= $tiempotermino Minutos Aproximados\n Porcentaje Terminado = %$porcentajefaltante";
	//echo"\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";

	if ($num_cia != $df['num_cia'])
	{
		if ($num_cia != NULL && $ieps > 0)
		{
			// $sql = $header . "\n(COALESCE((SELECT MAX(i_invoice_id) FROM i_invoice), 0) + 1, 1000000, {$tmp['num_cia']}, 1, 1, 130, '{$tmp['rfc_pro']}', '{$tmp['nombre_pro']}', '{$tmp['codigo_postal']}', '{$tmp['estado']}', NULL, 247, '{$tmp['email']}', '{$tmp['nombre_pro']}', NULL, '{$tmp['tipo']}-{$tmp['num_cia']}', '{$tmp['num_fact']}', 'IEPSC', 'I.E.P.S.', {$ieps}, '{$tmp['fecha']}', '1', '{$tmp['calle']}', '{$tmp['no_exterior']}', '{$tmp['no_interior']}', '{$tmp['colonia']}', '{$tmp['localidad']}', '{$tmp['referencia']}', '{$tmp['municipio']}', '{$tmp['rfc_pro']}', NULL, '4', NULL, 0, NULL, NULL, NULL, NULL, 'Y', NULL, 'N', '{$tmp['num_fact']}')";

			$sql = $header . "\n(COALESCE((SELECT MAX(i_invoice_id) FROM i_invoice), 0) + 2, 1000000, {$tmp['num_cia']}, 1, 1, 130, '{$tmp['rfc_pro']}', '{$tmp['nombre_pro']}', '{$tmp['codigo_postal']}', 'DISTRITO FEDERAL', NULL, 247, '{$tmp['email']}', '{$tmp['nombre_pro']}', NULL, '{$tmp['tipo_registro']}-{$tmp['num_cia']}', '{$tmp['num_fact']}', 'IEPSC', 'I.E.P.S.', {$ieps}, '{$tmp['fecha']}', '1', '', '', '', '', '', '', '', '{$tmp['rfc_pro']}', NULL, '4', NULL, 0, NULL, NULL, NULL, NULL, 'Y', NULL, 'N', '{$tmp['num_fact']}')";

			$dbf->query($sql);
			//usleep($tiempo);
		}

		$num_cia = $df['num_cia'];

		$num_pro = NULL;
	}

	if ($num_pro != $df['num_pro'])
	{
		if ($num_pro != NULL && $ieps > 0)
		{
			// $sql = $header . "\n(COALESCE((SELECT MAX(i_invoice_id) FROM i_invoice), 0) + 1, 1000000, {$tmp['num_cia']}, 1, 1, 130, '{$tmp['rfc_pro']}', '{$tmp['nombre_pro']}', '{$tmp['codigo_postal']}', '{$tmp['estado']}', NULL, 247, '{$tmp['email']}', '{$tmp['nombre_pro']}', NULL, '{$tmp['tipo']}-{$tmp['num_cia']}', '{$tmp['num_fact']}', 'IEPSC', 'I.E.P.S.', {$ieps}, '{$tmp['fecha']}', '1', '{$tmp['calle']}', '{$tmp['no_exterior']}', '{$tmp['no_interior']}', '{$tmp['colonia']}', '{$tmp['localidad']}', '{$tmp['referencia']}', '{$tmp['municipio']}', '{$tmp['rfc_pro']}', NULL, '4', NULL, 0, NULL, NULL, NULL, NULL, 'Y', NULL, 'N', '{$tmp['num_fact']}')";

			$sql = $header . "\n(COALESCE((SELECT MAX(i_invoice_id) FROM i_invoice), 0) + 2, 1000000, {$tmp['num_cia']}, 1, 1, 130, '{$tmp['rfc_pro']}', '{$tmp['nombre_pro']}', '{$tmp['codigo_postal']}', 'DISTRITO FEDERAL', NULL, 247, '{$tmp['email']}', '{$tmp['nombre_pro']}', NULL, '{$tmp['tipo_registro']}-{$tmp['num_cia']}', '{$tmp['num_fact']}', 'IEPSC', 'I.E.P.S.', {$ieps}, '{$tmp['fecha']}', '1', '', '', '', '', '', '', '', '{$tmp['rfc_pro']}', NULL, '4', NULL, 0, NULL, NULL, NULL, NULL, 'Y', NULL, 'N', '{$tmp['num_fact']}')";

			$dbf->query($sql);
			//usleep($tiempo);
		}

		$num_pro = $df['num_pro'];

		$num_fact = NULL;
	}

	if ($num_fact != $df['num_fact'])
	{
		if ($num_fact != NULL && $ieps > 0)
		{
			// $sql = $header . "\n(COALESCE((SELECT MAX(i_invoice_id) FROM i_invoice), 0) + 1, 1000000, {$tmp['num_cia']}, 1, 1, 130, '{$tmp['rfc_pro']}', '{$tmp['nombre_pro']}', '{$tmp['codigo_postal']}', '{$tmp['estado']}', NULL, 247, '{$tmp['email']}', '{$tmp['nombre_pro']}', NULL, '{$tmp['tipo']}-{$tmp['num_cia']}', '{$tmp['num_fact']}', 'IEPSC', 'I.E.P.S.', {$ieps}, '{$tmp['fecha']}', '1', '{$tmp['calle']}', '{$tmp['no_exterior']}', '{$tmp['no_interior']}', '{$tmp['colonia']}', '{$tmp['localidad']}', '{$tmp['referencia']}', '{$tmp['municipio']}', '{$tmp['rfc_pro']}', NULL, '4', NULL, 0, NULL, NULL, NULL, NULL, 'Y', NULL, 'N', '{$tmp['num_fact']}')";

			$sql = $header . "\n(COALESCE((SELECT MAX(i_invoice_id) FROM i_invoice), 0) + 2, 1000000, {$tmp['num_cia']}, 1, 1, 130, '{$tmp['rfc_pro']}', '{$tmp['nombre_pro']}', '{$tmp['codigo_postal']}', 'DISTRITO FEDERAL', NULL, 247, '{$tmp['email']}', '{$tmp['nombre_pro']}', NULL, '{$tmp['tipo_registro']}-{$tmp['num_cia']}', '{$tmp['num_fact']}', 'IEPSC', 'I.E.P.S.', {$ieps}, '{$tmp['fecha']}', '1', '', '', '', '', '', '', '', '{$tmp['rfc_pro']}', NULL, '4', NULL, 0, NULL, NULL, NULL, NULL, 'Y', NULL, 'N', '{$tmp['num_fact']}')";

			$dbf->query($sql);
			//usleep($tiempo);
		}

		$num_fact = $df['num_fact'];

		$tmp = $df;

		$ieps = 0;
	}

	if ($df['ieps'] > 0)
	{
		$ieps += $df['ieps'];
	}

	// $sql = $header . "\n(COALESCE((SELECT MAX(i_invoice_id) FROM i_invoice), 0) + 1, 1000000, {$df['num_cia']}, 1, 1, 130, '{$df['rfc_pro']}', '{$df['nombre_pro']}', '{$df['codigo_postal']}', '{$df['estado']}', NULL, 247, '{$df['email']}', '{$df['nombre_pro']}', NULL, '{$df['tipo']}-{$df['num_cia']}', '{$df['num_fact']}', '{$df['tipo']}-{$df['codigo']}', '{$df['descripcion']}', {$df['precio']}, '{$df['fecha']}', '{$df['cantidad']}', '{$df['calle']}', '{$df['no_exterior']}', '{$df['no_interior']}', '{$df['colonia']}', '{$df['localidad']}', '{$df['referencia']}', '{$df['municipio']}', '{$df['rfc_pro']}', NULL, '4', NULL, 0, NULL, NULL, NULL, NULL, " . (((isset($datos_fac[$i + 1]) && ($num_cia != $datos_fac[$i + 1]['num_cia'] || $num_pro != $datos_fac[$i + 1]['num_pro'] || $num_fact != $datos_fac[$i + 1]['num_fact'])) || ! isset($datos_fac[$i + 1])) && $ieps == 0 ? "'Y'" : "'N'") . ", NULL, 'N', '{$df['num_fact']}')";

	$sql = $header . "\n(COALESCE((SELECT MAX(i_invoice_id) FROM i_invoice), 0) + 2, 1000000, {$df['num_cia']}, 1, 1, 130, '{$df['rfc_pro']}', '{$df['nombre_pro']}', '{$df['codigo_postal']}', 'DISTRITO FEDERAL', NULL, 247, '{$df['email']}', '{$df['nombre_pro']}', NULL, '{$df['tipo_registro']}-{$df['num_cia']}', '{$df['num_fact']}', '{$df['tipo_producto']}{$df['codigo']}', '{$df['descripcion']}', {$df['precio']}, '{$df['fecha']}', '{$df['cantidad']}', '', '', '', '', '', '', '', '{$df['rfc_pro']}', NULL, '4', NULL, 0, NULL, NULL, NULL, NULL, " . (((isset($datos_fac[$i + 1]) && ($num_cia != $datos_fac[$i + 1]['num_cia'] || $num_pro != $datos_fac[$i + 1]['num_pro'] || $num_fact != $datos_fac[$i + 1]['num_fact'])) || ! isset($datos_fac[$i + 1])) && $ieps == 0 ? "'Y'" : "'N'") . ", NULL, 'N', '{$df['num_fact']}')";

	$dbf->query($sql);
	//usleep($tiempo);
}

// $sql .= implode(', ', $values) . ";\n";

// header('Content-Type: application/download; charset=utf-8');
// header('Content-Disposition: attachment; filename=datos.sql');

// echo $sql;
