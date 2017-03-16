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
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Cartas</p>
  <form action="./ban_gen_car.php" method="post" name="form" target="carta">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia1" type="text" class="insert" id="num_cia1" onFocus="temp.value=this.value" onChange="isInt(this)" onKeyDown="if (event.keyCode)" onKeyUp="if (event.keyCode == 13) num_cia2.select()" size="3" maxlength="3">
      a
        <input name="num_cia2" type="text" class="insert" id="num_cia2" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyUp="if (event.keyCode == 13) dirigida_a.select()" size="3" maxlength="3">
        </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Dirigida a </th>
      <td class="vtabla"><input name="dirigida_a" type="text" class="vinsert" id="dirigida_a" onKeyUp="if (event.keyCode == 13 && firma_pan) firma_pan.select(); else if (event.keyCode == 13 && firma_zap) firma_zap.select()" size="50" maxlength="100"></td>
    </tr>
	<!-- START BLOCK : pan -->
    <tr>
      <th class="vtabla" scope="row">Firma Panaderias </th>
      <td class="vtabla"><input name="firma_pan" type="text" class="vinsert" id="firma_pan" onKeyUp="if (event.keyCode == 13) firma_ros.select()" size="50" maxlength="100"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Firma Rosticer&iacute;as </th>
      <td class="vtabla"><input name="firma_ros" type="text" class="vinsert" id="firma_ros" onKeyUp="if (event.keyCode == 13) texto.focus()" size="50" maxlength="100"></td>
    </tr>
	<!-- END BLOCK : pan -->
	<!-- START BLOCK : zap -->
	<tr>
      <th class="vtabla" scope="row">Firma</th>
      <td class="vtabla"><input name="firma_zap" type="text" class="vinsert" id="firma_zap" onKeyUp="if (event.keyCode == 13) texto.focus()" size="50" maxlength="100"></td>
    </tr>
	<!-- END BLOCK : zap -->
  </table>
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Cuerpo de la carta</th>
    </tr>
    <tr>
      <td class="tabla"><input type="button" class="boton" value="Negrita  [N]...[/N]" onClick="bbcode('N')">
        <input type="button" class="boton" value="Cursiva  [C]...[/C]" onClick="bbcode('C')">
        <input type="button" class="boton" value="Subrayado  [S]...[/S]" onClick="bbcode('S')"></td>
    </tr>
    <tr>
      <td class="tabla"><textarea name="texto" cols="150" rows="20" class="insert" id="texto"></textarea></td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Generar Carta" onClick="ventana()">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	form = document.form;
	
	var cia = new Array();
	<!-- START BLOCK : cia -->
	cia[{num_cia}] = "{nombre_cia}";
	<!-- END BLOCK : cia -->
	
	function cambiaCia(num, nombre) {
		if (num.value == "")
			nombre.value = "";
		else if (cia[num.value] != null)
			nombre.value = cia[num.value];
		else {
			alert("La compañía no se encuentra en el catálogo");
			num.value = num.form.temp.value;
			num.select();
		}
	}
	
	function bbcode(bbcode) {
		if ((form.texto.selectionStart || form.texto.selectionStart == "0") && form.texto.selectionStart != form.texto.selectionEnd) {
			var start_selection = form.texto.selectionStart;
			var end_selection = form.texto.selectionEnd;
			
			var start = (form.texto.value).substring(0,start_selection);
			var middle = (form.texto.value).substring(start_selection, end_selection);
			var end = (form.texto.value).substring(end_selection, form.texto.textLength);
			
			if (middle.substring(0, bbcode.length + 2) == "[" + bbcode + "]" && middle.substring(middle.length - bbcode.length - 3, middle.length) == "[/" + bbcode + "]")
				middle = middle.substring(bbcode.length + 2, middle.length - bbcode.length - 3);
			else
				middle = "[" + bbcode + "]" + middle + "[/" + bbcode + "]";
			
			form.texto.value = start + middle + end;
			
			form.texto.focus();
			
			form.texto.selectionStart = end_selection + middle.length;
			form.texto.selectionEnd = start_selection + middle.length;
		}
		else {
			form.texto.value += "[" + bbcode + "][/" + bbcode + "]";
			form.texto.focus();
		}
	}
	
	function ventana() {
		var ventana = window.open("", "carta", "toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=800,height=600");
		form.submit();
		ventana.focus();
	}
	
	window.onload = form.num_cia1.select();
</script>
</body>
</html>
