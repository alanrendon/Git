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
<td align="center" valign="middle"><p class="title">Exportar Archivo de Dep&oacute;sitos a Excel </p>
  <form name="form" enctype="multipart/form-data" method="post" action="./ban_dep_exp_v2.php">
  <input name="temp" type="hidden">
<table class="tabla">
<tr>
 <input name="MAX_FILE_SIZE" type="hidden" value="1048576">
 <th class="vtabla">Archivo de dep&oacute;sitos 1 </th>
 <td class="vtabla"><input name="userfile1" type="file" class="vinsert" id="userfile1" size="40" readonly="true"></td>
</tr>
<tr>
  <th class="vtabla">Archivo de dep&oacute;sitos 2 </th>
  <td class="vtabla"><input name="userfile2" type="file" class="vinsert" id="userfile2" size="40" readonly="true"></td>
</tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th colspan="2" class="tabla" scope="row">Billetes</th>
    </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">1000</th>
    <td class="rtabla"><input name="b1000" type="text" class="rinsert" id="b1000" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b500.select()" value="{b1000}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">500</th>
    <td class="rtabla"><input name="b500" type="text" class="rinsert" id="b500" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b200.select()" value="{b500}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">200</th>
    <td class="rtabla"><input name="b200" type="text" class="rinsert" id="b200" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b100.select()" value="{b200}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">100</th>
    <td class="rtabla"><input name="b100" type="text" class="rinsert" id="b100" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b50.select()" value="{b100}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">50</th>
    <td class="rtabla"><input name="b50" type="text" class="rinsert" id="b50" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b20.select()" value="{b50}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">20</th>
    <td class="rtabla"><input name="b20" type="text" class="rinsert" id="b20" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b10.select()" value="{b20}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">10</th>
    <td class="rtabla"><input name="b10" type="text" class="rinsert" id="b10" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b5.select()" value="{b10}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">5</th>
    <td class="rtabla"><input name="b5" type="text" class="rinsert" id="b5" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b2.select()" value="{b5}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">2</th>
    <td class="rtabla"><input name="b2" type="text" class="rinsert" id="b2" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b1.select()" value="{b2}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">1</th>
    <td class="rtabla"><input name="b1" type="text" class="rinsert" id="b1" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b050.select()" value="{b1}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">0.50</th>
    <td class="rtabla"><input name="b050" type="text" class="rinsert" id="b050" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b020.select()" value="{b050}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">0.20</th>
    <td class="rtabla"><input name="b020" type="text" class="rinsert" id="b020" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b010.select()" value="{b020}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">0.10</th>
    <td class="rtabla"><input name="b010" type="text" class="rinsert" id="b010" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b005.select()" value="{b010}" size="10" maxlength="10"></td>
  </tr>
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row">0.05</th>
    <td class="rtabla"><input name="b005" type="text" class="rinsert" id="b005" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) suma()" onKeyDown="if (event.keyCode == 13) b1000.select()" value="{b005}" size="10" maxlength="10"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Total</th>
    <th class="rtabla"><input name="total" type="text" class="rtotal" id="total" value="{total}" size="12" maxlength="12"></th>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Cheque</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia(this,nombre_cia[{i}])" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" value="{num_cia}" size="3" maxlength="3">
      <input name="nombre_cia[]" type="text" readonly="true" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="20" maxlength="20"></td>
	<td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" value="{importe}" size="10" maxlength="10"></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<p>
	<input type="button" class="boton" value="Exportar" onClick="validar()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
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
	
	function suma() {
		var _b1000 = !isNaN(parseInt(form.b1000.value)) ? parseInt(form.b1000.value) : 0;
		var _b500 = !isNaN(parseInt(form.b500.value)) ? parseInt(form.b500.value) : 0;
		var _b200 = !isNaN(parseInt(form.b200.value)) ? parseInt(form.b200.value) : 0;
		var _b100 = !isNaN(parseInt(form.b100.value)) ? parseInt(form.b100.value) : 0;
		var _b50 = !isNaN(parseInt(form.b50.value)) ? parseInt(form.b50.value) : 0;
		var _b20 = !isNaN(parseInt(form.b20.value)) ? parseInt(form.b20.value) : 0;
		var _b10 = !isNaN(parseInt(form.b10.value)) ? parseInt(form.b10.value) : 0;
		var _b5 = !isNaN(parseInt(form.b5.value)) ? parseInt(form.b5.value) : 0;
		var _b2 = !isNaN(parseInt(form.b2.value)) ? parseInt(form.b2.value) : 0;
		var _b1 = !isNaN(parseInt(form.b1.value)) ? parseInt(form.b1.value) : 0;
		var _b050 = !isNaN(parseInt(form.b050.value)) ? parseInt(form.b050.value) : 0;
		var _b020 = !isNaN(parseInt(form.b020.value)) ? parseInt(form.b020.value) : 0;
		var _b010 = !isNaN(parseInt(form.b010.value)) ? parseInt(form.b010.value) : 0;
		var _b005 = !isNaN(parseInt(form.b005.value)) ? parseInt(form.b005.value) : 0;
		
		var _total;
		
		_total = _b1000 * 1000 + _b500 * 500 + _b200 * 200 + _b100 * 100 + _b50 * 50 + _b20 * 20 + _b10 * 10 + _b5 * 5 + _b2 * 2 + _b1 * 1 + _b050 * 0.50 + _b020 * 0.20 + _b010 * 0.10 + _b005 * 0.05;
		form.total.value = _total.toFixed(2);
	}
	
	function validar() {
		if (form.userfile1.value == "") {
			alert("Debe especificar el archivo a exportar");
			form.userfile.focus();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = form.userfile.focus();
</script>
</body>
</html>
