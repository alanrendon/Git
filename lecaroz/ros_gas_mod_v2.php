<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

$users = array(28, 29, 30, 31, 32);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ros/ros_gas_mod_v2.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	// Validar fecha
	$id = $_POST['id'];
	$num_cia = $_POST['num_cia'];
	$fecha = $_POST['fecha'];
	$codgastos = $_POST['codgastos'];
	$cod_turno = $_POST['cod_turno'] > 0 ? $_POST['cod_turno'] : "NULL";

	$tmp = $db->query("SELECT tipo_cia FROM catalogo_companias WHERE num_cia = {$num_cia}");

	$tipo_cia = $tmp[0]['tipo_cia'];

	// Validar fecha
	$tmp = $db->query("SELECT mes, anio FROM balances_pan WHERE num_cia = $num_cia ORDER BY anio DESC, mes DESC LIMIT 1");
	$ts_bal = $tmp ? mktime(0, 0, 0, $tmp[0]['mes'] + 1, 0, $tmp[0]['anio']) : mktime(0, 0, 0, date("n") - 1, 0, date("Y"));
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $tmp);
	$ts_cap = mktime(0, 0, 0, $tmp[2], $tmp[1], $tmp[3]);
	$ts_now = mktime(0, 0, 0, date("n"), date("d") - 1, date("Y"));

	if ($ts_cap <= $ts_bal && !in_array($_SESSION['iduser'], array(1, 30))) {
		$tpl->newBlock("valid");
		$tpl->assign("mensaje", "La fecha no puede ser del mes pasado porque ya se generaron balances");
		$tpl->assign("campo", "fecha");
		$tpl->printToScreen();
		die;
	}
	else if ($ts_cap > $ts_now) {
		$tpl->newBlock("valid");
		$tpl->assign("mensaje", "No se puede modificar la fecha para dias posteriores al de ayer");
		$tpl->assign("campo", "fecha");
		$tpl->printToScreen();
		die;
	}

	$sql = '
		SELECT
			num_cia,
			fecha,
			importe
		FROM
			movimiento_gastos
		WHERE
			idmovimiento_gastos = ' . $id . '
	';

	$old = $db->query($sql);

	$sql = "UPDATE movimiento_gastos SET codgastos = $codgastos, fecha = '$fecha', cod_turno = $cod_turno WHERE idmovimiento_gastos = $id;\n";
	$db->query($sql);

	if ($fecha != $old[0]['fecha']) {
		$sql = '
			UPDATE
				' . ($tipo_cia == 4 ? 'total_zapaterias' : ($tipo_cia == 1 ? 'total_panaderias' : 'total_companias')) . '
			SET
				gastos = gastos - ' . $old[0]['importe'] . ',
				efectivo = efectivo + ' . $old[0]['importe'] . '
			WHERE
				num_cia = ' . $old[0]['num_cia'] . '
				AND fecha = \'' . $old[0]['fecha'] . '\';

			UPDATE
				' . ($tipo_cia == 4 ? 'total_zapaterias' : ($tipo_cia == 1 ? 'total_panaderias' : 'total_companias')) . '
			SET
				gastos = gastos + ' . $old[0]['importe'] . ',
				efectivo = efectivo - ' . $old[0]['importe'] . '
			WHERE
				num_cia = ' . $old[0]['num_cia'] . '
				AND fecha = \'' . $fecha . '\';
		';

		$db->query($sql);
	}

	$tpl->newBlock("cerrar");
	$tpl->assign("codgastos", $codgastos);
	$tpl->printToScreen();
	die;
}

$sql = "SELECT idmovimiento_gastos AS id, num_cia, cc.nombre_corto, codgastos, cg.descripcion AS desc, fecha, cod_turno, concepto, importe FROM movimiento_gastos LEFT JOIN";
$sql .= " catalogo_companias AS cc USING (num_cia) LEFT JOIN catalogo_gastos AS cg USING (codgastos) WHERE idmovimiento_gastos = $_GET[id]";
$result = $db->query($sql);

$tpl->newBlock("datos");
$tpl->assign("id", $result[0]['id']);
$tpl->assign("num_cia", $result[0]['num_cia']);
$tpl->assign("nombre", $result[0]['nombre_corto']);
$tpl->assign("codgastos", $result[0]['codgastos']);
$tpl->assign("desc", $result[0]['desc']);
$tpl->assign("fecha", $result[0]['fecha']);
$tpl->assign("concepto", $result[0]['concepto'] != "" ? $result[0]['concepto'] : "&nbsp;");
$tpl->assign("importe", number_format($result[0]['importe'], 2, ".", ","));

if ($db->query('
	SELECT
		idoperadora
	FROM
		catalogo_operadoras
	WHERE
		iduser IS NOT NULL
		AND iduser = ' . $_SESSION['iduser'] . '
')) {
	$tpl->assign('readonly', ' readonly="readonly"');
}

if ($_SESSION['tipo_usuario'] == 1) {
	$tpl->newBlock("bturno");
	$turnos = $db->query("SELECT cod_turno, descripcion AS nombre FROM catalogo_turnos WHERE cod_turno IN (1, 2, 3, 4, 8, 9, 10) ORDER BY cod_turno");
	foreach ($turnos as $turno) {
		$tpl->newBlock("turno");
		$tpl->assign("cod", $turno['cod_turno']);
		$tpl->assign("nombre", $turno['nombre']);
		if ($turno['cod_turno'] == $result[0]['cod_turno']) $tpl->assign("selected", " selected");
	}
}

$gastos = $db->query("SELECT codgastos, descripcion AS desc FROM catalogo_gastos ORDER BY codgastos");
foreach ($gastos as $gasto) {
	$tpl->newBlock("gasto");
	$tpl->assign("codgastos", $gasto['codgastos']);
	$tpl->assign("desc", $gasto['desc']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
