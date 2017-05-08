<?php
// divide la frase mediante cualquier número de comas o caracteres de espacio,
// lo que incluye " ", \r, \t, \n y \f
$claves = preg_split("/\w\d/", "A1967");
print_r($claves);
preg_match_all(rtrim("/([a-zA-Z]{0,})([0-9]{1,})/", 'g'), "A1967", $result, PREG_PATTERN_ORDER);
echo '<pre>' . print_r($result, TRUE) . '</pre>';
?>
