<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<link href="/lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Otros Dep&oacute;sitos</p>
<form name="form" method="get" action="./ban_dep_otros.php">
<input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1" {1}>ENERO</option>
        <option value="2" {2}>FEBRERO</option>
        <option value="3" {3}>MARZO</option>
        <option value="4" {4}>ABRIL</option>
        <option value="5" {5}>MAYO</option>
        <option value="6" {6}>JUNIO</option>
        <option value="7" {7}>JULIO</option>
        <option value="8" {8}>AGOSTO</option>
        <option value="9" {9}>SEPTIEMBRE</option>
        <option value="10" {10}>OCTUBRE</option>
        <option value="11" {11}>NOVIEMBRE</option>
        <option value="12" {12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) form.numfilas.select();" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de filas </th>
      <td class="vtabla"><input name="numfilas" type="text" class="insert" id="numfilas" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) form.anio.select();" value="120" size="4" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Acumulado</th>
      <td class="vtabla"><p>
        <label>
        <input name="tipo_con" type="radio" value="TRUE" checked>
  Si</label>

        <label>
        <input type="radio" name="tipo_con" value="FALSE">
  No</label>

      </p></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">S&oacute;lo listado </th>
      <td class="vtabla"><p>
        <label>
        <input type="radio" name="gen_listado" value="TRUE">
  Si</label>
  
        <label>
        <input name="gen_listado" type="radio" value="FALSE" checked>
  No</label>
      
      </p></td>
    </tr>
  </table>  
  <p>
    <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" onClick="valida_registro()">
  </p>
  </form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.anio.value <= 0) {
			alert("Debe especificar el año");
			document.form.anio.select();
			return false;
		}
		else if (document.form.numfilas.value <= 0) {
			alert("Debe especificar el número de filas");
			document.form.numfilas.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.anio.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Captura de Otros Dep&oacute;sitos </p>
<form name="form" method="post" action="./ban_dep_otros.php">
<input name="temp" type="hidden">
<input name="maxdias" type="hidden" value="{maxdias}">
<input name="numfilas" type="hidden" value="{numfilas}">
<input name="acumulado" type="hidden" id="acumulado" value="{acumulado}">
<input name="todos" type="hidden" id="todos" value="{todos}">
<input name="con" type="hidden" id="con" value="{con}">
<input name="tmp" type="hidden" id="tmp">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Mes</th>
    <th class="tabla" scope="col">A&ntilde;o</th>
  </tr>
  <tr>
    <td class="tabla"><strong>
      <input name="mes" type="hidden" id="mes" value="{mes}">
      {nombre_mes}</strong></td>
    <td class="tabla"><input name="anio" type="hidden" id="anio" value="{anio}">
      <strong>{anio}</strong></td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">D&iacute;a</th>
    <th class="tabla" scope="col">Importe</th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Total</th>
    </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla"><input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp)) actualiza_compania(this,form.nombre{i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.dia{i}.select();
else if (event.keyCode == 38) form.num_cia{back}.select();
else if (event.keyCode == 40) form.num_cia{next}.select();
else if (event.keyCode == 37) form.concepto{back}.select();" value="{num_cia}" size="3" maxlength="3">
      <input name="nombre{i}" type="text" class="vnombre" id="nombre{i}" value="{nombre_cia}" size="25" readonly="true"></td>
    <td class="tabla"><input name="dia{i}" type="text" class="insert" id="dia{i}" onFocus="form.temp.value=this.value" onChange="if (!(isInt(this,form.temp) && parseInt(this.value) <= parseInt(form.maxdias.value))) this.value = '';" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.importe{i}.select();
else if (event.keyCode == 37) form.num_cia{i}.select();
else if (event.keyCode == 40) form.dia{next}.select();
else if (event.keyCode == 38) form.dia{back}.select();" value="{dia}" size="2" maxlength="2"></td>
    <td class="tabla"><input name="importe{i}" type="text" class="rinsert" id="importe{i}" onFocus="form.tmp.value=this.value" onChange="/*isFloat2(this,2,form.temp)*/if (inputFormat(this,2,false)) calculaTotal()" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.concepto{i}.select();
else if (event.keyCode == 37) form.dia{i}.select();
else if (event.keyCode == 38) form.importe{back}.select();
else if (event.keyCode == 40) form.importe{next}.select();" value="{importe}" size="10" maxlength="10"></td>
    <td class="tabla"><input name="concepto{i}" type="text" class="vinsert" id="concepto{i}" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia{next}.select();
else if (event.keyCode == 37) importe{i}.select();
else if (event.keyCode == 38) concepto{back}.select();
else if (event.keyCode == 40) concepto{next}.select();" value="{concepto}" size="20" maxlength="100"></td>
    <td class="tabla"><input name="total_row{i}" type="text" disabled class="rinsert" id="total_row{i}" style="color:#000;font-weight:bold;" size="10"></td>
    </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="2" class="tabla">&nbsp;</th>
    <th class="tabla"><input name="total" type="text" disabled class="rnombre" id="total" value="{total}" size="10"></th>
    <th colspan="2" class="tabla">&nbsp;</th>
    </tr>
</table>
<p>
  <input type="button" class="boton" value="Cancelar" onClick="document.location = '{regresar}'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Capturar" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script language="javascript" type="text/javascript">
var f = document.form, nombre = new Array();
	
	// Validar y actualizar número y nombre de compañía
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
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
	
	function calculaTotal() {
		var total = 0, i;
		
		for (i = 0; i < get_val(f.numfilas); i++)
			total += get_val(eval('f.importe' + i));
		
		f.total.value = numberFormat(total, 2);
		
		totalRows();
	}
	
	function totalRows() {
		var total = 0;
		
		$$('input[id^=importe]').each(function(el, i) {
			total += el.get('value').getNumericValue();
			
			if (el.get('value').getNumericValue() != 0 && total != 0) {
				$('total_row' + i).set('value', total.numberFormat(2, '.', ','));
			}
			else {
				$('total_row' + i).set('value', '');
			}
		});
	}
	
	function valida_registro() {
		if (confirm("¿Son todos los datos correctos?"))
			document.form.submit();
		else
			return false;
	}
	
	function tab_cancel(evento) {
		if (evento = 9)
			return false;
	}
	
	window.onload = document.form.num_cia0.select();
	//window.onclick = tab_cancel(event.keyCode);
</script>
<!-- END BLOCK : captura -->

<!-- START BLOCK : listado -->
<table width="98%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Otros Dep&oacute;sitos Capturados <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th class="print" scope="col">Cia.</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Fecha</th>
      <th class="print" scope="col">Concepto</th>
      <th class="print" scope="col">Dep&oacute;sito</th>
      </tr>
    <!-- START BLOCK : grupo -->
	<!-- START BLOCK : fila_lis -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="print">{fecha}</td>
      <td class="vprint">{concepto}</td>
      <td class="rprint"><strong class="rtabla">{deposito}</strong></td>
      </tr>
	<!-- END BLOCK : fila_lis -->
	<!-- START BLOCK : total -->
    <tr>
      <th colspan="4" class="rprint">Total</th>
      <th class="rprint_total">{total}</th>
      </tr>
	<!-- END BLOCK : total -->
	<tr>
      <td colspan="5">&nbsp;</td>
      </tr>
	<!-- END BLOCK : grupo -->
	<tr>
	  <th colspan="4" class="rprint">Gran Total </th>
	  <th class="rprint_total">{gran_total}</th>
	</tr>
  </table>
  <br><table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Otros Dep&oacute;sitos </th>
    </tr>
  <tr>
    <th class="print" scope="col">Total de Mes <br>
      Anterior </th>
    <th class="print" scope="col">Total del Mes </th>
  </tr>
  <tr>
    <th class="print"><font size="+1">{total_mes_ant}</font></th>
    <th class="print"><font size="+1">{total_mes}</font></th>
  </tr>
  <tr>
    <th colspan="2" class="print" scope="col">Gastos de Caja </th>
    </tr>
  <th class="print" scope="col">Total de Mes <br>
      Anterior </th>
    <th class="print" scope="col">Total del Mes </th>
  </tr>
  <tr>
    <th class="print"><font size="+1">{total_mes_ant_gas}</font></th>
    <th class="print"><font size="+1">{total_mes_gas}</font></th>
  </tr>
</table>
<script language="javascript" type="text/javascript">
	function imprimir() {
		window.print();
		document.location = "./ban_dep_otros.php";
	}
	
	//window.onload = imprimir();
</script>
<!-- END BLOCK : listado -->
</body>
</html>
