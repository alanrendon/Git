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
<td align="center" valign="middle"><p class="title">Pago Manual de Proveedores</p>
  <form action="./ban_pma_pro_v2.php" method="get" name="form">
  <input type="hidden" name="temp">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <td class="vtabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_proveedor.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla">Proveedor</th>
      <td class="vtabla"><input name="num_proveedor" type="text" class="insert" id="num_proveedor" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha_corte.select()" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER SERFIN</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">Fecha de Corte </th>
      <td class="vtabla"><input name="fecha_corte" type="text" class="insert" id="fecha_corte" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha_cheque.select()" value="{fecha_corte}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Fecha de Cheque </th>
      <td class="vtabla"><input name="fecha_cheque" type="text" class="insert" id="fecha_cheque" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{fecha_cheque}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Tipo Consulta </th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked onClick="titulo.value='Compañías que no pagaran'">
        Proveedor&nbsp;&nbsp;
        <input name="tipo" type="radio" value="2" onClick="titulo.value='Proveedores que no se les pagara'">
        Compa&ntilde;&iacute;a</td>
    </tr>
  </table>  
    <br>
    <table class="tabla">
      <tr>
        <th colspan="10" class="tabla" scope="col"><input name="titulo" type="text" disabled="true" class="nombre" id="titulo" value="Compañías que no pagaran" size="50"></th>
        </tr>
      <tr>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[1].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[2].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[3].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[4].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[5].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[6].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[7].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[8].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[9].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[10].select()" size="3" maxlength="4"></td>
      </tr>
	  <tr>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[11].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[12].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[13].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[14].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[15].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[16].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[17].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[18].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[19].select()" size="3" maxlength="4"></td>
        <td class="tabla"><input name="no_pago[]" type="text" class="insert" id="no_pago" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) no_pago[0].select()" size="3" maxlength="4"></td>
      </tr>
    </table>
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.fecha_corte.value.length < 8) {
			alert("Debe especificar la fecha de corte");
			form.fecha_corte.select();
			return false;
		}
		else if (form.fecha_cheque.value.length < 8) {
			alert("Debe especificar la fecha para los cheques");
			form.fecha_cheque.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : proveedor -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pago Manual de Proveedores </p>
  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Proveedor</th>
  </tr>
  <tr>
    <th class="tabla"><font size="+1">{num_proveedor} - {nombre_proveedor}</font> </th>
  </tr>
</table>

  <br>
  <form action="./ban_pma_pro_v2.php" method="post" name="form">
  <input type="hidden" name="temp">
  <table width="80%" class="tabla">
    <tr>
      <th class="tabla" scope="col"><input name="checkall" type="checkbox" id="checkall" checked onClick="check_all(this)"></th>
      <th class="tabla" scope="col">Fecha de Pago </th>
      <th class="tabla" scope="col">No. Factura </th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : block_cia -->
	<tr>
      <th class="tabla"><input name="ini[]" type="hidden" id="ini" value="{ini}">
	  <input name="fin[]" type="hidden" id="fin" value="{fin}">
	  <input name="checkblock[]" type="checkbox" id="checkblock" checked onClick="check_block({block},this)"></th>
      <th class="tabla">Cia.: {num_cia} </th>
      <th class="tabla">Cuenta: {clabe_cuenta}</th>
      <th colspan="2" class="tabla">{nombre_cia}</th>
      </tr>
    <!-- START BLOCK : fac_cia -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}" checked onClick="total_block({block})"></td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{num_fact}</td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla"><strong>
        <input name="importe[]" type="hidden" id="importe" value="{importe}">
        {fimporte}</strong></td>
    </tr>
	<!-- END BLOCK : fac_cia -->
    <tr>
      <th colspan="4" class="rtabla">Total</th>
      <th class="rtabla"><input name="total[]" type="text" disabled="true" class="rtotal" id="total" value="{total}" size="12" maxlength="12"></th>
    </tr>
    <tr>
      <td colspan="5">&nbsp;</td>
      </tr>
	<!-- END BLOCK : block_cia -->
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <td class="vtabla"><input name="num_proveedor" type="text" class="insert" id="num_proveedor" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaPro(this,nombre_proveedor)" size="4" maxlength="4">
        <input name="nombre_proveedor" type="text" disabled="true" class="vnombre" id="nombre_proveedor" size="50"></td>
      <td class="vtabla"><input type="button" class="boton" value="Siguiente >>" onClick="siguiente()"></td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Terminar" onClick="cancelar()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Pagar y Terminar" onClick="terminar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
	var OK = true;
	var pro = new Array();
	<!-- START BLOCK : pro -->
	pro[{num_pro}] = "{nombre}";
	<!-- END BLOCK : pro -->
	
	function cambiaPro(num, nombre) {
		if (num.value == "") {
			nombre.value = "";
			OK = true;
		}
		else if (pro[num.value] != null) {
			nombre.value = pro[num.value];
			OK = true;
		}
		else {
			OK = false;
			alert("El proveedor seleccionado no tiene facturas por pagar");
			num.value = num.form.temp.value;
			num.select();
			OK = true;
		}
	}
	
	function check_all(check) {
		if (form.id.length == undefined) {
			form.checkblock.checked = check.checked == true ? true : false;
			form.id.checked = check.checked == true ? true : false;
			total_block(0);
		}
		else {
			if (form.checkblock.length == undefined) {
				form.chekblock.checked = checked == true ? true : false;
			}
			else {
				for (i = 0; i < form.checkblock.length; i++) {
					form.checkblock[i].checked = check.checked == true ? true : false;
				}
			}
			
			for (i = 0; i < form.id.length; i++) {
				form.id[i].checked = check.checked == true ? true : false;
			}
			
			if (form.total.length == undefined) {
				total_block(0);
			}
			else {
				for (j = 0; j < form.total.length; j++) {
					total_block(j);
				}
			}
		}
	}
	
	function check_block(block, check) {
		if (form.id.length == undefined) {
			form.id.checked = check.checked == true ? true : false;
		}
		else {
			for (i = parseInt(form.ini[block].value); i <= parseInt(form.fin[block].value); i++) {
				form.id[i].checked = check.checked == true ? true : false;
			}
		}
		
		total_block(block);
	}
	
	function total_block(block) {
		var total = 0;
		
		if (form.id.length == undefined) {
			total += form.id.checked ? parseFloat(form.importe.value) : 0;
			form.total.value = total.toFixed(2);
		}
		else {
			for (i = parseInt(form.ini[block].value); i <= parseInt(form.fin[block].value); i++) {
				total += form.id[i].checked ? parseFloat(form.importe[i].value) : 0;
			}
			form.total[block].value = total.toFixed(2);
		}
	}
	
	function cancelar() {
		if (confirm("¿Desea cancelar el pago a proveedores?"))
			document.location = "./ban_pma_pro_v2.php?cancelar=1";
		else
			return false;
	}
	
	function siguiente() {
		if (!OK) return false;
		
		if (confirm("¿Desea generar los pagos y pasar al siguiente proveedor?")) {
			// Verificar que haya al menos una factura seleccionada
			var count = 0;
			if (form.id.length == undefined) {
				count += form.id.checked ? 1 : 0;
			}
			else {
				for (i = 0; i < form.id.length; i++) {
					count += form.id[i].checked ? 1 : 0;
				}
			}
			
			if (count < 1) {
				alert("Debe seleccionar al menos una factura");
				return false;
			}
			else {
				form.action = "./ban_pma_pro_v2.php?generar=1&siguiente=1";
				form.submit();
			}
		}
		else if (confirm("¿Desea pasar al siguiente proveedor?")) {
			form.action = "./ban_pma_pro_v2.php?siguiente=1";
			form.submit();
		}
		else {
			return false;
		}
	}
	
	function terminar() {
		if (confirm("¿Desea generar los pagos y terminar el proceso?")) {
			// Verificar que haya al menos una factura seleccionada
			var count = 0;
			if (form.id.length == undefined) {
				count += form.id.checked ? 1 : 0;
			}
			else {
				for (i = 0; i < form.id.length; i++) {
					count += form.id[i].checked ? 1 : 0;
				}
			}
			
			if (count < 1) {
				alert("Debe seleccionar al menos una factura");
				return false;
			}
			
			form.action = "./ban_pma_pro_v2.php?generar=1&terminar=1";
			form.submit();
		}
		else {
			return false;
		}
	}
</script>
<!-- END BLOCK : proveedor -->
<!-- START BLOCK : compania -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pago Manual de Proveedores </p>
  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
  </tr>
  <tr>
    <th class="tabla"><font size="+1">{num_cia} - {nombre_cia}</font> </th>
  </tr>
</table>

  <br>
  <form action="./ban_pma_pro_v2.php" method="post" name="form">
  <input type="hidden" name="temp">
  <table width="80%" class="tabla">
    <tr>
      <th class="tabla" scope="col"><input name="checkall" type="checkbox" id="checkall" checked onClick="check_all(this)"></th>
      <th class="tabla" scope="col">Fecha de Pago </th>
      <th class="tabla" scope="col">No. Factura </th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : block_pro -->
	<tr>
      <th class="tabla"><input name="ini[]" type="hidden" id="ini" value="{ini}">
	  <input name="fin[]" type="hidden" id="fin" value="{fin}">
	  <input name="checkblock[]" type="checkbox" id="checkblock" checked onClick="check_block({block},this)"></th>
      <th class="tabla">No.: {num_proveedor} </th>
      <th colspan="3" class="tabla">{nombre_proveedor}</th>
      </tr>
    <!-- START BLOCK : fac_pro -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}" checked onClick="total_block({block})"></td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{num_fact}</td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla"><strong>
        <input name="importe[]" type="hidden" id="importe" value="{importe}">
        {fimporte}</strong></td>
    </tr>
	<!-- END BLOCK : fac_pro -->
    <tr>
      <th colspan="4" class="rtabla">Total</th>
      <th class="rtabla"><input name="total[]" type="text" disabled="true" class="rtotal" id="total" value="{total}" size="12" maxlength="12"></th>
    </tr>
    <tr>
      <td colspan="5">&nbsp;</td>
      </tr>
	<!-- END BLOCK : block_pro -->
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia(this,nombre_cia)" size="3" maxlength="3">
        <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="50"></td>
      <td class="vtabla"><input type="button" class="boton" value="Siguiente >>" onClick="siguiente()"></td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Terminar" onClick="cancelar()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Pagar y Terminar" onClick="terminar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
	var OK = true;
	var cia = new Array();
	<!-- START BLOCK : cia -->
	cia[{num_cia}] = "{nombre}";
	<!-- END BLOCK : cia -->
	
	function cambiaCia(num, nombre) {
		if (num.value == "") {
			nombre.value = "";
			OK = true;
		}
		else if (cia[num.value] != null) {
			nombre.value = cia[num.value];
			OK = true;
		}
		else {
			OK = false;
			alert("La compañía seleccionada no tiene facturas por pagar");
			num.value = num.form.temp.value;
			num.focus();
			OK = true;
		}
	}
	
	function check_all(check) {
		if (form.id.length == undefined) {
			form.checkblock.checked = check.checked == true ? true : false;
			form.id.checked = check.checked == true ? true : false;
			total_block(0);
		}
		else {
			if (form.checkblock.length == undefined) {
				form.chekblock.checked = checked == true ? true : false;
			}
			else {
				for (i = 0; i < form.checkblock.length; i++) {
					form.checkblock[i].checked = check.checked == true ? true : false;
				}
			}
			
			for (i = 0; i < form.id.length; i++) {
				form.id[i].checked = check.checked == true ? true : false;
			}
			
			if (form.total.length == undefined) {
				total_block(0);
			}
			else {
				for (j = 0; j < form.total.length; j++) {
					total_block(j);
				}
			}
		}
	}
	
	function check_block(block, check) {
		if (form.id.length == undefined) {
			form.id.checked = check.checked == true ? true : false;
		}
		else {
			for (i = parseInt(form.ini[block].value); i <= parseInt(form.fin[block].value); i++) {
				form.id[i].checked = check.checked == true ? true : false;
			}
		}
		
		total_block(block);
	}
	
	function total_block(block) {
		var total = 0;
		
		if (form.id.length == undefined) {
			total += form.id.checked ? parseFloat(form.importe.value) : 0;
			form.total.value = total.toFixed(2);
		}
		else {
			for (i = parseInt(form.ini[block].value); i <= parseInt(form.fin[block].value); i++) {
				total += form.id[i].checked ? parseFloat(form.importe[i].value) : 0;
			}
			form.total[block].value = total.toFixed(2);
		}
	}
	
	function cancelar() {
		if (confirm("¿Desea cancelar el pago a proveedores?"))
			document.location = "./ban_pma_pro_v2.php?cancelar=1";
		else
			return false;
	}
	
	function siguiente() {
		if (!OK) return false;
		
		if (confirm("¿Desea generar los pagos y pasar a la siguiente compañía?")) {
			// Verificar que haya al menos una factura seleccionada
			var count = 0;
			if (form.id.length == undefined) {
				count += form.id.checked ? 1 : 0;
			}
			else {
				for (i = 0; i < form.id.length; i++) {
					count += form.id[i].checked ? 1 : 0;
				}
			}
			
			if (count < 1) {
				alert("Debe seleccionar al menos una factura");
				return false;
			}
			else {
				form.action = "./ban_pma_pro_v2.php?generar=1&siguiente=1";
				form.submit();
			}
		}
		else if (confirm("¿Desea pasar a la siguiente compañía?")) {
			form.action = "./ban_pma_pro_v2.php?siguiente=1";
			form.submit();
		}
		else {
			return false;
		}
	}
	
	function terminar() {
		if (confirm("¿Desea generar los pagos y terminar el proceso?")) {
			form.action = "./ban_pma_pro_v2.php?generar=1&terminar=1";
			form.submit();
		}
		else {
			return false;
		}
	}
</script>
<!-- END BLOCK : compania -->
</body>
</html>
