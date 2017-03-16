<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_dato -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Registro de Porcentajes de Compa&ntilde;&iacute;as </P>
<form name="formi" method="get" action="./fac_porc_fac.php" onKeyDown="if (event.keyCode == 13) document.formi.enviar.focus();">

  <table class="tabla">
    <tr>
      <th class="tabla" align="center"><font size="+1">Compa&ntilde;&iacute;a</font></th>
    </tr>

    <tr>
      <td class="tabla" align="center">
          <input name="compania" type="text" class="insert" id="compania" size="5">
		  
      </td>
    </tr>

  </table>
  <br>

  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Continuar" onclick='document.formi.submit();'>
    
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.formi.compania.select();</script>

<!-- END BLOCK : obtener_dato -->



<!-- START BLOCK : porcentajes -->
<script language="JavaScript" type="text/JavaScript">
	function verifica(valor,temp,total)
	{
	if (temp.value=="") temp.value=0;
	if (valor.value=="") valor.value=0;
	if (total.value=="") total.value=0;

	var entrada=parseFloat(valor.value);
	var tem=parseFloat(temp.value);
	var suma=parseFloat(total.value);
	
	if(tem > 0)
		{
		suma-=tem;
		suma+=entrada;
		}
	else
		suma+=entrada;
	
	total.value=suma.toFixed(4);
	
	if(total.value==100)
		{
		document.form.enviar.disabled=false;

		}
	else
		{
		document.form.enviar.disabled=true;

		}
	}
	

</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Registro de Porcentajes de Compa&ntilde;&iacute;as </P>
<form name="form" method="post" action="./insert_fac_porc.php?tabla" ><table class="tabla">
    <tr>
      <th class="tabla" align="center"><font size="+1">Compa&ntilde;&iacute;a</font></th>
    </tr>

    <tr>
      <td class="tabla" align="center">
          <input name="num_cia" type="hidden" class="insert" id="num_cia" value="{num_cia}" size="5"><font size="+1">
		  <input name="temp" type="hidden" id="temp" size="5">
{num_cia}&#8212;{nom_cia} </font></td>
    </tr>

  </table>
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" align="center">Porcentajes</th>
      </tr>
	<!-- START BLOCK : rows -->
    <tr>
      <td class="tabla"><input name="porcentaje{i}" type="text" class="rinsert" id="porcentaje{i}" value="{porcentaje}" size="10" onFocus="form.temp.value=this.value" onChange="verifica(this,form.temp,form.total)" onKeyDown="if(event.keyCode==13)form.porcentaje{next}.select();"></td>
      </tr>
	<!-- END BLOCK : rows -->
    <tr>
      <td class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="10" readonly></td>
    </tr>

  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='document.form.submit();' disabled="true">
&nbsp;&nbsp;
	<input type="button" class="boton" value="Regresar" onclick='parent.history.back()'>
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.porcentaje0.select();</script>
<!-- END BLOCK : porcentajes -->