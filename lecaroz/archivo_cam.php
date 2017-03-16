<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

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
		num_motor,
		tipo_combustible,
		entidad,
		estatus,
		fecha_venta,
		clave_vehicular,
		num_poliza,
		inciso,
		plan,
		localizacion_fac,
		vencimiento,
		ven_poliza,
		cambio_motor
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

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=vehiculos.csv");

echo '"NO.","MODELO","AÑO","PLACAS","CIA.","NOMBRE","PROPIETARIO","USUARIO","N0. SERIE","NO. MOTOR","COMBUSTIBLE","ENTIDAD","ESTATUS","FECHA DE VENTA","CLAVE VEHICULAR","NO. POLIZA","VENCIMIENTO POLIZA","INCISO","PLAN","LOCALIZACION","VENCIMIENTO","CAMBIO MOTOR"' . "\n";

for ($i = 0; $i < count($result); $i++) {
	echo "\"{$result[$i]['idcamioneta']}\",";
	echo "\"{$result[$i]['modelo']}\",";
	echo "\"{$result[$i]['anio']}\",";
	echo "\"{$result[$i]['placas']}\",";
	echo "\"{$result[$i]['num_cia']}\",";
	echo "\"{$result[$i]['nombre_corto']}\",";
	echo "\"{$result[$i]['propietario']}\",";
	echo "\"{$result[$i]['usuario']}\",";
	echo "\"{$result[$i]['num_serie']}\",";
	echo "\"{$result[$i]['num_motor']}\",";
	echo "\"" . ($result[$i]['tipo_combustible'] == "t" ? "GASOLINA" : "GAS") . "\",";
	echo "\"" . ($result[$i]['entidad'] == 1 ? "DISTRITO FEDERAL" : ($result[$i]['entidad'] == 2 ? "ESTADO DE MEXICO" : ($result[$i]['entidad'] == 3 ? "MORELOS" : "OTRO"))) . "\",";
	echo "\"" . ($result[$i]['estatus'] == 1 ? "EN USO" : ($result[$i]['estatus'] == 2 ? "VENDIDA" : "ROBADA")) . "\",";
	echo "\"{$result[$i]['fecha_venta']}\",";
	echo "\"{$result[$i]['clave_vehicular']}\",";
	echo "\"{$result[$i]['num_poliza']}\",";
	echo "\"{$result[$i]['ven_poliza']}\",";
	echo "\"{$result[$i]['inciso']}\",";
	echo "\"{$result[$i]['plan']}\",";
	echo "\"{$result[$i]['localizacion_fac']}\",";
	echo "\"{$result[$i]['vencimiento']}\",";
	echo "\"" . ($result[$i]['cambio_motor'] == "t" ? "SI" : "NO") . "\"\n";
}
?>