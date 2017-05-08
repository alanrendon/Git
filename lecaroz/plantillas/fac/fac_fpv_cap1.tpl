<!-- tabla facturas --><script type="text/javascript" language="JavaScript">

	function imp_iva(imp_sin_iva, porciento_iva, importe_iva) {
		var value;
		value = parseFloat(imp_sin_iva.value) * (parseFloat(porciento_iva.value)/100);
		importe_iva.value = value.toFixed(2);
	}
	
	function imp_total(imp_sin_iva, importe_total) {
		var value;
		value = parseFloat(imp_sin_iva.value) * 1.15;
		importe_total.value = value.toFixed(2);
	}
	
	function valida_registro() {
		if(document.form.codgastos.value <= 0) {
			alert('Debe especificar un c�igo de gasto');
			document.form.codgastos.select();
		}
		else if(document.form.concepto.value <= 0) {
			alert('Debe especificar el concepto');
			document.form.concepto.select();
		}
		else		
		{
			if (confirm("Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.num_cia.select();
		}
	}

	function borrar() {
		if (confirm("Desea borrar el formulario?")) {
			document.form.reset();
			document.form.num_cia.select();
		}
		else
			document.form.num_cia.select();
	}
	
</script>
<input type="hidden" value="{diascredito}">

<form name="form" method="post" action="./insert_fac_fpv_cap.php?tabla={tabla}" onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();">
<input type="hidden" name="temp_iva">
<input type="hidden" name="temp_isr">
<table class="tabla">
      <tr>
        <th class="vtabla"><font size="+1">N&uacute;mero compa&ntilde;&iacute;a</font></td>
        <td class="tabla">
 		  <input name="num_cia" type="hidden" value="{num_cia}">{num_cia} - {nombre_corto}
        </td>
      </tr>
      <tr>
        <th class="vtabla"><font size="+1">N&uacute;mero proveedor</font> </td>
        <td class="tabla">
          <input name="num_proveedor" type="hidden" value="{num_proveedor}">{num_proveedor} - {nombre}
        </td>
      </tr>
      <tr>
        <th class="vtabla"><font size="+1">Fecha movimiento</font></td>
        <td class="tabla">{fecha_mov}
          <input name="fecha_mov" type="hidden" value="{fecha_mov}">
	</tr>
      <tr>
        <th class="vtabla"><font size="+1">Fecha de vencimiento</font></th>
	<td class="tabla">
        {fecha_ven}
      </td>
          <input name="fecha_ven" type="hidden" value="{fecha_ven}"></td>
      </tr>
      <tr>
        <th class="vtabla"><font size="+1">N&uacute;m. documento (factura)</font></td>
        <td class="tabla">
          <input name="num_remi" type="text" class="insert" id="num_remi">
        </td>
      </tr>
      <tr>
        <th class="vtabla"><font size="+1">Tipo</font></td>
        <td class="tabla">
          <select name="tipo" class="insert" id="tipo" onFocus="temp_iva.value = form.porciento_ret_iva.value; temp_isr.value = form.porciento_ret_isr.value;" onChange="if(this.value == 0 ) {form.porciento_ret_iva.value = form.temp_iva.value; form.porciento_ret_isr.value = form.temp_isr.value;} else {form.porciento_ret_iva.value=10; form.porciento_ret_isr.value=10;}">
            <option value="0" selected>0 - Factura</option>
            <option value="1">1 - Recibo Honorario</option>
            <option value="2">2 - Recibo Renta</option>
			<option value="3">3 - Otros</option>
          </select>
		</td>
      </tr>
      <tr>
        <th class="vtabla"><font size="+1">Concepto</font></td>
        <td class="tabla">
          <input name="concepto" type="text" class="insert" id="concepto" value="" size="50">
        </td>
      </tr>
      <tr>
        <th class="vtabla"><font size="+1">Importe sin I.V.A. </font></td>
        <td class="tabla">
          <input name="imp_sin_iva" type="text" class="insert" id="imp_sin_iva" maxlength="20" onChange="if (parseFloat(this.value) > 0) {form.importe_iva{importe_iva} = imp_iva(this,porciento_iva,importe_iva); form.importe_total{importe_total} = imp_total(this, importe_total); } else error(this);" onKeyDown="if (event.keyCode == 9) form.codgastos.focus()">
        </td>
      </tr>
      <tr>
        <th class="vtabla"><font size="+1">Porcentaje de I.V.A.</font></td>
        <td class="tabla">
          <input name="porciento_iva" type="text" class="insert" id="porciento_iva" value="15" maxlength="2" readonly=>
        </td>
      </tr>
	  <tr>
        <th class="vtabla"><font size="+1">Importe del I.V.A.</font></td>
        <td class="tabla">
          <input name="importe_iva" type="text" class="insert" id="importe_iva" readonly>
        </td>
      </tr>
      <tr>
        <th class="vtabla"><font size="+1">Porcentaje de Retenci&oacute;n I.S.R.</font></td>
        <td class="tabla">
          <input name="porciento_ret_isr" type="text" class="insert" id="porciento_ret_isr" value="{porciento_ret_isr}">
</td>
      </tr>
	  <tr>
        <th class="vtabla"><font size="+1">Porcentaje de Retenci&oacute;n I.V.A.</font></td>
        <td class="tabla">
          <input name="porciento_ret_iva" type="text" class="insert" id="porciento_ret_iva" value="{porciento_ret_iva}">
        </td>
      </tr>
	  <tr>
        <th class="vtabla"><font size="+1">Total</font></td>
        <td class="tabla">
          <input name="importe_total" type="text" class="insert" id="importe_total" value="0" readonly>
        </td>
      </tr>
	  <tr>
        <th class="vtabla"><font size="+1">C&oacute;digo gasto</font></td>
        <td class="tabla">
          <input name="codgastos" type="text" class="insert" id="codgastos">
        </td>
      </tr>
      	
    </table>
	<p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  <br><br>
  <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
</form>
