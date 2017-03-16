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
<!-- START BLOCK : mensaje -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Movimientos de Estados de Cuenta</p>
  <p><font color="#FF0000" size="+1" face="Arial, Helvetica, sans-serif">{mensaje}</font></p>
</td>
</tr>
</table>
<!-- END BLOCK : mensaje -->
<!-- START BLOCK : password -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de Movimientos de Estados de Cuenta</p>
<p><font face="Arial, Helvetica, sans-serif">Escriba</font><font face="Arial, Helvetica, sans-serif"> su contrase&ntilde;a </font></p>
<form action="./ban_esc_mod.php" method="post" name="form"><p>
  <input name="password" type="password" class="vinsert" id="password">
</p>
<p>
  <input type="submit" class="boton" value="Siguiente">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.password.select()</script>
<!-- END BLOCK : password -->
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de Movimientos de Estados de Cuenta</p>
<p><font face="Arial, Helvetica, sans-serif">Criterios de b&uacute;squeda</font></p>
<form action="./ban_esc_mod.php" method="get" name="form">
<input name="buscar" type="hidden">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia()" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) fecha1.select();
else if (event.keyCode == 38) concepto.select();" size="3" maxlength="3">
      <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" size="50"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Cuenta</th>
    <td class="vtabla"><select name="cuenta" class="insert" id="cuenta" onChange="actualizaCod()">
      <option value="">-</option>
      <option value="1">BANORTE</option>
      <option value="2">SANTANDER</option>
    </select></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Fecha <font size="-2">(ddmmaa)</font> </th>
    <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) fecha2.select();
else if (event.keyCode == 40) importe.select();
else if (event.keyCode == 38) num_cia.select();" value="{fecha}" size="10" maxlength="10"> 
    al 
      <input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) importe.select();
else if (event.keyCode == 38) num_cia.select();
else if (event.keyCode == 37) fecha1.select();" size="10" maxlength="10">
      <input name="con" type="radio" value="0" checked>
      <span style="font-size:8pt; ">Todos
      <input name="con" type="radio" value="1">      
      Conciliados
      <input name="con" type="radio" value="2">
      No conciliados      </span></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Tipo de movimiento </th>
    <td class="vtabla"><input name="tipo_mov" type="radio" value="todos" checked>
      Todos&nbsp;&nbsp;
      <input name="tipo_mov" type="radio" value="abonos">
      Abonos&nbsp;&nbsp;
      <input name="tipo_mov" type="radio" value="retiros">
      Retiros</td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Importe</th>
    <td class="vtabla"><input name="importe" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) folio.select();
else if (event.keyCode == 38) fecha1.select();" size="12" maxlength="12"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">C&oacute;digo de Movimiento</th>
    <td class="vtabla"><select name="cod_mov" class="insert" id="cod_mov"></select></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">N&uacute;mero de Documento </th>
    <td class="vtabla"><input name="folio" type="text" class="vinsert" id="folio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) concepto.select();
else if (event.keyCode == 38) importe.select();" size="12" maxlength="12"></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Concepto</th>
    <td class="vtabla"><input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia.select();
else if (event.keyCode == 38) folio.select();" size="50" maxlength="200"></td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Terminar" onClick="document.location = './ban_esc_mod.php?terminar=1'">
&nbsp;&nbsp;  
<input type="button" class="boton" value="Siguiente" onClick="valida_registro()"> 
  </p>
</form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
	
	var cod_mov_1 = new Array();
	var des_mov_1 = new Array();
	var cod_mov_2 = new Array();
	var des_mov_2 = new Array();
	var c = new Array();
	
	<!-- START BLOCK : c -->
	c[{num_cia}] = '{nombre}';
	<!-- END BLOCK : c -->
	<!-- START BLOCK : banorte -->
	cod_mov_1[{i}] = "{cod_mov}";
	des_mov_1[{i}] = "{des}";
	<!-- END BLOCK : banorte -->
	<!-- START BLOCK : santander -->
	cod_mov_2[{i}] = "{cod_mov}";
	des_mov_2[{i}] = "{des}";
	<!-- END BLOCK : santander -->
	
	function cambiaCia() {
		if (form.num_cia.value == '' || form.num_cia.value == '0') {
			form.num_cia.value = '';
			form.nombre.value = '';
		}
		else if (c[get_val(form.num_cia)] != null)
			form.nombre.value = c[get_val(form.num_cia)];
		else {
			alert('La compañía no se encuentra en el catálogo');
			form.num_cia.value = form.temp.value;
			form.num_cia.select();
		}
	}
	
	function actualizaCod() {
		var cuenta = form.cuenta.options[form.cuenta.selectedIndex].value;
		
		if (cuenta != 0) {
			mov = eval("cod_mov_" + cuenta);
			des = eval("des_mov_" + cuenta);
			
			form.cod_mov.length = mov.length != undefined ? mov.length : 1;
			if (mov.length != undefined) {
				form.cod_mov.options[0].value = "";
				form.cod_mov.options[0].text = "-";
				
				for (i = 0; i < mov.length; i++) {
					form.cod_mov.options[i + 1].value = mov[i];
					form.cod_mov.options[i + 1].text = mov[i] + " " + des[i];
				}
			}
			else {
				form.cod_mov.options[0].value = "";
				form.cod_mov.options[0].text = "-";
			}
		}
		else {
			form.cod_mov.length = 1;
			form.cod_mov.options[0].value = "";
			form.cod_mov.options[0].text = "-";
		}
	}
	
	function valida_registro() {
		if (form.num_cia.value <= 0 && form.fecha1.value == "" && form.importe.value <= 0 && form.folio.value <= 0 && form.concepto.value == "") {
			alert("Debe especificar al menos un criterio de búsqueda");
			form.num_cia.select();
			return false;
		}
		else
			form.submit();
	}
	
	function alCargar() {
		actualizaCod();
		form.num_cia.select();
	}
	
	window.onload = alCargar();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de Movimientos de Estados de Cuenta</p>
<p><font face="Arial, Helvetica, sans-serif">Resultados de la b&uacute;squeda</font></p>
<table width="100%" class="tabla">
  <tr>
    <th class="tabla" scope="col">Banco</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">Fecha Conciliaci&oacute;n </th>
    <th class="tabla" scope="col">Abono</th>
    <th class="tabla" scope="col">Cargo</th>
    <th class="tabla" scope="col">Folio</th>
    <th colspan="2" class="tabla" scope="col">C&oacute;digo de Movimiento</th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Acci&oacute;n</th>
  </tr>
  <!-- START BLOCK : cia -->
  <tr>
    <th class="tabla">Cia.: {num_cia}</th>
    <!--<th colspan="3" class="tabla">Cuenta.: {cuenta} </th>-->
    <th colspan="9" class="tabla">{nombre_cia} ({nombre_corto}) </th>
    </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{banco}</td>
    <td class="tabla">{fecha}</td>
    <td class="tabla"><strong>{fecha_con}</strong></td>
    <td class="rtabla"><font color="#0000FF">{deposito}</font></td>
    <td class="rtabla"><font color="#FF0000">{retiro}</font></td>
    <td class="tabla">{folio}</td>
    <td class="tabla">{cod_mov}</td>
    <td class="vtabla">{descripcion}</td>
    <td class="vtabla">{concepto}</td>
    <td class="tabla"><input type="button" class="boton" value="Mod." onClick="modificar({id})" {disabled_mod}>
      <input type="button" class="boton" value="Borrar" onClick="borrar({id})" {disabled_del}></td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <td colspan="10">&nbsp;</td>
    </tr>
	<!-- START BLOCK : cia -->
</table>
<p>
  <input type="button" class="boton" value="Terminar" onClick="document.location = './ban_esc_mod.php?terminar=1'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Buscar" onClick="document.location = './ban_esc_mod.php'">
</p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function modificar(id) {
		window.open("ban_esc_minimod.php?id="+id,"mod_esc","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=500");
	}
	
	function borrar(id) {
		window.open("ban_esc_minidel.php?id="+id,"del_esc","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=200");
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
