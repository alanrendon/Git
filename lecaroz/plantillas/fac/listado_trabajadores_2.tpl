<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Personal{puesto}</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : listado -->
<table width="100%" align="center">
  <tr>
    <td class="print_encabezado">{cia}</td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td align="right" class="print_encabezado">{cia}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Cat&aacute;logo de Personal{puesto}{leyenda}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <!-- START BLOCK : titles -->
  <tr>
    <!-- START BLOCK : cia_title -->
	<th colspan="2" class="print" scope="col">Compa&ntilde;ia</th>
	<!-- END BLOCK : cia_title -->
	<th class="print" scope="col">Con.</th>
    <th class="print" scope="col">No.</th>
	<th class="print" scope="col">Nombre</th>
    <!-- START BLOCK : puesto_title -->
    <th class="print" scope="col">Puesto</th>
    <!-- END BLOCK : puesto_title -->
    <!-- START BLOCK : turno_title -->
    <th class="print" scope="col">Turno</th>
    <!-- END BLOCK : turno_title -->
    <!-- START BLOCK : antiguedad_title -->
    <th class="print" scope="col">Antig&uuml;edad</th>
    <!-- END BLOCK : antiguedad_title -->
    <!-- START BLOCK : agu_ant_title -->
    <th class="print" scope="col">Aguinaldo Ant. </th>
    <!-- END BLOCK : agu_ant_title -->
	<!-- START BLOCK : status_ant_title -->
    <th class="print" scope="col">&nbsp;</th>
    <!-- END BLOCK : status_ant_title -->
    <!-- START BLOCK : agu_act_title -->
    <th class="print" scope="col">Aguinaldo</th>
    <!-- END BLOCK : agu_act_title -->
    <!-- START BLOCK : status_title -->
    <th class="print" scope="col">&nbsp;</th>
    <!-- END BLOCK : status_title -->
    <!-- START BLOCK : notes_title -->
    <th class="print" scope="col">Anotaciones</th>
    <!-- END BLOCK : notes_title -->
  </tr>
  <!-- END BLOCK : titles -->
  <!-- START BLOCK : fila -->
  <tr>
   <!-- START BLOCK : cia -->
    <td class="vprint" style="font-size:{fontsize} ">{num_cia}</td>
    <td class="vprint" style="font-size:{fontsize} ">{nombre_cia}</td>
	<!-- END BLOCK : cia -->
	<td class="vprint" style="font-size:{fontsize}; font-weight: bolder;">{consecutivo}</td>
	<td class="vprint" style="font-size:{fontsize} ">{num_emp}</td>
    <td class="vprint" style="font-size:{fontsize} ">{nombre}</td>
    <!-- START BLOCK : puesto -->
    <td class="vprint" style="font-size:{fontsize} ">{puesto}</td>
    <!-- END BLOCK : puesto -->
    <!-- START BLOCK : turno -->
    <td class="vprint" style="font-size:{fontsize} ">{turno}</td>
    <!-- END BLOCK : turno -->
    <!-- START BLOCK : antiguedad -->
    <td class="vprint" style="font-size:{fontsize} ">{antiguedad}</td>
    <!-- END BLOCK : antiguedad -->
    <!-- START BLOCK : agu_ant -->
    <td class="rprint" style="font-size:{fontsize} ">{agu_ant}</td>
    <!-- END BLOCK : agu_ant -->
	<!-- START BLOCK : status_ant -->
    <td class="print" style="font-size:{fontsize} ">{status_ant}</td>
    <!-- END BLOCK : status_ant -->
    <!-- START BLOCK : agu_act -->
    <td class="rprint" style="font-size:{fontsize} ">{agu_act}</td>
    <!-- END BLOCK : agu_act -->
    <!-- START BLOCK : status -->
    <td class="print" style="font-size:{fontsize} ">{status}</td>
    <!-- END BLOCK : status -->
    <!-- START BLOCK : notes -->
    <td class="vprint">&nbsp;</td>
    <!-- END BLOCK : notes -->
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : totales_separados -->
  <tr>
    <th class="rprint" style="font-size:{fontsize} " colspan="{totales_colspan}">Total Panader&iacute;a </th>
    <!-- START BLOCK : total_agu_ant_pan -->
    <th class="rprint_total" style="font-size:{fontsize} ">{total_agu_ant_pan}</th>
    <!-- END BLOCK : total_agu_ant_pan -->
	<!-- START BLOCK : relleno_status_ant_pan -->
	<th class="print">&nbsp;</th>
	<!-- END BLOCK : relleno_status_ant_pan -->
    <!-- START BLOCK : total_agu_act_pan -->
    <th class="rprint_total" style="font-size:{fontsize} ">{total_agu_act_pan}</th>
    <!-- END BLOCK : total_agu_act_pan -->
    <!-- START BLOCK : relleno_pan -->
    <th class="print" colspan="{relleno_colspan}">&nbsp;</th>
    <!-- END BLOCK : relleno_pan -->
  </tr>
  <tr>
    <th class="rprint" style="font-size:{fontsize} " colspan="{totales_colspan}">Total Rosticer&iacute;a</th>
    <!-- START BLOCK : total_agu_ant_ros -->
    <th class="rprint_total" style="font-size:{fontsize} ">{total_agu_ant_ros}</th>
    <!-- END BLOCK : total_agu_ant_ros -->
	<!-- START BLOCK : relleno_status_ant_ros -->
	<th class="print">&nbsp;</th>
	<!-- END BLOCK : relleno_status_ant_ros -->
    <!-- START BLOCK : total_agu_act_ros -->
    <th class="rprint_total" style="font-size:{fontsize} ">{total_agu_act_ros}</th>
    <!-- END BLOCK : total_agu_act_ros -->
    <!-- START BLOCK : relleno_ros -->
    <th class="print" colspan="{relleno_colspan}">&nbsp;</th>
    <!-- END BLOCK : relleno_ros -->
  </tr>
  <!-- END BLOCK : totales_separados -->
  <!-- START BLOCK : totales -->
  <tr>
    <th class="rprint" style="font-size:{fontsize} " colspan="{totales_colspan}">Gran Total</th>
    <!-- START BLOCK : total_agu_ant -->
    <th class="rprint_total" style="font-size:{fontsize} ">{total_agu_ant}</th>
    <!-- END BLOCK : total_agu_ant -->
	<!-- START BLOCK : relleno_status_ant -->
	<th class="print">&nbsp;</th>
	<!-- END BLOCK : relleno_status_ant -->
    <!-- START BLOCK : total_agu_act -->
    <th class="rprint_total" style="font-size:{fontsize} ">{total_agu_act}</th>
    <!-- END BLOCK : total_agu_act -->
    <!-- START BLOCK : relleno -->
    <th class="print" colspan="{relleno_colspan}">&nbsp;</th>
    <!-- END BLOCK : relleno -->
  </tr>
  <!-- END BLOCK : totales -->
</table>
<!-- START BLOCK : desglose -->
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Total de Billetes </th>
    <th class="print" scope="col">Totales</th>
  </tr>
  <!-- START BLOCK : den -->
  <tr>
    <td class="vprint" style="font-size:10pt "><strong>{cantidad}</strong> billetes de <strong>{denominacion}</strong> </td>
    <td class="rprint" style="font-size:10pt ">{importe}</td>
  </tr>
  <!-- END BLOCK : den -->
  <tr>
    <th class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<!-- END BLOCK : desglose -->
<!-- START BLOCK : salto -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto -->
<!-- END BLOCK : listado -->
<script language="javascript" type="text/javascript">window.onload=window.focus();</script>
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
