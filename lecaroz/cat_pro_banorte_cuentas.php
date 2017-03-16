<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

function filler($str, $length, $chr, $side = TRUE) {
	$tmp = "";
	
	for ($i = 0; $i < $length - strlen($str); $i++)
		$tmp .= $chr;
	
	return $side ? $str . $tmp : $tmp . $str;
}

$patron = '^([a-zA-Z]{3,4})[ |\-]?([0-9]{6})[ |\-]?([a-zA-Z0-9]{3})$';

$chars = array(',', '-', ' ');

//$result = $db->query("SELECT num_proveedor, num_banco, \"IdEntidad\", cuenta FROM catalogo_proveedores cp LEFT JOIN catalogo_entidades ON (\"IdEntidad\" = substr(cp.plaza_banxico, 1, 2)::numeric) LEFT JOIN catalogo_bancos USING (idbanco) WHERE trans = 'TRUE' AND san = 'FALSE' AND rfc ~ '^([a-zA-Z]{3,4})([0-9]{6})([a-zA-Z0-9]{3})$' ORDER BY num_proveedor");
$result = $db->query('SELECT num_proveedor, num_banco, "IdEntidad" AS entidad, cuenta, clabe FROM catalogo_proveedores cp LEFT JOIN catalogo_entidades USING ("IdEntidad") LEFT JOIN catalogo_bancos USING (idbanco) WHERE /*mov_banorte = 1*/ num_proveedor = 482 ORDER BY num_proveedor');
$data = '';
foreach ($result as $reg) {
	$data .= filler($reg['num_proveedor'], '13', ' ');
	$data .= 'AC';
	$data .= filler('', '147', ' ');
	$data .= $reg['num_banco'] == '072' ? '001' : '040';
	$data .= 'PESOS  ';
	$data .= filler($reg['num_banco'], 4, '0', FALSE);
	$data .= filler($reg['entidad'], 2, '0', FALSE);
	$data .= substr($reg['clabe'], 3, 3);
	$data .= filler($reg['num_banco'] == '072' ? $reg['cuenta'] : $reg['clabe'], 20, '0', FALSE);
	if ($reg['num_banco'] == '072') {
		if ($db->query("SELECT num_cia FROM catalogo_companias WHERE clabe_cuenta = '$reg[cuenta]'"))
			$data .= '0';
		else
			$data .= '1';
		$data .= '0';
		$data .= '1';
	}
	else {
		$data .= '0';
		$data .= '1';
		$data .= '1';
	}
	
	$data .= '0';
	$data .= '0';
	$data .= '0';
	$data .= '0';
	$data .= '0';
	$data .= '0';
	$data .= "\r\n";
}
//echo "<pre>$data</pre>";
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=catprocue.txt");
echo $data;
?>