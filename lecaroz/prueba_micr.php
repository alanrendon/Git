<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>

<?php
echo "pago concepto de sistema:";
?>
<br>
<br>

<?php
// Inicializa modo MICR
echo "<code>&%STHPASSWORD$";
// Inicializa formato de cadena
echo "&%SMD";
echo "00000000;00000000:00000000";
echo "$</code>";
?>

<br>
  <br>
  <br>
  <br>
  <br>
  <?php
echo "<code>&%STP12500$";
echo "&%1B$(12500X$23,124.3&%$</code>";
?>
</p>
</body>
</html>
