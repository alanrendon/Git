<?php

include(dirname(__FILE__) . '/includes/class.db.inc.php');
include(dirname(__FILE__) . '/includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$datos = array(
	array(1, 24000, 627),
	array(332, 8500, 627),
	array(3, 28000, 615),
	array(5, 28000, 604),
	array(338, 4000, 604),
	array(6, 3000, 640),
	array(7, 51000, 615),
	array(309, 8000, 615),
	array(8, 3000, 604),
	array(304, 13000, 604),
	array(9, 17000, 615),
	array(306, 7000, 615),
	array(10, 2000, 615),
	array(800, 0, 615),
	array(320, 4000, 615),
	array(11, 14000, 627),
	array(303, 7000, 627),
	array(12, 6000, 615),
	array(345, 9000, 615),
	array(14, 15000, 610),
	array(323, 9000, 610),
	array(354, 11500, 604),
	array(16, 1000, 604),
	array(17, 15000, 614),
	array(18, 10000, 606),
	array(305, 13000, 612),
	array(19, 14000, 612),
	array(428, 16000, 612),
	array(20, 0, 612),
	array(21, 32000, 603),
	array(334, 16000, 603),
	array(24, 16000, 614),
	array(302, 5000, 614),
	array(25, 16000, 608),
	array(307, 11000, 608),
	array(26, 18000, 602),
	array(311, 6000, 602),
	array(27, 22000, 603),
	array(310, 9000, 603),
	array(28, 41000, 603),
	array(312, 12000, 603),
	array(29, 31000, 604),
	array(313, 13000, 604),
	array(31, 28000, 607),
	array(329, 11000, 607),
	array(33, 19000, 601),
	array(314, 9000, 601),
	array(318, 12000, 619),
	array(34, 0, 619),
	array(36, 33000, 614),
	array(327, 8000, 614),
	array(37, 18000, 619),
	array(339, 11000, 619),
	array(39, 5000, 627),
	array(335, 5000, 627),
	array(40, 37000, 617),
	array(331, 10850, 617),
	array(41, 9000, 612),
	array(373, 16250, 612),
	array(42, 1000, 611),
	array(337, 10000, 611),
	array(43, 3000, 611),
	array(330, 8000, 611),
	array(44, 14750, 604),
	array(344, 13400, 604),
	array(47, 7000, 619),
	array(348, 10000, 619),
	array(49, 26000, 605),
	array(350, 12000, 605),
	array(50, 0, 615),
	array(356, 8000, 615),
	array(430, 10000, 615),
	array(349, 8000, 627),
	array(51, 0, 627),
	array(415, 8000, 611),
	array(52, 6000, 611),
	array(53, 15000, 614),
	array(357, 10000, 614),
	array(54, 26000, 618),
	array(361, 12000, 618),
	array(58, 0, 615),
	array(59, 50000, 601),
	array(370, 10000, 601),
	array(703, 1000, 601),
	array(60, 41000, 619),
	array(369, 8000, 619),
	array(61, 2000, 615),
	array(368, 8000, 627),
	array(62, 7000, 627),
	array(63, 5000, 612),
	array(372, 4000, 612),
	array(64, 28000, 619),
	array(374, 12000, 619),
	array(65, 46343.1, 622),
	array(381, 9781.03, 622),
	array(67, 0, 615),
	array(376, 10000, 615),
	array(68, 9000, 603),
	array(326, 7000, 603),
	array(71, 43000, 615),
	array(324, 12000, 615),
	array(72, 23000, 613),
	array(321, 12000, 613),
	array(75, 4000, 603),
	array(325, 5000, 603),
	array(76, 53000, 622),
	array(358, 14000, 622),
	array(77, 44000, 601),
	array(352, 12000, 601),
	array(78, 65000, 601),
	array(360, 10000, 601),
	array(393, 30000, 604),
	array(85, 0, 604),
	array(387, 14000, 615),
	array(87, 10000, 615),
	array(91, 10000, 601),
	array(394, 10000, 601),
	array(371, 10000, 627),
	array(100, 6000, 627),
	array(400, 10000, 603),
	array(101, 6000, 603),
	array(102, 14000, 613),
	array(401, 8000, 613),
	array(402, 4000, 612),
	array(103, 5000, 612),
	array(105, 19000, 613),
	array(406, 12000, 613),
	array(106, 27000, 622),
	array(407, 16000, 622),
	array(403, 18759, 603),
	array(107, 0, 603),
	array(405, 8000, 603),
	array(108, 5000, 603),
	array(119, 8500, 622),
	array(121, 25000, 613),
	array(417, 10000, 613),
	array(341, 4000, 604),
	array(126, 6000, 604),
	array(365, 13000, 620),
	array(128, 3000, 620),
	array(419, 8000, 604),
	array(130, 5000, 604),
	array(134, 48000, 601),
	array(420, 24000, 601),
	array(421, 10000, 622),
	array(135, 5000, 622),
	array(423, 5000, 622),
	array(136, 8000, 622),
	array(137, 0, 603),
	array(138, 12000, 601),
	array(424, 3000, 601),
	array(141, 100000, 622),
	array(429, 34000, 622),
	array(141, 11000, 622),
	array(444, 8000, 622),
	array(142, 25000, 622),
	array(439, 10000, 622),
	array(148, 45000, 615),
	array(436, 10000, 615),
	array(440, 12000, 604),
	array(151, 6000, 604),
	array(152, 28000, 614),
	array(442, 14000, 614),
	array(447, 8000, 601),
	array(154, 6000, 601),
	array(446, 14000, 604),
	array(155, 13000, 604),
	array(445, 10000, 601),
	array(156, 5000, 601),
	array(449, 0, 622),
	array(157, 0, 622),
	array(164, 29000, 622),
	array(457, 22000, 622),
	array(459, 0, 622),
	array(165, 0, 622),
	array(456, 12000, 622),
	array(168, 10000, 622),
	array(434, 4000, 613),
	array(328, 7000, 601),
	array(702, 6000, 601),
	array(702, 6000, 601)
);

$cias_pago = array();
$cias_inm = array();

foreach ($datos as $row)
{
	$cias_pago[] = $row[0];
	$cias_inm[] = $row[2];
}

$result = $db->query("SELECT
	num_cia,
	CASE
		WHEN clabe_cuenta IS NULL OR TRIM(clabe_cuenta) = '' OR LENGTH(TRIM(clabe_cuenta)) < 11 THEN
			FALSE
		ELSE
			TRUE
	END AS con_cuenta,
	COALESCE((SELECT MAX(folio) FROM folios_cheque WHERE num_cia = cc.num_cia AND cuenta = 1 AND fecha >= '2015-01-01'), 50) + 1 AS folio,
	CASE
		WHEN clabe_cuenta IS NULL OR TRIM(clabe_cuenta) = '' OR LENGTH(TRIM(clabe_cuenta)) < 11 THEN
			(SELECT MIN(num_cia) FROM catalogo_companias WHERE rfc = cc.rfc AND LENGTH(clabe_cuenta) = 11)
		ELSE
			NULL
	END AS cia_pago,
	CASE
		WHEN clabe_cuenta IS NULL OR TRIM(clabe_cuenta) = '' OR LENGTH(TRIM(clabe_cuenta)) < 11 THEN
			COALESCE((SELECT MAX(folio) FROM folios_cheque WHERE num_cia = (SELECT MIN(num_cia) FROM catalogo_companias WHERE rfc = cc.rfc AND LENGTH(clabe_cuenta) = 11)), 50) + 1
		ELSE
			NULL
	END AS folio_pago
FROM
	catalogo_companias cc
WHERE
	num_cia IN (" . implode(', ', $cias_pago) . ")
ORDER BY
	num_cia");

$cias_folios = array();

foreach ($result as $row)
{
	$cias_folios[$row['num_cia']] = array(
		'folio'			=> intval($row['folio']),
		'cuenta'		=> $row['con_cuenta'] == 'f' ? FALSE : TRUE,
		'cia_pago'		=> intval($row['cia_pago']),
		'folio_pago'	=> intval($row['folio_pago'])
	);
}

$result = $db->query("SELECT
	num_cia,
	persona_fis_moral AS tipo_persona
FROM
	catalogo_companias
WHERE
	num_cia IN (" . implode(', ', $cias_inm) . ")
ORDER BY
	num_cia");

$inm = array();

foreach ($result as $row)
{
	$inm[$row['num_cia']] = $row['tipo_persona'] == 'f' ? 'moral' : 'fisica';
}

$sql = '';

$string = '';

$fecha = '2015-11-30';

foreach ($datos as $row)
{
	if ($row[1] <= 0)
	{
		continue;
	}

	$iva = $row[1] * 0.16;
	$ret_iva = $inm[$row[2]] == 'fisica' ? $row[1] * 0.106666667 : 0;
	$ret_isr = $inm[$row[2]] == 'fisica' ? $row[1] * 0.10 : 0;
	$importe = round($row[1] + $iva - $ret_iva - $ret_isr, 2);

	$sql .= "-- CIA " . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['cia_pago'] : $row[0]) . " FOLIO " . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['folio_pago'] : $cias_folios[$row[0]]['folio']) . "\n";
	$sql .= "INSERT INTO cheques (num_cia, codgastos, num_proveedor, fecha, cuenta, folio, cod_mov, importe, a_nombre, concepto, iduser, poliza) VALUES (" . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['cia_pago'] : $row[0]) . ", 52, {$row[2]} + 7000, '$fecha', 1, " . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['folio_pago'] : $cias_folios[$row[0]]['folio']) . ", 41, {$importe}, (SELECT nombre FROM catalogo_companias WHERE num_cia = {$row[2]}), 'RENTA', 1, TRUE);\n";
	$sql .= "INSERT INTO transferencias_electronicas (num_cia, num_proveedor, folio, importe, fecha_gen, status, iduser, cuenta, gen_dep) VALUES (" . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['cia_pago'] : $row[0]) . ", {$row[2]} + 7000, " . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['folio_pago'] : $cias_folios[$row[0]]['folio']) . ", {$importe}, '$fecha', 0, 1, 1, FALSE);\n";
	$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, folio, cuenta, iduser, concepto) VALUES (" . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['cia_pago'] : $row[0]) . ", '$fecha', TRUE, {$importe}, 41, " . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['folio_pago'] : $cias_folios[$row[0]]['folio']) . ", 1, 1, 'RENTA');\n";
	$sql .= "INSERT INTO movimiento_gastos (num_cia, fecha, codgastos, importe, cuenta, concepto, folio, captura) VALUES (" . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['cia_pago'] : $row[0]) . ", '$fecha', 52, {$importe}, 1, 'RENTA', " . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['folio_pago'] : $cias_folios[$row[0]]['folio']) . ", TRUE);\n";
	$sql .= "INSERT INTO folios_cheque (num_cia, cuenta, folio, fecha, reservado, utilizado) VALUES (" . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['cia_pago'] : $row[0]) . ", 1, " . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['folio_pago'] : $cias_folios[$row[0]]['folio']) . ", '$fecha', FALSE, TRUE);\n";

	$string .= "\"" . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['cia_pago'] : $row[0]) . "\",\"" . ( ! $cias_folios[$row[0]]['cuenta'] ? $cias_folios[$row[0]]['folio_pago'] : $cias_folios[$row[0]]['folio']) . "\",\"{$row[1]}\",\"{$iva}\",\"{$ret_iva}\",\"{$ret_isr}\",\"{$importe}\",\"{$row[2]}\"\n";

	if ( ! $cias_folios[$row[0]]['cuenta'])
	{
		$sql .= "INSERT INTO pagos_otras_cias (num_cia, folio, cuenta, num_cia_aplica, fecha) VALUES ({$cias_folios[$row[0]]['cia_pago']}, {$cias_folios[$row[0]]['folio_pago']}, 1, {$row[0]}, '$fecha');\n";

		$cias_folios[$row[0]]['folio_pago']++;

		foreach ($cias_folios as $cia => $info)
		{
			if ($row[0] != $cia && $info['cia_pago'] == $cias_folios[$row[0]]['cia_pago'])
			{
				$cias_folios[$cia]['folio_pago']++;
			}
		}
	}
	else
	{
		$cias_folios[$row[0]]['folio']++;

		foreach ($cias_folios as $cia => $info)
		{
			if ($row[0] != $cia && $info['cia_pago'] == $row[0])
			{
				$cias_folios[$cia]['folio_pago']++;
			}
		}
	}
}

echo "<pre>{$sql}</pre>";
echo "<pre>\"CIA\",\"FOLIO\",\"IMPORTE\",\"IVA\",\"RET. IVA\",\"RET. ISR\",\"TOTAL\",\"INMOBILIARIA\"\n{$string}</pre>";
