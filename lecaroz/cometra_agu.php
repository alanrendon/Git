<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/cheques.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$tpl = new TemplatePower( "./plantillas/ban/cometra_agu.tpl" );
$tpl->prepare();

$last_porc = $db->query("SELECT * FROM porcentaje_aguinaldo ORDER BY id DESC LIMIT 1");

$sql = "
	SELECT
		catalogo_companias.cia_aguinaldos
			AS num_cia,
		sum(aguinaldos.importe)
			AS importe
	FROM
		aguinaldos
		LEFT JOIN catalogo_trabajadores
			ON (catalogo_trabajadores.id = aguinaldos.id_empleado)
		LEFT JOIN catalogo_companias
			ON (catalogo_companias.num_cia=catalogo_trabajadores.num_cia)
	WHERE
		solo_aguinaldo = TRUE
		AND aguinaldos.fecha = '{$last_porc[0]['fecha_aguinaldo']}'
		AND aguinaldos.importe >= 20
		AND catalogo_companias.cia_aguinaldos NOT IN (28, 35, 45, 66, 132, 58, 61, 700, 800)
		AND catalogo_companias.cia_aguinaldos BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . "
	GROUP BY
		catalogo_companias.cia_aguinaldos
	ORDER BY
		catalogo_companias.cia_aguinaldos
";
$result = $db->query($sql);

$total_aguinaldos  = 0;
$num_empleados = 0;
for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("ficha");
	$tpl->assign("fecha", date("d/m/Y"));
	$tpl->assign("num_cia", $result[$i]['num_cia']);
	$tmp = $db->query("SELECT nombre AS nombre_cia, clabe_cuenta2 AS cuenta, direccion FROM catalogo_companias WHERE num_cia = {$result[$i]['num_cia']}");

	$tpl->assign("nombre_cia", $tmp[0]['nombre_cia']);
	$tpl->assign("cuenta", $tmp[0]['cuenta']);
	$tpl->assign("direccion", strlen($tmp[0]['direccion']) > 54 ? substr($tmp[0]['direccion'], 0, 54) : $tmp[0]['direccion']);
	$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
	$tpl->assign("importe_escrito", num2string($result[$i]['importe']));
}

$tpl->printToScreen();

$string = '';

foreach ($result as $rec) {
	$tmp = $db->query("SELECT num_cia, nombre AS nombre_cia, clabe_cuenta AS cuenta, direccion FROM catalogo_companias WHERE num_cia = {$rec['num_cia']}");

	$d = $tmp[0];

	$string .= str_pad('', 4, "\n");
	$string .= str_pad('', 1, ' ') . date('d-m-y');
	$string .= str_pad('', 31, ' ') . 'X';
	$string .= str_pad('', 2, "\n");
	$string .= str_pad('', 3, ' ') . str_pad($d['cuenta'], 11, '0') . str_pad('', 25, ' ') . 'X';
	$string .= str_pad('', 2, "\n");
	$string .= str_pad('', 6, ' ') . substr($d['num_cia'] . '-' . $d['nombre_cia'], 0, 64);
	$string .= str_pad('', 2, "\n");
	$string .= str_pad('', 4, ' ') . substr($d['direccion'], 0, 66);
	$string .= str_pad('', 2, "\n");
	$string .= str_pad('', 5, ' ') . '1(UNO)' . str_pad('', 19, ' ') . 'X' . str_pad('', 38, ' ') . number_format($rec['importe'], 2);
	$string .= str_pad('', 2, "\n");
	$string .= str_pad('', 4, ' ') . substr(num2string($rec['importe']), 0, 66);
	$string .= str_pad('', 3, "\n");
	$string .= /*number_format($d['total'], 2)*/'';
	$string .= str_pad('', 4, "\n");
	$string .= str_pad('', 4, ' ') . substr($d['num_cia'] . '-' . $d['nombre_cia'], 0, 64);
	$string .= str_pad('', 2, "\n");
	$string .= str_pad('', 4, ' ') . substr($d['direccion'], 0, 66);
	$string .= str_pad('', 1, "\n");
	$string .= str_pad('', 4, ' ')/* . 'DEL. VENUSTIANO CARRANZA, CP.15820, MEXICO, D.F.'*/;
	$string .= str_pad('', 27, "\n");
}

shell_exec("chmod ugo=rwx pcl");

$fp = fopen('pcl/ComprobantesCometraAguinaldos.txt', 'w');

fwrite($fp, $string);

fclose($fp);

shell_exec('lpr -l -P cometra pcl/ComprobantesCometraAguinaldos.txt');

shell_exec("chmod ugo=r pcl");



?>
