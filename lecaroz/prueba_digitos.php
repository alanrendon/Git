<?php
// Prueba de calculo de digitos
include './includes/cheques.inc.php';

/*function _Di($numBanco, $cuenta) {
	// *** Declaración de variables ***
	$swapKey = str_split($numBanco . $cuenta);			// Llave de intercambio (concatenacion de número de banco y cuenta)
	$peso    = array(3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7);		// Peso 3, 7, 1
	
	// Se multiplica cada una de las columnas por los pesos 3-7-1 haciendo
	// caso omiso de las decenas
	for ($i = 0; $i < count($swapKey); $i++) {
		$res[$i] = ($swapKey[$i] * $peso[$i]) % 10;
	}
	
	// Se suman los resultados en forma horizontal, haciendo caso omiso
	// de las decenas en el resultado de la suma
	$residuo = array_sum($res) % 10;
	
	// El resultado de la suma se resta a 10
	$resta = 10 - $residuo;
	
	// El resultado de la resta es el Dígito Verificador de Intercambio - Di,
	// si el resultado es 10 el dígito es cero
	$Di = ($resta < 10) ? $resta : 0;
	
	// Retornar Di
	return $Di;
}

function _Dp($codSeguridad, $transito, $numCuenta, $numFolio) {
	echo "codSeguridad = " . intval($codSeguridad) . "<br>";
	echo "transito = " . intval($transito) . "<br>";
	echo "numCuenta " . floatval($numCuenta) . "<br>";
	echo "folio = " . intval($numFolio) . "<br>";
	
	// Suma aritmética
	$suma = intval($codSeguridad) + intval($transito) + floatval($numCuenta) + intval($numFolio);
	echo "suma = $suma<br>";
	// Suma de valores
	$suma_valores = array_sum(str_split(number_format($suma,0,"","")));
	echo "suma_valores = $suma_valores<br>";
	// Se obtiene el residuo que resulta de dividir suma de valores entre 9
	$residuo = $suma_valores % 9;
	echo "residuo = $residuo<br>";
	// El residuo obtenido se resta a 9
	$resta = 9 - $residuo;
	echo "resta = $resta<br>";
	// El resultado de la resta es el Dígito Verificador de Premarcado - Dp
	$Dp = $resta;
	
	// Retornar Dp
	return $Dp;
}

$di = _Di("014", "65501795375");
echo "Di = $di<br>";
echo "Dp = "._Dp("000","51999014".$di,"65501795375","0000001")."<br>";
echo "Dp = ".Dp("000","51999014".$di,"65501795375","0000001")."<br>";*/
//echo "<br>".fillZero(1011,7);

// Suma aritmética
	/*$suma = intval("000") + intval("51115072".$di) + intval("00170129723") + intval("0000801");
	
	// Suma de valores
	$suma_valores = array_sum(str_split(number_format($suma,0,"","")));
	
	// Se obtiene el residuo que resulta de dividir suma de valores entre 9
	$residuo = $suma_valores % 9;
	
	// El residuo obtenido se resta a 9
	$resta = 9 - $residuo;
	
	// El resultado de la resta es el Dígito Verificador de Premarcado - Dp
	$Dp = $resta;
	
	echo $Dp;
	// Retornar Dp
	//return $Dp;
	
	
$strIni    = "&%STHPASSWORD$";				// Cadena que inicia el modo MICR de la impresora
$strImpIni = "&%STP12500$&%1B$(12500X$";	// Cadena de inicio de impresión de importe con protección especial
$strImpFin = "&%$";							// Cadena de fin de impresión de importe con protección especial
$strBanIni = "&%SMD";						// Cadena de inicio de impresión de banda MICR
$strBanFin = "$";	*/						// Cadena de fin de impresión de banda MICR

$numBanco          = "072";
$codSeguridad      = "000";
$claveTransaccion  = "51";
$plazaCompensacion = "115";

$bandaMICR   = bandaMICR($numBanco,"00682914310","1579",$codSeguridad,$claveTransaccion,$plazaCompensacion);

echo /*"<br>" . $strBanIni . */$bandaMICR/* . $strBanFin*/;
?>