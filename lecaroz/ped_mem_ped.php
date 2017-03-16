<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['idadministrador'])) {
	$sql = '
		SELECT
			num_cia,
			nombre,
			nombre_administrador
		FROM
				catalogo_companias
			LEFT JOIN
				catalogo_administradores
					USING
						(
							idadministrador
						)
		WHERE
			num_cia <= 300
	';
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['idadministrador'] > 0 ? " AND idadministrador = $_GET[idadministrador]" : "";
	$sql .= '
		ORDER BY
			idadministrador,
			num_cia
	';
	$result = $db->query($sql);
	
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/ped/memo_pedido_v3.tpl" );
	$tpl->prepare();
	
	if (!$result) {
		$tpl->printToScreen("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	$mes = mes_escrito($_GET['mes'], TRUE);
	$anio = $_GET['anio'];
	$fecha = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
	
	// [06-Abr-2010] Obtener ultima fecha de la tabla de consumos
	$sql = '
		SELECT
			anio,
			mes
		FROM
			consumos_mensuales
		ORDER BY
			anio
				DESC,
			mes
				DESC
		LIMIT
			1
	';
	$last = $db->query($sql);//print_r($last);
	
	$month = /*date('n', mktime(0, 0, 0, date($_GET['mes']), 0, $_GET['anio']))*/$last[0]['mes'];
	$year = /*date('Y', mktime(0, 0, 0, $_GET['mes'], 0, $_GET['anio']))*/$last[0]['anio'];
	
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $year));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $month + 1, 0, $year));
	
	$text1 = "lo que requiera para este mes. No es valido si no incluye existencia";
	$text2 = "<font color=\"#CC0000\"><strong>complementarios</strong></font> para el mes en <font color=\"#CC0000\"><strong>curso</strong></font>";
	
	// [07-Oct-2009] Obtener promedio de consumos
	$sql = '
		SELECT
			num_cia,
			cc.nombre_corto
				AS
					nombre_cia,
			codmp,
			cmp.nombre
				AS
					producto,
			(
				SELECT
					sum(
						CASE
							WHEN codmp = 1 THEN
								cantidad / 44
							WHEN codmp IN (3, 4) THEN
								cantidad / 50
							ELSE
								cantidad
						END
					) / ' . $month . '
				FROM
					mov_inv_real
				WHERE
						fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
					AND
						tipo_mov = FALSE
					AND
						descripcion <> \'DIFERENCIA INVENTARIO\'
					AND
						cantidad > 0
					AND
						num_cia = cc.num_cia
					AND
						codmp = cmp.codmp
			)
				AS
					consumo
		FROM
			catalogo_companias
				cc,
			catalogo_mat_primas
				cmp
		WHERE
			codmp
				IN
					(
						86,
						6,
						365,
						18,
						702,
						603,
						19,
						213,
						7,
						158,
						432,
						27,
						71,
						12,
						8,
						28,
						26,
						112,
						664,
						282,
						658,
						661,
						660,
						663,
						159,
						91,
						92,
						33,
						13,
						72,
						141,
						225,
						157,
						38,
						39,
						428,
						322,
						77,
						180,
						43,
						42,
						251,
						14,
						16,
						270,
						49,
						44,
						175,
						232,
						271,
						45,
						47,
						79,
						80,
						81,
						51,
						53,
						56,
						113,
						58,
						88,
						87,
						59,
						197,
						164,
						65,
						67,
						469,
						466,
						456,
						458,
						459,
						465,
						130,
						138,
						131,
						139,
						132,
						140,
						133,
						134,
						135,
						142,
						136,
						143,
						137,
						144,
						125,
						128,
						168,
						167,
						215,
						147,
						171,
						319,
						287,
						276,
						277,
						278,
						429,
						430,
						431,
						657,
						576,
						571,
						572,
						653,
						320,
						249,
						54,
						115,
						152,
						55,
						50,
						205,
						471,
						461,
						472,
						525,
						330,
						479,
						103,
						503,
						190,
						187,
						3,
						4
					)
		ORDER BY
			num_cia,
			producto
	';//echo $sql;
	$tmp = $db->query($sql);//echo '<pre>' . print_r($tmp, TRUE) . '</pre>';
	$consumos = array();
	if ($tmp) {
		foreach ($tmp as $t)
			if (round($t['consumo'], 2) > 0)
				$consumos[$t['num_cia']][$t['codmp']] = round($t['consumo'], 2);
	}
	
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("memo");
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", strtoupper($result[$i]['nombre']));
		$tpl->assign("fecha", $fecha);
		$tpl->assign("mes", $mes);
		$tpl->assign("anio", $anio);
		$tpl->assign("admin", strtoupper($result[$i]['nombre_administrador']));
		$enc = $db->query("SELECT nombre_fin FROM encargados WHERE num_cia = {$result[$i]['num_cia']} ORDER BY anio DESC, mes DESC LIMIT 1");
		$tpl->assign("encargado", strtoupper($enc[0]['nombre_fin']));
		$tpl->assign("texto", empty($_GET['com']) ? $text1 : $text2);
		
		// [07-Oct-2009] Asignar consumos a los productos
		if (isset($consumos[$result[$i]['num_cia']])) {
			foreach ($consumos[$result[$i]['num_cia']] as $k => $v)
				$tpl->assign($k, number_format($v, 2, '.', ','));
		}
	}
	
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ped/ped_mem_ped.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign(date("n"), "selected");
$tpl->assign("anio", date("Y"));

$admin = $db->query("SELECT idadministrador, nombre_administrador FROM catalogo_administradores ORDER BY nombre_administrador");
for ($i = 0; $i < count($admin); $i++) {
	$tpl->newBlock("admin");
	$tpl->assign("id", $admin[$i]['idadministrador']);
	$tpl->assign("nombre", $admin[$i]['nombre_administrador']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>