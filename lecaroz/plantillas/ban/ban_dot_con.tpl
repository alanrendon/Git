<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Otros Dep&oacute;sitos</p>
<form name="form" method="get">
  <input name="temp" type="hidden" id="temp" />
<table class="tabla">
  <tr>
    <th class="vtabla" scope="col"><input name="tipo" type="radio" value="totales">
      Totales</th>
    <th rowspan="2" class="vtabla" scope="col">Compa&ntilde;&iacute;a
      <input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) form.anio.select();" size="3" maxlength="3"></th>
    <th rowspan="2" class="vtabla" scope="col">Mes 
      <select name="mes" class="insert" id="mes">
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
    <th rowspan="2" class="vtabla" scope="col">A&ntilde;o 
      <input name="anio" type="text" class="insert" id="anio" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) form.num_cia.select();" value="{anio}" size="4" maxlength="4"></th>
  </tr>
  <tr>
    <th class="vtabla"><input name="tipo" type="radio" value="desglozado" checked>
      Desglosado</th>
    </tr>
</table>
<p>
  <input type="button" class="boton" value="Siguiente" onClick="submit()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado_totales -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Otros Dep&oacute;sitos Capturados <br>
      el mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table class="print">
    <tr>
      <th class="print" scope="col">Cia.</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Dep&oacute;sito</th>
      </tr>
    <!-- START BLOCK : grupo_tot -->
	<!-- START BLOCK : fila_tot -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint"><strong class="rtabla">{deposito}</strong></td>
      </tr>
	<!-- END BLOCK : fila_tot -->
	<!-- START BLOCK : total_tot -->
    <tr>
      <th colspan="2" class="rprint">Total</th>
      <th class="rprint_total">{total}</th>
      </tr>
	<!-- END BLOCK : total_tot -->
	<tr>
      <td colspan="3">&nbsp;</td>
      </tr>
	<!-- END BLOCK : grupo_tot -->
	<tr>
	  <th colspan="2" class="rprint">Gran Total </th>
	  <th class="rprint_total">{gran_total}</th>
	</tr>
  </table>
  </td>
</tr>
</table>
<!-- END BLOCK : listado_totales -->
<!-- START BLOCK : listado_desglozado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="rprint_encabezado">Cia.: {num_cia}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Otros Dep&oacute;sitos Capturados <br>
      el mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <form action="./ban_dot_con.php?tipo={tipo}&num_cia={num_cia}&mes={_mes}&anio={anio}" method="post" name="form">
  <table class="print">
    <tr>
      <th width="10%" class="print" scope="col">D&iacute;a</th>
      <th class="print" scope="col">Dep&oacute;sitos</th>
	  <th class="print" scope="col">Concepto</th>
      <th class="print" scope="col">Acreditado</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Fecha de Captura </th>
      <th class="print" scope="col">Remisiones</th>
      <th class="print" scope="col">Mod</th>
      <th class="print" scope="col">Div</th>
      <th class="print" scope="col">Ficha</th>
    </tr>
    <!-- START BLOCK : fila_des -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{dia}</td>
      <td class="rprint" style="color:#CC0000;"{color}>{deposito}</td>
	  <td class="vprint" style="color:#0000CC;">{concepto}</td>
      <td class="vprint">{acre}</td>
      <td class="vprint" style="color:#0066FF; font-weight:bold;">{nombre}</td>
      <td class="print">{fecha_cap}</td>
	  <td class="print">{remisiones}</td>
	  <td class="print"><input name="" type="button" class="boton" onclick="cambiaFecha({id})" value="..." /></td>
	  <td class="print"><input name="" type="button" class="boton" onclick="divDep({id})" value="..." /></td>
	  <td class="print"><input name="ficha[]" type="checkbox" id="ficha" value="{id}"{checked} /></td>
	</tr>
	<!-- END BLOCK : fila_des -->
    <tr>
      <th class="rprint">Total</th>
      <th class="rprint_total">{total}</th>
      <th colspan="8" class="rprint">&nbsp;</th>
      </tr>
  </table>
  <p>
  	<!-- START BLOCK : close -->
    <input type="button" class="boton" value="Cerrar" onClick="cerrar()">
    <!-- END BLOCK : close -->
    <!-- START BLOCK : back -->
    <input type="button" class="boton" value="Regresar" onClick="document.location='./{back}'">
    <!-- END BLOCK : back -->
    <!-- START BLOCK : update -->
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Actualizar" onclick="actualizar()" />
    <!-- END BLOCK : update -->
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaFecha(id) {
	var win = window.open("./ban_dot_mod_date.php?id=" + id, "", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=500,height=300");
	win.focus();
}

function divDep(id) {
	var win = window.open("./ban_dot_div.php?id=" + id, "", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=400");
	win.focus();
}

function actualizar() {
	var cont = 0;
	if (f.ficha.length == undefined)
		cont += f.ficha.checked && get_val(f.ficha) > 0 ? 1 : 0;
	else
		for (var i = 0; i < f.ficha.length; i++)
			cont += f.ficha[i].checked && get_val(f.ficha[i]) > 0 ? 1 : 0;
	
	if (cont == 0) {
		alert('Debe seleccionar al menos un depósito');
		return false;
	}
	
	if (confirm('¿Actualizar fichas?'))
		f.submit();
}

function cerrar() {
	window.opener.document.location.reload();
	self.close();
}
//-->
</script>
<!-- END BLOCK : listado_desglozado -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="application/javascript">
<!--
function cerrar() {
	window.opener.document.location.reload();
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->