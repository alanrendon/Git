<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Listado de Revisiones Vehiculares </p>
  <form action="./doc_cam_rev_con.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">No. Camioneta </th>
      <td class="vtabla"><input name="idcamioneta" type="text" class="insert" id="idcamioneta" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) idcamioneta.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Color</th>
      <td class="vtabla"><span class="vtabla"><select name="color" class="insert" id="color">
        <option selected>TODAS</option>
        <option value="1">AMARILLO</option>
        <option value="2">ROSA</option>
        <option value="3">ROJO</option>
        <option value="4">VERDE</option>
        <option value="5">AZUL</option>
      </select></span></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Revisiones Vehiculares del A&ntilde;o {anio}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="100%" align="center" class="print">
    <tr>
      <th class="print" scope="col">No.</th>
      <th class="print" scope="col">Modelo</th>
      <th class="print" scope="col">A&ntilde;o</th>
      <th class="print" scope="col">Placas</th>
      <th class="print" scope="col">Color</th>
      <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">1&ordf; Verificaci&oacute;n </th>
      <th class="print" scope="col">2&ordf; Verificaci&oacute;n</th>
      <th class="print" scope="col">Revista</th>
      <th class="print" scope="col">Tenencia</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{idcamioneta}</td>
      <td class="vprint">{modelo}</td>
      <td class="print">{anio}</td>
      <td class="print">{placas}</td>
      <td class="print">{color}</td>
      <td class="vprint">{num_cia} - {nombre_cia} </td>
      <td class="print">{ver1_ok}{ver1_rec}{ver1_obs}</td>
      <td class="print">{ver2_ok}{ver2_rec}{ver2_obs}</td>
      <td class="print">{rev_ok}{rev_rec}{rev_obs}</td>
      <td class="print">{ten_ok}{ten_rec}{ten_obs}</td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
<!-- END BLOCK : listado -->
</body>
</html>
