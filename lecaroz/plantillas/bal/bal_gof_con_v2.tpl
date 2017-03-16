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
<td align="center" valign="middle"><p class="title">Listado de Gastos de Oficina</p>
  <form action="./bal_gof_con_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia()" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3">
        <input name="nombre" type="text" class="vnombre" id="nombre" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
		<!-- START BLOCK : admin -->
        <option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value=""></option>
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) fecha_cap.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><select name="cod_gastos" class="insert" id="cod_gastos">
        <option value="0" selected></option>
        <!-- START BLOCK : cod -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : cod -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha captura </th>
      <td class="vtabla"><input name="fecha_cap" type="text" class="insert" id="fecha_cap" onFocus="tmp.value=this.value;this.select()" onChange="inputDateFormat(this)" onKeyDown="if (event.keyCode==13)num_cia.select()" size="10" maxlength="10"></td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre.value = '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre.value = cia[get_val(f.num_cia)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function validar() {
	if (f.anio.anio < 2000) {
		alert("Debe especificar el año");
		f.anio.select();
		return false;
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
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
    <td width="60%" class="print_encabezado" align="center">Gastos de Oficina<br>
      Periodo del {fecha1} al {fecha2} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="6" class="vprint_total" scope="col">{num_cia} {nombre} </th>
  </tr>
  <tr>
    <th class="print">Fecha</th>
    <th class="print">Concepto</th>
    <th class="print">Balance</th>
    <th class="print">Comentario</th>
    <th class="print">Egreso</th>
    <th class="print">Ingreso</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="vprint">{concepto}</td>
    <td class="print">{bal}</td>
    <td class="vprint">{comentario}</td>
    <td class="rprint">{egreso}</td>
    <td class="rprint">{ingreso}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="4" class="rprint">Totales</th>
    <th class="rprint_total">{egreso}</th>
    <th class="rprint_total">{ingreso}</th>
  </tr>
  <tr>
    <th colspan="4" class="rprint">Total</th>
    <th colspan="2" class="print_total">{total}</th>
  </tr>
  <tr>
    <td colspan="6" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
  <tr>
    <th colspan="4" rowspan="2" class="rprint">Gran Total </th>
    <th class="print_total">{egreso}</th>
    <th class="print_total">{ingreso}</th>
  </tr>
  <tr>
    <th colspan="2" class="print_total">{total}</th>
  </tr>
</table>
<p align="center">
<input type="button" class="boton" value="Regresar" onClick="document.location='./bal_gof_con_v2.php'">
</p>
<!-- END BLOCK : listado -->
</body>
</html>
