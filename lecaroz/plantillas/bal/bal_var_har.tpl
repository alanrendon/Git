<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Estimaci&oacute;n por Aumento de Precio</p>
  <form action="./bal_var_har.php" method="get" name="form" target="result">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) anio.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected="selected"></option>
        <option value="-1">AGRUPAR POR ADMINISTRADOR</option>
        <!-- START BLOCK : idadmin -->
        <option value="{id}">{admin}</option>
        <!-- END BLOCK : idadmin -->
      </select>
</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) precio_pieza.select()" value="{anio}" size="4" maxlength="4" /></td>
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
      <th class="vtabla" scope="row">Turnos</th>
      <td class="vtabla"><input name="turno[]" type="checkbox" id="turno" value="1" />
        Frances de D&iacute;a<br />
		<input name="turno[]" type="checkbox" id="turno" value="2" />
        Frances de Noche<br />
		<input name="turno[]" type="checkbox" id="turno" value="3" />
        Bizcochero<br />
		<input name="turno[]" type="checkbox" id="turno" value="4" />
        Repostero<br />
		<input name="turno[]" type="checkbox" id="turno" value="8" />
        Piconero</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Precio por pieza </th>
      <td class="vtabla"><input name="precio_pieza" type="text" class="rinsert" id="precio_pieza" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if (event.keyCode == 13) precio_harina.select()" size="8" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Precio por bulto de harina </th>
      <td class="vtabla"><input name="precio_harina" type="text" class="rinsert" id="precio_harina" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if (event.keyCode == 13) num_cia.select()" size="8" /></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaCia() {
}

function validar() {
	var cont = 0;
	
	for (var i = 0; i < f.turno.length; i++)
		cont += f.turno[i].checked ? 1 : 0;
	
	if (get_val(f.anio) <= 0) {
		alert('Debe especificar el año');
		f.anio.select();
		return false;
	}
	else if (cont == 0) {
		alert('Debe seleccionar al menos un turno');
		return false;
	}
	else if (get_val(f.precio_pieza) == 0) {
		alert('Debe especificar el precio por pieza');
		f.precio_pieza.select();
		return false;
	}
	else if (get_val(f.precio_harina) <= 0) {
		alert('Debe especificar el precio de la harina');
		f.precio_harina.select();
		return false;
	}
	
	var win = window.open('', 'result', "toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=1024,height=768");
	
	f.submit();
	
	win.focus();
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
    <td width="60%" class="print_encabezado" align="center">Estimaci&oacute;n por Aumento de Precio<br />
      {mes} {anio}<br />
    Precio Pieza: {precio_pieza}, Precio Bulto: {precio_harina}{admin}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Producci&oacute;n</th>
    <th class="print" scope="col">Costo Producci&oacute;n </th>
    <th class="print" scope="col">Bultos</th>
    <th class="print" scope="col">Costo Harina </th>
    <th class="print" scope="col">Diferencia</th>
    <th class="print" scope="col">Clientes</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="rprint">{piezas}</td>
    <td class="rprint" style="color:#0000CC;">{costo_pro}</td>
    <td class="rprint">{bultos}</td>
    <td class="rprint" style="color:#0000CC;">{costo_harina}</td>
    <td class="rprint">{dif}</td>
    <td class="rprint">{clientes}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="rprint">Totales</th>
    <th class="rprint">{piezas}</th>
    <th class="rprint" style="color:#0000CC;">{costo_pro}</th>
    <th class="rprint">{bultos}</th>
    <th class="rprint" style="color:#0000CC;">{costo_harina}</th>
    <th class="rprint">{dif}</th>
    <th class="rprint">{clientes}</th>
  </tr>
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
