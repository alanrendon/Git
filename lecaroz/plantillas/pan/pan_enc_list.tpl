<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida(){
	if(document.form.anio.value==""){
		alert("debes especificar un año de consulta");
		document.form.anio.select();
	}
	else
		document.form.submit();
}
</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consulta de sueldo de encargados </p>

<form name="form" method="get" action="./pan_enc_list.php">

<table class="tabla">
  <tr class="tabla">
    <th class="tabla">Mes
	<select name="mes" class="insert">
	<!-- START BLOCK : mes -->
		<option value="{mes}" {select}>{nombre_mes}</option>
	<!-- END BLOCK : mes -->
	</select>
	</th>
	<th class="tabla">Año<input name="anio" type="text" value="{anio_actual}" size="5" class="insert"></th>
  </tr>
  <tr>
    <td class="tabla" colspan="2">Compañía <input name="cia" type="text" size="5" class="insert">
	</td>
  </tr>
</table>


	<p>  
	<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="consultar">
	</p>
</form>
</td>
</tr>
</table>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.cia.select();
</script>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="print_encabezado"><strong>SUELDO DE ENCARGADOS DE {mes} DEL {anio}</strong></p>
<!-- START BLOCK : cias -->

<table width="40%" class="print">
  <tr class="print">
	<th colspan="3" class="print"><font size="2"><strong>{num_cia} - {nombre_cia}</strong></font></th>
  </tr>
  <tr class="print">
	<td class="print"><strong>INICIA</strong></td>  
	<td colspan="2" class="vprint"><strong>{nombre_inicia}</strong></td>
  </tr>
  <tr class="print">
	<td class="print"><strong>TERMINA</strong></td>
	<td colspan="2" class="vprint"><strong>{nombre_termina}</strong></td>
  </tr>
  
  <tr class="print">
    <th scope="col" class="print">Dia</th>
    <th scope="col" class="print">Concepto de gasto</th>
	<th scope="col" class="print">Importe</th>
  </tr>
<!-- START BLOCK : rows -->  
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="vprint">{concepto}</td>
	<td class="print">{importe}</td>
  </tr>
<!-- END BLOCK : rows -->  
  <tr class="print">
    <td scope="col" class="rprint" colspan="2"><strong>TOTAL</strong></td>
	<td scope="col" class="print"><strong>{total}</strong></td>
  </tr>
  <tr class="print">
    <td scope="col" class="rprint" colspan="2"><strong>LIMITE</strong></td>
	<td scope="col" class="print"><strong>{limite}</strong></td>
  </tr>
  <tr class="print">
	<td colspan="2" class="rprint"><strong>DIFERENCIA</strong></td>  
	<td class="print"><font color="{color}">{diferencia}</font></td>
  </tr>
</table>
<br>
<!-- END BLOCK : cias -->


</td>
</tr>
</table>

<!-- END BLOCK : listado -->