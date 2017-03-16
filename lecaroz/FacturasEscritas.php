<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/dbstatus.php');

if (!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

$_meses = array(
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		cc.rfc
			AS
				rfc_cia,
		\'|\'
		|| f.rfc
		|| \'|\'
		|| COALESCE(serie, \'\')
		|| \'|\'
		|| folio
		|| \'|\'
		|| num_aprobacion
		|| \'|\'
		|| fecha
		|| \'|\'
		|| ROUND(total::numeric, 2)
		|| \'|\'
		|| ROUND(iva::numeric, 2)
		|| \'|\'
		|| f.estado
		|| \'|I||||\'
			AS
				cadena
	FROM
			facturas_escritas f
		LEFT JOIN
			catalogo_companias cc
				USING (num_cia)
	ORDER BY
		rfc_cia,
		serie
';

$result = $db->query($sql);

$rfc = NULL;
foreach ($result as $rec) {
	if ($rfc != $rec['rfc_cia']) {
		if ($rfc != NULL && $ok) {
			fclose($fp);
		}
		
		$rfc = $rec['rfc_cia'];
		
		$filename = '2' . $rfc . '1220102.txt';
		
		if (!($fp = @fopen('facturas/' . $filename . '.txt', 'wb+'))) {
			echo 'No se pudo crear el archivo de datos ' . $filename . "\n";
			
			$ok = FALSE;
		}
		else {
			echo 'Archivo creado ' . $filename . "\n";
			$ok = TRUE;
		}
	}
	
	if ($ok) {
		fwrite($fp, $rec['cadena'] . "\r\n");
	}
}

?>
