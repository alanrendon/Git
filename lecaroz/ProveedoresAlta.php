<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'ultimo':
			$sql = '
				SELECT
					num_proveedor
						AS
							num_pro
				FROM
					catalogo_proveedores
				WHERE
					num_proveedor BETWEEN ' . ($_SESSION['iduser'] != 1 ? ($_SESSION['tipo_usuario'] == 2 ? '9001 AND 9999' : '1 AND 9000') : '1 AND 20000') . '
				ORDER BY
					num_pro
			';
			$result = $db->query($sql);
			
			if ($result) {
				$num = $_SESSION['tipo_usuario'] == 2 ? 9001 : 1;
				foreach ($result as $rec) {
					if ($rec['num_pro'] == $num) {
						$num++;
					}
					else {
						break;
					}
				}
			}
			
			echo $num;
		break;
		
		case 'validar':
			$sql = '
				SELECT
					num_proveedor
						AS
							num_pro
				FROM
					catalogo_proveedores
				WHERE
					num_proveedor = ' . $_REQUEST['num_pro'] . '
			';
			$result = $db->query($sql);
			
			if ($result) {
				echo -1;
			}
			else if ($_SESSION['tipo_usuario'] == 1 && $_REQUEST['num_pro'] > 9000) {
				echo -2;
			}
			else if ($_SESSION['tipo_usuario'] == 2 && $_REQUEST['num_pro'] <= 9000) {
				echo -3;
			}
		break;
		
		case 'descuento':
			$sql = '
				SELECT
					cod,
					concepto,
					CASE
						WHEN tipo = 1 THEN
							\'COMPRA\'
						WHEN tipo = 2 THEN
							\'PAGO\'
					END
						AS
							tipo
				FROM
					cat_conceptos_descuentos
				WHERE
					cod = ' . $_REQUEST['cod'] . '
			';
			$result = $db->query($sql);
			
			if ($result) {
				echo json_encode($result[0]);
			}
		break;
		
		case 'alta':
			function map($value) {
				return trim($value) != '' ? '\'' . utf8_decode($value) . '\'' : 'NULL';
			}
			
			$datos = array_map('map', $_REQUEST);
			
			unset($datos['accion']);
			unset($datos['alta']);
			unset($datos['PPA_ID']);
			unset($datos['PHPSESSID']);
			unset($datos['webfx-tree-cookie-persistence']);
			
			if (!isset($_REQUEST['verfac'])) {
				$datos['verfac'] = 'FALSE';
			}
			
			if (!isset($_REQUEST['restacompras'])) {
				$datos['restacompras'] = 'FALSE';
			}
			
			if (!isset($_REQUEST['para_abono'])) {
				$datos['para_abono'] = 'FALSE';
			}
			
			$sql = '
				INSERT INTO
					catalogo_proveedores
						(
							"' . implode('", "', array_keys($datos)) . '"
						)
					VALUES
						(
							' . implode(', ', array_values($datos)) . '
						)
			';
			$db->query($sql);
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/ProveedoresAlta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		idtipoproveedor
			AS
				id,
		UPPER(descripcion)
			AS
				tipo
	FROM
		tipo_proveedor
	ORDER BY
		id
';
$tipos = $db->query($sql);

if ($tipos) {
	foreach ($tipos as $t) {
		$tpl->newBlock('tipo_proveedor');
		$tpl->assign('id', $t['id']);
		$tpl->assign('tipo', $t['tipo']);
	}
}

$sql = '
	SELECT
		pais
	FROM
		catalogo_paises
	ORDER BY
		pais
';
$paises = $db->query($sql);

if ($paises) {
	foreach ($paises as $p) {
		$tpl->newBlock('pais');
		$tpl->assign('pais', strtoupper(str_replace(array('á', 'é', 'í', 'ó', 'ú', 'ñ'), array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'), $p['pais'])));
		
		if ($p['pais'] == 'México') {
			$tpl->assign('selected', ' selected');
		}
	}
}

$sql = '
	SELECT
		idbanco
			AS
				id,
		nombre
	FROM
		catalogo_bancos
	ORDER BY
		nombre
';
$bancos = $db->query($sql);

if ($bancos) {
	foreach ($bancos as $b) {
		$tpl->newBlock('banco');
		$tpl->assign('id', $b['id']);
		$tpl->assign('nombre', $b['nombre']);
	}
}

$sql = '
	SELECT
		"IdEntidad"
			AS
				id,
		UPPER("Entidad")
			AS
				entidad
	FROM
		catalogo_entidades
	ORDER BY
		"IdEntidad"	
';
$entidades = $db->query($sql);

foreach ($entidades as $e) {
	$tpl->newBlock("entidad");
	$tpl->assign("id", $e['id']);
	$tpl->assign("entidad", $e['entidad']);
}

if ($_SESSION['tipo_usuario'] == 2 || $_SESSION['iduser'] == 1) {
	$tpl->newBlock('extra');
}

$tpl->printToScreen();
?>
