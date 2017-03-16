<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Tipo</th>
    <th class="tabla" scope="col">Fecha</th>
  </tr>
  <tr>
    <th class="tabla">{num_cia} - {nombre_cia} </th>
    <th class="tabla">{tipo}</th>
    <th class="tabla">{fecha}</th>
  </tr>
</table>

  <br>
  <form action="./impresion_doc.php" method="post" name="form" target="_blank">
  	<input name="id_doc" type="hidden" id="id_doc" value="{id_doc}">
  	<table class="tabla">
    <!-- START BLOCK : fila -->
	<tr>
      <!-- START BLOCK : col -->
	  <td class="tabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><a href="./imagen.php?id={id}&width=965" target="_blank"><img src="./imagen.php?id={id}&width=180"><br>
	    Hoja {indice}</a><input name="id_img[]" type="checkbox" id="id_img" value="{id}"></td>
	  <!-- END BLOCK : col -->
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  <p>
    <input type="button" class="boton" value="Cerrar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Imprimir Selección" onClick="imprimir(this.form)">
&nbsp;&nbsp;
<input type="button" class="boton" name="agregar" id="agregar" value="Agregar hoja" onClick="agregar_hoja(this.form)">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function imprimir(form) {
		var count = 0;
		
		if (form.id_img.length == undefined)
			count += form.id_img.checked ? 1 : 0;
		else
			for (i = 0; i < form.id_img.length; i++)
				count += form.id_img[i].checked ? 1 : 0 ;
		
		if (count == 0) {
			alert("Debe seleccionar al menos una imagen");
			return false;
		}
		else
			form.submit();
	}
	
	function agregar_hoja(form) {
		form.action = 'doc_doc_add_page.php';
		form.target = '_self';
		form.method = 'get';
		
		form.submit();
	}
</script>
</body>
</html>
