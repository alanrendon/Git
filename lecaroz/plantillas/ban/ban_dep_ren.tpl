<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Dep&oacute;sitos de Renta</p>
  <form action="./ban_dep_ren.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected="selected">SANTANDER</option>
      </select></td>
    </tr>
  </table>  
  <br />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Local</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia({i})" onkeydown="movCursor(event.keyCode,fecha[{i}],null,fecha[{i}],num_cia[{back}],num_cia[{next}])" value="{num_cia}" size="3" />
        <input name="nombre_cia[]" type="text" disabled="true" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="20" /></td>
      <td class="tabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,anio[{i}],num_cia[{i}],anio[{i}],fecha[{back}],fecha[{next}])" value="{fecha}" size="10" maxlength="10" /></td>
      <td class="tabla"><select name="mes[]" class="insert" id="mes">
        <option value="ENERO"{1}>ENERO</option>
        <option value="FEBRERO"{2}>FEBRERO</option>
        <option value="MARZO"{3}>MARZO</option>
        <option value="ABRIL"{4}>ABRIL</option>
        <option value="MAYO"{5}>MAYO</option>
        <option value="JUNIO"{6}>JUNIO</option>
        <option value="JULIO"{7}>JULIO</option>
        <option value="AGOSTO"{8}>AGOSTO</option>
        <option value="SEPTIEMBRE"{9}>SEPTIEMBRE</option>
        <option value="OCTUBRE"{10}>OCTUBRE</option>
        <option value="NOVIEMBRE"{11}>NOVIEMBRE</option>
        <option value="DICIEMBRE"{12}>DICIEMBRE</option>
      </select>
        <input name="anio[]" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,importe[{i}],fecha[{i}],importe[{i}],anio[{back}],anio[{next}])" value="{anio}" size="4" maxlength="4" /></td>
      <td class="vtabla"><select name="local[]" class="insert" id="local" onchange="importeRenta({i})" style="width:100%;">
        <option value=""></option>
      </select></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" style="width:100%;" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,true)) calculaTotal()" onkeydown="movCursor(event.keyCode,num_cia[{next}],anio[{i}],null,importe[{back}],importe[{next}])" value="{importe}" size="10" /></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="4" class="rtabla">Total</th>
      <th class="tabla"><input name="total" type="text" disabled="true" class="rnombre" id="total" style="font-size:12pt; " value="{total}" size="10" /></th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), arr = new Array(), ren = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : cia -->
<!-- START BLOCK : arr -->
arr[{arr}] = new Array({locales});
<!-- END BLOCK : arr -->
<!-- START BLOCK : ren -->
ren[{local}] = {renta};
<!-- END BLOCK : ren -->

function cambiaCia(i) {
	if (f.num_cia[i].value == '' || f.num_cia[i].value == '0') {
		f.num_cia[i].value = '';
		f.nombre_cia[i].value = '';
	}
	else if (cia[get_val(f.num_cia[i])] != null)
		f.nombre_cia[i].value = cia[get_val(f.num_cia[i])];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia[i].value = f.tmp.value;
		f.num_cia[i].select();
	}
	
	cambiaLocales(i);
}

function cambiaLocales(i) {
	var j, k;
	
	if (get_val(f.num_cia[i]) == 0) {
		f.local[i].length = 1;
		f.local[i].options[0].value = '';
		f.local[i].options[0].text = '';
	}
	else {
		f.local[i].length = arr[get_val(f.num_cia[i])].length != undefined ? 1 + arr[get_val(f.num_cia[i])].length / 3 : 1;
		if (arr[get_val(f.num_cia[i])] != null && arr[get_val(f.num_cia[i])].length != undefined) {
			f.local[i].options[0].value = '';
			f.local[i].options[0].text = '';
			for (j = 0, k = 1; j < arr[get_val(f.num_cia[i])].length; j += 3, k++) {
				f.local[i].options[k].value = arr[get_val(f.num_cia[i])][j];
				f.local[i].options[k].text = arr[get_val(f.num_cia[i])][j + 1]/* + ' - ' + arr[get_val(f.num_cia[i])][j + 2]*/;
			}
		}
		else {
			f.local[i].length = 1;
			f.local[i].options[0].value = '';
			f.local[i].options[0].text = '';
		}
	}
}

function importeRenta(i) {
	f.importe[i].value = get_val(f.local[i]) > 0 ? numberFormat(ren[get_val(f.local[i])], 2) : '';
	calculaTotal();
}

function calculaTotal() {
	var total = 0, i;
	
	for (i = 0; i < f.importe.length; i++)
		total += get_val(f.importe[i]);
	
	f.total.value = numberFormat(total, 2);
}

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null && enter) enter.select();
	else if (keyCode == 37 && lt != null && lt) lt.select();
	else if (keyCode == 39 && rt != null && rt) rt.select();
	else if (keyCode == 38 && up != null && up) up.select();
	else if (keyCode == 40 && dn != null && dn) dn.select();
}

window.onload = function () { f.num_cia[0].select(); showAlert = true; };
//-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Dep&oacute;sitos de Renta<br />
      capturados el {fecha} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Cuenta</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Local</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : mov -->
  <tr>
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="print">{cuenta}</td>
    <td class="print">{fecha}</td>
    <td class="vprint">{concepto}</td>
    <td class="vprint">{local}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : mov -->
  <tr>
    <th colspan="5" class="print">&nbsp;</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
