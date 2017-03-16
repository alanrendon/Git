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
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><input name="cod" type="text" class="insert" id="cod" size="3"></td>
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
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked>
        Desglosado<br>
        <input name="tipo" type="radio" value="2">
        Total</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente"> 
    </p></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : desglosado -->
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
      <td class="tabla">{num_cia} {nombre} </td>
      <td class="tabla">{mes}</td>
      <td class="tabla">{a&ntilde;o}</td>
    </tr>
  </table>  
  <br>  <table class="tabla">
    <!-- START BLOCK : codgastos -->
	<tr>
      <th colspan="4" class="tabla" scope="col"><a name="{codgastos}"></a>{codgastos} {nombre} </th>
      </tr>
    <tr>
      <th class="tabla">Fecha</th>
      <th class="tabla">Concepto</th>
      <th class="tabla">Importe</th>
      <th class="tabla">&nbsp;</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla">{importe}</td>
      <td class="tabla"><input type="button" class="boton" value="..."></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="tabla">Total</th>
      <th class="rtabla">{total}</th>
      <th class="tabla">&nbsp;</th>
    </tr>
    <tr>
      <td colspan="4" class="tabla">&nbsp;</td>
      </tr>
	<!-- END BLOCK : codgastos -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar">
  </p></td>
</tr>
</table>
<!-- END BLOCK : desglosado -->
<!-- START BLOCK : totales -->

<!-- END BLOCK : totales -->
</body>
</html>
