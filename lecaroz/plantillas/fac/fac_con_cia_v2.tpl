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
<td align="center" valign="middle"><p class="title">Consumos Anuales por Compa&ntilde;&iacute;a </p>
  <form action="./fac_con_cia_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[1].select()" size="4">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[2].select()" size="4">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[3].select()" size="4">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[4].select()" size="4">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[0].select()" size="4"></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
		<!-- START BLOCK : admin -->
        <option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select>      </td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Producto(s)</th>
      <td class="vtabla"><input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[1].select()" size="4">
	  	<input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[2].select()" size="4">
		<input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[3].select()" size="4">
		<input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[4].select()" size="4">
		<input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[5].select()" size="4">
		<input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[6].select()" size="4">
		<input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[7].select()" size="4">
		<input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[8].select()" size="4">
		<input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp[9].select()" size="4">
		<input name="codmp[]" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[0].select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
    	<th class="vtabla" scope="row">Mes(es)</th>
    	<td class="vtabla"><select name="mes[]" class="insert" id="mes">
    		<option value="" selected></option>
    		<option value="1">ENERO</option>
    		<option value="2">FEBRERO</option>
    		<option value="3">MARZO</option>
    		<option value="4">ABRIL</option>
    		<option value="5">MAYO</option>
    		<option value="6">JUNIO</option>
    		<option value="7">JULIO</option>
    		<option value="8">AGOSTO</option>
    		<option value="9">SEPTIEMBRE</option>
    		<option value="10">OCTUBRE</option>
    		<option value="11">NOVIEMBRE</option>
    		<option value="12">DICIEMBRE</option>
    		</select>
    		<br>
			<select name="mes[]" class="insert" id="mes">
    		<option value="" selected></option>
    		<option value="1">ENERO</option>
    		<option value="2">FEBRERO</option>
    		<option value="3">MARZO</option>
    		<option value="4">ABRIL</option>
    		<option value="5">MAYO</option>
    		<option value="6">JUNIO</option>
    		<option value="7">JULIO</option>
    		<option value="8">AGOSTO</option>
    		<option value="9">SEPTIEMBRE</option>
    		<option value="10">OCTUBRE</option>
    		<option value="11">NOVIEMBRE</option>
    		<option value="12">DICIEMBRE</option>
    		</select>
    		<br>
			<select name="mes[]" class="insert" id="mes">
    		<option value="" selected></option>
    		<option value="1">ENERO</option>
    		<option value="2">FEBRERO</option>
    		<option value="3">MARZO</option>
    		<option value="4">ABRIL</option>
    		<option value="5">MAYO</option>
    		<option value="6">JUNIO</option>
    		<option value="7">JULIO</option>
    		<option value="8">AGOSTO</option>
    		<option value="9">SEPTIEMBRE</option>
    		<option value="10">OCTUBRE</option>
    		<option value="11">NOVIEMBRE</option>
    		<option value="12">DICIEMBRE</option>
    		</select>
    		<br>
			<select name="mes[]" class="insert" id="mes">
    		<option value="" selected></option>
    		<option value="1">ENERO</option>
    		<option value="2">FEBRERO</option>
    		<option value="3">MARZO</option>
    		<option value="4">ABRIL</option>
    		<option value="5">MAYO</option>
    		<option value="6">JUNIO</option>
    		<option value="7">JULIO</option>
    		<option value="8">AGOSTO</option>
    		<option value="9">SEPTIEMBRE</option>
    		<option value="10">OCTUBRE</option>
    		<option value="11">NOVIEMBRE</option>
    		<option value="12">DICIEMBRE</option>
    		</select></td>
    	</tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked>
        Listado
          <input name="tipo" type="radio" value="2">
          Archivo</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Opciones</th>
      <td class="vtabla"><input name="opc" type="radio" value="1" checked>
        Mes terminado<br>
        <input name="opc" type="radio" value="2">
        Existencia al d&iacute;a </td>
    </tr>
  </table>  
    <p style="font-weight:bold; font-family:Arial, Helvetica, sans-serif;">NOTA: Para impresi&oacute;n configurar orientaci&oacute;n de la hoja en 'Horizontal o Apaisado' </p>
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	/*if (form.num_cia.value <= 0) {
		alert("Debe especificar la compa��a");
		form.codmp.select();
		return false;
	}
	else */if (form.anio.value <= 2000) {
		alert("Debe especificar el a�o de consulta");
		form.anio.select();
		return false;
	}
	else
		form.submit();
}

window.onload = document.form.num_cia[0].select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia: {num_cia} </td>
    <td class="print_encabezado" align="center"><span style="font-size: 14pt;">{nombre}</span></td>
    <td class="rprint_encabezado">Cia: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Consumos Anuales por Compa&ntilde;&iacute;a del {anio}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <tr>
    <th width="16%" class="print" scope="col">Producto</th>
    <th width="6%" class="print" scope="col">Ene</th>
    <th width="6%" class="print" scope="col">Feb</th>
    <th width="6%" class="print" scope="col">Mar</th>
    <th width="6%" class="print" scope="col">Abr</th>
    <th width="6%" class="print" scope="col">May</th>
    <th width="6%" class="print" scope="col">Jun</th>
    <th width="6%" class="print" scope="col">Jul</th>
    <th width="6%" class="print" scope="col">Ago</th>
    <th width="6%" class="print" scope="col">Sep</th>
    <th width="6%" class="print" scope="col">Oct</th>
    <th width="6%" class="print" scope="col">Nov</th>
    <th width="6%" class="print" scope="col">Dic</th>
    <th width="6%" class="print" scope="col">Total</th>
    <th width="6%" class="print" scope="col">Promedio</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint" style="font-weight:bold">{codmp} {nombre}</td>
    <td class="rprint">{1}</td>
    <td class="rprint">{2}</td>
    <td class="rprint">{3}</td>
    <td class="rprint">{4}</td>
    <td class="rprint">{5}</td>
    <td class="rprint">{6}</td>
    <td class="rprint">{7}</td>
    <td class="rprint">{8}</td>
    <td class="rprint">{9}</td>
    <td class="rprint">{10}</td>
    <td class="rprint">{11}</td>
    <td class="rprint">{12}</td>
    <td class="rprint" style="font-weight:bold">{total}</td>
    <td class="rprint" style="color:#00C">{prom}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : totales -->
  <tr>
    <th class="rprint">Total</th>
    <th class="rprint">{1}</th>
    <th class="rprint">{2}</th>
    <th class="rprint">{3}</th>
    <th class="rprint">{4}</th>
    <th class="rprint">{5}</th>
    <th class="rprint">{6}</th>
    <th class="rprint">{7}</th>
    <th class="rprint">{8}</th>
    <th class="rprint">{9}</th>
    <th class="rprint">{10}</th>
    <th class="rprint">{11}</th>
    <th class="rprint">{12}</th>
    <th class="rprint_total">{total}</th>
    <th class="rprint">&nbsp;</th>
  </tr>
  <!-- END BLOCK : totales -->
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
