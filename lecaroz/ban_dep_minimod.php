<?php
// MODIFICACIÓN RÁPIDA DE DEPÓSITOS
// Tablas 'estado_cuenta'
// Menu 'No definido'

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

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/ban/ban_dep_minimod.tpl");
$tpl->prepare();

// Si ya se modificaron los datos, actualizar la base de datos
if (isset($_POST['id'])) {
	/**
	* [06-Ene-2011] Obtener datos del movimiento
	*/
	$sql = '
		SELECT
			*
		FROM
			estado_cuenta
		WHERE	
			id = ' . $_POST['id'] . '
	';
	
	$result = ejecutar_script($sql, $dsn);
	
	$sql = '
		UPDATE
			estado_cuenta
		SET
			fecha = \'' . $_POST['fecha'] . '\',
			cod_mov = ' . $_POST['cod_mov'] . '
		WHERE
			id = ' . $_POST['id'] . '
	' . ";\n";
	
	if ($result[0]['comprobante'] > 0) {
		$sql .= '
			UPDATE
				otros_depositos
			SET
				fecha = \'' . $_POST['fecha'] . '\'
			WHERE
				num_cia = ' . $result[0]['num_cia'] . '
				AND fecha = \'' . $result[0]['fecha'] . '\'
				AND concepto LIKE \'%' . $result[0]['comprobante'] . '%\'
		' . ";\n";
	}
	
	ejecutar_script($sql, $dsn);
	
	$tpl->newBlock("cerrar");
	
	$tpl->printToScreen();
	
	die;
}

// Obtener depósito
$sql = "SELECT id,concepto,importe,fecha_con,fecha,cod_mov FROM estado_cuenta WHERE estado_cuenta.id=$_GET[id]";
$deposito = ejecutar_script($sql,$dsn);

// Crear bloque de modificación
$tpl->newBlock("modificar");

// Trazar encabezado de pantalla
$tpl->assign("num_cia",$_SESSION['efe']['num_cia'.$_SESSION['efe']['next']]);
$tpl->assign("nombre_cia",$_SESSION['efe']['nombre_cia'.$_SESSION['efe']['next']]);

$tpl->assign("id",$_GET['id']);
$tpl->assign("concepto",$deposito[0]['concepto']);
$tpl->assign("importe",number_format($deposito[0]['importe'],2,".",","));
$tpl->assign("fecha_con",($deposito[0]['fecha_con'] != "")?$deposito[0]['fecha_con']:"&nbsp;");
$tpl->assign("fecha",$deposito[0]['fecha']);
// Obtener listado de códigos de movimientos
$cod_mov = ejecutar_script("SELECT cod_mov,descripcion FROM catalogo_mov_bancos WHERE tipo_mov='FALSE' GROUP BY cod_mov,descripcion ORDER BY cod_mov ASC",$dsn);
for ($i=0; $i<count($cod_mov); $i++) {
	$tpl->newBlock("cod_mov");
	$tpl->assign("cod_mov",$cod_mov[$i]['cod_mov']);
	$tpl->assign("descripcion",$cod_mov[$i]['descripcion']);
	if ($cod_mov[$i]['cod_mov'] == $deposito[0]['cod_mov']) $tpl->assign("selected","selected");
}

$tpl->printToScreen();
?>