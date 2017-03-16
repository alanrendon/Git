<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

$users = array(28, 29, 30, 31);

//if ($_SESSION['iduser'] != 1) die("En reparacion");

$db = new DBclass($dsn, "autocommit=yes");

// [AJAX] Obtener nombre de compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c] AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	$result = $db->query($sql);
	
	die(trim($result[0]['nombre']));
}

// Insertar datos
if (isset($_POST['id'])) {
	$sql = "";
	
	for ($i = 0; $i < count($_POST['id']); $i++)
		$sql .= "UPDATE faltantes_cometra SET fecha_con = CURRENT_DATE, imp = 'TRUE' WHERE id = {$_POST['id'][$i]};\n";
	$db->query($sql);
	
	header("location: ./ban_fal_liq.php?listado=1");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fal_liq.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['listado'])) {
	$sql = "SELECT id, num_cia, fecha, tipo, /*sum(importe) AS */importe FROM faltantes_cometra WHERE imp = 'TRUE' /*GROUP BY num_cia, fecha, tipo*/ ORDER BY num_cia, tipo";
	$result = $db->query($sql);
	
	if (!$result) {
		$db->desconectar();
		header("location: ./ban_fal_liq.php");
		die;
	}
	
	$num_cia = NULL;
	$fecha = NULL;
	$sql = "";
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			if ($num_cia != NULL && $total > 0) {
				//$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta) VALUES ($num_cia, now()::date, 'FALSE', $total, 7, 'DEPOSITO FALTANTE', 2);\n";
				$sql .= 'UPDATE faltantes_cometra SET iduser = ' . $_SESSION['iduser'] . ', tsmod = now(), id_ec = (SELECT last_value FROM estado_cuenta_id_seq) WHERE id IN (' . implode(', ', $ids) . ");\n";
				$sql .= '
					INSERT INTO
						cometra_faltantes
							(
								num_cia,
								fecha,
								cod_mov,
								concepto,
								importe,
								iduser_ins,
								tsins
							)
						VALUES
							(
								' . $num_cia . ',
								now()::date,
								7,
								\'DEPOSITO FALTANTE\',
								' . $total . ',
								' . $_SESSION['iduser'] . ',
								now()
							)
				' . ";\n";
			}
			
			$num_cia = $result[$i]['num_cia'];
			$total = 0;
			
			$ids = array();
		}
		$total += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : /*($result[$i]['importe'] < $total ? -$result[$i]['importe'] : 0)*/-$result[$i]['importe'];
		$ids[] = $result[$i]['id'];
	}
	if ($num_cia != NULL && $total > 0) {
		//$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta) VALUES ($num_cia, now()::date, 'FALSE', $total, 7, 'DEPOSITO FALTANTE', 2);\n";
		$sql .= 'UPDATE faltantes_cometra SET iduser = ' . $_SESSION['iduser'] . ', tsmod = now(), id_ec = (SELECT last_value FROM estado_cuenta_id_seq) WHERE id IN (' . implode(', ', $ids) . ");\n";
		$sql .= '
			INSERT INTO
				cometra_faltantes
					(
						num_cia,
						fecha,
						cod_mov,
						concepto,
						importe,
						iduser_ins,
						tsins
					)
				VALUES
					(
						' . $num_cia . ',
						now()::date,
						7,
						\'DEPOSITO FALTANTE\',
						' . $total . ',
						' . $_SESSION['iduser'] . ',
						now()
					)
		' . ";\n";
	}
	if ($sql != "") $db->query($sql);
	
	$sql = "SELECT num_cia, clabe_cuenta2, nombre_corto, fecha_con, importe, descripcion, tipo FROM faltantes_cometra LEFT JOIN catalogo_companias USING (num_cia) WHERE imp = 'TRUE' ORDER BY num_cia, fecha, tipo";
	$result = $db->query($sql);
	
	$sql = "UPDATE faltantes_cometra SET imp = 'FALSE' WHERE imp = 'TRUE'";
	$db->query($sql);
	
	$tpl->newBlock("listado");
	$tpl->assign("dia", date("d"));
	$tpl->assign("mes", mes_escrito(date("n")));
	$tpl->assign("anio", date("Y"));
	
	$num_cia = NULL;
	$gran_total = 0;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("cia_lis");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("cuenta", $result[$i]['clabe_cuenta2']);
			$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
			
			$total = 0;
		}
		$tpl->newBlock("fila_lis");
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("cuenta", $result[$i]['clabe_cuenta2']);
		$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		$tpl->assign("codigo", $result[$i]['tipo'] == "f" ? "7 DEPOSITO FALTANTE" : "&nbsp;");
		$tpl->assign("concepto", $result[$i]['tipo'] == "f" ? $result[$i]['descripcion'] : ($result[$i]['descripcion'] == "" ? "SOBRANTE" : $result[$i]['descripcion']));
		$tpl->assign("importe", number_format($result[$i]['tipo'] == "f" ? $result[$i]['importe'] : -$result[$i]['importe'], 2, ".", ","));
		$tpl->assign("fecha", $result[$i]['fecha_con']);
		
		$total += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : -$result[$i]['importe'];
		$gran_total += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : -$result[$i]['importe'];
		$tpl->assign("cia_lis.total", number_format($total, 2, ".", ","));
	}
	$tpl->assign("listado.gran_total", number_format($gran_total, 2, ".", ","));
	
	$tpl->printToScreen();
	$db->desconectar();
	die;
}

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	$admin = $db->query("SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre_administrador");
	foreach ($admin as $row) {
		$tpl->newBlock("admin");
		$tpl->assign("id", $row['id']);
		$tpl->assign("nombre", $row['nombre']);
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	$tpl->printToScreen();
	die;
}

$sql = "SELECT id, num_cia, nombre_corto, fecha, deposito, importe, tipo, descripcion FROM faltantes_cometra LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_con IS NULL";
$sql .= in_array($_SESSION['iduser'], $users) ? " AND num_cia BETWEEN 900 AND 950" : " AND num_cia BETWEEN 1 AND 800";
$sql .= $_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : "";
$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
$sql .= " ORDER BY num_cia, fecha";
$result = $db->query($sql);

if (!$result) {
	header("location: ./ban_fal_liq.php?codigo_error=1");
	die;
}

$tpl->newBlock("liquidar");

if ($result) {
	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			if ($num_cia != NULL) {
				$tpl->assign("cia.deposito", number_format($depositos, 2, ".", ","));
				$tpl->assign("cia.faltante", number_format($faltantes, 2, ".", ","));
				$tpl->assign("cia.sobrante", number_format($sobrantes, 2, ".", ","));
				$tpl->assign("cia.diferencia", number_format(abs($diferencia), 2, ".", ","));
				$tpl->assign("cia.color_dif", $diferencia >= 0 ? "0000FF" : "FF0000");
			}
			
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
			$tpl->assign("ini", $i);
			
			$sql = '
				SELECT
					nombre_fin
						AS encargado
				FROM
					encargados
				WHERE
					num_cia = ' . $num_cia . '
				ORDER BY
					anio DESC,
					mes DESC
				LIMIT
					1
			';
			
			$tmp = $db->query($sql);
			
			$encargado = $tmp ? $tmp[0]['encargado'] : '';
			
			$tpl->assign('encargado', $encargado);
			
			$depositos = 0;
			$faltantes = 0;
			$sobrantes = 0;
			$diferencia = 0;
		}
		$tpl->newBlock("fila");
		$tpl->assign("cia.fin", $i);
		$tpl->assign("id", $result[$i]['id']);
		$tpl->assign("num_cia", $num_cia);
		$tpl->assign("tipo", $result[$i]['tipo']);
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("deposito", $result[$i]['deposito'] != 0 ? number_format($result[$i]['deposito'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("faltante", $result[$i]['tipo'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("sobrante", $result[$i]['tipo'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("descripcion", $result[$i]['descripcion'] != "" ? $result[$i]['descripcion'] : "&nbsp;");
		
		$depositos += $result[$i]['deposito'];
		$faltantes += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : 0;
		$sobrantes += $result[$i]['tipo'] == "t" ? $result[$i]['importe'] : 0;
		$diferencia += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : -$result[$i]['importe'];
	}
	
	if ($num_cia != NULL) {
		$tpl->assign("cia.deposito", number_format($depositos, 2, ".", ","));
		$tpl->assign("cia.faltante", number_format($faltantes, 2, ".", ","));
		$tpl->assign("cia.sobrante", number_format($sobrantes, 2, ".", ","));
		$tpl->assign("cia.diferencia", number_format(abs($diferencia), 2, ".", ","));
		$tpl->assign("cia.color_dif", $diferencia >= 0 ? "0000FF" : "FF0000");
	}
}
else
	$tpl->newBlock("no_result");

$tpl->printToScreen();
$db->desconectar();
?>