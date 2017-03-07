<?php
$url[0]='../';
require_once "../class/cat_cuentas.class.php";
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=ctas_contab.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('nivel', 'codagr', 'descripcion', 'natur', 'afectacion', 'codsat'));

// fetch the data
$cta = new Cuenta();
$rows = $cta->get_cuentas_csv();

foreach($rows as $cta){
    fputcsv($output, $cta);
}

?>