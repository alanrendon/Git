<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "Los archivos solo puede ser de tipo 'gzip' (extensión .gz)";
$descripcion_error[2] = "No hay resultados";

function buscar_cia($catCias, $cuenta) {
	for ($i = 0; $i < count($catCias); $i++)
		if ($cuenta == $catCias[$i]['clabe_cuenta2'])
			return $catCias[$i]['num_cia'];

	return FALSE;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_exp_san.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Cargar y validar archivo de datos al sistema
if (isset($_FILES['file'])) {
	/*if (!stristr($_FILES['file']['type'], "application/gzip")) {
		header("location: ./ban_exp_san.php?codigo_error=1");
		die;
	}*/

	// Cargar catálogo de compañías
	$catCias = $db->query("SELECT num_cia, clabe_cuenta2 FROM catalogo_companias WHERE clabe_cuenta2 IS NOT NULL ORDER BY clabe_cuenta2");

	$j = 0;
	// Abrir el archivo
	$file_name = $_FILES['file']['name'];
	$lines = gzfile($_FILES['file']['tmp_name']);

	// Recorrer el archivo
	foreach ($lines as $line) {
		if (trim($line) == '') {
			continue;
		}
		$data[$j]['num_cia'] = buscar_cia($catCias, trim(substr($line, 0, 11)));
		$data[$j]['cuenta'] = trim(substr($line, 0, 11));
		$data[$j]['fecha'] = substr($line, 18, 2) . "/" . substr($line, 16, 2) . "/" . substr($line, 20, 4) . " " . substr($line, 24, 2) . ":" . substr($line, 26, 2);
		$data[$j]['cod_banco'] = substr($line, 32, 4);
		$data[$j]['descripcion'] = trim(substr($line, 36, 40));
		$data[$j]['tipo_mov'] = substr($line, 76, 1) == "+" ? "FALSE" : "TRUE";
		$data[$j]['importe'] = floatval(substr($line, 77, 12) . "." . substr($line, 89, 2));
		$data[$j]['folio'] = intval(substr($line, 105, 8)) > 0 ? intval(substr($line, 105, 8)) : "";
		$data[$j]['concepto'] = trim(substr($line, 113, 40));
		$data[$j]['saldo'] = floatval(substr($line, 91, 12) . "." . substr($line, 103, 2));
		$data[$j]['hash'] = md5($line);

		$j++;
	}
	// Guardar movimientos en la tabla mov_santander
	$db->query("TRUNCATE TABLE mov_santander_tmp");//echo '<pre>' . $db->multiple_insert("mov_santander_tmp", $data) . '</pre>';die;
	$db->query($db->multiple_insert("mov_santander_tmp", $data));

	$sql = "SELECT num_cia, nombre, cuenta, fecha, cod_banco, descripcion, tipo_mov, importe, folio, concepto, saldo, hash FROM mov_santander_tmp LEFT JOIN catalogo_companias USING (num_cia) ORDER BY num_cia, fecha";
	$result = $db->query($sql);echo count($result);

	//$num_movs_pan = $db->query("SELECT count(id) FROM mov_santander_tmp WHERE num_cia < 900");
	//$num_movs_zap = $db->query("SELECT count(id) FROM mov_santander_tmp WHERE num_cia >= 900");
	//echo "pan: {$num_movs_pan[0]['count']}<br>";
	//echo "zap: {$num_movs_zap[0]['count']}<br>";

	if (!$result) {
		header("location: ./ban_exp_san.php?codigo_error=2");
		die;
	}

	$tpl->newBlock("listado");
	$tpl->assign("nombre_archivo", basename($file_name));
	$tpl->assign("hash", md5(implode($lines)));

	$num_cia = NULL;
	$saldo = array();
	foreach ($result as $row) {
		if ($num_cia != $row['num_cia']) {
			$num_cia = $row['num_cia'];

			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("cuenta", $row['cuenta']);
			$tpl->assign("nombre_cia", $row['nombre']);

			$abonos = 0;
			$cargos = 0;
			$saldo[$num_cia] = NULL;
		}
		$tpl->newBlock("row");
		$tpl->assign("fecha", $row['fecha']);
		$tpl->assign("abono", $row['tipo_mov'] == "f" ? number_format($row['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("cargo", $row['tipo_mov'] == "t" ? number_format($row['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("saldo", number_format($row['saldo'], 2, ".", ","));
		$tpl->assign("folio", $row['folio']);
		$tpl->assign("cod_banco", $row['cod_banco']);
		$tpl->assign("descripcion", $row['descripcion']);
		$tpl->assign("concepto", $row['concepto']);
		$tpl->assign("hash", $row['hash']);

		$abonos += $row['tipo_mov'] == "f" ? $row['importe'] : 0;
		$cargos += $row['tipo_mov'] == "t" ? $row['importe'] : 0;

		$tpl->assign("cia.abonos", number_format($abonos, 2, ".", ","));
		$tpl->assign("cia.cargos", number_format($cargos, 2, ".", ","));

		$saldo[$num_cia]['saldo'] = $row['saldo'];
		$saldo[$num_cia]['fecha'] = $row['fecha'];
	}

	$sql = "";
	foreach ($saldo as $cia => $imp)
		if ($imp['saldo'] != NULL && $cia > 0) {
			if ($db->query("SELECT id FROM saldo_santander WHERE num_cia = $cia"))
				$sql .= "UPDATE saldo_santander SET saldo = $imp[saldo], fecha_archivo = '$imp[fecha]' WHERE num_cia = $cia;\n";
			else
				$sql .= "INSERT INTO saldo_santander (num_cia, saldo, fecha_archivo) VALUES ($cia, $imp[saldo], $imp[fecha]);\n";
		}

	// echo "<pre>$sql</pre>";
	$db->query($sql);

	$tpl->printToScreen();
	die;
}

// Si no se ha cargado archivo, solicitarlo
$tpl->newBlock("archivo");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
