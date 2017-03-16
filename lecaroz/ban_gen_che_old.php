<?php
// GENERACIÓN E IMPRESIÓN DE CHEQUES
// Tabla 'cheques'
// Menu ''

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/cheques.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay cheques por imprimir";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/cheque.tpl" );
$tpl->prepare();

// Generar script sql
$sql  = "SELECT id,num_cia,catalogo_companias.rfc AS rfc,catalogo_companias.clabe_cuenta AS cuenta,catalogo_companias.nombre AS nombre_cia,num_proveedor,fecha,folio,a_nombre,concepto,importe,facturas,para_abono ";
$sql .= "FROM cheques JOIN catalogo_companias USING (num_cia) JOIN catalogo_proveedores USING (num_proveedor) WHERE imp = 'FALSE'";
// Por folio
if ($_POST['tipo'] == "folio")
	$sql .= " AND folio >= $_POST[param1] AND folio <= $_POST[param2] AND num_cia = $_POST[param3]";
// Por compañía
else if ($_POST['tipo'] == "cia")
	$sql .= " AND num_cia = $_POST[param1]";
// Por proveedor
else if ($_POST['tipo'] == "proveedor")
	$sql .= " AND num_proveedor = $_POST[param1]";
// Por fecha
else if ($_POST['tipo'] == "fecha")
	$sql .= " AND fecha >= '$_POST[param1]' AND fecha <= '$_POST[param2]'";
// Organizar registros
$sql .= " ORDER BY a_nombre,num_cia,folio ASC";

// Obtener registros
$reg = ejecutar_script($sql,$dsn);

// Si no hay resultados, cerrar la ventana
if (!$reg) {
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Declarar variables
$num_cheque1 = $_POST['num_cheque'];						// Folio inicial de los cheques (ficha)
$num_cheque2 = $_POST['num_cheque'] + count($reg) - 1;		// Folio final de los cheques (ficha)
$num_cheque  = ($_POST['orden'] == "asc")?$num_cheque1:$num_cheque2;	// Folio de los cheques (ficha)

$strIni    = "&%STHPASSWORD$";				// Cadena que inicia el modo MICR de la impresora
$strImpIni = "&%STP12500$&%1B$(12500X$";	// Cadena de inicio de impresión de importe con protección especial
$strImpFin = "&%$";							// Cadena de fin de impresión de importe con protección especial
$strBanIni = "&%SMD";						// Cadena de inicio de impresión de banda MICR
$strBanFin = "$";							// Cadena de fin de impresión de banda MICR

$numBanco          = "072";
$codSeguridad      = "000";
$claveTransaccion  = "51";
$plazaCompensacion = "115";

// Generar los cheques
for ($i=0; $i<count($reg); $i++) {
	$tpl->newBlock("cheque");
	// Datos generales
	$tpl->assign("nombre_cia",$reg[$i]['nombre_cia']);
	$tpl->assign("rfc",strRFC($reg[$i]['rfc']));
	$tpl->assign("fecha",strFecha($reg[$i]['fecha']));
	$tpl->assign("a_nombre",$reg[$i]['a_nombre']);
	$tpl->assign("importe_escrito",num2string($reg[$i]['importe']));
	$tpl->assign("");
	
	// Generar banda MICR
	$bandaMICR   = bandaMICR($numBanco,$reg[$i]['cuenta'],$reg[$i]['folio'],$codSeguridad,$claveTransaccion,$plazaCompensacion);
	$pseudoBanda = pseudoBanda($bandaMICR,$reg[$i]['importe']);
	
	// Assignar cadenas de inicio
	$tpl->assign("strini",$strIni);
	$tpl->assign("strimpini",$strImpIni);
	$tpl->assign("strimpfin",$strImpFin);
	$tpl->assign("strbanini",$strBanIni);
	$tpl->assign("strbanfin",$strBanFin);
	
	// Asignar banda MICR y pseudo banda
	$tpl->assign("banda_micr",$bandaMICR);
	$tpl->assign("pseudo_banda",$pseudoBanda);
	
	// Asignar importe formateado
	$tpl->assign("importe",number_format($reg[$i]['importe'],2,".",","));
	
	// Asignar facturas
	$tpl->assign("facturas",$reg[$i]['facturas']);
	// Asignar concepto
	$tpl->assign("concepto",$reg[$i]['concepto']);
	
	// Crear bloques de abono a cuenta
	if ($reg[$i]['para_abono'] == "t") {
		$tpl->newBlock("para_abono");
		$tpl->newBlock("para_abono_cheque");
	}
	
	// Actualizar base de datos
	ejecutar_script("UPDATE cheques SET imp = 'TRUE', num_cheque = $num_cheque WHERE id = ".$reg[$i]['id'],$dsn);
	// Incrementar número de cheque
	if ($_POST['orden'] == "asc")
		$num_cheque++;
	else
		$num_cheque--;
}

if (isset($_POST['automatico'])) {
	$tpl->newBlock("imprimir");
	$tpl->assign("num_cheque1",$num_cheque1);
	$tpl->assign("num_cheque2",$num_cheque2);
}
else {
	$tpl->newBlock("no_imprimir");
	$tpl->assign("num_cheque1",$num_cheque1);
	$tpl->assign("num_cheque2",$num_cheque2);
}

$tpl->printToScreen();
?>