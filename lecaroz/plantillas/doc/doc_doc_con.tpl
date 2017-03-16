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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Documentos</p>
  <form action="./doc_doc_con.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de Documento </th>
      <td class="vtabla"><select name="tipo_doc" class="insert" id="tipo_doc">
        <option>Todos</option>
        <!-- START BLOCK : tipo_doc -->
		<option value="{tipo_doc}">{descripcion}</option>
		<!-- END BLOCK : tipo_doc -->
      </select></td>
    </tr>
  </table>  <p>
    <input type="submit" class="boton" value="Siguiente">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload=document.form.num_cia.select();</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Documentos</p>
  <form action="./impresion_doc.php" method="post" name="form" target="_blank"><table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="5" class="tabla" scope="col">{num_cia} - {nombre_cia} </th>
      </tr>
    <tr>
      <th class="tabla"><input type="checkbox" onClick="seleccionar(this,{ini},{fin})"></th>
      <th class="tabla">Fecha</th>
      <th class="tabla">Tipo</th>
      <th class="tabla">Descripci&oacute;n</th>
      <th class="tabla">Acci&oacute;n</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="tabla"><input name="id_doc[]" type="checkbox" id="id_doc" value="{id}"></td>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{tipo}</td>
      <td class="vtabla">{descripcion}</td>
      <td class="vtabla"><input type="button" class="boton" value="Consultar" onClick="consultar({id})">
        <input type="button" class="boton" value="Eliminar" onClick="borrar({id})"></td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <td colspan="5">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : cia -->
	  <!-- START BLOCK : no_result -->
	  <tr>
      <th colspan="5" class="tabla">No hay resultados</th>
    </tr>
	<!-- END BLOCK : no_result -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./doc_doc_con.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Imprimir Seleccionados" onClick="imprimir(form)">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function seleccionar(box, ini, fin) {
		if (box.form.id_doc.length == undefined)
			box.form.id_doc.checked = box.checked ? true : false;
		else
			for (i = ini; i <= fin; i++)
				box.form.id_doc[i].checked = box.checked ? true : false;
	}
	
	function imprimir(form) {
		var count = 0;
		
		if (form.id_doc.length == undefined)
			count += form.id_doc.checked ? 1 : 0;
		else
			for (i = 0; i < form.id_doc.length; i++)
				count += form.id_doc[i].checked ? 1 : 0;
		
		if (count == 0) {
			alert("Debe seleccionar al menos un documento");
			return false;
		}
		else
			form.submit();
	}
	
	function consultar(id) {
		var left = (screen.width - 800) / 2;
		var top = (screen.height - 600) / 2;
		
		window.open("doc_doc_preview.php?id=" + id,"consulta","left=" + Math.round(left) +",top=" + Math.round(top) + "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
	}
	
	function borrar(id) {
		var left = (screen.width - 300) / 2;
		var top = (screen.height - 200) / 2;
		
		window.open("doc_doc_del.php?id=" + id,"borrar","left=" + Math.round(left) +",top=" + Math.round(top) + "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=200");
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
