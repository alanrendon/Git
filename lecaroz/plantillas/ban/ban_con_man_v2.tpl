<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Conciliaci&oacute;n Manual</p>
  <form action="./ban_con_man_v2.php" method="get" name="form">
  <input name="num_cia1" type="hidden" value="{num_cia1}">
  <input name="num_cia2" type="hidden" value="{num_cia2}">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha de Conciliaci&oacute;n </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.fecha.value.length < 8) {
		alert("Debe especificar la fecha de conciliación");
		form.fecha.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.fecha.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : con_screen -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Conciliaci&oacute;n Manual </p>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Banco</th>
    <th class="tabla" scope="col">Cuenta</th>
    <th class="tabla" scope="col">Fecha de Conciliaci&oacute;n</th>
  </tr>
  <tr>
    <td class="tabla" scope="row"><font size="+2" color="#0066FF"><strong>{num_cia} - {nombre_cia} </strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{banco}</strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{clabe_cuenta}</strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{fecha}</strong></font></td>
  </tr>
</table>
<br>
<form action="./ban_con_man_v2.php" name="form">
<input name="cuenta" type="hidden" value="{cuenta}">
<input name="fecha" type="hidden" id="fecha" value="{fecha}">
<input name="accion" type="hidden">
<input name="tmp" type="hidden" id="tmp">
<table>
  <tr>
    <td align="center" valign="top"><span style="font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 14pt;">Cargos</span><br>
      <table class="tabla">
        <tr>
          <th class="tabla" scope="col"><input name="allcar" type="checkbox" id="allcar" onClick="checkCar(this)"></th>
          <th class="tabla" scope="col">Fecha</th>
          <th class="tabla" scope="col">Concepto</th>
          <th class="tabla" scope="col">Folio</th>
          <th class="tabla" scope="col">Importe</th>
        </tr>
        <!-- START BLOCK : cargo -->
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="tabla"><input name="idcar[]" type="checkbox" id="idcar" value="{id}" {checked}></td>
          <td class="tabla" style="font-weight:bold;">{fecha}</td>
          <td class="vtabla" style="font-weight:bold;">{cod_mov} {descripcion}</td>
          <td class="tabla" style="font-weight:bold;">{folio}</td>
          <td class="rtabla" style="color:#CC0000; font-weight:bold;">{importe}</td>
        </tr>
		<!-- END BLOCK : cargo -->
		<!-- START BLOCK : no_cargos -->
        <tr>
          <td colspan="5" class="tabla" style="color: #CC0000; font-weight: bold;">No hay movimientos</td>
          </tr>
		  <!-- END BLOCK : no_cargos -->
      </table></td>
    <td width="50">&nbsp;</td>
    <td align="center" valign="top"><span style="font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 14pt;">Abonos</span><br>
      <table class="tabla">
        <tr>
          <th class="tabla" scope="col"><input name="allabo" type="checkbox" id="allabo" onClick="checkAbo(this)"></th>
          <th class="tabla" scope="col">Fecha</th>
          <th class="tabla" scope="col">Concepto</th>
          <th class="tabla" scope="col">Importe</th>
          <th class="tabla" scope="col">Div</th>
        </tr>
        <!-- START BLOCK : abono -->
		<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
          <td class="tabla"><input name="idabo[]" type="checkbox" id="idabo" value="{id}" {checked}></td>
          <td class="tabla" style="font-weight:bold;">{fecha}</td>
          <td class="vtabla" style="font-weight:bold;">{cod_mov} {descripcion}</td>
          <td class="rtabla" style="color:#0000CC; font-weight:bold;">{importe}</td>
          <td class="tabla"><input type="button" class="boton" value="..." onClick="div({id})"></td>
        </tr>
		<!-- END BLOCK : abono -->
		<!-- START BLOCK : no_abonos -->
        <tr>
          <td colspan="5" class="tabla" style="color: #CC0000; font-weight: bold;">No hay movimientos </td>
          </tr>
		  <!-- END BLOCK : no_abonos -->
      </table></td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Otros Depósitos">
</p>
<table class="tabla">
  <tr>
    <td colspan="2" class="vtabla"><strong>Usted esta en: {num_cia} - {nombre_cia} </strong></td>
    </tr>
  <tr>
    <td class="tabla"><input name="num_cia{cuenta}" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia(this,nombre_cia)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{num_cia_next}" size="3" maxlength="3">
      <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia_next}" size="30"></td>
    <td class="tabla"><input type="button" class="boton" value="Siguiente >>" onClick="validar('next')"></td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_con_man_v2.php'">
&nbsp;&nbsp;  
<input type="button" class="boton" value="Terminar"  onClick="validar('finish')">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;
var cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre_cia}";
<!-- END BLOCK : cia -->

function cambiaCia(num, nombre) {
	if (num.value == "")
		nombre.value = "";
	else if (cia[num.value] != null)
		nombre.value = cia[num.value];
	else {
		alert("La compañía especificada no tiene movimientos pendientes");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function checkCar(check) {
	if (form.idcar) {
		if (form.idcar.length == undefined) {
			form.idcar.checked = check.checked;
		}
		else {
			for (i = 0; i < form.idcar.length; i++) {
				form.idcar[i].checked = check.checked;
			}
		}
	}
}

function checkAbo(check) {
	if (form.idabo) {
		if (form.idabo.length == undefined) {
			form.idabo.checked = check.checked;
		}
		else {
			for (i = 0; i < form.idabo.length; i++) {
				form.idabo[i].checked = check.checked;
			}
		}
	}
}

function div(id) {
	var win = window.open("./ban_dep_div_v2.php?id=" + id, "mod", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=600");
}

function validar(accion) {
	var cont = 0;
	
	if (form.idcar) {
		if (form.idcar.length == undefined)
			cont += form.idcar.checked ? 1 : 0;
		else
			for (i = 0; i < form.idcar.length; i++)
				cont += form.idcar[i].checked ? 1 : 0;
	}
	if (form.idabo) {
		if (form.idabo.length == undefined)
			cont += form.idabo.checked ? 1 : 0;
		else
			for (i = 0; i < form.idabo.length; i++)
				cont += form.idabo[i].checked ? 1 : 0;
	}
	
	if (cont == 0) {
		document.form.action = "./ban_con_man_v2.php";
		document.form.target = "_self";
		document.form.method = "get"
		document.form.accion.value = accion;
		document.form.submit();
	}
	else {
		res = window.open("", "res_con", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=850,height=700");
		res.moveTo(87, 34);
		res.focus();
		
		document.form.action = "./ban_res_con_v2.php";
		document.form.target = "res_con";
		document.form.method = "post";
		document.form.accion.value = accion;
		document.form.submit();
	}
}

window.onload = form.num_cia.select();
-->
</script>
<!-- END BLOCK : con_screen -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Movimientos Palomeados Manualemente<br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <!-- START BLOCK : cia_list -->
  <tr>
    <th class="print" scope="col">Cia.: {num_cia} </th>
    <th colspan="2" class="print" scope="col">Cuenta: {cuenta} </th>
    <th colspan="3" class="print" scope="col">{nombre_cia}</th>
  </tr>
  <tr>
    <th class="print">Fecha</th>
    <th class="print">Abono</th>
    <th class="print">Cargo</th>
    <th class="print">Folio</th>
    <th class="print">C&oacute;digo de Movimiento </th>
    <th class="print">Concepto</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="rprint" style="color: #0000CC;">{abono}</td>
    <td class="rprint" style="color: #CC0000;">{cargo}</td>
    <td class="print">{folio}</td>
    <td class="vprint">{cod_mov} {descripcion}</td>
    <td class="vprint">{concepto}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="print">Totales</th>
    <th class="rprint_total" style="color: #0000CC;">{abonos}</th>
    <th class="rprint_total" style="color: #CC0000;">{cargos}</th>
    <th colspan="3" class="print">&nbsp;</th>
  </tr>
  <tr>
    <td colspan="6">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia_list -->
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col" style="font-size: 12pt; color: #0000CC;">Total de Abonos </th>
    <th class="print" scope="col" style="font-size: 12pt; color: #CC0000;">Total de Cargos </th>
  </tr>
  <tr>
    <th class="print" style="font-size: 14pt; color: #0000CC;">{total_abonos}</th>
    <th class="print" style="font-size: 14pt; color: #CC0000;">{total_cargos}</th>
  </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
