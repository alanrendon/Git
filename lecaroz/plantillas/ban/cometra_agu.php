<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$tpl = new TemplatePower( "./plantillas/ban/cometra3.tpl" );
$tpl->prepare();

$last_porc = $db->query("SELECT * FROM porcentaje_aguinaldo ORDER BY id DESC LIMIT 1");

$sql = "SELECT catalogo_companias.num_cia AS num_cia, catalogo_companias.nombre AS nombre_cia, catalogo_companias.clabe_cuenta AS cuenta, catalogo_companias.direccion AS direccion, sum(aguinaldos.importe) AS importe";
$sql .= " FROM aguinaldos LEFT JOIN catalogo_trabajadores ON (catalogo_trabajadores.id=aguinaldos.id_empleado) LEFT JOIN catalogo_companias ON (catalogo_companias.num_cia=catalogo_trabajadores.num_cia)";
$sql .= " WHERE aguinaldos.fecha = '{$last_porc[0]['fecha_aguinaldo']}' GROUP BY catalogo_companias.num_cia, catalogo_companias.nombre, catalogo_companias.clabe_cuenta, catalogo_companias.direccion ORDER BY catalogo_companias.num_cia LIMIT 5";
$result = $db->query($sql);

$total_aguinaldos  = 0;
$num_empleados = 0;
for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("ficha");
	$tpl->assign("num_cia", $result[$i]['num_cia']);
	$tpl->assign("nombre_cia", $result[$i]['nombre']);
	$tpl->assign("cuenta", $result[$i]['clabe_cuenta']);
	$tpl->assign("direccion", strlen($result[$i]['direccion']) > 54 ? substr($result[$i]['direccion'], 0, 54) : $result[$i]['direccion']);
	$tpl->assign("importe", number_format($result[$i]['importe']));
}

$tpl->printToScreen();

?>