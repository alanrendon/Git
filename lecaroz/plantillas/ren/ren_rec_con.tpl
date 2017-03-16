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
<td align="center" valign="middle"><p class="title">Consulta de Recibos de Renta</p>
  <form action="" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="" style="color:#0033CC;">ANUAL</option>
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
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) arr.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td class="vtabla"><input name="arr" type="text" class="insert" id="arr" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaArr()" onKeyDown="if (event.keyCode == 13) local.select()" size="4">
        <input name="nombre_arr" type="text" class="vnombre" id="nombre_arr" size="40" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Local</th>
      <td class="vtabla"><input name="local" type="text" class="insert" id="local" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaLocal()" onKeyDown="if (event.keyCode == 13) recibo.select()" size="4">
        <input name="nombre_local" type="text" class="vnombre" id="nombre_local" size="40" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Recibo</th>
      <td class="vtabla"><input name="recibo" type="text" class="insert" id="recibo" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="6"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Bloque</th>
      <td class="vtabla"><input name="bloque" type="radio" value="0" checked>
        Todos<br>
        <input name="bloque" type="radio" value="1">
        Internos<br>
        <input name="bloque" type="radio" value="2">
        Externos</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Incluir</th>
      <td class="vtabla"><input name="cancelados" type="checkbox" id="cancelados" value="1">
        Cancelados</td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, arr = new Array(), local = new Array();
<!-- START BLOCK : codarr -->
arr[{cod}] = "{nombre}";
<!-- END BLOCK : codarr -->
<!-- START BLOCK : numlocal -->
local[{num}] = "{nombre}";
<!-- END BLOCK : numlocal -->

function cambiaArr() {
	if (f.arr.value == '' || f.arr.value == '0') {
		f.arr.value = '';
		f.nombre_arr.value = '';
	}
	else if (arr[get_val(f.arr)] != null)
		f.nombre_arr.value = arr[get_val(f.arr)];
	else {
		alert('El arrendador no se encuentra en el catálogo');
		f.arr.value = f.tmp.value;
		f.arr.select();
	}
}

function cambiaLocal() {
	if (f.local.value == '' || f.local.value == '0') {
		f.local.value = '';
		f.nombre_local.value = '';
	}
	else if (local[get_val(f.local)] != null)
		f.nombre_local.value = local[get_val(f.local)];
	else {
		alert('El local no se encuentra en el catálogo');
		f.local.value = f.tmp.value;
		f.local.select();
	}
}

function validar() {
	/*if (f.anio.value <= 0) {
		alert("Debe especificar el año de consulta");
		f.anio.select();
		return false;
	}
	else*/
		f.submit();
}

window.onload = f.anio.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Recibos de Renta{leyenda}<br>
    Locales {bloque}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : arr -->
  <tr>
    <th colspan="12" class="vprint" style="font-size: 12pt;" scope="col">{cod} {arr} </th>
  </tr>
  <tr>
    <th class="print">Recibo</th>
    <th class="print">Local</th>
    <th class="print">Arrendatario</th>
    <th class="print">Renta</th>
    <th class="print">Agua</th>
    <th class="print">Mantenimiento</th>
    <th class="print">I.V.A.</th>
    <th class="print">I.S.R. Retenido </th>
    <th class="print">I.V.A. Retenido</th>
    <th class="print">Neto</th>
    <th class="print">Conciliado</th>
    <th class="print">Banco</th>
  </tr>
  <!-- START BLOCK : recibo -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{recibo}</td>
    <td class="vprint">{num} {nombre} </td>
    <td class="vprint">{arr}</td>
    <td class="rprint">{renta}</td>
    <td class="rprint">{agua}</td>
    <td class="rprint">{mant}</td>
    <td class="rprint">{iva}</td>
    <td class="rprint">{isr}</td>
    <td class="rprint">{ret}</td>
    <td class="rprint">{neto}</td>
    <td class="print">{fecha_con}</td>
    <td class="vprint">{banco}</td>
  </tr>
  <!-- START BLOCK : concepto -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">&nbsp;</td>
    <td colspan="11" class="vprint">{concepto}</td>
  </tr>
  <!-- END BLOCK : concepto -->
  <!-- END BLOCK : recibo -->
  <tr>
    <th colspan="9" class="rprint">Total</th>
    <th class="rprint" style="font-size: 12pt;">{total}</th>
    <th colspan="2" class="rprint" style="font-size: 12pt;">&nbsp;</th>
  </tr>
  <tr>
    <td colspan="12">&nbsp;</td>
  </tr>
  <!-- END BLOCK : arr -->
  <tr class="prit">
    <th colspan="9" class="rprint">Gran Total </th>
    <th class="rprint" style="font-size: 12pt;">{total}</th>
    <th colspan="2" class="rprint" style="font-size: 12pt;">&nbsp;</th>
  </tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : listado -->
</body>
</html>
