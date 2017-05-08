<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Movimientos Pendientes de Palomear</p>
  <form action="./ban_mov_pen_v2.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER</option>
      </select></td>
    </tr>
  </table>  <p>
    <input type="submit" class="boton" value="Siguiente">
    </p></form></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : movimientos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Movimientos Pendientes de Palomear</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="row" style="font-size: 16pt;">{banco}</th>
    </tr>
  </table>
  <br>
  <!-- END BLOCK : cia -->
  <form action="" method="get" name="form{num_cia}">
  <a name="{num_cia}"></a>
  <input name="cuenta" type="hidden" id="cuenta" value="{cuenta}">
  <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
  <table width="100%" class="tabla">
	<tr>
      <th width="10%" rowspan="2" class="vtabla" scope="col"><input type="button" class="boton" value="A" style="color: #0000CC;" onClick="conAbono(this.form)">
        Abonos<br>
        <input type="button" class="boton" value="C" style="color: #CC0000;" onClick="conCargo(this.form)">
        Cargos<!-- <br>
        <input type="button" class="boton" value="X" onClick="delMov(this.form)">
        Borrar</th> -->
      <th class="tabla" scope="col" style="font-size: 14pt; color: #000000;">Cia.: {num_cia} </th>
      <th colspan="2" class="tabla" scope="col" style="font-size: 14pt; color: #000000;">Cuenta: {clabe_cuenta} </th>
      <th colspan="3" class="tabla" scope="col" style="font-size: 14pt; color: #000000;">{nombre_cia}</th>
      </tr>
    <tr>
      <th width="12%" class="tabla">Fecha</th>
      <th width="10%" class="tabla">Abono</th>
      <th width="10%" class="tabla">Cargo</th>
      <th width="10%" class="tabla">Folio</th>
      <th width="10%" class="tabla">C&oacute;digo</th>
      <th class="tabla">Concepto</th>
    </tr>
    <!-- START BLOCK : mov -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}">
        <input name="tipo_mov[]" type="hidden" id="tipo_mov" value="{tipo_mov}"></td>
      <td class="tabla">{fecha}</td>
      <td class="rtabla" style="font-weight: bold; color: #0000CC;">{abono}</td>
      <td class="rtabla" style="font-weight: bold; color: #CC0000;">{cargo}</td>
      <td class="tabla">{folio}</td>
      <td class="tabla">{cod_banco}</td>
      <td class="vtabla">{concepto}</td>
    </tr>
	<!-- END BLOCK : mov -->
    <tr>
      <th colspan="2" class="rtabla">Totales</th>
      <th class="rtabla" style="color: #0000CC;">{abonos}</th>
      <th class="rtabla" style="color: #CC0000;">{cargos}</th>
      <th colspan="3" class="tabla">&nbsp;</th>
      </tr>
  </table>
  </form>
  <br>
  <!-- END BLOCK : cia -->
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Abonos</th>
      <th class="tabla" scope="col">Cargos</th>
    </tr>
    <tr>
      <th class="tabla" style="color: #0000CC; font-size: 14pt;">{abonos}</th>
      <th class="tabla" style="color: #CC0000; font-size: 14pt;">{cargos}</th>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_mov_pen_v2.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Listado" onClick="document.location='./ban_mov_pen_v2.php?list=1&cuenta={cuenta}'">
</p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function conAbono(form) {
	if (form.id.length == undefined) {
		if (!form.id.checked) {
			alert("Debe seleccionar al menos un movimiento");
			return false;
		}
		else if (form.tipo_mov.value != "f") {
			alert("El movimiento seleccionado no es 'Abono a la cuenta'");
			return false;
		}
	}
	else {
		var cont = 0;

		for (i = 0; i < form.id.length; i++) {
			if (form.id[i].checked) {
				if (form.tipo_mov[i].value != "f") {
					alert("Uno de los movimientos seleccionados no es 'Abono a cuenta'");
					return false;
				}

				cont++;
			}
		}

		if (cont == 0) {
			alert("Debe seleccionar al menos un movimiento");
			return false;
		}
	}

	var win = window.open("", "mod_abo", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600");
	form.action = "./ban_mov_dep_v2.php";
	form.target = "mod_abo";
	form.submit();
	win.focus();
}

function conCargo(form) {
	if (form.id.length == undefined) {
		if (!form.id.checked) {
			alert("Debe seleccionar al menos un movimiento");
			return false;
		}
		else if (form.tipo_mov.value != "t") {
			alert("El movimiento seleccionado no es 'Cargo a la cuenta'");
			return false;
		}
	}
	else {
		var cont = 0;

		for (i = 0; i < form.id.length; i++) {
			if (form.id[i].checked) {
				if (form.tipo_mov[i].value != "t") {
					alert("Uno de los movimientos seleccionados no es 'Cargo a cuenta'");
					return false;
				}

				cont++;
			}
		}

		if (cont == 0) {
			alert("Debe seleccionar al menos un movimiento");
			return false;
		}
		// [28-Jul-2008] Ya se pueden seleccionar mas cargos para conciliar al mismo tiempo
		/*else if (cont > 1) {
			alert("Debe seleccionar solo un movimiento de 'Cargo a cuenta'");
			return false;
		}*/
	}

	var win = window.open("", "mod_car", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600");
	form.action = "./ban_mov_ret_v2.php";
	form.target = "mod_car";
	form.submit();
	win.focus();
}

function delMov(form) {
	if (form.id.length == undefined) {
		if (!form.id.checked) {
			alert("Debe seleccionar al menos un movimiento");
			return false;
		}
	}
	else {
		var cont = 0;

		for (i = 0; i < form.id.length; i++) {
			cont += form.id[i].checked ? 1 : 0;
		}

		if (cont == 0) {
			alert("Debe seleccionar al menos un movimiento");
			return false;
		}
	}

	var win = window.open("", "del_mov", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=400,height=300");
	form.action = "./ban_mov_del_v2.php";
	form.target = "del_mov";
	form.submit();
	win.focus();
}
-->
</script>
<!-- END BLOCK : movimientos -->
<!-- START BLOCK : no_mov -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-family: Arial, Helvetica, sans-serif; font-size: 12pt; font-weight: bold; color: #CC0000;">No hay movimientos pendientes</p>
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_mov_pen_v2.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Listado" onClick="document.location='./ban_mov_pen_v2.php?list=1&cuenta={cuenta}'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : no_mov -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Movimientos Pendientes Palomeados <br>
    al d&iacute;a {dia} de {mes} del {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <!-- START BLOCK : cia_con -->
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
  <!-- END BLOCK : cia_con -->
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
