<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">


<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida(){
	if(document.form.anio.value=="" || document.form.anio.value < 2005)
		alert("El año es incorrecto");
	else
		document.form.submit();
}
</script>

<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">&Uacute;LTIMOS FOLIOS DE CHEQUES </p>
  <form action="./ban_ult_fol.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla">Mes</th>
      <th class="tabla">Anio</th>
    </tr>
    <tr>
      <td class="tabla">
	  <select name="mes" class="insert" id="mes">
	  <!-- START BLOCK : mes -->
        <option value="{mes}" {selected}>{nombre_mes}</option>
	  <!-- END BLOCK : mes -->
      </select></td>
      <td class="tabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Consultar" onClick="valida();"> 
  </p>
	</form></td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p><strong>ULTIMOS FOLIOS IMPRESOS DE CHEQUES CORRESPONDIENTES A <br> 
  {mes} DEL {anio}</strong></p>

<table class="print">
  <tr class="print">
    <th colspan="2" class="print" scope="col">Compañía</th>
    <th scope="col" class="print" colspan="2">Proveedor</th>
    <th scope="col" class="print">Fecha</th>
    <th scope="col" class="print">Importe</th>
    <th scope="col" class="print">FOLIO</th>
  </tr>
  <!-- START BLOCK : rows -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{num_cia}</td>
    <td class="vprint">{nombre_cia}</td>
    <td class="rprint">{num_proveedor}</td>
    <td class="vprint">{nombre_proveedor}</td>
    <td class="print">{fecha}</td>
    <td class="print">{importe}</td>
    <td class="print"><strong>{folio}</strong></td>
  </tr>
  <!-- END BLOCK : rows -->
</table>


</td>
</tr>
</table>
<!-- END BLOCK : listado -->