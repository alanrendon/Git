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
<td align="center" valign="middle"><p class="title">Auxiliar de Inventario </p>
  <form action="./aux_inv_v4.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla" >
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia(this,nombre_cia)" onKeyDown="if (event.keyCode == 13) anio.select()" size="4">
        <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Producto</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaMP(this,nombre_mp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="4">
        <input name="nombre_mp" type="text" disabled="true" class="vnombre" id="nombre_mp" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Filtros</th>
      <td class="vtabla"><input name="ctrl" type="radio" value="0" checked>
        Todas<br>
        <input name="ctrl" type="radio" value="1">
        Controladas<br>
        <input name="ctrl" type="radio" value="2">
        No controladas<br>
        &nbsp;&nbsp;&nbsp;
        <input name="tipo" type="radio" value="0" checked>
        Todas <br>
        &nbsp;&nbsp;&nbsp;
        <input name="tipo" type="radio" value="1">
        Materia Prima<br>
        &nbsp;&nbsp;&nbsp;
        <input name="tipo" type="radio" value="2">
        Material de Empaque </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Incluir</th>
      <td class="vtabla"><input name="dif" type="checkbox" id="dif" value="1" checked>
        Diferencias<br>
        <input name="gas" type="checkbox" id="gas" value="1" checked>
        Gas</td>
    </tr>
    <!-- START BLOCK : extras -->
	<tr>
      <th class="vtabla" scope="row">Extras</th>
      <td class="vtabla"><strong>Inventario:</strong><br>
&nbsp;&nbsp;&nbsp;<input name="table" type="radio" onClick="act_real.disabled=false;
act_virtual.disabled=true;
act_his.disabled=false;" value="real" checked>
        Real
        <input name="table" type="radio" onClick="act_real.disabled=true;
act_virtual.disabled=false;
act_his.disabled=true;" value="virtual">
        Virtual<br>
        <!-- START BLOCK : update -->
		<strong>Actualizar:</strong><br>
        &nbsp;&nbsp;&nbsp;
        <input name="act_real" type="checkbox" id="act_real" value="1">
        Inventario Real<br>
        &nbsp;&nbsp;&nbsp;
        <input name="act_virtual" type="checkbox" disabled="true" id="act_virtual" value="1">
        Inventario Virtual<br>
        &nbsp;&nbsp;&nbsp;
        <input name="act_his" type="checkbox" id="act_his" value="1">
        Historico Inventario
		<!-- START BLOCK : update -->
		</td>
    </tr>
	<!-- END BLOCK : extras -->
  </table>
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form, cia = new Array(), mp = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : mp -->
mp[{codmp}] = "{nombre}";
<!-- END BLOCK : mp -->

function cambiaCia(num, nombre) {
	if (num.value == "") {
		nombre.value = "";
	}
	else if (cia[num.value] != null) {
		nombre.value = cia[num.value];
	}
	else {
		alert("La compañía no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function cambiaMP(num, nombre) {
	if (num.value == "") {
		nombre.value = "";
	}
	else if (mp[num.value] != null) {
		nombre.value = mp[num.value];
	}
	else {
		alert("El producto no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function validar() {
	if (form.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		form.num_cia.select();
		return false;
	}
	else if (form.anio.value <= 2000) {
		alert("Debe especificar el año de consulta");
		form.anio.select();
		return false;
	}
	else
		form.submit();
}

window.onload = form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : desglosado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="rprint_encabezado">Cia: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Auxiliar de Inventario del mes de {mes} de {anio} <br>
    {codmp} {nombre_mp} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th rowspan="2" class="print" scope="col">Fecha</th>
    <th rowspan="2" class="print" scope="col">Concepto</th>
    <th rowspan="2" class="print" scope="col">Proveedor</th>
    <th rowspan="2" class="print" scope="col">Costo<br>
      Unitario</th>
    <th colspan="2" class="print" scope="col">Entradas</th>
    <th colspan="3" class="print" scope="col">Salidas</th>
    <th colspan="2" class="print" scope="col">Existencia</th>
    <th rowspan="2" class="print" scope="col">Costo<br>
    Promedio</th>
    <th rowspan="2" class="print" scope="col">Dif.<br>
      Costo</th>
  </tr>
  <tr>
    <th class="print">Unidades</th>
    <th class="print">Valores</th>
    <th class="print">Turno</th>
    <th class="print">Unidades</th>
    <th class="print">Valores</th>
    <th class="print">Unidades</th>
    <th class="print">Valores</th>
  </tr>
  <tr>
    <th colspan="9" class="rprint" style="font-size: 10pt; font-weight: bold;">Existencia Inicial </th>
    <th class="rprint" style="font-size: 10pt; font-weight: bold;">{unidades_ini}</th>
    <th class="rprint" style="font-size: 10pt; font-weight: bold;">{valores_ini}</th>
    <th class="rprint" style="font-size: 10pt; font-weight: bold;">{costo_ini}</th>
    <th class="print">&nbsp;</th>
  </tr>
  <!-- START BLOCK : mov -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" {detalle}>
    <td class="print">{fecha}</td>
    <td class="vprint">{concepto}</td>
    <td class="vprint">{pro}</td>
    <td class="rprint">{costo}</td>
    <td class="rprint" style="color: #0000CC;">{uentrada}</td>
    <td class="rprint" style="color: #0000CC;">{ventrada}</td>
    <td class="print">{turno}</td>
    <td class="rprint" style="color: #CC0000;">{usalida}</td>
    <td class="rprint" style="color: #CC0000;">{vsalida}</td>
    <td class="rprint">{unidades}</td>
    <td class="rprint">{valores}</td>
    <td class="rprint">{costo_prom}</td>
    <td class="rprint">{dif}</td>
  </tr>
  <!-- END BLOCK : mov -->
  <tr>
    <th colspan="4" class="rprint" style="font-size: 10pt;">Totales</th>
    <th class="rprint_total" style="color: #0000CC;">{uentrada}</th>
    <th class="rprint_total" style="color: #0000CC;">{ventrada}</th>
    <th class="rprint_total">&nbsp;</th>
    <th class="rprint_total" style="color: #CC0000;">{usalida}</th>
    <th class="rprint_total" style="color: #CC0000;">{vsalida}</th>
    <th class="rprint_total">{unidades}</th>
    <th class="rprint_total">{valores}</th>
    <th class="rprint_total">{costo}</th>
    <th class="rprint_total">{dif}</th>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Turno</th>
    <th class="print" scope="col">Consumo</th>
  </tr>
  <!-- START BLOCK : consumo_turno -->
  <tr>
    <td class="vprint">{turno}</td>
    <td class="rprint">{consumo}</td>
  </tr>
  <!-- END BLOCK : consumo_turno -->
</table>
<p align="center">
  <!-- START BLOCK : button_back -->
  <input type="button" class="boton" value="Regresar" onClick="document.location='./aux_inv_v4.php'">
  <!-- END BLOCK : button_back -->
  <!-- START BLOCK : button_close -->
  <input type="button" class="boton" value="Cerrar" onClick="self.close()">
  <!-- END BLOCK : button_close -->
</p>
<!-- START BLOCK : act_dif -->
<script language="javascript" type="text/javascript">
<!--
var f = window.opener.document.data;

function act_dif(i, existencia, costo) {
	var dif, inv, total;
	
	if (f.existencia.length == undefined) {
		inv = get_val(f.inventario);
		dif = inv - existencia;
		total = dif * costo;
		f.existencia.value = existencia != 0 ? number_format(existencia, 2) : "";
		f.existencia.style.color = existencia > 0 ? "#000000" : "#CC0000";
		f.costo.value = number_format(costo, 4);
		f.falta.value = dif < 0 ? number_format(Math.abs(dif), 2) : "";
		f.sobra.value = dif > 0 ? number_format(Math.abs(dif), 2) : "";
		f.total.value = total != 0 ? number_format(Math.abs(total), 2) : "";
		f.total.style.color = total > 0 ? "#0000CC" : "#CC0000";
	}
	else {
		inv = get_val(f.inventario[i]);
		dif = inv - existencia;
		total = dif * costo;
		f.existencia[i].value = existencia != 0 ? number_format(existencia, 2) : "";
		f.existencia[i].style.color = existencia > 0 ? "#000000" : "#CC0000";
		f.costo[i].value = number_format(costo, 4);
		f.falta[i].value = dif < 0 ? number_format(Math.abs(dif), 2) : "";
		f.sobra[i].value = dif > 0 ? number_format(Math.abs(dif), 2) : "";
		f.total[i].value = total != 0 ? number_format(Math.abs(total), 2) : "";
		f.total[i].style.color = total > 0 ? "#0000CC" : "#CC0000";
	}
	totales();
}

function totales() {
	var falta = 0, sobra = 0, total = 0;
	if (f.total.length == undefined) {
		falta = get_val(f.falta) * get_val(f.costo);
		sobra = get_val(f.sobra) * get_val(f.costo);
	}
	else
		for (var i = 0; i < f.total.length; i++) {
			falta += get_val(f.falta[i]) * get_val(f.costo[i]);
			sobra += get_val(f.sobra[i]) * get_val(f.costo[i]);
		}
	
	f.contra.value = falta > 0 ? number_format(falta, 2) : "";
	f.favor.value = sobra > 0 ? number_format(sobra, 2) : "";
	f.gran_total.value = falta - sobra != 0 ? number_format(Math.abs(falta - sobra), 2) : "";
	f.gran_total.style.color = falta - sobra > 0 ? "#CC0000" : "#0000CC";
}

window.onload = act_dif({i}, {existencia}, {costo});
-->
</script>
<!-- END BLOCK : act_dif -->
<script language="javascript" type="text/javascript">
<!--
function detalle(fecha_cheque, folio_cheque, fecha_con, cuenta) {
	var win = window.open("fac_detalle.php?fecha_cheque=" + fecha_cheque + "&folio_cheque=" + folio_cheque + "&fecha_con=" + fecha_con + "&cuenta=" + cuenta,"fac_detalle","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=500,height=200");
	win.focus();
}
//-->
</script>
<!-- END BLOCK : desglosado -->
<!-- START BLOCK : totales -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="rprint_encabezado">Cia: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Auxiliar de Inventario del mes de {mes} de {anio}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" class="print">
  <tr>
    <th class="print">&nbsp;</th>
	<th colspan="3" class="print" scope="col">Existencia Inicial </th>
    <th colspan="2" class="print" scope="col">Entradas</th>
    <th colspan="2" class="print" scope="col">Salidas</th>
    <th colspan="3" class="print" scope="col">Existencia Final </th>
  </tr>
  <tr>
    <th class="print" scope="col">Materia Prima </th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Costo Promedio </th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Unidades</th>
    <th class="print" scope="col">Valores</th>
    <th class="print" scope="col">Costo Promedio </th>
  </tr>
  <!-- START BLOCK : pro -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint" style="color: #{color};" onClick="subCon({num_cia},{mes},{anio},{codmp})" onMouseOver="this.style.cursor='pointer'" onMouseOut="this.style.cursor='default'">{codmp} {nombre}</td>
    <td class="rprint">{unidades_ini}</td>
    <td class="rprint">{valores_ini}</td>
    <td class="rprint">{costo_ini}</td>
    <td class="rprint" style="color: #0000CC;">{uentrada}</td>
    <td class="rprint" style="color: #0000CC;">{ventrada}</td>
    <td class="rprint" style="color: #CC0000;">{usalida}</td>
    <td class="rprint" style="color: #CC0000;">{vsalida}</td>
    <td class="rprint">{unidades}</td>
    <td class="rprint">{valores}</td>
    <td class="rprint">{costo}</td>
  </tr>
  <!-- END BLOCK : pro -->
  <tr>
    <th class="rprint">Total General </th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint_total">{valores_ini}</th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint_total" style="color: #0000CC;">{ventrada}</th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint_total" style="color: #CC0000;">{vsalida}</th>
    <th class="rprint">&nbsp;</th>
    <th class="rprint_total">{valores}</th>
    <th class="rprint">&nbsp;</th>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Leyendas</th>
  </tr>
  <tr>
    <td bgcolor="#0000CC" class="vprint">&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td class="vprint">Controladas</td>
  </tr>
  <tr>
    <td bgcolor="#993300" class="vprint">&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td class="vprint">No controladas </td>
  </tr>
  <tr>
    <td bgcolor="#993399" class="vprint">&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td class="vprint">Material de Empaque </td>
  </tr>
</table>
<p align="center">
  <input type="button" class="boton" value="Regresar" onClick="document.location='./aux_inv_v4.php'">
</p>
<script language="javascript" type="text/javascript">
<!--
function subCon(num_cia, mes, anio, codmp) {
	var url = "./aux_inv_v4.php?num_cia=" + num_cia + "&mes=" + mes + "&anio=" + anio + "&codmp=" + codmp + "&ctrl=0&tipo=0&dif=1&gas=1&close=1";
	var win = window.open(url, "aux", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
}
-->
</script>
<!-- END BLOCK : totales -->
</body>
</html>
