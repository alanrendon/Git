<!-- START BLOCK : no_conciliados -->

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="top">
<td>
<p class="title">Movimientos Pendientes de Palomear</p>
<!-- START BLOCK : cia_nocon -->
<form name="form{num_cia}" method="get">
<table class="tabla" width="100%">
  <tr>
    <th class="print" scope="col">
		<a name="{num_cia}"></a>
		<!-- START BLOCK : boton_d -->
		<input name="numfilas" type="hidden" value="{numfilas}">
		<input type="button" class="boton" value="D" onClick="modifica_depositos(this.form)">
		<!-- END BLOCK : boton_d -->
		</th>
    <th class="print" scope="col">Cia.: {num_cia}</th>
    <th colspan="2" class="print" scope="col">Cuenta.: {cuenta}</th>
    <th colspan="3" class="print" scope="col">{nombre_cia} ({nombre_corto}) </th>
    </tr>
  <tr>
    <th class="print" width="10%">&nbsp;</th>
    <th class="print" width="10%">Fecha</th>
    <th class="print" width="10%">Dep&oacute;sito</th>
    <th class="print" width="10%">Retiro</th>
    <th class="print" width="10%">Cheque</th>
	<th class="print" width="15%">C&oacute;digo bancario </th>
    <th class="print" width="35%">Concepto</th>
    </tr>
  <!-- START BLOCK : fila_nocon -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">
		<input type="button" class="boton" value="Borrar" onClick="borrar({id})">
		<!-- START BLOCK : modifica_depositos -->
		<input name="id{i}" type="checkbox" id="id{i}" value="{id}">
		<!-- END BLOCK : modifica_depositos -->
		<!-- START BLOCK : modifica_retiros -->
		<input type="button" class="boton" value="R" onClick="modifica_retiro({id})">
		<!-- END BLOCK : modifica_retiros -->	</td>
    <td class="print">{fecha}</td>
    <td class="rprint"><strong><font color="#0000FF">{deposito}</font></strong></td>
    <td class="rprint"><strong><font color="#FF0000">{retiro}</font></strong></td>
    <td class="print">{folio}</td>
	<td class="print">{codigo}</td>
    <td class="vprint">{concepto}</td>
  </tr>
  <!-- END BLOCK : fila_nocon -->
   <tr>
     <th class="rprint">&nbsp;</th>
    <th class="rprint">Total Cuenta</th>
    <th class="rprint_total">{total_deposito}</th>
    <th class="rprint_total">{total_retiro}</th>
    <th colspan="3" class="tabla">&nbsp;</th>
    </tr>
</table>
</form>
<br>
<!-- END BLOCK : cia_nocon -->
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Total de Dep&oacute;sitos</th>
    <th class="tabla" scope="col">Total de Retiros </th>
  </tr>
  <tr>
    <th class="tabla"><font size="+1">{gran_total_deposito}</font></th>
    <th class="tabla"><font size="+1">{gran_total_retiro}</font></th>
  </tr>
</table>

<p>
  <input type="button" class="boton" value="Terminar" onClick="document.location = './ban_mov_pen.php?imp=1'">
</p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function borrar(id) {
		window.open("./ban_mov_del.php?id="+id,"borrar","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=200");
	}
	
	function modifica_retiro(id) {
		window.open("./ban_mov_ret.php?id="+id,"mod_retiro","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
	}
	
	function modifica_depositos(form) {
		window.open("","mod_depositos","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
		form.target = "mod_depositos";
		form.action = "./ban_mov_dep.php";
		form.method = "get";
		form.submit();
	}
</script>
<!-- END BLOCK : no_conciliados -->

<!-- START BLOCK : no_result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p><strong><font face="Geneva, Arial, Helvetica, sans-serif" color="#FF0000">No hay movimientos pendientes por conciliar</font></strong> </p>
  <p>
    <input type="button" class="boton" onClick="document.location = './ban_mov_pen.php?imp=1'" value="Terminar">
  </p></td>
</tr>
</table>
<!-- END BLOCK : no_result -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Movimientos Conciliados Manualmente<br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
<!-- START BLOCK : cia_con -->
<table class="print" width="100%">
  <tr>
    <th class="print" scope="col">Cia.: {num_cia}</th>
    <th colspan="2" class="print" scope="col">Cuenta.: {cuenta}</th>
    <th colspan="3" class="print" scope="col">{nombre_cia} ({nombre_corto})</th>
    </tr>
  <tr>
    <th class="print" width="15%">Fecha</th>
    <th class="print" width="10%">Dep&oacute;sito</th>
    <th class="print" width="10%">Retiro</th>
    <th class="print" width="10%">Cheque</th>
	<th class="print" width="25%">C&oacute;digo de movimiento</th>
    <th class="print" width="30%">Concepto</th>
    </tr>
  <!-- START BLOCK : fila_con -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="rprint"><strong><font color="#0000FF">{deposito}</font></strong></td>
    <td class="rprint"><strong><font color="#FF0000">{retiro}</font></strong></td>
    <td class="print">{folio}</td>
	<td class="vprint">{codigo} {descripcion} </td>
    <td class="vprint">{concepto}</td>
  </tr>
  <!-- END BLOCK : fila_con -->
   <tr>
    <th class="rprint">Total Cuenta</th>
    <th class="rprint_total">{total_deposito}</th>
    <th class="rprint_total">{total_retiro}</th>
    <th colspan="3" class="tabla">&nbsp;</th>
    </tr>
</table>
<br>
<!-- END BLOCK : cia_con -->
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Total de Dep&oacute;sitos</th>
    <th class="tabla" scope="col">Total de Retiros </th>
  </tr>
  <tr>
    <th class="tabla"><font size="+1">{gran_total_deposito}</font></th>
    <th class="tabla"><font size="+1">{gran_total_retiro}</font></th>
  </tr>
</table>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->