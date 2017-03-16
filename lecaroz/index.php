<?php
if (isset($_SESSION['iduser'])) {
	header('location: ./main.php');
	die;
}
?>
<html>
<head>
<title>Sistema de Informaci&oacute;n - Oficinas Mollendo</title>
<style type="text/css">
<!--
.style1 {
	color: #000000;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
-->
</style>
<link href="./styles/pages.css" rel="stylesheet" type="text/css">
<link href="./styles/tablas.css" rel="stylesheet" type="text/css">
</head>
<body class="main" onload="window.opener.opener=NaN;window.opener.close();" oncontextmenu="return false;">
<form name="login" method="POST" action="login.php">
<table width="100%" height="100%" align="center" >
	<tr>
		<td align="center" valign="middle">
			<h2 align="center" class="style1">Sistema de Informaci&oacute;n</h2>
			<h2 align="center" class="style1">Oficinas Mollendo</h2>
			<p align="center" class="style1">&nbsp;</p>
<?php 
if (isset($_GET['loginerror'])) {
	echo "<p class='error'>Error de acceso al sistema.<br>Por favor verifique su nombre de usuario y contrase&ntilde;a.</p>";
}
?>
			<table class="tabla" align="center">
				<TR>

            <TH class="vtabla">USUARIO:</TH>
					<TD class="vtabla"><input class="user" type="text" name="username"></TD>
				</TR>
				<TR class="vtabla">
					
            <TH class="vtabla">CONTRASE&Ntilde;A:</TH>
					<TD class="vtabla"><input class="passwd" type="password" name="password"></TD>
				</tr>
				<tr>
					<td height="20"></td><td></td>
				</tr>
				<tr>			
					<TD colspan="2" align="center"><img src="./imagenes/login.gif" alt="Login" align="middle">&nbsp;&nbsp;
						<input class="boton" type="submit" name="submit" value="Login">&nbsp;&nbsp;&nbsp;&nbsp;<img src="menus/delete.gif" align="middle">&nbsp;&nbsp;
						<input class="boton" type="button" onClick="self.close();" value="Cerrar Sistema"></TD>
				</TR>
			</table>
		</td>
	</tr>
</table>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.login.username.select();
</script>

</body>
</html>

