<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Rentas Pagadas</p>
  <form action="./ban_ren_pag.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) next.focus()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Local</th>
      <td class="vtabla"><input name="local" type="text" class="vinsert" id="local" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if(event.keyCode==13)anio.select()" size="4" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Solo pendientes</th>
      <td class="vtabla"><input name="pen" type="checkbox" id="pen" value="1" /></td>
    </tr>
  </table>  
  <p>
    <input name="next" type="button" class="boton" id="next" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.anio) <= 0)
		alert('Debe especificar el año');
	else
		f.submit();
}

window.onload = f.anio.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : especial -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Rentas {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <tr>
    <th class="print" scope="col">Local</th>
    <th class="print" scope="col">Arrendatario</th>
    <th class="print" scope="col">Giro</th>
    <th class="print" scope="col">Jul{ant}</th>
    <th class="print" scope="col">Ago{ant}</th>
    <th class="print" scope="col">Sep{ant}</th>
    <th class="print" scope="col">Oct{ant} </th>
    <th class="print" scope="col">Nov{ant}</th>
    <th class="print" scope="col">Dic{ant}</th>
    <th class="print" scope="col">Ene</th>
    <th class="print" scope="col">Feb</th>
    <th class="print" scope="col">Mar</th>
    <th class="print" scope="col">Abr</th>
    <th class="print" scope="col">May</th>
    <th class="print" scope="col">Jun</th>
    <th class="print" scope="col">Jul</th>
    <th class="print" scope="col">Ago</th>
    <th class="print" scope="col">Sep</th>
    <th class="print" scope="col">Oct</th>
    <th class="print" scope="col">Nov</th>
    <th class="print" scope="col">Dic</th>
  </tr>
  <!-- START BLOCK : fila_esp -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num} {local}</td>
    <td class="vprint" onclick="detalle({id})" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'">{arr} {nombre} </td>
    <td class="vprint">{giro}</td>
    <td class="print">{mes_ant_7}</td>
    <td class="print">{mes_ant_8}</td>
    <td class="print">{mes_ant_9}</td>
    <td class="print">{mes_ant_10}</td>
    <td class="print">{mes_ant_11}</td>
    <td class="print">{mes_ant_12}</td>
    <td class="print">{mes1}</td>
    <td class="print">{mes2}</td>
    <td class="print">{mes3}</td>
    <td class="print">{mes4}</td>
    <td class="print">{mes5}</td>
    <td class="print">{mes6}</td>
    <td class="print">{mes7}</td>
    <td class="print">{mes8}</td>
    <td class="print">{mes9}</td>
    <td class="print">{mes10}</td>
    <td class="print">{mes11}</td>
    <td class="print">{mes12}</td>
  </tr>
  <!-- END BLOCK : fila_esp -->
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : especial -->
<!-- START BLOCK : scripts -->
<script language="javascript" type="application/javascript">
<!--
function detalle(id) {
	var win;
	
	win = window.open('ban_ren_pag.php?id=' + id, '', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=200');
	
}
//-->
</script>
<!-- END BLOCK : scripts -->
<!-- START BLOCK : detalle -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Arrendador</th>
    <td class="vtabla">{nombre_arrendatario}</td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Representante</th>
    <td class="vtabla">{representante}</td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Contacto</th>
    <td class="vtabla">{contacto}</td>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Tel&eacute;fono</th>
    <td class="vtabla">{telefono}</td>
  </tr>
</table>
  <p>
    <input name="" type="button" class="boton" onclick="self.close()" value="Cerrar" />
  </p></td>
</tr>
</table>
<!-- END BLOCK : detalle -->
</body>
</html>
