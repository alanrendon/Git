<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}
.style1 {
	font-size: 36px;
	font-weight: bold;
}
.style2 {
	font-size: 24px;
	font-weight: bold;
}
-->
</style></head>

<body>
<!-- START BLOCK : recibo -->
<p align="right" class="style2">M&Eacute;XICO, D.F., A {dia} DE {mes} DE {anio} </p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p align="center" class="style2">BUENO POR $ {importe_gasto} </p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p align="center" class="style1">RECIBO</p>
<p>&nbsp;</p>
<table width="70%" align="center">
  <tr>
    <td align="justify">RECIBI DE <strong>{nombre_cia}</strong> LA CANTIDAD DE <strong>${total} ({total_escrito})</strong> POR CONCEPTO DE PAGO DE <strong>{gasto}</strong> DEL MES DE <strong>{mes_cheque}</strong> DEL <strong>{anio_cheque}</strong>, QUE A CONTINUACI&Oacute;N SE DETALLA:</td>
  </tr>
</table>
<p>&nbsp;</p>
<table align="center">
  <tr>
    <td><strong>{gasto}:</strong></td>
    <td align="right"><strong>{importe_gasto}</strong></td>
  </tr>
  <!-- START BLOCK : imp -->
  <tr>
    <td><strong>{imp}:</strong></td>
    <td align="right"><strong>{importe_imp}</strong></td>
  </tr>
  <!-- END BLOCK : imp -->
  <tr>
    <td><strong>Total:</strong></td>
    <td align="right"><strong>{total}</strong></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><strong>CHEQUE NO.: {folio}</strong></td>
    <td><strong>BANCO: {banco}</strong></td>
  </tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p align="center"><strong>RECIB&Iacute; DE CONFORMIDAD</strong></p>
<p>&nbsp;</p>
<p align="center">_________________________________________________________________________<br>
  <span class="style2">{a_nombre}</span> 
</p>
<br style="page-break-after:always;">
<!-- END BLOCK : recibo -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
