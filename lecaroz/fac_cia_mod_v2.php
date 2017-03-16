<?php
// MODIFICACION DE COMPAÑÍAS V2
// Tabla 'catalogo_companias'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

$mensaje[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_cia_mod_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Obtener datos de la compañía a modificar ---------------------------------
if (isset($_POST['num_cia'])) {
	// Generar script de actualización
	$sql  = "UPDATE catalogo_companias SET ";
	$sql .= " nombre = '$_POST[nombre]',";
	$sql .= " direccion = '$_POST[direccion]',";
	$sql .= " rfc = '$_POST[rfc]',";
	$sql .= " no_imss = '$_POST[no_imss]',";
	$sql .= " no_infonavit = '$_POST[no_infonavit]',";
	$sql .= " telefono = '$_POST[telefono]',";
	$sql .= " sub_cuenta_deudores = " . ($_POST['sub_cuenta_deudores'] > 0 ? $_POST['sub_cuenta_deudores'] : "NULL") . ",";
	$sql .= " no_cta_cia_luz = '$_POST[no_cta_cia_luz]',";
	$sql .= " persona_fis_moral = '$_POST[persona_fis_moral]',";
	$sql .= " nombre_corto = '$_POST[nombre_corto]',";
	$sql .= " idadministrador = $_POST[idadministrador],";
	$sql .= " idaseguradora = $_POST[idaseguradora],";
	$sql .= " idauditor = $_POST[idauditor],";
	$sql .= " idcontador = $_POST[idcontador],";
	$sql .= " iddelimss = $_POST[iddelimss],";
	$sql .= " idoperadora = $_POST[idoperadora],";
	$sql .= " idsindicato = $_POST[idsindicato],";
	$sql .= " idsubdelimss = $_POST[idsubdelimss],";
	$sql .= " cod_gasolina = " . ($_POST['cod_gasolina'] != "" ? $_POST['cod_gasolina'] : "NULL") . ",";
	$sql .= " clabe_banco = '$_POST[clabe_banco]',";
	$sql .= " clabe_plaza = '$_POST[clabe_plaza]',";
	$sql .= " clabe_cuenta = '$_POST[clabe_cuenta]',";
	$sql .= " clabe_identificador = '$_POST[clabe_identificador]',";
	$sql .= " clabe_banco2 = '$_POST[clabe_banco2]',";
	$sql .= " clabe_plaza2 = '$_POST[clabe_plaza2]',";
	$sql .= " clabe_cuenta2 = '$_POST[clabe_cuenta2]',";
	$sql .= " clabe_identificador2 = '$_POST[clabe_identificador2]',";
	$sql .= " email = '$_POST[email]',";
	$sql .= " aplica_iva = '$_POST[aplica_iva]',";
	$sql .= " num_cia_primaria = $_POST[num_cia_primaria],";
	$sql .= " num_proveedor = ".($_POST['num_proveedor'] != "" ? $_POST['num_proveedor'] : "NULL") . ",";
	$sql .= " med_agua = '$_POST[med_agua]',";
	$sql .= " cliente_cometra = " . ($_POST['cliente_cometra'] != "" ? $_POST['cliente_cometra'] : "NULL") . ",";
	$sql .= " luz_esp = '" . (isset($_POST['luz_esp']) ? "TRUE" : "FALSE") . "',";
	$sql .= " aviso_saldo = '$_POST[aviso_saldo]',";
	$sql .= " dia_ven_luz = " . ($_POST['dia_ven_luz'] != '' ? $_POST['dia_ven_luz'] : 'NULL') . ",";
	$sql .= " periodo_pago_luz = $_POST[periodo_pago_luz],";
	$sql .= " bim_par_imp_luz = $_POST[bim_par_imp_luz],";
	$sql .= " turno_cometra = $_POST[turno_cometra],";
	$sql .= " por_bg = " . get_val($_POST['por_bg']) . ",";
	$sql .= " por_efectivo = " . get_val($_POST['por_efectivo']) . ",";
	$sql .= " por_bg_1 = " . get_val($_POST['por_bg_1']) . ",";
	$sql .= " por_efectivo_1 = " . get_val($_POST['por_efectivo_1']) . ",";
	$sql .= " por_bg_2 = " . get_val($_POST['por_bg_2']) . ",";
	$sql .= " por_efectivo_2 = " . get_val($_POST['por_efectivo_2']) . ",";
	$sql .= " por_bg_3 = " . get_val($_POST['por_bg_3']) . ",";
	$sql .= " por_efectivo_3 = " . get_val($_POST['por_efectivo_3']) . ",";
	$sql .= " por_bg_4 = " . get_val($_POST['por_bg_4']) . ",";
	$sql .= " por_efectivo_4 = " . get_val($_POST['por_efectivo_4']);
	$sql .= " WHERE num_cia = $_POST[num_cia];\n";
	$sql .= "UPDATE catalogo_companias SET nombre = upper(nombre), nombre_corto = upper(nombre_corto), rfc = upper(rfc), direccion = upper(direccion) WHERE num_cia = $_POST[num_cia];\n";
	
	// [14-Jul-2008] Obtener numeros de cuenta y si han cambiado poner los saldos en 0
	$cuentas = $db->query("SELECT clabe_cuenta, clabe_cuenta2 FROM catalogo_companias WHERE num_cia = $_POST[num_cia]");
	if ($cuentas[0]['clabe_cuenta'] != $_POST['clabe_cuenta']) {
		$sql .= "DELETE FROM saldos WHERE num_cia = $_POST[num_cia] AND cuenta = 1;\n";
		$sql .= "INSERT INTO saldos (num_cia, saldo_libros, saldo_bancos, cuenta) VALUES ($_POST[num_cia], 0, 0, 1);\n";
	}
	if ($cuentas[0]['clabe_cuenta2'] != $_POST['clabe_cuenta2']) {
		$sql .= "DELETE FROM saldos WHERE num_cia = $_POST[num_cia] AND cuenta = 2;\n";
		$sql .= "INSERT INTO saldos (num_cia, saldo_libros, saldo_bancos, cuenta) VALUES ($_POST[num_cia], 0, 0, 2);\n";
	}
	
	// Actualizar compañía
	$db->query($sql);
	header("location: ./fac_cia_mod_v2.php");
	die;
}

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $mensaje[$_GET['mensaje']]);
	}

	$tpl->printToScreen();
	die();
}

// -------------------------------- Modificar -------------------------------------------------------
$sql = "SELECT * FROM catalogo_companias WHERE num_cia = $_GET[num_cia]";
$result = $db->query($sql);

if (!$result) {
	header("location: ./fac_cia_mod_v2.php?mensaje=1");
	die;
}

$tpl->newBlock("modificar");
$tpl->assign("num_cia", $result[0]['num_cia']);
$tpl->assign("nombre", $result[0]['nombre']);
$tpl->assign("nombre_corto", $result[0]['nombre_corto']);
$tpl->assign("direccion", $result[0]['direccion']);
$tpl->assign("telefono", $result[0]['telefono']);
$tpl->assign("email", $result[0]['email']);
$tpl->assign("rfc", $result[0]['rfc']);
$tpl->assign("iva_" . ($result[0]['aplica_iva'] != '' ? $result[0]['aplica_iva'] : 'f'), "checked");
$tpl->assign("agua_" . ($result[0]['med_agua'] != '' ? $result[0]['med_agua'] : 'f'), "checked");
$tpl->assign("cod_gasolina", $result[0]['cod_gasolina']);
$tpl->assign("per_" . ($result[0]['persona_fis_moral'] != '' ? $result[0]['persona_fis_moral'] : 't'), "checked");
$tpl->assign("no_imss", $result[0]['no_imss']);
$tpl->assign("no_infonavit", $result[0]['no_infonavit']);
$tpl->assign("cuenta_luz", $result[0]['no_cta_cia_luz']);
$tpl->assign("sub_cuenta_deudores", $result[0]['sub_cuenta_deudores']);
$tpl->assign("num_cia_pri", $result[0]['num_cia_primaria']);
$tpl->assign("num_pro", $result[0]['num_proveedor']);
$tpl->assign("banco1", $result[0]['clabe_banco']);
$tpl->assign("plaza1", $result[0]['clabe_plaza']);
$tpl->assign("cuenta1", $result[0]['clabe_cuenta']);
$tpl->assign("id1", $result[0]['clabe_identificador']);
$tpl->assign("banco2", $result[0]['clabe_banco2']);
$tpl->assign("plaza2", $result[0]['clabe_plaza2']);
$tpl->assign("cuenta2", $result[0]['clabe_cuenta2']);
$tpl->assign("id2", $result[0]['clabe_identificador2']);
$tpl->assign("cliente_cometra", $result[0]['cliente_cometra']);
$tpl->assign("luz_esp", $result[0]['luz_esp'] == "t" ? "checked" : "");
$tpl->assign('saldo_' . ($result[0]['aviso_saldo'] != '' ? $result[0]['aviso_saldo'] : 'f'), 'checked');
$tpl->assign('dia_ven_luz', $result[0]['dia_ven_luz']);
$tpl->assign('per_' . ($result[0]['periodo_pago_luz'] > 0 ? $result[0]['periodo_pago_luz'] : 2), ' checked');
$tpl->assign('par_' . ($result[0]['bim_par_imp_luz'] != '' ? $result[0]['bim_par_imp_luz'] : 0), ' checked');
$tpl->assign('turno_cometra_' . $result[0]['turno_cometra'], ' checked');
$tpl->assign('por_bg', $result[0]['por_bg'] > 0 ? number_format($result[0]['por_bg'], 2, '.', '') : '');
$tpl->assign('por_efectivo', $result[0]['por_efectivo'] > 0 ? number_format($result[0]['por_efectivo'], 2, '.', '') : '');
$tpl->assign('por_bg_1', $result[0]['por_bg_1'] > 0 ? number_format($result[0]['por_bg_1'], 2, '.', '') : '');
$tpl->assign('por_efectivo_1', $result[0]['por_efectivo_1'] > 0 ? number_format($result[0]['por_efectivo_1'], 2, '.', '') : '');
$tpl->assign('por_bg_2', $result[0]['por_bg_2'] > 0 ? number_format($result[0]['por_bg_2'], 2, '.', '') : '');
$tpl->assign('por_efectivo_2', $result[0]['por_efectivo_2'] > 0 ? number_format($result[0]['por_efectivo_2'], 2, '.', '') : '');
$tpl->assign('por_bg_3', $result[0]['por_bg_3'] > 0 ? number_format($result[0]['por_bg_3'], 2, '.', '') : '');
$tpl->assign('por_efectivo_3', $result[0]['por_efectivo_3'] > 0 ? number_format($result[0]['por_efectivo_3'], 2, '.', '') : '');
$tpl->assign('por_bg_4', $result[0]['por_bg_4'] > 0 ? number_format($result[0]['por_bg_4'], 2, '.', '') : '');
$tpl->assign('por_efectivo_4', $result[0]['por_efectivo_4'] > 0 ? number_format($result[0]['por_efectivo_4'], 2, '.', '') : '');

$cat = $db->query("SELECT * FROM catalogo_del_imss ORDER BY iddelimss ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("imss");
	$tpl->assign("id", $cat[$i]['iddelimss']);
	$tpl->assign("nombre", $cat[$i]['nombre_del_imss']);
	if ($cat[$i]['iddelimss'] == $result[0]['iddelimss']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_subdel_imss ORDER BY idsubdelimss ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("subimss");
	$tpl->assign("id", $cat[$i]['idsubdelimss']);
	$tpl->assign("nombre", $cat[$i]['nombre_subdel_imss']);
	if ($cat[$i]['idsubdelimss'] == $result[0]['idsubdelimss']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_contadores ORDER BY idcontador ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("contador");
	$tpl->assign("id", $cat[$i]['idcontador']);
	$tpl->assign("nombre", $cat[$i]['nombre_contador']);
	if ($cat[$i]['idcontador'] == $result[0]['idcontador']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_auditores ORDER BY idauditor ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("auditor");
	$tpl->assign("id", $cat[$i]['idauditor']);
	$tpl->assign("nombre", $cat[$i]['nombre_auditor']);
	if ($cat[$i]['idauditor'] == $result[0]['idauditor']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_sindicatos ORDER BY idsindicato ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("sindicato");
	$tpl->assign("id", $cat[$i]['idsindicato']);
	$tpl->assign("nombre", $cat[$i]['nombre_sindicato']);
	if ($cat[$i]['idsindicato'] == $result[0]['idsindicato']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_administradores ORDER BY idadministrador ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("administrador");
	$tpl->assign("id", $cat[$i]['idadministrador']);
	$tpl->assign("nombre", $cat[$i]['nombre_administrador']);
	if ($cat[$i]['idadministrador'] == $result[0]['idadministrador']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_aseguradoras ORDER BY idaseguradora ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("aseguradora");
	$tpl->assign("id", $cat[$i]['idaseguradora']);
	$tpl->assign("nombre", $cat[$i]['nombre_aseguradora']);
	if ($cat[$i]['idaseguradora'] == $result[0]['idaseguradora']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_operadoras ORDER BY idoperadora ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("operadora");
	$tpl->assign("id", $cat[$i]['idoperadora']);
	$tpl->assign("nombre", $cat[$i]['nombre_operadora']);
	if ($cat[$i]['idoperadora'] == $result[0]['idoperadora']) $tpl->assign("selected", "selected");
}

$tpl->printToScreen();
?>