<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Solicitud de modificaci&oacute;n de facturas de pastel</p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.num_cia0.value<0 || document.form.num_cia0.value=="")
		alert("Compañía erronea");
	if (document.form.num_fact0.value<0 || document.form.num_fact0.value=="")
		alert("Revise el número de factura por favor");
	if (document.form.descripcion0.value=="")
		alert("Introduzca la razón de la modificación");
	else
	document.form.submit();
}
	function actualiza_compania(num_cia, nombre) 
	{
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) 
		{
			if (cia[parseInt(num_cia.value)] == null) 
			{
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else 
			{
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") 
		{
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}



</script>


<form name="form" method="get" action="./pan_rev_sol.php">
<p class="title">Revisar solicitudes por Capturista</p>

<p>
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">Nombre</th>
  </tr>
  <tr class="tabla">
    <td class="tabla">
	<select name="capturistas" size="1" class="insert">
	<!-- START BLOCK : capturistas -->
	  <option value="{num_cap}" class="insert">{nom_cap}</option>
	<!-- END BLOCK : capturistas -->
	</select>

	</td>
  </tr>
</table>

</p>

<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="Consultar" {disabled}>
</p>
</form>

</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : pasteles -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Solicitud de modificaci&oacute;n de {nom_usuario} </p>


<form name="form" method="post" action="./actualiza_pastel_fac.php">
  <table class="tabla">
    <tr class="tabla">
      <th scope="col" class="tabla">Compa&ntilde;&iacute;a</th>
      <th scope="col" class="tabla">N&uacute;mero de factura </th>
      <th class="tabla">Descripción</th>

  
      <th class="tabla">Kilos</th>
      <th class="tabla">Precio unidad</th>
      <th class="tabla">Pan</th>
      <th class="tabla">Base</th>
      <th class="tabla">Pérdida</th>
      <th class="tabla"> Fecha Entrega</th>
      <th class="tabla">Cancelaci&oacute;n</th>
      <th class="tabla">Cambio de Fecha</th>
	  <th class="tabla">Autorizar</th>
    </tr>
	<!-- START BLOCK : renglones -->
    <tr class="tabla">
      <td class="vtabla"><input name="id{i}" type="hidden" class="insert" id="id{i}" value="{id}" size="3"> 
        {num_cia}&#8212;{nom_cia} </td>
      <td class="tabla">{let_folio}&nbsp;{num_fact}            </td>
		<td class="tabla">{descripcion}		  </td>
		

	    <td class="tabla">{kilos}</td>
	    <td class="tabla">{precio_unidad}</td>
	    <td class="tabla">{otros}</td>
	    <td class="tabla">{base}</td>
	    <td class="tabla">{perdida}</td>
	    <td class="tabla">{cambio_fecha}</td>
	    <td class="tabla">{cancelar}</td>
		<td class="tabla">{fecha_nueva}</td>
	    <td class="tabla"><input name="autorizar" type="checkbox" id="autorizar" 
		onChange="if(parseFloat(document.form.autorizado{i}.value)==1) document.form.autorizado{i}.value=0; else if (parseFloat(document.form.autorizado{i}.value)==0) document.form.autorizado{i}.value=1;" value="0" checked>
		  <input name="autorizado{i}" type="hidden" class="insert" id="autorizado{i}" value="1" size="5"></td>
	    
	    <!-- END BLOCK : renglones -->
    </tr>
  </table>
<p>
  <input name="regresar" type="button" class="boton" id="regresar" onClick="parent.history.back();" value="Regresar">  

<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="Autorizar">


</p>
<input name="contador" type="hidden" class="insert" id="contador" value="{cont}" size="5">
</form>

</td>
</tr>
</table>
<!-- END BLOCK : pasteles -->