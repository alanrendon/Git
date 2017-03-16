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
<td align="center" valign="middle"><p class="title">Comisiones de Rosticer&iacute;as</p>
  <form action="./ros_com_con_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) anio.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected="selected"></option>
        <!-- START BLOCK : admin -->
        <option value="{id}">{nombre}</option>
        <!-- END BLOCK : admin -->
      </select>
</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
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
      </select>      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.select();this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
  </table>  
    <p>
    <input name="Submit" type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
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
	if (get_val(f.anio) < 2004) {
		alert('Debe especificar el año de consulta');
		f.anio.select();
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
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
    <td width="60%" class="print_encabezado" align="center">Comisiones de Rosticer&iacute;as <br />
      {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Pollos<br /> 
    Vendidos </th>
    <th class="print" scope="col">Piernas de Pavo <br />
    Vendidas </th>
    <th class="print" scope="col">Pescuezos<br />
    Vendidos</th>
    <th class="print" scope="col">Alas<br />
      Vendidas</th>
    <th class="print" scope="col">Comisi&oacute;n</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="rprint">{pollos}</td>
    <td class="rprint">{piernas}</td>
    <td class="rprint">{pescuezos}</td>
    <td class="rprint">{alas}</td>
    <td class="rprint">{comision}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="print">Totales</th>
    <th class="rprint_total">{pollos}</th>
    <th class="rprint_total">{piernas}</th>
    <th class="rprint_total">{pescuezos}</th>
    <th class="rprint_total">{alas}</th>
    <th class="rprint_total">{comision}</th>
  </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
