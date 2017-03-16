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
<td align="center" valign="middle"><p class="title">Compra de Pollo Anual</p>
  <form action="./ros_com_anu.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" value="{num_cia}" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
		<!-- START BLOCK : admin -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><select name="num_pro" class="insert" id="num_pro">
        <option value="" selected="selected"></option>
		    <option value="13">13 POLLOS GUERRA</option>
		    <option value="482">482 CENTRAL DE POLLOS Y CARNES S.A. DE C.V.</option>
        <option value="204">204 GONZALEZ AYALA JOSE REGINO</option>
        <option value="2112">2112 COMERCIALIZADORA DE CARNES DE MEXICO S  DE RL DE CV</option>
        <option value="2112">1757 BOTANAS DEL CARRITO SA DE CV</option>
        <option value="2112">2324 ALIMENTOS ESPECIALIZADOS GALICIA, S.A. DE C.V.</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked>
        Compras
          <input name="tipo" type="radio" value="2">
          Ventas</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Productos</th>
      <td class="vtabla"><input name="codmp" type="radio" checked>
        Todos (solo suma códigos 160, 600, 700 y 573)<br>
        <input name="codmp" type="radio" value="600">
        Pollo Chico<br>
        <input name="codmp" type="radio" value="160">
        Pollo Normal<br>
        <input name="codmp" type="radio" value="700">
        Pollo Grande <br>
        <input name="codmp" type="radio" value="573">
        Pollo Marinado <br>
        <input type="radio" name="codmp" id="radio" value="334">
        Pollo Navide&ntilde;o<br>
        <input name="codmp" type="radio" value="297">
        Pescuezos<br>
        <input name="codmp" type="radio" value="363">
        Alas de pollo<br>
        <input name="codmp" type="radio" value="434">
        Alas marinadas<br>
        <input name="codmp" type="radio" value="300">
        Papas chicas<br>
        <input name="codmp" type="radio" value="301">
        Papas medianas<br>
        <input name="codmp" type="radio" value="302">
        Papas grandes<br>
        <input name="codmp" type="radio" value="1182">
        Papas Lecaroz medianas<br>
        <input name="codmp" type="radio" value="1183">
        Papas Lecaroz grande<br>
        <input name="codmp" type="radio" value="1093">
        Papas totis costeña<br>
        <input name="codmp" type="radio" value="451">
        Sabritas grandes<br>
        <input name="codmp" type="radio" value="452">
        Sabritas medianas<br>
        <input name="codmp" type="radio" value="640">
        Rajas coste&ntilde;a<br>
        <input name="codmp" type="radio" value="644">
        Frijoles coste&ntilde;a grande<br>
        <input name="codmp" type="radio" value="673">
        Frijoles coste&ntilde;a chico<br>
        <input name="codmp" type="radio" value="819">
        Jalape&ntilde;os coste&ntilde;a<br>
        <input name="codmp" type="radio" value="304">
        Rajas<br>
        <input name="codmp" type="radio" value="821">
        Chipotle coste&ntilde;a<br>
        <input name="codmp" type="radio" value="822">
        Chipotle<br>
        <input name="codmp" type="radio" value="641">
        Salsa coste&ntilde;a<br>
        <input name="codmp" type="radio" value="126">
        Bolsa de frijoles coste&ntilde;a</td>
    </tr>
  </table>
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.anio) <= 2000) {
		alert("Debe especificar el año de consulta");
		f.anio.select();
		return false
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : list -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">{tipo} de Pollo {tamanio} Anual<br>
    A&ntilde;o {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <tr>
    <th width="16%" class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th width="6%" class="print" scope="col">Enero</th>
    <th width="6%" class="print" scope="col">Febrero</th>
    <th width="6%" class="print" scope="col">Marzo</th>
    <th width="6%" class="print" scope="col">Abril</th>
    <th width="6%" class="print" scope="col">Mayo</th>
    <th width="6%" class="print" scope="col">Junio</th>
    <th width="6%" class="print" scope="col">Julio</th>
    <th width="6%" class="print" scope="col">Agosto</th>
    <th width="6%" class="print" scope="col">Septiembre</th>
    <th width="6%" class="print" scope="col">Octubre</th>
    <th width="6%" class="print" scope="col">Noviembre</th>
    <th width="6%" class="print" scope="col">Diciembre</th>
    <th width="6%" class="print" scope="col">Promedio</th>
    <th width="6%" class="print" scope="col">Total</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="rprint">{1}</td>
    <td class="rprint">{2}</td>
    <td class="rprint">{3}</td>
    <td class="rprint">{4}</td>
    <td class="rprint">{5}</td>
    <td class="rprint">{6}</td>
    <td class="rprint">{7}</td>
    <td class="rprint">{8}</td>
    <td class="rprint">{9}</td>
    <td class="rprint">{10}</td>
    <td class="rprint">{11}</td>
    <td class="rprint">{12}</td>
    <td class="rprint">{prom}</td>
    <td class="rprint">{total}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="rprint">Totales</th>
    <th class="rprint_total">{1}</th>
    <th class="rprint_total">{2}</th>
    <th class="rprint_total">{3}</th>
    <th class="rprint_total">{4}</th>
    <th class="rprint_total">{5}</th>
    <th class="rprint_total">{6}</th>
    <th class="rprint_total">{7}</th>
    <th class="rprint_total">{8}</th>
    <th class="rprint_total">{9}</th>
    <th class="rprint_total">{10}</th>
    <th class="rprint_total">{11}</th>
    <th class="rprint_total">{12}</th>
    <th class="rprint_total">{prom}</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
{salto}
<!-- START BLOCK : back -->
<style type="text/css" media="print">
#boton {
	display: none;
}
</style>
<div id="boton">
<p align="center">
<input type="button" class="boton" value="Regresar" onClick="document.location='./ros_com_anu.php'">
</p>
</div>
<!-- END BLOCK : back -->
<!-- END BLOCK : list -->
</body>
</html>
