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
<td align="center" valign="middle"><p class="title">Consulta de Efectivos</p>
  <form action="./ban_efe_zap_con.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3"></td>
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
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked>
        Desglosado
          <input name="tipo" type="radio" value="2">
          Totales</td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.anio.value <= 0) {
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
<!-- START BLOCK : ventas_des -->
<table width="100%">
  <tr>
    <td class="print_encabezado">&nbsp;</td>
    <td class="print_encabezado" align="center">Zapaterias Elite </td>
    <td class="rprint_encabezado">&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Efectivos Desglosados <br>
      {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<!-- START BLOCK : ventas_cia -->
<table width="80%" align="center" class="print">
  <tr>
    <th colspan="13" class="print" scope="col" style="font-size:14pt;">{num_cia} {nombre} </th>
  </tr>
  <tr>
    <th width="6%" class="print" scope="col">Dia</th>
    <th width="9%" class="print" scope="col">Venta</th>
    <th width="9%" class="print" scope="col">Errores</th>
    <th width="9%" class="print" scope="col">Venta Total </th>
    <th width="9%" class="print" scope="col">Otros</th>
    <th width="9%" class="print" scope="col">Gastos</th>
    <th width="13%" class="print" scope="col">Efectivo</th>
    <th width="6%" class="print" scope="col">Nota 1 </th>
    <th width="6%" class="print" scope="col">Nota 2 </th>
    <th width="6%" class="print" scope="col">Nota 3 </th>
    <th width="6%" class="print" scope="col">Nota 4 </th>
    <th width="6%" class="print" scope="col">Clientes</th>
    <th width="6%" class="print" scope="col">Pares</th>
  </tr>
  <!-- START BLOCK : ventas_cia_fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{dia}</td>
    <td class="rprint">{venta}</td>
    <td class="rprint" style="color:#CC0000;">{errores}</td>
    <td class="rprint">{venta_total}</td>
    <td class="rprint">{otros}</td>
    <td class="rprint">{gastos}</td>
    <td class="rprint" style="font-weight:bold;{color_efectivo}">{efectivo}</td>
    <td class="print">{nota1}</td>
    <td class="print">{nota2}</td>
    <td class="print">{nota3}</td>
    <td class="print">{nota4}</td>
    <td class="rprint">{clientes}</td>
    <td class="rprint">{pares}</td>
  </tr>
  <!-- END BLOCK : ventas_cia_fila -->
  <tr>
    <th class="print">Totales</th>
    <th class="rprint_total">{venta}</th>
    <th class="rprint_total">{errores}</th>
    <th class="rprint_total">{venta_total}</th>
    <th class="rprint_total">{otros}</th>
    <th class="rprint_total">{gastos}</th>
    <th class="rprint_total">{efectivo}</th>
    <th colspan="4" class="rprint_total">&nbsp;</th>
    <th class="rprint_total">{clientes}</th>
    <th class="rprint_total">{pares}</th>
  </tr>
</table>
<br>
<!-- END BLOCK : ventas_cia -->
<p align="center">
  <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_efe_zap_con.php'">
</p>
<!-- END BLOCK : ventas_des -->
<!-- START BLOCK : ventas_totales -->
<table width="100%">
  <tr>
    <td class="print_encabezado">&nbsp;</td>
    <td class="print_encabezado" align="center">Zapaterias Elite </td>
    <td class="rprint_encabezado">&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Total de Efectivos <br>
    {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Venta</th>
    <th class="print" scope="col">Errores</th>
    <th class="print" scope="col">Venta Total </th>
    <th class="print" scope="col">Otros</th>
    <th class="print" scope="col">Gastos</th>
    <th class="print" scope="col">Efectivo</th>
    <th class="print" scope="col">Clientes</th>
    <th class="print" scope="col">Pares</th>
  </tr>
  <!-- START BLOCK : fila_total -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="rprint">{venta}</td>
    <td class="rprint" style="color:#CC0000;">{errores}</td>
    <td class="rprint">{venta_total}</td>
    <td class="rprint">{otros}</td>
    <td class="rprint">{gastos}</td>
    <td class="rprint" style="font-weight:bold;{color_efectivo}">{efectivo}</td>
    <td class="rprint">{clientes}</td>
    <td class="rprint">{pares}</td>
  </tr>
  <!-- END BLOCK : fila_total -->
  <tr>
    <th class="print">&nbsp;</th>
    <th class="rprint_total">{venta}</th>
    <th class="rprint_total">{errores}</th>
    <th class="rprint_total">{venta_total}</th>
    <th class="rprint_total">{otros}</th>
    <th class="rprint_total">{gastos}</th>
    <th class="rprint_total">{efectivo}</th>
    <th class="rprint_total">{clientes}</th>
    <th class="rprint_total">{pares}</th>
  </tr>
</table>
<!-- END BLOCK : ventas_totales -->
</body>
</html>
