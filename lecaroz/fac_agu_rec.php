<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/pcl.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT catalogo_companias.nombre AS nombre_cia, catalogo_companias.direccion, catalogo_trabajadores.ap_paterno, catalogo_trabajadores.ap_materno, catalogo_trabajadores.nombre, catalogo_puestos.descripcion AS puesto";
$sql .= " FROM catalogo_trabajadores LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_turnos USING (cod_turno) LEFT JOIN
catalogo_puestos USING (cod_puestos) WHERE catalogo_trabajadores.num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " AND";
$sql .= isset($_GET['num_cia']) && $_GET['num_cia'] > 0 ? " catalogo_companias.cia_aguinaldos = $_GET[num_cia] AND" : "";
$sql .= isset($_GET['num_emp']) && $_GET['num_emp'] > 0 ? " num_emp = $_GET[num_emp] AND" : "";
$sql .= " fecha_baja IS NULL /*AND catalogo_trabajadores.cod_turno NOT IN (7) AND catalogo_companias.cia_aguinaldos NOT IN (28, 35, 45, 66, 58, 61, 132, 700, 800, 364)*/ ORDER BY cia_aguinaldos, catalogo_puestos.sueldo DESC, cod_puestos, catalogo_turnos.orden_turno, num_emp";
$result = $db->query($sql);//echo $sql;die;

if (!$result) {
	header("location: ./fac_tra_agu.php?codigo_error=1");
	die;
}

shell_exec("chmod ugo=rwx pcl");
$fp = fopen("pcl/rec_agu_" . $_SESSION['tipo_usuario'] . ".pcl", "w");

if ($_SESSION['tipo_usuario'] == 1) {
	$pcl = "";

	$pcl .= HEADER;
	$pcl .= SetPageSize(LEGAL);
	$pcl .= SetPaperSource(AUTO_SELECT);
	$pcl .= SetAlphanumericID(PLAIN_PAPER);
	$pcl .= SetTopMargin(1);
	$pcl .= SetLeftMargin(0);
	$pcl .= DEFAULT_FONT;
	$pcl .= SetFontPointSize(10);

	$offset = 3;

	for ($i = 0; $i < count($result); $i++) {
		$pcl .= MoveCursorV($offset + 29);
		$pcl .= MoveCursorH(40);
		$pcl .= strtoupper($result[$i]['nombre_cia']);
		$pcl .= MoveCursorV($offset + 33);
		$pcl .= MoveCursorH(32);
		$pcl .= SetFontPointSize(8);
		$pcl .= strtoupper($result[$i]['direccion']);
		$pcl .= MoveCursorV($offset + 97);
		$pcl .= MoveCursorH(113);
		$pcl .= SetFontPointSize(10);
		$pcl .= strtoupper("{$result[$i]['ap_paterno']} {$result[$i]['ap_materno']} {$result[$i]['nombre']}");

		$pcl .= MoveCursorV($offset + 138);
		$pcl .= MoveCursorH(40);
		$pcl .= strtoupper($result[$i]['nombre_cia']);
		$pcl .= MoveCursorV($offset + 142);
		$pcl .= MoveCursorH(32);
		$pcl .= SetFontPointSize(8);
		$pcl .= strtoupper($result[$i]['direccion']);
		$pcl .= MoveCursorV($offset + 202);
		$pcl .= MoveCursorH(113);
		$pcl .= SetFontPointSize(10);
		$pcl .= strtoupper("{$result[$i]['ap_paterno']} {$result[$i]['ap_materno']} {$result[$i]['nombre']}");

		$pcl .= MoveCursorV($offset + 246);
		$pcl .= MoveCursorH(40);
		$pcl .= strtoupper($result[$i]['nombre_cia']);
		$pcl .= MoveCursorV($offset + 250);
		$pcl .= MoveCursorH(32);
		$pcl .= SetFontPointSize(8);
		$pcl .= strtoupper($result[$i]['direccion']);
		$pcl .= MoveCursorV($offset + 307);
		$pcl .= MoveCursorH(113);
		$pcl .= SetFontPointSize(10);
		$pcl .= strtoupper("{$result[$i]['ap_paterno']} {$result[$i]['ap_materno']} {$result[$i]['nombre']}");

		$pcl .= $i < count($result) - 1 ? FORM_FEED : "";
	}

	$pcl .= RESET;
}
else if ($_SESSION['tipo_usuario'] == 2) {
	$pcl = "";

	$pcl .= HEADER;
	$pcl .= SetPageSize(LETTER);
	$pcl .= SetPaperSource(AUTO_SELECT);
	$pcl .= SetAlphanumericID(PLAIN_PAPER);
	$pcl .= SetTopMargin(1);
	$pcl .= SetLeftMargin(0);
	$pcl .= DEFAULT_FONT;
	$pcl .= SetFontPointSize(10);

	$offset = 3;

	for ($i = 0; $i < count($result); $i++) {
		$pcl .= MoveCursorV($offset + 60);
		$pcl .= MoveCursorH(40);
		$pcl .= strtoupper($result[$i]['nombre_cia']);
		$pcl .= MoveCursorV($offset + 64);
		$pcl .= MoveCursorH(32);
		$pcl .= SetFontPointSize(8);
		$pcl .= strtoupper($result[$i]['direccion']);
		$pcl .= MoveCursorV($offset + 68);
		$pcl .= MoveCursorH(102);
		$pcl .= SetFontPointSize(8);
		$pcl .= strtoupper($result[$i]['puesto']);
		$pcl .= MoveCursorV($offset + 121);
		$pcl .= MoveCursorH(135);
		$pcl .= SetFontPointSize(10);
		$pcl .= strtoupper("{$result[$i]['ap_paterno']} {$result[$i]['ap_materno']} {$result[$i]['nombre']}");

		$pcl .= MoveCursorV($offset + 168);
		$pcl .= MoveCursorH(40);
		$pcl .= strtoupper($result[$i]['nombre_cia']);
		$pcl .= MoveCursorV($offset + 172);
		$pcl .= MoveCursorH(32);
		$pcl .= SetFontPointSize(8);
		$pcl .= strtoupper($result[$i]['direccion']);
		$pcl .= MoveCursorV($offset + 223);
		$pcl .= MoveCursorH(133);
		$pcl .= SetFontPointSize(10);
		$pcl .= strtoupper("{$result[$i]['ap_paterno']} {$result[$i]['ap_materno']} {$result[$i]['nombre']}");

		$pcl .= $i < count($result) - 1 ? FORM_FEED : "";
	}

	$pcl .= RESET;
}

fwrite($fp, $pcl);
fclose($fp);

//shell_exec("lp -d S1855 pcl/rec_agu.pcl");
shell_exec("lp -d " . ($_SESSION['tipo_usuario'] == 2 ? 'elite' : /*'general'*/'septimo') . " pcl/rec_agu_" . $_SESSION['tipo_usuario'] . ".pcl");
shell_exec("chmod ugo=r pcl");

header("location: ./fac_tra_agu.php");
?>
