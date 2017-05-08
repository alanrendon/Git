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
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->

<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de Otros Dep&oacute;sitos </p>
<form action="" method="post" name="form">
<input name="id" type="hidden" value="{id}">
<!--<input name="anio" type="hidden" value="{anio}">-->
<input name="maxdias" type="hidden" value="{maxdias}">
<input name="temp" type="hidden">
<input name="current_mes" type="hidden" id="current_mes" value="{mes}">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Anio</th>
    <th class="tabla" scope="col">Mes</th>
    <th class="tabla" scope="col">D&iacute;a</th>
    <th class="tabla" scope="col">Importe</th>
    <th class="tabla" scope="col">Acreditado</th>
    <th class="tabla" scope="col">Nombre</th>
    <th class="tabla" scope="col">Concepto</th>
  </tr>
  <tr>
    <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp)) actualiza_compania(this,form.nombre)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.anio.select();
else if (event.keyCode == 37) form.importe.select();" value="{num_cia}" size="3" maxlength="3">
      <input name="nombre" type="text" class="vnombre" id="nombre" value="{nombre_cia}" size="30" readonly="true"></td>
    <td class="tabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) mes.select();
else if (event.keyCode == 37) num_cia.select()" value="{anio}" size="4" maxlength="4" readonly="true"></td>
    <td class="tabla"><input name="mes" type="text" class="insert" id="mes" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) valida_mes(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.dia.select();
else if (event.keyCode == 37) form.anio.select();" value="{mes}" size="2" maxlength="2" readonly="true"></td>
    <td class="tabla"><input name="dia" type="text" class="insert" id="dia" onFocus="form.temp.value=this.value" onChange="if (!(isInt(this,form.temp) && parseInt(this.value) <= parseInt(form.maxdias.value))) this.value = '';" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.importe.select();
else if (event.keyCode == 37) form.mes.select();" value="{dia}" size="2" maxlength="2"></td>
    <td class="tabla"><input name="importe" type="text" class="insert" id="importe" onFocus="form.temp.value=this.value" onChange="isFloat2(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.acre.select();
else if (event.keyCode == 37) form.dia.select();" value="{importe}" size="10" maxlength="10"></td>
    <td class="tabla"><input name="acre" type="text" class="insert" id="acre" onFocus="temp.value=this.value;this.select()" onChange="if (isInt(this,form.temp)) actualiza_compania(this,form.nombre_acre)" onKeyDown="if (event.keyCode == 13) num.select()" value="{acre}" size="3">
      <input name="nombre_acre" type="text" class="vnombre" id="nombre_acre" value="{nombre_acre}" size="20" readonly="true"></td>
    <td class="tabla"><input name="idnombre" type="hidden" id="idnombre" value="{idnombre}">
    <input name="num" type="text" class="insert" id="num" onFocus="temp.value=this.value;this.select()" onChange="if (isInt(this,temp)) cambiaNombre()" onKeyDown="if (event.keyCode == 13) concepto.select()" value="{num}" size="3">
      <input name="nom" type="text" class="vnombre" id="nom" value="{nombre}" size="20" readonly="true"></td>
    <td class="tabla"><input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia.select(); else if (event.keyCode == 37) num.select();" value="{concepto}" size="30" maxlength="100"></td>
  </tr>
</table>

<p>
  <input name="Button" type="button" class="boton" onClick="self.close()" value="Cancelar">
&nbsp;&nbsp;  
<input name="Submit2" type="button" class="boton" value="Modificar" onClick="valida_registro()">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
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
	
	function cambiaNombre() {
		var f = document.form, nombre = new Array();
		<!-- START BLOCK : nombre -->
		nombre[{num}] = new Array();
		nombre[{num}][0] = {id};
		nombre[{num}][1] = '{nombre}';
		<!-- END BLOCK : nombre -->
		
		if (f.num.value == '' || f.num.value == '0') {
			f.idnombre.value = '';
			f.num.value = '';
			f.nom.value = '';
		}
		else if (nombre[get_val(f.num)] != null) {
			f.idnombre.value = nombre[get_val(f.num)][0];
			f.nom.value = nombre[get_val(f.num)][1];
		}
		else {
			alert('El nombre no se encuentra en el catálogo');
			f.num.value = f.temp.value;
		}
	}
	
	function valida_mes(mes_input) {
		var current_mes, mes_min, mes_max, mes, form = mes_input.form;
		current_mes = parseInt(form.current_mes.value);
		mes_min = current_mes > 1 ? current_mes - 1 : 12;
		mes_max = current_mes < 12 ? current_mes + 1 : 1;
		mes = parseInt(mes_input.value);
		
		if (!(mes == mes_min || mes == current_mes || mes == mes_max)) {
			alert("El mes solo puede estar entre " + mes_min + " y " + mes_max);
			form.mes.value = form.temp.value;
			form.mes.select();
			return false;
		}
	}
	
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			document.form.num_cia.select();
			return false;
		}
		else if (document.form.anio.value <= 0) {
			alert("Debe especificar el anio");
			document.form.anio.select();
			return false;
		}
		else if (document.form.mes.value <= 0) {
			alert("Debe especificar el mes");
			document.form.mes.select();
			return false;
		}
		else if (document.form.dia.value <= 0) {
			alert("Debe especificar el día");
			document.form.dia.select();
			return false;
		}
		else if (document.form.importe.value == 0) {
			alert("Debe especificar el importe");
			document.form.importe.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
