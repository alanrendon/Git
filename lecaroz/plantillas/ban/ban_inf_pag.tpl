<!-- START BLOCK : inicio -->
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Pago de Infonavit</p>
<form action="" method="get" name="form">
<input name="tmp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="form.temp.value=this.value" onchange="if (isInt(this,form.temp)) cambiaCia()" onkeydown="if (event.keyCode == 13) this.blur()" size="3" maxlength="5">
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="40" /></td>
    </tr>
  </table>

  <p>
    <input type="button" class="boton" value="Listado" onclick="document.location='./ban_inf_pag.php?list=1'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onclick="valida_registro()">
  </p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	window.onload = document.form.num_cia.select();
	
	var f = document.form;
	
	function cambiaCia() {
		if (f.num_cia.value == '' || f.num_cia.value == '0') {
			f.num_cia.value = '';
			f.nombre.value = '';
		}
		else {
			var myConn = new XHConn();
		
			if (!myConn)
				alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
			
			// Pedir datos
			myConn.connect('./ban_inf_pag.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
		}
	}
	
	var obtenerCia = function (oXML) {
		var result = oXML.responseText;
		
		if (result == '') {
			alert('La compañía no se encuentra en el catálogo');
			f.num_cia.value = f.tmp.value;
			f.num_cia.select();
		}
		else
			f.nombre.value = result;
	}
	
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		else
			document.form.submit();
	}
</script>
<!-- END BLOCK : inicio -->

<!-- START BLOCK : empleados -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title">Pago de Infonavit</p>
<form name="form" method="post" action="./ban_inf_pag.php?tabla={tabla}" onkeydown="if (event.keyCode == 13) return false">
<input name="numfilas" type="hidden" value="{numfilas}">
<input name="tmp" type="hidden" id="tmp">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
	<td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    {num_cia} - {nombre_cia}</td>
    <th class="vtabla">Fecha</th>
    <td class="vtabla" style="font-size:12pt; font-weight:bold;"><input name="fecha" type="hidden" id="fecha" value="{fecha}">
    {fecha}</td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th colspan="2" class="vtabla" scope="col">N&uacute;mero y nombre del empleado </th>
    <th class="tabla" scope="col">Mes</th>
    <th class="tabla" scope="col">A&ntilde;o</th>
	<th class="tabla" scope="col">Pago</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vtabla"><input name="id_emp{i}" type="checkbox" id="id_emp{i}" value="{id_emp}" />      
      {num_emp}</td>
    <td class="vtabla">{nombre_emp}</td>
    <td class="vtabla"><input name="mes[]" type="hidden" id="mes" value="{mes}" />
    {mes_escrito}<!--<select name="mes[]" class="insert" id="mes" onchange="buscaImporte({index})">
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
    </select>--></td>
    <td class="tabla"><input name="anio[]" type="hidden" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp); buscaImporte({index})" onkeydown="movCursor(event.keyCode,importe{i},null,importe{i},anio{back},anio{next})" value="{anio}" />
    {anio}</td>
	<td class="rtabla"><input name="importe[]" type="hidden" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="movCursor(event.keyCode,anio{next},anio{i},null,importe{back},importe{next})" value="{importe}">
	{importe}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<p>
  <input type="button" class="boton" value="Regresar" onclick="document.location='./ban_inf_pag.php'" />
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Capturar" onclick="validar()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

/*function buscaImporte(i) {
	var id = f.id_emp.length == undefined ? f.id_emp.value : f.id_emp[i].value;
	var mes = f.mes.length == undefined ? f.mes.value : f.mes[i].value;
	var anio = f.anio.length == undefined ? f.anio.value : f.anio[i].value;
	
	if (get_val2(mes) == 0 && get_val2(anio) == 0) return false;
	
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	// Pedir datos
	myConn.connect("./ban_inf_pag.php", "GET", 'id=' + id + '&mes=' + mes + '&anio=' + anio + '&i=' + i, resultImporte);
}

var resultImporte = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') return false;
	else {
		var tmp = result.split('|');
		
		if (f.importe.length == undefined)
			f.importe.value = tmp[1];
		else
			f.importe[get_val2(tmp[0])].value = tmp[1];
	}
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}*/

function validar() {
	if (confirm("¿Son correctos los datos?"))
		document.form.submit();
	else
		return false;
}

//window.onload = f.anio.length == undefined ? f.anio.select() : f.anio[0].select();
//-->
</script>
<!-- END BLOCK : empleados -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Pagos de Infonavit<br>
      capturados el {fecha} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="4" class="vprint_total" scope="col">{num_cia} {nombre} </th>
  </tr>
  <tr>
    <th class="print">No.</th>
    <th class="print">Nombre</th>
    <th class="print">Mes</th>
    <th class="print">Importe</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr>
    <td class="print">{num_emp}</td>
    <td class="vprint">{nombre}</td>
    <td class="print">{mes}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : row -->
  <tr>
    <th colspan="3" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
  <tr>
    <td colspan="4" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
</table>

<!-- END BLOCK : listado -->
<!-- START BLOCK : recibos -->
<!-- START BLOCK : recibo -->
<style type="text/css">
	td.folio {
		border: 3px solid;
	}
	td.firma {
		border-bottom: 1px solid;
	}
</style>
<table width="100%" height="49%">
  <tr>
    <td width="15%" rowspan="3" align="left" valign="top"><img src="./imagenes/escudo_lecaroz.jpg" width="83" height="132"></td>
    <td width="70" colspan="2" align="center" valign="top"><font face="Geneva, Arial, Helvetica, sans-serif" size="+2"><strong>{cia}</strong></font></td>
    <td align="right" valign="top" width="15%"><table width="100%"><tr><td><font face="Geneva, Arial, Helvetica, sans-serif"><strong>Folio&nbsp;&nbsp;&nbsp;</strong></font></td><td class="folio" width="70%" align="center"><font face="Geneva, Arial, Helvetica, sans-serif"><strong>{folio}</strong></font></td></tr></table>
  </tr>
  <tr>
    <td colspan="2" align="justify" valign="top">&nbsp;</td>
    <td rowspan="2" align="right" valign="top">  
  </tr>
  <tr>
    <td colspan="2" align="justify" valign="top"><font face="Geneva, Arial, Helvetica, sans-serif">Recib&iacute; de <strong>{nombre}</strong> la cantidad de <strong>{importe} PESOS ({importe_escrito} PESOS {centavos}/100 M.N.)</strong> correspondiente al pago del mes de <strong>{mes}</strong> por el concepto de pago de <strong>INFONAVIT</strong>. </font></td>
  </tr>
  <tr>
    <td widthalign="left" valign="top" height="40">&nbsp;</td>
    <td align="center" valign="top" width="40%">&nbsp;</td>
    <td align="center" valign="top" width="30%" class="firma">&nbsp;</td>
    <td align="right" valign="top">  
  </tr>
  <tr>
    <td widthalign="left" valign="top">&nbsp;</td>
    <td align="center" valign="top">&nbsp;</td>
    <td align="center" valign="top"><font face="Geneva, Arial, Helvetica, sans-serif"><strong>Lic. Miguel A. Rebuelta Diez </strong></font></td>
    <td align="right" valign="top">  
  </tr>
</table>
{br}
<!-- END BLOCK : recibo -->
<script language="javascript" type="text/javascript">
	function imprimir() {
		//window.print();
		document.location = "./ban_inf_pag.php";
	}
	
	window.onload = imprimir();
</script>
<!-- END BLOCK : recibos -->