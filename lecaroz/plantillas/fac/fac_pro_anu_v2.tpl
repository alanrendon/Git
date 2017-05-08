<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>

<style type="text/css" media="screen">
.Tip {
  background: #FF9;
  border: solid 1px #000;
  padding: 3px 5px;
}

.tip-title {
  font-weight: bold;
  font-size: 8pt;
  border-bottom: solid 2px #FC0;
  padding: 0 5px 3px 5px;
  margin-bottom: 3px;
}

.tip-text {
  font-weight: bold;
  font-size: 8pt;
  padding: 0 5px;
}
</style>
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Compras Anuales por Proveedor </p>
  <form action="./fac_pro_anu_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp.select()" size="4"></td>
    </tr>
    <tr>
    	<th class="vtabla" scope="row">Tipo</th>
    	<td class="vtabla"><input name="tipo" type="radio" id="tipo1" value="1" checked>
    		Dinero
    			<input type="radio" name="tipo" id="tipo2" value="2">
    			Cantidad</td>
    	</tr>
    <tr>
    	<th class="vtabla" scope="row">Producto</th>
    	<td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3"></td>
    	</tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value=""></option>
		<!-- START BLOCK : admin -->
        <option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla">
        <input name="periodo" type="radio" id="periodo_1" value="1" checked />
        Mes terminado
        <input name="periodo" type="radio" id="periodo_2" value="2" />
        Al d&iacute;a
      </td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.num_pro.value <= 0) {
		alert("Debe especificar el código de proveedor");
		form.num_pro.select();
		return false;
	}
	else if (form.tipo2.checked && form.codmp.value <= 0) {
		alert("Debe especificar el producto");
		form.codmp.select();
		return false;
	}
	else if (form.anio.value <= 2000) {
		alert("Debe especificar el año de consulta");
		form.anio.select();
		return false;
	}
	else
		form.submit();
}

window.onload = document.form.num_pro.select();
-->
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
    <td width="60%" class="print_encabezado" align="center">Compras Anuales por Proveedor del {anio} <br>
      <span style="font-size: 14pt;">{num_pro} {nombre}</span></td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
  <tr>
    <th width="16%" class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th width="6%" class="print" scope="col">Ene</th>
    <th width="6%" class="print" scope="col">Feb</th>
    <th width="6%" class="print" scope="col">Mar</th>
    <th width="6%" class="print" scope="col">Abr</th>
    <th width="6%" class="print" scope="col">May</th>
    <th width="6%" class="print" scope="col">Jun</th>
    <th width="6%" class="print" scope="col">Jul</th>
    <th width="6%" class="print" scope="col">Ago</th>
    <th width="6%" class="print" scope="col">Sep</th>
    <th width="6%" class="print" scope="col">Oct</th>
    <th width="6%" class="print" scope="col">Nov</th>
    <th width="6%" class="print" scope="col">Dic</th>
    <th width="6%" class="print" scope="col">Total</th>
    <th width="6%" class="print" scope="col">Promedio</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre}</td>
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
    <td class="rprint"><a id="tip" style="text-decoration:none;" alt="{fac}">{total}</a></td>
    <td class="rprint">{prom}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : totales -->
  <tr>
    <th class="rprint">Total</th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 1, {num_pro}, {codmp})" style="text-decoration:none;">{1}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 2, {num_pro}, {codmp})" style="text-decoration:none;">{2}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 3, {num_pro}, {codmp})" style="text-decoration:none;">{3}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 4, {num_pro}, {codmp})" style="text-decoration:none;">{4}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 5, {num_pro}, {codmp})" style="text-decoration:none;">{5}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 6, {num_pro}, {codmp})" style="text-decoration:none;">{6}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 7, {num_pro}, {codmp})" style="text-decoration:none;">{7}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 8, {num_pro}, {codmp})" style="text-decoration:none;">{8}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 9, {num_pro}, {codmp})" style="text-decoration:none;">{9}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 10, {num_pro}, {codmp})" style="text-decoration:none;">{10}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 11, {num_pro}, {codmp})" style="text-decoration:none;">{11}</a></th>
    <th class="rprint"><a id="reporte_facturas" href="javascript:reporte_facturas({anio}, 12, {num_pro}, {codmp})" style="text-decoration:none;">{12}</a></th>
    <th class="rprint_total">{total}</th>
    <th class="rprint">&nbsp;</th>
  </tr>
  <!-- END BLOCK : totales -->
</table>
{salto}
<script language="javascript" type="text/javascript">

window.addEvent('domready', function() {
  $$('a[id=tip]').filter(function(el) { return el.get("alt").trim() != ''; }).each(function(el, i) {
    el.store('tip:title', '<img src="imagenes/info.png" /> Informaci&oacute;n');
    el.store('tip:text', el.get('alt'));
  });
  
  tips = new Tips($$('a[id=tip]').filter(function(el) { return el.get("text").trim() != ''; }), {
    'fixed': true,
    'className': 'Tip',
    'showDelay': 50,
    'hideDelay': 50
  });
});

function reporte_facturas(anio, mes, num_pro, codmp) {
	var url = 'ReporteFacturasMateriaPrima.php',
		param = '?anio=' + anio + '&mes=' + mes + '&num_pro=' + num_pro + '&codmp=' + codmp,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768',
		win = window.open(url + param, 'reporte_facturas_materia_prima', opt);
	
	win.focus();
}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
