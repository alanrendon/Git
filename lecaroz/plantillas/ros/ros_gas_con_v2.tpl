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
<td align="center" valign="middle"><p class="title">Consulta de Gastos</p>
  <form action="./ros_gas_con_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codgastos.select()" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3"></td>
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
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <!-- START BLOCK : opt_ext -->
	<tr>
      <th class="vtabla" scope="row">Tipo Gasto </th>
      <td class="vtabla"><input name="tipo" type="radio" value="" checked>
        Todos<br>
        <input name="tipo" type="radio" value="1">
        Operaci&oacute;n<br>
        <input name="tipo" type="radio" value="2">
        Generales<br>
        <input name="tipo" type="radio" value="0">
        No Incluidos </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo Listado </th>
      <td class="vtabla"><input name="con" type="radio" value="1" checked>
        Desglosado<br>
        <input name="con" type="radio" value="2">
        Total</td>
    </tr>
	<!-- END BLOCK : opt_ext -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.num_cia.value <= 0) {
		alert("Debe especificar la compañía");
		f.num_cia.select();
		return false;
	}
	if (f.anio.value < 2000) {
		alert("Debe especificar el año de consulta");
		f.anio.select();
		return false;
	}
	else
		f.submit();
}


window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : desglose -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Gastos </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt;"><strong>{num_cia} {nombre} </strong></td>
      <td class="tabla" style="font-size: 12pt;"><strong>{mes}</strong></td>
      <td class="tabla" style="font-size: 12pt;"><strong>{anio}</strong></td>
    </tr>
  </table>  
  <br>
  <form action="./ros_gas_con_v2.php" method="post" name="form" target="borrar">
    <input name="tipo" type="hidden" id="tipo" value="{tipo}">
    <input name="codgastos" type="hidden" id="codgastos" value="{codgastos}">    
    <table class="tabla">
    <!-- START BLOCK : gasto -->
	<tr>
      <th colspan="{span1}" class="vtabla" scope="col" style="font-size: 12pt;">{codgastos} {concepto}<a name="{codgastos}"></a></th>
      </tr>
    <tr>
      <th class="tabla">&nbsp;</th>
      <th class="tabla">Fecha</th>
      <th class="tabla">Concepto</th>
      <!-- START BLOCK : tturno -->
	  <th class="tabla">Turno</th>
	  <!-- END BLOCK : tturno -->
      <th class="tabla">Importe</th>
      <th class="tabla">&nbsp;</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}"{disabled}></td>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{concepto}</td>
      <!-- START BLOCK : turno -->
	  <td class="vtabla">{turno}</td>
	  <!-- END BLOCK : turno -->
      <td class="rtabla">{importe}</td>
      <td class="tabla"><input type="button" class="boton" value="..." onClick="mod({id})"{disabled}></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="{span2}" class="rtabla">Total</th>
      <th class="rtabla">{total}</th>
      <th class="tabla">&nbsp;</th>
    </tr>
    <tr>
      <td colspan="{span1}" class="tabla">&nbsp;</td>
      </tr>
	<!-- END BLOCK : gasto -->
	 <tr>
      <th colspan="{span2}" class="rtabla">Gran Total </th>
      <th class="rtabla" style="font-size:12pt;">{gran_total}</th>
      <th class="tabla">&nbsp;</th>
    </tr>
  </table>  
  <!-- START BLOCK : botones1 -->
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ros_gas_con_v2.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Borrar" onClick="del()"{disabled}>
</p>
<!-- END BLOCK : botones1 -->
<!-- START BLOCK : botones2 -->
<p>
<input type="button" class="boton" value="Cerrar" onClick="cerrar()">
</p>
<script language="javascript">
var fo = window.opener.document.form;

function cerrar() {
	pedirDatos();
	//self.close();
}

var mostrarDatos = function (oXML) {
	var data = oXML.responseText, mod, tmp, imp = new Array();
	
	data = data.split('\n');
	mod = data[0] == '1' ? false : true;
	data = data[1] && data[1].length > 0 ? data[1].split('||') : false;
	
	//fo.next.disabled = mod;
	
	if (!data)
		return false;
	
	for (i = 0; i < data.length; i++)
		if (data[i].length > 0) {
			tmp = data[i].split('|');
			imp[tmp[0]] = new Array(get_val2(tmp[1]), get_val2(tmp[2]), get_val2(tmp[3]), get_val2(tmp[4]), get_val2(tmp[5]), get_val2(tmp[6]), get_val2(tmp[7]), get_val2(tmp[8]), get_val2(tmp[9]), get_val2(tmp[10]), get_val2(tmp[11]), get_val2(tmp[12]), get_val2(tmp[13]), get_val2(tmp[14]), get_val2(tmp[15]));
		}
	
	for (i = 0; i < fo.num_cia.length; i++)
		if (imp[fo.num_cia[i].value] != null) {
			fo.venta[i].value = imp[fo.num_cia[i].value][0] != 0 ? number_format(imp[fo.num_cia[i].value][0], 2) : "";
			fo.errores[i].value = imp[fo.num_cia[i].value][1] != 0 ? number_format(imp[fo.num_cia[i].value][1], 2) : "";
			fo.venta_total[i].value = imp[fo.num_cia[i].value][0] - imp[fo.num_cia[i].value][1] != 0 ? number_format(imp[fo.num_cia[i].value][0] - imp[fo.num_cia[i].value][1], 2) : "";
			fo.venta_total[i].style.color = imp[fo.num_cia[i].value][0] - imp[fo.num_cia[i].value][1] < 0 ? 'CC0000' : '000000';
			fo.otros[i].value = imp[fo.num_cia[i].value][2] != 0 ? number_format(imp[fo.num_cia[i].value][2], 2) : "";
			fo.clientes[i].value = imp[fo.num_cia[i].value][3] != 0 ? number_format(imp[fo.num_cia[i].value][3], -1) : "";
			fo.pares[i].value = imp[fo.num_cia[i].value][4] != 0 ? number_format(imp[fo.num_cia[i].value][4], -1) : "";
			fo.nota1[i].value = imp[fo.num_cia[i].value][5] != 0 ? imp[fo.num_cia[i].value][5] : "";
			fo.nota2[i].value = imp[fo.num_cia[i].value][6] != 0 ? imp[fo.num_cia[i].value][6] : "";
			fo.nota3[i].value = imp[fo.num_cia[i].value][7] != 0 ? imp[fo.num_cia[i].value][7] : "";
			fo.nota4[i].value = imp[fo.num_cia[i].value][8] != 0 ? imp[fo.num_cia[i].value][8] : "";
			fo.nota1_ini[i].value = imp[fo.num_cia[i].value][9] != 0 ? imp[fo.num_cia[i].value][9] : "";
			fo.nota2_ini[i].value = imp[fo.num_cia[i].value][10] != 0 ? imp[fo.num_cia[i].value][10] : "";
			fo.nota3_ini[i].value = imp[fo.num_cia[i].value][11] != 0 ? imp[fo.num_cia[i].value][11] : "";
			fo.nota4_ini[i].value = imp[fo.num_cia[i].value][12] != 0 ? imp[fo.num_cia[i].value][12] : "";
			fo.gastos[i].value = imp[fo.num_cia[i].value][13] != 0 ? number_format(imp[fo.num_cia[i].value][13], 2) : "";
			fo.efectivo[i].value = imp[fo.num_cia[i].value][14] != 0 ? number_format(imp[fo.num_cia[i].value][14], 2) : "";
			fo.efectivo[i].style.color = imp[fo.num_cia[i].value][14] <= 0 ? 'CC0000' : '0000CC';
		}
	self.close();
}

function pedirDatos() {
	for (var i = 0; i < fo.num_cia.length; i++) {
		fo.venta[i].value = "";
		fo.errores[i].value = "";
		fo.venta_total[i].value = "";
		fo.otros[i].value = "";
		fo.nota1[i].value = "";
		fo.nota2[i].value = "";
		fo.nota3[i].value = "";
		fo.nota4[i].value = "";
		fo.clientes[i].value = "";
		fo.pares[i].value = "";
		fo.gastos[i].value = "";
		fo.efectivo[i].value = "";
	}
	
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	if (fo.fecha.value.length >= 8)
		// Pedir datos
		myConn.connect("./ban_efe_zap.php", "GET", "fecha=" + escape(fo.fecha.value), mostrarDatos);
	else
		fo.next.disabled = false;
}
</script>
<!-- END BLOCK : botones2 -->
</form></td>
</tr>
</table>
<iframe name="borrar" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function mod(id) {
	var win = window.open("./ros_gas_mod_v2.php?id=" + id,"mod_gas","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=250");
	win.focus();
}

function del() {
	var cont = 0;
	
	if (f.id.length == undefined)
		cont += f.id.checked ? 1 : 0;
	else
		for (var i = 0; i < f.id.length; i++)
			cont += f.id[i].checked ? 1 : 0;
	
	if (cont == 0) {
		alert("Debe seleccionar al menos un registro");
		return false;
	}
	else if (confirm("¿Desea borrar los gastos seleccionados?"))
		f.submit();
}
//-->
</script>
<!-- END BLOCK : desglose -->
<!-- START BLOCK : totales -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Gastos </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt;"><strong>{num_cia} {nombre} </strong></td>
      <td class="tabla" style="font-size: 12pt;"><strong>{mes}</strong></td>
      <td class="tabla" style="font-size: 12pt;"><strong>{anio}</strong></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <!-- START BLOCK : tipo -->
	<tr>
      <th colspan="2" class="tabla" scope="col">{tipo}</th>
      </tr>
    <tr>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : concepto -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{cod} {concepto} </td>
      <td class="rtabla">{importe}</td>
    </tr>
	<!-- END BLOCK : concepto -->
    <tr>
      <th class="rtabla">Total</th>
      <th class="rtabla">{total}</th>
    </tr>
    <tr>
      <td colspan="2" class="tabla">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : tipo -->
	  <tr>
      <th class="rtabla"> Gran Total</th>
      <th class="rtabla">{total}</th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ros_gas_con_v2.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : totales -->
<!-- START BLOCK : reload -->
<script language="javascript" type="text/javascript">
<!--
var doc = top.mainFrame ? top.mainFrame : top;

function recargar() {
	doc.location = "./ros_gas_con_v2.php?num_cia={num_cia}&codgastos={codgastos}&mes={mes}&anio={anio}&tipo={tipo}&con=1";
}

window.onload = recargar();
//-->
</script>
<!-- END BLOCK : reload -->
</body>
</html>
