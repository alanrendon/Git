<?php
// Prueba fecha
function antiguedad($fecha_alta) {
	// Desglozar elementos de la fecha
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha_alta,$fecha))
		return FALSE;
	
	// Timestamp de la fecha de alta
	$ts_alta = mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]);
	// Timestamp actual
	$ts_current = time();
	// Diferencia
	$diferencia = $ts_current - $ts_alta;
	// Calcular antiguedad
	$antiguedad[0] = date("Y", $diferencia) - 1970;	// Años
	$antiguedad[1] = date("n", $diferencia) - 1;	// Meses
	$antiguedad[2] = date("d", $diferencia) - 1;	// Días
	
	return $antiguedad;
}

function mostrar_antiguedad($fecha_alta) {
	// Desglozar elementos de la fecha
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha_alta,$fecha))
		return FALSE;
	
	$antiguedad = antiguedad($fecha_alta);
	
	// Construir cadena
	//$cadena = "";
	$cadena .= $antiguedad[0] > 0 ? ($antiguedad[0] == 1 ? "$antiguedad[0] Año " : "$antiguedad[0] Años ") : "";
	$cadena .= $antiguedad[1] > 0 ? ($antiguedad[1] == 1 ? "$antiguedad[1] Mes " : "$antiguedad[1] Meses ") : "";
	$cadena .= $antiguedad[2] > 0 ? ($antiguedad[2] == 1 ? "$antiguedad[2] Día" : "$antiguedad[2] Días") : "";
	
	return $cadena;
}
//echo mostrar_antiguedad("07/10/1955");

//$last_saturday = strtotime('last saturday - 3 week');
//
//echo date('d/m/Y', $last_saturday);
//
//$last_friday = strtotime('last saturday - 1 day');
//
//echo '<br />' . date('d/m/Y', $last_friday);

//for ($i = 1; $i <= 5; $i++) {
//	echo date('d/m/Y D', strtotime('last saturday - ' . $i . ' week')) . ' - ' . date('d/m/Y D', strtotime('last saturday - ' . $i . ' week + 6 days')) . '<br />';
//}

echo date('d/m/Y');
?>