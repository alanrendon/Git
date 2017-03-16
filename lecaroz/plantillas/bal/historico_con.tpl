<script type="text/javascript" language="JavaScript">

	function valida_registro() {
		/*if(document.form.num_gastos.value <= 0) {
			alert('Debe especificar un numero de gastos a insertar');
			document.form.idcia.focus();
		}
		else {*/
//if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();
		//}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.num_gastos.focus();
	}
	

</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">


<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="http://192.168.1.250//var/www/html/lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">

<!--START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" action="./historico_con.php" method="get"  onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();" >
  <table border="1" class="print">
    <tr class="print">
      <th class="print">Consultar por: </th>
    </tr>
    <tr class="print">
      <td class="vtabla">
	  <input name="tipo_con" type="radio" value="0" checked>Todas las compañías<br>
	  <input name="tipo_con" type="radio" value="1">Compañía
      <input type="text" name="num_cia" class="insert" id="num_cia" size="5">	  
	  </td>
    </tr>
  </table>
  <p>
  <p></p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
  <input name="enviar" type="button" class="boton" id="enviar" onclick='valida_registro()' value="Consultar">
</form>
</td>
</tr>
</table>
<!--END BLOCK : obtener_datos -->


<!--START BLOCK : historico -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<form name="form" method="post" action="./insert_historico.php?tabla={tabla}">

  <!--START BLOCK : companias -->
  <table width="600" border="1" class="print">
      <tr class="print">
      <th class="print_encabezado"><font size="+1">Compa&ntilde;&iacute;a</font></th>
      <td class="print_encabezado" colspan="3"><font size="+1">{num_cia}-{nombre_cia}</font> </td>

  
    <tr class="print">
      <th class="print">Mes</th>
      <th class="print">A&ntilde;o anterior ({anio_anterior})</th>
      <th class="print">A&ntilde;o Actual ({anio_actual}) </th>
      <th class="print">Ventas</th>
    </tr>
    <!-- START BLOCK : rows -->
    <tr class="print">
      <th class="print" align="center">{nombre_mes}      </th>
      <td class="print" align="center"><span class="vtabla">{res_anio_anterior}
      </span> </td>
      <td class="print" align="center">{res_anio_actual}</td>
      <td class="print" align="center">{ventas}</td>
    </tr>
    <!-- END BLOCK : rows -->
    <tr class="print">
      <th class="print" align="center">TOTAL</th>
      <td class="print"><font size="+1">{total_anio_anterior}</font></td>
      <td class="print"><font size="+1">{total_anio_actual}</font></td>
      <td class="print"><font size="+1">{total_ventas}</font></td>
    </tr>
  </table>
    <br>
  <!--END BLOCK : companias -->
</form>

</td>
</tr>
</table>
<!-- END BLOCK : historico -->