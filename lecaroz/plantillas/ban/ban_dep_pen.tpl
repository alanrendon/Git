<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Dep&oacute;sitos Pendientes de Palomear</p>
  <form action="./ban_dep_pen.php" method="get"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER</option>
      </select></td>
    </tr>
  </table>  <p>
    <input type="submit" class="boton" value="Siguiente">
  </p></form></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : pendientes -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Dep&oacute;sitos Pendientes de Palomear</p>
  <form action="./ban_dep_pen.php" method="post" name="form">
  <input name="temp" type="hidden">
  <input name="cuenta" type="hidden" id="cuenta" value="{cuenta}">  
  <table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="2" class="tabla" scope="col">Cia.: {num_cia}</th>
      <th class="tabla" scope="col">Cuenta: {cuenta} </th>
      <th colspan="2" class="tabla" scope="col">{nombre_cia} ({nombre_corto}) </th>
      </tr>
	<tr>
      <th class="tabla" scope="col"><input type="checkbox" onClick="checkBlock(this,{ini},{fin})"></th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Concepto</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}" onClick="habilitar({i})"></td>
      <td class="tabla"><input name="fecha_con[]" type="hidden" id="fecha_con" value="{fecha_con}" disabled="true">
      <input name="fecha[]" type="text" disabled="true" class="insert" id="fecha" onFocus="temp.value=this.value" onChange="actualiza_fecha2(this,temp)" onKeyDown="if (event.keyCode == 13) fecha{next}.select()" value="{fecha}" size="10" maxlength="10"></td>
      <td class="rtabla"><strong>{fimporte}</strong><input name="num_cia[]" id="num_cia" type="hidden" value="{num_cia}" disabled="true"><input name="importe[]" id="importe" type="hidden" value="{importe}" disabled="true"></td>
      <td class="tabla"><select name="cod_mov[]" disabled="true" class="insert" id="cod_mov">
        <!-- START BLOCK : cod_mov -->
		<option value="{cod_mov}">{cod_mov} - {descripcion}</option>
		<!-- END BLOCK : cod_mov -->
      </select></td>
      <td class="vtabla"><input name="concepto_ant[]" type="hidden" id="concepto_ant" value="{concepto}">      	<input name="concepto[]" type="text" disabled="true" class="vinsert" id="concepto" onFocus="temp.value=this.value" onChange="if (this.value.length == 0) this.value=temp.value" onKeyDown="if (event.keyCode == 13) fecha{next}.select()" value="{concepto}" size="30" maxlength="30"></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="rtabla">Total</th>
      <th class="rtabla">{total}</th>
      <th colspan="2" class="tabla">&nbsp;</th>
      </tr>
    <tr>
      <td colspan="5">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : cia -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_dep_pen.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="modificar()">
</p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
	
	function habilitar(i) {
		if (form.id.length == undefined) {
			form.fecha.disabled = form.id.checked == true ? false : true;
			form.fecha_con.disabled = form.id.checked == true ? false : true;
			form.cod_mov.disabled = form.id.checked == true ? false : true;
			form.concepto.disabled = form.id.checked == true ? false : true;
			form.num_cia.disabled = form.id.checked == true ? false : true;
			form.importe.disabled = form.id.checked == true ? false : true;
			form.concepto.disabled = form.id.checked == true ? false : true;
			
			form.fecha.select();
		}
		else {
			form.fecha[i].disabled = form.id[i].checked == true ? false : true;
			form.fecha_con[i].disabled = form.id[i].checked == true ? false : true;
			form.cod_mov[i].disabled = form.id[i].checked == true ? false : true;
			form.concepto[i].disabled = form.id[i].checked == true ? false : true;
			form.num_cia[i].disabled = form.id[i].checked == true ? false : true;
			form.importe[i].disabled = form.id[i].checked == true ? false : true;
			form.concepto[i].disabled = form.id[i].checked == true ? false : true;
			
			form.fecha[i].select();
		}
	}
	
	function checkBlock(checkblock, ini, fin) {
		if (form.id.length == undefined) {
			form.id.checked = checkblock.checked == true ? true : false;
			
			form.fecha.disabled = checkblock.checked == true ? false : true;
			form.fecha_con.disabled = checkblock.checked == true ? false : true;
			form.cod_mov.disabled = checkblock.checked == true ? false : true;
			form.concepto.disabled = checkblock.checked == true ? false : true;
			form.num_cia.disabled = checkblock.checked == true ? false : true;
			form.importe.disabled = checkblock.checked == true ? false : true;
			form.concepto.disabled = checkblock.checked == true ? false : true;
			
			form.fecha.select();
		}
		else {
			for (i = ini; i <= fin; i++) {
				form.id[i].checked = checkblock.checked == true ? true : false;
				
				form.fecha[i].disabled = checkblock.checked == true ? false : true;
				form.fecha_con[i].disabled = checkblock.checked == true ? false : true;
				form.cod_mov[i].disabled = checkblock.checked == true ? false : true;
				form.concepto[i].disabled = checkblock.checked == true ? false : true;
				form.num_cia[i].disabled = checkblock.checked == true ? false : true;
				form.importe[i].disabled = checkblock.checked == true ? false : true;
				form.concepto[i].disabled = checkblock.checked == true ? false : true;
			}
			
			form.fecha[ini].select();
		}
	}
	
	function modificar() {
		var count = 0;
		if (form.id.length == undefined) {
			count += form.id.checked ? 1 : 0;
		}
		else {
			for (i = 0; i < form.id.length; i++) {
				count += form.id[i].checked ? 1 : 0;
			}
		}
		
		if (count == 0) {
			alert("Debe serleccionar al menos un depósito");
			return false;
		}
		else if (confirm("¿Desea modificar los depósitos seleccionados y conciliarlos?")) {
			form.submit();
		}
		else
			return false;
	}
	
	window.onload = form.id.length == undefined ? form.fecha.select() : form.fecha[0].select();
</script>
<!-- END BLOCK : pendientes -->
<!-- START BLOCK : no_result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">No hay dep&oacute;sitos pendientes</p>
  </td>
</tr>
</table>
<!-- END  BLOCK : no_result -->
</body>
</html>
