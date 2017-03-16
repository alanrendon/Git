<?php

preg_match_all(rtrim($_POST['pattern'], 'g'), $_POST['string'], $result, PREG_PATTERN_ORDER);

echo '<pre>' . print_r($result, TRUE) . '</pre>';

?>