<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>ERROR</title>
  <meta name="GENERATOR" content="Quanta Plus">
   <script language="JavaScript"> 
       <!--// evito que se cargue en otro frame 
       if (top.location != self.location)top.location = self.location; 
       //--> 
   </script>
   <style type="text/css">
<!--
.style2 {
	font-weight: bold;
	font-size: 42px;
	font-family: Arial, Helvetica, sans-serif;
	color: #FF0000;
}
.style3 {
	font-weight: bold;
	font-size: 12px;
	font-family: Arial, Helvetica, sans-serif;
	color: #FF0000;
}
.style4 {color: #000000; font-size: 12px;}
-->
  </style>
   <link href="./styles/pages.css" rel="stylesheet" type="text/css">
</head>
<body oncontextmenu="return false" class="main">
 <div align="center" class="style2">
   <p>ERROR</p>
<?php
if (!$_GET['error']) {
	echo "<< Error desconocido >>";
}
else {
	switch ($_GET['error']) {
		case 1: // Término de sesión o usuario no valido
			echo "<p class='style4'>Problemas de acceso al sistema, por favor intente regresando a la pantalla de registro.</p>";
			echo "<p class='style3'>";
			echo "<< Se terminó el tiempo de sesión >></p>";
		break;
		case 2: // El usuario no tiene permisos para acceder a la página
			echo "<p class='style3'>";
			echo "<< No tiene autorización para usar esta pantalla >></p>";
		break;
	}
	if ($_GET['error'] == 1) {
		echo "<p class='style4'>";
		echo  "<input type='button' value='Pantalla de registro' onClick='parent.location=\"index.php\"'>";
		echo "</p>";
	}
}
?>
  </div>
</body>
</html>
