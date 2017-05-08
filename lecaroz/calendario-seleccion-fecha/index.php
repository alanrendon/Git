<?
include ("calendario/calendario.php");
?>

<html>
<head>
	<title>Utilización del calendario</title>
	<script language="JavaScript" src="calendario/javascripts.js"></script>
</head>

<body>
<h1>Uso de la librería del calendario</h1>
Para seleccionar una fecha que se colocaría en un campo de formulario
<br>
<br>
<br>
<form name="fcalen">
Fecha inicio: 
<?
escribe_formulario_fecha_vacio("fecha1","fcalen");
?>
<br>
<br>
Fecha final:
<?
escribe_formulario_fecha_vacio("fecha2","fcalen");
?>

</form>

</body>
</html>
	