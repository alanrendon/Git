<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Formato de archivo no valido";
$descripcion_error[2] = "Una de las compañías en el archivo no existe en el catalogo";
$descripcion_error[3] = "Los importes de los depositos y de los billetes capturados no coinciden, El total de depositos es de ";

$db = new DBclass($dsn, "autocommit=yes");

function buscar_cia($array, $num_cia) {
	$num_elementos = count($array);
	if ($num_elementos < 1)
		return FALSE;
	
	$count = 0;
	for ($i = 0; $i < $num_elementos; $i++)
		if ($array[$i]['num_cia'] == $num_cia)
			return $i;
	
	return FALSE;
}

if (isset($_POST['MAX_FILE_SIZE'])) {
	$_SESSION['exp'] = $_POST;
	
	$sql = "SELECT num_cia, nombre, clabe_cuenta FROM catalogo_companias ORDER BY num_cia";
	$cias = $db->query($sql);
	
	// Cantidad de billetes por denominación
	$bil['b1000'] = (int)$_POST['b1000'];
	$bil['b500']  = (int)$_POST['b500'];
	$bil['b200']  = (int)$_POST['b200'];
	$bil['b100']  = (int)$_POST['b100'];
	$bil['b50']   = (int)$_POST['b50'];
	$bil['b20']   = (int)$_POST['b20'];
	$bil['b10']   = (int)$_POST['b10'];
	$bil['b5']    = (int)$_POST['b5'];
	$bil['b2']    = (int)$_POST['b2'];
	$bil['b1']    = (int)$_POST['b1'];
	$bil['b050']  = (int)$_POST['b050'];
	$bil['b020']  = (int)$_POST['b020'];
	$bil['b010']  = (int)$_POST['b010'];
	$bil['b005']  = (int)$_POST['b005'];
	
	// Valor de cada billete
	$val['b1000'] = 1000;
	$val['b500']  = 500;
	$val['b200']  = 200;
	$val['b100']  = 100;
	$val['b50']   = 50;
	$val['b20']   = 20;
	$val['b10']   = 10;
	$val['b5']    = 5;
	$val['b2']    = 2;
	$val['b1']    = 1;
	$val['b050']  = 0.50;
	$val['b020']  = 0.20;
	$val['b010']  = 0.10;
	$val['b005']  = 0.05;
	
	// ****************************************************************
	// * FUNCION PARA DISTRIBUIR CHEQUES ENTRE LOS DEPOSITOS          *
	// ****************************************************************
	function cheques(&$dep, $num_cia, $cheque) {
		for ($i = 0; $i < count($cheque); $i++) {
			for ($j = 0; $j < count($dep); $j++)
				if ($num_cia[$i] == $dep[$j]['num_cia'] && $cheque[$i] <= $dep[$j]['importe']) {
					$dep[$j]['importe'] -= $cheque[$i];
					$dep[$j]['cheque'] = $cheque[$i];
					break 1;
				}
		}
	}
	
	// ****************************************************************
	// * FUNCION PARA DISTRIBUIR BILLETES ENTRE LOS DEPOSITOS         *
	// ****************************************************************
	function distribuir(&$dep, $bil, $val, $key) {
		$bil_sob = NULL;	// Billetes que sobran al repartir
		
		// Recorrer depositos y seleccionar los que sean mayores a la denominación de los billetes
		$count = 0;
		for ($i = 0; $i < count($dep); $i++)
			$count += round($dep[$i]['importe'], 2) > round($val, 2) ? 1 : 0;
		
		if ($count == 0)
			return FALSE;
		
		// Calcular el promedio de billetes por deposito
		$pro = (int)ceil($bil / $count);
		//if ($key == "b005") echo "PRIMERA PASADA NO. BILLETES = $bil || PROMEDIO = $pro || SOBRANTE = $bil_sob<br><br>";
		//if ((int)date("d") % 2 == 0) {
			// Distribuir billetes entre los depositos (PRIMER BARRIDO)
			for ($i = 0; $i < count($dep); $i++)
				if (round($dep[$i]['importe'], 2) >= round($val, 2) && $bil > 0) {
					//if ($key == "b005") echo "DEPOSITO = " . round($dep[$i]['importe'], 2) . " || NO. BILLETES 1A. PASADA = $bil || SOBRANTES = $bil_sob<br>";
					// Si el importe del deposito es mayor al promedio de billetes por deposito
					if (round($dep[$i]['importe'], 2) >= round($val * $pro, 2)) {
						$res_bil = $bil - $pro >= 0 ? $pro : $bil;
						$bil -= $res_bil;	// Descontar billetes usados
						$dep[$i]['importe'] -= round($val * $res_bil, 2);	// Descontar del importe
						$dep[$i][$key] = $res_bil;		// Poner cantidad de billetes fueron asignados a este deposito
						//if ($key == "b005") echo "----(>)DEPOSITO RESTO = " . round($dep[$i]['importe'], 2) . " || NO. BILLETES RESTANTES = $bil || RESTO = $res_bil<br>";
						// Si hay billetes sobrantes y el importe sigue siendo mayor a la denominacion del billete,
						// asignar a la fajilla
						if ((int)$bil_sob > 0 && round($dep[$i]['importe'], 2) > round($val, 2)) {
							$num_bil = floor(round($dep[$i]['importe'], 2) / round($val, 2));	// Obtener número de billetes restantes para el depósito
							$res_sob = $bil - $num_bil >= 0 ? $num_bil : $bil;	// Ver cuanto hay que tomar del sobrante
							$bil_sob -= $res_sob;										// Restar a los billetes sobrantes
							$bil -= $res_sob;											// Descontar billetes usados
							$dep[$i]['importe'] -= round($val * $res_sob, 2);						// Descontar del importe
							$dep[$i][$key] += $res_sob;							// Sumar cantidad de billetes que fueron asignados a este deposito
							//if ($key == "b005") echo "----(S) DEPOSITO RESTO = " . round($dep[$i]['importe'], 2) . " || NO. BILLETES RESTANTES = $bil || RESTO = $res_sob || = $bil - $num_bil<br>";
						}
					}
					// El importe del deposito es menor al promedio de billetes por deposito
					else if (round($dep[$i]['importe'], 2) < round($val * $pro, 2) && round($dep[$i]['importe'], 2) > 0) {
						$num_bil = floor(round($dep[$i]['importe'], 2) / round($val, 2));	// Obtener número de billetes usados por el depósito
						$bil_sob += $pro - $num_bil;
						$bil -= $num_bil;
						$dep[$i]['importe'] -= round($val * $num_bil, 2);
						$dep[$i][$key] = $num_bil;
						//if ($key == "b005") echo "----(<)DEPOSITO RESTO = " . round($dep[$i]['importe'], 2) . " || NO. BILLETES RESTANTES = $bil<br>";
					}
				}
				else
					$dep[$i][$key] = NULL;
			
			// Distribuir billetes sobrantes entre los depositos (SEGUNDO BARRIDO)
			//if ($key == "b005") echo "<br>SEGUNDA PASADA NO. BILLETES = $bil || PROMEDIO = $pro || SOBRANTE = $bil_sob<br><br>";
			for ($i = 0; $i < count($dep); $i++)
				if (round($dep[$i]['importe'], 2) >= round($val, 2) && $bil > 0) {
					$num_bil = floor(round($dep[$i]['importe'], 2) / round($val, 2));
					
					if ($num_bil > 0 && $bil > 0 && $num_bil <= $bil) {
						$bil -= $num_bil;
						$dep[$i]['importe'] -= round($val * $num_bil, 2);
						$dep[$i][$key] += $num_bil;
					}
					else if ($num_bil > 0 && $bil > 0 && $num_bil > $bil) {
						$dep[$i]['importe'] -= round($val * $bil, 2);
						$dep[$i][$key] += $bil;
						$bil = 0;
					}
				}
		/*}
		else {
		}*/
	}
	// ***********************************************************************************************
	// * FUNCION PARA DECODIFICAR LOS ARCHIVOS DE COMETRA                                            *
	// ***********************************************************************************************
	function decodifica_archivo(&$datos, $file, $index = 0) {
		// Comprobar características del fichero
		if (!(stristr($file['type'], "text/plain") && $file['size'] < 1048576))
				return FALSE;
		else {
			// Cargar depositos a un arreglo temporal
			$fd = fopen($file['tmp_name'], "rb");
			$count = $index;
			while (!feof($fd)) {
				// Obtener cadena del archivo y almacenarlo en el buffer (MOD. 06/09/2005. CAMBIO EL TAMAÑO DE LA CADENA DE 36 A 46)
				$buffer = fgets($fd, 46);
	
				// Dividir cadena en secciones y almacenarlas en variables
				if ($buffer != "") {
					$temp_cia = (int)substr($buffer, 0, 3);
					// Si la compañía es 140 o 146, cambiar a 147, si es 171, cambiar a 170, de lo contrario sera la compañía obtenida del archivo
					$datos[$count]['num_cia']   = $temp_cia == 140 || $temp_cia == 146 ? 147 : ($temp_cia == 171 ? 170 : $temp_cia);
					$datos[$count]['fecha_mov'] = substr($buffer, 9, 2) . "/" . substr($buffer, 7, 2) . "/" . substr($buffer, 3, 4);
					$temp_cod = number_format(substr($buffer, 11, 2), 0, "", "");
					$datos[$count]['cod_mov']   = ($datos[$count]['num_cia'] > 100 && $datos[$count]['num_cia'] < 200) || ($datos[$count]['num_cia'] > 701 && $datos[$count]['num_cia'] < 750) ? 16 : $temp_cod;
					$datos[$count]['importe']   = (float)(substr($buffer, 13, 18) . "." . substr($buffer, 31, 2));
					$datos[$count]['ficha']     = substr($buffer, 33, 10);
					$count++;
				}
			}
			fclose($fd);
			
			return TRUE;
		}
	}
	
	// Función de comparacion para ordenar los datos
	function cmp($a, $b) {
		// Descomponer fecha
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})", $a['fecha_mov'], $fecha_a);
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})", $b['fecha_mov'], $fecha_b);
		
		// Timestamp para comparacion
		$ts_a = mktime(0, 0, 0, $fecha_a[2], $fecha_a[1], $fecha_a[3]);
		$ts_b = mktime(0, 0, 0, $fecha_b[2], $fecha_b[1], $fecha_b[3]);
		
		// Si las compañías son iguales
		if ($a['num_cia'] == $b['num_cia']) {
			if ($ts_a == $ts_b) {
				if ($a['importe'] == $b['importe'])
					return 0;
				else
					return $a['importe'] < $b['importe'] ? -1 : 1;
			}
			else
				return $ts_a < $ts_b ? -1 : 1;
		}
		else
			return $a['num_cia'] < $b['num_cia'] ? -1 : 1;
	}
	
	$datos = array();
	if (decodifica_archivo($datos, $_FILES['userfile1'])) {
		// Si hay un segundo archivo, decodificarlo y concatenarlo al actual
		if ($_FILES['userfile2']['tmp_name'] != "")
			decodifica_archivo($datos, $_FILES['userfile2'], count($datos));
		
		// Ordenar datos
		usort($datos, "cmp");
		
		// Revisar si existen todas las compañías contenidas en el archivo
		for ($i = 0; $i < count($datos); $i++)
			if (buscar_cia($cias, $datos[$i]['num_cia']) === FALSE) {
				header("location: ./ban_dep_exp.php?codigo_error=2");
				die;
			}
		
		// Revisar y descartar repetidos
		$index = 0;
		$total = 0;
		for ($i = 0; $i < count($datos); $i++) {
			$rep = 0; // Variable contador para registros repetidos
			for ($j = $i; $j < count($datos); $j++)
				if ($datos[$i]['num_cia'] == $datos[$j]['num_cia'] && $datos[$i]['fecha_mov'] == $datos[$j]['fecha_mov'] && $datos[$i]['cod_mov'] == $datos[$j]['cod_mov'] && $datos[$i]['importe'] == $datos[$j]['importe'])
					$rep++;
			if ($rep == 1) {
				$dep[$index]['num_cia']    = $datos[$i]['num_cia'];
				$cia = buscar_cia($cias, $dep[$index]['num_cia']);
				$dep[$index]['nombre_cia'] = $cias[$cia]['nombre'];
				$dep[$index]['cuenta']     = $cias[$cia]['clabe_cuenta'];
				$dep[$index]['fecha_mov']  = $datos[$i]['fecha_mov'];
				$dep[$index]['cod_mov']    = $datos[$i]['cod_mov'];
				$dep[$index]['importe']    = $datos[$i]['importe'];
				$dep[$index]['ficha']      = $datos[$i]['ficha'];
				
				$total += (float)$datos[$i]['importe'];
				
				$index++;
			}
		}
		
		if (round($total - array_sum($_POST['importe']) - $_POST['total'], 2) != 0) {
			$_SESSION['exp']['total_dep'] = round($total - array_sum($_POST['importe']), 2);
			header("location: ./ban_dep_exp_v2.php?codigo_error=3");
			die;
		}
		
		// Copiar tabla de depositos
		$temp = $dep;
		
		cheques($temp, $_POST['num_cia'], $_POST['importe']);
		distribuir($temp, $bil['b1000'], $val['b1000'], 'b1000');
		distribuir($temp, $bil['b500'], $val['b500'], 'b500');
		distribuir($temp, $bil['b200'], $val['b200'], 'b200');
		distribuir($temp, $bil['b100'], $val['b100'], 'b100');
		distribuir($temp, $bil['b50'], $val['b50'], 'b50');
		distribuir($temp, $bil['b20'], $val['b20'], 'b20');
		distribuir($temp, $bil['b10'], $val['b10'], 'b10');
		distribuir($temp, $bil['b5'], $val['b5'], 'b5');
		distribuir($temp, $bil['b2'], $val['b2'], 'b2');
		distribuir($temp, $bil['b1'], $val['b1'], 'b1');
		distribuir($temp, $bil['b050'], $val['b050'], 'b050');
		distribuir($temp, $bil['b020'], $val['b020'], 'b020');
		distribuir($temp, $bil['b010'], $val['b010'], 'b010');
		distribuir($temp, $bil['b005'], $val['b005'], 'b005');
		
		// Anexar desglozado de billetes
		$tot['cheque'] = 0;
		$tot['b1000'] = 0;
		$tot['b500'] = 0;
		$tot['b200'] = 0;
		$tot['b100'] = 0;
		$tot['b50'] = 0;
		$tot['b20'] = 0;
		$tot['b10'] = 0;
		$tot['b5'] = 0;
		$tot['b2'] = 0;
		$tot['b1'] = 0;
		$tot['b050'] = 0;
		$tot['b020'] = 0;
		$tot['b010'] = 0;
		$tot['b005'] = 0;
		for ($i = 0; $i < count($dep); $i++) {
			$dep[$i]['cheque'] = isset($temp[$i]['cheque']) ? $temp[$i]['cheque'] : "";
			$dep[$i]['b1000'] = $temp[$i]['b1000'];
			$dep[$i]['b500'] = $temp[$i]['b500'];
			$dep[$i]['b200'] = $temp[$i]['b200'];
			$dep[$i]['b100'] = $temp[$i]['b100'];
			$dep[$i]['b50'] = $temp[$i]['b50'];
			$dep[$i]['b20'] = $temp[$i]['b20'];
			$dep[$i]['b10'] = $temp[$i]['b10'];
			$dep[$i]['b5'] = $temp[$i]['b5'];
			$dep[$i]['b2'] = $temp[$i]['b2'];
			$dep[$i]['b1'] = $temp[$i]['b1'];
			$dep[$i]['b050'] = $temp[$i]['b050'];
			$dep[$i]['b020'] = $temp[$i]['b020'];
			$dep[$i]['b010'] = $temp[$i]['b010'];
			$dep[$i]['b005'] = $temp[$i]['b005'];
			
			$tot['cheque'] += isset($temp[$i]['cheque']) ? $temp[$i]['cheque'] : "";
			$tot['b1000'] += $temp[$i]['b1000'];
			$tot['b500'] += $temp[$i]['b500'];
			$tot['b200'] += $temp[$i]['b200'];
			$tot['b100'] += $temp[$i]['b100'];
			$tot['b50'] += $temp[$i]['b50'];
			$tot['b20'] += $temp[$i]['b20'];
			$tot['b10'] += $temp[$i]['b10'];
			$tot['b5'] += $temp[$i]['b5'];
			$tot['b2'] += $temp[$i]['b2'];
			$tot['b1'] += $temp[$i]['b1'];
			$tot['b050'] += $temp[$i]['b050'];
			$tot['b020'] += $temp[$i]['b020'];
			$tot['b010'] += $temp[$i]['b010'];
			$tot['b005'] += $temp[$i]['b005'];
		}
		
		
		unset($_SESSION['exp']);
		
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=depositos.csv");
		
		echo '"CIA.","NOMBRE","CUENTA","FECHA","CODIGO","FOLIO","IMPORTE","CHEQUE","BILLETES 1,000","BILLETES 500","BILLETES 200","BILLETES 100","BILLETES 50","BILLETES 20","MONEDAS 10","MONEDAS 5","MONEDAS 2","MONEDAS 1","MONEDAS 0.50","MONEDAS 0.20","MONEDAS 0.10","MONEDAS 0.05",' . "\n";
		for ($i = 0; $i < count($dep); $i++) {
			echo "\"{$dep[$i]['num_cia']}\",";
			echo "\"{$dep[$i]['nombre_cia']}\",";
			echo "\"{$dep[$i]['cuenta']}\",";
			echo "\"{$dep[$i]['fecha_mov']}\",";
			echo "\"{$dep[$i]['cod_mov']}\",";
			echo "\"{$dep[$i]['ficha']}\",";
			echo "\"" . number_format($dep[$i]['importe'], 2, ".", ",") . "\",";
			echo "\"" . ($dep[$i]['cheque'] > 0 ? number_format($dep[$i]['cheque'], 2, ".", ",") : "") . "\",";
			echo "\"" . ($dep[$i]['b1000'] != 0 ? $dep[$i]['b1000'] : "") . "\",";
			echo "\"" . ($dep[$i]['b500'] != 0 ? $dep[$i]['b500'] : "") . "\",";
			echo "\"" . ($dep[$i]['b200'] != 0 ? $dep[$i]['b200'] : "") . "\",";
			echo "\"" . ($dep[$i]['b100'] != 0 ? $dep[$i]['b100'] : "") . "\",";
			echo "\"" . ($dep[$i]['b50'] != 0 ? $dep[$i]['b50'] : "") . "\",";
			echo "\"" . ($dep[$i]['b20'] != 0 ? $dep[$i]['b20'] : "") . "\",";
			echo "\"" . ($dep[$i]['b10'] != 0 ? $dep[$i]['b10'] : "") . "\",";
			echo "\"" . ($dep[$i]['b5'] != 0 ? $dep[$i]['b5'] : "") . "\",";
			echo "\"" . ($dep[$i]['b2'] != 0 ? $dep[$i]['b2'] : "") . "\",";
			echo "\"" . ($dep[$i]['b1'] != 0 ? $dep[$i]['b1'] : "") . "\",";
			echo "\"" . ($dep[$i]['b050'] != 0 ? $dep[$i]['b050'] : "") . "\",";
			echo "\"" . ($dep[$i]['b020'] != 0 ? $dep[$i]['b020'] : "") . "\",";
			echo "\"" . ($dep[$i]['b010'] != 0 ? $dep[$i]['b010'] : "") . "\",";
			echo "\"" . ($dep[$i]['b005'] != 0 ? $dep[$i]['b005'] : "") . "\"\n";
		}
		echo '""' . "\n";
		echo '"","","","","","TOTALES",';
		echo "\"" . number_format($total - array_sum($_POST['importe']), 2, ".", ",") . "\",";
		echo "\"" . ($tot['cheque'] != 0 ? number_format($tot['cheque'], 2, ".", ",") : "") . "\",";
		echo "\"" . ($tot['b1000'] != 0 ? $tot['b1000'] : "") . "\",";
		echo "\"" . ($tot['b500'] != 0 ? $tot['b500'] : "") . "\",";
		echo "\"" . ($tot['b200'] != 0 ? $tot['b200'] : "") . "\",";
		echo "\"" . ($tot['b100'] != 0 ? $tot['b100'] : "") . "\",";
		echo "\"" . ($tot['b50'] != 0 ? $tot['b50'] : "") . "\",";
		echo "\"" . ($tot['b20'] != 0 ? $tot['b20'] : "") . "\",";
		echo "\"" . ($tot['b10'] != 0 ? $tot['b10'] : "") . "\",";
		echo "\"" . ($tot['b5'] != 0 ? $tot['b5'] : "") . "\",";
		echo "\"" . ($tot['b2'] != 0 ? $tot['b2'] : "") . "\",";
		echo "\"" . ($tot['b1'] != 0 ? $tot['b1'] : "") . "\",";
		echo "\"" . ($tot['b050'] != 0 ? $tot['b050'] : "") . "\",";
		echo "\"" . ($tot['b020'] != 0 ? $tot['b020'] : "") . "\",";
		echo "\"" . ($tot['b010'] != 0 ? $tot['b010'] : "") . "\",";
		echo "\"" . ($tot['b005'] != 0 ? $tot['b005'] : "") . "\"\n";
	}
	else
		header("location: ./ban_dep_exp_v2.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dep_exp_v2.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_SESSION['exp'])) {
	$tpl->assign("b1000", $_SESSION['exp']['b1000']);
	$tpl->assign("b500", $_SESSION['exp']['b500']);
	$tpl->assign("b200", $_SESSION['exp']['b200']);
	$tpl->assign("b100", $_SESSION['exp']['b100']);
	$tpl->assign("b50", $_SESSION['exp']['b50']);
	$tpl->assign("b20", $_SESSION['exp']['b20']);
	$tpl->assign("b10", $_SESSION['exp']['b10']);
	$tpl->assign("b5", $_SESSION['exp']['b5']);
	$tpl->assign("b2", $_SESSION['exp']['b2']);
	$tpl->assign("b1", $_SESSION['exp']['b1']);
	$tpl->assign("b050", $_SESSION['exp']['b050']);
	$tpl->assign("b020", $_SESSION['exp']['b020']);
	$tpl->assign("b010", $_SESSION['exp']['b010']);
	$tpl->assign("b005", $_SESSION['exp']['b005']);
	$tpl->assign("total", $_SESSION['exp']['total']);
}

for ($i = 0; $i < 20; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < 20 - 1 ? $i + 1 : 0);
	if (isset($_SESSION['exp'])) {
		$tpl->assign("num_cia", $_SESSION['exp']['num_cia'][$i]);
		$tpl->assign("nombre_cia", $_SESSION['exp']['nombre_cia'][$i]);
		$tpl->assign("importe", $_SESSION['exp']['importe'][$i]);
	}
}

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia NOT IN (999) ORDER BY num_cia";
$result = $db->query($sql);
for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $result[$i]['num_cia']);
	$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']] . ($_GET['codigo_error'] == 3 ? "<font size=\"+1\">" . number_format($_SESSION['exp']['total_dep'], 2, ".", ",") . "</font>" : ""));	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
$db->desconectar();
?>