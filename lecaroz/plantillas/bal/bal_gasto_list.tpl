<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de importes anualizado de gastos </p>
  <form action="./bal_gasto_list.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;ia</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) codgasto.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Gasto</th>
      <td class="vtabla"><input name="codgasto" type="text" class="insert" id="codgasto" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
	
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.anio.value < 2005) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else if(form.codgasto.value < 0 || form.codgasto.value ==''){
			alert("Revise el gasto");
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado del gasto {nombre_gasto} del {anio} <br>      </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Enero</th>
      <th class="print" scope="col">Febrero</th>
      <th class="print" scope="col">Marzo</th>
      <th class="print" scope="col">Abril</th>
      <th class="print" scope="col">Mayo</th>
      <th class="print" scope="col">Junio</th>
      <th class="print" scope="col">Julio</th>
      <th class="print" scope="col">Agosto</th>
      <th class="print" scope="col">Septiembre</th>
      <th class="print" scope="col">Octubre</th>
      <th class="print" scope="col">Noviembre</th>
      <th class="print" scope="col">Diciembre</th>
      <th class="print" scope="col">Total</th>
      <th class="print" scope="col">Prom</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rprint">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint">{1}</td>
      <td class="rprint">{2}</td>
      <td class="rprint">{3}</td>
      <td class="rprint">{4}</td>
      <td class="rprint">{5}</td>
      <td class="rprint">{6}</td>
      <td class="rprint">{7}</td>
      <td class="rprint">{8}</td>
      <td class="rprint">{9}</td>
      <td class="rprint">{10}</td>
      <td class="rprint">{11}</td>
      <td class="rprint">{12}</td>
      <td class="rprint"><strong>{total}</strong></td>
      <td class="rprint"><strong>{prom}</strong></td>
    </tr>
	<!-- END BLOCK : fila -->
	<tr>
	  <th colspan="2" class="rprint"> <strong> <font size="2">Totales</font></strong></th>
      <th class="rprint_total"><strong>{t1}</strong></th>
      <th class="rprint_total"><strong>{t2}</strong></th>
      <th class="rprint_total"><strong>{t3}</strong></th>
      <th class="rprint_total"><strong>{t4}</strong></th>
      <th class="rprint_total"><strong>{t5}</strong></th>
      <th class="rprint_total"><strong>{t6}</strong></th>
      <th class="rprint_total"><strong>{t7}</strong></th>
      <th class="rprint_total"><strong>{t8}</strong></th>
      <th class="rprint_total"><strong>{t9}</strong></th>
      <th class="rprint_total"><strong>{t10}</strong></th>
      <th class="rprint_total"><strong>{t11}</strong></th>
      <th class="rprint_total"><strong>{t12}</strong></th>
	  <th class="rprint_total">{total}</th>
	  <th class="rprint_total">{prom}</th>
	</tr>	
</table>
  
<!-- END BLOCK : listado -->
