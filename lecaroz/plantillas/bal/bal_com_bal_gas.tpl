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
<td align="center" valign="middle"><p class="title">Comparativo de Datos de Balance contra Gastos</p>
  <form action="bal_com_bal_gas.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) anio.select()" size="3" />
        <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected="selected"></option>
        <!-- START BLOCK : idadmin -->
        <option value="{id}">{admin}</option>
        <!-- END BLOCK : idadmin -->
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
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) cod[0].select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Campo</th>
      <td class="vtabla"><select name="campo" class="insert" id="campo">
        <option value="venta_puerta">VENTA EN PUERTA</option>
        <option value="ventas_netas">VENTAS NETAS</option>
        <option value="abono_reparto">ABONO REPARTO</option>
        <option value="produccion_total">PRODUCCION</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digos<br /> 
        de Gasto</th>
      <td class="vtabla"><input name="cod[]" type="text" class="insert" id="cod" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(0)" onkeydown="if (event.keyCode == 13) cod[1].select()" size="3" />
        <input name="desc[]" type="text" disabled="true" class="vnombre" id="desc" style="border-color:#000000; border-style:solid; border-width:1px;" size="30" /><br />
		<input name="cod[]" type="text" class="insert" id="cod" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(1)" onkeydown="if (event.keyCode == 13) cod[2].select()" size="3" />
        <input name="desc[]" type="text" disabled="true" class="vnombre" id="desc" style="border-color:#000000; border-style:solid; border-width:1px;" size="30" /><br />
		<input name="cod[]" type="text" class="insert" id="cod" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(2)" onkeydown="if (event.keyCode == 13) cod[3].select()" size="3" />
        <input name="desc[]" type="text" disabled="true" class="vnombre" id="desc" style="border-color:#000000; border-style:solid; border-width:1px;" size="30" /><br />
		<input name="cod[]" type="text" class="insert" id="cod" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(3)" onkeydown="if (event.keyCode == 13) cod[4].select()" size="3" />
        <input name="desc[]" type="text" disabled="true" class="vnombre" id="desc" style="border-color:#000000; border-style:solid; border-width:1px;" size="30" /><br />
		<input name="cod[]" type="text" class="insert" id="cod" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(4)" onkeydown="if (event.keyCode == 13) num_cia.select()" size="3" />
        <input name="desc[]" type="text" disabled="true" class="vnombre" id="desc" style="border-color:#000000; border-style:solid; border-width:1px;" size="30" />		</td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" onclick="validar()" value="Siguiente" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), gasto = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : cia -->
<!-- START BLOCK : cod -->
cod[{cod}] = '{desc}';
<!-- END BLOCK : cod -->

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

function cambiaCod(i) {
	if (f.cod[i].value == '' || f.cod[i].value == '0') {
		f.cod[i].value = '';
		f.desc[i].value = '';
	}
	else if (cod[get_val(f.cod[i])] != null)
		f.desc[i].value = cod[get_val(f.cod[i])];
	else {
		alert('El código no se encuentra en el catálogo');
		f.cod[i].value = f.tmp.value;
		f.cod[i].select();
	}
}

function validar() {
	var cont = 0, i;
	
	for (i = 0; i < f.cod.length; i++)
		cont += get_val(f.cod[i]) > 0 ? 1 : 0;
	
	if (get_val(f.anio) == 0) {
		alert('Debe especificar el año de consulta');
		f.anio.select();
	}
	else if (cont == 0) {
		alert('Debe especificar al menos un código de gasto');
		f.cod[0].select();
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
    <td width="60%" class="print_encabezado" align="center">Comparativo de {campo} contra Gastos <br />
      {mes} del {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">{campo}</th>
    <!-- START BLOCK : gasto_title -->
	<th class="print" scope="col">{gasto}</th>
	<!-- END BLOCK : gasto_title -->
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint" style="color:#0000CC;">{num_cia} {nombre} </td>
    <td class="rprint" style="color:#006600;">{dato}</td>
    <!-- START BLOCK : gasto -->
	<td class="rprint" style="color:#CC0000 ">{gasto}</td>
	<!-- END BLOCK : gasto -->
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="rprint">Totales</th>
    <th class="rprint_total" style="color:#006600;">{dato}</th>
    <!-- START BLOCK : gasto_total -->
	<th class="rprint_total" style="color:#CC0000;">{gasto}</th>
	<!-- END BLOCK : gasto_total -->
  </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
