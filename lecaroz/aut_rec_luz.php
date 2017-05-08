<?php
// CARTA DE CERTIFICACION DE CHEQUES
// Tablas ''
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/cheques.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

// ---------------------------------- Insertar datos en tablas -----------------------------------------------

$sql = "SELECT nombre, clabe_cuenta2, folio, importe, a_nombre, fecha, cuenta FROM estado_cuenta LEFT JOIN cheques USING (num_cia, folio, cuenta, importe, fecha) LEFT JOIN catalogo_companias";
$sql .= " USING (num_cia) WHERE cheques.num_proveedor = 216 AND fecha_con IS NULL AND cuenta = 2 ORDER BY num_cia";
$result = $db->query($sql);

$tpl = new TemplatePower( "./plantillas/ban/aut_rec_luz.tpl" );
$tpl->prepare();

foreach ($result as $row) {
	$tpl->newBlock("carta");
	$tpl->assign("banco", $row['cuenta'] == 1 ? "BANCO MERCANTIL DEL NORTE S.A." : "BANCO SANTANDER SERFIN, S.A.");
	$tpl->assign("nombre_cia", $row['nombre']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", date("d/m/Y"), $fecha);
	$tpl->assign("dia", $fecha[1]);
	$tpl->assign("mes", mes_escrito($fecha[2],TRUE));
	$tpl->assign("anio", $fecha[3]);
	$tpl->assign("folio", $row['folio']);
	$tpl->assign("importe", number_format($row['importe'],2,".",","));
	$tpl->assign("importe_escrito", num2string($row['importe']));
	$tpl->assign("cuenta", $row['clabe_cuenta2']);
	$tpl->assign("a_nombre", $row['a_nombre']);
}
$tpl->printToScreen();
die;
?>