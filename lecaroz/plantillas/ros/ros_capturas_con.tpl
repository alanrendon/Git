<!-- tabla catalogo_productos_proveedor -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia.value <= 0) {
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
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. Y C.V. </P>
<p class="title">Consulta de &Uacute;ltimas Entradas </P>


  <br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center" colspan="2">Compa&ntilde;&iacute;a       </th>
      <th class="tabla" align="center">&nbsp;&nbsp;&nbsp;Facturas&nbsp;&nbsp;&nbsp;</th>
      <th class="tabla" align="center">&nbsp;&nbsp;&nbsp;Efectivos&nbsp;&nbsp;&nbsp;</th>
      <th class="tabla" align="center">&nbsp;&nbsp;Estado&nbsp;&nbsp;</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">{num_cia}	  	  </th>
	  <th class="vtabla">{nom_cia}</th>
      <td class="rtabla">{fecha_fac}
      </td>
      <td  class="rtabla">{fecha_efe}
      </td>
      <td class="tabla">{estado}</td>
    </tr>
	<!-- END BLOCK : rows -->
</table>


</td>
</tr>
</table>


