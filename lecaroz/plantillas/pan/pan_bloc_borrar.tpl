<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consutla de Blocks de pastel</p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.cia.value<0 || document.form.cia.value=="" && document.form.stat.value==0)
		alert("Compañía erronea");
	else
	document.form.submit();
}
</script>

<form name="form" method="get" action="./pan_bloc_borrar.php">

  <table class="tabla">
    <tr class="tabla">
      <th class="vtabla">
        <label>
      <input name="tipo_con" type="radio" onChange="form.stat.value=0" value="0" checked>
      Compa&ntilde;&iacute;a	    </label>
        <input name="cia" type="text" class="insert" id="cia" onKeyDown="if (event.keyCode == 13) document.form.enviar.select();" size="10">      </th>
    </tr>
    <tr class="tabla">
      <td class="vtabla" colspan="2">


          <label>
          <input type="radio" name="tipo_con" value="1" onChange="form.stat.value=1">
  Todas las compa&ntilde;&iacute;as </label>
          <input name="stat" type="hidden" class="insert" id="stat" value="0" size="10">
          <br>
        </p></td>
    </tr>
  </table>

  <br>
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="consultar">
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.cia.select();</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : bloc -->

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">BLOCKS DE PASTEL RECIBIDOS </p>
<p class="title">EL {fecha}</p>
<!-- START BLOCK : compania -->
<table border="1" class="tabla">
	  <tr class="tabla">
		<th class="tabla" colspan="2">{num_cia}&#8212;{nombre_cia}</th>
		</tr>
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
	    <th class="tabla">FOLIO INCIO</th>
	    <th class="tabla">FOLIO FIN</th>
	    </tr>
	
	<!-- START BLOCK : rows -->
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
		<td class="tabla">{let_folio}&#8212;{folio_inicio}</td>	
		<td class="tabla">{let_folio}&#8212;{folio_final}</td>
		</tr>
	<!-- END BLOCK : rows --> 
	  
	</table>
<!-- END BLOCK : compania -->
<br>

<table border="1" class="tabla">
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
	    <th class="tabla">Blocks enviados </th>
	    <td class="tabla"><font size="+1"><strong>{total}</strong></font></td>
	    </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : bloc -->