<!-- START BLOCK : obtener_datos -->
<script language="javascript" type="text/javascript">
function valida(){
	if (document.form.num_cia.value<=0 || document.form.num_cia.value=="" || document.form.num_fac.value=="")
	{
		alert("Verifique los campos por favor");
		document.form.num_cia.select();		
	}
	else document.form.submit();
}


</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de facturas de materia prima</P>
<form name="form" action="./fac_fac_ad.php" method="get" onKeyDown="if (event.keyCode == 13) form.enviar.focus();">
  <input name="temp" type="hidden" id="temp" />
  <table class="tabla">
    <tr>
      <th class="vtabla">Compañía</th>
      <td class="vtabla">
        <input class="insert" name="num_cia" type="text" id="num_cia" size="10" maxlength="10" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) document.form.proveedor.select();">
      </td>
      <th class="vtabla">Proveedor</th>
      <td class="vtabla"><input class="insert" name="proveedor" type="text" id="proveedor" size="10" maxlength="10" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) document.form.num_fac.select();"></td>
      <th class="vtabla">N&uacute;mero de factura</th>
      <td class="vtabla"><input class="insert" name="num_fac" type="text" id="num_fac" size="15" maxlength="15" onKeyDown="if (event.keyCode == 13) document.form.enviar2.focus();"></td>
    </tr>
  </table>
  <p>
    <input class="boton" name="enviar2" type="button" value="Consultar" onClick='valida();'>
  </p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : factura -->
<script language="JavaScript" type="text/JavaScript">

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
		
		//if (value_temp >= 0)
			//value_costo_total = value_costo_total - value_temp;
		
		// Calcular costo total
		//value_costo_total += value_costo_unitario;
		
		cantidad.value = value_cantidad.toFixed(2);
		costo_unitario.value = value_costo_unitario.toFixed(2);
		//costo_total.value = value_costo_total.toFixed(2);
		//return;
	}
	else if (cantidad.value == "") {
		//if (value_temp >= 0)
			//value_costo_total = value_costo_total - value_temp;
		
		costo_unitario.value = "";
		//costo_total.value = value_costo_total.toFixed(2);
		//return false;
	}
	
	calculaTotal();
}

function calculaTotal() {
	var total = 0, i, f = document.form;
	
	for (i = 0; i < get_val(f.contador); i++)
		total += !eval('f.regalo' + i + '.checked') ? get_val(eval('f.total' + i)) : 0;
	
	f.total_factura.value = total.toFixed(2);
}

function regalado(checa,temporal,cantidad,total_unitario,total_general,bandera)
{
	/*var auxiliar=0;
	
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
	}*/
	calculaTotal();
}


</script>



<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de facturas de Materia Prima </P>
<form name="form" action="./modifica_fac_fac.php" method="post">


  <table class="tabla">
    <tr>
      <th class="tabla" align="center">Compa&ntilde;&iacute;a</th>
      <th class="tabla" align="center">Proveedor</th>
      <th class="tabla" align="center">N&uacute;mero Factura </th>
      <th class="tabla" align="center">Fecha movimiento </th>
      <th class="tabla" align="center">Fecha de pago </th>
    </tr>

    <tr>
      <td class="tabla" align="center">
<font size="+1">
<strong>{numero_cia}&#8212;{nombre_cia}</strong>
</font>
<input name="num_cia" type="hidden" value="{numero_cia}">
<input name="contador" type="hidden" id="contador" value="{cont}">
<input name="temp" type="hidden" id="temp">
<input name="temp_total" type="hidden">
<input name="temporal" type="hidden" id="temporal"></td>
      <td class="tabla" align="center">

<font size="+1">
{num_proveedor}&#8212;{nom_proveedor}
<input name="num_proveedor" type="hidden" id="num_proveedor" value="{num_proveedor}">
</font>
</td>
      <td class="tabla" align="center">
	<font size="+1">
	{num_factura}<font size="+1">
    <font size="+1"><font size="+1">
    <input name="num_fac" type="hidden" id="num_fac" value="{num_factura}">
    </font></font>	</font>	</font>      
</td>
      <td class="tabla" align="center">
<font size="+1">{fecha_mov}</font> <font size="+1"><font size="+1">
<input name="fecha_mov" type="hidden" id="fecha_mov" value="{fecha_mov}">
</font></font> </td>
      <td class="tabla" align="center">
<font size="+1">
      {fecha_pago}<font size="+1"><font size="+1">
      <input name="fecha_pago" type="hidden" id="fecha_pago" value="{fecha_pago}">
      </font></font></font>	  </td>
    </tr>

  </table>
  <br>
  <table class="tabla">
    <tr>
      <th align="center" class="tabla">Cantidad</th>
      <th align="center" class="tabla" colspan="2">Materia Primas</th>
      <th align="center" class="tabla">Contenido</th>
      <th align="center" class="tabla">Unidad</th>
      <th align="center" class="tabla">Precio</th>
      <th align="center" class="tabla">Desc1</th>
      <th align="center" class="tabla">Desc 2 </th>
      <th align="center" class="tabla">Desc 3 </th>
      <th align="center" class="tabla">I.V.A.</th>
      <th align="center" class="tabla">IEPS</th>
      <th align="center" class="tabla">Total</th>
      <th align="center" class="tabla">Regalado</th>
    </tr>
    <!-- START BLOCK : rows -->
     <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
		  <td class="tabla" align="center">

		      <input name="cantidad{i}" type="text" class="insert" id="cantidad{i}" value="{cantidad}" size="10" 
			  onFocus="document.form.temp.value = this.value; document.form.temp_total.value=document.form.total{i}.value;"
			  onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); 
			  else costo_unit(this,form.precio{i},form.desc1{i},form.desc2{i},form.desc3{i},form.iva{i},form.ieps{i},form.total{i},form.total_factura,form.temp_total);"
			  onKeyDown="if(event.keyCode==13) document.form.ieps{i}.select();">
	        
			  <input name="cantidad_ant{i}" type="hidden" value="{cantidad}">
			</td>

		  <td class="rtabla" align="left">
			<strong>{codmp}</strong>
            <input name="codmp{i}" type="hidden" id="codmp{i}" value="{codmp}">
            </td>

		  <td class="vtabla" align="left">
			<strong>{nom_mp}</strong>
			</td>
		  <td class="tabla" align="center">
			 {contenido} 
               <input name="contenido{i}" type="hidden" id="contenido{i}" value="{contenido}">
            </td>
		  <td class="tabla" align="center">
			{unidad}
		      <input name="unidad{i}" type="hidden" id="unidad{i}" value="{unidad}">
	        </td>
		  <td class="rtabla" align="center">{precio}
		    <input name="precio{i}" type="hidden" id="precio{i}" value="{precio}">
		  </td>
          <td class="rtabla" align="center">{desc1}
            <input name="desc1{i}" type="hidden" class="insert" id="desc1{i}" value="{desc1}" size="10" >
          </td>
          <td class="rtabla" align="center">{desc2}
            <input name="desc2{i}" type="hidden" class="insert" id="desc2{i}" value="{desc2}" size="10">
          </font></font></span></td>
          <td class="rtabla" align="center">{desc3}
            <input name="desc3{i}" type="hidden"class="insert" id="desc3{i}" value="{desc3}" size="10">
          </td>
          <td class="rtabla" align="center">{iva}
            <input name="iva{i}" type="hidden" id="iva{i}" value="{iva}" class="insert">
		</td>
          <td class="rtabla" align="center">
            <input name="ieps{i}" type="text" class="insert" id="ieps{i}" value="{ieps}" size="5" maxlength="5" onKeyDown="if(event.keyCode==13) document.form.cantidad{next}.select();">
          </td>
          <td class="rtabla" align="center">
            <input name="total{i}" type="rtext" id="total{i}" value="{total}" class="nombre" readonly>
          </td>
          <td class="rtabla" align="center">
		  <input name="regalo{i}" type="checkbox" id="regalo{i}" onChange="regalado(this,form.temporal,form.cantidad{i},form.total{i},form.total_factura,form.bandera{i})" value="0" {che}> 
		  <input name="bandera{i}" type="hidden" id="bandera{i}" value="{bandera}" size="5"></td>
     </tr>
    <!-- END BLOCK : rows -->

<!-- START BLOCK : totales -->
	
  <tr>
  <th class="tabla" colspan="11" align="center"><b>Total</b></th>
	  <th class="rtabla" align="center"><strong><font size="3">
	    <input name="total_factura" id="total_factura" value="{total_factura}" class="nombre" readonly>
	  </font></strong></th>
	 </tr>
<!-- END BLOCK : totales -->           
  </table>
  <br>
  <input type="button" name="enviar" class="boton" value="Regresar" onclick='parent.history.back()'>
  <input name="modificar" type="button" class="boton" id="modificar" onclick="if(confirm('¿Estas segura de modificar esta factura?')) document.form.submit(); else return false;" value="MODIFICAR FACTURA">
  <br>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : factura -->