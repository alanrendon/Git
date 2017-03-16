<!-- START BLOCK : obtener_datos -->

<script type="text/javascript" language="JavaScript">
	function valida_registro() {

/*		if(document.form.con.value==0 && document.form.cia.value=="")
		{
			alert("Inserte un número de compañía");
			document.form.cia.select();
			return;
		}
*/		if(document.form.con.value==0 && parseInt(document.form.cia.value)<=0)
		{
			alert("Inserte un número de compañía");
			document.form.cia.select();
			return;
		}

		else if(document.form.con.value==1 && document.form.accionista.value=="")
		{
			alert("Inserte un número de accionista");
			document.form.accionista.select();
			return;
		}
		else if(document.form.con.value==1 && document.form.accionista.value<=0)
		{
			alert("Inserte un número de accionista");
			document.form.accionista.select();
			return;
		}
		else {
				document.form.submit();
			}
	}


</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="get" action="./admin_porc_list.php">
<p class="title">LISTADO DE PORCENTAJES DE ACCIONISTAS</p>

<table class="tabla">
  <tr class="tabla">
    <th class="tabla">

  <label><input name="tipo_con" type="radio" value="0" checked onChange="form.con.value=0">
  Por Compañía </label><input name="cia" type="text" class="insert" size="3" maxlength="3">
  <input name="acc" type="hidden" class="insert" id="acc" value="0" size="3" maxlength="3">
  <input name="con" type="hidden" class="insert" id="con2" value="0" size="3" maxlength="3">
  &nbsp;
  <label><input type="radio" name="tipo_con" value="1" onChange="form.con.value=1">Por Accionista </label><input name="accionista" type="text" class="insert" size="3" maxlength="2">
	</th>

  </tr>
  <!--
  <tr class="tabla">
    <td class="vtabla">      <p>
        <label>
        <input name="tipo_listado" type="radio" value="0" checked onChange="form.acc.value=0">
  Accionistas</label>
        <br>
        <label>
        <input type="radio" name="tipo_listado" value="1" onChange="form.acc.value=1">
  Distribuciones</label>
      </p>	</td>

  </tr>
  -->
</table>
<p>
  <input name="enviar" type="button" id="enviar" value="Enviar" onClick="valida_registro();" class="boton">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : compania -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.<br>
  {consulta}</p>
	<!-- START BLOCK : cia -->
	<table class="tabla">

	  <tr class="tabla">
		<th scope="col" class="tabla" colspan="2">{num_cia}&nbsp;{nombre_cia}</th>
	
	  </tr>
	  <tr class="tabla">
		<td class="tabla">Nombre del Accionista </td>
		<td class="tabla">Porcentaje(%)</td>
	  </tr>
	<!-- START BLOCK : accionista_registro -->
	  <tr class="tabla">
		<td class="vtabla">{accionista}</td>
		<td class="tabla">{porcentaje}</td>
	  </tr>
	<!-- END BLOCK : accionista_registro -->  
	</table>
	<!-- END BLOCK : cia -->
</td>
</tr>
</table>
<!-- END BLOCK : compania -->


<!-- START BLOCK : accionista -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.<br>
  {consulta}
</p>
<table class="tabla">
<!-- START BLOCK : accion -->
  <tr class="tabla">
    <th scope="col" class="tabla" colspan="2">{accionista}</th>
  </tr>
  
  <tr class="tabla">
    <td class="tabla">Compa&ntilde;&iacute;a </td>
    <td class="tabla">Porcentaje(%)</td>
  </tr>
  
<!-- START BLOCK : cia_registro -->
  <tr class="tabla">
    <td class="vtabla">{num_cia}&nbsp; {nombre_cia} </td>
    <td class="tabla">{porcentaje}</td>
  </tr>
<!-- END BLOCK : cia_registro -->

</table>
<!-- END BLOCK : accion -->
</td>
</tr>
</table>
<!-- END BLOCK : accionista -->
