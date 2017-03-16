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
<td align="center" valign="middle"><p class="title">Comparativo entre Compa&ntilde;&iacute;as</p>
  <form action="./bal_com_pan.php" method="get" name="form" target="comparativo">
  <input name="temp" type="hidden">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <th class="vtabla" scope="row"><select name="mes" class="insert" id="mes">
        <option value="1" {1}>ENERO</option>
        <option value="2" {2}>FEBRERO</option>
        <option value="3" {3}>MARZO</option>
        <option value="4" {4}>ABRIL</option>
        <option value="5" {5}>MAYO</option>
        <option value="6" {6}>JUNIO</option>
        <option value="7" {7}>JULIO</option>
        <option value="8" {8}>AGOSTO</option>
        <option value="9" {9}>SEPTIEMBRE</option>
        <option value="10" {10}>OCTUBRE</option>
        <option value="11" {11}>NOVIEMBRE</option>
        <option value="12" {12}>DICIEMBRE</option>
      </select></th>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <th class="vtabla" scope="row"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 && tipo[3].checked) num_cia[0].select(); else if (event.keyCode == 13 && tipo[0].checked) produccion.select(); else if (event.keyCode == 13 && tipo[1].checked) ventas.select(); else if (event.keyCode == 13 && tipo[2].checked) ventas_ros.select();" value="{anio}" size="4" maxlength="4"></th>
    </tr>
    <tr>
      <th colspan="4" class="vtabla" scope="row"><input name="tipo" type="radio" value="1" checked onClick="habilita(this.form,true,'produccion')">
        Producci&oacute;n (
        <input name="produccion" type="text" class="rinsert" id="produccion" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="10" maxlength="10">
        )</th>
      </tr>
    <tr>
      <th colspan="4" class="vtabla" scope="row"><input name="tipo" type="radio" value="3" onClick="habilita(this.form,true,'ventas')">
        Ventas Netas Panaderias(
          <input name="ventas" type="text" class="rinsert" id="ventas" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="10" maxlength="10">
          ) </th>
    </tr>
    <tr>
      <th colspan="4" class="vtabla" scope="row"><input name="tipo" type="radio" value="4" onClick="habilita(this.form,true,'ventas_ros')">
        Ventas Netas Rosticerias (
          <input name="ventas_ros" type="text" class="rinsert" id="ventas_ros" onFocus="temp.value=this.value;this.select()" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="10" maxlength="10">
          ) </th>
    </tr>
    <tr>
      <th colspan="4" class="vtabla" scope="row"><input name="tipo" type="radio" value="2" onClick="habilita(this.form,false,'')">
        Seleccionar Compa&ntilde;&iacute;as </th>
      </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th colspan="7" class="tabla" scope="col">Compa&ntilde;&iacute;as</th>
      </tr>
    <tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[1].select()" size="3" maxlength="3"></td>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[2].select()" size="3" maxlength="3"></td>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[3].select()" size="3" maxlength="3"></td>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[4].select()" size="3" maxlength="3"></td>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[5].select()" size="3" maxlength="3"></td>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[6].select()" size="3" maxlength="3"></td>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia[0].select()" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function habilita(form, flag, campo) {
		if (!flag) {
			form.num_cia[0].select();
		}
		else {
			form.eval(campo).select();
		}
	}
	
	function validar(form) {
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else if (form.tipo[3].checked) {
			var ok = false;
			
			for (i = 0; i < form.num_cia.length; i++) {
				if (form.num_cia[i].value > 0) {
					ok = true;
				}
			}
			
			var tmp = form.num_cia[0].value;
			for (i = 0; i < form.num_cia.length; i++) {
				if (form.num_cia[i].value > 0 && form.num_cia[i].value < 100 && tmp > 100) {
					alert("Solo se pueden comparar compañías del mismo tipo");
					form.num_cia[i].select();
					return false;
				}
				else if (form.num_cia[i].value > 0 && form.num_cia[i].value > 100 && tmp < 100) {
					alert("Solo se pueden comparar compañías del mismo tipo");
					form.num_cia[i].select();
					return false;
				}
				if (form.num_cia[i].value > 0) tmp = form.num_cia[i].value;
			}
			
			if (!ok) {
				alert("Debe especificar al menos una compañía");
				form.num_cia[0].select();
				return false;
			}
			else {
				window.open("","comparativo","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=1024,height=768");
				form.submit();
			}
		}
		else if (form.tipo[0].checked && form.produccion.value <= 0) {
			alert("Debe especificar el rango de producción");
			form.produccion.select();
			return false;
		}
		else if (form.tipo[1].checked && form.ventas.value <= 0) {
			alert("Debe especificar el rango de ventas");
			form.ventas.select();
			return false;
		}
		else if (form.tipo[2].checked && form.ventas_ros.value <= 0) {
			alert("Debe especificar el rango de ventas");
			form.ventas_ros.select();
			return false;
		}
		else {
			window.open("","comparativo","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=1024,height=768");
			form.submit();
		}
	}
	
	window.onload = document.form.anio.select();
</script>
</body>
</html>
