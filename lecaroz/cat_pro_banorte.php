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

//$result = $db->query("SELECT num_proveedor, nombre, rfc, telefono1, email, direccion FROM catalogo_proveedores WHERE trans = 'TRUE' AND san = 'FALSE' AND rfc ~ '^([a-zA-Z]{3,4})([0-9]{6})([a-zA-Z0-9]{3})$' ORDER BY num_proveedor");
$result = $db->query("SELECT num_proveedor, nombre, rfc, telefono1, lower(email) AS email, direccion, contacto FROM catalogo_proveedores WHERE /*mov_banorte = 1*/ num_proveedor = 482 ORDER BY num_proveedor");
$data = '';
foreach ($result as $reg) {
	$status = ereg($patron, trim($reg['rfc']), $tmp);
	$RFC = $status ? strtoupper("$tmp[1]$tmp[2]$tmp[3]") : '';
	if ($status) {
		$nombre = preg_replace('/[^\w\s]/', '', $reg['nombre']);
		$nombre = preg_replace('/\s{2,}/', ' ', $nombre);
		
		$tel = preg_replace('/[^\d]/', '', $reg['telefono1']);
		if (!ereg('^[0-9]{8,15}$', $tel))
			$tel = '52726721';
		
		if (ereg('^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$', $reg['email']))
			$email = $reg['email'];
		else
			$email = 'noreply@lecaroz.com';
		
		$contacto = trim($reg['contacto']) != '' ? trim($reg['contacto']) : 'MIGUEL ANGEL SANZ';
		
		$data .= filler($reg['num_proveedor'], 13, ' ');
		$data .= 'AR';
		$data .= filler(substr($nombre, 0, 60), 60, ' ');
		$data .= filler($RFC, 13, ' ');
		$data .= filler($tel, 15, ' ');
		$data .= filler($contacto, 20, ' ');
		$data .= filler($email, 39, ' ');
		$data .= filler('', 48, '0');
		$data .= "\r\n";
	}
}
//echo "<pre>$data</pre>";
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=catpro.txt");
echo $data;
?>