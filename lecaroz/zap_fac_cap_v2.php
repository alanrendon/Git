<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Validar número de factura
if (isset($_GET['num_pro'])) {
	// [12-Mar-2008] Se agrega condición para comparar tambien el año de la factura
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha'], $tmp);
	$anio = $tmp[3];
	if ($db->query("SELECT id FROM facturas_zap WHERE num_proveedor = $_GET[num_pro] AND num_fact = '$_GET[num_fact]'/* AND extract(year from fecha) = '$_GET[fecha]'*/"))
		echo 0;
	else
		echo 1;
	die;
}

// [AJAX] Obtener sucursales
if (isset($_GET['matriz'])) {
	$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia_primaria = $_GET[matriz] AND num_cia != num_cia_primaria";
	$result = $db->query($sql);
	
	if (!$result) die();
	
	$data = '';
	foreach ($result as $i => $reg) {
		if ($data != '')
			$data .= '<br />';
		$data .= "<input name=\"suc[]\" type=\"text\" class=\"vnombre\" id=\"suc\" value=\"$reg[num_cia]\" size=\"3\" readonly=\"true\" />
        <input name=\"nombre_suc[]\" type=\"text\" disabled=\"disabled\" class=\"vnombre\" id=\"nombre_suc\" value=\"$reg[nombre_corto]\" size=\"30\" />
        <input name=\"importe_suc[]\" type=\"text\" class=\"rinsert\" id=\"importe_suc\" onfocus=\"tmp.value=this.value;this.select()\" onchange=\"inputFormat(this,2,tmp)\" onkeydown=\"movCursor(event.keyCode," . (count($result) > 0 ? ($i < count($result) - 1 ? 'importe_suc[' . ($i + 1) . ']' : 'num_cia') : 'num_cia') . ",null,null," . (count($result) > 0 ? ($i > 0 ? 'importe_suc[' . ($i - 1) . ']' : 'otros') : 'otros') . "," . (count($result) > 0 ? ($i < count($result) - 1 ? 'num_cia' : 'importe_suc[' . ($i + 1) . ']') : 'num_cia') . ")\" size=\"10\" />";
	}
	
	die($data);
}

// Insertar datos
if (isset($_POST['num_cia'])) {
	$_SESSION['zfc'] = $_POST;
	
	// Datos de factura
	$data['num_cia']        = $_POST['num_cia'];
	$data['num_proveedor']  = $_POST['num_pro'];
	$data['num_fact']       = $_POST['num_fact'];
	$data['entrada']        = get_val($_POST['entrada']);
	$data['fecha']          = $_POST['fecha'];
	$data['fecha_rec']      = $_POST['fecha_rec'];
	$data['fecha_inv']      = $_POST['fecha_inv'];
	$data['concepto']       = trim($_POST['concepto']) != '' ? strtoupper(trim($_POST['concepto'])) : 'FACTURA';
	$data['codgastos']      = $_POST['codgastos'];
	$data['importe']        = get_val($_POST['importe']);
	$data['pdesc1']         = get_val($_POST['pdesc1']);
	$data['pdesc2']         = get_val($_POST['pdesc2']);
	$data['pdesc3']         = get_val($_POST['pdesc3']);
	$data['pdesc4']         = get_val($_POST['pdesc4']);
	$data['desc1']          = get_val($_POST['desc1']);
	$data['desc2']          = get_val($_POST['desc2']);
	$data['desc3']          = get_val($_POST['desc3']);
	$data['desc4']          = get_val($_POST['desc4']);
	$data['cod_desc1']      = get_val($_POST['cod_desc1']);
	$data['cod_desc2']      = get_val($_POST['cod_desc2']);
	$data['cod_desc3']      = get_val($_POST['cod_desc3']);
	$data['cod_desc4']      = get_val($_POST['cod_desc4']);
	$data['faltantes']      = get_val($_POST['faltantes']);
	$data['iva']            = get_val($_POST['iva']);
	$data['pisr']           = isset($_POST['apl_ret']) ? get_val($_POST['pisr']) : 0;
	$data['isr']            = get_val($_POST['isr']);
	$data['pivaret']        = isset($_POST['apl_ret']) ? get_val($_POST['pivaret']) : 0;
	$data['ivaret']         = get_val($_POST['ivaret']);
	$data['fletes']         = get_val($_POST['fletes']);
	$data['otros']          = get_val($_POST['otros']);
	$data['con_otros']      = trim(strtoupper($_POST['con_otros']));
	$data['total']          = get_val($_POST['total']);
	$data['por_aut']        = 'FALSE';
	$data['iduser']         = $_SESSION['iduser'];
	$data['tscap']          = 'now()';
	$data['copia_fac']      = 'FALSE';
	$data['clave']          = get_val($_POST['clave']);
	$data['dif_precio']     = get_val($_POST['dif_precio']);
	$data['con_dif_precio'] = trim(strtoupper($_POST['con_dif_precio']));
	$data['dev']            = 0;
	$data['sucursal']       = 'FALSE';
	
	$sql = $db->preparar_insert('facturas_zap', $data) . ";\n";
	
	if (isset($_POST['suc']))
		foreach ($_POST['suc'] as $i => $suc)
			if (get_val($_POST['importe_suc'][$i]) > 0) {
				$data['num_cia']        = $suc;
				$data['importe']        = get_val($_POST['importe_suc'][$i]);
				$data['pdesc1']         = 0;
				$data['pdesc2']         = 0;
				$data['pdesc3']         = 0;
				$data['pdesc4']         = 0;
				$data['desc1']          = 0;
				$data['desc2']          = 0;
				$data['desc3']          = 0;
				$data['desc4']          = 0;
				$data['cod_desc1']      = 0;
				$data['cod_desc2']      = 0;
				$data['cod_desc3']      = 0;
				$data['cod_desc4']      = 0;
				$data['faltantes']      = 0;
				$data['iva']            = 0;
				$data['pisr']           = 0;
				$data['isr']            = 0;
				$data['pivaret']        = 0;
				$data['ivaret']         = 0;
				$data['fletes']         = 0;
				$data['otros']          = 0;
				$data['con_otros']      = '';
				$data['total']          = get_val($_POST['importe_suc'][$i]);
				$data['dif_precio']     = 0;
				$data['con_dif_precio'] = '';
				$data['dev']            = 0;
				$data['sucursal']       = 'TRUE';
				
				$sql .= $db->preparar_insert('facturas_zap', $data) . ";\n";
			}
	
	// Datos de faltantes de mercancia
	for ($i = 0; $i < count($_POST['importe_fal']); $i++)
		if (get_val($_POST['importe_fal'][$i]) > 0) {
			$fal['num_cia'] = $_POST['num_cia'];
			$fal['num_proveedor'] = $_POST['num_pro'];
			$fal['num_fact'] = $_POST['num_fact'];
			$fal['modelo'] = trim(strtoupper($_POST['modelo'][$i]));
			$fal['color'] = trim(strtoupper($_POST['color'][$i]));
			$fal['talla'] = get_val($_POST['talla'][$i]);
			$fal['piezas'] = get_val($_POST['piezas'][$i]);
			$fal['precio'] = get_val($_POST['precio'][$i]);
			$fal['importe_fal'] = get_val($_POST['importe_fal'][$i]);
			
			$sql .= $db->preparar_insert('faltantes_zap', $fal) . ";\n";
		}
	
	// [23-May-2008] Para refacturación
	if (isset($_POST['refac'])) {
		$acla['num_proveedor'] = $_POST['num_pro'];
		$acla['num_fact'] = $_POST['num_fact'];
		$acla['fecha_solicitud'] = date('d/m/Y');
		$acla['obs'] = substr(trim(strtoupper($_POST['obs'])), 0, 255);
		
		$sql .= $db->preparar_insert('facturas_pendientes', $acla) . ";\n";
	}
	
	if ($sql != '') $db->query($sql);
	
	header('location: ./zap_fac_cap_v2.php');
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/zap/zap_fac_cap_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->newBlock('captura');
if (isset($_SESSION['zfc'])) {
	$tpl->assign('num_pro', $_SESSION['zfc']['num_pro']);
	$tpl->assign('nombre_pro', $_SESSION['zfc']['nombre_pro']);
	$tpl->assign('fecha_rec', $_SESSION['zfc']['fecha_rec']);
	$tpl->assign('codgastos', $_SESSION['zfc']['codgastos']);
	$tpl->assign('desc', $_SESSION['zfc']['desc']);
}

$numfilas = 10;
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock('faltante');
	$tpl->assign('i', $i);
	$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
}

$result = $db->query('SELECT num_cia, nombre FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 998 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('cia');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

$result = $db->query('SELECT num_proveedor AS num_pro, nombre, cod_desc1, desc1, cod_desc2, desc2, cod_desc3, desc3, cod_desc4, desc4, clave_seguridad AS clave FROM catalogo_proveedores ORDER BY num_proveedor');
foreach ($result as $reg) {
	$tpl->newBlock('pro');
	$tpl->assign('num_pro', $reg['num_pro']);
	$tpl->assign('nombre', $reg['nombre']);
	$tpl->assign('cod_desc1', $reg['cod_desc1'] > 0 ? $reg['cod_desc1'] : 0);
	$tpl->assign('desc1', $reg['desc1'] > 0 ? number_format($reg['desc1'], 2) : 0);
	$tpl->assign('cod_desc2', $reg['cod_desc2'] > 0 ? $reg['cod_desc2'] : 0);
	$tpl->assign('desc2', $reg['desc2'] > 0 ? number_format($reg['desc2'], 2) : 0);
	$tpl->assign('cod_desc3', $reg['cod_desc3'] > 0 ? $reg['cod_desc3'] : 0);
	$tpl->assign('desc3', $reg['desc3'] > 0 ? number_format($reg['desc3'], 2) : 0);
	$tpl->assign('cod_desc4', $reg['cod_desc4'] > 0 ? $reg['cod_desc4'] : 0);
	$tpl->assign('desc4', $reg['desc4'] > 0 ? number_format($reg['desc4'], 2) : 0);
	$tpl->assign('clave', $reg['clave'] > 0 ? $reg['clave'] : 0);
}

$result = $db->query('SELECT codgastos AS cod, descripcion AS desc FROM catalogo_gastos ORDER BY codgastos');
foreach ($result as $reg) {
	$tpl->newBlock('cod');
	$tpl->assign('cod', $reg['cod']);
	$tpl->assign('desc', $reg['desc']);
}

$result = $db->query('SELECT cod, concepto, CASE WHEN tipo = 1 THEN \'COMPRA\' ELSE \'PAGO\' END AS tipo FROM cat_conceptos_descuentos ORDER BY cod');
foreach ($result as $reg) {
	$tpl->newBlock('codDesc');
	$tpl->assign('cod', $reg['cod']);
	$tpl->assign('desc', "$reg[concepto]|$reg[tipo]");
}

$tpl->printToScreen();
?>