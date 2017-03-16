<!-- START BLOCK : fecha_con -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.fecha_con.value == "") {
			alert("Debe especificar la fecha de conciliación");
			document.form.fecha_con.select();
			return false;
		}
		else
			document.form.submit();
	}
</script>
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Conciliaci&oacute;n Manual </p>
<form name="form" method="get">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Fecha de Conciliaci&oacute;n <font size="-2">(ddmmaa)</font></th>
    <td class="vtabla"><input name="fecha_con" type="text" class="insert" id="fecha" onChange="if (actualiza_fecha(this)) return; else this.select();" onKeyDown="if (event.keyCode == 13 && this.value != '') form.enviar.focus();" value="{fecha}" size="10" maxlength="10"></td>
  </tr>
</table>
<p>
  <input name="enviar" type="button" class="boton" id="enviar" value="Siguiente >>" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.fecha_con.select();</script>
<!-- END BLOCK : fecha_con -->

<!-- START BLOCK : conciliacion -->
<script language="javascript" type="text/javascript">
	function divide_deposito(id) {
		res = window.open("","dividir","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=640,height=480");
		document.form.accion.value = "";
		document.form.method = "post";
		document.form.target = "dividir";
		document.form.action = "./ban_dep_div.php?id="+id;
		document.form.submit();
	}
	
	function agregar_mov() {
		res = window.open("","agregar_mov","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
		document.form.accion.value = "";
		document.form.method = "post";
		document.form.target = "agregar_mov";
		document.form.action = "./ban_mov_cap.php";
		document.form.submit();
	}
	
	function ir_a(cia) {
		if (parseInt(cia.value) < 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.focus();
			return false;
		}
		else {
			res = window.open("","ir_a","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=850,height=700");
			res.moveTo(87,34);
			document.form.accion.value = "ir_a";
			document.form.method = "post";
			document.form.target = "ir_a";
			document.form.action = "./ban_res_con.php";
			document.form.submit();
		}
	}
	
	function siguiente() {
		res = window.open("","siguiente","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=850,height=700");
		res.moveTo(87,34);
		document.form.accion.value = "siguiente";
		document.form.method = "post";
		document.form.target = "siguiente";
		document.form.action = "./ban_res_con.php";
		document.form.submit();
	}
	
	function terminar() {
		document.form.accion.value = "terminar";
		document.form.method = "get";
		document.form.target = "_self";
		document.form.action = "./ban_conciliacion.php";
		document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Conciliaci&oacute;n Manual </p>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Cuenta</th>
    <th class="tabla" scope="col">Fecha de Conciliaci&oacute;n</th>
  </tr>
  <tr>
    <td class="tabla" scope="row"><font size="+2" color="#0066FF"><strong>{num_cia} - {nombre_cia} </strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{cuenta}</strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{fecha_con}</strong></font></td>
  </tr>
</table>
<br>
<form name="form">
<input name="accion" type="hidden">
<table>
<tr>
<td valign="top">
<strong><font size="+1" face="Geneva, Arial, Helvetica, sans-serif">Cheques</font></strong>
<!-- START BLOCK : sin_cheques -->
<br>
<strong><font face="Geneva, Arial, Helvetica, sans-serif" color="#FF0000">Sin movimiento de cheques</font></strong>
<!-- END BLOCK : sin_cheques -->
<!-- START BLOCK : cheques -->
<input name="num_che" type="hidden" id="num_che" value="{num_che}">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col"><img src="./menus/insert.gif" alt="Conciliar cheques" width="16" height="16" onClick="select_all();"></th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">No. Cheque</th>
    <th class="tabla" scope="col">Monto</th>
  </tr>
  <!-- START BLOCK : fila_cheque -->
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="tabla"><input name="che_con{i}" type="checkbox" id="che_con{i}" value="{id}" {checked}>
	  <!--<input name="id_che{i}" type="hidden" id="id_che{i}" value="{id}">-->
	</th>
    <td class="tabla"><strong>{fecha}</strong></td>
    <td class="tabla"><strong>{num_cheque}</strong></td>
    <td class="rtabla"><strong><input name="monto{i}" type="hidden" value="{monto}">{fmonto}</strong></td>
  </tr>
  <!-- END BLOCK : fila_cheque -->
</table>
<!-- END BLOCK : cheques -->
</td>
<td width="50"></td>
<td valign="top">
<strong><font size="+1" face="Geneva, Arial, Helvetica, sans-serif">Dep&oacute;sitos</font></strong>
<!-- START BLOCK : sin_depositos -->
<br>
<strong><font face="Geneva, Arial, Helvetica, sans-serif" color="#FF0000">Sin movimiento de depósitos</font></strong>
<!-- END BLOCK : sin_depositos -->
<!-- START BLOCK : depositos -->
<input name="num_dep" type="hidden" id="num_dep" value="{num_dep}">
<table class="tabla">
  <tr>
	<th class="tabla" scope="col"><img src="./menus/insert.gif" alt="Conciliar depósitos" width="16" height="16"></th>
    <th class="tabla" scope="col">Fecha</th>
    <th colspan="2" class="tabla" scope="col">Cod. movimiento </th>
    <th class="rtabla" scope="col">Importe</th>
    <th class="tabla" scope="col">&nbsp;</th>
  </tr>
  <!-- START BLOCK : fila_deposito -->
  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="tabla"><input name="dep_con{i}" type="checkbox" id="dep_con{i}" value="{id}" {checked}>
      <!--<input name="id_dep{i}" type="hidden" id="id_dep{i}" value="{id}"></th>-->
    <td class="tabla"><strong>{fecha}</strong></td>
    <td class="vtabla"><strong>{cod_mov}</strong></td>
    <td class="vtabla"><strong>{descripcion}</strong></td>
    <td class="rtabla"><strong><strong>
      <input name="importe{i}" type="hidden" value="{importe}">
    </strong>{fimporte}</strong></td>
    <td class="tabla"><input name="" type="button" class="boton" value="Dividir" onClick="divide_deposito({id})"></td>
  </tr>
  <!-- END BLOCK : fila_deposito -->
</table>
<!-- END BLOCK : depositos -->
</td>
</tr>
</table>
<p>
  <input type="button" value="Otros movimientos" class="boton" onClick="agregar_mov()">
</p>
<table class="tabla">
<tr>
  <td colspan="2" class="vtabla"><strong>Usted est&aacute; en: {num_cia} - {nombre_cia} </strong></td>
  </tr>
<tr>
  <td colspan="2">&nbsp;</td>
</tr>
<tr><td>
<strong>
<font face="Geneva, Arial, Helvetica, sans-serif">Ir a:</font></strong>
<select name="num_cia" id="num_cia" class="insert" onChange="ir_a(this)">
  <!-- START BLOCK : cia -->
  <option value="{index}" {selected}>{num_cia} - {nombre_cia}</option>
  <!-- END BLOCK : cia -->
</select>
</td>
<td><input name="" type="button" class="boton" value="Siguiente >>" onClick="siguiente()"> </td></tr>
</table>
<p><input name="" type="button" class="boton" value="Terminar" onClick="terminar()"></p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : conciliacion -->

<!-- START BLOCK : listado_final -->
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
<script language="javascript" type="text/javascript">
	function imprimir() {
		window.print();
		document.location = "./ban_conciliacion.php";
	}
	
	window.onload = imprimir();
</script>
<!-- END BLOCK : listado_final -->