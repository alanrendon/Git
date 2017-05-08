<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_compania -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
		}
		else if(document.form.num_proveedor.value <= 0) {
			alert("Debe especificar el número de proveedor");
			document.form.num_proveedor.select();
		}
		else {
			document.form.submit();
		}
	}
</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Captura de Facturas para Proveedores Varios </p>
<form name="form" method="get" action="./fac_fpv_cap.php">
<input name="temp" type="hidden">
<table class="tabla">
      <tr>
        <th class="vtabla">N&uacute;mero compa&ntilde;&iacute;a</th>
        <td class="vtabla">
		  <input name="num_cia" type="text" class="vinsert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_proveedor.select();
else if (event.keyCode == 38) form.fecha_mov.select();" size="5" maxlength="3">
        </td>
      </tr>
      <tr>
        <th class="vtabla">N&uacute;mero proveedor </th>
        <td class="vtabla">
          <input name="num_proveedor" type="text" class="vinsert" id="num_proveedor" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.fecha.select();
else if (event.keyCode == 38) form.num_cia.select();" value="{num_proveedor}" size="5" maxlength="5">
        </td>
      </tr>
      <tr>
        <th class="vtabla">Fecha movimiento <font size="-2">(ddmmaa)</font> </th>
        <td class="vtabla">
          <input name="fecha" type="text" class="vinsert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.enviar.focus();
else if (event.keyCode == 38) form.num_proveedor.select();" value="{fecha}" size="10" maxlength="10">
        </td>
      </tr>
</table>
<p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
 </p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_compania -->

<!-- START BLOCK : captura -->
<script type="text/javascript" language="JavaScript">
	function calcular_total() {
		var imp = parseFloat(document.form.imp_sin_iva.value);
		var iva = parseFloat(document.form.porciento_iva.value);
		var ret_isr = parseFloat(document.form.porciento_ret_isr.value);
		var ret_iva = parseFloat(document.form.porciento_ret_iva.value);
		
		var imp_iva;
		var imp_ret_isr;
		var imp_ret_iva;
		
		if (imp >= 0) {
			if (iva >= 0)
				imp_iva = imp * (iva / 100);
			else
				imp_iva = 0;
			
			if (ret_isr >= 0)
				imp_ret_isr = imp * (ret_isr / 100);
			else
				imp_ret_isr = 0;
			
			if (ret_iva >= 0)
				imp_ret_iva = imp * (ret_iva / 100);
			else
				imp_ret_iva = 0;
			
			// Calcular total
			total = imp + imp_iva - imp_ret_isr - imp_ret_iva;
			
			document.form.imp_sin_iva.value = imp.toFixed(2);
			document.form.porciento_iva.value = iva.toFixed(2);
			document.form.importe_iva.value = imp_iva.toFixed(2);
			document.form.porciento_ret_isr.value = ret_isr.toFixed(2);
			document.form.importe_ret_isr.value = imp_ret_isr.toFixed(2);
			document.form.porciento_ret_iva.value = ret_iva.toFixed(2);
			document.form.importe_ret_iva.value = imp_ret_iva.toFixed(2);
			document.form.importe_total.value = total.toFixed(2);
			return true;
		}
		else if (imp.value == "") {
			document.form.imp_sin_iva.value = '0.00';
			document.form.porciento_iva.value = '0.00';
			document.form.importe_iva.value = '0.00';
			document.form.porciento_ret_isr.value = '0.00';
			document.form.importe_ret_isr.value = '0.00';
			document.form.porciento_ret_iva.value = '0.00';
			document.form.importe_ret_iva.value = '0.00';
			document.form.importe_total.value = '0.00';
			return false;
		}
	}
	
	function valida_registro() {
		if (document.form.imp_sin_iva.value <= 0) {
			alert("Debe especificar el importe del documento");
			document.form.imp_sin_iva.select();
			return false;
		}
		else if (document.form.num_fact.value <= 0) {
			alert("Debe especificar el número de documento");
			document.form.num_fact.select();
			return false;
		}
		else if (document.form.codgastos.value <= 0) {
			alert("Debe especificar el número de gasto");
			document.form.codgastos.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				return false;
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			return false;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Captura de Facturas para Proveedores Varios </p>
<form name="form" method="post" action="./fac_fpv_cap.php?tabla={tabla}">
<input name="temp" type="hidden">
<table class="tabla">
      <tr>
        <th class="vtabla">N&uacute;mero compa&ntilde;&iacute;a</th>
        <td class="vtabla">
 		  <input name="num_cia" type="hidden" value="{num_cia}">{num_cia} - {nombre_corto}
        </td>
      </tr>
      <tr>
        <th class="vtabla">N&uacute;mero proveedor </th>
        <td class="vtabla">
          <input name="num_proveedor" type="hidden" value="{num_proveedor}">{num_proveedor} - {nombre}
        </td>
      </tr>
      <tr>
        <th class="vtabla">Fecha movimiento</th>
        <td class="vtabla"><input name="fecha_mov" type="hidden" id="fecha_mov" value="{fecha_mov}">
          {fecha_mov}
	</tr>
      <tr>
        <th class="vtabla">Fecha de vencimiento</th>
	<td class="vtabla">
        <input name="fecha_ven" type="hidden" id="fecha_ven" value="{fecha_ven}">{fecha_ven}
      </td>
      </tr>
      <tr>
        <th class="vtabla">N&uacute;m. documento (factura)</th>
        <td class="vtabla">
          <input name="num_fact" type="text" class="vinsert" id="num_fact" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.concepto.select();
else if (event.keyCode == 38) form.codgastos.select();" value="{num_fact}" size="10" maxlength="10">
        </td>
      </tr>
      <tr>
        <th class="vtabla">Tipo</th>
        <td class="vtabla">
          <select name="tipo_factura" class="insert" id="tipo_factura" onFocus="temp_iva.value = form.porciento_ret_iva.value; temp_isr.value = form.porciento_ret_isr.value;" onChange="if(this.value == 0 ) {form.porciento_ret_iva.value = form.temp_iva.value; form.porciento_ret_isr.value = form.temp_isr.value;} else {form.porciento_ret_iva.value=10; form.porciento_ret_isr.value=10;}">
            <option value="0" {0}>0 - Factura</option>
            <option value="1" {1}>1 - Recibo Honorario</option>
            <option value="2" {2}>2 - Recibo Renta</option>
			<option value="3" {3}>3 - Otros</option>
          </select>
		</td>
      </tr>
      <tr>
        <th class="vtabla">Concepto</th>
        <td class="vtabla">
          <input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.imp_sin_iva.select();
else if (event.keyCode == 38) form.num_fact.select();" value="{concepto}" size="30" maxlength="30">
        </td>
      </tr>
      <tr>
        <th class="vtabla">Importe sin I.V.A.</th>
        <td class="vtabla">
          <input name="imp_sin_iva" type="text" class="vinsert" id="imp_sin_iva" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp))
calcular_total();" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.porciento_iva.select();
else if (event.keyCode == 38) form.concepto.select();" value="{imp_sin_iva}" size="12" maxlength="12">
        </td>
      </tr>
      <tr>
        <th class="vtabla">Porcentaje de I.V.A.</th>
        <td class="vtabla">
          <input name="porciento_iva" type="text" class="vinsert" id="porciento_iva" onFocus="form.temp.value=this.value" onChange="if ((parseFloat(this.value) &gt;= 0 &amp;&amp; parseFloat(this.value) &lt;= 100) || this.value == '')
calcular_total();
else this.value=form.temp.value;" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.porciento_ret_isr.select();
else if (event.keyCode == 38) form.imp_sin_iva.select();" value="{porciento_iva}" size="5" maxlength="5">
        </td>
      </tr>
	  <tr>
        <th class="vtabla">Importe del I.V.A.</th>
        <td class="vtabla">
          <input name="importe_iva" type="text" class="vtotal" id="importe_iva" value="{importe_iva}" size="12" maxlength="12" readonly>
        </td>
      </tr>
      <tr>
        <th class="vtabla">Porcentaje de Retenci&oacute;n I.S.R.</th>
        <td class="vtabla">
          <input name="porciento_ret_isr" type="text" class="vinsert" id="porciento_ret_isr" onFocus="form.temp.value=this.value" onChange="if ((parseFloat(this.value) &gt;= 0 &amp;&amp; parseFloat(this.value) &lt;= 100) || this.value == '')
calcular_total();
else this.value=form.temp.value;" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.porciento_ret_iva.select();
else if (event.keyCode == 38) form.porciento_iva.select();" value="{porciento_ret_isr}" size="5" maxlength="5">
</td>
      </tr>
      <tr>
        <th class="vtabla">Importe de Retenci&oacute;n I.S.R.</th>       
        <td class="vtabla"><input name="importe_ret_isr" type="text" class="vtotal" id="importe_ret_isr" value="{importe_ret_isr}" size="12" maxlength="12" readonly></td>
      </tr>
	  <tr>
        <th class="vtabla">Porcentaje de Retenci&oacute;n I.V.A.</th>
        <td class="vtabla">
          <input name="porciento_ret_iva" type="text" class="vinsert" id="porciento_ret_iva" onFocus="form.temp.value=this.value" onChange="if ((parseFloat(this.value) &gt;= 0 &amp;&amp; parseFloat(this.value) &lt;= 100) || this.value == '')
calcular_total();
else this.value=form.temp.value;" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.codgastos.select();
else if (event.keyCode == 38) form.porciento_ret_isr.select();" value="{porciento_ret_iva}" size="5" maxlength="5">
        </td>
      </tr>
	  <tr>
	    <th class="vtabla">Importe de Retenci&oacute;n I.V.A.</th>       
	    <td class="vtabla"><input name="importe_ret_iva" type="text" class="vtotal" id="importe_ret_iva" value="{importe_ret_iva}" size="12" maxlength="12" readonly></td>
    </tr>
	  <tr>
        <th class="vtabla">Total</th>
        <td class="vtabla">
          <input name="importe_total" type="text" class="vtotal" id="importe_total" value="{importe_total}" size="12" maxlength="12" readonly>
        </td>
      </tr>
	  <tr>
        <th class="vtabla">C&oacute;digo gasto</th>
        <td class="vtabla">
          <input name="codgastos" type="text" class="insert" id="codgastos" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13) form.enviar.focus();
else if (event.keyCode == 40) form.num_fact.select();
else if (event.keyCode == 38) form.porciento_ret_iva.select();" value="{codgastos}" size="4" maxlength="4">
        </td>
      </tr>
      	
  </table>
	<p>  <input type="button" class="boton" value="<< Regresar" onclick="document.location = './fac_fpv_cap.php'">
  &nbsp;&nbsp;&nbsp;&nbsp;
  <input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  </p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_fact.select();
</script>

</td>
</tr>
</table>

<!-- END BLOCK : captura -->