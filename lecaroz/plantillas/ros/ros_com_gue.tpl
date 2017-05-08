<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Compras de Pollo por Periodo </p>
  <form action="./ros_com_gue.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a(s)</th>
      <td class="vtabla"><input name="cias" type="text" class="vinsert" id="cias" onfocus="tmp.value=this.value;this.select()" onchange="rango(this)" onkeydown="if (event.keyCode == 13) omitir.select()" size="50" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Omitir</th>
      <td class="vtabla"><input name="omitir" type="text" class="vinsert" id="omitir" style="width:98%;" onfocus="tmp.value=this.value;this.select();" onchange="rango(this)" onkeydown="if (event.keyCode==13)fecha1.select()" value="304,307,330,337,340,342,349,351,358,360,365,366,368,369,371,384,386,387,394,400,405,407,408,410" /></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
		<!-- START BLOCK : admin -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select>      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><select name="num_pro" class="insert" id="num_pro">
        <option value="" selected="selected"></option>
		    <option value="13">13 POLLOS GUERRA</option>
		    <option value="482">482 CENTRAL DE POLLOS Y CARNES S.A. DE C.V.</option>
        <option value="204">204 GONZALEZ AYALA JOSE REGINO</option>
        <option value="2112">2112 GONZALEZ AYALA JOSE REGINO</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) cias.select()" value="{fecha2}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked="checked" />
        Total
          <input name="tipo" type="radio" value="2" />
          Semanal</td>
    </tr>
    <tr>
    	<th class="vtabla" scope="row">Consultar</th>
    	<td class="vtabla"><input name="consultar" type="radio" id="consultar" value="compras" checked="checked" />
    		Compras
    			<input type="radio" name="consultar" id="consultar" value="ventas" />
    			Ventas</td>
    	</tr>
    <tr>
      <th class="vtabla" scope="row">Productos</th>
      <td class="vtabla"><input name="codmp[]" type="checkbox" id="codmp" value="160" checked="checked" />
        Pollo Normal<br />
        <input name="codmp[]" type="checkbox" id="codmp" value="600" checked="checked" />
        Pollo Chico<br />
        <input name="codmp[]" type="checkbox" id="codmp" value="700" checked="checked" />
        Pollo Grande<br />
        <input name="codmp[]" type="checkbox" id="codmp" value="297" checked="checked" />
        Pescuezos<br />
        <input name="codmp[]" type="checkbox" id="codmp" value="363" checked="checked" />
        Alas<br />
        <input name="codmp[]" type="checkbox" id="codmp" value="352" checked="checked" />
        Pierna  <br />
        <input name="codmp[]" type="checkbox" id="codmp" value="573" checked="checked" />
        Pollo marinado <br />
        <input name="codmp[]" type="checkbox" id="codmp" value="434" checked="checked" />
        Ala adobada<br />
        <input name="codmp[]" type="checkbox" id="codmp" value="869" checked="checked" />
        Nuggets de pollo</td>
    </tr>
  </table>
    <p>
    <input type="button" class="boton" onclick="validar()" value="Siguiente" />
    </p></form></td>
</tr>
</table>
<!-- START IGNORE -->
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function rango(el) {//console.log(el.value.match(/([0-9]{1,}(-(?=[0-9])[0-9]{1,})?)/g));
	el.value = el.value.match(/([0-9]{1,}(-(?=[0-9])[0-9]{1,})?)/g);
	return false;
}

function validar() {
	var cont = 0;
	for (var i = 0; i < f.codmp.length; i++)
		cont += f.codmp[i].checked ? 1 : 0;

	if (f.fecha1.value.length < 8) {
		alert('Debe especificar la fecha de inicio');
		f.fecha1.select();
		return false;
	}
	else if (f.fecha2.value.length < 8) {
		alert('Debe especificar la fecha de termino');
		f.fecha2.select();
		return false;
	}
	else if (cont == 0) {
		alert('Debe seleccionar al menos un producto');
		return false;
	}
	else
		f.submit();
}

window.onload = function () { showAlert = true; f.cias.select();};
//-->
</script>
<!-- END IGNORE -->
<!-- END BLOCK : datos -->
<!-- START BLOCK : total -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Compras a Pollos Guerra<br />
      del {fecha1} al {fecha2} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Producto</th>
    <th class="print" scope="col">Cantidad</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{codmp} {nombre} </td>
    <td class="rprint">{cantidad}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<!-- START BLOCK : back_total -->
<style type="text/css" media="print">
#boton {
	display: none;
}
</style>
<div id="boton">
<p align="center">
<input type="button" class="boton" value="Regresar" onclick="document.location='./ros_com_gue.php'">
</p>
</div>
<!-- END BLOCK : back_total -->
<!-- END BLOCK : total -->
<!-- START BLOCK : semanal -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Compras a Pollos Guerra<br />
del {fecha1} al {fecha2} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Producto</th>
    <th class="print" scope="col">Lunes</th>
    <th class="print" scope="col">Martes</th>
    <th class="print" scope="col">Miercoles</th>
    <th class="print" scope="col">Jueves</th>
    <th class="print" scope="col">Viernes</th>
    <th class="print" scope="col">Sabado</th>
    <th class="print" scope="col">Domingo</th>
    <th class="print" scope="col">Total</th>
  </tr>
  <!-- START BLOCK : pro -->
  <tr>
    <td class="vprint">{codmp} {nombre} </td>
    <td class="rprint">{cantidad1}</td>
    <td class="rprint">{cantidad2}</td>
    <td class="rprint">{cantidad3}</td>
    <td class="rprint">{cantidad4}</td>
    <td class="rprint">{cantidad5}</td>
    <td class="rprint">{cantidad6}</td>
    <td class="rprint">{cantidad0}</td>
    <td class="rprint" style="font-weight:bold;">{total}</td>
  </tr>
  <!-- END BLOCK : pro -->
  <tr>
    <th class="rprint">Totales</th>
    <th class="rprint_total">{total1}</th>
    <th class="rprint_total">{total2}</th>
    <th class="rprint_total">{total3}</th>
    <th class="rprint_total">{total4}</th>
    <th class="rprint_total">{total5}</th>
    <th class="rprint_total">{total6}</th>
    <th class="rprint_total">{total0}</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<!-- START BLOCK : back_semanal -->
<style type="text/css" media="print">
#boton {
	display: none;
}
</style>
<div id="boton">
<p align="center">
<input type="button" class="boton" value="Regresar" onclick="document.location='./ros_com_gue.php'">
</p>
</div>
<!-- END BLOCK : back_semanal -->
<!-- END BLOCK : semanal -->
</body>
</html>
