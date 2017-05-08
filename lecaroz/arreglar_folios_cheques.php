<?php
include 'includes/class.db.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		num_cia,
		MAX(folio) + 1
			AS folio
	FROM
		folios_cheque
	WHERE
		(num_cia, cuenta) IN (
			SELECT
				num_cia,
				cuenta
			FROM
				cheques
			WHERE
				id IN (
					883070,
					883071,
					883160,
					883182,
					883266,
					883307,
					883308,
					883384,
					883438,
					883439,
					883473,
					883626,
					883670,
					883704,
					883705,
					883739,
					883740,
					883773,
					883774,
					883808,
					883815,
					883895,
					883896,
					883933,
					883934,
					883987,
					883988,
					883989,
					884009,
					884018,
					884023
				)
			GROUP BY
				num_cia,
				cuenta
		)
	GROUP BY
		num_cia
	ORDER BY
		num_cia
';

$query = $db->query($sql);

$folios = array();

foreach ($query as $row) {
	$folios[$row['num_cia']] = $row['folio'];
}

$sql = '
	SELECT
		id,
		num_cia,
		folio
	FROM
		cheques
	WHERE
		id IN (
			883070,
			883071,
			883160,
			883182,
			883266,
			883307,
			883308,
			883384,
			883438,
			883439,
			883473,
			883626,
			883670,
			883704,
			883705,
			883739,
			883740,
			883773,
			883774,
			883808,
			883815,
			883895,
			883896,
			883933,
			883934,
			883987,
			883988,
			883989,
			884009,
			884018,
			884023
		)
	ORDER BY
		num_cia,
		folio
';

$query = $db->query($sql);

$cheques = array();

foreach ($query as $row) {
	$cheques[$row['num_cia']][$row['folio']] = $row['id'];
}

$sql = '
	SELECT
		id,
		num_cia,
		folio
	FROM
		estado_cuenta
	WHERE
		id IN (
			2026633,
			2026634,
			2026723,
			2026745,
			2026829,
			2026870,
			2026871,
			2026947,
			2027001,
			2027002,
			2027036,
			2027189,
			2027233,
			2027267,
			2027268,
			2027302,
			2027303,
			2027336,
			2027337,
			2027371,
			2027378,
			2027458,
			2027459,
			2027496,
			2027497,
			2027550,
			2027551,
			2027552,
			2027572,
			2027581,
			2027586
		)
	ORDER BY
		num_cia,
		folio
';

$query = $db->query($sql);

$estado_cuenta = array();

foreach ($query as $row) {
	$estado_cuenta[$row['num_cia']][$row['folio']] = $row['id'];
}

$sql = '
	SELECT
		id,
		num_cia,
		folio
	FROM
		folios_cheque
	WHERE
		id IN (
			904734,
			904735,
			904824,
			904846,
			904930,
			904971,
			904972,
			905048,
			905102,
			905103,
			905137,
			905290,
			905334,
			905368,
			905369,
			905403,
			905404,
			905437,
			905438,
			905472,
			905479,
			905559,
			905560,
			905597,
			905598,
			905651,
			905652,
			905653,
			905673,
			905682,
			905687
		)
	ORDER BY
		num_cia,
		folio
';

$query = $db->query($sql);

$folios_cheque = array();

foreach ($query as $row) {
	$folios_cheque[$row['num_cia']][$row['folio']] = $row['id'];
}

$sql = '
	SELECT
		idmovimiento_gastos
			AS id,
		num_cia,
		folio
	FROM
		movimiento_gastos
	WHERE
		idmovimiento_gastos IN (
			5758155,
			5758156,
			5758245,
			5758267,
			5758351,
			5758392,
			5758393,
			5758469,
			5758523,
			5758524,
			5758558,
			5758711,
			5758755,
			5758789,
			5758790,
			5758824,
			5758825,
			5758858,
			5758859,
			5758893,
			5758900,
			5758980,
			5758981,
			5759018,
			5759019,
			5759072,
			5759073,
			5759074,
			5759094,
			5759103,
			5759108
		)
	ORDER BY
		num_cia,
		folio
';

$query = $db->query($sql);

$movimiento_gastos = array();

foreach ($query as $row) {
	$movimiento_gastos[$row['num_cia']][$row['folio']] = $row['id'];
}

$sql = '
	SELECT
		id,
		num_cia,
		folio
	FROM
		transferencias_electronicas
	WHERE
		id IN (
			588609,
			587744,
			588275,
			588047,
			587852,
			587972,
			588559,
			587832,
			588411,
			588522,
			588558,
			587933,
			587745,
			588347,
			588442,
			588098,
			588132,
			588523,
			587973,
			588410,
			588644,
			588379,
			588099,
			588448,
			588317,
			588346,
			588608,
			588639,
			588378,
			588610,
			588630
		)
	ORDER BY
		num_cia,
		folio
';

$query = $db->query($sql);

$transferencias_electronicas = array();

foreach ($query as $row) {
	$transferencias_electronicas[$row['num_cia']][$row['folio']] = $row['id'];
}

$sql = '
	SELECT
		id,
		num_cia,
		folio_cheque
			AS folio
	FROM
		facturas_pagadas
	WHERE
		id IN (
			1136402,
			1135173,
			1135916,
			1135582,
			1135583,
			1135313,
			1135314,
			1135480,
			1135483,
			1135481,
			1135482,
			1136339,
			1136340,
			1135287,
			1136124,
			1136281,
			1136280,
			1136338,
			1135422,
			1135174,
			1136027,
			1136172,
			1135663,
			1135664,
			1135709,
			1136282,
			1135484,
			1136123,
			1136445,
			1136077,
			1135665,
			1136180,
			1135976,
			1136026,
			1136401,
			1136438,
			1136076,
			1136075,
			1136404,
			1136403,
			1136428
		)
	ORDER BY
		num_cia,
		folio
';

$query = $db->query($sql);

$facturas_pagadas = array();

foreach ($query as $row) {
	$facturas_pagadas[$row['num_cia']][$row['folio']][] = $row['id'];
}

$sql = '';

foreach ($cheques as $num_cia => $_folios) {
	foreach ($_folios as $folio => $id) {
		$sql .= 'UPDATE cheques SET folio = ' . $folios[$num_cia] . ' WHERE id = ' . $id . ";\n";
		$sql .= 'UPDATE estado_cuenta SET folio = ' . $folios[$num_cia] . ' WHERE id = ' . $estado_cuenta[$num_cia][$folio] . ";\n";
		$sql .= 'UPDATE movimiento_gastos SET folio = ' . $folios[$num_cia] . ' WHERE idmovimiento_gastos = ' . $movimiento_gastos[$num_cia][$folio] . ";\n";
		$sql .= 'UPDATE transferencias_electronicas SET folio = ' . $folios[$num_cia] . ' WHERE id = ' . $transferencias_electronicas[$num_cia][$folio] . ";\n";
		
		foreach ($facturas_pagadas[$num_cia][$folio] as $idfac) {
			$sql .= 'UPDATE facturas_pagadas SET folio_cheque = ' . $folios[$num_cia] . ' WHERE id = ' . $idfac . ";\n";
		}
		
		$sql .= 'INSERT INTO folios_cheque (num_cia, cuenta, folio, reservado, utilizado, fecha) VALUES (' . $num_cia . ', 1, ' . $folios[$num_cia] . ', FALSE, TRUE, NOW()::DATE)' . ";\n";
		
		$folios[$num_cia]++;
	}
}

echo "<pre>$sql</pre>";
