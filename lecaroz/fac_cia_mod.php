<?php
// MODIFICACION DE COMPAÑÍAS
// Tabla 'catalogo_companias'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_cia_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Obtener datos de la compañía a modificar ---------------------------------
if (isset($_GET['actualizar'])) {
	// Generar script de actualización
	$sql  = "UPDATE catalogo_companias SET ";
	$sql .= "nombre = '$_POST[nombre]', ";
	$sql .= "direccion = '$_POST[direccion]', ";
	$sql .= "rfc = '$_POST[rfc]', ";
	$sql .= "no_imss = '$_POST[no_imss]', ";
	$sql .= "no_infonavit = '$_POST[no_infonavit]', ";
	$sql .= "telefono = '$_POST[telefono]', ";
	$sql .= "contrato_recoleccion = '$_POST[contrato_recoleccion]', ";
	$deudores = ($_POST['sub_cuenta_deudores'] > 0) ? $_POST['sub_cuenta_deudores'] : "NULL";
	//$sql .= "sub_cuenta_deudores = $_POST[sub_cuenta_deudores], ";
		$sql .= "sub_cuenta_deudores = $deudores, ";
	$sql .= "no_cta_cia_luz = '$_POST[no_cta_cia_luz]', ";
	$sql .= "persona_fis_moral = '$_POST[persona_fis_moral]', ";
	$sql .= "nombre_corto = '$_POST[nombre_corto]', ";
	$sql .= "idadministrador = $_POST[idadministrador], ";
	$sql .= "idaseguradora = $_POST[idaseguradora], ";
	$sql .= "idauditor = $_POST[idauditor], ";
	$sql .= "idcontador = $_POST[idcontador], ";
	$sql .= "iddelimss = $_POST[iddelimss], ";
	$sql .= "idoperadora = $_POST[idoperadora], ";
	$sql .= "idsindicato = $_POST[idsindicato], ";
	$sql .= "idsubdelimss = $_POST[idsubdelimss], ";
	$gas = ($_POST['cod_gasolina'] != "") ? $_POST['cod_gasolina'] : "NULL";
	//$sql .= "cod_gasolina = $_POST[cod_gasolina], ";
		$sql .= "cod_gasolina = $gas, ";
	$sql .= "clabe_banco = '$_POST[clabe_banco]', ";
	$sql .= "clabe_plaza = '$_POST[clabe_plaza]', ";
	$sql .= "clabe_cuenta = '$_POST[clabe_cuenta]', ";
	$sql .= "clabe_identificador = '$_POST[clabe_identificador]', ";
	$sql .= "idbancos = $_POST[idbancos], ";
	$sql .= "email = '$_POST[email]', ";
	$sql .= "homo_clave = '$_POST[homo_clave]', ";
	$sql .= "aplica_iva = '$_POST[aplica_iva]', ";
	$sql .= "num_cia_primaria = ".(($_POST['homoclave'] != "")?$_POST['homoclave']:$_POST['num_cia']).", ";
	$sql .= "num_proveedor = ".(($_POST['num_proveedor'] != "")?$_POST['num_proveedor']:"NULL")." ";
	$sql .= "WHERE num_cia = $_POST[num_cia]";
	// Actualizar compañía
	ejecutar_script($sql,$dsn);
	if ($_POST['homo_clave'] == "TRUE") {
		if (existe_registro("dependencia_cia",array("cia_secundaria"),array($_POST['num_cia']),$dsn)) {
			$sql = "UPDATE dependencia_cia SET cia_primaria = $_POST[homoclave] WHERE cia_secundaria = $_POST[num_cia]";
			ejecutar_script($sql,$dsn);
		}
		else {
			$sql = "INSERT INTO dependencia_cia (cia_primaria,cia_secundaria) VALUES ($_POST[homoclave],$_POST[num_cia])";
			ejecutar_script($sql,$dsn);
		}
	}
	else {
		if (existe_registro("dependencia_cia",array("cia_secundaria"),array($_POST['num_cia']),$dsn))
			ejecutar_script("DELETE FROM dependencia_cia WHERE cia_secundaria = $_POST[num_cia]",$dsn);
	}
	
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
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}

// -------------------------------- Modificar -------------------------------------------------------
$sql  = "SELECT ";
$sql .= "* ";
$sql .= "FROM catalogo_companias ";
$sql .= "WHERE num_cia = $_GET[num_cia] ";
$sql .= "ORDER BY num_cia ASC";

$result = ejecutar_script($sql,$dsn);
if (!$result) {
	header("location: ./fac_cia_mod.php?mensaje=No+hay+resultados");
	die;
}

$tpl->newBlock("modificar");

$tpl->assign("num_cia",$result[0]['num_cia']);
$tpl->assign("nombre",$result[0]['nombre']);
$tpl->assign("nombre_corto",$result[0]['nombre_corto']);
$tpl->assign("direccion",$result[0]['direccion']);
$tpl->assign("telefono",$result[0]['telefono']);
$tpl->assign("email",$result[0]['email']);
$tpl->assign("rfc",$result[0]['rfc']);
$tpl->assign("num_cia_primaria",($result[0]['num_cia_primaria'] != $result[0]['num_cia'])?$result[0]['num_cia_primaria']:"");
$tpl->assign("num_proveedor",$result[0]['num_proveedor']);
$tpl->assign("cod_gasolina",$result[0]['cod_gasolina']);

// Generar listado de bancos
$banco = ejecutar_script("SELECT * FROM catalogo_bancos ORDER BY idbancos ASC",$dsn);
for ($i=0; $i<count($banco); $i++) {
	$tpl->newBlock("banco");
	$tpl->assign("idbanco",$banco[$i]['idbancos']);
	$tpl->assign("namebanco",$banco[$i]['nom_banco']);
	if ($banco[$i]['idbancos'] == $result[0]['idbancos'])
		$tpl->assign("checked","selected");
}

$tpl->gotoBlock("modificar");
// Assignar CLABE
$tpl->assign("clabe_banco",$result[0]['clabe_banco']);
$tpl->assign("clabe_plaza",$result[0]['clabe_plaza']);
$tpl->assign("clabe_cuenta",$result[0]['clabe_cuenta']);
$tpl->assign("clabe_identificador",$result[0]['clabe_identificador']);

if ($result[0]['persona_fis_moral'] == "f")
	$tpl->newBlock("persona_moral");
else if ($result[0]['persona_fis_moral'] == "t")
	$tpl->newBlock("persona_fisica");

$tpl->gotoBlock("modificar");
$tpl->assign("no_imss",$result[0]['no_imss']);
$tpl->assign(($result[0]['aplica_iva'] == "t")?"checked_true":"checked_false","checked");

$delimss = ejecutar_script("SELECT * FROM catalogo_del_imss ORDER BY iddelimss ASC",$dsn);
for ($i=0; $i<count($delimss); $i++) {
	$tpl->newBlock("delimss");
	$tpl->assign("iddelimss",$delimss[$i]['iddelimss']);
	$tpl->assign("namedelimss",$delimss[$i]['nombre_del_imss']);
	if ($delimss[$i]['iddelimss'] == $result[0]['iddelimss'])
		$tpl->assign("checked","selected");
}

$subdelimss = ejecutar_script("SELECT * FROM catalogo_subdel_imss ORDER BY idsubdelimss ASC",$dsn);
for ($i=0; $i<count($subdelimss); $i++) {
	$tpl->newBlock("subdelimss");
	$tpl->assign("idsubdelimss",$subdelimss[$i]['idsubdelimss']);
	$tpl->assign("namesubdelimss",$subdelimss[$i]['nombre_subdel_imss']);
	if ($subdelimss[$i]['idsubdelimss'] == $result[0]['idsubdelimss'])
		$tpl->assign("checked","selected");
}

$tpl->gotoBlock("modificar");
$tpl->assign("no_infonavit",$result[0]['no_infonavit']);
$tpl->assign("no_luz",$result[0]['no_cta_cia_luz']);
$tpl->assign("contrato",$result[0]['contrato_recoleccion']);
$tpl->assign("deudores",$result[0]['sub_cuenta_deudores']);

$contador = ejecutar_script("SELECT * FROM catalogo_contadores ORDER BY idcontador ASC",$dsn);
for ($i=0; $i<count($contador); $i++) {
	$tpl->newBlock("contador");
	$tpl->assign("idcontador",$contador[$i]['idcontador']);
	$tpl->assign("namecontador",$contador[$i]['nombre_contador']);
	if ($contador[$i]['idcontador'] == $result[0]['idcontador'])
		$tpl->assign("checked","selected");
}

$admon = ejecutar_script("SELECT * FROM catalogo_administradores",$dsn);
for ($i=0; $i<count($admon); $i++) {
	$tpl->newBlock("administrador");
	$tpl->assign("idadministrador",$admon[$i]['idadministrador']);
	$tpl->assign("nameadministrador",$admon[$i]['nombre_administrador']);
	if ($admon[$i]['idadministrador'] == $result[0]['idadministrador'])
		$tpl->assign("checked","selected");
}

$auditor = ejecutar_script("SELECT * FROM catalogo_auditores ORDER BY idauditor ASC",$dsn);
for ($i=0; $i<count($auditor); $i++) {
	$tpl->newBlock("auditor");
	$tpl->assign("idauditor",$auditor[$i]['idauditor']);
	$tpl->assign("nameauditor",$auditor[$i]['nombre_auditor']);
	if ($auditor[$i]['idauditor'] == $result[0]['idauditor'])
		$tpl->assign("checked","selected");
}

$ase = ejecutar_script("SELECT * FROM catalogo_aseguradoras ORDER BY idaseguradora ASC",$dsn);
for ($i=0; $i<count($ase); $i++) {
	$tpl->newBlock("aseguradora");
	$tpl->assign("idaseguradora",$ase[$i]['idaseguradora']);
	$tpl->assign("nameaseguradora",$ase[$i]['nombre_aseguradora']);
	if ($ase[$i]['idaseguradora'] == $result[0]['idaseguradora'])
		$tpl->assign("checked","selected");
}

$sin = ejecutar_script("SELECT * FROM catalogo_sindicatos ORDER BY idsindicato ASC",$dsn);
for ($i=0; $i<count($sin); $i++) {
	$tpl->newBlock("sindicato");
	$tpl->assign("idsindicato",$sin[$i]['idsindicato']);
	$tpl->assign("namesindicato",$sin[$i]['nombre_sindicato']);
	if ($sin[$i]['idsindicato'] == $result[0]['idsindicato'])
		$tpl->assign("checked","selected");
}

$ope = ejecutar_script("SELECT * FROM catalogo_operadoras ORDER BY idoperadora",$dsn);
for ($i=0; $i<count($ope); $i++) {
	$tpl->newBlock("operadora");
	$tpl->assign("idoperadora",$ope[$i]['idoperadora']);
	$tpl->assign("nameoperadora",$ope[$i]['nombre_operadora']);
	if ($ope[$i]['idoperadora'] == $result[0]['idoperadora'])
		$tpl->assign("checked","selected");
}

$tpl->gotoBlock("modificar");
$tpl->assign("gasolina",$result[0]['cod_gasolina']);

if ($result[0]['homo_clave'] == "t") {
	$dep = ejecutar_script("SELECT * FROM dependencia_cia WHERE cia_secundaria = $_GET[num_cia]",$dsn);
	$tpl->assign("homo_clave","TRUE");
	$tpl->assign("homoclave",$dep[0]['cia_primaria']);
}

$tpl->printToScreen();
?>