<?php
include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

echo '<pre>--------------------' . date('d/m/Y H:i:s') . '--------------------';
echo '<br /><br />Obteniendo facturas para cancelar... ';

$sql = '
	SELECT
		id,
		num_cia,
		consecutivo,
		comprobante_pdf
	FROM
		facturas_electronicas
	WHERE
		num_cia IN (85, 92, 96, 97, 109)
		AND status = 1
		AND consecutivo > 0
		AND tipo = 1
		AND fecha BETWEEN \'2010/12/01\' AND \'2011/07/31\'
	ORDER BY
		num_cia,
		consecutivo
';
$result = $db->query($sql);

echo '<strong>Completo!!!</strong>';

if ($result) {
	echo '<br /><br /><strong>Cancelando...</strong><br />';
	
	$sql = '';
	
	$canceladas = 0;
	$errores = 0;
	foreach ($result as $rec) {
		echo '<br /><strong>' . $rec['num_cia'] . '-' . $rec['consecutivo'] . ': </strong>';
		
		$url = 'http://192.168.1.70/clases/servlet/cancelaFacturaLE?archivo=' . $rec['comprobante_pdf'];
		
		if (!($url_result = file_get_contents($url))) {
			echo '<strong style="color:#C00;">Imposible acceder a la cancelaci&oacute;n de facturas electr&oacute;nicas</strong>';
			
			$errores++;
		}
		else {
			$url_result = explode('|', $url_result);
			
			foreach ($url_result as $i => $value) {
				list($var, $val) = explode('=', trim($value));
				
				${trim($var)} = trim($val);
			}
			
			if ($Resultado != 1) {
				echo '<strong style="color:#C00;">No se pudo cancelar la factura: "' . $Error . '"</strong>';
				
				$errores++;
			}
			else {
				$sql .= '
					UPDATE
						facturas_electronicas
					SET
						status = 0,
						iduser_can = 1,
						tscan = now()
					WHERE
						id = ' . $rec['id'] . '
				' . ";\n";
				
				echo '<strong style="color:#00C;">Cancelada</strong>';
				
				$canceladas++;
			}
		}
	}
	
	if ($sql != '') {
		$db->query($sql);
	}
	
	echo '<br /><br /><strong>Canceladas: ' . number_format($canceladas) . '</strong>';
	echo '<br /><strong>Errores: ' . number_format($errores) . '</strong>';
	echo '<br /><strong>Total: ' . number_format($canceladas + $errores) . '</strong>';
}
else {
	echo '<br /><br /><strong>No hay resultados</strong>';
}
echo '<br /><br />----------------------------FINAL--------------------------</pre>';
?>