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
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Facturas Enviadas a Panader&iacute;as</p>
  <form action="./ban_fac_env_pan.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Folio<br />
        Inicial</th>
      <th class="tabla" scope="col">Folio<br />
        Final</th>
	  <th class="tabla" scope="col">Cantidad</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia({i})" onkeydown="movCursor(event.keyCode,folio_ini[{i}],null,folio_ini[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
        <input name="nombre[]" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
      <td class="tabla"><input name="folio_ini[]" type="text" class="rinsert" id="folio_ini" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) calculaCantidad({i})" onkeydown="movCursor(event.keyCode,folio_fin[{i}],num_cia[{i}],folio_fin[{i}],folio_ini[{back}],folio_ini[{next}])" size="8" /></td>
      <td class="tabla"><input name="folio_fin[]" type="text" class="rinsert" id="folio_fin" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) calculaCantidad({i})" onkeydown="movCursor(event.keyCode,num_cia[{next}],folio_ini[{i}],null,folio_fin[{back}],folio_fin[{next}])" size="8" /></td>
	  <td class="tabla"><input name="cantidad[]" type="text" class="rnombre" id="cantidad" size="5" readonly="true" /></td>
      </tr>
	  <!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Imprimir" onclick="imprimir()" />
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Capturar" onclick="validar()" />
</p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->

function cambiaCia(i) {
	if (f.num_cia[i].value == '' || f.num_cia[i].value == '0') {
		f.num_cia[i].value = '';
		f.nombre[i].value = '';
	}
	else if (cia[get_val(f.num_cia[i])] != null)
		f.nombre[i].value = cia[get_val(f.num_cia[i])];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia[i].value = f.tmp.value;ç
		f.num_cia[i].select();
	}
}

function calculaCantidad(i) {
	if (get_val(f.folio_ini[i]) == 0 || get_val(f.folio_fin[i]) == 0) {
		f.cantidad[i].value = '';
		return false;
	}
	
	var cantidad = get_val(f.folio_fin[i]) - get_val(f.folio_ini[i]) + 1;
	
	if (cantidad <= 0) {
		f.cantidad[i].value = '';
		return false;
	}
	
	f.cantidad[i].value = cantidad;
}

function validar() {
	for (var i = 0; i < f.num_cia.length; i++)
		if (get_val(f.num_cia[i]) > 0 && get_val(f.folio_ini[i]) > 0 && get_val(f.folio_fin[i]) > 0 && get_val(f.cantidad[i]) <= 0) {
			alert('La cantidad de facturas enviadas debe ser mayor a 0');
			f.folio_ini[i].select();
			return false;
		}
	
	if (confirm('¿Son correctos todos los datos?'))
		f.submit();
}

function imprimir() {
	var win = window.open("./ban_fac_env_pan.php?print=1","","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768");
	win.focus();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

window.onload = f.num_cia[0].select();
//-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	alert('No hay cartas por imprimir');
	self.close();
}

window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : carta -->
<style type="text/css">
table {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12pt;
}
</style>
<table width="100%">
  <tr>
    <td height="165" align="left" valign="top" style="font-weight:bold">FOLIO: {folio} </td>
    <td align="right" valign="top" style="font-weight:bold">M&Eacute;XICO D.F., A {dia} DE {mes} DE {anio} </td>
  </tr>
  <tr>
    <td height="98" colspan="2" valign="top"><p><strong>{admin}<br />
    PRESENTE</strong></p>    </td>
  </tr>
  <tr>
    <td colspan="2" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;POR MEDIO DE LA PRESENTE ME PERMITO SALUDARLE Y A LA VEZ INFORMALE QUE ESTA RECIBIENDO POR PARTE DE LA OFICINA LA SIGUIENTE NUMERACI&Oacute;N DE FACTURAS: </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><br />
      <table class="print">
      <tr>
        <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
        <th class="print" scope="col">Inicio</th>
        <th class="print" scope="col">Fin</th>
        <th class="print" scope="col">Cantidad</th>
      </tr>
      <!-- START BLOCK : rango -->
	  <tr>
        <td class="vprint">{num_cia} {nombre} </td>
        <td class="rprint">{ini}</td>
        <td class="rprint">{fin}</td>
        <td class="rprint">{cantidad}</td>
      </tr>
	  <!-- END BLOCK : rango -->
    </table>
    <br /></td>
  </tr>
  <tr>
    <td colspan="2" valign="bottom"><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SR. ADMINISTRADOR, LE RECORDAMOS QUE ESTAS FACTURAS SE TENDRAN QUE HACER DIARIAMENTE Y REVISARLAS POR USTEDES, FIRMARLAS Y VERIFICAR QUE SEA CONSECUTIVA; QUE NO FALTE NINGUNA POR NINGUN MOTIVO. USTED SR. ADMINISTRADOR SERA RESPONSABLE DE TODOS LOS FOLIOS. </p>
      <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SIN MAS POR EL MOMENTO, QUEDO A SUS ORDENES PARA CUALQUIER ACLARACION. </p></td>
  </tr>
  <tr>
    <td height="170" colspan="2" align="center" valign="bottom">__________________________________________________<br />
      <strong>LIC. MIGUEL ANGEL REBUELTA DIEZ </strong></td>
  </tr>
  <tr>
    <td height="43" colspan="2" valign="bottom" style="font-size:6pt;"><strong>C.C.P. JULIAN EUGENIO LARRACHEA ECHENIQUE </strong></td>
  </tr>
</table>
<!--{salto}-->
<br style="page-break-after:always;">
<!-- END BLOCK : carta -->
<!-- START BLOCK : portada -->
<table width="100%">
  <tr>
    <td align="center" valign="middle" style="font-family:Arial, Helvetica, sans-serif;"><p>&nbsp;</p>
    <p style="font-weight:bold; text-decoration:underline; font-size:48pt;">{nombre}</p>
    <p style="font-weight:bold; font-size:48pt;">&quot;{num_cia}&quot;</p>
    <p style="font-weight:bold; font-size:48pt;">{mes} {anio} </p>
    <p style="font-weight:bold; font-size:48pt;">{tipo}</p>
    <p style="font-weight:bold; font-size:48pt;">&nbsp;</p>
    <p style="font-weight:bold; font-size:16pt;">Cantidad: {cantidad} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;De la factura {ini} a la {fin} </p></td>
  </tr>
</table>
<!--{salto}-->
<br style="page-break-after:always;">
<!-- END BLOCK : portada -->
</body>
</html>
