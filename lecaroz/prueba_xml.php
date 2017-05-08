<?php
include ('includes/class.xml.inc.php');

$xml = new XMLClass('XML/PolizaSantanderMollendoLexmark.xml', 'file');
$xml->parse();

echo '<pre>' . print_r($xml->data, TRUE) . '</pre>';
?>