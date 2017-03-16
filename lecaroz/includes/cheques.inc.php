<?php
// Conjunto de funciones para el proceso de generación de cheques

// +-----------------------------------------------------------------------------------------------------------+
// | DIGITO VERIFICADOR DE INTERCAMBIO                                                                         |
// |                                                                                                           |
// | integer Di(string numBanco, string cuenta)                                                                |
// |                                                                                                           |
// | Calcula el Dígito Verificador de Intercambio para un cheque. Retorna un dato de tipo integer.             |
// |                                                                                                           |
// | string numBanco => Cadena de 3 dígitos con el número del banco                                            |
// | stirng cuenta   => Cadena de 11 dígitos con el número de cuenta                                           |
// +-----------------------------------------------------------------------------------------------------------+
function Di($numBanco, $cuenta) {
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

// +-----------------------------------------------------------------------------------------------------------+
// | DIGITO VERIFICADOR DE PREMARCADO                                                                          |
// |                                                                                                           |
// | integer Dp(integer codSeguridad, integer transito, integer numCuenta, integer numFolio)                   |
// |                                                                                                           |
// | Calcula el Dígito Verificador de Premarcado para un cheque. Retorna un dato de tipo integer.              |
// |                                                                                                           |
// | integer codSeguridad => Código de seguridad                                                               |
// | integer transito     => Tránsito                                                                          |
// | integer numCuenta    => Número de Cuenta                                                                  |
// | integer numFolio     => Número de Folio                                                                   |
// +-----------------------------------------------------------------------------------------------------------+
function Dp($codSeguridad, $transito, $numCuenta, $numFolio) {
	// Suma aritmética
	$suma = intval($codSeguridad, 10) + intval($transito, 10) + floatval($numCuenta) + intval($numFolio, 10);
	
	// Suma de valores
	$suma_valores = array_sum(str_split(number_format($suma, 0, '', '')));
	
	// Se obtiene el residuo que resulta de dividir suma de valores entre 9
	$residuo = $suma_valores % 9;
	
	// El residuo obtenido se resta a 9
	$resta = 9 - $residuo;
	
	// El resultado de la resta es el Dígito Verificador de Premarcado - Dp
	$Dp = $resta;
	
	// Retornar Dp
	return $Dp;
}

// +-----------------------------------------------------------------------------------------------------------+
// | GENERADOR DE BANDA MICR                                                                                   |
// |                                                                                                           |
// | string bandaMICR(string numBanco, string numCuenta, string numFolio, string codSeguridad,                 |
// | string claveTransaccion, string plazaCompensacion)                                                        |
// |                                                                                                           |
// | Genera la Banda de Impresión MICR.                                                                        |
// |                                                                                                           |
// | string numBanco          => Número de Banco                                                               |
// | string numCuenta         => Número de Cuenta                                                              |
// | string numFolio          => Número de Folio                                                               |
// | string codSeguridad      => Código de Seguridad                                                           |
// | string claveTransaccion  => Clave de Transacción                                                          |
// | string plazaCompensacion => Plaza de Compensación                                                         |
// +-----------------------------------------------------------------------------------------------------------+
function bandaMICR($numBanco, $numCuenta, $numFolio, $codSeguridad, $claveTransaccion, $plazaCompensacion) {
	// *** Declaración de variables ***
	$Di = Di($numBanco, $numCuenta);												// Dígito verificador de intercambio
	$transito = $claveTransaccion . $plazaCompensacion . $numBanco . $Di;			// Tránsito
	$Dp = Dp($codSeguridad, $transito, $numCuenta, $numFolio);	// Dígito verificador de premarcado
	
	// Representar número de folio como una cadena de 7 caractéres
	$num_ceros = 7 - strlen($numFolio);		// Calcula el número de ceros a la izquierda del folio
	// Si num_ceros > 0, rellenar con ceros a la izquierda
	if ($num_ceros > 0) {
		$ceros = '';
		for ($i = 0; $i < $num_ceros; $i++)
			$ceros .= '0';
		$strFolio = $ceros.$numFolio;
	}
	// Si no, strFolio = $numFolio
	else
		$strFolio = $numFolio;
	
	// Generar Banda de Impresión MICR
	$bandaMICR = $codSeguridad . $Dp . ':' . $transito . ':' . $numCuenta . ';' . $strFolio;
	
	// Retornar cadena generada
	return $bandaMICR;
}

// +-----------------------------------------------------------------------------------------------------------+
// | GENERADOR DE BANDA MICR (SIN FORMATO)                                                                     |
// |                                                                                                           |
// | string bandaMICR(string numBanco, string numCuenta, string numFolio, string codSeguridad,                 |
// |                                                                                                           |
// | Genera la Banda de Impresión MICR (sin formato para impresora).                                           |
// |                                                                                                           |
// | string bandaMICR => Banda de Impresión MICR                                                               |
// | string importe   => Importe del Cheque                                                                    |
// +-----------------------------------------------------------------------------------------------------------+
function pseudoBanda($bandaMICR, $importe) {
	// Descomponer banda micro en todos sus componentes
	ereg("([0-9]{3})([0-9]{1}):([0-9]{8})([0-9]{1}):([0-9]{11});([0-9]{7})",$bandaMICR,$comp);
	
	// Convertir importe en una cadena de 10 caractéres
	$temp = str_replace('.', '', number_format($importe, 2, '',''));
	$num_ceros = 10 - strlen($temp);
	// Si num_ceros > 0; rellenar con ceros a la izquierda
	if ($num_ceros > 0) {
		$ceros = "";
		for ($i = 0; $i < $num_ceros; $i++)
			$ceros .= '0';
		$strImporte = $ceros . $temp;
	}
	// Si no, strImporte = temp
	else
		$strImporte = $temp;
	
	// Reordenar datos para generar la pseudo cadena
	$pseudostr = /*$comp[1] . ' ' . $comp[2] . ' :' . $comp[3] . ' ' . $comp[4] . ' ;' . */$comp[5] . ' ' . $comp[6]/* . ' ' . $strImporte . '$'*/;
	
	// Retornar cadena generada
	return $pseudostr;
}

function cuenta($cuenta) {
	if (strlen($cuenta) != 11)
		return FALSE;
	
	//return substr($cuenta, 2, 3) . '-' . substr($cuenta, 5, 5) . '-' . substr($cuenta, 10);
	return substr($cuenta, -10);
}

// +-----------------------------------------------------------------------------------------------------------+
// | NÚMERO A CADENA                                                                                           |
// | string num2string(float num)                                                                              |
// |                                                                                                           |
// | Convierte un número flotante en su equivalente textual.                                                   |
// |                                                                                                           |
// | integer num => Número a convertir                                                                         |
// +-----------------------------------------------------------------------------------------------------------+
function num2string($num) {
	// Desglozar número
	$dec_millon = floor($num / 10000000);
	$millones   = floor(($num % 10000000) / 1000000);
	$cen_millar = floor((($num % 10000000) % 1000000) / 100000);
	$dec_millar = floor(((($num % 10000000) % 1000000) % 100000) / 10000);
	$millares   = floor((((($num % 10000000) % 1000000) % 100000) % 10000) / 1000);
	$centenas   = floor(((((($num % 10000000) % 1000000) % 100000) % 10000) % 1000) / 100);
	$decenas    = floor((((((($num % 10000000) % 1000000) % 100000) % 10000) % 1000) % 100) / 10);
	$unidades   = (((((($num % 10000000) % 1000000) % 100000) % 10000) % 1000) % 100) % 10;
	
	$cadena = "";
	
	// Decenas de millon
	if ($dec_millon > 0) {
		switch ($dec_millon) {
			case 1: $cadena .= ($millones > 0)?(($millones > 5)?"DIECI":""):"DIEZ MILLONES "; break;
			case 2: $cadena .= ($millones > 0)?"VEINTI":"VEINTE MILLONES "; break;
			case 3: $cadena .= ($millones > 0)?"TREINTA Y ":"TREINTA MILLONES "; break;
			case 4: $cadena .= ($millones > 0)?"CUARENTA Y ":"CUARENTA MILLONES "; break;
			case 5: $cadena .= ($millones > 0)?"CINCUENTA Y ":"CINCUENTA MILLONES "; break;
			case 6: $cadena .= ($millones > 0)?"SESENTA Y ":"SESENTA MILLONES "; break;
			case 7: $cadena .= ($millones > 0)?"SETENTA Y ":"SETENTA MILLONES "; break;
			case 8: $cadena .= ($millones > 0)?"OCHENTA Y ":"OCHENTA MILLONES "; break;
			case 9: $cadena .= ($millones > 0)?"NOVENTA Y ":"NOVENTA MILLONES "; break;
		}
	}
	// Millones
	if ($millones > 0) {
		switch ($millones) {
			case 1: $cadena .= ($dec_millon == 1)?"ONCE MILLONES ":"UN MILLON "; break;
			case 2: $cadena .= ($dec_millon == 1)?"DOCE MILLONES ":"DOS MILLONES "; break;
			case 3: $cadena .= ($dec_millon == 1)?"TRECE MILLONES ":"TRES MILLONES "; break;
			case 4: $cadena .= ($dec_millon == 1)?"CATORCE MILLONES ":"CUATRO MILLONES "; break;
			case 5: $cadena .= ($dec_millon == 1)?"QUINCE MILLONES ":"CINCO MILLONES "; break;
			case 6: $cadena .= "SEIS MILLONES "; break;
			case 7: $cadena .= "SIETE MILLONES "; break;
			case 8: $cadena .= "OCHO MILLONES "; break;
			case 9: $cadena .= "NUEVE MILLONES "; break;
		}
	}
	// Centenas de millar
	if ($cen_millar > 0) {
		switch ($cen_millar) {
			case 1: $cadena .= ($dec_millar > 0 || $millares > 0)?"CIENTO ":"CIEN "; break;
			case 2: $cadena .= "DOSCIENTOS "; break;
			case 3: $cadena .= "TRESCIENTOS "; break;
			case 4: $cadena .= "CUATROCIENTOS "; break;
			case 5: $cadena .= "QUINIENTOS "; break;
			case 6: $cadena .= "SEISCIENTOS "; break;
			case 7: $cadena .= "SETECIENTOS "; break;
			case 8: $cadena .= "OCHOCIENTOS "; break;
			case 9: $cadena .= "NOVECIENTOS "; break;
		}
		if ($dec_millar == 0 && $millares == 0)
			$cadena .= "MIL ";
	}
	// Decenas de millar
	if ($dec_millar > 0) {
		switch ($dec_millar) {
			case 1: $cadena .= ($millares > 0)?(($millares > 5)?"DIECI":""):"DIEZ MIL "; break;
			case 2: $cadena .= ($millares > 0)?"VEINTI":"VEINTE MIL "; break;
			case 3: $cadena .= ($millares > 0)?"TREINTA Y ":"TREINTA MIL "; break;
			case 4: $cadena .= ($millares > 0)?"CUARENTA Y ":"CUARENTA MIL "; break;
			case 5: $cadena .= ($millares > 0)?"CINCUENTA Y ":"CINCUENTA MIL "; break;
			case 6: $cadena .= ($millares > 0)?"SESENTA Y ":"SESENTA MIL "; break;
			case 7: $cadena .= ($millares > 0)?"SETENTA Y ":"SETENTA MIL "; break;
			case 8: $cadena .= ($millares > 0)?"OCHENTA Y ":"OCHENTA MIL "; break;
			case 9: $cadena .= ($millares > 0)?"NOVENTA Y ":"NOVENTA MIL "; break;
		}
	}
	// Millares
	if ($millares > 0) {
		switch ($millares) {
			case 1: $cadena .= ($dec_millar == 1)?"ONCE MIL ":"UN MIL "; break;
			case 2: $cadena .= ($dec_millar == 1)?"DOCE MIL ":"DOS MIL "; break;
			case 3: $cadena .= ($dec_millar == 1)?"TRECE MIL ":"TRES MIL "; break;
			case 4: $cadena .= ($dec_millar == 1)?"CATORCE MIL ":"CUATRO MIL "; break;
			case 5: $cadena .= ($dec_millar == 1)?"QUINCE MIL ":"CINCO MIL "; break;
			case 6: $cadena .= "SEIS MIL "; break;
			case 7: $cadena .= "SIETE MIL "; break;
			case 8: $cadena .= "OCHO MIL "; break;
			case 9: $cadena .= "NUEVE MIL "; break;
		}
	}
	// Centenas
	if ($centenas > 0) {
		switch ($centenas) {
			case 1: $cadena .= ($decenas > 0 || $unidades > 0)?"CIENTO ":"CIEN "; break;
			case 2: $cadena .= "DOSCIENTOS "; break;
			case 3: $cadena .= "TRESCIENTOS "; break;
			case 4: $cadena .= "CUATROCIENTOS "; break;
			case 5: $cadena .= "QUINIENTOS "; break;
			case 6: $cadena .= "SEISCIENTOS "; break;
			case 7: $cadena .= "SETECIENTOS "; break;
			case 8: $cadena .= "OCHOCIENTOS "; break;
			case 9: $cadena .= "NOVECIENTOS "; break;
		}
	}
	// Decenas
	if ($decenas > 0) {
		switch ($decenas) {
			case 1: $cadena .= ($unidades > 0)?(($unidades > 5)?"DIECI":""):"DIEZ "; break;
			case 2: $cadena .= ($unidades > 0)?"VEINTI":"VEINTE "; break;
			case 3: $cadena .= ($unidades > 0)?"TREINTA Y ":"TREINTA "; break;
			case 4: $cadena .= ($unidades > 0)?"CUARENTA Y ":"CUARENTA "; break;
			case 5: $cadena .= ($unidades > 0)?"CINCUENTA Y ":"CINCUENTA "; break;
			case 6: $cadena .= ($unidades > 0)?"SESENTA Y ":"SESENTA "; break;
			case 7: $cadena .= ($unidades > 0)?"SETENTA Y ":"SETENTA "; break;
			case 8: $cadena .= ($unidades > 0)?"OCHENTA Y ":"OCHENTA "; break;
			case 9: $cadena .= ($unidades > 0)?"NOVENTA Y ":"NOVENTA "; break;
		}
	}
	// Unidades
	if ($unidades > 0) {
		switch ($unidades) {
			case 1: $cadena .= ($decenas == 1)?"ONCE ":(($decenas > 1)?"UN ":"UN "); break;
			case 2: $cadena .= ($decenas == 1)?"DOCE ":"DOS "; break;
			case 3: $cadena .= ($decenas == 1)?"TRECE ":"TRES "; break;
			case 4: $cadena .= ($decenas == 1)?"CATORCE ":"CUATRO "; break;
			case 5: $cadena .= ($decenas == 1)?"QUINCE ":"CINCO "; break;
			case 6: $cadena .= "SEIS "; break;
			case 7: $cadena .= "SIETE "; break;
			case 8: $cadena .= "OCHO "; break;
			case 9: $cadena .= "NUEVE "; break;
		}
	}
	// Obtener centavos
	//$centavos = floor((number_format($num,2,".","") - floor($num)) * 100);
	$temp = number_format($num,2,".","");
	$centavos = substr($temp,strpos($temp,".")+1,2);
	
	// Concatenar la palabra 'PESOS'
	if ($num < 2 && $num >= 1)
		$cadena .= "PESO ";
	else	
		$cadena .= "PESOS ";
	
	// Concatenar centavos
	//$cadena .= " ".((strlen($centavos) > 1)?$centavos:"0".$centavos)."/100";
	$cadena .= $centavos."/100 ";
	
	// Concatenar la palabra 'M.N.'
	$cadena .= "M.N.";
	
	return $cadena;
}

function mes($mes, $mayusculas = FALSE) {
	// Evaluar $mes
	switch ($mes) {
		case 1:  $string = "Enero";      break;
		case 2:  $string = "Febrero";    break;
		case 3:  $string = "Marzo";      break;
		case 4:  $string = "Abril";      break;
		case 5:  $string = "Mayo";       break;
		case 6:  $string = "Junio";      break;
		case 7:  $string = "Julio";      break;
		case 8:  $string = "Agosto";     break;
		case 9:  $string = "Septiembre"; break;
		case 10: $string = "Octubre";    break;
		case 11: $string = "Noviembre";  break;
		case 12: $string = "Diciembre";  break;
		default: $string = "";           break;
	}
	
	// Si $mayusculas = TRUE, retornar la cadena en mayúsculas
	if ($mayusculas == TRUE)
		return strtoupper($string);
	else
		return $string;
}

function strFecha($fecha, $mayusculas = FALSE) {
	// Descomponer fecha en sus componentes (día/mes/año) (en caso de error, retornar FALSE)
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha,$comp))
		return FALSE;
	
	// Contruir cadena
	$strFecha = $comp[1]." de ".mes($comp[2])." de ".$comp[3];
	
	// Si $mayusculas = TRUE, retornar la cadena en mayúsculas
	if ($mayusculas == TRUE)
		return strtoupper($strFecha);
	else
		return $strFecha;
}

function strRFC($rfc, $mayusculas = FALSE) {
	// Descomponer RFC en sus componentes (iniciales fecha homoclave) (en caso de error, retornar FALSE)
	if (!ereg("([a-zA-Z]{3,4})([0-9]{6})([0-9a-zA-Z]{3})",$rfc,$comp))
		return FALSE;
	
	// Construir cadena
	$strRFC = $comp[1]." ".$comp[2]." ".$comp[3];
	
	// Si $mayusculas = TRUE, retornar la cadena en mayúsculas
	if ($mayusculas == TRUE)
		return strtoupper($strRFC);
	else
		return $strRFC;
}

function fillZero($num, $strSize = 10, $dec = FALSE) {
	// Formatear número
	if ($dec == TRUE)	// Interpretar decimales y eliminar el punto
		$temp = str_replace(".","",number_format($num,2,"",""));
	else		// Solo interprentar parte entera
		$temp = number_format(floor($num),0,"","");
	
	// Representar número como una cadena de n caracteres
	$num_ceros = $strSize - strlen($temp);		// Calcula el número de ceros a la izquierda del número
	// Si num_ceros > 0, rellenar con ceros a la izquierda
	if ($num_ceros > 0) {
		$ceros = "";
		for ($i=0; $i<$num_ceros; $i++)
			$ceros .= "0";
		$string = $ceros.$temp;
	}
	// Si no, $string = $temp
	else
		$string = $temp;
	
	// Regresar cadena
	return $string;
}

// ---------------------------------------------------------------------------------------------------------------------------
// Si no existe la función 'str_split' crearla
if (!function_exists('str_split')) {
   function str_split($string, $split_length = 1) {
       if (!is_numeric($split_length)) {
           trigger_error('str_split() expects parameter 2 to be long, ' . gettype($split_length) . ' given', E_USER_WARNING);
           return FALSE;
       }

       if ($split_length < 1) {
           trigger_error('str_split() The the length of each segment must be greater then zero', E_USER_WARNING);
           return FALSE;
       }

       preg_match_all('/.{1,' . $split_length . '}/s', $string, $matches);
       return $matches[0];
   }
}
?>