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
<td align="center" valign="middle"><p class="title">Carta de Cancelaci&oacute;n de Cheques Extraviados </p>
  <form action="./ban_car_can.php" method="post" name="form" target="carta" onKeyDown="if (event.keyCode == 13) return false">
  <input name="carta" value="1" type="hidden">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Firma</th>
      <td class="vtabla"><select name="firma" class="insert" id="firma">
        <!-- START BLOCK : pan -->
		<option value="JULIAN LARRACHEA ECHENIQUE" selected>JULIAN LARRACHEA ECHENIQUE</option>
        <option value="ILDEFONSO LARRACHEA ECHENIQUE">ILDEFONSO LARRACHEA ECHENIQUE</option>
        <option value="BAUTISTA GOÑI ARAMBURU">BAUTISTA GO&Ntilde;I ARAMBURU</option>
        <option value="JUAN MANUEL GOÑI LARRACHEA">JUAN MANUEL GO&Ntilde;I LARRACHEA</option>
		<!-- END BLOCK : pan -->
		<!-- START BLOCK : zap -->
		<option value="RAMON IRIGOYEN LERCHUNDI" selected>RAMON IRIGOYEN LERCHUNDI</option>
		<!-- END BLOCK : zap -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER SERFIN</option>
      </select></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Folio</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia[{i}])" onKeyDown="if (event.keyCode == 13) folio[{i}].select()" value="{num_cia}" size="3" maxlength="3">
        <input name="nombre_cia[]" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="30"></td>
      <td class="tabla"><input name="folio[]" type="text" class="insert" id="folio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" value="{folio}" size="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="popup(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_cia(num_cia, nombre) {
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function popup(form) {
		window.open("","carta","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
		form.submit();
	}
	
	window.onload = document.form.num_cia[0].select();
</script>
</body>
</html>
