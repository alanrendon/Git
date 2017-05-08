<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Factura de Materia Prima</p>
  <form action="./fac_fac_cap_v2.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia)" onKeyDown="if (event.keyCode == 13) num_proveedor.select()" size="3" maxlength="3">        
      <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" size="50" maxlength="50" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_proveedor" type="text" class="insert" id="num_proveedor" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_pro(this,nombre_pro)" onKeyDown="if (event.keyCode == 13) num_documento.select()" value="{num_pro}" size="3" maxlength="4">
        <input name="nombre_pro" type="text" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="50" maxlength="50" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">No. de documento </th>
      <td class="vtabla"><input name="num_documento" type="text" class="vinsert" id="num_documento" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha.select();" size="20" maxlength="20"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha <font size="-2">(ddmmaa)</font></th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) total_fac.select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Total del documento </th>
      <td class="vtabla"><input name="total_fac" type="text" class="rinsert" id="total_fac" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="12" maxlength="12"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_cia(num_cia, nombre) {
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = "{nombre_cia}";
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
	
	function actualiza_pro(num_pro, nombre) {
		pro = new Array();
		<!-- START BLOCK : nombre_pro -->
		pro[{num_pro}] = "{nombre_pro}";
		<!-- END BLOCK : nombre_pro -->
		
		if (parseInt(num_pro.value) > 0) {
			if (pro[parseInt(num_pro.value)] == null) {
				alert("Proveedor "+parseInt(num_pro.value)+" no esta en el catálogo de proveedores");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
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
	
	function validar(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.num_proveedor.value <= 0) {
			alert("Debe especificar el proveedor");
			form.num_proveedor.select();
			return false;
		}
		else if (form.num_documento.value <= 0) {
			alert("Debe especificar el número de documento");
			form.num_documento.select();
			return false;
		}
		else if (form.fecha.value.length < 8) {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else if (form.total_fac.value < 0) {
			alert("Debe especificar el total del documento");
			form.total_fac.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Factura de Materia Prima</p>
  <form action="./fac_fac_cap_v2.php" method="post" name="form">
  <input name="temp" type="hidden">
  <input name="num_cia" type="hidden" value="{num_cia}">
  <input name="num_proveedor" type="hidden" value="{num_proveedor}">
  <input name="observaciones" type="hidden" value="{observaciones}">
  <input name="num_documento" type="hidden" value="{num_documento}">
  <input name="fecha" type="hidden" value="{fecha}">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">No. de Documento </th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="vtabla" scope="col">Total de la Factura </th>
      </tr>
    <tr>
      <td class="tabla"><font size="+1">{num_cia} - {nombre_cia}</font> </td>
      <td class="tabla"><font size="+1">{num_proveedor} - {nombre_proveedor}</font> </td>
      <td class="tabla"><font size="+1">{num_documento}</font></td>
      <td class="tabla"><font size="+1">{fecha}</font></td>
      <td class="tabla"><input name="total_fac" type="text" id="total_fac" value="{total_fac}" size="12" maxlength="12" style="text-align:center;font-family:Arial, Helvetica, sans-serif;font-weight:bold;text-transform:uppercase;background-color:transparent;border:0 none;color:Black;font-size:large"></td>
      </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
      <th class="tabla" scope="col">Contenido</th>
      <th class="tabla" scope="col">Unidad</th>
      <th class="tabla" scope="col">Precio</th>
      <th class="tabla" scope="col">Desc. 1 </th>
      <th class="tabla" scope="col">Desc. 2 </th>
      <th class="tabla" scope="col">Desc. 3 </th>
      <th class="tabla" scope="col">I.V.A.</th>
      <th class="tabla" scope="col">I.E.P.S.</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Regalado</th>
      <th class="tabla" scope="col">No ingresar <br>
        a inventario </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="cantidad[]" type="text" class="insert" id="cantidad" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) importe({index}, this.form)" onKeyDown="if (event.keyCode == 13) ieps{i}.select()" size="6" maxlength="6"></td>
      <td class="rtabla"><input name="codmp[]" type="hidden" id="codmp" value="{codmp}">
        {codmp}</td>
      <td class="vtabla">{descripcion}</td>
      <td class="tabla"><input name="contenido[]" type="hidden" id="contenido" value="{contenido}">
        {contenido}</td>
      <td class="tabla">{unidad}</td>
      <td class="tabla"><input name="precio[]" type="hidden" id="precio" value="{precio}">
        {precio}</td>
      <td class="tabla"><input name="desc1[]" type="hidden" id="desc1" value="{desc1}">
        {desc1}</td>
      <td class="tabla"><input name="desc2[]" type="hidden" id="desc2" value="{desc2}">
        {desc2}</td>
      <td class="tabla"><input name="desc3[]" type="hidden" id="desc3" value="{desc3}">
        {desc3}</td>
      <td class="tabla"><input name="iva[]" type="hidden" id="iva" value="{iva}">
        {iva}</td>
      <td class="tabla"><input name="ieps[]" type="text" class="insert" id="ieps" onFocus="temp.value=this.value" onChange="if (isFloat(this,2,temp)) importe({index}, this.form); else this.value = temp.value" onKeyDown="if (event.keyCode == 13) cantidad{next}.select()" value="{ieps}" size="8"></td>
      <th class="rtabla"><input name="costo_unitario[]" type="text" class="rnombre" id="costo_unitario" size="12" maxlength="12" readonly="true"></th>
      <th class="tabla"><input name="regalado[]" type="checkbox" id="regalado" value="{index}" onClick="importe({index}, this.form)"></th>
      <th class="tabla"><input name="no_inv[]" type="checkbox" id="no_inv" value="{index}"></th>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="11" class="rtabla">Total</th>
      <th class="tabla"><input name="costo_total" type="text" id="costo_total" style="text-align:right;font-family:Arial, Helvetica, sans-serif;font-weight:bold;text-transform:uppercase;background-color:transparent;border:0 none;color:Black;font-size:large" value="0.00" size="12" maxlength="12" readonly="true"></th>
      <th colspan="2" class="tabla">&nbsp;</th>
      </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Para<br>
        aclaraci&oacute;n</th>
      <th class="tabla" scope="col">Observaciones</th>
    </tr>
    <tr>
      <td class="tabla"><input name="aclaracion" type="checkbox" id="aclaracion" value="1">
        Si</td>
      <td class="tabla"><textarea name="obs" class="insert" id="obs"></textarea></td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Atrás" onClick="document.location = './fac_fac_cap_v2.php'">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function importe(index, form) {
		if (form.cantidad.length != undefined) {
			var cantidad = !isNaN(parseFloat(form.cantidad[index].value)) ? parseFloat(form.cantidad[index].value) : 0;
			var precio   = !isNaN(parseFloat(form.precio[index].value)) ? parseFloat(form.precio[index].value) : 0;
			var desc1    = !isNaN(parseFloat(form.desc1[index].value)) ? parseFloat(form.desc1[index].value) : 0;
			var desc2    = !isNaN(parseFloat(form.desc2[index].value)) ? parseFloat(form.desc2[index].value) : 0;
			var desc3    = !isNaN(parseFloat(form.desc3[index].value)) ? parseFloat(form.desc3[index].value) : 0;
			var iva      = !isNaN(parseFloat(form.iva[index].value)) ? parseFloat(form.iva[index].value) : 0;
			var ieps     = !isNaN(parseFloat(form.ieps[index].value)) ? parseFloat(form.ieps[index].value) : 0;
			var regalado = form.regalado[index].checked;
			var importe_form  = form.costo_unitario[index];
		}
		else {
			var cantidad = !isNaN(parseFloat(form.cantidad.value)) ? parseFloat(form.cantidad.value) : 0;
			var precio   = !isNaN(parseFloat(form.precio.value)) ? parseFloat(form.precio.value) : 0;
			var desc1    = !isNaN(parseFloat(form.desc1.value)) ? parseFloat(form.desc1.value) : 0;
			var desc2    = !isNaN(parseFloat(form.desc2.value)) ? parseFloat(form.desc2.value) : 0;
			var desc3    = !isNaN(parseFloat(form.desc3.value)) ? parseFloat(form.desc3.value) : 0;
			var iva      = !isNaN(parseFloat(form.iva.value)) ? parseFloat(form.iva.value) : 0;
			var ieps     = !isNaN(parseFloat(form.ieps.value)) ? parseFloat(form.ieps.value) : 0;
			var regalado = form.regalado.checked;
			
			var importe_form  = form.costo_unitario;
		}
		
		var importe = 0;
		var total = 0;
		
		// Si cantidad es mayor a 0 y regalado no esta marcado
		if (cantidad > 0 && !regalado) {
			// Importe bruto
			importe = cantidad * precio;
			//Descuentos
			importe = desc1 > 0 ? importe * (1 - (desc1 / 100)) : importe;
			importe = desc2 > 0 ? importe * (1 - (desc2 / 100)) : importe;
			importe = desc3 > 0 ? importe * (1 - (desc3 / 100)) : importe;
			
			// I.E.P.S.
			importe = ieps > 0 ? importe + ieps : importe;
			
			// I.V.A.
			importe = iva > 0 ? importe * (1 + (iva / 100)) : importe;
			
			// Importe del producto
			importe_form.value = importe.toFixed(2);
		}
		// Si cantidad es mayor a 0 y regalado esta marcado
		else if (cantidad > 0 && regalado)
			importe_form.value = importe.toFixed(2);
		// Si cantidad es 0
		else if (cantidad == 0)
			importe_form.value = "";
		
		// Calcular total de la factura
		if (form.cantidad.length != undefined)
			for (i = 0; i < form.costo_unitario.length; i++)
				total += !isNaN(parseFloat(form.costo_unitario[i].value)) ? parseFloat(form.costo_unitario[i].value) : 0;
		else
			total = !isNaN(parseFloat(form.costo_unitario.value)) ? parseFloat(form.costo_unitario.value) : 0;
		
		form.costo_total.value = total.toFixed(2);
	}
	
	function validar(form) {
		if (form.costo_total.value < 0) {
			alert("Debe ingresar al menos un producto");
			if (form.cantidad.length != undefined)
				form.cantidad[0].select();
			else
				form.cantidad.select();
			return false;
		}
		else if (parseFloat(form.total_fac.value) > parseFloat(form.costo_total.value)) {
			if (confirm("El total de la factura no coincide con el total calculado.\n¿Desea cambiar el total de la factura?")) {
				var temp = prompt("Total de factura : " + form.total_fac.value + "\nTotal calculado  : " + form.costo_total.value + "\n\nEscriba el nuevo total de la factura", form.costo_total.value);
				if (parseFloat(temp) > 0) {
					value_total_fac = parseFloat(temp);
					form.total_fac.value = value_total_fac.toFixed(2);
				}
			}
			else
				return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				form.submit();
			else
				if (form.cantidad.length != undefined)
					form.cantidad[0].select();
				else
					form.cantidad.select();
	}
	
	window.onload = function() {
		document.form.cantidad.length == undefined ? document.form.cantidad.select() : document.form.cantidad[0].select();
		
		if (document.form.observaciones.value != '') {
			alert('observaciones:\n\n' + document.form.observaciones.value);
		}
	}
</script>
<!-- END BLOCK : captura -->
</body>
</html>
