<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
<style type="text/css" media="print">
.boton {
	display: none;
}
</style>
<link href="./styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Mes</th>
  </tr>
  <tr>
    <td class="print" style="font-size:14pt; font-weight:bold;">{num_cia} - {nombre_cia}</td>
    <td class="print" style="font-size:14pt; font-weight:bold;">{mes_escrito}</td>
  </tr>
  <tr>
    <th class="print" scope="col">Encargado</th>
    <th class="print" scope="col">Operadora</th>
  </tr>
  <tr>
    <td class="print"><strong><font color="#0000FF">{encargado} </font></strong></td>
    <td class="print"><strong><font color="#0000FF">{operadora}</font></strong></td>
  </tr>
</table>
<!-- START BLOCK : tabla -->
<br />
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">D&iacute;a</th>
    <th class="print" scope="col">Efectivo</th>
    <!-- START BLOCK : num_dep -->
    <th class="print" scope="col">Dep&oacute;sito {num_dep}</th>
    <!-- END BLOCK : num_dep -->
    <!-- START BLOCK : num_tar -->
    <th class="print" scope="col">Tarjeta {num_tar}</th>
    <!-- END BLOCK : num_tar -->
    <th class="print" scope="col"><font color="#FFFF00">Otros dep&oacute;sitos</font></th>
    <th class="print" scope="col">Diferencia</th>
    <th class="print" scope="col">Total Dep&oacute;sitos</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
    <th class="print">{dia}</th>
    <td class="print" {bgcolor}><strong>{font1}{efectivo}{font2}</strong></td>
    <!-- START BLOCK : depositos -->
    <td class="print" {bgcolor} {color}><strong>{deposito}</strong></td>
    <!-- END BLOCK : depositos -->
    <!-- START BLOCK : tarjetas -->
    <td class="print" {bgcolor} {color}><strong>{tarjeta}</strong></td>
    <!-- END BLOCK : tarjetas -->
    <td class="print"><strong>{otro_deposito}</strong></td>
    <td class="print"><strong><font color="#{dif_color}">{diferencia}</font></strong></td>
    <th class="print">{total}</th>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="print">Totales</th>
    <th class="print" style="font-size:12pt;">{total_efectivos}</th>
    <!-- START BLOCK : total_depositos -->
    <th class="print" style="font-size:12pt;">{total_depositos}</th>
    <!-- END BLOCK : total_depositos -->
    <!-- START BLOCK : total_tarjetas -->
    <th class="print" style="font-size:12pt;">{total_tarjetas}</th>
    <!-- END BLOCK : total_tarjetas -->
    <th class="print" style="font-size:12pt; color:#FF0">{total_otros_depositos}</th>
    <th class="print" style="font-size:12pt;">{total_diferencias}</th>
    <th class="print" style="font-size:12pt;">{gran_total}</th>
  </tr>
  <tr>
    <th class="print">Porcentajes</th>
    <th class="print">&nbsp;</th>
    <!-- START BLOCK : por_dep -->
    <th class="print" style="font-size:12pt; color:#00C">{por_dep}</th>
    <!-- END BLOCK : por_dep -->
    <!-- START BLOCK : por_tar -->
    <th class="print" style="font-size:12pt; color:#00C">{por_tar}</th>
    <!-- END BLOCK : por_tar -->
    <th class="print" style="font-size:12pt; color:#900">{por_otros}%</th>
    <th class="print">&nbsp;</th>
    <th class="print">&nbsp;</th>
  </tr>
  <tr>
    <th class="print">Promedios</th>
    <th class="print" style="font-size:12pt;">{promedio_efectivos}</th>
    <!-- START BLOCK : promedio_depositos -->
    <th class="print" style="font-size:12pt;">{promedio_depositos}</th>
    <!-- END BLOCK : promedio_depositos -->
    <!-- START BLOCK : promedio_tarjetas -->
    <th class="print" style="font-size:12pt; color:#3333CC">{promedio_tarjetas}</th>
    <!-- END BLOCK : promedio_tarjetas -->
    <th class="print" style="font-size:12pt;">{promedio_otros_depositos}</th>
    <th class="print">&nbsp;</th>
    <th class="print" style="font-size:12pt;">{promedio_total}</th>
  </tr>
</table>
<!-- END BLOCK : tabla -->
<p align="center" class="boton">
  <input type="button" class="boton" onclick="imp()" value="Imprimir" />
</p>
<script language="javascript" type="text/javascript">
<!--
function imp() {
	window.print();
	self.close();
}
//-->
</script>
</body>
</html>
