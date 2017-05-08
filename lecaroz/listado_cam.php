<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

function color($placa) {
	$ultimo_digito = NULL;
	
	if ($placa == "")
		return 0;
	
	// Recorrer todos los caracteres de la placa
	for ($i = 0; $i < strlen($placa); $i++)
		if ($placa{$i} == "0" || $placa{$i} == "1" || $placa{$i} == "2" || $placa{$i} == "3" || $placa{$i} == "4" || $placa{$i} == "5" ||
			$placa{$i} == "6" || $placa{$i} == "7" || $placa{$i} == "8" || $placa{$i} == "9")
			$ultimo_digito = (int)$placa{$i};
	
	switch ($ultimo_digito) {
		case 5:
		case 6: $color = "./imagenes/amarillo.GIF"; break;
		case 7:
		case 8: $color = "./imagenes/rosa.GIF"; break;
		case 3:
		case 4: $color = "./imagenes/rojo.GIF"; break;
		case 1:
		case 2: $color = "./imagenes/verde.GIF"; break;
		case 9:
		case 0: $color = "./imagenes/azul.GIF"; break;
		default: $color = "./imagenes/blanco.GIF";
	}
	
	return $color;
}

function regresa_color($placa) {
	$ultimo_digito = NULL;
	
	if ($placa == "")
		return 0;
	
	// Recorrer todos los caracteres de la placa
	for ($i = 0; $i < strlen($placa); $i++)
		if ($placa{$i} == "0" || $placa{$i} == "1" || $placa{$i} == "2" || $placa{$i} == "3" || $placa{$i} == "4" || $placa{$i} == "5" ||
			$placa{$i} == "6" || $placa{$i} == "7" || $placa{$i} == "8" || $placa{$i} == "9")
			$ultimo_digito = (int)$placa{$i};
	
	switch ($ultimo_digito) {
		case 5:
		case 6: $color = 1; break;
		case 7:
		case 8: $color = 2; break;
		case 3:
		case 4: $color = 3; break;
		case 1:
		case 2: $color = 4; break;
		case 9:
		case 0: $color = 5; break;
		default: $color = 0;
	}
	
	return $color;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/doc/listado_cam.tpl" );
$tpl->prepare();

$condiciones = array();

$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

if ($_GET['num_cia'] > 0) {
	$condiciones[] = 'num_cia = ' . $_GET['num_cia'];
}

if ($_GET['idcamioneta'] > 0) {
	$condiciones[] = 'idcamioneta = ' . $_GET['idcamioneta'];
}

if ($_GET['entidad'] > 0) {
	$condiciones[] = 'entidad = ' . $_GET['entidad'];
}

if ($_GET['estatus'] > 0) {
	$condiciones[] = 'estatus = ' . $_GET['estatus'];
}

if ($_GET['idadmin'] > 0) {
	$condiciones[] = 'idadministrador = ' . $_GET['idadmin'];
}

if ($_GET['tipo_unidad'] > 0) {
	$condiciones[] = 'tipo_unidad = ' . $_GET['tipo_unidad'];
}

if ($_GET['color'] > 0) {
	$condiciones[] = 'color_placa(placas) = ' . $_GET['color'];
}

if (in_array($_SESSION['iduser'], array(52, 53, 54, 58, 59, 61))) {
	$condiciones[] = 'tipo_unidad != 3';
}

$sql = "
	SELECT
		idcamioneta,
		modelo,
		anio,
		placas,
		num_cia,
		nombre_corto,
		propietario,
		usuario,
		num_serie,
		num_motor
	FROM
		catalogo_camionetas
		LEFT JOIN catalogo_companias
			USING (num_cia)
	WHERE
		" . implode(' AND ', $condiciones) . "
	ORDER BY
		num_cia,
		idcamioneta
";

$result = $db->query($sql);

$db->desconectar();

// Dividir por verificación
if ($_GET['color'] > 0) {
	$temp = array();
	
	for ($i = 0; $i < count($result); $i++)
		if (regresa_color($result[$i]['placas']) == $_GET['color'])
			$temp[] = $result[$i];
	
	if (count($temp) > 0)
		$result = $temp;
	else
		$result = FALSE;
}

for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("id", $result[$i]['idcamioneta']);
	$tpl->assign("modelo", $result[$i]['modelo']);
	$tpl->assign("anio", $result[$i]['anio']);
	$tpl->assign("color", color($result[$i]['placas']));
	$tpl->assign("placas", $result[$i]['placas']);
	$tpl->assign("num_cia", $result[$i]['num_cia']);
	$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
	$tpl->assign("propietario", $result[$i]['propietario'] != "" ? $result[$i]['propietario'] : "&nbsp;");
	$tpl->assign("usuario", $result[$i]['usuario'] != "" ? $result[$i]['usuario'] : "&nbsp;");
	$tpl->assign("num_serie", $result[$i]['num_serie'] != "" ? $result[$i]['num_serie'] : "&nbsp;");
	$tpl->assign("num_motor", $result[$i]['num_motor'] != "" ? $result[$i]['num_motor'] : "&nbsp;");
}

$tpl->printToScreen();
?>