<?php
include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

echo '<pre>--------------------' . date('d/m/Y H:i:s') . '--------------------';
echo '<br /><br />Obteniendo facturas para cambiar nombre';

$sql = '
	SELECT
		*
	FROM
		(
			SELECT
				num_cia,
				(
					SELECT
						serie
					FROM
						facturas_electronicas_series
					WHERE
							num_cia = fe.num_cia
						AND
							tipo_serie = fe.tipo_serie
						AND
							fe.consecutivo BETWEEN folio_inicial AND folio_final
				)
					AS
						serie,
				consecutivo
			FROM
				facturas_electronicas fe
			ORDER BY
				num_cia,
				consecutivo
		)
			result
	WHERE
		serie <> \'\'
';
$result = $db->query($sql);

echo '<strong>Completo!!!</strong>';

if ($result) {
	$path_pdf = 'facturas/comprobantes_pdf/';
	$path_xml = 'facturas/comprobantes_xml/';
	
	echo '<br /><br /><strong>Cambiando nombres...</strong><br />';
	
	foreach ($result as $rec) {
		$old_file_name = $rec['num_cia'] . '-' . $rec['consecutivo'];
		$new_file_name = $rec['num_cia'] . '-' . $rec['serie'] . $rec['consecutivo'];
		
		if (!rename($path_pdf . $rec['num_cia'] . '/' . $old_file_name . '.pdf', $path_pdf . $rec['num_cia'] . '/' . $new_file_name . '.pdf')) {
			echo '<li style="color:#F00;">No se pudo renombrar el archivo ' . $old_file_name . '.pdf';
		}
		else {
			echo '<li style="color:#00F;">' . $old_file_name . '.pdf renombrado a ' . $new_file_name . '.pdf';
		}
		
		if (!rename($path_xml . $rec['num_cia'] . '/' . $old_file_name . '.xml', $path_xml . $rec['num_cia'] . '/' . $new_file_name . '.xml')) {
			echo '<li style="color:#F00;">No se pudo renombrar el archivo ' . $old_file_name . '.xml';
		}
		else {
			echo '<li style="color:#00F;">' . $old_file_name . '.xml renombrado a ' . $new_file_name . '.xml';
		}
	}
}
else {
	echo '<br /><br /><strong>No hay resultados</strong>';
}
echo '<br /><br />----------------------------FINAL--------------------------</pre>';
?>