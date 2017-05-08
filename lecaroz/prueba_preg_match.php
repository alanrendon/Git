<?php
$patron = "/([0-9]{2})\/([0-9]{2})\/([0-9]{4}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})/";

preg_match($patron, '01/01/2011 23:59:59', $matches);

echo preg_replace('/[^0-9]/', '', '01/01/2011 23:59:59');die;


$patron = "/^([a-zA-ZñÑ\&]{3,4})([\d]{6})([a-zA-Z0-9]{3})$/";

$cadenas = array(
	'CACC7909015U5',
	'CACC790901',
	'XAXX010101000',
	'XAXX0101010000',
	'PUE790901584',
	'ÑERO950302555'
);

foreach ($cadenas as $c) {
	$r = preg_match_all($patron, $c, $matches);
	
	echo $c . ' => Se encontraron ' . $r . ' coincidencias. ' . print_r($matches, TRUE) . '<br />';
}

$patron = "/\D/";

$cadenas = array(
	'A25',
	'ZD8596',
	'P1236',
	'321X',
	'PUE790901584',
	'987K'
);

foreach ($cadenas as $c) {
	$r = preg_replace($patron, '', $c);
	
	echo $c . ' => ' . $r . '<br />';
}

$patrones = array("/[^a-zA-ZñÑ\s]/", "/Ñ/");

$cadenas = array(
	'CARLOS ALBERTO CANDELARIO',
	'MOLLENDO ÑACAÑACA S.A. DE C.V.'
);

foreach ($cadenas as $c) {
	/*$r = preg_replace("/[^A-ZÑ\s]/", '', $c);
	$r = preg_replace("/Ñ/", 'N', $r);*/$r = preg_replace($patrones, array('', 'N'), $c);
	
	echo $c . ' => ' . $r . '<br />';
}

?>