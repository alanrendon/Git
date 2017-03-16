<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = "select num_cia,nombre_corto,sum(efectivo) as efectivo from total_panaderias left join catalogo_companias using(num_cia) where num_cia < 100 and fecha between '2005/01/01' and '2005/08/31' group by num_cia,nombre_corto order by num_cia";

$result = $db->query($sql);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<table border="1">
  <tr>
    <th scope="col">Cia</th>
    <th scope="col">Nombre</th>
    <th scope="col">Efectivo</th>
  </tr>
  <?php
  $total = 0;
  for ($i = 0; $i < count($result); $i++) { ?>
  <tr>
    <td><?php=$result[$i]['num_cia'];?></td>
    <td><?php=$result[$i]['nombre_corto'];?></td>
    <td align="right"><?php=number_format($result[$i]['efectivo'],2,".",",");?></td>
  </tr>
  <?php $total += $result[$i]['efectivo'];
  } ?>
  <tr>
    <th>&nbsp;</th>
    <th>Total</th>
    <th align="right"><?php=number_format($total,2,".",",");?></th>
  </tr>
</table>
</body>
</html>
