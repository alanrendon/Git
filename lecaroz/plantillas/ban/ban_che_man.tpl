<script type="text/javascript" language="JavaScript">
	// Validar y actualizar número y nombre de compañía
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las compañías
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	// Validar y actualizar número y nombre del proveedor
	function actualiza_proveedor(num_pro, nombre) {
		// Arreglo con los nombres de los proveedores
		pro = new Array();
		<!-- START BLOCK : nombre_pro -->
		pro[{num_pro}] = '{nombre_pro}';
		<!-- END BLOCK : nombre_pro -->
		
		if (parseInt(num_pro.value) > 0) {
			if (pro[parseInt(num_pro.value)] == null) {
				alert("Proveedor "+parseInt(num_pro.value)+" no esta en el catálogo de proveedores");
				num_pro.value = "";
				nombre.value  = "";
				num_pro.focus();
				return false;
			}
			else {
				num_pro.value = parseFloat(num_pro.value);
				nombre.value  = pro[parseInt(num_pro.value)];
				return;
			}
		}
		else if (num_pro.value == "") {
			num_pro.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	// Validar y actualizar código de gasto
	function actualiza_gasto(codgasto, nombre) {
		// Arreglo con los nombres de los gastos
		gas = new Array();
		<!-- START BLOCK : nombre_gasto -->
		gas[{codgasto}] = '{nombre_gasto}';
		<!-- END BLOCK : nombre_gasto -->
		
		if (parseInt(codgasto.value) > 0) {
			if (gas[parseInt(codgasto.value)] == null) {
				alert("Código "+parseInt(codgasto.value)+" no esta en el catálogo de gastos");
				codgasto.value = "";
				nombre.value  = "";
				codgasto.focus();
				return false;
			}
			else {
				codgasto.value = parseFloat(codgasto.value);
				nombre.value  = gas[parseInt(codgasto.value)];
				return;
			}
		}
		else if (codgasto.value == "") {
			codgasto.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
//-----------
	function actualiza_saldo(numcia,saldo_campo) {
		// Arreglo con los nombres de los gastos
		saldo = new Array();
		var numero=0;
		<!-- START BLOCK : importe_saldo -->
		saldo[{num_cia}] = '{saldo}';
		<!-- END BLOCK : importe_saldo -->
		
		if (parseInt(numcia.value) > 0) {
			if (saldo[parseInt(numcia.value)] == null) {
				alert("Código "+parseInt(numcia.value)+" no tiene saldo");
//				numcia.value = "";
				saldo_campo.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				numcia.value = parseFloat(numcia.value);
//				if(parseFloat(saldo[parseFloat(numcia.value)]) < 0)
//						document.form.saldo.style="color:#FF0000";
				numero=parseFloat(saldo[parseFloat(numcia.value)]);
				saldo_campo.value  = numero.toFixed(2);
				return;
			}
		}
		else if (numcia.value == "") {
			numcia.value = "";
			saldo_campo.value  = "";
			return false;
		}
	}

//-------------


	function calcula_total() {
		var importe1 = parseFloat(document.form.importe1.value);
		var importe2 = parseFloat(document.form.importe2.value);
		var importe3 = parseFloat(document.form.importe3.value);
		var iva1     = parseFloat(document.form.iva1.value);
		var iva2     = parseFloat(document.form.iva2.value);
		var iva3     = parseFloat(document.form.iva3.value);
		var total1   = 0;
		var total2   = 0;
		var total3   = 0;
		var importe_cheque = 0;
		
		if (isNaN(importe1)) importe1 = 0;
		if (isNaN(importe2)) importe2 = 0;
		if (isNaN(importe3)) importe3 = 0;
		
		total1 = importe1 + importe1 * (iva1 / 100);
		total2 = importe2 + importe2 * (iva2 / 100);
		total3 = importe3 + importe3 * (iva3 / 100);
		
		importe_cheque = total1 + total2 + total3;
		
		document.form.total1.value = total1.toFixed(2);
		document.form.total2.value = total2.toFixed(2);
		document.form.total3.value = total3.toFixed(2);
		
		document.form.importe.value = importe_cheque.toFixed(2);
	}
	
	function valida_registro() {
		if (document.form.fecha.value == "") {
			alert("Debe especificar la fecha");
			document.form.fecha.select();
			return false;
		}
		else if (document.form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			document.form.num_cia.select();
			return false;
		}
		else if (document.form.num_proveedor.value <= 0) {
			alert("Debe especificar el proveedor");
			document.form.num_proveedor.select();
			return false;
		}
		else if (document.form.concepto.value == "") {
			alert("Debe especificar el concepto");
			document.form.concepto.select();
			return false;
		}
		else if (document.form.codgastos.value <= 0) {
			alert("Debe especificar el código del gasto");
			document.form.codgastos.select();
			return false;
		}
		/*else if (document.form.factura1.value <= 0 && document.form.factura2.value <= 0 && document.form.factura3.value <= 0) {
			alert("Debe especificar al menos una factura");
			document.form.factura1.select();
			return false;
		}*/
		else {
			/*if ((document.form.factura1.value > 0 && document.form.importe1.value <= 0) || (document.form.factura1.value <= 0 && document.form.importe1.value > 0)) {
				alert("Debe especificar número e importe para la factura 1");
				document.form.importe1.select();
				return false;
			}
			if (document.form.factura2.value > 0 && document.form.importe2.value <= 0 || (document.form.factura2.value <= 0 && document.form.importe2.value > 0)) {
				alert("Debe especificar número e importe para la factura 2");
				document.form.importe2.select();
				return false;
			}
			if (document.form.factura3.value > 0 && document.form.importe3.value <= 0 || (document.form.factura3.value <= 0 && document.form.importe3.value > 0)) {
				alert("Debe especificar número e importe para la factura 3");
				document.form.importe3.select();
				return false;
			}*/
			
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				document.form.fecha.select();
		}
	}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<!-- CAPTURA MANUAL DE CHEQUES-->
<p class="title">Captura Manual de Cheques </p>
<form name="form" action="./ban_che_man.php?tabla={tabla}" method="post">
<input name="temp" type="hidden">
  <table class="tabla">
	<tr>
      <th class="vtabla">Fecha</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
  	  <input name="fecha" type="text" class="vinsert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_cia.select();
else if (event.keyCode == 38) form.factura3.select();" value="{fecha}" size="10" maxlength="10">
  	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Saldo en libros  $</strong>  	  <input name="saldo" type="text" class="vnombre" id="saldo" value="{importe_saldo}" size="20" maxlength="20" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla">N&uacute;mero de compa&ntilde;&iacute;a</th>
      <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
		  <input name="num_cia" type="text" class="vinsert" id="num_cia" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp))
actualiza_compania(this,form.nombre_cia); actualiza_saldo(this,form.saldo);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_proveedor.select();
else if (event.keyCode == 38) form.fecha.select();" value="{num_cia}" size="3" maxlength="3">
		  <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="50" maxlength="50" readonly="true">		  </td>
    </tr>
 
    <tr>
      <th class="vtabla">C&oacute;digo del proveedor</th>
      <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<input name="num_proveedor" type="text" class="vinsert" id="num_proveedor" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp))
actualiza_proveedor(this,form.a_nombre);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.concepto.select();
else if (event.keyCode == 38) form.num_cia.select();" value="{num_proveedor}" size="4" maxlength="4">
	  	<input name="a_nombre" type="text" class="vnombre" id="a_nombre2" value="{nombre_proveedor}" size="50" maxlength="50" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla">Concepto</th>
      <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.codgastos.select();
else if (event.keyCode == 38) form.num_proveedor.select();" value="{concepto}" size="50" maxlength="50"></td>
    </tr>
    <tr>
      <th class="vtabla">C&oacute;digo del gasto</th>
      <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <input name="codgastos" type="text" class="vinsert" id="codgastos" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp))
actualiza_gasto(this,form.nombre_gasto);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.factura1.select();
else if (event.keyCode == 38) form.concepto.select();" value="{codgastos}" size="3" maxlength="3">
        <input name="nombre_gasto" type="text" class="vnombre" id="nombre_gasto" value="{nombre_gasto}" size="50" maxlength="50" readonly="true">
</td>
    </tr>
    <tr>
      <th class="vtabla">Factura 1 </th>
      <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  No. Factura
	  <input name="factura1" type="text" class="vinsert" id="factura1" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.importe1.select();
else if (event.keyCode == 38) form.codgastos.select();
else if (event.keyCode == 40) form.factura2.select();" value="{factura1}" size="10" maxlength="10"> 
	  Importe
	  <input name="importe1" type="text" class="rinsert" id="importe1" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_total()" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.factura2.select();
else if (event.keyCode == 37) form.factura1.select();
else if (event.keyCode == 38) form.codgastos.select();
else if (event.keyCode == 40) form.importe2.select();" value="{importe1}" size="10" maxlength="10"> 
	  IVA
	  <select name="iva1" class="insert" id="iva1" onChange="calcula_total()">
	    <option value="0" {iva1_0}>0%</option>
	    <option value="15" {iva1_15}>15%</option>
	    </select> 
	  <b>Total</b>
	  <input name="total1" type="text" class="rnombre" id="total1" value="{total1}" size="10" maxlength="10" readonly="true"></td>
    </tr><tr>
      <th class="vtabla">Factura 2 </th>
      <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  No. Factura
	  <input name="factura2" type="text" class="vinsert" id="factura2" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.importe2.select();
else if (event.keyCode == 38) form.factura1.select();
else if (event.keyCode == 40) form.factura3.select();" value="{factura2}" size="10" maxlength="10"> 
	  Importe
	  <input name="importe2" type="text" class="rinsert" id="importe2" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_total()" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.factura3.select();
else if (event.keyCode == 37) form.factura2.select();
else if (event.keyCode == 38) form.importe1.select();
else if (event.keyCode == 40) form.importe3.select();" value="{factura2}" size="10" maxlength="10"> 
	  IVA
	  <select name="iva2" class="insert" id="iva2" onChange="calcula_total()">
	    <option value="0" {iva2_0}>0%</option>
	    <option value="15" {iva2_15}>15%</option>
	    </select> 
	  <b>Total</b>
	  <input name="total2" type="text" class="rnombre" id="total2" value="{total2}" size="10" maxlength="10" readonly="true"></td>
    </tr>
	<tr>
      <th class="vtabla">Factura 3 </th>
      <td class="vtabla"  onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  No. Factura
	  <input name="factura3" type="text" class="vinsert" id="factura3" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.importe3.select();
else if (event.keyCode == 38) form.factura2.select();
else if (event.keyCode == 40) form.fecha.select();" value="{factura3}" size="10" maxlength="10"> 
	  Importe
	  <input name="importe3" type="text" class="rinsert" id="importe3" onFocus="form.temp.value=this.value" onChange="if (isFloat(this,2,form.temp)) calcula_total()" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 40) form.fecha.select();
else if (event.keyCode == 37) form.factura3.select();
else if (event.keyCode == 38) form.importe2.select();" value="{factura3}" size="10" maxlength="10"> 
	  IVA
	  <select name="iva3" class="insert" id="iva3" onChange="calcula_total()">
	    <option value="0" {iva3_0}>0%</option>
	    <option value="15" {iva3_15}>15%</option>
	    </select> 
	  <b>Total</b>
	  <input name="total3" type="text" class="rnombre" id="total3" value="{total3}" size="10" maxlength="10" readonly="true"></td>
    </tr>
        <th class="vtabla"><font size="+1">Total del cheque </font></th>
          <th class="rtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
	  	<input name="importe" type="text" class="rnombre" id="importe" value="{importe}" size="12" maxlength="12" readonly="true"></th>
    </tr>
  </table>
  <p><img src="./menus/insert.gif" align="middle">&nbsp;
    <input type="button" class="boton"  name="enviar" value="Capturar cheque" onclick='valida_registro()'>
</p>
</form></td>
</tr>
</table>