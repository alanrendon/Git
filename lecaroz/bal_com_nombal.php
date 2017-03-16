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
$tpl->assignInclude("body","./plantillas/bal/bal_com_nombal.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "(SELECT num_cia, nombre_corto, sum(utilidad_neta) AS bal FROM balances_pan LEFT JOIN catalogo_companias USING (num_cia) WHERE anio = $_GET[anio]";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia < 100";
	$sql .= " GROUP BY num_cia, nombre_corto)";
	$sql .= " UNION "; 
	$sql .= "(SELECT num_cia, nombre_corto, sum(utilidad_neta) AS bal FROM balances_ros LEFT JOIN catalogo_companias USING (num_cia) WHERE anio = $_GET[anio]";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia BETWEEN 100 AND 200 OR num_cia IN (702, 704)";
	$sql .= " GROUP BY num_cia, nombre_corto)";
	$sql .= " ORDER BY num_cia";
	$bal = $db->query($sql);
	
	$sql = "SELECT num_cia, sum(importe) AS nom FROM cheques WHERE fecha BETWEEN '01/01/$_GET[anio]' AND '31/12/$_GET[anio]' AND fecha_cancelacion IS NULL AND codgastos = 134";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
	$sql .= " GROUP BY num_cia ORDER BY num_cia";
	$nom = $db->query($sql);
	
	$sql = "SELECT num_cia, saldo_libros FROM saldos WHERE cuenta = 1";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
	$sql .= " ORDER BY num_cia";
	$saldo = $db->query($sql);
	
	$sql = "SELECT num_cia, sum(total) AS pro FROM pasivo_proveedores";
	$sql .= $_GET['num_cia'] > 0 ? " WHERE num_cia = $_GET[num_cia]" : "";
	$sql .= " GROUP BY num_cia ORDER BY num_cia";
	$pro = $db->query($sql);
	
	if (!$bal) {
		header("location: ./bal_com_nombal.php?codigo_error=1");
		die;
	}
	
	function buscarNom($num_cia) {
		global $nom;
		
		if (!$nom)
			return 0;
		
		for ($i = 0; $i < count($nom); $i++)
			if ($num_cia == $nom[$i]['num_cia'])
				return $nom[$i]['nom'];
		
		return 0;
	}
	
	function buscarSaldo($num_cia) {
		global $saldo;
		
		if (!$saldo)
			return 0;
		
		for ($i = 0; $i < count($saldo); $i++)
			if ($num_cia == $saldo[$i]['num_cia'])
				return $saldo[$i]['saldo_libros'];
		
		return 0;
	}
	
	function buscarPro($num_cia) {
		global $pro;
		
		if (!$pro)
			return 0;
		
		for ($i = 0; $i < count($pro); $i++)
			if ($num_cia == $pro[$i]['num_cia'])
				return $pro[$i]['pro'];
		
		return 0;
	}
	
	$numfilas_x_hoja = 60;
	$numfilas = $numfilas_x_hoja;
	for ($i = 0; $i < count($bal); $i++) {
		if ($numfilas == $numfilas_x_hoja) {
			$tpl->newBlock("listado");
			$numfilas = 0;
		}
		
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $bal[$i]['num_cia']);
		$tpl->assign("nombre_cia", $bal[$i]['nombre_corto']);
		
		$general = $bal[$i]['bal'];
		$nomina = buscarNom($bal[$i]['num_cia']);
		$saldo_lib = buscarSaldo($bal[$i]['num_cia']);
		$saldo_pro = buscarPro($bal[$i]['num_cia']);
		$saldo_actual = $saldo_lib - $saldo_pro;
		$diferencia = $general - $nomina + $saldo_actual;
		$prom_mes = $diferencia / 12;
		$prom_dia = $prom_mes / 30;
		
		$tpl->assign("nom", $nomina != 0 ? number_format($nomina, 2, ".", ",") : "&nbsp;");
		$tpl->assign("saldo", $saldo_actual != 0 ? number_format($saldo_actual, 2, ".", ",") : "&nbsp;");
		$tpl->assign("bal", $general != 0 ? number_format($general, 2, ".", ",") : "&nbsp;");
		$tpl->assign("dif", $diferencia != 0 ? number_format($diferencia, 2, ".", ",") : "&nbsp;");
		$tpl->assign("prom_mes", $prom_mes != 0 ? number_format($prom_mes, 2, ".", ",") : "&nbsp;");
		$tpl->assign("prom_dia", $prom_dia != 0 ? number_format($prom_dia, 2, ".", ",") : "&nbsp;");
		
		$numfilas++;
		
		if ($numfilas >= $numfilas_x_hoja)
			$tpl->assign("listado.salto", "<br style=\page-break-after:always;\">");
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign("anio", date("Y") - 1);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>