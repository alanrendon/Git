<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, 'autocommit=yes');

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

function dia_escrito($dia) {
	switch ($dia) {
		case 1: $string = 'Lunes'; break;
		case 2: $string = 'Martes'; break;
		case 3: $string = 'Miércoles'; break;
		case 4: $string = 'Jueves'; break;
		case 5: $string = 'Viernes'; break;
		case 6: $string = 'Sábado'; break;
		case 0: $string = 'Domingo'; break;
		default: $string = $dia;
	}
	
	return $string;
}

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/ros/ros_hoj_dia.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT num_cia, nombre_corto FROM mov_inv_tmp LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia BETWEEN 301 AND 599 AND fecha = '$_GET[fecha]'";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : '';
	$sql .= ' GROUP BY num_cia, nombre_corto ORDER BY num_cia';
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./ros_hoj_dia.php?codigo_error=1'));
	
	foreach ($result as $key => $cia) {
		if ($key > 0) {
			$tpl->assign('hoja.salto', '<br style="page-break-after:always;">');
		}
		
		// Movimientos de compras y ventas
		$sql = "SELECT codmp, nombre, tipomov, cantidad, precio, existencia FROM inventario_real AS ir LEFT JOIN (SELECT num_cia,codmp, tipomov, cantidad,";
		$sql .= " precio FROM mov_inv_tmp WHERE num_cia = $cia[num_cia] AND fecha = '$_GET[fecha]') AS mov USING (num_cia, codmp) LEFT JOIN catalogo_mat_primas AS";
		$sql .= " cmp USING (codmp) WHERE num_cia = $cia[num_cia] AND codmp NOT IN (90, 425, 194, 138, 364, 167, 61, /*170,*/ 169) ORDER BY orden, tipomov";
		$movs = $db->query($sql);
		
		// Movimientos de gastos
		$sql = "SELECT concepto, importe FROM gastos_tmp WHERE num_cia = $cia[num_cia] AND fecha = '$_GET[fecha]'";
		$gas = $db->query($sql);
		
		// Movimientos de prestamos
		$sql = "SELECT nombre, saldo, tipo_mov, importe FROM prestamos_tmp WHERE num_cia = $cia[num_cia] AND fecha = '$_GET[fecha]' ORDER BY nombre";
		$pres = $db->query($sql);
		
		// Fecha del último efectivo
		$sql = "SELECT fecha FROM total_companias WHERE num_cia = $cia[num_cia] AND efectivo > 0 ORDER BY fecha DESC LIMIT 1";
		$last = $db->query($sql);
		if (!$last) {
			$last[0]['fecha'] = $_GET['fecha'];
		}
		
		ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha'], $fecha);
		
		$tpl->newBlock('hoja');
		
		//$cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
		$tpl->assign('num_cia', $cia['num_cia']);
		$tpl->assign('nombre', $cia['nombre_corto']);
		$tpl->assign('dia_esc', dia_escrito(date("w", mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]))));
		$tpl->assign('dia', $fecha[1]);
		$tpl->assign('mes_esc', mes_escrito(intval($fecha[2])));
		$tpl->assign('anio', $fecha[3]);
		
		$ventas = 0;
		$gastos = 0;
		$otros = 0;
		
		$codmp = NULL;
		foreach ($movs as $reg) {
			if ($codmp != $reg['codmp']) {
				$codmp = $reg['codmp'];
				
				$tpl->newBlock('fila');
				$tpl->assign('producto', $reg['nombre']);
				
				// Obtener la existencia al dia dado
				$existencia = $reg['existencia'];
				
				$sql = "SELECT tipo_mov, sum(cantidad) AS cantidad FROM mov_inv_real WHERE num_cia = $cia[num_cia] AND codmp = $reg[codmp] AND fecha >=";
				$sql .= " '$_GET[fecha]' GROUP BY tipo_mov ORDER BY tipo_mov";
				$tmp = $db->query($sql);
				if ($tmp)
					foreach ($tmp as $t)
						$existencia += $t['tipo_mov'] == 't' ? $t['cantidad'] : -$t['cantidad'];
				
				// Movimientos temporales entre la fecha pedida y el ultimo efectivo
				$sql = "SELECT tipomov, sum(cantidad) AS cantidad FROM mov_inv_tmp WHERE num_cia = $cia[num_cia] AND codmp = $reg[codmp] AND fecha BETWEEN";
				$sql .= " cast('{$last[0]['fecha']}' as date) + interval '1 day' AND cast('$_GET[fecha]' as date) - interval '1 day' GROUP BY tipomov ORDER BY tipomov";
				$tmp = $db->query($sql);
				if ($tmp)
					foreach ($tmp as $t)
						$existencia += $t['tipomov'] == 't' ? -$t['cantidad'] : $t['cantidad'];
				
				$tpl->assign('existencia', $existencia != 0 ? number_format($existencia) : '&nbsp;');
			}
			if ($reg['tipomov'] == 'f') $tpl->assign('mercancias', number_format($reg['cantidad']));
			$existencia += $reg['tipomov'] == 'f' ? $reg['cantidad'] : 0;
			$tpl->assign('total', $existencia != 0 ? number_format($existencia) : '&nbsp;');
			$tpl->assign('venta_total', $reg['tipomov'] == 't' ? number_format($reg['cantidad']) : '&nbsp;');
			$tpl->assign('precio_venta', $reg['tipomov'] == 't' ? number_format($reg['precio'], 2) : '&nbsp;');
			$tpl->assign('importe_venta', $reg['tipomov'] == 't' ? number_format($reg['cantidad'] * $reg['precio'], 2, '.', ',') : '&nbsp;');
			$ventas += $reg['tipomov'] == 't' ? round($reg['cantidad'] * $reg['precio'], 2) : 0;
			$existencia -= $reg['tipomov'] == 't' ? $reg['cantidad'] : 0;
			$tpl->assign('para_manyana', $existencia != 0 ? number_format($existencia) : '&nbsp;');
		}
		$tpl->assign('hoja.ventas', number_format($ventas, 2, '.', ','));
		
		if ($gas)
			foreach ($gas as $reg) {
				$tpl->newBlock('gasto');
				$tpl->assign('concepto', $reg['concepto']);
				$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
				$gastos += $reg['importe'];
			}
		$tpl->assign('hoja.total_gastos', number_format($gastos, 2, '.', ','));
		
		if ($pres) {
			$adeudo = 0;
			$prestado = 0;
			$abono = 0;
			$resta = 0;
			foreach ($pres as $reg) {
				$tpl->newBlock('prestamo');
				$tpl->assign('nombre', $reg['nombre']);
				$tpl->assign('adeudo', $reg['saldo'] != 0 ? number_format($reg['saldo'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('prestado', $reg['tipo_mov'] == 'f' ? number_format($reg['importe'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('abono', $reg['tipo_mov'] == 't' ? number_format($reg['importe'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('resta', $reg['saldo'] + ($reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe']) != 0 ? number_format($reg['saldo'] + ($reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe']), 2, '.', ',') : '&nbsp;');
				
				$adeudo += $reg['saldo'];
				$prestado += $reg['tipo_mov'] == 'f' ? $reg['importe'] : 0;
				$abono += $reg['tipo_mov'] == 't' ? $reg['importe'] : 0;
				$resta += $reg['saldo'] + ($reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe']);
				
				$tpl->assign('hoja.adeudo', number_format($adeudo, 2, '.', ','));
				$tpl->assign('hoja.prestado', number_format($prestado, 2, '.', ','));
				$tpl->assign('hoja.abono', number_format($abono, 2, '.', ','));
				$tpl->assign('hoja.resta', number_format($resta, 2, '.', ','));
				
				if ($reg['tipo_mov'] == 'f') {
					$tpl->newBlock('gasto');
					$tpl->assign('concepto', $reg['nombre']);
					$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
					$gastos += $reg['importe'];
					$tpl->assign('hoja.total_gastos', number_format($gastos, 2, '.', ','));
				}
				else if ($reg['tipo_mov'] == 't') {
					$tpl->newBlock('otro');
					$tpl->assign('concepto', $reg['nombre']);
					$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
					$otros += $reg['importe'];
					$tpl->assign('hoja.total_otros', number_format($otros, 2, '.', ','));
				}
			}
		}
		
		$tpl->assign('hoja.total_otros', number_format($otros, 2, '.', ','));
		
		$efectivo = $ventas - $gastos + $otros;
		$tpl->assign('hoja.efectivo', number_format($efectivo, 2, '.', ','));
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');
$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('d') - 1, date('Y'))));

$admins = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('admin', $a['admin']);
}

$result = $db->query('SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 301 AND 599 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>