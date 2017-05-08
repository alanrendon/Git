<?php
// LISTADO DE COMPAÑIAS
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
$tpl->assignInclude("body","./plantillas/fac/fac_cia_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Tipo de listado -------------------------------------------------------
if (!isset($_GET['listado'])) {
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

// -------------------------------- Consulta -------------------------------------------------------
$sql  = "
SELECT
	num_cia,
	catalogo_companias.nombre
		AS nombre,
	nombre_corto,
	direccion,
	telefono,
	catalogo_companias.email,
	rfc,
	clabe_banco,
	clabe_plaza,
	clabe_cuenta,
	clabe_identificador,
	persona_fis_moral,
	no_imss,
	iddelimss,
	nombre_del_imss,
	idsubdelimss,
	nombre_subdel_imss,
	no_infonavit,
	no_cta_cia_luz,
	sub_cuenta_deudores,
	idcontador,
	nombre_contador,
	idadministrador,
	nombre_administrador,
	idauditor,
	nombre_auditor,
	idaseguradora,
	nombre_aseguradora,
	idsindicato,
	nombre_sindicato,
	idoperadora,
	nombre_operadora,
	cod_gasolina
FROM
	catalogo_companias
	JOIN catalogo_del_imss
		USING (iddelimss)
	JOIN catalogo_subdel_imss
		USING (idsubdelimss)
	JOIN catalogo_contadores
		USING (idcontador)
	JOIN catalogo_administradores
		USING (idadministrador)
	JOIN catalogo_auditores
		USING (idauditor)
	JOIN catalogo_aseguradoras
		USING (idaseguradora)
	JOIN catalogo_sindicatos
		USING (idsindicato)
	JOIN catalogo_operadoras
		USING (idoperadora)";
// De una sola compañía
if ($_GET['listado'] == "cia" && isset($_GET['num_cia']))
	$sql .= "
		WHERE num_cia = $_GET[num_cia]
	";
$sql .= "
	ORDER BY num_cia ASC
";

$result = ejecutar_script($sql,$dsn);
if (!$result) {
	header("location: ./fac_cia_con.php?mensaje=No+hay+resultados");
	die;
}

$tpl->newBlock("listado");

for ($i=0; $i<count($result); $i++) {
	$tpl->newBlock("bloque");
	$tpl->assign("num_cia",$result[$i]['num_cia']);
	$tpl->assign("nombre_cia",$result[$i]['nombre']);
	$tpl->assign("nombre_corto",$result[$i]['nombre_corto']);
	$tpl->assign("direccion",$result[$i]['direccion']);
	$tpl->assign("telefono",$result[$i]['telefono']);
	$tpl->assign("email",$result[$i]['email']);
	$tpl->assign("rfc",$result[$i]['rfc']);
	$tpl->assign("banco",/*$result[$i]['idbancos']." ".$result[$i]['nom_banco']*/'&nbsp;');
	$tpl->assign("clabe",$result[$i]['clabe_banco']." ".$result[$i]['clabe_plaza']." ".$result[$i]['clabe_cuenta']." ".$result[$i]['clabe_identificador']);
	if ($result[$i]['persona_fis_moral'] == "f")
		$tpl->assign("tipo","MORAL");
	else if ($result[$i]['persona_fis_moral'] == "t")
		$tpl->assign("tipo","FISICA");
	$tpl->assign("no_imss",$result[$i]['no_imss']);
	$tpl->assign("del_imss",$result[$i]['iddelimss']." ".$result[$i]['nombre_del_imss']);
	$tpl->assign("sub_imss",$result[$i]['idsubdelimss']." ".$result[$i]['nombre_subdel_imss']);
	$tpl->assign("no_infonavit",$result[$i]['no_infonavit']);
	$tpl->assign("no_luz",$result[$i]['no_cta_cia_luz']);
	$tpl->assign("contrato",/*$result[$i]['contrato_recoleccion']*/'&nbsp;');
	$tpl->assign("deudores",$result[$i]['sub_cuenta_deudores']);
	$tpl->assign("contador",$result[$i]['idcontador']." ".$result[$i]['nombre_contador']);
	$tpl->assign("administrador",$result[$i]['idadministrador']." ".$result[$i]['nombre_administrador']);
	$tpl->assign("auditor",$result[$i]['idcontador']." ".$result[$i]['nombre_contador']);
	$tpl->assign("aseguradora",$result[$i]['idaseguradora']." ".$result[$i]['nombre_aseguradora']);
	$tpl->assign("sindicato",$result[$i]['idsindicato']." ".$result[$i]['nombre_sindicato']);
	$tpl->assign("operadora",$result[$i]['idoperadora']." ".$result[$i]['nombre_operadora']);
	$tpl->assign("gasolina",$result[$i]['cod_gasolina']);
}
$tpl->printToScreen();
?>