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
<td align="center" valign="middle"><p class="title">Recibos de Rentas Autom&aacute;ticos </p>
  <form action="./ren_rec_cap.php" method="get" name="form"><table class="tabla">
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
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(f) {
	if (f.anio.value <= 0) {
		alert("Debe especificar el año de captura");
		f.anio.select();
		return false;
	}
	else
		f.submit();
}

window.onload = document.form.anio.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Recibos de Rentas Autom&aacute;ticos </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{mes_escrito}</td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{anio}</td>
    </tr>
  </table>  
  <br>
  <form action="./ren_rec_cap.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="mes" type="hidden" id="mes" value="{mes}">
    <input name="anio" type="hidden" id="anio" value="{anio}">
    <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Arrendador</th>
      <th class="tabla" scope="col">Folio</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rtabla"><input name="cod[]" type="hidden" id="cod" value="{cod}">
        {cod}</td>
      <td class="vtabla">{nombre}</td>
      <td class="tabla"><input name="folio[]" type="text" class="rinsert" id="folio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) folio[{next}].select()" value="{folio}" size="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ren_rec_cap.php?cancel=1'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	f.submit();
}

window.onload = f.folio.length == undefined ? f.folio.select() : f.folio[0].select();
-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : vencidos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Contratos Vencidos</p>
  <p style="font-family:Arial, Helvetica, sans-serif; font:10pt; font-weight:bold;">Los siguientes contratos estan vencidos y no se proseguira con la impresi&oacute;n.</p>
  <table align="center" class="tabla">
  <tr>
    <th class="tabla" scope="col">Local</th>
    <th class="tabla" scope="col">Inmobiliaria</th>
    <th class="tabla" scope="col">Renta</th>
    <th class="tabla" scope="col">Fecha de Vencimiento </th>
  </tr>
  <!-- START BLOCK : fila_ven -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vtabla">{num} {nombre} </td>
    <td class="vtabla"><!--{cod} -->{arr}</td>
    <td class="rtabla">{renta}</td>
    <td class="tabla">{fecha_ven}</td>
  </tr>
  <!-- END BLOCK : fila_ven -->
</table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ren_rec_cap.php'"> 
    </p></td>
</tr>
</table>
<!-- END BLOCK : vencidos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Recibos de Rentas Autom&aacute;ticos</p>
<table class="tabla">
    <tr>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{mes_escrito}</td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{anio}</td>
    </tr>
  </table>  
  <br>
<form action="./ren_rec_cap.php" method="get" name="form">
  <input name="mes" type="hidden" id="mes" value="{mes}">
  <input name="anio" type="hidden" id="anio" value="{anio}">
  <table align="center" class="tabla">
  <!-- START BLOCK : arrendador -->
  <tr>
    <th colspan="11" class="vtabla" scope="col" style="font-size: 12pt;">{cod} {nombre} </th>
  </tr>
  <tr>
    <th class="tabla">Recibo</th>
    <th class="tabla">Local</th>
    <th class="tabla">Bloque</th>
    <th class="tabla">Arrendatario</th>
    <th class="tabla">Renta</th>
    <th class="tabla">Agua</th>
    <th class="tabla">Mantenimiento</th>
    <th class="tabla">I.V.A.</th>
    <th class="tabla">I.S.R. Ret. </th>
    <th class="tabla">I.V.A. Ret. </th>
    <th class="tabla">Neto</th>
  </tr>
  <!-- START BLOCK : recibo -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rtabla"><strong>{recibo}</strong></td>
    <td class="tabla"><strong>{local}</strong></td>
    <td class="vtabla">{bloque}</td>
    <td class="vtabla"><strong>{nombre}</strong></td>
    <td class="rtabla">{renta}</td>
    <td class="rtabla">{agua}</td>
    <td class="rtabla">{mant}</td>
    <td class="rtabla">{iva}</td>
    <td class="rtabla">{isr}</td>
    <td class="rtabla">{ret}</td>
    <td class="rtabla"><strong>{neto}</strong></td>
  </tr>
  <!-- END BLOCK : recibo -->
  <tr>
    <td colspan="11">&nbsp;</td>
  </tr>
  <!-- END BLOCK : arrendador -->
</table>
<p>
  <input type="button" class="boton" value="Regresar" onClick="this.form.submit()">
&nbsp;&nbsp;  
<input type="button" class="boton" value="Siguiente" onClick="document.location='./ren_rec_cap.php?imp=1'">
</p></form>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->
<!-- START BLOCK : impresion -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Recibos de Rentas Autom&aacute;ticos</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{mes_escrito}</td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{anio}</td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Arrendador</th>
      <th class="tabla" scope="col">Folios a Imprimir </th>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : imp -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{cod} {nombre} </td>
      <td class="tabla">{folio1}{folio2}</td>
      <td class="tabla">{cantidad}</td>
      <td class="tabla"><input name="" type="button" class="boton" onClick="imp({cod},{ini},{fin})" value="Imprimir"></td>
    </tr>
	<!-- END BLOCK : imp -->
  </table>  
  <p>
    <input type="button" class="boton" value="Terminar" onClick="document.location='./ren_rec_cap.php'">
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function imp(arr, ini, fin) {
	opt = "arr=" + arr + "&ini=" + ini + "&fin=" + fin;
	var win = window.open("./recibo_renta.php?" + opt, "rec", "toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=800,height=600");
	win.focus();
}
-->
</script>
<!-- END BLOCK : impresion -->
</body>
</html>
