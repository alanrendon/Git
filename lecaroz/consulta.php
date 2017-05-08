<?php
// CAPTURAS

include './includes/class.session.inc.php';
include './includes/class.db2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass();
$session->validar_sesion();

$tabla = $_GET['tabla'];

switch ($tabla) {
	case "mov_expendios":
		$ok = TRUE;
		$nombre_exp;
		$numcols = 5;
		$numrows = 10;
		$cont = 0;
		// Conectandose a la base de datos
		$db = DB::connect($dsn);
		if (DB::isError($db))
			die($db->getMessage());
		// Consultar si existe el número de compañía
		$sql = "SELECT num_cia FROM catalogo_companias WHERE num_cia = ".$_POST['compania'];
		$result = $db->query($sql);
		$cia = $result->fetchRow(DB_FETCHMODE_OBJECT);
		// Si existe compañía...
		if ($cia->num_cia) {
			for ($i=0;$i<$numrows-1;$i++) {
				// Consultar si existe el número de expendio para la compañía
				if ($_POST['campo'.$i*$numcols]) {
					$sql = "SELECT num_expendio,nombre,porciento_ganancia FROM expendios WHERE num_cia = ".$_POST['compania']." AND num_expendio = ".$_POST['campo'.$i*$numcols];
					$result = $db->query($sql);
					$exp = $result->fetchRow(DB_FETCHMODE_OBJECT);
					if ($exp->num_expendio) {
						$nombre_exp[$i] = $exp->nombre; // Almacena el nombre del expendio
						$datos[$cont++] = $_POST['compania']; // Campo num_cia
						$datos[$cont++] = $_POST['campo'.$i*$numcols]; // Campo num_expendio
						$datos[$cont++] = $_POST['campo'.($i*$numcols+1)]; // Campo pan_p_venta
						// 'pan_p_expendio' debe ser igual o menor al porcentaje
						// de ganancia de 'pan_p_venta'
						if ($_POST['campo'.($i*$numcols+2)] <= (($_POST['campo'.($i*$numcols+1)]*(100-$exp->porciento_ganancia))/100))
							$datos[$cont++] = $_POST['campo'.($i*$numcols+2)]; // Campo pan_p_expendio
						else {
							$datos[$cont++] = $_POST['campo'.($i*$numcols+2)]." ERROR";
							echo "<font color='#ff0000'>Expendio no.".$_POST['campo'.$i*$numcols].": 'Pan p/expendio' debe ser menor a 'Pan p/venta'.</font><br>";
							$ok = FALSE;
						}
						$datos[$cont++] = $_POST['campo'.($i*$numcols+3)]; // Campo abono
						// 'devolucion' debe ser menor a 'abono'
						if ($_POST['campo'.($i*$numcols+4)] < $_POST['campo'.($i*$numcols+3)])
							$datos[$cont++] = $_POST['campo'.($i*$numcols+4)]; // Campo devolucion
						else {
							$datos[$cont++] = $_POST['campo'.($i*$numcols+4)]." ERROR";
							echo "<font color='#ff0000'>Expendio no.".$_POST['campo'.$i*$numcols].": 'Devoluci&oacute;n' debe ser menor a 'Abono'.</font><br>";
							$ok = FALSE;
						}

						// Consultar rezago anterior
						$sql = "SELECT rezago FROM $tabla WHERE num_cia = ".$_POST['compania']." AND num_expendio = ".$_POST['campo'.$i*$numcols]." ORDER BY fecha ASC";
						$result = $db->query($sql);
						$rez = $result->fetchRow(DB_FETCHMODE_OBJECT);
						// Si no existe rezago anterior, rezago = 0
						if ($rez->rezago == "" || $rez->rezago == 0) {
							$datos[$cont++] = 0; // Campo rezago = 0
						}
						// Si existe rezago anterior, calcular nuevo rezago con la fórmula:
						// rezago = rezago_anterior+pan_p_expendio-abono-devolucion
						else if ($rez->rezago > 0) {
							$new_rezago = $rez->rezago + $_POST['campo'.($i*$numcols+2)] - $_POST['campo'.($i*$numcols+3)] - $_POST['campo'.($i*$numcols+4)];
							$datos[$cont++] = $new_rezago; // Campo rezago = new_rezago
						}

						$datos[$cont++] = $_POST['fecha']; // Campo fecha
					}
					// Si no existe el número de expendio
					else {
						echo "<p>Expendio no.".$_POST['campo'.$i*$numcols]." no existe para la Compa&ntilde;&iacute;a ".$_POST['compania']."</p>\n";
						echo "<input type='button' value='<<Regresar' onclick='parent.history.back()'>";
						$db->disconnect();
						die();
					}
				}
			}
		}
		else {
			echo "<p>Compa&ntilde;&iacute;a ".$_POST['compania']." no existe en la Base de Datos</p>\n";
			echo "<input type='button' value='<<Regresar' onclick='parent.history.back()'>";
			$db->disconnect();
			die();
		}
		// Desconectandose de la base de datos
		$db->disconnect();

		// Construir tabla de consulta
		echo "<table width='476' border='1'>\n";
		echo "\t<tr align='center'>";
		echo "\t<td width='96' bgcolor='#888888'><strong>Compa&ntilde;ia</strong></td>\n";
		// Mostrar número de compañía
		echo "\t<td width='99'>".$_POST['compania']."</td>\n";
		echo "\t<td width='78' bgcolor='#888888'><strong>Fecha</strong></td>\n";
		// Mostrar fecha
		echo "\t<td width='175'>".$_POST['fecha']."</td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
		echo "<form method='POST' action='insercion.php?tabla=mov_expendios'><br>";
		echo "<table width='70%' border='1'>\n";
		echo "\t\t<tr bgcolor='#888888'>\n";
		echo "\t\t<th>Nombre Expendio</th>\n";
		echo "\t\t<th>Cod. Expendio</th>\n";
		echo "\t\t<th>Pan p/venta</th>\n";
		echo "\t\t<th>Pan p/exp</th>\n";
		echo "\t\t<th>Abono</th>\n";
		echo "\t\t<th>Devoluci&oacute;n</th>\n";
		echo "\t\t<th>Rezago</th>\n";
		echo "\t</tr>\n";
		// Mostrar resultados de comparación
		$numrows = count($datos)/8;
		// Mostrar Tabla
		for ($i=0;$i<$numrows;$i++) {
			echo "<tr>\n";
			echo "<td>".strtoupper($nombre_exp[$i])."</td>";
			echo "<input type='hidden' name='campo".($i*8)."' value=".$datos[$i*8].">\n";
			for ($j=1;$j<7;$j++) {
				echo "<td align='center'><input type='text' size='10' name='campo".($i*8+$j)."' readonly value='".$datos[$i*8+$j]."'></td>\n";
			}
			echo "<input type='hidden' name='campo".($i*8+7)."' value=".$datos[$i*8+7].">\n";
			echo "</tr>\n";
		}
		// Sumar campos
		echo "<tr bgcolor='#CCCCCC'>";
		for ($i=0;$i<$numrows;$i++) {
			$total_pan_venta += $datos[$i*8+2];
			$total_pan_exp += $datos[$i*8+3];
			$total_abono += $datos[$i*8+4];
			$total_devolucion += $datos[$i*8+5];
			$total_rezago += $datos[$i*8+6];
		}
		// Mostrar totales
		echo "<td colspan=2 align='right'><b>Total</b></td><td align='center'><b>$total_pan_venta</b></td><td align='center'><b>$total_pan_exp</b></td><td align='center'><b>$total_abono</b></td><td align='center'><b>$total_devolucion</b></td><td align='center'><b>$total_rezago</b></td></tr>\n";
		echo "</table>\n<br>\n";

		echo "";
		echo "<input type='button' value='<<Regresar' onclick=parent.history.back()>&nbsp;&nbsp;&nbsp;";
		if ($ok)
			echo "<input type='submit' value='Ingresar datos'>\n";
		else
			echo "<input type='submit' value='Ingresar datos' disabled>\n";
		echo "</form>";

	break;

?>