<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT num_cia, codmp, catalogo_mat_primas.nombre AS nombre, tipo_presentacion.descripcion AS unidad FROM inventario_real LEFT JOIN catalogo_mat_primas USING (codmp)";
	$sql .= " LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN tipo_presentacion ON (idpresentacion = presentacion) WHERE";
	$sql .= $_GET['num_cia'] > 0 && $_GET['admin'] == "" ? " num_cia = $_GET[num_cia] AND" : " num_cia < 100 AND";
	$sql .= $_GET['admin'] > 0 ? " idadministrador = $_GET[admin] AND num_cia < 100 AND" : " num_cia < 100 AND";
	$sql .= " (existencia != 0 OR (num_cia, codmp) IN (SELECT num_cia, codmp FROM mov_inv_real LEFT JOIN catalogo_companias USING (num_cia) WHERE";	
	$sql .= $_GET['num_cia'] > 0 && $_GET['admin'] == "" ? " num_cia = $_GET[num_cia] AND" : " num_cia < 100 AND";
	$sql .= $_GET['admin'] > 0 ? " idadministrador = $_GET[admin] AND num_cia < 100 AND" : " num_cia < 100 AND";
	$sql .= " fecha >= CURRENT_DATE - interval '3 months' GROUP BY num_cia, codmp))";
	$sql .= " AND codmp NOT IN (69, 30, 148, 149, 416, 170, 169, 90, 538, 31, 291, 34, 5, 1, 496, 310, 161, 265, 181, 70, 627, 727, 508, 580, 250, 311, 811, 60, 900)";
	$sql .= " ORDER BY num_cia, controlada DESC, nombre";
	$result = $db->query($sql);
	
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/ped/memo_pedido_v2.tpl" );
	$tpl->prepare();
	
	if (!$result) {
		$tpl->printToScreen("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	$sql = "SELECT num_cia, count(codmp) AS filas FROM inventario_real LEFT JOIN catalogo_mat_primas USING (codmp)";
	$sql .= " LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN tipo_presentacion ON (idpresentacion = presentacion) WHERE";
	$sql .= $_GET['num_cia'] > 0 && $_GET['admin'] == "" ? " num_cia = $_GET[num_cia] AND" : " num_cia < 100 AND";
	$sql .= $_GET['admin'] > 0 ? " idadministrador = $_GET[admin] AND num_cia < 100 AND" : " num_cia < 100 AND";
	$sql .= " (existencia != 0 OR (num_cia, codmp) IN (SELECT num_cia, codmp FROM mov_inv_real LEFT JOIN catalogo_companias USING (num_cia) WHERE";	
	$sql .= $_GET['num_cia'] > 0 && $_GET['admin'] == "" ? " num_cia = $_GET[num_cia] AND" : " num_cia < 100 AND";
	$sql .= $_GET['admin'] > 0 ? " idadministrador = $_GET[admin] AND num_cia < 100 AND" : " num_cia < 100 AND";
	$sql .= " fecha >= CURRENT_DATE - interval '3 months' GROUP BY num_cia, codmp))";
	$sql .= " AND codmp NOT IN (69, 30, 148, 149, 416, 170, 169, 90, 538, 31, 291, 34, 5, 1, 496, 310, 161, 265, 181, 70, 627, 727, 508, 580, 250, 311, 811, 60, 900)";
	$sql .= " GROUP BY num_cia ORDER BY num_cia";
	$nfilas = $db->query($sql);
	
	function buscar($num_cia) {
		global $nfilas;
		
		foreach ($nfilas as $filas)
			if ($num_cia == $filas['num_cia'])
				return $filas['filas'];
	}
	
	$mes = mes_escrito($_GET['mes'], TRUE);
	$anio = $_GET['anio'];
	$fecha = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
	
	$text1 = "para el mes que se esta por solicitar";
	$text2 = "<font color=\"#CC0000\"><strong>complementarios</strong></font> para el mes en <font color=\"#CC0000\"><strong>curso</strong></font>";
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				if ($filas < $maxfilas) {
					if ($col == 1 && $filas < $maxfilas) {
						for ($i = $filas; $i < $maxfilas; $i++) {
							$tpl->newBlock("fila_1");
							$tpl->assign("codmp", "&nbsp;");
							$tpl->assign("nombre", "&nbsp;");
							$tpl->assign("unidad", "&nbsp;");
						}
						$tpl->newBlock("col_2");
						for ($i = 1; $i < $maxfilas; $i++) {
							$tpl->newBlock("fila_2");
							$tpl->assign("codmp", "&nbsp;");
							$tpl->assign("nombre", "&nbsp;");
							$tpl->assign("unidad", "&nbsp;");
						}
					}
					else {
						for ($i = $filas; $i < $maxfilas; $i++) {
							$tpl->newBlock("fila_2");
							$tpl->assign("codmp", "&nbsp;");
							$tpl->assign("nombre", "&nbsp;");
							$tpl->assign("unidad", "&nbsp;");
						}
					}
				}
				
				$tpl->newBlock("footer");
				$tpl->assign("admin", strtoupper($datos_cia[0]['nombre_administrador']));
				//$tpl->newBlock("reverso");
				//$hojas++;
				$salto = "<br style=\"page-break-after:always;\">" . ($hojas % 2 == 1 ? "<br style=\"page-break-after:always;\">" : "");
				$tpl->assign("memo.salto", $salto);
			}
			
			$num_cia = $reg['num_cia'];
			
			// Crear memo
			$tpl->newBlock("memo");
			// Crear encabezado de memo
			$tpl->newBlock("memo_header");
			$tpl->assign("num_cia", $num_cia);
			$datos_cia = $db->query("SELECT nombre, nombre_administrador FROM catalogo_companias LEFT JOIN catalogo_administradores USING (idadministrador) WHERE num_cia = $num_cia");
			$tpl->assign("nombre_cia", $datos_cia[0]['nombre']);
			$tpl->assign("fecha", $fecha);
			$tpl->assign("mes", $mes);
			$tpl->assign("anio", $anio);
			$enc = $db->query("SELECT nombre_fin FROM encargados WHERE num_cia = $num_cia ORDER BY anio DESC, mes DESC LIMIT 1");
			$tpl->assign("encargado", strtoupper($enc[0]['nombre_fin']));
			$tpl->assign("texto", empty($_GET['com']) ? $text1 : $text2);
			
			// Crear primera columna de productos
			$tpl->newBlock("table");
			$tpl->newBlock("col_1");
			
			$filas_cia = buscar($num_cia);
			
			$maxfilas = $filas_cia > 42 * 2 ? 47 : 42;
			$filas_cia -= $maxfilas * 2;
			
			$hojas = 1;
			$col = 1;
			$filas = 1;
		}
		if ($col == 2 && $filas >= $maxfilas) {
			$tpl->assign("table.salto", "<br style=\"page-break-after:always;\">");
			// Crear primera columna de productos
			$tpl->newBlock("table");
			$tpl->newBlock("col_1");
			
			$maxfilas = $filas_cia > 47 * 2 ? 53 : 47;
			$filas_cia -= $maxfilas * 2;
			
			$hojas++;
			$col = 1;
			$filas = 1;
		}
		if ($col == 1 && $filas >= $maxfilas) {
			// Crear primera columna de productos
			$tpl->newBlock("col_2");
			
			$col = 2;
			$filas = 1;
		}
		$tpl->newBlock("fila_" . $col);
		$tpl->assign("codmp", $reg['codmp']);
		$tpl->assign("nombre", $reg['nombre']);
		$tpl->assign("unidad", $reg['unidad']);
		$filas++;
	}
	if ($num_cia != NULL) {
		if ($filas < $maxfilas) {
			if ($col == 1 && $filas < $maxfilas) {
				for ($i = $filas; $i < $maxfilas; $i++) {
					$tpl->newBlock("fila_1");
					$tpl->assign("codmp", "&nbsp;");
					$tpl->assign("nombre", "&nbsp;");
					$tpl->assign("unidad", "&nbsp;");
				}
				$tpl->newBlock("col_2");
				for ($i = 1; $i < $maxfilas; $i++) {
					$tpl->newBlock("fila_2");
					$tpl->assign("codmp", "&nbsp;");
					$tpl->assign("nombre", "&nbsp;");
					$tpl->assign("unidad", "&nbsp;");
				}
			}
			else {
				for ($i = $filas; $i < $maxfilas; $i++) {
					$tpl->newBlock("fila_2");
					$tpl->assign("codmp", "&nbsp;");
					$tpl->assign("nombre", "&nbsp;");
					$tpl->assign("unidad", "&nbsp;");
				}
			}
		}
		
		$tpl->newBlock("footer");
		$tpl->assign("admin", strtoupper($datos_cia[0]['nombre_administrador']));
		//$tpl->newBlock("reverso");
	}
	
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ped/ped_mem_ped_v2.tpl");
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