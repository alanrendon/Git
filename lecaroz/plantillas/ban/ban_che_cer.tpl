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
<td align="center" valign="middle"><p class="title">Carta de Certificaci&oacute;n</p>
  <form action="./ban_che_cer.php?carta=1" method="post" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Cuenta</th>
    </tr>
    <tr>
      <td class="tabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER</option>
      </select></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Se autoriza a </th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr>
      <td class="tabla"><input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia{i},cuenta{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) fecha{i}.select();
else if (event.keyCode == 37) nombre{i}.select();
else if (event.keyCode == 38) num_cia{back}.select();
else if (event.keyCode == 40) num_cia{next}.select();" value="{num_cia}" size="3" maxlength="3">
        <input name="cuenta{i}" type="hidden" id="cuenta{i}" value="{cuenta}">
        <input name="nombre_cia{i}" type="text" class="vnombre" id="nombre_cia{i}" value="{nombre_cia}" size="30"></td>
      <td class="tabla"><input name="fecha{i}" type="text" class="insert" id="fecha{i}" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) folio{i}.select();
else if (event.keyCode == 37) num_cia{i}.select();
else if (event.keyCode == 38) fecha{back}.select();
else if (event.keyCode == 40) fecha{next}.select();" value="{fecha}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="folio{i}" type="text" class="insert" id="folio{i}" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia{next}.select();
else if (event.keyCode == 37) fecha{i}.select();
else if (event.keyCode == 38) folio{back}.select();
else if (event.keyCode == 40) folio{next}.select();" value="{folio}" size="10" maxlength="10"></td>
      <td class="tabla"><select name="nombre{i}" class="insert" id="nombre{i}">
        <!-- START BLOCK : nombre -->
		  <option value="{nombre}"{selected}>{nombre}</option>
		  <!-- END BLOCK : nombre -->
      </select>
      </td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Generar" onClick="valida_registro(form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_cia(num_cia, nombre,cuenta) {
		cia = new Array();
		cue = new Array();
		cue2 = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		cue[{num_cia}] = '{cuenta}';
		cue2[{num_cia}] = '{cuenta2}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				cuenta.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				tmp = document.form.cuenta.value == 1 ? cue : cue2;
				cuenta.value  = tmp[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			cuenta.value  = "";
			return false;
		}
	}
	
	function cambiaClabe(form) {
		var tmp = form.cuenta.value == 1 ? cue : cue2;
		
		for (i = 0; i < 10; i++) {
			if (form.eval("num_cia" + i).value > 0) {
				form.eval("cuenta" + i).value = tmp[parseInt(form.eval("num_cia" + i).value)];
			}
			else {
				form.eval("cuenta" + i).value = "";
			}
		}
	}
	
	function valida_registro(form) {
		window.open("","carta","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
		form.target = "carta";
		form.submit();
	}
	
	window.onload = document.form.num_cia0.select();
</script>
</body>
</html>
