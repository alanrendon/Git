<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body>
<!-- START BLOCK : vale -->
<table width="100%" align="center" style="font-family:Arial, Helvetica, sans-serif; font-weight:bold;">
  <tr>
    <td width="10%">&nbsp;</td>
    <td width="80%" align="center" style="font-size:14pt;">ZAPATERIAS ELITE </td>
    <td width="10%" align="right">{folio}</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center" style="font-size:14pt;">VALE DE DEVOLUCIONES </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">{num_cia} {nombre_cia} </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">{nombre_pro}</td>
    <td>&nbsp;</td>
  </tr>
</table>
<br />
<table width="80%" border="1" align="center" bordercolor="#000000" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; border-collapse:collapse; ">
  <tr>
    <th scope="col">#</th>
    <th scope="col">Modelo</th>
    <th scope="col">Color</th>
    <th scope="col">N&uacute;mero</th>
    <th scope="col">Piezas</th>
    <th scope="col">Precio</th>
    <th scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td align="center">{num}</td>
    <td>{modelo}</td>
    <td>{color}</td>
    <td align="right">{talla}</td>
    <td align="right">{piezas}</td>
    <td align="right">{precio}</td>
    <td align="right">{importe}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="6" align="left">Importe total descontado </th>
    <th align="right">{total}</th>
  </tr>
  <tr>
    <th colspan="6" align="left">Total de no. de pares recibidos </th>
    <th align="right">{total_pares}</th>
  </tr>
  <tr>
    <td colspan="7">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="7">Descontada y/o afectando en el pago </td>
  </tr>
  <tr>
    <td>Cheque:</td>
    <th colspan="2">{cheque} ({fecha})</th>
    <td>Banco:</td>
    <th colspan="3">{banco}</th>
  </tr>
  <tr>
    <td colspan="7">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="7">Nombre y firma del cobrador<br /><br /></td>
  </tr>
  <tr>
    <td colspan="7">Fecha recibida de mercancia<br /><br /></td>
  </tr>
  <tr>
    <td colspan="7">Autorizado<br /><br /><br /></td>
  </tr>
  <tr>
    <th colspan="7" align="justify">No nos hacemos responsables de la mercancia
      en devoluciones, que no recojan al momento
    del pago. </th>
  </tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : vale -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	alert('No hay vales por imprimir');
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
