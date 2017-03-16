<?php

include 'includes/class.db.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		*
	FROM
		catalogo_trabajadores
	WHERE
		ap_paterno = \'CANDELARIO\'
		AND ap_materno = \'CORONA\'
		AND num_cia NOT IN (700)
	ORDER BY
		num_cia
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$manual = array();
	
	foreach ($result as $rec) {
		$pieces = explode(' ', $rec['nombre_completo']);
		
		if (count($pieces) == 2) {
			$sql .= 'UPDATE catalogo_trabajadores SET ap_paterno = \'' . utf8_encode($pieces[0]) . '\', ap_materno = \'\', nombre = \'' . utf8_encode($pieces[1]) . '\' WHERE id = ' . $rec['id'] . ";\n";
		}
		else if (count($pieces) == 3) {
			$sql .= 'UPDATE catalogo_trabajadores SET ap_paterno = \'' . utf8_encode($pieces[0]) . '\', ap_materno = \'' . utf8_encode($pieces[1]) . '\', nombre = \'' . utf8_encode($pieces[2]) . '\' WHERE id = ' . $rec['id'] . ";\n";
		}
		else if (count($pieces) == 4) {
			if (in_array($pieces[0], array('DE', 'DEL', 'SAN'))) {
				$sql .= 'UPDATE catalogo_trabajadores SET ap_paterno = \'' . utf8_encode($pieces[0] . ' ' . $pieces[1]) . '\', ap_materno = \'' . utf8_encode($pieces[2]) . '\', nombre = \'' . utf8_encode($pieces[3]) . '\' WHERE id = ' . $rec['id'] . ";\n";
			}
			else if (in_array($pieces[1], array('DE', 'DEL', 'SAN'))) {
				$sql .= 'UPDATE catalogo_trabajadores SET ap_paterno = \'' . utf8_encode($pieces[0]) . '\', ap_materno = \'' . utf8_encode($pieces[1] . ' ' . $pieces[2]) . '\', nombre = \'' . utf8_encode($pieces[3]) . '\' WHERE id = ' . $rec['id'] . ";\n";
			}
			else {
				$sql .= 'UPDATE catalogo_trabajadores SET ap_paterno = \'' . utf8_encode($pieces[0]) . '\', ap_materno = \'' . utf8_encode($pieces[1]) . '\', nombre = \'' . utf8_encode($pieces[2] . ' ' . $pieces[3]) . '\' WHERE id = ' . $rec['id'] . ";\n";
			}
		}
		//else if (count($pieces) == 5) {
//			if (in_array($pieces[0], array('DE', 'DEL', 'SAN'))) {
//				$sql .= 'UPDATE catalogo_trabajadores SET ap_paterno = \'' . utf8_encode($pieces[0] . ' ' . $pieces[1]) . '\', ap_materno = \'' . utf8_encode($pieces[2]) . '\', nombre = \'' . utf8_encode($pieces[3]) . '\' WHERE id = ' . $rec['id'] . ";\n";
//			}
//			else if (in_array($pieces[1], array('DE', 'DEL', 'SAN'))) {
//				$sql .= 'UPDATE catalogo_trabajadores SET ap_paterno = \'' . utf8_encode($pieces[0]) . '\', ap_materno = \'' . utf8_encode($pieces[1] . ' ' . $pieces[2]) . '\', nombre = \'' . utf8_encode($pieces[3]) . '\' WHERE id = ' . $rec['id'] . ";\n";
//			}
//			else {
//				$sql .= 'UPDATE catalogo_trabajadores SET ap_paterno = \'' . utf8_encode($pieces[0]) . '\', ap_materno = \'' . utf8_encode($pieces[1]) . '\', nombre = \'' . utf8_encode($pieces[2] . ' ' . $pieces[3]) . '\' WHERE id = ' . $rec['id'] . ";\n";
//			}
//		}
		else {
			$manual[] = $rec;
		}
	}
	
	echo '<pre>' . $sql . '</pre><table border="1">';
	
	foreach ($manual as $rec) {
		echo '<tr><td>' . $rec['id'] . '</td><td>' . $rec['num_emp'] . '</td><td>' . utf8_encode($rec['nombre_completo']) . '</td></tr>';
	}
	
	echo '</table>';
}