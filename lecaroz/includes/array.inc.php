<?php
// Funciones complementarias para el manejo de Arrays


function array_key_exists_recursive($key, $search) {
	if (!(is_array($search) || is_object($search))) {
		trigger_error('array_key_exists_recursive() Se esperaba un Array u Objeto en el segundo parametro', E_USER_WARNING);
		return FALSE;
	}
	
	if (array_key_exists($key, $search))
		return TRUE;
	
	foreach ($search as $rec)
		if (is_array($rec) || is_object($rec))
			if (array_key_exists_recursive($key, $rec))
				return TRUE;
	
	return FALSE;
}

?>