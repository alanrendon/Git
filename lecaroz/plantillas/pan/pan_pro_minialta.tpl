<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		window.opener.document.form.reset();
		window.opener.document.form.method = "post";
		window.opener.document.form.target = "_self";
		window.opener.document.form.action = "./hojadiaria.php?tabla=produccion";
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : alta -->
<script language="javascript" type="text/javascript">
	function actualiza_producto(num_pro, nombre) {
		pro = new Array();
		<!-- START BLOCK : nombre_pro -->
		pro[{num_pro}] = '{nombre_pro}';
		<!-- END BLOCK : nombre_pro -->
		
		if (parseInt(num_pro.value) > 0) {
			if (pro[parseInt(num_pro.value)] == null) {
				alert("El código "+parseInt(num_pro.value)+" no esta en el catálogo de productos");
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
	
	function valida_registro() {
		if (document.form.precio_venta.value < 0) {
			alert("Debe especificar el precio de venta");
			document.form.precio_venta.select();
			return false;
		}
		else if (document.form.precio_raya.value < 0 && document.form.porc_raya.value < 0) {
			alert("Debe especificar el precio de raya o el porcentaje de raya");
			document.form.precio_raya.select();
			return false;
		}
		else
			document.form.submit();
	}
</script>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Alta de  producto</p>
<form name="form" method="post" action="./pan_pro_minialta.php?alta=1">
<input name="temp" type="hidden">
<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
<input name="cod_turno" type="hidden" id="cod_turno" value="{cod_turno}">
<table class="tabla">
   <tr>
     <th class="vtabla">Compa&ntilde;&iacute;a</th>
     <th class="vtabla">{num_cia} - {nombre_cia} </th>
   </tr>
   <tr>
     <th class="vtabla">Turno</th>
     <th class="vtabla">{cod_turno} - {nombre_turno} </th>
   </tr>
   <tr>
     <th class="vtabla">Producto</th>
     <td class="vtabla"><input name="cod_producto" type="text" class="insert" id="cod_producto" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp)) actualiza_producto(this, form.nombre_producto)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_orden.select();
else if (event.keyCode == 38) form.precio_venta.select();" size="5" maxlength="5">
       <input name="nombre_producto" type="text" disabled="true" class="vnombre" id="nombre_producto" size="50" maxlength="50"></td>
   </tr>
   <tr>
      <th class="vtabla">N&uacute;mero de orden </th>
      <td class="vtabla"><input name="num_orden" type="text" class="insert" id="num_orden" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.precio_raya.select();
else if (event.keyCode == 38) form.cod_producto.select();" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla">Precio de raya por unidad producida </th>
      <td class="vtabla"><input name="precio_raya" type="text" class="rinsert" id="precio_raya_unidad" onFocus="form.temp.value=this.value" onChange="isFloat(this,4,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.porc_raya.select();
else if (event.keyCode == 38) form.num_orden.select();" size="10" maxlength="6"></td>
    </tr>
    <tr>
      <th class="vtabla">Porcentaje de raya sobre la produccion </th>
      <td class="vtabla"><input name="porc_raya" type="text" class="rinsert" id="porc_raya_produccion" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.precio_venta.select();
else if (event.keyCode == 38) form.precio_raya.select();" size="10" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla">Precio de venta por unidad </th>
      <td class="vtabla"><input name="precio_venta" type="text" class="rinsert" id="precio_venta_unidad" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13) form.enviar.focus();
else if (event.keyCode == 40) form.cod_producto.select();
else if (event.keyCode == 38) form.porc_raya.select();" size="10" maxlength="5"></td>
    </tr>
  </table>
<p>
  <input type="button" class="boton" value="Cerrar ventana" onClick="self.close()">
&nbsp;&nbsp;&nbsp;
<input name="enviar" type="button" class="boton" id="enviar" value="Modificar" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : alta -->