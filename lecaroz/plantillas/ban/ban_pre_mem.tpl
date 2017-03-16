<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body,td,th {
	font-family: Geneva, Arial, Helvetica, sans-serif;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style1 {font-size: x-small}
.style2 {font-size: x-large}
.style3 {
	font-size: xx-small;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Memorandums de Prestamos Pendientes</p>
  <form action="./ban_pre_mem.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadministrador" class="insert" id="idadministrador">
        <option value="" selected></option>
		<!-- START BLOCK : id -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : id -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">D&iacute;as de atraso </th>
      <td class="vtabla"><input name="dias" type="text" class="insert" id="dias" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) this.blur()" value="20" size="3" maxlength="3"></td>
    </tr>
  </table>  
  <p>
    <input name="Submit" type="submit" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.dias.value <= 0) {
		alert("Debe especificar los dias de atraso");
		form.dias.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.dias.select();
-->
</script>
<!-- START BLOCK : datos -->
<!-- START BLOCK : memo -->
<table width="80%" align="center">
<tr><td>
<table width="100%">
  <tr>
    <td width="10%"><span class="style1">{num_cia}</span></td>
    <td width="80%" align="center"><strong>OFICINAS ADMINISTRATIVAS MOLLENDO, S.R.L. DE C.V. <br>
    <span class="style2">MEMORANDUM</span></strong></td>
    <td width="10%">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
<p align="right"><strong>MEXICO D.F., A {dia} DE {mes} DEL {anio} </strong></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><strong>{nombre_cia}<br>
  SR.  
  {encargado}
</strong></p>
<p>&nbsp;</p>
<p align="justify">&nbsp;&nbsp;&nbsp;&nbsp;POR MEDIO DEL PRESENTE ME PERMITO SALUDARLE Y A SU VEZ SOLICITARLE LA ACLARACI&Oacute;N DE LOS PRESTAMOS PERSONALES A EMPLEADOS:</p>
<table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">C&oacute;digo y nombre del empleado </th>
      <th class="print" scope="col">Fecha de &uacute;ltimo prestamo </th>
      <th class="print" scope="col">Saldo</th>
      <th class="print" scope="col">Abonos</th>
      <th class="print" scope="col">Fecha del &uacute;ltimo pago </th>
      <th class="print" scope="col">Importe del &uacute;ltimo pago </th>
      <th class="print" scope="col">Dias de atraso </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="print">{num_emp}</td>
      <td class="vprint">{nombre}</td>
      <td class="print">{fecha}</td>
      <td class="rprint">{saldo}</td>
      <td class="rprint">{abonos}</td>
      <td class="print">{fecha_ultimo}</td>
      <td class="rprint">{importe}</td>
      <td class="print">{dias}</td>
	</tr>
	<!-- END BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="3" class="print">Total</th>
	  <th class="rprint_total">{saldo_total}</th>
	  <th colspan="4" class="rprint_total">&nbsp;</th>
	  </tr>
  </table>
<p align="justify">&nbsp;&nbsp;&nbsp;&nbsp;SE OBSERVA QUE TIENEN PAGOS ATRASADOS CON <strong>{dias_retraso} D&Iacute;AS DE ATRASO</strong> A ESTA FECHA Y QUE NO HAN SIDO LIQUIDADOS TODAV&Iacute;A.</p>
<p align="justify">&nbsp;&nbsp;&nbsp;&nbsp;SIN M&Aacute;S POR EL MOMENTO ME DESPIDO DE USTED.</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p align="center">________________________________________________________<br>
  <strong>LIC. MIGUEL ANGEL REBUELTA DIEZ </strong></p>
<br>
<br>
<br>
<span class="style3">CCP. {admin} </span>
</td></tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : memo -->
</body>
</html>
