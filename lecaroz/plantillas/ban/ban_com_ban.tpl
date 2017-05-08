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
<td align="center" valign="middle"><p class="title">Consulta de Comisiones Bancarias</p>
  <form action="./ban_com_ban.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) anio.select()" size="3" />
        <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" size="30" /></td>
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
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Banco</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected="selected">SANTANDER</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="0" checked="checked" />
        Todo<br />
        <input name="tipo" type="radio" value="1" />
        Bonificaciones<br />
        <input name="tipo" type="radio" value="2" />
        Comisiones</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" /> 
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
	if (f.num_cia.value == '' || f.num_cia.value == 0) {
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
	if (get_val(f.anio) <= 0) {
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
    <td width="60%" class="print_encabezado" align="center">Comisiones Bancarias<br />
      {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="5" class="vprint" scope="col">{num_cia} {nombre} </th>
  </tr>
  <tr>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">C&oacute;digo</th>
    <th class="print" scope="col">Abono</th>
    <th class="print" scope="col">Cargo</th>
  </tr>
  <!-- START BLOCK : mov -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{fecha}</td>
    <td class="vprint">{concepto}</td>
    <td class="vprint">{cod_mov} {desc} </td>
    <td class="rprint" style="color:#0000CC;">{abono}</td>
    <td class="rprint" style="color:#CC0000;">{cargo}</td>
  </tr>
  <!-- END BLOCK : mov -->
  <tr>
    <th colspan="3" class="rprint">Total por Concepto </th>
    <th class="rprint_total" style="color:#0000CC;">{abono}</th>
    <th class="rprint_total" style="color:#CC0000;">{cargo}</th>
  </tr>
  <tr>
    <th colspan="3" class="rprint">Total</th>
    <th colspan="2" class="print_total" style="color:#{color_total};">{total}</th>
  </tr>
  <tr>
    <td colspan="5" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
</table>
<br />
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Abonos</th>
    <th class="print" scope="col">Cargos</th>
  </tr>
  <tr>
    <td class="print" style="color:#0000CC; font-size:14pt; font-weight:bold;">{abonos}</td>
    <td class="print" style="color:#CC0000; font-size:14pt; font-weight:bold;">{cargos}</td>
  </tr>
  <tr>
    <th colspan="2" class="print">Gran Total </th>
  </tr>
  <tr>
    <td colspan="2" class="print" style="color:#{color}; font-size:14pt; font-weight:bold;">{total}</td>
  </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
