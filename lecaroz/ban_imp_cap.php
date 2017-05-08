<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "";
$numfilas = 25;

if (isset($_GET['num_cia'])) {
	$result = $db->query("SELECT
		isr,
		ieps_gravado,
		ieps_excento,
		ret_isr_ren,
		ret_isr_hon,
		ret_hon_con,
		cre_sal,
		isr_pago,
		ret_iva_hon,
		ret_iva_ren,
		ret_iva_fle,
		iva_pago,
		iva_tras,
		iva_acre,
		iva_dec,
		dec_anu,
		COALESCE((
			SELECT
				SUM(iva_dec)
			FROM
				impuestos_federales
			WHERE
				num_cia = if.num_cia
				AND anio = {$_GET['anio']}
				AND mes < {$_GET['mes']}
		), 0) AS acu_anual
	FROM
		impuestos_federales if
	WHERE
		num_cia = {$_GET['num_cia']}
		AND mes = {$_GET['mes']}
		AND anio = {$_GET['anio']}");

	if (!$result) die("");

	$str = "";
	foreach ($result as $reg)
		foreach ($reg as $tag => $value)
			if (!in_array($tag, array("id", "num_cia", "mes", "anio")))
				$str .= $tag . "[$_GET[i]]|$value||";

	die($str);
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_imp_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['anio'])) {
	$_SESSION['imp'] = $_POST;

	$sql = "";
	for ($i = 0; $i < $numfilas; $i++) {
		$data['num_cia'] = get_val($_POST['num_cia'][$i]);
		$data['mes']     = $_POST['mes'];
		$data['anio']    = $_POST['anio'];

		if ($id = $db->query("SELECT id FROM impuestos_federales WHERE num_cia = $data[num_cia] AND mes = $data[mes] AND anio = $data[anio]")) {
			$sql .= "DELETE FROM impuestos_federales WHERE id = {$id[0]['id']};\n";

			/*$tpl->newBlock("valid");
			$tpl->assign("mensaje", "Ya existe un registro con la misma fecha para la compañía $data[num_cia]");
			$tpl->assign("campo", "num_cia[$i]");
			$tpl->printToScreen();
			die;*/
		}

		$data['isr_pago'] = get_val($_POST['isr_pago'][$i]);
		$data['iva_pago'] = get_val($_POST['iva_pago'][$i]);
		$data['iva_dec']  = get_val($_POST['iva_dec'][$i]);
		$data['isr_neto'] = get_val($_POST['isr_neto'][$i]);

		if ($data['num_cia'] > 0 && ($data['isr_pago'] != 0 || $data['iva_pago'] != 0 || $data['iva_dec'] != 0 || $data['isr_neto'] != 0)) {
			$data['isr']          = get_val($_POST['isr'][$i]);
			// [11-Abr-2014] Ya no se usara IETU y el IEPS se desglosa en gravado y excento
			// $data['ietu']         = get_val($_POST['ietu'][$i]);
			// $data['ieps']         = get_val($_POST['ieps'][$i]);
			$data['ieps_gravado'] = get_val($_POST['ieps_gravado'][$i]);
			$data['ieps_excento'] = get_val($_POST['ieps_excento'][$i]);
			$data['ret_isr_ren']  = get_val($_POST['ret_isr_ren'][$i]);
			$data['ret_isr_hon']  = get_val($_POST['ret_isr_hon'][$i]);
			$data['ret_hon_con']  = get_val($_POST['ret_hon_con'][$i]);
			$data['cre_sal']      = get_val($_POST['cre_sal'][$i]);
			$data['ret_iva_hon']  = get_val($_POST['ret_iva_hon'][$i]);
			$data['ret_iva_ren']  = get_val($_POST['ret_iva_ren'][$i]);
			$data['ret_iva_fle']  = get_val($_POST['ret_iva_fle'][$i]);
			$data['iva_tras']     = get_val($_POST['iva_tras'][$i]);
			$data['iva_acre']     = get_val($_POST['iva_acre'][$i]);
			$data['dec_anu']      = get_val($_POST['dec_anu'][$i]);
			// [29-Ago-2008] Campos agregados por cambio en los calculos
			// [02-Abr-2014] A partir de marzo de 2014 ya no se desglosa el IDE
			// $data['ide_ret']     = get_val($_POST['ide_ret'][$i]);
			// $data['isr_acr_ide'] = get_val($_POST['isr_acr_ide'][$i]);
			// $data['ide_dev']     = get_val($_POST['ide_dev'][$i]);
			// $data['isr_neto']    = get_val($_POST['isr_neto'][$i]);

			$sql .= $db->preparar_insert("impuestos_federales", $data) . ";\n";
		}
	}

	if ($sql != "") $db->query($sql);

	$tpl->newBlock("redir");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("captura");
$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

// Filas de captura
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("back", $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

// Catálogo de Compañías
$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	foreach ($cia as $tag => $value)
		$tpl->assign($tag, $value);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
