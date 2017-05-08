<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$patron = '^([a-zA-Z]{3,4})[ |\-]?([0-9]{6})[ |\-]?([a-zA-Z0-9]{3})$';

$result = $db->query("SELECT num_proveedor, nombre, rfc FROM catalogo_proveedores WHERE trans = 'TRUE' AND san = 'FALSE' ORDER BY num_proveedor");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body>
<table border="1" bordercolor="#000000">
  <tr>
    <th scope="col">No</th>
    <th scope="col">Proveedor</th>
    <th scope="col">RFC</th>
    <th scope="col">Desglosado</th>
    <th scope="col">Nuevo RFC </th>
    <th scope="col">Status</th>
  </tr>
<?php
foreach ($result as $reg) {
	$status = ereg($patron, trim($reg['rfc']), $tmp);
	$des = $status ? "$tmp[1]|$tmp[2]|$tmp[3]" : '---';
	$new = $status ? strtoupper("$tmp[1]$tmp[2]$tmp[3]") : '---';
	if (!$status) {
?>
  <tr>
    <td><?php=$reg['num_proveedor']?></td>
    <td><?php=$reg['nombre']?></td>
    <td><?php=trim($reg['rfc']) != '' ? trim($reg['rfc']) : '---'?></td>
    <td><?php=$des?></td>
    <td><?php=$new?></td>
    <td><?php=$status ? 'OK' : '&nbsp;'?></td>
  </tr>
<?php
	}
}
?>
</table>
</body>
</html>