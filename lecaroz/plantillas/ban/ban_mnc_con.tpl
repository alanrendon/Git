<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_anio  -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.anio.value <= 0) {
			alert('Debe especificar un año para la consulta');
			document.form.anio.select();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.anio.select();
		}
	}
	
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">MOVIMIENTOS CONCILIADOS MAYORES A 30 DIAS</p>
<form name="form" action="./ban_mnc_con.php" method="get">
<input type="hidden" name="temp">
<table class="tabla">
  <tr>
    <th class="vtabla">A&Ntilde;O</th>
    <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	<input name="anio" type="text" class="insert" id="anio" size="5" maxlength="5" value="{anio_actual}" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';">
	</td>
  </tr>
</table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Siguiente" onclick='valida_registro()' onb>
    </p>
  </form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_anio -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="print_encabezado"><strong>DEPOSITOS CONCILIADOS MAYORES A 30 DIAS<br>
  A&Ntilde;O {anio} </strong></p>
<table class="print">
  <tr>
    <th class="print"><font size="2"><strong>Concepto</strong></font></th>
    <th class="print"><font size="2"><strong>Fecha del movimiento </strong></font></th>
    <th class="print"><font size="2"><strong>Fecha de conciliaci&oacute;n</strong></font> </th>
    <th class="print"><font size="2"><strong>Importe</strong></font></th>
    <th class="print"><font size="2"><strong>Dias de diferencia</strong></font> </th>
  </tr>
  <!-- START BLOCK : cias -->
  <tr class="print">
 	 <td class="rprint"><font size="2"><strong>{num_cia}</strong></font></td>
     <td colspan="4" class="vprint"><font size="2"><strong>{nombre_cia}</strong></font></td>
  </tr>
  <!-- START BLOCK : rows -->
  <tr class="print">
    <td class="vprint">{concepto}</td>
    <td class="print">{fecha_mov}</td>
    <td class="print">{fecha_con}</td>
    <td class="print">{importe}</td>
    <td class="print">{dias}</td>
  </tr>
  <!-- END BLOCK : rows -->
  <!-- END BLOCK : cias -->
</table>
</td>
</tr>
</table>

<!-- END BLOCK : listado -->