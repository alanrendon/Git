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
<td align="center" valign="middle"><p class="title">Recepci&oacute;n de Llamadas</p>
  <form action="./recepcion_llamadas.php" method="post" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
      <tr>
        <th class="tabla" scope="col">Fecha</th>
        <th class="tabla" scope="col">Hora</th>
      </tr>
      <tr>
        <th class="tabla"><input name="fecha" type="text" class="nombre" id="fecha" value="{fecha}" size="10" maxlength="10" readonly="true"></th>
        <th class="tabla"><input name="horas" type="text" class="nombre" id="horas3" value="{hora}" size="2" maxlength="2" readonly="true">
:
  <input name="minutos" type="text" class="nombre" id="minutos2" value="{min}" size="2" maxlength="2" readonly="true"></th>
      </tr>
    </table>    
    <br>
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Para</th>
      <th class="tabla" scope="col">De</th>
      <th class="tabla" scope="col">Recado</th>
      <th class="tabla" scope="col">Contestada</th>
      <th class="tabla" scope="col">Comentario</th>
    </tr>
    <tr>
      <td class="tabla"><select name="iduser" class="insert" id="iduser">
        <option value="4">REBUELTA</option>
        <option value="20">JULIAN</option>
        <option value="25">CHUS</option>
        <option value="18">SANZ</option>
        <option>BAUTISTA</option>
        <option>ILDEFONSO</option>
        <option>MIRIAM</option>
        <option>OTROS</option>
      </select></td>
      <td class="tabla"><input name="de" type="text" class="vinsert" id="de" onKeyDown="if (event.keyCode == 13) recado.select()" size="30" maxlength="50"></td>
      <td class="tabla"><textarea name="recado" cols="30" class="insert" id="recado" onKeyDown="if (event.keyCode == 13) de.select()"></textarea></td>
      <td class="tabla"><input name="contestada" type="checkbox" id="contestada" onClick="if (this.checked) {
comentario.disabled=false;
comentario.focus();
} else comentario.disabled=true;" value="1">
        Si</td>
      <td class="tabla"><textarea name="comentario" cols="30" rows="" disabled="disabled" class="insert" id="comentario"></textarea></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Capturar" onClick="validar()">
  </p>
  </form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
var form = document.form;

function validar() {
	if (form.de.value.length < 4) {
		alert("Debe escribir de quien es el recado");
		form.de.select();
		return false;
	}
	else if (form.fecha.value.length < 8) {
		alert("Debe especificar la fecha en la que recibio la llamada");
		form.fecha.select();
		return false;
	}
	else if (isNaN(parseInt(form.horas.value)) || parseInt(form.horas.value) < 0 || parseInt(form.horas.value) > 23) {
		alert("Las horas deben de estar entre 0 y 23 (horario militar)");
		form.horas.select();
		return false;
	}
	else if (isNaN(parseInt(form.minutos.value)) || parseInt(form.minutos.value) < 0 || parseInt(form.minutos.value) > 59) {
		alert("Los minutos deben de estar entre 0 y 59");
		form.minutos.select();
		return false;
	}
	else if (form.recado.value.length < 4) {
		alert("Debe escribir el recado recibido");
		form.recado.select();
		return false;
	}
	else if (confirm("¿Son correctos los datos?")) {
		form.submit();
	}
	else {
		form.de.select();
		return false;
	}
}

window.onload = form.de.select();
</script>
</body>
</html>
