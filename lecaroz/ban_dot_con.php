<?php
// LISTADO DE OTROS DEPOSITOS
// Tabla 'estado_cuenta'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

if (isset($_POST['ficha'])) {
	$sql = "";

	foreach ($_POST['ficha'] as $reg)
		if ($reg > 0)
			$sql .= "UPDATE otros_depositos SET ficha = 'TRUE' WHERE id = {$reg};\n";

	if (trim($sql) != '') ejecutar_script($sql, $dsn);
	die(header("location: ./ban_dot_con.php?tipo=$_GET[tipo]&num_cia=$_GET[num_cia]&mes=$_GET[mes]&anio=$_GET[anio]"));
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dot_con.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (!isset($_GET['tipo'])) {
	$tpl->newBlock("datos");

	$tpl->assign(date("n"),"selected");
	$tpl->assign("anio",date("Y"));

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
	die;
}

if (!isset($_GET['fecha'])) {
	$fecha1 = "1/$_GET[mes]/$_GET[anio]";
	$fecha2 = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));
	$mes = $_GET['mes'];
	$anio = $_GET['anio'];
}
else {
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha'], $tmp);
	$dia = $tmp[1];
	$mes = $tmp[2];
	$anio = $tmp[3];
}

// Verificar si hay registros de la fecha de captura
$result = ejecutar_script("SELECT * FROM otros_depositos WHERE fecha " . (isset($_GET['fecha']) ? "= '$_GET[fecha]'" : "BETWEEN '$fecha1' AND '$fecha2'") . " LIMIT 1",$dsn);
if (!$result) {
	header("location: ./ban_dot_con.php?codigo_error=1");
	die;
}

if ($_GET['tipo'] == "desglozado") {
	/*$sql = "SELECT * FROM otros_depositos WHERE";
	if ($_GET['num_cia'] > 0)
		$sql .= " num_cia = $_GET[num_cia] AND";
	$sql .= " fecha >= '$fecha1' AND fecha <= '$fecha2' ORDER BY num_cia, fecha";
	$result = ejecutar_script($sql,$dsn);*/
	$sql = "SELECT num_cia FROM otros_depositos WHERE";
	if ($_GET['num_cia'] > 0)
		$sql .= " num_cia = $_GET[num_cia] AND";
	if (isset($_GET['fecha']))
		$sql .= " fecha = '$_GET[fecha]'";
	else
		$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? "900 AND 998" : "1 AND 899");

	if ($_SESSION['tipo_usuario'] == 1 && $_GET['num_cia'] <= 0) {
		$sql .= '
			UNION

			SELECT
				num_cia
			FROM
				otros_depositos
			WHERE
				num_cia BETWEEN 900 AND 998
				AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				AND comprobante > 0
		';
	}

	$sql .= " GROUP BY num_cia ORDER BY num_cia";
	$cia = ejecutar_script($sql,$dsn);

	if (!$cia) {
		if (isset($_GET['con']))
			header("location: ./ban_con_dep.php");
		else if (isset($_GET['efe'])) {
			$tpl->newBlock('cerrar');
			$tpl->printToScreen();
		}
		else
			header("location: ./ban_dot_con.php?codigo_error=1");
		die;
	}

	$diasxmes[1] = 31;
	$diasxmes[2] = $anio % 4 == 0 ? 29 : 28;
	$diasxmes[3] = 31;
	$diasxmes[4] = 30;
	$diasxmes[5] = 31;
	$diasxmes[6] = 30;
	$diasxmes[7] = 31;
	$diasxmes[8] = 31;
	$diasxmes[9] = 30;
	$diasxmes[10] = 31;
	$diasxmes[11] = 30;
	$diasxmes[12] = 31;

	for ($i=0; $i<count($cia); $i++) {
		$num_cia = $cia[$i]['num_cia'];
		$tpl->newBlock("listado_desglozado");
		$tpl->assign("num_cia",$num_cia);
		$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia",$dsn);
		$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
		$tpl->assign('tipo', $_GET['tipo']);
		$tpl->assign("anio",$anio);
		$tpl->assign("_mes",$mes);
		$tpl->assign("mes",mes_escrito($mes));

		$total = 0;
		for ($j=(isset($_GET['fecha']) ? $dia : 1); $j<=(isset($_GET['fecha']) ? $dia : $diasxmes[(int)$mes]); $j++) {
			$sql = "SELECT otros_depositos.id, concepto,importe, nombre, fecha_cap, acre, ficha, num_fact1, num_fact2, num_fact3, num_fact4 FROM otros_depositos LEFT JOIN catalogo_nombres AS cn ON (cn.id = idnombre) WHERE num_cia = $num_cia AND fecha = '$j/$mes/$anio'" . ($_SESSION['tipo_usuario'] == 1 && $num_cia >= 900 ? ' AND comprobante > 0' : '');
			$importe = ejecutar_script($sql,$dsn);
			if ($importe)
				for ($k=0; $k<count($importe); $k++) {
					$tpl->newBlock("fila_des");
					$tpl->assign('color', $_SESSION['tipo_usuario'] == 2 && $importe[$k]['ficha'] == 'f' ? ' bgcolor="#FFFF99"' : '');
					//$tpl->assign('_color', $_SESSION['iduser'] >= 28 && $importe[$k]['ficha'] == 'f' ? '#FFFF99' : '');
					$tpl->assign("dia",$j);
					$tpl->assign("id",$importe[$k]['id']);
					$tpl->assign("checked", $importe[$k]['ficha'] == 't' ? ' checked' : '');
					$tpl->assign("concepto",$importe[$k]['concepto'] != "" ? $importe[$k]['concepto'] : "&nbsp;");
					$tpl->assign("deposito",number_format($importe[$k]['importe'],2,".",","));
					if ($importe[$k]['acre'] > 0) $acre = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$importe[$k]['acre']}",$dsn);
					$tpl->assign('acre', $importe[$k]['acre'] > 0 ? $acre[0]['nombre_corto'] : '&nbsp;');
					$tpl->assign("nombre", $importe[$k]['nombre']);
					$tpl->assign("fecha_cap", $importe[$k]['fecha_cap']);
					$remisiones = array();
					if ($importe[$k]['num_fact1'] != '')
						$remisiones[] = $importe[$k]['num_fact1'];
					if ($importe[$k]['num_fact2'] != '')
						$remisiones[] = $importe[$k]['num_fact2'];
					if ($importe[$k]['num_fact3'] != '')
						$remisiones[] = $importe[$k]['num_fact3'];
					if ($importe[$k]['num_fact4'] != '')
						$remisiones[] = $importe[$k]['num_fact4'];

					$remisiones = implode(', ', $remisiones);
					$tpl->assign('remisiones', $remisiones != '' ? $remisiones : '&nbsp;');
					$total += $importe[$k]['importe'];
					$tpl->assign("listado_desglozado.total",number_format($total,2,".",","));
				}
			else {
				$tpl->newBlock("fila_des");
				$tpl->assign("dia",$j);
				$tpl->assign("deposito","&nbsp;");
				$tpl->assign("concepto", "&nbsp;");
				$tpl->assign("nombre", "&nbsp;");
				$tpl->assign("fecha_cap", "&nbsp;");
			}
		}
	}

	/*$num_cia = NULL;
	for ($i=0; $i<count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];

			$tpl->newBlock("listado_desglozado");

			$tpl->assign("num_cia",$num_cia);
			$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia",$dsn);
			$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);

			$tpl->assign("anio",$_GET['anio']);
			switch ($_GET['mes']) {
				case 1: $tpl->assign("mes","Enero"); break;
				case 2: $tpl->assign("mes","Febrero"); break;
				case 3: $tpl->assign("mes","Marzo"); break;
				case 4: $tpl->assign("mes","Abril"); break;
				case 5: $tpl->assign("mes","Mayo"); break;
				case 6: $tpl->assign("mes","Junio"); break;
				case 7: $tpl->assign("mes","Julio"); break;
				case 8: $tpl->assign("mes","Agosto"); break;
				case 9: $tpl->assign("mes","Septiembre"); break;
				case 10: $tpl->assign("mes","Octubre"); break;
				case 11: $tpl->assign("mes","Noviembre"); break;
				case 12: $tpl->assign("mes","Diciembre"); break;
			}

			$total = 0;
		}
		$tpl->newBlock("fila_des");
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})",$result[$i]['fecha'],$fecha);
		$tpl->assign("dia",$fecha[1]);
		$tpl->assign("deposito",number_format($result[$i]['importe'],2,".",","));
		$total += $result[$i]['importe'];
		$tpl->assign("listado_desglozado.total",number_format($total,2,".",","));
	}*/

	if ($_SESSION['tipo_usuario'] == 2)
		$tpl->newBlock('update');

	if (isset($_GET['efe']))
		$tpl->newBlock('close');
	else {
		$tpl->newBlock('back');
		if (isset($_GET['con']))
			$tpl->assign('back', 'ban_con_dep_v2.php');
		else
			$tpl->assign('back', 'ban_dot_con.php');
	}

	$tpl->printToScreen();
}
else if ($_GET['tipo'] == "totales") {
	$tpl->newBlock("listado_totales");

	$sql = "
		SELECT
			num_cia,
			nombre,
			SUM(importe)
				AS importe,
			num_cia_primaria
		FROM
			otros_depositos
			LEFT JOIN catalogo_companias
				USING(num_cia)
		WHERE
	";

	if ($_GET['num_cia'] > 0)
		$sql .= " num_cia = $_GET[num_cia] AND";

	$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? "900 AND 998" : "1 AND 899");

	$sql .= '
		GROUP BY
			num_cia,
			num_cia_primaria,
			nombre
	';

	if ($_SESSION['tipo_usuario'] == 1 && $_GET['num_cia'] <= 0) {
		$sql .= '

			UNION

			SELECT
				num_cia,
				nombre,
				SUM(importe)
					AS importe,
				num_cia
					AS num_cia_primaria
			FROM
				otros_depositos
				LEFT JOIN catalogo_companias
					USING(num_cia)
			WHERE
				num_cia BETWEEN 900 AND 998
				AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				AND comprobante > 0
			GROUP BY
			num_cia,
			num_cia_primaria,
			nombre
		';
	}

	$sql .= "

		ORDER BY
			num_cia_primaria,
			num_cia";
	$result = ejecutar_script($sql,$dsn);

	if ($result) {
		$gran_total = 0;

		$tpl->assign("anio",$anio);
		switch ($mes) {
			case 1: $tpl->assign("mes","Enero"); break;
			case 2: $tpl->assign("mes","Febrero"); break;
			case 3: $tpl->assign("mes","Marzo"); break;
			case 4: $tpl->assign("mes","Abril"); break;
			case 5: $tpl->assign("mes","Mayo"); break;
			case 6: $tpl->assign("mes","Junio"); break;
			case 7: $tpl->assign("mes","Julio"); break;
			case 8: $tpl->assign("mes","Agosto"); break;
			case 9: $tpl->assign("mes","Septiembre"); break;
			case 10: $tpl->assign("mes","Octubre"); break;
			case 11: $tpl->assign("mes","Noviembre"); break;
			case 12: $tpl->assign("mes","Diciembre"); break;
		}

		$fecha = NULL;
		$num_cia = NULL;
		$rows = 0;
		for ($i=0; $i<count($result); $i++) {
			if (/*$fecha != $result[$i]['fecha'] ||*/ $num_cia != $result[$i]['num_cia_primaria']) {
				if ($rows > 1) {
					$tpl->newBlock("total_tot");
					$tpl->assign("total",number_format($total,2,".",","));
				}

				//$fecha = $result[$i]['fecha'];
				$num_cia = $result[$i]['num_cia_primaria'];

				$rows = 0;
				$total = 0;

				$tpl->newBlock("grupo_tot");
			}
			$tpl->newBlock("fila_tot");
			$tpl->assign("num_cia",$result[$i]['num_cia']);
			$tpl->assign("nombre_cia",$result[$i]['nombre']);
			$tpl->assign("deposito",number_format($result[$i]['importe'],2,".",","));

			$total += $result[$i]['importe'];
			$gran_total += $result[$i]['importe'];
			$rows++;
		}
		if ($rows > 1) {
			$tpl->newBlock("total_tot");
			$tpl->assign("total",number_format($total,2,".",","));
		}

		$tpl->assign("listado_totales.gran_total",number_format($gran_total,2,".",","));
		$total_mes = ejecutar_script("SELECT SUM(importe) FROM otros_depositos WHERE fecha >= '1/$mes/$anio' AND fecha <= '".date("d/m/Y",mktime(0,0,0,$mes+1,0,$anio))."'",$dsn);
		$tpl->assign("listado_totales.total_mes",number_format($total_mes[0]['sum'],2,'.',','));
	}
	else {
		if (isset($_GET['con']))
			header("location: ./ban_con_dep_v2.php");
		else
			header("location: ./ban_dot_con.php?codigo_error=1");
		die;
	}
	$tpl->printToScreen();
}
?>