<?php
$horarios = array(
array(23, 16, 65, -9, 12, 31, 01, 0),
array(23, 16, 64, -9, 12, 24, 10, 0),
array(23, 16, 64, -8, 12, 20, 04, 1),
array(23, 16, 63, -4, 11, 57, 46, 0),
array(23, 16, 63, 0, 11, 44, 39, 0),
array(22, 15, 65, -9, 9, 50, 56, 0),
array(22, 15, 64, -9, 9, 40, 29, 0),
array(22, 15, 64, -8, 9, 34, 37, 0),
array(22, 15, 63, -4, 9, 02, 16, 0),
array(22, 15, 63, 0, 8, 43, 54, 0)
);

$ts1 = mktime(14, 30, 0, 3, 28, 2009);

$cord = array(NULL, NULL);
echo '<table border="1" style="font-family:Arial, Helvetica, sans-serif;"><tr><th>Origen</th><th>Destino</th><th>Tiempo</th><th>Hora Salida</th><th>Hora Llegada</th><th>Hora Salida(MX)</th><th>Hora Llegada(MX)</th><th>Diferencia</th></tr>';
foreach ($horarios as $reg) {
	if ($cord[0] != $reg[0] || $cord[1] != $reg[1]) {
			if ($cord[0] != NULL)
				echo '<td colspan="8">&nbsp;</td>';
		
		$cord[0] = $reg[0];
		$cord[1] = $reg[1];
		
		$tstmp = $ts1 - $reg[4] * 3600 - $reg[5] * 60 - $reg[6];
	}
	
	$dif = $ts1 - $reg[4] * 3600 - $reg[5] * 60 - $reg[6] - $tstmp;
	
	echo '<tr' . ($reg[7] == 1 ? ' style="font-weight:bold;"' : '') ."><td>($reg[0]|$reg[1])</td><td>($reg[2]|$reg[3])</td>";
	echo "<td>$reg[4]hrs $reg[5]min $reg[6]seg</td>";
	echo '<td>' . date('G:i:s d/m/Y', $ts1 - $reg[4] * 3600 - $reg[5] * 60 - $reg[6]) . '</td>';
	echo '<td>' . date('G:i:s d/m/Y', $ts1) . '</td>';
	echo '<td>' . date('G:i:s d/m/Y', $ts1 - $reg[4] * 3600 - $reg[5] * 60 - $reg[6] - 7200) . '</td>';
	echo '<td>' . date('G:i:s d/m/Y', $ts1 - 7200) . '</td>';
	echo '<td>' . round($dif / 3600) . 'hrs ' . round($dif % 3600 / 60) . 'min ' . round($dif % 3600 % 60) . 'seg' . '</td></tr>';
	
	$tstmp = $ts1 - $reg[4] * 3600 - $reg[5] * 60 - $reg[6];
}
echo '</table>';

?>