<!-- tabla catalogo_productos_proveedor -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia.value < 0) {
			alert('Debe especificar una compañía');
			document.form.num_cia.select();
		}
		else {
				document.form.submit();
			}
	}

	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.num_cia.select();
		}
		else
			document.form.num_cia.select();
	}
	
</script>

<!-- START BLOCK : obtener_datos -->
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta al catálogo de Pesos por Compa&ntilde;&iacute;a </P>
<form name="form" method="get" action="./ros_pesos_con.php" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
  <table class="tabla">
  <tr class="tabla">
    <th class="tabla">Número de Compa&ntilde;&iacute;a 
      <input name="num_cia" type="text" id="num_cia" size="5" maxlength="5" class="insert"></th>
  </tr>
</table>

  
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Consultar" onclick='valida_registro()'>

  </p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V. </P>
<p class="title">Consulta al catálogo de Pesos por Compa&ntilde;&iacute;a </P>
<form name="form" method="post" action="./ros_pesos_mod.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
	<tr class="tabla">
		<th class="tabla" align="center">Precios de Materias Primas</th>
	</tr>
	<tr>
		<td class="tabla" align="center"><strong><font size="+1">{num_cia}&#8212;{nom_cia}</font></strong>
          <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">	  
      <input name="cont" type="hidden" id="cont" value="{count}">	  </td>
	</tr>
	
</table>
<br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center" colspan="2">C&oacute;digo materia prima </th>
      <th class="tabla" align="center">Peso máximo</th>
      <th class="tabla" align="center">Peso mínimo</th>
      <th class="tabla" align="center">Modificar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{codmp} 
	  <input name="codmp{i}" type="hidden" value="{codmp}">
	  <input name="id{i}" type="hidden" value="{id}">
	  </th>
	  <th class="vtabla">{nom_mp}</th>
      <td class="rtabla">{peso_maximo1}
        <input name="peso_maximo{i}" type="hidden" id="peso_maximo{i}" value="{peso_maximo}">
      </td>
      <td  class="rtabla">{peso_minimo1}
        <input name="peso_minimo{i}" type="hidden" id="peso_minimo{i}" value="{peso_minimo}">
      </td>
      <td class="tabla"><input type="checkbox" name="mod{i}" onClick="if (this.checked==true) document.form.modificar{i}.value=1; else if(this.checked==false)document.form.modificar{i}.value=0;"><input type="hidden" name="modificar{i}" value="0"></td>
    </tr>
	<!-- END BLOCK : rows -->
</table>
  
  <p>
<input type="button" name="enviar2" class="boton" value="Regresar" onclick='parent.history.back()'>&nbsp;&nbsp;
<input type="button" name="enviar2" class="boton" value="Modificar" onclick='document.form.submit();'>
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->

<!-- START BLOCK : listado1 -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado" align="center">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V. <br> RELACION DE PESOS DE MATERIA PRIMA POR COMPAÑÍA</p>

<table width="60%" class="print" >
  <tr class="print">
    <th colspan="2" class="print_cia">MATERIA PRIMA </th>
    <th class="print_cia">PESO M&Aacute;XIMO </th>
    <th class="print_cia">PESO M&Iacute;NIMO </th>
  </tr>
  
 <!-- START BLOCK : rows2 --> 
 
 	<!-- START BLOCK : cia2 -->
    <tr class="print">
    	<td colspan="4" class="vprint"><strong><font size="2">{num_cia}-{nombre_cia}</font></strong></td>
    </tr>
	<!-- END BLOCK : cia2 -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{codmp}</td>
    <td class="vprint">{nombre_mp}</td>
    <td class="print">{maximo}</td>
    <td class="print">{minimo}</td>
  </tr>
 <!-- END BLOCK : rows2 -->   
</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado1 -->