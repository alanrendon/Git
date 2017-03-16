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
<td align="center" valign="middle"><p class="title">Consulta de Nombres</p>
  <form action="./ban_nom_con.php" method="post" name="form"><table class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">N&uacute;mero</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}"></td>
      <td class="tabla">{num}</td>
      <td class="vtabla">{nombre}</td>
      <td class="tabla"><input type="button" class="boton" value="Mod" onClick="mod({id})"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>    
<input type="button" class="boton" value="Borrar" onClick="del()">
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function mod(id) {
	var url = "./ban_nom_mod.php?id=" + id;
	var win = window.open(url,"","top=284,left=287,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=450,height=200");
	win.focus();
}

function del() {
	if (!f.id) {
		alert("No hay registros");
		return false;
	}
	
	var cont = 0;
	if (f.id.length == undefined)
		cont += f.id.checked ? 1 : 0;
	else
		for (var i = 0; i < f.id.length; i++)
			cont += f.id[i].checked ? 1 : 0;
	
	if (cont == 0) {
		alert("Debe seleccionar al menos un registro");
		return false;
	}
	else if (confirm("¿Desea borrar los registros seleccionados?"))
		f.submit();
}
//-->
</script>
</body>
</html>
