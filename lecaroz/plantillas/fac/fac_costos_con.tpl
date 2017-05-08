<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
function valida()
{
if (document.form.num_cia.value==0 && document.form.temp1.value==0){
	alert("Debe especificar una compañía");
	document.form.num_cia.select();
}	
else document.form.submit();
}	
</script>
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">

<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/listado.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Costos Unitarios de Materias Primas </P>
<form name="form" method="get" action="./fac_costos_con.php">
<input name="temp" type="hidden" value="">
  <table class="tabla">
  <tr class="tabla">
  <th class="tabla" colspan="2">TIPO DE CONSULTA</th>
  </tr>
  <tr class="tabla">
    <td class="vtabla">
	  <label> <input type="radio" name="tipo_con" value="0" checked onChange="document.form.temp1.value=0;">Compañía</label>
	  <input name="num_cia" type="text" id="num_cia" size="5" maxlength="5" class="insert" onChange="valor=isInt(this,form.temp);" onFocus="form.temp.value=this.value">
	  <input name="temp1" type="hidden" class="insert" id="temp1" value="0" size="5" maxlength="5">
	  <input name="temp2" type="hidden" class="insert" id="temp2" value="0" size="5" maxlength="5">
	  
	</td>
	<td class="vtabla">
	  <p>
	    <label>
	    <input type="radio" name="tipo_con" value="1" onChange="document.form.temp1.value=1;">
	    Todas</label>
	  </p>
	  </td>
	
  </tr>
  <tr class="tabla">
  	<td class="vtabla" colspan="2">
	    <label>
	    <input type="radio" name="mat_prima" value="0" checked onChange="document.form.temp2.value=0;">
Materia Prima</label>
	    <span class="vtabla">
	    <input name="codmp" type="text" id="codmp" size="5" maxlength="5" class="insert" onChange="valor=isInt(this,form.temp);" onFocus="form.temp.value=this.value">
	    </span><br>
	    <label>
	    <input type="radio" name="mat_prima" value="1" onChange="document.form.temp2.value=1;">
Todas</label>
	</td>
  
  </tr>
  
</table>
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Consultar" onclick='valida()'>
  </p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->


<!-- START BLOCK : listado_cia -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p>
<p class="title">COSTOS UNITARIOS POR CADA COMPAÑÍA AL {dia} DE {mes} DE {anio}</p>
<table border="1" class="tabla">
  <tr class="tabla">
    <td scope="col" class="tabla" colspan="6"><font size="+1">{num_cia} &#8212; {nom_cia}</font></td>
  </tr>
  <tr class="tabla">
    <th scope="row" class="tabla" colspan="2">Materia Prima</th>
    <th class="tabla">Costo</th>

    <th class="tabla" colspan="2">Materia Prima</th>
    <th class="tabla">Costo</th>
  </tr>

<!-- START BLOCK : rows -->

  <tr class="tabla">
    <th class="rtabla">&nbsp;&nbsp;&nbsp;&nbsp;{codmp}</th>
    <td class="vtabla">{nom_mp}</td>
    <td class="rtabla">{costo}</td>

    <th class="rtabla">&nbsp;&nbsp;&nbsp;&nbsp;{codmp1}</th>
    <td class="vtabla">{nom_mp1}</td>
    <td class="rtabla">{costo1}</td>
  </tr>
<!-- END BLOCK : rows -->
</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado_cia -->

<!-- START BLOCK : listado_todos -->
<table width="100%"  height="98%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="listado_encabezado">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.<br>
COSTOS UNITARIOS POR CADA COMPAÑÍA AL {dia} DE {mes} DE {anio}
</p>
	<table width="100%" cellpadding="0" cellspacing="1" class="listado">
	  <tr class="listado">
		<th scope="col" width="20%"class="vlistado">COMPAÑÍA</th>
		<!-- START BLOCK : mat_prima -->
		<th scope="col" class="listado" width="10%"> {codmp} <br> {nom_mp}</th>
		<!-- END BLOCK : mat_prima -->
	  </tr>
		<!-- START BLOCK : rows1 -->
	  <tr class="listado" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<td class="vlistado">
		{num_cia} - {nom_cia}
		</td>
		<!-- START BLOCK : costo -->
		<td class="listado">{costo}</td>
		<!-- END BLOCK : costo -->
	  </tr>
		<!-- END BLOCK : rows1 -->
	</table>
</td>
</tr>
</table>
{salto_pagina}
<!-- END BLOCK : listado_todos -->


<!-- START BLOCK : listado_una -->
<table width="100%"  height="98%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="listado_encabezado">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.<br>
COSTOS UNITARIOS POR CADA COMPAÑÍA AL {dia} DE {mes} DE {anio}
</p>
	<table width="30%" cellpadding="0" cellspacing="2" class="listado">
	  <tr class="listado">
		<th scope="col" width="20%"class="vlistado">COMPAÑÍA</th>
		<!-- START BLOCK : mat_primaA -->
		<th scope="col" class="listado" width="10%"> {codmp} <br> {nom_mp}</th>
		<!-- END BLOCK : mat_primaA -->
	  </tr>
		<!-- START BLOCK : rows1A -->
	  <tr class="listado" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		<td class="vlistado">
		{num_cia}
		<!-- START BLOCK : nombre_ciaA -->
		{nom_cia}
		<!--END BLOCK : nombre_ciaA -->
		</td>
		<!-- START BLOCK : costoA -->
		<td class="listado">{costo}</td>
		<!-- END BLOCK : costoA -->
	  </tr>
		<!-- END BLOCK : rows1A -->
	</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado_una -->


<!-- START BLOCK : listado_cia_mp -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p>
<p class="title">COSTOS UNITARIOS POR CADA COMPAÑÍA AL {dia} DE {mes} DE {anio}</p>
<table border="1" class="tabla">
  <tr class="tabla">
    <td scope="col" class="tabla" colspan="3" ><font size="+1">{num_cia} &#8212; {nom_cia}</font></td>
  </tr>
  <tr class="tabla">
    <th scope="row" class="tabla" colspan="2">Materia Prima</th>
    <th class="tabla">Costo</th>
  </tr>

  <tr class="tabla">
    <th class="rtabla">&nbsp;&nbsp;&nbsp;&nbsp;{codmp}</th>
    <td class="vtabla">{nom_mp}</td>
    <td class="rtabla">{costo}</td>
  </tr>

</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado_cia_mp -->
