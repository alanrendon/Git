<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Usuarios de Sistema</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Nombre de Usuario </th>
      <th class="tabla" scope="col">Nombre Completo </th>
      <th class="tabla" scope="col">Acci&oacute;n</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{username}</td>
      <td class="vtabla">{nombre}</td>
      <td class="tabla"><input type="button" class="boton" value="Modificar" onClick="modificar({id})">
        <input type="button" class="boton" value="Borrar" onClick="borrar({id})"></td>
      </tr>
	  <!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Alta de Usuario" onClick="alta()">
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function alta() {
		window.open("./adm_usr_altas.php","insertar","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=600");
	}
	
	function borrar(id) {
		window.open("./adm_usr_del.php?iduser="+id,"borrar","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=200");
	}
	
	function modificar(id) {
		window.open("./adm_usr_mod.php?iduser="+id,"modificar","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=600");
	}
</script>
</body>
</html>
