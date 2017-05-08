<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consulta al cat&aacute;logo de expendios </p>

<form name="form" method="get" action="./pan_exp_list.php">

<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">COMPAÑÍA</th>
  </tr>
  <tr>
    <td class="tabla">
	  <select name="cia" class="insert">
	<!-- START BLOCK : cias -->
		<option value="{num_cia}" class="insert">{num_cia}-{nombre_cia}</option>
	<!-- END BLOCK : cias -->
	  </select>
	</td>
  </tr>
</table>


	<p>  
	<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="consultar">
	</p>
</form>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">EXPENDIOS DE LA PANADERIA <br> {num_cia} - {nombre_cia}</p>

<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">&nbsp;N&uacute;mero de <br>expendio&nbsp; </th>
    <th scope="col" class="tabla">&nbsp;N&uacute;mero de <br>referencia&nbsp; </th>
    <th scope="col" class="tabla">&nbsp;Nombre&nbsp;</th>
    <th scope="col" class="tabla">&nbsp;Direcci&oacute;n&nbsp;</th>
    <th scope="col" class="tabla">&nbsp;Tipo de <br>expendio&nbsp; </th>
    <th scope="col" class="tabla">&nbsp;Porciento de <br>ganancia&nbsp;</th>
    <th scope="col" class="tabla">&nbsp;Importe fijo&nbsp; </th>
    <th scope="col" class="tabla">&nbsp;Autoriza<br>devoluci&oacute;n&nbsp;</th>
  </tr>
<!-- START BLOCK : rows -->  
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{num_exp}</td>
    <td class="tabla">{num_ref}</td>
    <td class="vtabla">{nombre}</td>
    <td class="tabla">{direccion}</td>
    <td class="vtabla">{tipo_exp}</td>
    <td class="tabla">{porciento}</td>
    <td class="tabla">{importe}</td>
    <td class="tabla">{devolucion}</td>
  </tr>
<!-- END BLOCK : rows -->  
</table>


<p>
  <input type="button" name="regresar" value="Regresar" onClick="parent.history.back();" class="boton">
</p></td>
</tr>
</table>
<!-- END BLOCK : listado -->

