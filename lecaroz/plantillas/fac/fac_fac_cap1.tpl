<script type="text/javascript" language="JavaScript">
	function costo_unit(cantidad, precio, desc1, desc2, desc3, iva, ieps, costo_unitario, costo_total, temp) {
		var value_cantidad = parseFloat(cantidad.value);
		var value_precio   = parseFloat(precio.value);
		var value_desc1    = parseFloat(desc1.value);
		var value_desc2    = parseFloat(desc2.value);
		var value_desc3    = parseFloat(desc3.value);
		var value_iva      = parseFloat(iva.value);
		var value_ieps     = parseFloat(ieps.value);
		var value_costo_unitario = parseFloat(costo_unitario.value);
		var value_costo_total    = parseFloat(costo_total.value);
		var value_temp     = parseFloat(temp.value);
		
		if (value_cantidad > 0) {
			value_costo_unitario = value_cantidad * value_precio;
			
			if(value_desc1 > 0) {					
				value_costo_unitario = value_costo_unitario * (1 - (value_desc1 / 100));
			}
			if(value_desc2 > 0) {					
				value_costo_unitario = value_costo_unitario * (1 - (value_desc2 / 100));
			}
			if(value_desc3 > 0) {					
				value_costo_unitario = value_costo_unitario * (1 - (value_desc3 / 100));
			}
			if (value_iva > 0) {
				value_costo_unitario = value_costo_unitario * (1 + (value_iva / 100));
			}
			if (value_ieps > 0) {
				value_costo_unitario = value_costo_unitario * (1 + (value_ieps / 100));
			}
			
			if (value_temp >= 0)
				value_costo_total = value_costo_total - value_temp;
			
			// Calcular costo total
			value_costo_total += value_costo_unitario;
			
			cantidad.value = value_cantidad.toFixed(2);
			costo_unitario.value = value_costo_unitario.toFixed(2);
			costo_total.value = value_costo_total.toFixed(2);
			return;
		}
		else if (cantidad.value == "") {
			if (value_temp >= 0)
				value_costo_total = value_costo_total - value_temp;
			
			costo_unitario.value = "";
			costo_total.value = value_costo_total.toFixed(2);
			return false;
		}
	}
	
	function valida_registro() {
		//if ()
		
		if (document.form.totalf.value <= document.form.costo_total.value) {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.num_cia.select();
		}
		else {
			if (confirm("El total de la factura no coincide con el total calculado.\n¿Desea cambiar el total de la factura?")) {
				var temp = prompt("Total de factura : "+document.form.totalf.value+"\nTotal calculado  : "+document.form.costo_total.value+"\n\nEscriba el nuevo total de la factura","");
				if (parseFloat(temp) > 0) {
					value_total_fac = parseFloat(temp);
					document.form.totalf.value = value_total_fac.toFixed(2);
				}
			}
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
	
	function error(valor_campo, valor_anterior) {
		valor_campo.value = valor_anterior.value;
		alert("No se permiten valores negativos o caractéres");
		valor_campo.select();
		return false;
	}

//Parámetros:
//regalado(this,form.temporal,form.cantidad{i},form.costo_unitario{i},form.costo_total)
function regalado(checa,temporal,cantidad,total_unitario,total_general,bandera)
{
	var auxiliar=0;
	
	if(cantidad.value=="" || parseFloat(cantidad.value) <=0 )
		return;
	
	temporal.value=parseFloat(total_unitario.value);
	
	if(checa.checked==true)
	{
		auxiliar = parseFloat(total_general.value) - parseFloat(temporal.value);
		total_general.value = auxiliar.toFixed(2);
		bandera.value=1;
	}
	else if(checa.checked==false)
	{
		auxiliar = parseFloat(total_general.value) + parseFloat(temporal.value);
		total_general.value = auxiliar.toFixed(2);
		bandera.value=0;
	}
}
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura de Facturas</p>
<form name="form" method="post" action="./insert_fac_fac_cap.php?tabla={tabla}">
<input name="temp" type="hidden">
<input name="temp_total" type="hidden">
<input name="temporal" type="hidden" id="temporal">
<table class="tabla">
  <tr>
    <th class="tabla"scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Proveedor</th>
    <th class="tabla" scope="col">Documento</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col"><p>Total de factura</p>      </th>
  </tr>
  <tr>
    <td class="tabla">
      <input name="num_cia" type="hidden" value="{num_cia}">
      <font size="+1">{num_cia} - {nombre_corto}</font></td>
    <td class="tabla">
      <input name="num_proveedor" type="hidden" value="{num_proveedor}">
	  <font size="+1">{num_proveedor} - {nombre}</font></td>
    <td class="tabla">
      <input name="num_documento" type="hidden" value="{num_documento}">
      <font size="+1">{num_documento}</font></td>
    <td class="tabla">
      <input name="fecha" type="hidden" value="{fecha}">
      <font size="+1">{fecha}</font></td>
    <td class="tabla"><input name="totalf" type="text" class="nombre" disabled="true" id="totalf" value="{totalf}" size="12" maxlength="12"></td>
  </tr>
</table>
<br>
  <table class="tabla">
    <tr>
      <th class="tabla" align="center">Cantidad</th>
      <th class="tabla" align="center">C&oacute;digo</th>
      <th class="tabla" align="center">Descripci&oacute;n</th>
      <th class="tabla" align="center">Contenido</th>
      <th class="tabla" align="center">Unidad</th>
      <th class="tabla" align="center">Precio</th>
      <th class="tabla" align="center">Desc 1</th>
      <th class="tabla" align="center">Desc 2</th>
      <th class="tabla" align="center">Desc 3</th>
      <th class="tabla" align="center">I.V.A.</th>
      <th class="tabla" align="center">IEPS</th>
      <th class="tabla" align="center">Total por producto</th>
      <th class="tabla" align="center">Regalo</th>
    </tr>
    <!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">
        <input name="cantidad{i}" type="text" class="insert" onFocus="form.temp.value=this.value; form.temp_total.value=form.costo_unitario{i}.value;" 
		onChange="if (parseFloat(this.value) >= 0 || this.value == '')
costo_unit(this,form.precio{i},form.desc1{i},form.desc2{i},form.desc3{i},form.iva{i},form.ieps{i},form.costo_unitario{i},form.costo_total,form.temp_total);
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.ieps{i}.select();
else if (event.keyCode == 38) form.cantidad{back}.select();
else if (event.keyCode == 39) form.ieps{i}.select();
else if (event.keyCode == 37) form.ieps{back}.select();" size="6" maxlength="6">
      </td>
      <td class="vtabla">
        <input name="codmp{i}" type="hidden" value="{codmp}">{codmp}
      </td>
      <td class="vtabla">
        <input name="nombre" type="hidden" value="{nombre}">{nombre}
      </td>
      <td class="tabla">
        <input name="contenido{i}" type="hidden" value="{contenido}">
      {contenido}</td>
      <td class="tabla">{unidad}</td>
      <td  class="tabla">
        <input name="precio{i}" type="hidden" value="{precio}">
        {fprecio}
      </td>
      <td class="tabla">
        <input name="desc1{i}" type="hidden" value="{desc1}">        {fdesc1}
      </td>
      <td class="tabla">
        <input name="desc2{i}" type="hidden" value="{desc2}">
        {fdesc2}
      </td>
      <td class="tabla">
        <input name="desc3{i}" type="hidden" value="{desc3}">
        {fdesc3}
      </td>
      <td class="tabla">
        <input name="iva{i}" type="hidden" value="{iva}">
        {fiva}
      </td>
      <td class="tabla">
      <input name="ieps{i}" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="if ((parseFloat(this.value) >= 0 && parseFloat(this.value) <= 10) || this.value == '')
costo_unit(form.cantidad{i},form.precio{i},form.desc1{i},form.desc2{i},form.desc3{i},form.iva{i},this,form.costo_unitario{i},form.costo_total,form.temp_total);
else error(this,form.temp);" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.cantidad{next}.select();
else if (event.keyCode == 38) form.ieps{back}.select();
else if (event.keyCode == 40) form.ieps{next}.select();
else if (event.keyCode == 37) form.cantidad{i}.select();" value="{ieps}" size="3" maxlength="2">
      <font size="-2">(&lt;=10%)</font>
      </td>
      <th class="tabla">
        <input name="costo_unitario{i}" type="text" class="total" size="12" maxlength="12" readonly="true">
      </th>
      <td class="tabla">
	  <input name="regalo{i}" type="checkbox" id="regalo{i}" value="0" onChange="regalado(this,form.temporal,form.cantidad{i},form.costo_unitario{i},form.costo_total,form.bandera{i})">
        <input name="bandera{i}" type="hidden" id="bandera{i}" value="0" size="5"></td>
    </tr>
    <!-- END BLOCK : rows -->
    <tr>
      <th class="rtabla" colspan="11">Total</th>
      <th class="tabla"><input name="costo_total" type="text" class="total" value="0.00" size="12" maxlength="12" readonly="true"></th>
      <td class="tabla">&nbsp;</td>
    </tr>
  </table>
  <p>
  <input type="button" class="boton" value="<< Regresar" onclick='parent.history.back()'>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <img src="./menus/delete.gif" align="middle">&nbsp;<input type="button" class="boton" value="Borrar" onclick='borrar()'>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <img src="./menus/insert.gif" align="middle">&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
  </p>
</form>
<script language="JavaScript" type="text/JavaScript">window.onload=document.form.cantidad0.select();</script>

</td>
</tr>
</table>