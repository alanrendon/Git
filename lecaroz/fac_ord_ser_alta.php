<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");
$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");	// Coneccion a la base de datos de las imagenes

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Obtener nombre de compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$result = $db->query($sql);

	die(trim($result[0]['nombre']));
}

// [AJAX] Obtener nombre del proveedor
if (isset($_GET['p'])) {
	$sql = "SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[p]";
	$result = $db->query($sql);

	die($_GET['i'] . '|' . trim($result[0]['nombre']));
}

// [AJAX] Obtener maquinaria asociada a la compañía
if (isset($_GET['ce'])) {
	$sql = 'SELECT id, num_maquina, descripcion, marca FROM maquinaria';
	$sql .= $_GET['ce'] > 0 ? " WHERE num_cia = $_GET[ce] AND status = 1 ORDER BY num_maquina" : '';
	$result = $db->query($sql);

	if (!$result) die("-1");

	$data = "";
	foreach ($result as $i => $reg)
		$data .= "$reg[id]/$reg[num_maquina]-" . (trim($reg['descripcion']) != '' ? trim($reg['descripcion']) : '') .  (trim($reg['marca']) != '' ? ' (' . trim($reg['marca']) . ')' : '') . ($i < count($result) - 1 ? '|' : '');

	die($data);
}

// [AJAX] Comprobar que el folio de la orden de servicio no este repetido
if (isset($_GET['f'])) {
	$sql = "SELECT folio FROM orden_servicio WHERE folio = $_GET[f]";
	$result = $db->query($sql);

	echo $result ? 1 : 0;
	die;
}

// [AJAX] Comprobar que el folio de la orden de servicio no este repetido
if (isset($_GET['d'])) {
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['d'], $tmp);
	$td = mktime(0, 0, 0, $tmp[2], $tmp[1], $tmp[3]);
	$tmin = mktime() - 2592000;
	$tmax = mktime();

/*	if ($td < $tmin)
		echo -1;
	else if ($td > $tmax)
		echo 1;*/

	die;
}

// [AJAX] Buscar ordenes de servicio anteriores de la máquina en cuestion
if (isset($_GET['idm'])) {
	$sql = "SELECT id, folio, fecha, (SELECT sum(importe) FROM orden_servicio_facs WHERE folio = os.folio) AS importe, estatus FROM orden_servicio os WHERE idmaq = $_GET[idm]";
	$result = $db->query($sql);

	if (!$result) die('No hay ordenes de servicio anteriores');

	$data = '<table width="100%"><tr>
          <th class="tabla" width="25%">Fecha</th>
          <th class="tabla" width="25%">Folio</th>
          <th class="tabla" width="25%">Importe</th>
          <th class="tabla" width="25%">Estatus</th>
        </tr>';
	foreach ($result as $r) {
		$data .= "<tr onmouseover=\"this.style.cursor='pointer'\" onmouseout=\"this.style.cursor='default'\" onclick=\"det($r[id])\">
          <td class=\"tabla\">$r[fecha]</td>
          <td class=\"tabla\">$r[folio]</td>
          <td class=\"tabla\">" . ($r['importe'] > 0 ? number_format($r['importe'], 2, '.', ',') : '&nbsp;') . "</td>
          <td class=\"tabla\">" . ($r['estatus'] == 1 ? 'TERMINADO' : '&nbsp;') . "</td>
        </tr>";
	}
	$data .= '</table>';

	die($data);
}

if (isset($_POST['folio'])) {
	$sql = '';
	$sql_scan = '';

	$data['folio'] = $_POST['folio'];
	$data['fecha'] = $_POST['fecha'];
	$data['idmaq'] = $_POST['idmaq'];
	$data['tipo_orden'] = $_POST['tipo_orden'];
	$data['estatus'] = isset($_POST['estatus']) ? 1 : 0;
	$data['autorizo'] = strtoupper(trim(str_replace(array('    ', '   ', '  '), ' ', $_POST['autorizo'])));
	$data['concepto'] = strtoupper(trim(str_replace(array('    ', '   ', '  '), ' ', $_POST['concepto'])));
	$data['observaciones'] = strtoupper(trim(str_replace(array('    ', '   ', '  '), ' ', $_POST['observaciones'])));
	$data['iduser'] = $_SESSION['iduser'];
	$data['lastmod'] = date('d/m/Y H:i:s');

	$sql = $db->preparar_insert('orden_servicio', $data) . ";\n";
	// echo '<pre>' . print_r($_POST, TRUE) . '</pre>';
	$cont = 0;
	for ($i = 0; $i < count($_POST['num_fact']); $i++)
		if (/*$_POST['ok'][$i] > 0 && */$_POST['num_fact'][$i] != '' && $_POST['num_pro'][$i] > 0 && $_POST['fecha_fac'][$i] != '' && trim($_POST['concepto_fac'][$i]) != '' && get_val($_POST['importe'][$i]) > 0) {
			$fac[$cont]['folio'] = $_POST['folio'];
			$fac[$cont]['num_fact'] = $_POST['num_fact'][$i];
			$fac[$cont]['num_proveedor'] = $_POST['num_pro'][$i];
			$fac[$cont]['fecha'] = $_POST['fecha_fac'][$i];
			$fac[$cont]['concepto'] = strtoupper(trim(str_replace(array('    ', '   ', '  '), ' ', $_POST['concepto_fac'][$i])));
			$fac[$cont]['importe'] = get_val($_POST['importe'][$i]);
			$fac[$cont]['iduser'] = $_SESSION['iduser'];
			$fac[$cont]['lastmod'] = date('d/m/Y H:i:s');
			$cont++;

			$sql_scan .= "INSERT INTO img_fac_ord_ser (folio, num_proveedor, num_fact, imagen) SELECT folio, num_proveedor, num_fact, imagen FROM img_tmp_fac WHERE folio = $_POST[folio] AND num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = '{$_POST['num_fact'][$i]}';\n";
		}

	$sql_scan .= "INSERT INTO img_ord_ser (folio, imagen) SELECT folio, imagen FROM img_tmp_ord WHERE folio = $_POST[folio];\n";

	$sql_scan .= "DELETE FROM img_tmp_fac WHERE folio = $_POST[folio];\n";
	$sql_scan .= "DELETE FROM img_tmp_ord WHERE folio = $_POST[folio];\n";

	if ($cont > 0)
		$sql .= $db->multiple_insert('orden_servicio_facs', $fac);

	//echo '<pre>' . $sql_scan . '</pre>';die;

	$db->query($sql);
	$db_scans->query($sql_scan);

	die(header('location: ./fac_ord_ser_alta.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_ord_ser_alta.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Obtener nuevo folio para la orden de servicio
$sql = 'SELECT folio FROM orden_servicio ORDER BY folio DESC LIMIT 1';
$result = $db->query($sql);

$tpl->assign('fecha', date('d/m/Y'));

// Borrar cualquier imagen temporal que se encuentre en la base de datos
$db_scans->query("TRUNCATE img_tmp_fac");
$db_scans->query("TRUNCATE img_tmp_ord");

$tpl->printToScreen();
?>
