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
<td align="center" valign="middle"><p class="title">Consulta de Existencias</p>
  <form action="./ped_exi_mes.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Producto</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaMP()" onkeydown="if (event.keyCode == 13) anio.select()" size="3" />
        <input name="desc" type="text" class="vnombre" id="desc" size="30" readonly="true" /></td>
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
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) codmp.select()" size="3" />
        <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected="selected"></option>
        <!-- START BLOCK : idadmin -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : idadmin -->
      </select>
        <input name="separar" type="checkbox" id="separar" value="1" />
        Separar      </td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, pro = new Array(), cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : cia -->
<!-- START BLOCK : pro -->
pro[{codmp}] = '{nombre}';
<!-- END BLOCK : pro -->

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
		f.select();
	}
}

function cambiaMP() {
	if (f.codmp.value == '' || f.codmp.value == '0') {
		f.codmp.value = '';
		f.desc.value = '';
	}
	else if (pro[get_val(f.codmp)] != null)
		f.desc.value = pro[get_val(f.codmp)];
	else {
		alert('El producto no se encuentra en el catálogo');
		f.codmp.value = f.tmp.value;
		f.select();
	}
}

function validar() {
	if (get_val(f.codmp) <= 0) {
		alert('Debe espeficar el producto')
		f.codmp.select();
	}
	else if (get_val(f.anio) <= 0) {
		alert('Debe especificar el año de consulta');
		f.anio.select();
	}
	else
		f.submit();
}

window.onload = f.codmp.select();
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
    <td width="60%" class="print_encabezado" align="center">Existencias Mensuales<br />
      {codmp}       {nombre} <br />
    {mes} de {anio} <br />
    {admin}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Existencia<br />
    Inicial</th>
    <th class="print" scope="col">Consumo</th>
    <th class="print" scope="col">Compras</th>
    <th class="print" scope="col">Existencia<br />
    Final</th>
    <th class="print" scope="col">Promedio<br />
      Consumo</th>
    <th class="print" scope="col">D&iacute;as</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="rprint" style="color:#006600;">{ini}</td>
    <td class="rprint" style="color:#990000;">{con}</td>
    <td class="rprint" style="color:#0000CC;">{com}</td>
    <td class="rprint" style="color:#006600;">{fin}</td>
    <td class="rprint" style="color:#990000;">{prom}</td>
    <td class="rprint" style="color:#0000CC;">{dias}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : totales -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="rprint">Totales</th>
    <th class="rprint" style="color:#006600;">{ini}</th>
    <th class="rprint" style="color:#990000;">{con}</th>
    <th class="rprint" style="color:#0000CC;">{com}</th>
    <th class="rprint" style="color:#006600;">{fin}</th>
    <th class="rprint" style="color:#006600;">&nbsp;</th>
    <th class="rprint" style="color:#006600;">&nbsp;</th>
  </tr>
  <!-- END BLOCK : totales -->
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : listado -->
</body>
</html>
