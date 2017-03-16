<!-- START BLOCK : enviar_archivo -->
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Conciliaci&oacute;n Autom&aacute;tica <br>
  Banorte
</p>
<form name="form" enctype="multipart/form-data" method="post" action="./ban_con_aut_no_bloqueo.php?">
<input name="pantalla" type="hidden" value="1">
<table class="tabla">
<tr>
 <input name="MAX_FILE_SIZE" type="hidden" value="5242880">
 <th class="vtabla">Archivo de Movimientos Bancarios:</th>
 <td class="vtabla"><input name="userfile" type="file" class="vinsert" id="userfile" size="40" readonly="true"></td>
</tr>
</table>
{mensaje}
<p>
	<input name="enviar" type="button" class="boton" id="enviar" value="Enviar" onClick="valida_registro()"{disabled}>
</p>
</form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.userfile.value == "" && confirm('No especifico el archivo de movimientos, ¿desea conciliar de todas maneras?')) {
			document.location = './ban_con_aut_no_bloqueo.php?conciliar=1';
			return false;
		}
		else if (confirm("¿Desea comenzar con la conciliación?"))
			document.form.submit();
		else
			document.form.userfile.focus();
	}
</script>
 <!-- END BLOCK : enviar_archivo -->

<!-- START BLOCK : procesando -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p><strong><font face="Geneva, Arial, Helvetica, sans-serif">Procesando, por favor espere...</font> </strong></p>

</td>
</tr>
</table>
<!-- END BLOCK : procesando -->

 <!-- START BLOCK : val_cue -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Conciliaci&oacute;n Autom&aacute;tica</p>
<p><font face="Geneva, Arial, Helvetica, sans-serif">Las siguientes cuentas no se encuentran en el cat&aacute;logo de compa&ntilde;&iacute;as.</font></p>
 <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Cuenta</th>
    <th class="tabla" scope="col">A nombre de</th>
    </tr>
  <!-- START BLOCK : fila_cue -->
  <tr>
    <td class="tabla"><strong class="vtabla">{cuenta}</strong></td>
    <td class="vtabla"><strong>{nombre}</strong></td>
    </tr>
	<!-- END BLOCK : fila_cue -->
</table>
<p><font face="Geneva, Arial, Helvetica, sans-serif">&iquest;Desea proseguir con la conciliaci&oacute;n?</font></p>
<p>
  <input type="button" class="boton" value="Cancelar" onClick="cancelar()">
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Siguiente >>" onClick="siguiente()">
</p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function siguiente() {
		document.location = './ban_con_aut_no_bloqueo.php?val_cod=1';
	}

	function cancelar() {
		document.location = './ban_con_aut_no_bloqueo.php?cancelar=1';
	}
</script>
 <!-- END BLOCK : val_cue -->

 <!-- START BLOCK : val_cod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
 <p class="title">Conciliaci&oacute;n Autom&aacute;tica</p>
 <p><font face="Geneva, Arial, Helvetica, sans-serif">Los siguientes c&oacute;digos no se encuentran en el cat&aacute;logo de movimientos bancarios. No se proseguira con la conciliaci&oacute;n.</font></p>
 <table class="tabla">
   <tr>
     <th class="tabla">C&oacute;digo</th>
     <th class="tabla">Concepto</th>
     </tr>
   <!-- START BLOCK : fila_cod -->
   <tr>
     <th class="tabla"><strong>{cod_mov}</strong></th>
     <td class="vtabla"><strong>{concepto}</strong></td>
     </tr>
   <!-- END BLOCK : fila_cod -->
 </table>
 <p><input type="button" class="boton" value="Cancelar" onClick="cancelar()"></p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function cancelar() {
		document.location = './ban_con_aut_no_bloqueo.php';
	}
</script>
 <!-- END BLOCK : val_cod -->

 <!-- START BLOCK : cod3 -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Conciliaci&oacute;n Autom&aacute;tica </p>
<p class="title">Movimientos Bancarios con C&oacute;digo 3 </p>
<form name="form" method="post" action="./ban_con_aut_no_bloqueo.php?conciliar=1">
<input name="numfilas" type="hidden" value="{numfilas}">
 <table class="tabla">
  <tr>
    <th class="tabla" scope="col">&nbsp;</th>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Nombre</th>
    <th class="tabla" scope="col">Cuenta</th>
    <th class="tabla" scope="col">Dep&oacute;sito</th>
    <th class="tabla" scope="col">C&oacute;digo Bancario </th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">C&oacute;digo de Movimiento </th>
  </tr>
  <!-- START BLOCK : fila3 -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla"><input name="id{i}" type="checkbox" id="id{i}" value="{id}"></td>
    <td class="tabla"><input name="num_cia{i}" type="hidden" id="num_cia{i}" value="{num_cia}">
    {num_cia}</td>
    <td class="vtabla">{nombre_cia}</td>
    <td class="tabla">{cuenta}</td>
    <td class="rtabla"><input name="importe{i}" type="hidden" id="importe{i}" value="{deposito}">
    <strong>{fdeposito}</strong></td>
    <td class="tabla"><strong>{cod_banco}</strong></td>
    <td class="vtabla">{concepto}</td>
    <td class="vtabla"><select name="cod_mov{i}" class="insert" id="cod_mov{i}">
      <!-- START BLOCK : cod_mov -->
	  <option value="{cod_mov}" {selected}>{cod_mov} - {descripcion}</option>
	  <!-- END BLOCK : cod_mov -->
    </select></td>
  </tr>
  <!-- END BLOCK : fila3 -->
</table>
 <p>
 <input type="button" class="boton" value="Cancelar" onClick="document.location = './ban_con_aut_no_bloqueo.php?cancelar=1'">
  &nbsp;&nbsp;
   <input type="button" class="boton" value="Siguiente" onClick="form.submit()">
 </p>
 </form>
 </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function siguiente() {
		window.open("./ban_con_aut_no_bloqueo.php?popup=1","popup","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=200");
		document.form.submit();
	}

	function cancelar() {
		document.location = './ban_con_aut_no_bloqueo.php?cancelar=1';
	}
</script>
<!-- END BLOCK : cod3 -->
<!-- START BLOCK : saldos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Saldos Bajos</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Saldo</th>
    </tr>
    <!-- START BLOCK : saldo -->
	<tr>
      <td class="vtabla"{styles}>{num_cia} {nombre} </td>
      <td class="rtabla"{styles}>{saldo}</td>
    </tr>
	<!-- END BLOCK : saldo -->
  </table>
  <p>
    <input type="button" class="boton" value="Continuar" onClick="document.location='./ban_con_aut_no_bloqueo.php?resultados=1'">
  </p>  <p>&nbsp; </p></td>
</tr>
</table>
<!-- END BLOCK : saldos -->
<!-- START BLOCK : resultados -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="top">
<td>
<!-- START BLOCK : conciliados -->
<p class="title">Conciliaci&oacute;n Autom&aacute;tica </p>
<p class="title">Movimientos Palomeados Autom&aacute;ticamente </p>
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
<!-- END BLOCK : conciliados -->

<!-- START BLOCK : autorizados -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="top">
<td>
<p class="title">Conciliaci&oacute;n Autom&aacute;tica </p>
<p class="title">Movimientos Autorizados en el Palomeo </p>
<!-- START BLOCK : cia_aut -->
<table class="tabla" width="100%">
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
  <!-- START BLOCK : fila_aut -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="rprint"><strong><font color="#0000FF">{deposito}</font></strong></td>
    <td class="rprint"><strong><font color="#FF0000">{retiro}</font></strong></td>
    <td class="print">{folio}</td>
	<td class="vprint">{codigo} {descripcion} </td>
    <td class="vprint">{concepto}</td>
  </tr>
  <!-- END BLOCK : fila_aut -->
   <tr>
    <th class="rprint">Total Cuenta</th>
    <th class="rprint_total">{total_deposito}</th>
    <th class="rprint_total">{total_retiro}</th>
    <th colspan="3" class="tabla">&nbsp;</th>
    </tr>
</table>
<br>
<!-- END BLOCK : cia_aut -->
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
<!-- END BLOCK : autorizados -->

<!-- START BLOCK : no_conciliados -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="top">
<td>
<p class="title">Conciliaci&oacute;n Autom&aacute;tica </p>
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
		<!-- END BLOCK : boton_d -->	</th>
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
  <input type="button" class="boton" value="Imprimir" onClick="window.print()">
&nbsp;&nbsp;
<input type="button" class="boton" value="Terminar" onClick="document.location = './ban_con_aut_no_bloqueo.php'">
</p>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function borrar(id) {
		window.open("./ban_mnc_del.php?id="+id,"borrar","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=200");
	}

	function modifica_retiro(id) {
		window.open("./ban_mnc_mre.php?id="+id,"mod_retiro","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
	}

	function modifica_depositos(form) {
		window.open("","mod_depositos","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
		form.target = "mod_depositos";
		form.action = "./ban_mnc_mde.php";
		form.method = "get";
		form.submit();
	}
</script>
<!-- END BLOCK : no_conciliados -->
<script language="javascript" type="text/javascript">
	function imprimir() {
		if (confirm("Imprimir listado"))
			window.print();
	}

	window.onload = imprimir();
</script>
 <!-- END BLOCK : resultados -->
