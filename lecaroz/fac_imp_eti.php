<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/pcl.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("modificando");

if (isset($_GET['num_cia1'])) {
	$numetiquetas_x_hoja = 20;
	$numetiquetas = $_GET['etiqueta'] > 0 ? $_GET['etiqueta'] - 1 : 0;
	$offset = 9;	// Ajuste arriba
	$loffset = 1;	// Ajuste a la izquierda
	$x0 = $_GET['etiqueta'] > 0 ? (($_GET['etiqueta'] - 1) % 2 == 0 ? 0 + $loffset : 107 + $loffset) : 0 + $loffset;
	$y0 = $_GET['etiqueta'] > 0 ? floor(($_GET['etiqueta'] - 1) / 2) * 25.5 + $offset : $offset;
	$label = $_GET['etiqueta'] > 0 ? (($_GET['etiqueta'] - 1) % 2 == 0 ? FALSE : TRUE) : FALSE;

	if ($_GET['tipo'] == 1) {
		$sql = "SELECT num_cia, nombre, direccion FROM catalogo_companias";
		if ($_GET['num_cia1'] > 0 || $_GET['num_cia2'] > 0) {
			$sql .= " WHERE";
			if ($_GET['num_cia1'] > 0 && $_GET['num_cia2'] == "")
				$sql .= " num_cia = $_GET[num_cia1]";
			else if ($_GET['num_cia1'] == "" && $_GET['num_cia2'] > 0)
				$sql .= " num_cia = $_GET[num_cia2]";
			else if ($_GET['num_cia1'] > 0 && $_GET['num_cia2'] > 0)
				$sql .= " num_cia BETWEEN $_GET[num_cia1] AND $_GET[num_cia2]";
		}
		$sql .= " ORDER BY num_cia";
	}
	else {
		$condiciones = array();

		$condiciones[] = "aguinaldos.fecha = (SELECT fecha_aguinaldo FROM porcentaje_aguinaldo ORDER BY id DESC LIMIT 1)";

		$condiciones[] = "catalogo_trabajadores.fecha_baja IS NULL";

		$condiciones[] = "importe >= 20";

		$condiciones[] = "solo_aguinaldo = TRUE";

		if ($_GET['num_cia1'] > 0 || $_GET['num_cia2'] > 0)
		{
			if ($_GET['num_cia1'] > 0 && $_GET['num_cia2'] == "")
			{
				$condiciones[] = "catalogo_companias.cia_aguinaldos = {$_GET['num_cia1']}";
			}
			else if ($_GET['num_cia1'] == "" && $_GET['num_cia2'] > 0)
			{
				$condiciones[] = "catalogo_companias.cia_aguinaldos >= {$_GET['num_cia2']}";
			}
			else if ($_GET['num_cia1'] > 0 && $_GET['num_cia2'] > 0)
			{
				$condiciones[] = "catalogo_companias.cia_aguinaldos BETWEEN {$_GET['num_cia1']} AND {$_GET['num_cia2']}";
			}
		}

		if (isset($_REQUEST['tipo_aguinaldo']) && $_REQUEST['tipo_aguinaldo'] > 0)
		{
			if ($_REQUEST['tipo_aguinaldo'] == 1)
			{
				$condiciones[] = "aguinaldos.tipo <= 3";
			}
			else if ($_REQUEST['tipo_aguinaldo'] == 2)
			{
				$condiciones[] = "aguinaldos.tipo > 3";
			}
		}

		$sql = "
			SELECT
				catalogo_companias.num_cia
					AS num_cia,
				catalogo_companias.cia_aguinaldos,
				catalogo_companias.nombre_corto
					AS nombre_cia,
				catalogo_trabajadores.num_emp,
				catalogo_trabajadores.nombre,
				catalogo_trabajadores.ap_paterno,
				catalogo_trabajadores.ap_materno,
				catalogo_puestos.descripcion
					AS puesto
				FROM
					aguinaldos
					LEFT JOIN catalogo_trabajadores
						ON (catalogo_trabajadores.id=aguinaldos.id_empleado)
					LEFT JOIN catalogo_companias
						ON (catalogo_companias.num_cia=catalogo_trabajadores.num_cia)
					LEFT JOIN catalogo_turnos
						USING (cod_turno)
					LEFT JOIN catalogo_puestos
						USING (cod_puestos)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					catalogo_companias.cia_aguinaldos,
					catalogo_puestos.sueldo DESC,
					cod_puestos,
					catalogo_turnos.orden_turno,
					num_emp
		";

		// if ($_GET['num_cia1'] > 0 || $_GET['num_cia2'] > 0) {
		// 	if ($_GET['num_cia1'] > 0 && $_GET['num_cia2'] == "")
		// 		$sql .= " catalogo_companias./*num_cia_primaria*/cia_aguinaldos = $_GET[num_cia1] AND";
		// 	else if ($_GET['num_cia1'] == "" && $_GET['num_cia2'] > 0)
		// 		$sql .= " catalogo_companias./*num_cia_primaria*/cia_aguinaldos >= $_GET[num_cia2] AND";
		// 	else if ($_GET['num_cia1'] > 0 && $_GET['num_cia2'] > 0)
		// 		$sql .= " catalogo_companias./*num_cia_primaria*/cia_aguinaldos BETWEEN $_GET[num_cia1] AND $_GET[num_cia2] AND";
		// }

		// $sql .= "aguinaldos.fecha = (SELECT fecha_aguinaldo FROM porcentaje_aguinaldo ORDER BY id DESC LIMIT 1) AND catalogo_trabajadores.fecha_baja IS NULL AND importe >= 20 AND solo_aguinaldo = 'TRUE' /*AND catalogo_companias.cia_aguinaldos NOT IN (28, 35, 45, 66, 58, 61, 132, 700, 800, 364)*/ ORDER BY catalogo_companias.cia_aguinaldos, catalogo_puestos.sueldo DESC, cod_puestos, catalogo_turnos.orden_turno, num_emp";
	}//echo $sql;die;

	//$sql = 'SELECT ct.num_cia, cc.nombre_corto AS nombre_cia, ct.num_emp, ct.nombre, ct.ap_paterno, ct.ap_materno, cp.descripcion AS puesto FROM catalogo_trabajadores ct LEFT JOIN catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_turnos USING (cod_turno) LEFT JOIN catalogo_puestos cp USING (cod_puestos) WHERE ct.id IN (21735)';

	$result = $db->query($sql);

	$consecutivo = 0;
	$num_cia = NULL;

	shell_exec("chmod ugo=rwx pcl");
	$fp = fopen("pcl/labels.pcl", "w");

	$pcl = "";

	$pcl .= HEADER;
	$pcl .= SetPageSize(2);
	$pcl .= SetTopMargin(1);
	$pcl .= SetLeftMargin(0);
	$pcl .= DEFAULT_FONT;

	$num_cia_result = $_GET['tipo'] == 1 ? "num_cia" : 'cia_aguinaldos';

	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i][$num_cia_result]) {
			$num_cia = $result[$i][$num_cia_result];
			$consecutivo = 0;
		}
		// ETIQUETAS MODELO #5261
		if ($_GET['tipo'] == 1) {
			$pcl .= MoveCursorV($y0 + 5);
			$pcl .= MoveCursorH($x0);
			$pcl .= SetFontStrokeWeight(BOLD);
			$pcl .= SetFontPointSize(8);
			$pcl .= strtoupper("{$result[$i]['num_cia']} {$result[$i]['nombre']}");
			$pcl .= SetFontStrokeWeight(MEDIUM);
			$pcl .= SetFontPointSize(6);
			$dir = explode(",", trim($result[$i]['direccion']), 3);
			foreach ($dir as $key => $value) {
				$pcl .= MoveCursorV($y0 + 10 + $key * 4);
				$pcl .= MoveCursorH($x0);
				$pcl .= strtoupper(trim($value));
			}
		}
		// ETIQUETAS MODELO #5261
		else {
			$pcl .= SetFontStrokeWeight(MEDIUM);
			$pcl .= SetFontPointSize(10);
			$pcl .= MoveCursorV($y0 + 10);
			$pcl .= MoveCursorH($x0);
			$pcl .= strtoupper("{$result[$i]['num_cia']} {$result[$i]['nombre_cia']}");
			$pcl .= MoveCursorV($y0 + 15);
			$pcl .= MoveCursorH($x0);
			$pcl .= strtoupper("{$result[$i]['num_emp']} {$result[$i]['ap_paterno']} {$result[$i]['ap_materno']} {$result[$i]['nombre']}");
			$pcl .= MoveCursorV($y0 + 20);
			$pcl .= MoveCursorH($x0);
			$pcl .= SetFontStrokeWeight(EXTRABOLD);
			$pcl .= (++$consecutivo);
			$pcl .= SetFontStrokeWeight(MEDIUM);
			$pcl .= "  " . strtoupper($result[$i]['puesto']);
		}

		$label = !$label;
		$y0 += !$label ? 25.5 : 0;
		$x0 = !$label ? 0 + $loffset : 107 + $loffset;
		$numetiquetas++;

		if ($numetiquetas == $numetiquetas_x_hoja) {
			$pcl .= FORM_FEED;
			$x0 = $loffset;
			$y0 = $offset;
			$numetiquetas = 0;
		}
	}

	$pcl .= RESET;

	fwrite($fp, $pcl);
	fclose($fp);
	// shell_exec("lp -d general pcl/labels.pcl");
	shell_exec("lp -d septimo pcl/labels.pcl");
	shell_exec("chmod ugo=r pcl");

	header("location: ./fac_imp_eti.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_imp_eti.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->printToScreen();
?>
