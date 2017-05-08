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
<td align="center" valign="middle"><p class="title">Cambio de Folios Reservados</p>
  <form action="./ban_che_mfr.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro.select()" size="4" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2">SANTANDER</option>
      </select></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	form.submit();
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cheques -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cambio de Folios Reservados</p>
  <form action="./ban_che_mfr.php" method="post" name="form"><table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="6" class="vtabla" scope="col"><input name="ini{num_cia}" type="hidden" id="ini{num_cia}" value="{ini}">
        <input name="fin{num_cia}" type="hidden" id="fin{num_cia}" value="{fin}">
        {num_cia} - {nombre_cia} </th>
      </tr>
    <tr>
      <th class="tabla">Folio</th>
      <th class="tabla">Fecha</th>
      <th class="tabla">Beneficiario</th>
      <th class="tabla">C&oacute;digo</th>
      <th class="tabla">Importe</th>
      <th class="tabla">Reservados</th>
    </tr>
    <!-- START BLOCK : cheque -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="hidden" id="id" value="{id}">
        {folio}</td>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{a_nombre}</td>
      <td class="vtabla">{codgastos} {descripcion} </td>
      <td class="rtabla">{importe}</td>
      <td class="tabla"><select name="folio[]" class="insert" id="folio" onChange="actualizaFolios({num_cia},ini{num_cia}.value,fin{num_cia}.value)">
        <option value="" selected></option>
		<!-- START BLOCK : folio -->
		<option value="{folio}">{folio}</option>
		<!-- END BLOCK : folio -->
      </select></td>
    </tr>
	<!-- END BLOCK : cheque -->
    <tr>
      <td colspan="6">&nbsp;</td>
    </tr>
	<!-- END BLOCK : cia -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar">
&nbsp;&nbsp;
<input type="button" class="boton" value="Cambiar"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
var form = document.form;
var folio = new Array();
<!-- START BLOCK : array_cia -->
folio[{num_cia}] = new Array();
<!-- START BLOCK : array_folio -->
folio[{num_cia}][{i}] = "{folio}";
<!-- END BLOCK : array_folio -->
<!-- END BLOCK : array_cia -->

function actualizaFolios(num_cia, ini, fin) {
	var cont_usados = 0, cont_disponibles = 0;
	var usados = new Array(), disponibles = new Array();
	
	// Buscar folios usados
	if (form.folio.length == undefined) {
		if (form.folio.value > 0) {
			usados[cont_usados++] = form.folio.value;
		}
	}
	for (i = ini; i <= fin; i++) {
		if (form.folio[i].value > 0) {
			usados[cont_usados++] = form.folio[i].value;
		}
	}
	
	// Construir arreglo con folios disponibles
	var ok;
	if (folio[num_cia] != null) {
		for (i = 0; i < folio[num_cia].length; i++) {
			ok = true;
			if (usados.length != undefined) {
				for (j = 0; j < usados.length; j++) {
					if (folio[num_cia][i] == usados[j]) {
						ok = false;
					}
				}
			}
			if (ok) {
				disponibles[cont_disponibles++] = folio[num_cia][i];
			}
		}
	}alert("usados: " + usados + "\nDisponibles: " + disponibles);//return false;
	
	// Actualizar folios
	if (form.folio.length == undefined) {
		cambiaFolios(form.folio, disponibles);
	}
	else {
		for (i = ini; i <= fin; i++) {
			cambiaFolios(form.folio[i], disponibles);
		}
	}
}

function cambiaFolios(objSelect, folios) {
	var selectedIndex = 0;
	alert(objSelect.options[objSelect.selectedIndex].value);
	if (objSelect.options[objSelect.selectedIndex].value != "") {
		folios.unshift(objSelect.options[objSelect.selectedIndex].value);
		selectedIndex = 1;
	}alert(folios);return false;
	
	objSelect.length = 1 + folios.length;
	
	objSelect.options[0].value = "";
	objSelect.options[0].text = "";
	
	if (folios.length != undefined) {
		for (i = 0; i < folios.length; i++) {
			objSelect.options[i + 1].value = tmp[i];
			objSelect.options[i + 1].text = tmp[i];
		}
	}
	
	objSelect.selectedIndex = selectedIndex;
}
</script>
<!-- END BLOCK : cheques -->
</body>
</html>
