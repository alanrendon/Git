<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['num_cia'])) {
	$num_cia = $_POST['num_cia'];
	$mes = $_POST['mes'];
	$anio = $_POST['anio'];
	$sql = "";
	
	// [09-Oct-2009] Obtener precio
	$tmp = $db->query('SELECT precio FROM precio_fac_rancho');
	$precio = $tmp ? $tmp[0]['precio'] : 0;
	
	foreach ($_POST['dia'] as $i => $dia) {
		$fecha = "$dia/$mes/$anio";
		$litros = floatval(str_replace(",", "", $_POST['litros'][$i]));
		$num_fact = $_POST['num_fact'][$i];
		if (/*$litros >= 0 && $num_fact > 0 && */$_POST['status'][$i] == 0) {
			if ($id = $db->query("SELECT id, litros FROM facturas_rancho_litros WHERE num_cia = $num_cia AND fecha = '$fecha'")) {
				if ($litros > 0 && $num_fact > 0) {
					$sql .= "UPDATE facturas_rancho_litros SET litros = $litros, num_fact = $num_fact WHERE id = {$id[0]['id']};\n";
					$sql .= "UPDATE mov_inv_real SET cantidad = $litros, precio = $precio, precio_unidad = $precio, total_mov = $litros * $precio WHERE fecha = '$fecha' AND num_cia = $num_cia AND codmp = 580 AND tipo_mov = 'FALSE';\n";
					$sql .= "UPDATE inventario_real SET existencia = existencia - {$id[0]['litros']} + $litros WHERE num_cia = 
$num_cia AND codmp = 580;\n";
				}
				else {
					$sql .= "DELETE FROM facturas_rancho_litros WHERE id = {$id[0]['id']};\n";
					$sql .= "DELETE FROM mov_inv_real WHERE fecha = '$fecha' AND num_cia = $num_cia AND codmp = 580 AND tipo_mov = 'FALSE';\n";
					$sql .= "UPDATE inventario_real SET existencia = existencia - {$id[0]['litros']} WHERE num_cia = $num_cia AND 
codmp = 580;\n";
				}
			}
			else if ($litros > 0 && $num_fact > 0) {
				$sql .= "INSERT INTO facturas_rancho_litros (num_cia, fecha, litros, status, num_fact) VALUES ($num_cia, '$fecha', $litros, 0, $num_fact);\n";
				$sql .= "INSERT INTO mov_inv_real (num_cia, codmp, fecha, tipo_mov, cantidad, precio, total_mov, precio_unidad, descripcion, num_proveedor) VALUES ($num_cia, 580, '$fecha', 'FALSE', $litros, $precio, $litros * $precio, $precio, 'COMPRA F. NO.' || $num_fact, 617);\n";
				$sql .= "UPDATE inventario_real SET existencia = existencia + $litros WHERE num_cia = $num_cia AND codmp = 580;\n";
			}
		}
	}
	
	if ($sql != "") $db->query($sql);
	
	header("location: ./pan_fac_ran_cap.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_fac_ran_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$precio = $db->query("SELECT precio FROM precio_fac_rancho");
$tpl->assign("precio", $precio ? number_format($precio[0]['precio'], 2, ".", "") : "");

if (isset($_GET['num_cia'])) {
	$numdias = (int)date("d", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$fecha1 = "01/$_GET[mes]/$_GET[anio]";
	$fecha2 = "$numdias/$_GET[mes]/$_GET[anio]";
	
	$tmp = $db->query("SELECT precio FROM precio_fac_rancho");
	$precio = floatval($tmp[0]['precio']);
	
	$sql = "SELECT extract(day from fecha) as dia, litros, status, num_fact FROM facturas_rancho_litros WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
	$result = $db->query($sql);
	
	function buscar($dia) {
		global $result;
		
		if (!$result)
			return FALSE;
		
		foreach ($result as $reg)
			if ($dia == $reg['dia'])
				return $reg;
		
		return FALSE;
	}
	
	$tpl->newBlock("captura");
	$tpl->assign("num_cia", $_GET['num_cia']);
	$nombre = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$tpl->assign("nombre", $nombre[0]['nombre_corto']);
	$tpl->assign("mes", $_GET['mes']);
	$tpl->assign("mes_escrito", mes_escrito($_GET['mes'], TRUE));
	$tpl->assign("anio", $_GET['anio']);
	$tpl->assign("precio", $precio);
	
	$litros_x_bidon = 20;
	
	$total_litros = 0;
	$total_importe = 0;
	for ($i = 1; $i <= $numdias; $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i", $i - 1);
		$tpl->assign("next", $i < $numdias ? $i : 0);
		$tpl->assign("back", $i > 1 ? $i - 2 : $numdias - 1);
		$tpl->assign("dia", $i);
		$litros = buscar($i);
		$tpl->assign("num_fact", $litros ? $litros['num_fact'] : "");
		$tpl->assign("bidones", $litros ? number_format($litros['litros'] / $litros_x_bidon, 2, ".", ",") : "");
		$tpl->assign("litros", $litros ? number_format($litros['litros'], 2, ".", ",") : "");
		$tpl->assign("importe", $litros ? number_format($litros['litros'] * $precio, 2, ".", ",") : "");
		$tpl->assign("status", $litros ? $litros['status'] : 0);
		$tpl->assign("proceso", $litros && $litros['status'] == 1 ? "FACTURADO" : "&nbsp;");
		$tpl->assign("readonly", $litros && $litros['status'] == 1 ? "readonly" : "");
		
		$total_litros += $litros['litros'];
		$total_importe += $litros['litros'] * $precio;
	}
	$tpl->assign("captura.total_litros", number_format($total_litros, 2, ".", ","));
	$tpl->assign("captura.total_importe", number_format($total_importe, 2, ".", ","));
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

$tpl->printToScreen();
?>
