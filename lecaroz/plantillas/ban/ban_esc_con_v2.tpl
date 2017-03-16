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
<td align="center" valign="middle"><p class="title">Estados de Cuenta</p>
  <form action="./ban_esc_con_v2.php" method="get" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia()" onKeyDown="if (event.keyCode == 13) fecha1.select()" size="3" maxlength="3">
        <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta" onChange="cambiaConcepto()">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10">
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{fecha2}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mostrar</th>
      <td class="vtabla"><input name="tipo" type="radio" value="0" checked onClick="cod_mov.disabled = true">
        Todos<br>
        <input name="tipo" type="radio" value="1" onClick="cod_mov.disabled = true">
        Dep&oacute;sitos<br>
        <input name="tipo" type="radio" value="2" onClick="cod_mov.disabled = true">
        Retiros<br>
        <input name="tipo" type="radio" value="3" onClick="cod_mov.disabled = false">
        Concepto</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla"><select name="cod_mov" disabled="disabled" class="insert" id="cod_mov">
      </select></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

var cod_mov_1 = new Array();
var des_mov_1 = new Array();
var cod_mov_2 = new Array();
var des_mov_2 = new Array();
var cia = new Array();

<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->

<!-- START BLOCK : banorte -->
cod_mov_1[{i}] = "{cod_mov}";
des_mov_1[{i}] = "{des}";
<!-- END BLOCK : banorte -->
<!-- START BLOCK : santander -->
cod_mov_2[{i}] = "{cod_mov}";
des_mov_2[{i}] = "{des}";
<!-- END BLOCK : santander -->

function cambiaCia() {
	if (form.num_cia.value == "" || form.num_cia.value == "0") {
		form.num_cia.value = "";
		form.nombre.value = "";
	}
	else if (cia[get_val(form.num_cia)] != null)
		form.nombre.value = cia[get_val(form.num_cia)];
	else {
		alert("La compañía no se encuentra en el catálogo");
		form.num_cia.value = form.temp.value;
		form.num_cia.select();
	}
}

function cambiaConcepto() {
	var cuenta = form.cuenta.options[form.cuenta.selectedIndex].value;
	
	if (cuenta != 0) {
		mov = eval("cod_mov_" + cuenta);
		des = eval("des_mov_" + cuenta);
		
		form.cod_mov.length = mov.length != undefined ? mov.length : 1;
		if (mov.length != undefined) {
			for (i = 0; i < mov.length; i++) {
				form.cod_mov.options[i].value = mov[i];
				form.cod_mov.options[i].text = des[i];
			}
		}
		else {
			form.cod_mov.options[0].value = "";
			form.cod_mov.options[0].text = "";
		}
	}
}

function validar() {
	if (form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else if (form.fecha1.value.length < 8) {
		alert("Debe especificar el periodo de consulta");
		form.fecha1.select();
		return false;
	}
	else {
		form.submit();
	}
}

function alCargar() {
	cambiaConcepto();
	form.num_cia.select();
}

window.onload = alCargar();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td width="20%" rowspan="2" class="rprint_encabezado">{fecha}<br>
    {hora}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Estado de Cuenta </td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col" style="font-size: 16pt; color: #000000;">Cia.: {num_cia} </th>
    <th colspan="2" class="print" scope="col" style="font-size: 16pt; color: #000000;">Cuenta: {cuenta}</th>
    <th colspan="4" class="print" scope="col" style="font-size: 16pt; color: #000000;">{nombre_cia} ({banco})</th>
  </tr>
  <!-- START BLOCK : saldo_ini -->
  <tr>
    <th colspan="2" rowspan="2" class="print">Saldo Anterior </th>
    <th class="print" style="color: #0000CC;">Libros</th>
    <th class="print" style="color: #CC0000;">Banco</th>
    <th colspan="4" rowspan="2" class="print">&nbsp;</th>
  </tr>
  <tr>
    <th class="print_total" style="color: #0000CC;">{saldo_lib_ini}</th>
    <th class="print_total" style="color: #CC0000;">{saldo_ban_ini}</th>
  </tr>
  <!-- END BLOCK : saldo_ini -->
  <tr>
    <th colspan="8">&nbsp;</th>
  </tr>
  <tr>
    <th class="print">Fecha</th>
    <th class="print">Conciliado</th>
    <th class="print">Abono</th>
    <th class="print">Cargo</th>
    <th class="print">Folio</th>
    <th class="print">Beneficiario</th>
    <th class="print">Concepto</th>
    <th class="print">C&oacute;digo</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="print" style="color: #FF3300;">{fecha_con}</td>
    <td class="rprint" style="color: #0000CC;">{abono}</td>
    <td class="rprint" style="color: #CC0000 ">{cargo}</td>
    <td class="print"{color_folio}>{folio}</td>
    <td class="vprint">{beneficiario}</td>
    <td class="vprint">{concepto}</td>
    <td class="vprint">{cod_mov} {descripcion} </td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="2" class="print">Totales</th>
    <th class="rprint_total" style="color: #0000CC;">{abonos}</th>
    <th class="rprint_total" style="color: #CC0000;">{cargos}</th>
    <th colspan="4" class="print">&nbsp;</th>
  </tr>
  <tr>
    <th colspan="8">&nbsp;</th>
  </tr>
  <!-- START BLOCK : saldo_fin -->
  <tr>
    <th colspan="2" rowspan="2" class="print">Saldo Actual </th>
    <th class="print" style="color: #0000CC;">Libros</th>
    <th class="print" style="color: #CC0000;">Banco</th>
    <th colspan="2" class="print">Diferencia</th>
    <th colspan="2" rowspan="2" class="print">&nbsp;</th>
  </tr>
  <tr>
    <th class="print_total" style="color: #0000CC; font-size: 14pt;">{saldo_lib_fin}</th>
    <th class="print_total" style="color: #CC0000; font-size: 14pt;">{saldo_ban_fin}</th>
    <th colspan="2" class="print_total" style=" font-size: 14pt;">{diferencia}</th>
  </tr>
  <!-- END BLOCK : saldo_fin -->
</table>
<!-- END BLOCK : listado -->
<!-- START BLOCK : boton_cerrar -->
<p align="center">
<input type="button" class="boton" onClick="self.close()" value="Cerrar">
</p>
<!-- END BLOCK : boton_cerrar -->
<!-- START BLOCK : boton_regresar -->
<p align="center">
<input type="button" class="boton" onClick="document.location='./ban_esc_con_v2.php'" value="Regresar">
</p>
<!-- END BLOCK : boton_regresar -->
<!-- START BLOCK : boton_efe -->
<p align="center">
<input type="button" class="boton" onClick="history.back()" value="Regresar">
</p>
<!-- END BLOCK : boton_efe -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
window.onload = self.close();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
