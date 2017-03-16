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
<td align="center" valign="middle"><p class="title">Desglose de Impuestos y Comprobaci&oacute;n de Importes Pagados </p>
  <form action="./ban_imp_des.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
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
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.anio) < 2000) {
		alert("Debe especificar el año de consulta");
		f.anio.select();
		return false;
	}
	else if (confirm("¿Desea generar el desglose de impuestos?"))
		f.submit();
}

window.onload = f.anio.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : dif -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Desglose de Impuestos y Comprobaci&oacute;n de Importes Pagados</p>
  <p style="font-family:Arial, Helvetica, sans-serif;">Existen las siguientes diferencias. No se continuara con el proceso.</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Capturado</th>
      <th class="tabla" scope="col">Pagado</th>
      <th class="tabla" scope="col">Diferencia</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{num_cia} {nombre} </td>
      <td class="rtabla">{cap}</td>
      <td class="rtabla">{pagado}</td>
      <td class="rtabla">{dif}</td>
	</tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_imp_des.php'">
  </p>  </td>
</tr>
</table>
<!-- END BLOCK : dif -->
<!-- START BLOCK : list -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Desglose de Gastos<br>
      {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : gasto -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="vprint">{concepto}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : gasto -->
</table>
<!-- END BLOCK : list -->
</body>
</html>
