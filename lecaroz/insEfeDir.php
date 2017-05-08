<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

ereg('([0-9]{1,3})\|([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['opt'], $tmp);
$num_cia = $tmp[1];
$fecha = "$tmp[2]/$tmp[3]/$tmp[4]";

$efectivo = $db->query("SELECT efectivo FROM efectivos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha'");

$sql = "DELETE FROM importe_efectivos WHERE num_cia = $num_cia AND fecha = '$fecha';\n";
$sql .= "INSERT INTO importe_efectivos (num_cia, fecha, importe) VALUES ($num_cia, '$fecha', {$efectivo[0]['efectivo']});\n";
$db->query($sql);

$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");

die("Se inserto efectivo de la compaρνa '$num_cia - {$nombre_cia[0]['nombre_corto']}' con fecha del '$fecha' la cantidad de '" . number_format($efectivo[0]['efectivo'], 2, '.', ',') . "'");
?>