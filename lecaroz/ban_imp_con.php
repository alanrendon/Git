<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_imp_lis.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT num_cia, cf.num_cia_primaria AS pri, nombre_corto, isr, /*impacieps,*/ ieps_gravado, ieps_excento, ret_isr_ren, ret_isr_hon, ret_hon_con, cre_sal, isr_pago, ret_iva_hon, ret_iva_ren, ret_iva_fle,";
	$sql .= " iva_pago, iva_tras, iva_acre, iva_dec FROM impuestos_federales AS if LEFT JOIN catalogo_filiales AS cf USING (num_cia) LEFT JOIN catalogo_companias AS cc USING (num_cia)";
	$sql .= " WHERE mes = $_GET[mes] AND anio = $_GET[anio]";
	$sql .= $_GET['num_cia'] > 0 ? " AND " . (isset($_GET['fil']) ? "num_cia_primaria" : "num_cia") . " = $_GET[num_cia]" : "";
	$sql .= " ORDER BY cf.num_cia_primaria, first DESC, num_cia";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_imp_lis.php?codigo_error=1");
		die;
	}
	
	$gtotal = array('isr' => 0,
					// 'impac' => 0,
					'ieps_gravado' => 0,
					'ieps_excento' => 0,
					'ret_isr_ren' => 0,
					'ret_isr_hon' => 0,
					'ret_hon_con' => 0,
					'cre_sal' => 0,
					'isr_pago' => 0,
					'ret_iva_hon' => 0,
					'ret_iva_ren' => 0,
					'ret_iva_fle' => 0,
					'iva_pago' => 0,
					'iva_tras' => 0,
					'iva_acre' => 0,
					'iva_dec' => 0);
	$pri = NULL;
	$numfilas = 43;
	$filas = $numfilas + 1;
	foreach ($result as $reg) {
		// Si llega al maximo numero de lineas, crear una hoja nueva
		if ($filas > $numfilas) {
			$tpl->newBlock("listado");
			$tpl->assign("mes", mes_escrito($_GET['mes']));
			$tpl->assign("anio", $_GET['anio']);
			$filas = 1;
		}
		// Crear bloque al cambiar grupo de filiales
		if ($pri != $reg['pri']) {
			// Totales del ultimo bloque
			if ($pri != NULL) {
				// Si los totales no caben al final de la hoja, crear una nueva
				if ($filas + 1 > $numfilas) {
					$tpl->newBlock("listado");
					$tpl->assign("mes", mes_escrito($_GET['mes']));
					$tpl->assign("anio", $_GET['anio']);
					$tpl->newBlock("bloque");
					$filas = 1;
				}
				
				$tpl->newBlock("totales");
				foreach ($total as $tag => $value)
					$tpl->assign($tag, number_format($value, 2, ".", ","));
				$filas += 2;
			}
			
			if ($filas > $numfilas) {
				$tpl->newBlock("listado");
				$tpl->assign("mes", mes_escrito($_GET['mes']));
				$tpl->assign("anio", $_GET['anio']);
				$filas = 1;
			}
			
			$pri = $reg['pri'];
			
			$tpl->newBlock("bloque");
			$total = array('isr' => 0,
							// 'impac' => 0,
							'ieps_gravado' => 0,
							'ieps_excento' => 0,
							'ret_isr_ren' => 0,
							'ret_isr_hon' => 0,
							'ret_hon_con' => 0,
							'cre_sal' => 0,
							'isr_pago' => 0,
							'ret_iva_hon' => 0,
							'ret_iva_ren' => 0,
							'ret_iva_fle' => 0,
							'iva_pago' => 0,
							'iva_tras' => 0,
							'iva_acre' => 0,
							'iva_dec' => 0);
		}
		$tpl->newBlock("fila");$tpl->assign("fila", $filas);
		foreach ($reg as $tag => $value)
			if ($tag != 'pri') {
				$tpl->assign($tag, in_array($tag, array('num_cia', 'nombre_corto')) ? $value : ($value != 0 ? number_format($value, 2, ".", ",") : "&nbsp;"));
				if (!in_array($tag, array('num_cia', 'nombre_corto'))) {
					$total[$tag] += $value;
					$gtotal[$tag] += $value;
				}
			}
		$filas++;
	}
	// Totales del ultimo bloque
	if ($pri != NULL) {
		if ($filas + 2 > $numfilas) {
			$tpl->newBlock("listado");
			$tpl->assign("mes", mes_escrito($_GET['mes']));
			$tpl->assign("anio", $_GET['anio']);
			$tpl->newBlock("bloque");
			$filas = 0;
		}
		
		$tpl->newBlock("totales");
		foreach ($total as $tag => $value)
			$tpl->assign("$tag", number_format($value, 2, ".", ","));
	}
	// Totales generales
	$tpl->newBlock("gtotales");
	foreach ($gtotal as $tag => $value) {
		$tpl->assign("$tag", number_format($value, 2, ".", ","));
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>