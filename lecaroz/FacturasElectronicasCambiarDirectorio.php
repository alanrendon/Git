<?php
include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

echo '<pre>--------------------' . date('d/m/Y H:i:s') . '--------------------';
echo '<br /><br />Obteniendo facturas para cambiar directorio';

$sql = '
	SELECT
		num_cia
	FROM
		facturas_electronicas
	GROUP BY
		num_cia
	ORDER BY
		num_cia
';
$result = $db->query($sql);

echo '<strong>Completo!!!</strong>';

if ($result) {
	$path_pdf = 'facturas/comprobantes_pdf/';
	$path_xml = 'facturas/comprobantes_xml/';
	
	echo '<br /><br /><strong>Creando directorios...</strong><br />';
	
	foreach ($result as $rec) {
		if (@mkdir($path_pdf . $rec['num_cia'])) {
			echo '<br />' . $rec['num_cia'] . ': PDF CREADO';
		}
		else {
			echo '<br />' . $rec['num_cia'] . ': ERROR AL CREAR EL DIRECTORIO PDF O YA HA SIDO CREADO CON ANTERIORIDAD';
		}
		
		if (@mkdir($path_xml . $rec['num_cia'])) {
			echo '<br />' . $rec['num_cia'] . ': XML CREADO';
		}
		else {
			echo '<br />' . $rec['num_cia'] . ': ERROR AL CREAR EL DIRECTORIO XML O YA HA SIDO CREADO CON ANTERIORIDAD';
		}
		
		if (($output = trim(shell_exec('mv ' . $path_pdf . $rec['num_cia'] . '-* ' . $path_pdf . $rec['num_cia'] . '/'))) != '') {
			echo '<br />@@ ERROR AL MOVER LOS ARCHIVOS PDF: ' . $output;
		}
		else {
			echo '<br />@@ ARCHIVOS PDF MOVIDOS';
		}
		
		if (($output = trim(shell_exec('mv ' . $path_xml . $rec['num_cia'] . '-* ' . $path_xml . $rec['num_cia'] . '/'))) != '') {
			echo '<br />@@ ERROR AL MOVER LOS ARCHIVOS XML: ' . $output;
		}
		else {
			echo '<br />@@ ARCHIVOS XML MOVIDOS';
		}
	}
}
else {
	echo '<br /><br /><strong>No hay resultados</strong>';
}
echo '<br /><br />----------------------------FINAL--------------------------</pre>';
?>