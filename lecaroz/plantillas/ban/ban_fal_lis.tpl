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
<td align="center" valign="middle"><p class="title">Listado de Faltantes de Cometra</p>
  <form action="./ban_fal_lis.php" method="get" name="form"><input name="tmp" id="tmp" type="hidden" value=""><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Tipo de listado </th>
      <td class="vtabla"><input name="tipo" type="radio" value="todas" checked onClick="idadministrador.disabled=true">
        Todas<br>
        <input name="tipo" type="radio" value="admin" onClick="idadministrador.disabled=false">
        Administrador<br>
        <input name="tipo" type="radio" value="aclarados" onClick="idadministrador.disabled=true">
        Aclarados<br>
        <input name="tipo" type="radio" value="memo" onClick="idadministrador.disabled=false">
        Memorandums</td>
    </tr>
  </table>  
    <br>
    <table class="tabla">
      <tr>
        <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
        <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3">
          <input name="nombre_cia" type="text" disabled class="vnombre" id="nombre_cia" size="30"></td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Administrador</th>
        <td class="vtabla"><select name="idadministrador" disabled="disabled" class="insert" id="idadministrador">
          <option selected></option>
		  <!-- START BLOCK : administrador -->
		  <option value="{idadministrador}">{nombre}</option>
		  <!-- END BLOCK : administrador -->
        </select></td>
      </tr>
    </table>
    <p>
    <input type="submit" class="boton" value="Siguiente">
  </p></form></td>
</tr>
</table>

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript">
<!--
window.addEvent('domready', function() {
	$('num_cia').addEvents({
		'focus': function() {
			$('tmp').set('value', this.value);
			this.select();
		},
		'change': function() {
			if (this.get('value') == 0 || this.get('value') == '') {
				$('num_cia').set('value', '');
				$('nombre_cia').set('value', '');
			}
			else {
				new Request({
					'url': 'ban_fal_lis.php',
					'data': 'c=' + $('num_cia').get('value'),
					'onSuccess': function(result) {
						if (result == '') {
							alert('La compañía no se encuentra en el catálogo');
							$('num_cia').set('value', $('tmp').get('value'));
							$('num_cia').select();
						}
						else {
							$('nombre_cia').set('value', result);
							$('num_cia').select();
						}
					}
				}).send();
			}
		},
		'keydown': function(e) {
			if (e.key == 'enter') {
				this.blur();
				e.stop();
			}
		}
	});
});
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center"><font size="+2">Faltantes de Cometra</font> </td>
    <td width="20%" align="right"><font face="Geneva, Arial, Helvetica, sans-serif" size="-1">{fecha}</font></td>
  </tr>
</table>
  <br>
  <!-- START BLOCK : encargado -->
  <table align="center" class="tabla">
  <tr>
    <th class="tabla" scope="col"><font size="+2">{encargado}</font></th>
  </tr>
</table>
  <br>
  <!-- END BLOCK : encargado -->
  <table width="70%" align="center" class="print">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="6" class="print" scope="col"><font size="+1">{num_cia} - {nombre_cia}</font> </th>
    </tr>
    <tr>
      <th width="10%" class="print">Fecha</th>
      <th width="15%" class="print">Comprobante</th>
      <th width="15%" class="print">Dep&oacute;sito</th>
	  <th width="15%" class="print">Faltante</th>
      <th width="15%" class="print">Sobrante</th>
      <th width="30%" class="print">Descripci&oacute;n</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{fecha}</td>
      <td class="rprint">{importe_comprobante}</td>
	  <td class="rprint">{deposito}</td>
      <td class="rprint"><font color="#0000FF">{faltante}</font></td>
      <td class="rprint"><font color="#FF0000">{sobrante}</font></td>
      <td class="vprint">{descripcion}</td>
    </tr>
	<!-- END BLOCK : fila -->
    <!-- START BLOCK : totales -->
	<tr>
      <th class="rprint">Totales</th>
      <th class="rprint_total">{importe_comprobante}</th>
      <th class="rprint_total">{deposito}</th>
	  <th class="rprint_total">{faltante}</th>
      <th class="rprint_total">{sobrante}</th>
      <th class="print">&nbsp;</th>
    </tr>
	<tr>
	  <th colspan="3" class="rprint">Diferencia</th>
	  <th colspan="2" class="print_total">{diferencia} &#8212; {tipo} </th>
	  <th class="print">&nbsp;</th>
    </tr>
	<!-- END BLOCK : totales -->
    <tr>
      <td colspan="6">&nbsp;</td>
    </tr>
	<!-- END BLOCK : cia -->
</table>
<!-- START BLOCK : email -->
<p align="center">
<input name="email" type="button" value="Enviar email a panaderías" onClick="document.location='ban_fal_lis.php?email=1&num_cia={num_cia}'">
</p>
<!-- END BLOCK : email -->
  <!-- START BLOCK : mensaje -->
<p align="center" style="font-family:Arial, Helvetica, sans-serif;font-size:18pt;">Sr. Administrador, tiene usted dep&oacute;sitos sin aclarar con {dias_retraso} d&iacute;as de retraso.</p>
  <!-- END BLOCK : mensaje -->
  <!-- START BLOCK : pasteles -->
  <table align="center" class="print">
  <tr>
    <th colspan="3" class="print" style="font-size:10pt" scope="col">Blocks de Pastel Terminados</th>
    </tr>
  <!-- START BLOCK : block -->
  <tr>
    <td class="vprint">{num_cia} {nombre}</td>
    <td class="print" width="30">{blocks}</td>
    <td class="vprint">{folios}</td>
  </tr>
  <!-- END BLOCK : block -->
</table>
<br>
<!-- END BLOCK : pasteles -->
<!-- START BLOCK : nominas -->
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" style="font-size:11pt" scope="col">Nominas Pendientes</th>
  </tr>
  <!-- START BLOCK : nom -->
  <tr>
    <td class="vprint">{num_cia} {nombre}</td>
    <td class="vprint">{nominas}</td>
  </tr>
  <!-- END BLOCK : nom -->
</table>
<br>
<!-- END BLOCK : nominas -->
<!-- START BLOCK : infonavit -->
<table align="center" class="print">
  <tr>
    <th colspan="{colspan}" class="print" style="font-size:11pt" scope="col">Pendientes Infonavit</th>
  </tr>
  <tr>
    <td colspan="{colspan}" class="vprint" scope="col">&nbsp;</td>
  </tr>
  <!-- START BLOCK : cia_inf -->
  <tr>
    <th colspan="{colspan}" class="vprint" scope="col">{num_cia} {nombre} </th>
  </tr>
  <tr>
    <th class="print">Empleado</th>
    <!-- START BLOCK : column_name -->
	<th class="print">{mes}</th>
	<!-- END BLOCK : column_name -->
  </tr>
  <!-- START BLOCK : row -->
  <tr>
    <td class="vprint">{num} {nombre} </td>
    <!-- START BLOCK : cel -->
	<td class="rprint">{importe}</td>
	<!-- END BLOCK : cel -->
  </tr>
  <!-- END BLOCK : row -->
  <tr>
    <th colspan="{colspan_total}" class="rprint">Total Compañía </th>
	<th class="rprint_total">{total}</th>
  </tr>
  <tr>
    <td colspan="{colspan}" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia_inf -->
  <tr>
    <th colspan="{colspan_total}" class="rprint">Total General </th>
	<th class="rprint_total">{total}</th>
  </tr>
</table>
<br>
<!-- END BLOCK : infonavit -->
<!-- START BLOCK : dep_min -->
<table width="30%" align="center" class="print">
    <tr>
      <th colspan="3" class="print" style="font-size:11pt" scope="col">Depósitos Menores al Promedio Mensual</th>
    </tr>
	<!-- START BLOCK : cia_dep_min -->
	<tr>
      <th colspan="3" class="vprint" scope="col">{num_cia} {nombre_cia}</th>
    </tr>
    <tr>
      <th width="20%" class="print">D&iacute;a</th>
      <th width="40%" class="print">Efectivo</th>
      <th width="40%" class="print">Dep&oacute;sito</th>
    </tr>
    <!-- START BLOCK : dia_dep_min -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{dia}</td>
      <td class="rprint">{efectivo}</td>
      <td class="rprint">{deposito}</td>
	</tr>
	<!-- END BLOCK : dia_dep_min -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="2" class="print">Promedio mensual </th>
	  <th class="rprint">{promedio}</th>
	</tr>
	<tr>
	  <td colspan="8" class="print">&nbsp;</td>
    </tr>
	<!-- END BLOCK : cia_dep_min -->
</table>
<!-- END BLOCK : dep_min -->
<!-- START BLOCK : salto -->
  <br style="page-break-after:always;">
  <!-- END BLOCK : salto -->
<!-- END BLOCK : listado -->
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
<p align="justify">&nbsp;&nbsp;&nbsp;&nbsp;POR MEDIO DEL PRESENTE ME PERMITO SALUDARLE Y A SU VEZ SOLICITARLE LA ACLARACI&Oacute;N DE LOS SIGUIENTES ERRORES QUE SURGIERON EN SUS DEP&Oacute;SITOS:</p>
<table width="70%" align="center" class="print">
    <tr>
      <th width="10%" class="print">Fecha</th>
      <th width="20%" class="print">Dep&oacute;sito</th>
	  <th width="20%" class="print">Faltante</th>
      <th width="20%" class="print">Sobrante</th>
      <th width="30%" class="print">Descripci&oacute;n</th>
    </tr>
    <!-- START BLOCK : fila_memo -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{fecha}</td>
	  <td class="rprint">{deposito}</td>
      <td class="rprint"><font color="#0000FF">{faltante}</font></td>
      <td class="rprint"><font color="#FF0000">{sobrante}</font></td>
      <td class="vprint">{descripcion}</td>
    </tr>
	<!-- END BLOCK : fila_memo -->
    <!-- START BLOCK : totales_memo -->
	<tr>
      <th class="rprint">Totales</th>
      <th class="rprint_total">{deposito}</th>
	  <th class="rprint_total">{faltante}</th>
      <th class="rprint_total">{sobrante}</th>
      <th class="print">&nbsp;</th>
    </tr>
	<tr>
	  <th colspan="2" class="rprint">Diferencia</th>
	  <th colspan="2" class="print_total">{diferencia} &#8212; {tipo} </th>
	  <th class="print">&nbsp;</th>
    </tr>
	<!-- END BLOCK : totales_memo -->
</table>
<!--<p align="justify">&nbsp;&nbsp;&nbsp;&nbsp;SE OBSERVA QUE USTED TIENE DIFERENCIAS CON <strong>{dias_retraso} D&Iacute;AS DE ATRASO</strong> A ESTA FECHA Y QUE NO HAN SIDO ACLARADOS TODAV&Iacute;A.</p>
<p align="justify">&nbsp;&nbsp;&nbsp;&nbsp;POR FAVOR {texto} Y LE SOLICITO TENER UN POCO M&Aacute;S DE <strong>CUIDADO</strong> AL ELABORAR SUS DEP&Oacute;SITOS, PUESTO QUE ESTOS ERRORES LE REPRESENTAN UNA P&Eacute;RDIDA DE TIEMPO INNECESARIO, EL CUAL USTED PODR&Iacute;A OCUPAR EN COSAS M&Aacute;S PRODUCTIVAS PARA SU PANIFICADORA. </p>
<p align="justify">&nbsp;&nbsp;&nbsp;&nbsp;SIN M&Aacute;S POR EL MOMENTO ME DESPIDO DE USTED.</p>-->
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
<!-- START BLOCK : no_result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p><font face="Geneva, Arial, Helvetica, sans-serif" size="+1">No hay resultados</font></p>
  <p><font size="+1" face="Geneva, Arial, Helvetica, sans-serif">
    <input name="Button" type="button" class="boton" value="Regresar" onClick="document.location='./ban_fal_lis.php'">
  </font></p></td>
</tr>
</table>
<!-- END BLOCK : no_result -->
</body>
</html>
