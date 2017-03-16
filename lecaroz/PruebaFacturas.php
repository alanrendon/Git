<?php
include_once('includes/class.db.inc.php');
include_once('includes/class.session2.inc.php');
include_once('includes/dbstatus.php');
include_once('includes/class.facturas.inc.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$fac = FacturasClass::factory(1);



?>