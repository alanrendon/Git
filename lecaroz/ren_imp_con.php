<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "";

if (isset($_GET['num_local'])) {
	$dir_ofi = "DIAGONAL PATRIOTISMO NO. 1-501, COLONIA HIPODROMO CONDESA, CODIGO POSTAL 06170, MÉXICO, DISTRITO FEDERAL";
	
	$sql = "SELECT nombre_arrendatario AS nombre_loc, carr.nombre AS nombre_arr, cl.representante AS representante_local, carr.representante AS representante_arr, cn.nombre AS";
	$sql .= " nombre_notario, cn.num_notario, giro, num_acta, direccion_local, nombre_aval, bien_avaluo, carr.tipo_persona, fecha_inicio, fecha_final, renta_con_recibo AS renta, agua, mantenimiento AS mant, cargo_daños,";
	$sql .= " cargo_termino FROM catalogo_arrendatarios AS cl LEFT JOIN catalogo_arrendadores AS carr USING (cod_arrendador) LEFT JOIN catalogo_notario AS cn ON (cod_notario =";
	$sql .= " carr.num_notario) WHERE status = 1 AND num_local = $_GET[num_local] ORDER BY cod_arrendador, num_local";
	$result = $db->query($sql);
	
	$tpl = new TemplatePower("./plantillas/ren/" . ($result[0]['tipo_persona'] == 't' ? 'contrato_renta.tpl' : 'contrato_renta2.tpl'));
	$tpl->prepare();
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $result[0]['fecha_inicio'], $tmp1);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $result[0]['fecha_final'], $tmp2);
	$fecha_ini = "$tmp1[1] DE " . mes_escrito($tmp1[2], TRUE) . " DEL $tmp1[3]";
	$fecha_fin = "$tmp2[1] DE " . mes_escrito($tmp2[2], TRUE) . " DEL $tmp2[3]";
	
	$tpl->assign("arrendatario", strtoupper($result[0]['nombre_loc']));
	$tpl->assign("arrendador", strtoupper($result[0]['nombre_arr']));
	$tpl->assign("representante_arrendador", strtoupper($result[0]['representante_arr']));
	$tpl->assign("representante_arrendatario", strtoupper($result[0]['representante_local']));
	$tpl->assign("notario", strtoupper($result[0]['nombre_notario']));
	$tpl->assign("num_notario", $result[0]['num_notario']);
	$tpl->assign("giro", strtoupper($result[0]['giro']));
	$tpl->assign("escritura", $result[0]['num_acta']);
	$tpl->assign("direccion_oficina", $dir_ofi);
	$tpl->assign("direccion_arrendador", $result[0]['direccion_local']);
	$tpl->assign("aval", trim($result[0]['nombre_aval']) != '' ? trim(strtoupper($result[0]['nombre_aval'])) : 'NO SE REQUIRIO AVAL');
	$tpl->assign("direccion_aval", trim($result[0]['nombre_aval']) != '' ? trim(strtoupper($result[0]['bien_avaluo'])) : 'NO SE REQUIRIO AVAL');
	$tpl->assign("fecha_inicial", $fecha_ini);
	$tpl->assign("fecha_final", $fecha_fin);
	$tpl->assign("cantidad_numero", number_format($result[0]['renta'] + $result[0]['agua'] + $result[0]['mant'], 2, ',', '.'));
	$tpl->assign("cantidad_numero1", number_format($result[0]['cargo_daños'], 2, '.', ','));
	$tpl->assign("cantidad_numero2", number_format($result[0]['cargo_termino'], 2, '.', ','));
	$tpl->assign("cantidad_letra", num2string($result[0]['renta'] + $result[0]['agua'] + $result[0]['mant'], 2, '.', ','));
	$tpl->assign("cantidad_letra1", $result[0]['cargo_daños'] != 0 ? num2string($result[0]['cargo_daños'], 2, '.', ',') : '&nbsp;');
	$tpl->assign("cantidad_letra2", $result[0]['cargo_termino'] != 0 ? num2string($result[0]['cargo_termino'], 2, '.', ',') : '&nbsp;');
	
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_imp_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['arr'])) {
	$sql = "SELECT num_local, nombre_local, cod_arrendador AS arr, carr.nombre FROM catalogo_arrendatarios LEFT JOIN catalogo_arrendadores AS carr USING (cod_arrendador) WHERE status = 1";
	$sql .= $_GET['arr'] > 0 ? " AND cod_arrendador = $_GET[arr]" : "";
	$sql .= $_GET['local'] > 0 ? " AND num_local = $_GET[local]" : "";
	$sql .= " ORDER BY cod_arrendador, num_local";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ren_imp_con.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("result");
	
	$arr = NULL;
	foreach ($result as $reg) {
		if ($arr != $reg['arr']) {
			$arr = $reg['arr'];
			
			$tpl->newBlock("arr");
			$tpl->assign("arr", $reg['arr']);
			$tpl->assign("nombre", $reg['nombre']);
		}
		$tpl->newBlock("local");
		$tpl->assign("num_local", $reg['num_local']);
		$tpl->assign("nombre", $reg['nombre']);
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>