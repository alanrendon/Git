<?php
include 'includes/class.db.inc.php';

$db = new DBclass('oci8://lecar:lecar2010@facelectronica.lecaroz.com:1521/facturae');
//$db = new DBclass('pgsql://mollendo:pobgnj@192.168.1.250/lecaroz', 'autocommit=yes');
//
//include "phppgadmin/libraries/adodb/adodb.inc.php";
//
//
//$Srv="192.168.1.70";
//$tnsName="facturae";
//$usuario = "lecar";
//$contrasenna = "lecar2010";
//$db = NewADOConnection("oci8");
//$db->Connect($Srv,$tnsName, $usuario, $contrasenna);


?>
