<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/status.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2">
	function actualiza_frames(menu) {
		parent.topFrame.location = "./menu.php?menu="+menu;
		parent.mainFrame.location = "./blank.php";
		return;
	}
	function salir() {
		if (confirm("¿Desea salir del sistema?")) {
			parent.location="./logout.php";
		}
	}
</script>
</head>

<body bgcolor="#73A8B7" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0" oncontextmenu="return false;">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" id="status">
	<tr> 
		<td colspan="2" align="center">
		<!-- START BLOCK : menus -->
			|&nbsp;&nbsp;<a class="link" href="javascript:actualiza_frames('{menupath}');">{descripcion}</a>&nbsp;
		<!-- END BLOCK : menus-->
			|
		</td>
	</tr>
	<form name="clock">
	<tr valign="bottom">
		<td align="left"><p class="status"><img src="./menus/bullet1.gif"> Usuario: "{user}" &#8212; Fecha y Hora de acceso: {fecha}, {hora}</p></td>
		<td align="right"><!--<input name="" type="button" class="boton" id="imprimir" onClick="parent.mainFrame.print()" value="Imprimir">-->		  &nbsp;&nbsp;
	  <input class='status' type="button" name="logout" value="Salir del sistema" onclick="salir();">	  </td>
	</tr>
	</form>
</table>
</body>
</html>
