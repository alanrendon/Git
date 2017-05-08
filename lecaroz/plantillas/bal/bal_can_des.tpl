<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cantidades Descontables a la Utilidad del Mes</p>
  <form action="./bal_can_des.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Cantidad</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}">
        {num_cia} {nombre_cia} </td>
      <td class="tabla"><input name="cantidad[]" type="text" class="rinsert" id="cantidad" onFocus="tmp.value=this.value;this.select();" onBlur="validaCampo(this)" onKeyDown="if (event.keyCode == 13) cantidad[{next}].select()" value="{cantidad}" size="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
	<tr>
	  <th class="rtabla">Total</th>
	  <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="10"></th>
	  </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Cerrar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar(this.form)">
</p></form></td>
</tr>
</table>
<script type="text/javascript" language="javascript">
<!--
function validaCampo(campo) {
	if (campo.value == "" || campo.value == "0") {
		campo.value = "";
	}
	else {
		var tmp = new oNumero(parseFloat(form.tmp.value.replace(",", "")));
		
		if (isNaN(parseFloat(campo.value.replace(",", "")))) {
			alert("Solo se permiten n\u00FAmeros");
			campo.value = form.tmp.value == "" || form.tmp.value == "0" ? "" : tmp.formato(2, true);
			return false;
		}
		
		var field = parseFloat(campo.value.replace(",", ""));
		
		tmp = new oNumero(field);
		campo.value = tmp.formato(2, true);
	}
	
	total(campo.form);
}

function total(form) {
	var total = 0, tmp;
	for (i = 0; i < form.cantidad.length; i++) {
		tmp = form.cantidad[i].value.replace(",", "");
		total += !isNaN(parseFloat(tmp)) ? parseFloat(tmp) : 0;
	}
	
	tmp = new oNumero(total);
	form.total.value = tmp.formato(2, true);
}

function validar(form) {
	if (confirm("¿Son correctos los datos?")) {
		form.submit();
	}
	else {
		form.cantidad[0].select();
		return false;
	}
}

window.onload = document.form.cantidad[0].select();
-->
</script>
<!-- END BLOCK : listado -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
window.onload = self.close();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
