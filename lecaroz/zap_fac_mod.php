<?php
// COMPARATIVO DE GAS MENSUAL
// Menu 'No definido'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "La factura ha sido autorizada y no tiene privilegios para modificarla";
$descripcion_error[3] = "La factura ya ha sido pagada y no es posible modificarla";

$admin_users = array(1, 28);

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_fac_mod.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	// Datos de factura
	$data['id']            = $_POST['id'];
	$data['num_cia']       = $_POST['num_cia'];
	$data['num_proveedor'] = $_POST['num_pro'];
	$data['num_fact']      = get_val($_POST['num_fact']);
	$data['entrada']       = get_val($_POST['entrada']);
	$data['fecha']         = $_POST['fecha'];
	$data['fecha_rec']     = $_POST['fecha_rec'];
	$data['fecha_inv']     = $_POST['fecha_inv'];
	$data['concepto']      = strtoupper(trim($_POST['concepto']));
	$data['codgastos']     = $_POST['codgastos'];
	$data['importe']       = get_val($_POST['importe']);
	$data['con_dif_precio'] = strtoupper(trim($_POST['con_dif_precio']));
	$data['dif_precio']    = get_val($_POST['dif_precio']);
	$data['pdesc1']        = get_val($_POST['pdesc1']);
	$data['pdesc2']        = get_val($_POST['pdesc2']);
	$data['pdesc3']        = get_val($_POST['pdesc3']);
	$data['pdesc4']        = get_val($_POST['pdesc4']);
	$data['desc1']         = get_val($_POST['desc1']);
	$data['desc2']         = get_val($_POST['desc2']);
	$data['desc3']         = get_val($_POST['desc3']);
	$data['desc4']         = get_val($_POST['desc4']);
	$data['con_desc1']     = trim(strtoupper($_POST['con_desc1']));
	$data['con_desc2']     = trim(strtoupper($_POST['con_desc2']));
	$data['con_desc3']     = trim(strtoupper($_POST['con_desc3']));
	$data['con_desc4']     = trim(strtoupper($_POST['con_desc4']));
	$data['faltantes']     = get_val($_POST['faltantes']);
	$data['iva']           = get_val($_POST['iva']);
	$data['pisr']          = isset($_POST['apl_ret']) ? get_val($_POST['pisr']) : 0;
	$data['isr']           = get_val($_POST['isr']);
	$data['pivaret']       = isset($_POST['apl_ret']) ? get_val($_POST['pivaret']) : 0;
	$data['ivaret']        = get_val($_POST['ivaret']);
	$data['fletes']        = get_val($_POST['fletes']);
	$data['otros']         = get_val($_POST['otros']);
	$data['con_otros']     = trim(strtoupper($_POST['con_otros']));
	$data['total']         = get_val($_POST['total']);
	$data['clave']         = get_val($_POST['clave']);
	
	$sql = "UPDATE facturas_zap SET num_cia = $data[num_cia], num_proveedor = $data[num_proveedor], num_fact = $data[num_fact],";
	$sql .= " entrada = $data[entrada], fecha = '$data[fecha]', fecha_rec = " . ($data['fecha_rec'] != '' ? "'$data[fecha_rec]'" : 'NULL') . ", fecha_inv = " . ($data['fecha_inv'] != '' ? "'$data[fecha_inv]'" : 'NULL') . ", concepto = '$data[concepto]',";
	$sql .= " codgastos = $data[codgastos], importe = $data[importe], dif_precio = $data[dif_precio], con_dif_precio = '$data[con_dif_precio]', pdesc1 = $data[pdesc1], pdesc2 = $data[pdesc2],";
	$sql .= " pdesc3 = $data[pdesc3], pdesc4 = $data[pdesc4], desc1 = $data[desc1], desc2 = $data[desc2], desc3 = $data[desc3],";
	$sql .= " desc4 = $data[desc4], con_desc1 = '$data[con_desc1]', con_desc2 = '$data[con_desc2]', con_desc3 = '$data[con_desc3]',";
	$sql .= " con_desc4 = '$data[con_desc4]', faltantes = $data[faltantes], iva = $data[iva], pisr = $data[pisr], isr = $data[isr], pivaret = $data[pivaret], ivaret = $data[ivaret],";
	$sql .= " fletes = $data[fletes], otros = $data[otros], con_otros = '$data[con_otros]', total = $data[total], clave = $data[clave], iduser = $_SESSION[iduser], tscap = now() WHERE id = $data[id];\n";
	
	// Datos de faltantes de mercancia
	$sql .= "DELETE FROM faltantes_zap WHERE num_cia = $_POST[num_cia] AND num_proveedor = $_POST[num_pro] AND num_fact = $_POST[num_fact];\n";
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
			
			$sql .= $db->preparar_insert('faltantes_zap', $fal) . ";\n";;
		}
	
	$db->query($sql);
	
	if ($_POST['action'] == 1)
		header('location: ./zap_fac_mod.php');
	else {
		$tpl->newBlock('close');
		$tpl->printToScreen();
	}
	die;
}

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_pro']) || isset($_GET['id'])) {
	$sql = "SELECT * FROM facturas_zap WHERE";
	$sql .= isset($_GET['num_pro']) ? " num_proveedor = $_GET[num_pro] AND num_fact = '$_GET[num_fact]' ORDER BY fecha DESC LIMIT 1" : " id = $_GET[id]";
	$result = $db->query($sql);
	
	if (!$result) {
		header('location: ./zap_fac_mod.php?codigo_error=1');
		die;
	}
	else if ($result[0]['por_aut'] == 't' && !in_array($_SESSION['iduser'], $admin_users)) {
		header('location: ./zap_fac_mod.php?codigo_error=2');
		die;
	}
	else if ($result[0]['folio'] > 0 && !in_array($_SESSION['iduser'], $admin_users)) {
		header('location: ./zap_fac_mod.php?codigo_error=3');
		die;
	}
	
	// Pasar todos los valores a otro registro simplificado
	$reg = $result[0];
	
	$tpl->newBlock('mod');
	$tpl->assign('close', isset($_GET['id']) ? 'self.close()' : "document.location='./zap_fac_mod.php'");
	$tpl->assign('action', isset($_GET['id']) ? 2 : 1);
	
	// Asignar valores al formulario de modificación
	foreach ($reg as $k => $v)
		if (in_array($k, array('num_cia', 'num_proveedor', 'codgastos'))) {
			$tpl->assign($k, $v);
			switch ($k) {
				case 'num_cia': $nombre = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $reg[num_cia]");
					$tpl->assign('nombre_cia', $nombre[0]['nombre']);
					break;
				case 'num_proveedor': $nombre = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $reg[num_proveedor]");
					$tpl->assign('nombre_pro', $nombre[0]['nombre']);
					break;
				case 'codgastos': $desc = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = $reg[codgastos]");
					$tpl->assign('desc', $desc[0]['descripcion']);
			}
		}
		else if (in_array($k, array('id', 'fecha', 'fecha_rec', 'fecha_inv', 'concepto', 'con_desc1', 'con_desc2', 'con_desc3', 'con_desc4', 'con_dif_precio', 'con_otros')))
			$tpl->assign($k, $v);
		else if (in_array($k, array('num_fact', 'entrada', 'clave')))
			$tpl->assign($k, $v != 0 ? $v : '');
		else if (in_array($k, array('importe', 'pdesc1', 'pdesc2', 'pdesc3', 'pdesc4', 'desc1', 'desc2', 'desc3', 'desc4', 'dif_precio', 'faltantes', 'iva', 'pisr', 'isr', 'pivaret', 'ivaret', 'fletes', 'otros', 'total')))
			$tpl->assign($k, $v != 0 ? number_format($v, 2, '.', ',') : '');
		else
			continue;
	
	$tpl->assign('apl_iva', $reg['iva'] > 0 ? 'checked' : '');
	$tpl->assign('apl_ret', $reg['pisr'] > 0 || $reg['pivaret'] > 0 ? 'checked' : '');
	
	// Faltantes
	$falt = $db->query("SELECT * FROM faltantes_zap WHERE num_cia = $reg[num_cia] AND num_proveedor = $reg[num_proveedor] AND num_fact = $reg[num_fact]");
	$numfilas = 10;
	for ($i = 0; $i < $numfilas; $i++) {
		$tpl->newBlock('faltante');
		$tpl->assign('i', $i);
		$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
		$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
		
		if ($falt && isset($falt[$i])) {
			$tpl->assign('modelo', $falt[$i]['modelo']);
			$tpl->assign('color', $falt[$i]['color']);
			$tpl->assign('talla', $falt[$i]['talla']);
			$tpl->assign('piezas', number_format($falt[$i]['piezas'], 0));
			$tpl->assign('precio', number_format($falt[$i]['precio'], 2));
			$tpl->assign('importe_fal', number_format($falt[$i]['importe_fal'], 2));
		}
	}
	
	// CATALOGOS
	$result = $db->query('SELECT num_cia, nombre FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num_cia');
	foreach ($result as $reg) {
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre']);
	}
	
	$result = $db->query('SELECT num_proveedor AS num_pro, nombre, con_desc1, desc1, con_desc2, desc2, con_desc3, desc3, con_desc4, desc4, clave_seguridad AS clave FROM catalogo_proveedores ORDER BY num_proveedor');
	foreach ($result as $reg) {
		$tpl->newBlock('pro');
		$tpl->assign('num_pro', $reg['num_pro']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('con1', trim($reg['con_desc1']));
		$tpl->assign('desc1', $reg['desc1'] > 0 ? number_format($reg['desc1'], 2) : 0);
		$tpl->assign('con2', trim($reg['con_desc2']));
		$tpl->assign('desc2', $reg['desc2'] > 0 ? number_format($reg['desc2'], 2) : 0);
		$tpl->assign('con3', trim($reg['con_desc3']));
		$tpl->assign('desc3', $reg['desc3'] > 0 ? number_format($reg['desc3'], 2) : 0);
		$tpl->assign('con4', trim($reg['con_desc4']));
		$tpl->assign('desc4', $reg['desc4'] > 0 ? number_format($reg['desc4'], 2) : 0);
		$tpl->assign('clave', $reg['clave'] > 0 ? $reg['clave'] : 0);
	}
	
	$result = $db->query('SELECT codgastos AS cod, descripcion AS desc FROM catalogo_gastos ORDER BY codgastos');
	foreach ($result as $reg) {
		$tpl->newBlock('cod');
		$tpl->assign('cod', $reg['cod']);
		$tpl->assign('desc', $reg['desc']);
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');

$provs = $db->query("SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_pro");
foreach ($provs as $pro) {
	$tpl->newBlock('p');
	$tpl->assign('num_pro', $pro['num_pro']);
	$tpl->assign('nombre', $pro['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}
$tpl->printToScreen();
?>