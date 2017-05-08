<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : cias -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Validaci&oacute;n de Datos </p>
  <form action="zap_rev_dat.php" method="post" name="form">
    <input name="action" type="hidden" id="action" value="hoja">
    <input name="tmp" type="hidden" id="tmp">  
    <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Usuario</th>
  </tr>
  <tr>
    <td class="tabla" style="font-size:14pt; font-weight:bold;">{usuario}</td>
  </tr>
</table>

  <br>
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla">Compa&ntilde;&iacute;a</th>
      <th class="tabla">Fecha</th>
    </tr>
    <!-- START BLOCK : cia -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="opt" type="radio" value="{opt}" onClick="next.disabled=false;tmp.value=this.value"{disabled}></td>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;">{num_cia} - {nombre} </td>
      <td class="tabla" style="font-size:12pt; font-weight:bold;">{fecha}</td>
    </tr>
	<!-- START BLOCK : void -->
	<tr>
      <td colspan="3" class="tabla" style="font-size:12pt; font-weight:bold;">&nbsp;</td>
      </tr>
	<!-- END BLOCK : void -->
	<!-- END BLOCK : cia -->
	<!-- END BLOCK : no_cias -->
	<tr>
      <td colspan="3" class="tabla" style="font-size:12pt; font-weight:bold;">No se ha recibo nueva infromaci&oacute;n por parte de las panaderias </td>
      </tr>
	  <!-- END BLOCK : no_cias -->
  </table>  
  <p>
    <input name="next" type="button" disabled="true" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	var tmp = f.tmp.value.split('|');
	var url = './zap_rev_dat.php?action=venta&num_cia=' + tmp[0] + '&fecha=' + escape(tmp[1]) + '&dir=r';
	document.location = url;
}
//-->
</script>
<!-- END BLOCK : cias -->
<!-- START BLOCK : venta -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Venta del D&iacute;a</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Tipo</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : venta_row -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{tipo}</td>
      <td class="rtabla">{importe}</td>
    </tr>
	<!-- END BLOCK : venta_row -->
    <tr>
      <th class="tabla">Total</th>
      <th class="rtabla" style="font-size:12pt;">{total}</th>
    </tr>
  </table><p>
<input type="button" class="boton" value="Inicio" onClick="document.location='./zap_rev_dat.php'"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./zap_rev_dat.php?action=gastos&num_cia={num_cia}&fecha={fecha}&dir=r'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : venta -->
<!-- START BLOCK : gastos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Gastos</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
    </tr>
  </table>  
  <br>
  <form method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Concepto</th>
	  <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Importe</th>
      </tr>
    <!-- START BLOCK : gas_row -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="tabla">	    <span class="rtabla">
	    <input name="omitir{i}" type="checkbox" id="omitir{i}" value="{id}" {omitir} />
	    </span></td>
	  <td class="vtabla" style="color:#0000CC; font-weight:bold;">{concepto}</td>
      <td class="tabla"><input name="id[]" type="hidden" id="id" value="{id}">
      <input name="codgastos[]" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaGasto({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) {
if (codgastos.length == undefined) this.blur();
else codgastos[{next}].select();
}
else if (event.keyCode == 38) {
if (codgastos.length == undefined) this.blur();
else codgastos[{back}].select();
}" value="{codgastos}" size="3">
        <input name="desc[]" type="text" disabled="true" class="vnombre" id="desc" value="{desc}" size="30"></td>
      <td class="rtabla" style="font-weight:bold;">{importe}</td>
      </tr>
	<!-- END BLOCK : gas_row -->
	<!-- START BLOCK : gas_pre -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="tabla">&nbsp;</td>
	  <td class="vtabla" style="color:#0000CC; font-weight:bold;">{concepto}</td>
      <td class="vtabla" style="font-weight:bold;">41 - PRESTAMO EMPLEADO </td>
      <td class="rtabla" style="font-weight:bold;">{importe}</td>
      </tr>
	<!-- END BLOCK : gas_pre -->
    <tr>
      <th colspan="3" class="rtabla">Total</th>
      <th class="rtabla" style="font-size:12pt; ">{total}</th>
      </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="validar('l')">
&nbsp;&nbsp;
<input type="button" class="boton" value="Inicio" onClick="document.location='./zap_rev_dat.php'"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="validar('r')">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, gasto = new Array();

<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{desc}";
<!-- END BLOCK : gasto -->

function cambiaGasto(i) {
	var inputGasto = null, nombreGasto = null;
	
	inputGasto = f.codgastos.length == undefined ? f.codgastos : f.codgastos[i];
	nombreGasto = f.desc.length == undefined ? f.desc : f.desc[i];
	
	if (inputGasto.value == "" || inputGasto.value == "0") {
		inputGasto.value = "";
		nombreGasto.value = "";
	}
	else if (gasto[get_val(inputGasto)] != null)
		nombreGasto.value = gasto[get_val(inputGasto)];
	else {
		alert("El código de gasto no se encuentra en el catálogo");
		inputGasto.value = f.tmp.value;
	}
}

function validar(dir) {
	// Validar que todos los gastos hayan sido codificados
	if (f.codgastos.length == undefined && get_val(f.codgastos) <= 0) {
		alert("Debe códificar todos los gastos");
		f.codgastos.select();
		return false;
	}
	else
		for (var i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) <= 0) {
				alert("Debe códificar todos los gastos");
				f.codgastos[i].select();
				return false;
			}
	
	// Validar el turno en los códigos de mercancia
	if (f.codgastos.length == undefined && get_val(f.codgastos) == 23 && f.turno.selectedIndex == 0) {
		alert("Debe seleccionar el turno para las mercancias");
		f.turno.focus();
		return false;
	}
	else
		for (i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) == 23 && f.turno[i].selectedIndex == 0) {
				alert("Debe seleccionar el turno para las mercancias");
				f.turno[i].focus();
				return false;
			}
	
	f.action = './zap_rev_dat.php?action=gastos_mod&num_cia={num_cia}&fecha={fecha}&dir=' + dir;
	f.submit();
}

window.onload = f.codgastos.length == undefined ? f.codgastos.select() : f.codgastos[0].select();
//-->
</script>
<!-- END BLOCK : gastos -->
<!-- START BLOCK : pres -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Prestamos</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
    </tr>
  </table>  
  <br>
  <form action="" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Cat&aacute;logo
        <input type="button" class="boton" value="Listar" onClick="listar({num_cia})"></th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Saldo<br>
        Anterior</th>
      <th class="tabla" scope="col">Prestamo</th>
      <th class="tabla" scope="col">Abono</th>
      <th class="tabla" scope="col">Nuevo<br>
        Saldo</th>
    </tr>
	<!-- START BLOCK : pres_row -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="hidden" id="id" value="{id}">
	  <input name="id_emp[]" type="hidden" id="id_emp" value="{id_emp}">
	  <input name="num_emp[]" type="text" class="insert" id="num_emp" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaEmp({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) {
if (num_emp.length == undefined) this.blur();
else num_emp[{next}].select();
}
else if (event.keyCode == 38) {
if (num_emp.length == undefined) this.blur();
else num_emp[{back}].select();
}" value="{num_emp}" size="4">
      <input name="nombre[]" type="text" class="vnombre" id="nombre" value="{nombre_real}" size="30"></td>
      <td class="vtabla" style="font-weight:bold;">{nombre}</td>
      <td class="rtabla" style="font-weight:bold;">&nbsp;{saldo_ini}&nbsp;</td>
      <td class="rtabla" style="color:#CC0000; font-weight:bold;">&nbsp;{cargo}&nbsp;</td>
      <td class="rtabla" style="color:#0000CC; font-weight:bold;">&nbsp;{abono}&nbsp;</td>
      <td class="rtabla" style="font-weight:bold;">&nbsp;{saldo_fin}&nbsp;</td>
    </tr>
	<!-- END BLOCK : pres_row -->
    <tr>
      <th colspan="2" class="rtabla">Total</th>
      <th class="rtabla">{saldo_ini}</th>
      <th class="rtabla" style="color:#CC0000;">{cargos}</th>
      <th class="rtabla" style="color:#0000CC;">{abonos}</th>
      <th class="rtabla">{saldo_fin}</th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="validar('l')">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
    &nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente >>" onClick="validar('r')">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, emp = new Array();
<!-- START BLOCK : emp -->
emp[{num_emp}] = new Array();
emp[{num_emp}][0] = {id_emp};
emp[{num_emp}][1] = "{nombre}";
<!-- END BLOCK : emp -->

function listar(num_cia) {
	var win = window.open("./listar_emp.php?num_cia=" + num_cia,"listar_emp.php?num_cia=" + num_cia,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=400,height=400");
	win.focus();
}

function cambiaEmp(i) {
	var num_emp, nombre, id_emp;
	num_emp = f.num_emp.length == undefined ? f.num_emp : f.num_emp[i];
	nombre = f.nombre.length == undefined ? f.nombre : f.nombre[i];
	id_emp = f.id_emp.length == undefined ? f.id_emp : f.id_emp[i];
	
	if (num_emp.value == '' || num_emp.value == '0') {
		num_emp.value = '';
		nombre.value = '';
		id_emp.value = '';
	}
	else if (emp[get_val(num_emp)] != null) {
		id_emp.value = emp[get_val(num_emp)][0];
		nombre.value = emp[get_val(num_emp)][1];
	}
	else {
		alert("El empleado no se encuentra en el catálogo");
		num_emp.value = f.tmp.value;
		num_emp.select();
	}
}

function validar(dir) {
	if (f.num_emp.length == undefined && f.num_emp.value == '') {
		alert('Debe especificar el número de empleado para el movimiento');
		f.num_emp.select();
		return false;
	}
	else
		for (var i = 0; i < f.num_emp.length; i++)
			if (f.num_emp[i].value == '') {
				alert('Debe especificar el número de empleado para el movimiento');
				f.num_emp[i].select();
				return false;
			}
	
	f.action = './zap_rev_dat.php?action=pres_mod&num_cia={num_cia}&fecha={fecha}&dir=' + dir;
	f.submit();
}

window.onload = f.num_emp.length == undefined ? f.num_emp.select() : f.num_emp[0].select();
//-->
</script>
<!-- END BLOCK : pres -->
<!-- START BLOCK : nomina -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Nomina</p>
<table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
    </tr>
  </table>  
  <br><table class="tabla">
  <tr>
    <th class="tabla" scope="col">Nombre</th>
    <th class="tabla" scope="col">S. Diario </th>
    <th class="tabla" scope="col">L</th>
    <th class="tabla" scope="col">M</th>
    <th class="tabla" scope="col">M</th>
    <th class="tabla" scope="col">J</th>
    <th class="tabla" scope="col">V</th>
    <th class="tabla" scope="col">S</th>
    <th class="tabla" scope="col">D</th>
    <th class="tabla" scope="col">Subtotal </th>
    <th class="tabla" scope="col">Comisi&oacute;n</th>
    <th class="tabla" scope="col">Total</th>
  </tr>
  <!-- START BLOCK : nom -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vtabla">{nombre}</td>
    <td class="rtabla">{sueldo}</td>
    <td class="tabla">{1}</td>
    <td class="tabla">{2}</td>
    <td class="tabla">{4}</td>
    <td class="tabla">{8}</td>
    <td class="tabla">{16}</td>
    <td class="tabla">{32}</td>
    <td class="tabla">{64}</td>
    <td class="rtabla">{subtotal}</td>
    <td class="rtabla">{comision}</td>
    <td class="rtabla" style="font-weight:bold;">{total}</td>
  </tr>
  <!-- END BLOCK : nom -->
  <tr>
    <th colspan="11" class="rtabla">Total</th>
    <th class="rtabla">{total}</th>
  </tr>
</table>
  <p><input type="button" class="boton" value="<< Regresar" onClick="document.location='./zap_rev_dat.php?action=pres&num_cia={num_cia}&fecha={fecha}&dir=l'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Inicio" onClick="document.location='./zap_rev_dat.php'"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./zap_rev_dat.php?action=acre&num_cia={num_cia}&fecha={fecha}&dir=r'"></p></td>
</tr>
</table>
<!-- END BLOCK : nomina -->
<!-- START BLOCK : acreditado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Acreditados</p>
<table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Acreditado</th>
    </tr>
    <!-- START BLOCK : acre -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{nombre}</td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla">{importe}</td>
      <td class="vtabla">{acreditado}</td>
    </tr>
	<!-- END BLOCK : acre -->
    <tr>
      <th colspan="2" class="rtabla">Total</th>
      <th class="rtabla">{total}</th>
      <th class="tabla">&nbsp;</th>
    </tr>
  </table>
  <p><input type="button" class="boton" value="<< Regresar" onClick="document.location='./zap_rev_dat.php?action=nomina&num_cia={num_cia}&fecha={fecha}&dir=l'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Inicio" onClick="document.location='./zap_rev_dat.php'"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./zap_rev_dat.php?action=inter&num_cia={num_cia}&fecha={fecha}&dir=r'"></p></td>
</tr>
</table>
<!-- END BLOCK : acreditado -->
<!-- START BLOCK : intercambio -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Intercambios</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Entradas</th>
      <th class="tabla" scope="col">Salidas</th>
    </tr>
    <tr>
      <td class="rtabla">{entradas}</td>
      <td class="rtabla">{salidas}</td>
    </tr>
    <tr>
      <th class="rtabla">{entradas}</th>
      <th class="rtabla">{salidas}</th>
    </tr>
  </table>  <p><input type="button" class="boton" value="<< Regresar" onClick="document.location='./zap_rev_dat.php?action=nomina&num_cia={num_cia}&fecha={fecha}&dir=l'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Inicio" onClick="document.location='./zap_rev_dat.php'"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./zap_rev_dat.php?action=result&num_cia={num_cia}&fecha={fecha}&dir=r'"></p></td>
</tr>
</table>
<!-- END BLOCK : intercambio -->
</body>
</html>
