<?php
$file_name = basename($_GET['file']);

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=$file_name");

@readfile($_GET['file']) OR die();
?>