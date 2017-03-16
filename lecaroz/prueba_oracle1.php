<?php
//include 'includes/class.db.inc.php';

//$db = new DBclass('oci8://lecar:lecar2010@192.168.1.70:1521/facturae');
//$db = new DBclass('pgsql://mollendo:pobgnj@192.168.1.250/lecaroz', 'autocommit=yes');

include "adodb5/adodb.inc.php";


$Srv="192.168.1.70";
$tnsName="facturae";
$usuario = "lecar";
$contrasenna = "lecar2010";
$db = NewADOConnection("oci8");
$db->PConnect($Srv,$usuario, $contrasenna,$tnsName);
$rs = $db->Execute("SELECT * FROM FE_SERIES WHERE COMPANIA IN (1, 2, 3)");
print_r($rs);
while ($arr = $rs->FetchRow()) {
print "<pre>";
print_r($rs->GetRows());
print "</pre>";
}


?>
