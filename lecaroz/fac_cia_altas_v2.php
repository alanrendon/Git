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

$descripcion_error[1] = "La compañía ya existe en el catálogo";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_cia_altas_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Obtener datos de la compañía a modificar ---------------------------------
if (isset($_POST['num_cia'])) {
	$_SESSION['cia_alta'] = $_POST;
	$_SESSION['cia_alta']['luz_esp'] = isset($_POST['luz_esp']) ? "TRUE" : "FALSE";
	$_SESSION['cia_alta']['por_bg'] = get_val($_SESSION['cia_alta']['por_bg']);
	$_SESSION['cia_alta']['por_efectivo'] = get_val($_SESSION['cia_alta']['por_efectivo']);
	
	if ($db->query("SELECT num_cia FROM catalogo_companias WHERE num_cia = $_POST[num_cia]")) {
		header("location: ./fac_cia_altas_v2.php?codigo_error=1");
		die;
	}
	
	$sql = $db->preparar_insert("catalogo_companias", $_SESSION['cia_alta']) . "\n;";
	$sql .= "INSERT INTO saldos (num_cia, saldo_libros, saldo_bancos, cuenta) VALUES ($_POST[num_cia], 0, 0, 1);\n";
	$sql .= "INSERT INTO saldos (num_cia, saldo_libros, saldo_bancos, cuenta) VALUES ($_POST[num_cia], 0, 0, 2);\n";
	$sql .= "UPDATE catalogo_companias SET nombre = upper(nombre), nombre_corto = upper(nombre_corto), rfc = upper(rfc), direccion = upper(direccion) WHERE num_cia = $_POST[num_cia];\n";
	$db->query($sql);
	
	unset($_SESSION['cia_alta']);
	header("location: ./fac_cia_altas_v2.php");
	die;
}

// -------------------------------- Modificar -------------------------------------------------------
$sql = "SELECT num_cia FROM catalogo_companias WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 800') . " ORDER BY num_cia";
$cias = $db->query($sql);

function ultimaCia() {
	global $cias;
	
	if (!$cias)
		return $_SESSION['tipo_usuario'] == 2 ? 900 : 1;
	
	$num = $_SESSION['tipo_usuario'] == 2 ? 900 : 1;
	foreach ($cias as $cia)
		if ($cia['num_cia'] == $num)
			$num++;
		else
			return $num;
	
	return $num;
}

if (isset($_SESSION['cia_alta'])) {
	$tpl->assign("num_cia", $_SESSION['cia_alta']['num_cia']);
	$tpl->assign("nombre", $_SESSION['cia_alta']['nombre']);
	$tpl->assign("nombre_corto", $_SESSION['cia_alta']['nombre_corto']);
	$tpl->assign("direccion", $_SESSION['cia_alta']['direccion']);
	$tpl->assign("telefono", $_SESSION['cia_alta']['telefono']);
	$tpl->assign("email", $_SESSION['cia_alta']['email']);
	$tpl->assign("rfc", $_SESSION['cia_alta']['rfc']);
	$tpl->assign("iva_" . $_SESSION['cia_alta']['aplica_iva'], "checked");
	$tpl->assign("agua_" . $_SESSION['cia_alta']['med_agua'], "checked");
	$tpl->assign("cod_gasolina", $_SESSION['cia_alta']['cod_gasolina']);
	$tpl->assign("per_" . $_SESSION['cia_alta']['persona_fis_moral'], "checked");
	$tpl->assign("no_imss", $_SESSION['cia_alta']['no_imss']);
	$tpl->assign("no_infonavit", $_SESSION['cia_alta']['no_infonavit']);
	$tpl->assign("cuenta_luz", $_SESSION['cia_alta']['no_cta_cia_luz']);
	$tpl->assign("luz_esp", $_SESSION['cia_alta']['luz_esp'] == "TRUE" ? "checked" : "");
	$tpl->assign("sub_cuenta_deudores", $_SESSION['cia_alta']['sub_cuenta_deudores']);
	$tpl->assign("num_cia_primaria", $_SESSION['cia_alta']['num_cia_primaria']);
	$tpl->assign("num_proveedor", $_SESSION['cia_alta']['num_proveedor']);
	$tpl->assign("cliente_cometra", $_SESSION['cia_alta']['cliente_cometra']);
	$tpl->assign("clabe_banco", $_SESSION['cia_alta']['clabe_banco']);
	$tpl->assign("clabe_plaza", $_SESSION['cia_alta']['clabe_plaza']);
	$tpl->assign("clabe_cuenta", $_SESSION['cia_alta']['clabe_cuenta']);
	$tpl->assign("clabe_identificador", $_SESSION['cia_alta']['clabe_identificador']);
	$tpl->assign("clabe_banco2", $_SESSION['cia_alta']['clabe_banco2']);
	$tpl->assign("clabe_plaza2", $_SESSION['cia_alta']['clabe_plaza2']);
	$tpl->assign("clabe_cuenta2", $_SESSION['cia_alta']['clabe_cuenta2']);
	$tpl->assign("clabe_identificador2", $_SESSION['cia_alta']['clabe_identificador2']);
	$tpl->assign("saldo_" . $_SESSION['cia_alta']['aviso_saldo']);
	$tpl->assign('per_' . $_SESSION['cia_alta']['periodo_pago_luz'], ' checked');
	$tpl->assign('par_' . $_SESSION['cia_alta']['bim_par_imp_luz'], ' checked');
	$tpl->assign('por_bg', $_SESSION['cia_alta']['por_bg'] > 0 ? number_format($_SESSION['cia_alta']['por_bg']) : '');
	$tpl->assign('por_efectivo', $_SESSION['cia_alta']['por_efectivo'] > 0 ? number_format($_SESSION['cia_alta']['por_efectivo']) : '');
	$tpl->assign('por_bg_1', $_SESSION['cia_alta']['por_bg_1'] > 0 ? number_format($_SESSION['cia_alta']['por_bg_1']) : '');
	$tpl->assign('por_efectivo_1', $_SESSION['cia_alta']['por_efectivo_1'] > 0 ? number_format($_SESSION['cia_alta']['por_efectivo_1']) : '');
	$tpl->assign('por_bg_2', $_SESSION['cia_alta']['por_bg_2'] > 0 ? number_format($_SESSION['cia_alta']['por_bg_2']) : '');
	$tpl->assign('por_efectivo_2', $_SESSION['cia_alta']['por_efectivo_2'] > 0 ? number_format($_SESSION['cia_alta']['por_efectivo_2']) : '');
	$tpl->assign('por_bg_3', $_SESSION['cia_alta']['por_bg_3'] > 0 ? number_format($_SESSION['cia_alta']['por_bg_3']) : '');
	$tpl->assign('por_efectivo_3', $_SESSION['cia_alta']['por_efectivo_3'] > 0 ? number_format($_SESSION['cia_alta']['por_efectivo_3']) : '');
	$tpl->assign('por_bg_4', $_SESSION['cia_alta']['por_bg_4'] > 0 ? number_format($_SESSION['cia_alta']['por_bg_4']) : '');
	$tpl->assign('por_efectivo_4', $_SESSION['cia_alta']['por_efectivo_4'] > 0 ? number_format($_SESSION['cia_alta']['por_efectivo_4']) : '');
	unset($_SESSION['cia_alta']);
}
else {
	$tpl->assign("num_cia", ultimaCia());
	$tpl->assign("iva_FALSE", "checked");
	$tpl->assign("agua_FALSE", "checked");
	$tpl->assign("per_TRUE", "checked");
	$tpl->assign("saldo_FALSE", "checked");
	$tpl->assign('per_2', ' checked');
	$tpl->assign('par_' . (date('n') % 2 == 0 ? 0 : 1), ' checked');
}

$cat = $db->query("SELECT * FROM catalogo_del_imss ORDER BY iddelimss ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("imss");
	$tpl->assign("id", $cat[$i]['iddelimss']);
	$tpl->assign("nombre", $cat[$i]['nombre_del_imss']);
	if (isset($_SESSION['cia_alta']) && $cat[$i]['iddelimss'] == $_SESSION['cia_alta']['iddelimss']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_subdel_imss ORDER BY idsubdelimss ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("subimss");
	$tpl->assign("id", $cat[$i]['idsubdelimss']);
	$tpl->assign("nombre", $cat[$i]['nombre_subdel_imss']);
	if (isset($_SESSION['cia_alta']) && $cat[$i]['idsubdelimss'] == $_SESSION['cia_alta']['idsubdelimss']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_contadores ORDER BY idcontador ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("contador");
	$tpl->assign("id", $cat[$i]['idcontador']);
	$tpl->assign("nombre", $cat[$i]['nombre_contador']);
	if (isset($_SESSION['cia_alta']) && $cat[$i]['idcontador'] == $_SESSION['cia_alta']['idcontador']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_auditores ORDER BY idauditor ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("auditor");
	$tpl->assign("id", $cat[$i]['idauditor']);
	$tpl->assign("nombre", $cat[$i]['nombre_auditor']);
	if (isset($_SESSION['cia_alta']) && $cat[$i]['idauditor'] == $_SESSION['cia_alta']['idauditor']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_sindicatos ORDER BY idsindicato ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("sindicato");
	$tpl->assign("id", $cat[$i]['idsindicato']);
	$tpl->assign("nombre", $cat[$i]['nombre_sindicato']);
	if (isset($_SESSION['cia_alta']) && $cat[$i]['idsindicato'] == $_SESSION['cia_alta']['idsindicato']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_administradores ORDER BY idadministrador ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("administrador");
	$tpl->assign("id", $cat[$i]['idadministrador']);
	$tpl->assign("nombre", $cat[$i]['nombre_administrador']);
	if (isset($_SESSION['cia_alta']) && $cat[$i]['idadministrador'] == $_SESSION['cia_alta']['idadministrador']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_aseguradoras ORDER BY idaseguradora ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("aseguradora");
	$tpl->assign("id", $cat[$i]['idaseguradora']);
	$tpl->assign("nombre", $cat[$i]['nombre_aseguradora']);
	if (isset($_SESSION['cia_alta']) && $cat[$i]['idaseguradora'] == $_SESSION['cia_alta']['idaseguradora']) $tpl->assign("selected", "selected");
}

$cat = $db->query("SELECT * FROM catalogo_operadoras ORDER BY idoperadora ASC");
for ($i = 0; $i < count($cat); $i++) {
	$tpl->newBlock("operadora");
	$tpl->assign("id", $cat[$i]['idoperadora']);
	$tpl->assign("nombre", $cat[$i]['nombre_operadora']);
	if (isset($_SESSION['cia_alta']) && $cat[$i]['idoperadora'] == $_SESSION['cia_alta']['idoperadora']) $tpl->assign("selected", "selected");
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>