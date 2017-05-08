<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

if (isset($_GET['ok'])) {
	$db->query("UPDATE status_cheques SET ok = 'TRUE'");
	header("location: ./ban_che_imp_v2.php");
	die;
}

if (isset($_POST['num_cheque_reim'])) {
	// Obtener datos de los cheques impresos
	$result = $db->query("SELECT folio1, folio2, orden FROM status_cheques");
	
	// Obtener los id's de los cheques a reimprimir
	$sql = "SELECT id FROM cheques WHERE num_cheque BETWEEN " . ($result[0]['orden'] == 1 ? "{$result[0]['folio1']} AND $_POST[num_cheque_reim]" : "$_POST[num_cheque_reim] AND {$result[0]['folio2']}");
	$id = $db->query($sql);
	
	// Mandar datos via POST para su reimpresion
	$ch = curl_init("http://192.168.1.250/lecaroz/ban_che_imp_v2.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	// Construir cadena de datos
	$data = "imp=1&reim=1&ultimo_folio=" . ($result[0]['orden'] == 1 ? $result[0]['folio1'] : $_POST['num_cheque_reim']) . "&orden=" . ($result[0]['orden'] == 1 ? "asc" : "desc") . "&";
	for ($i = 0; $i < count($id); $i++)
		$data .= "id[]={$id[$i]['id']}" . ($i < count($id) - 1 ? "&" : "");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_exec($ch);
	if (curl_errno($ch))
		print curl_error($ch);
	else
		curl_close($ch);
	die;
}

if (isset($_POST['num_cheque'])) {
	// Obtener datos de los ultimos folios impresos
	$result = $db->query("SELECT * FROM status_cheques");
	
	$sql = "";
	// Corregir datos con ordenación ascendente
	if ($result[0]['orden'] == 1) {
		// Cabiar folios de los últimos cheques impresos
		$num_cheques = $result[0]['folio2'] - $result[0]['folio1'] + 1;
		
		// Ordenar folios a recorrer
		rsort($_POST['num_cheque']);
		
		$sql .= "UPDATE cheques SET num_cheque = num_cheque + $num_cheques WHERE num_cheque BETWEEN {$result[0]['folio1']} AND " . ($result[0]['folio1'] + count($_POST['num_cheque']) - 1) . ";\n";
		// Recorrer los folios restantes
		//for ($i = count($_POST['num_cheque']) - 1; $i >= 0; $i++)
		foreach ($_POST['num_cheque'] as $num_cheque)
			$sql .= "UPDATE cheques SET num_cheque = num_cheque - 1 WHERE num_cheque BETWEEN {$result[0]['folio1']} AND $num_cheque;\n";
		// Actualizar status
		$sql .= "UPDATE status_cheques SET folio2 = folio2 + " . count($_POST['num_cheque']) . ";\n";
	}
	else {
		// Ordenar folios a recorrer
		sort($_POST['num_cheque']);
		
		// Cabiar folios de los últimos cheques impresos
		$sql .= "UPDATE cheques SET num_cheque = num_cheque + " . count($_POST['num_cheque']) . " WHERE num_cheque BETWEEN " . ($result[0]['folio2'] - count($_POST['num_cheque']) + 1) . " AND {$result[0]['folio2']};\n";
		// Recorrer los folios restantes
		foreach ($_POST['num_cheque'] as $num_cheque)
			$sql .= "UPDATE cheques SET num_cheque = num_cheque + 1 WHERE num_cheque BETWEEN $num_cheque AND {$result[0]['folio2']};\n";
		// Actualizar status
		$sql .= "UPDATE status_cheques SET folio2 = folio2 + " . count($_POST['num_cheque']) . ";\n";
	}
	$db->query($sql);
	header("location: ./ban_che_error.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_che_error.tpl");
$tpl->prepare();

// Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_folios'])) {
	$tpl->newBlock("saltar_folios_2");
	
	// Obtener datos de los ultimos folios impresos
	$result = $db->query("SELECT * FROM status_cheques");
	
	$tpl->assign("num_cheque1", $result[0]['folio1']);
	$tpl->assign("num_cheque2", $result[0]['folio2']);
	
	for ($i = 0; $i < $_GET['num_folios']; $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("next", $i < $_GET['num_folios'] - 1 ? $i + 1 : 0);
	}
	$tpl->printToScreen();
	die;
}

if (isset($_GET['opcion'])) {
	if ($_GET['opcion'] == 1) {
		$tpl->newBlock("saltar_folios_1");
		$tpl->printToScreen();
		die;
	}
	else {
		$tpl->newBlock("reimprimir");
		
		// Obtener folios
		$folios = $db->query("SELECT folio1, folio2 FROM status_cheques");
		$tpl->assign("folio1", $folios[0]['folio1']);
		$tpl->assign("folio2", $folios[0]['folio2']);
		
		$tpl->printToScreen();
		die;
	}
}

if (isset($_GET['error'])) {
	$tpl->newBlock("error");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("pregunta");
$tpl->printToScreen();
?>