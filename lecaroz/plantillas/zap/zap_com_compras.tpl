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
<td align="center" valign="middle"><p class="title">Comparativo de Compras de Zapaterias </p>
  <form action="./zap_com_compras.php" method="post" enctype="multipart/form-data" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
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
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) cod_desc[0].select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Archivo (.TXT) </th>
      <td class="vtabla"><input name="archivo" type="file" class="vinsert" id="archivo" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 1 </th>
      <td class="vtabla"><input name="cod_desc[]" type="text" class="insert" id="cod_desc" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(0)" onkeydown="if (event.keyCode == 13) cod_desc[1].select()" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 2 </th>
      <td class="vtabla"><input name="cod_desc[]" type="text" class="insert" id="cod_desc" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(1)" onkeydown="if (event.keyCode == 13) cod_desc[2].select()" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 3 </th>
      <td class="vtabla"><input name="cod_desc[]" type="text" class="insert" id="cod_desc" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(2)" onkeydown="if (event.keyCode == 13) cod_desc[3].select()" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 4 </th>
      <td class="vtabla"><input name="cod_desc[]" type="text" class="insert" id="cod_desc" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(3)" onkeydown="if (event.keyCode == 13) cod_desc[4].select()" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 5 </th>
      <td class="vtabla"><input name="cod_desc[]" type="text" class="insert" id="cod_desc" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(4)" onkeydown="if (event.keyCode == 13) cod_desc[5].select()" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 6 </th>
      <td class="vtabla"><input name="cod_desc[]" type="text" class="insert" id="cod_desc" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(5)" onkeydown="if (event.keyCode == 13) cod_desc[6].select()" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 7 </th>
      <td class="vtabla"><input name="cod_desc[]" type="text" class="insert" id="cod_desc" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(6)" onkeydown="if (event.keyCode == 13) cod_desc[7].select()" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Descuento 8 </th>
      <td class="vtabla"><input name="cod_desc[]" type="text" class="insert" id="cod_desc" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(7)" onkeydown="if (event.keyCode == 13) cod_desc[8].select()" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Descuento 9 </th>
      <td class="vtabla"><input name="cod_desc[]" type="text" class="insert" id="cod_desc" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(8)" onkeydown="if (event.keyCode == 13) cod_desc[9].select()" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Descuento 10 </th>
      <td class="vtabla"><input name="cod_desc[]" type="text" class="insert" id="cod_desc" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod(9)" onkeydown="if (event.keyCode == 13) anio.select()" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
  </table>  
    <p style=" font-family:Arial, Helvetica, sans-serif; font-weight:bold;">NOTA: El formato del archivo debe ser 'TXT (Texto delimitado por tabulaciones)'. </p>
    <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cod = [];
<!-- START BLOCK : cod -->
cod[{cod}] = '{desc}';
<!-- END BLOCK : cod -->

function cambiaCod(i) {
	if (f.cod_desc[i].value == '' || f.cod_desc[i].value == '0') {
		f.cod_desc[i].value = '';
		f.desc[i].value.value = '';
	}
	else if (cod[get_val(f.cod_desc[i])] != null) {
		f.desc[i].value = cod[get_val(f.cod_desc[i])];
	}
	else {
		alert('El código de descuento no se encuentra en el catálogo');
		f.cod_desc[i].value = f.tmp.value;
		f.cod_desc[i].select();
	}
}

function validar() {
	if (get_val(f.anio) <= 0) {
		alert('Debe especificar el año');
	}
	else
		f.submit();
}

window.onload = f.cod_desc[0].select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<!-- START BLOCK : cia -->
<table align="center" class="tabla">
  <tr>
    <th class="tabla" scope="col">Sucursal</th>
  </tr>
  <tr>
    <th class="tabla" style="font-size: 14pt;">{num_cia} {nombre_cia} </th>
  </tr>
</table>
<!-- START BLOCK : ok -->
<p align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold;">Facturas Encontradas e Igualadas</p>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Factura</th>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Fecha Inv </th>
    <th class="print" scope="col">Fecha Sis </th>
    <th class="print" scope="col">Cantidad</th>
    <th class="print" scope="col">Desc 1 </th>
    <th class="print" scope="col">Desc 2 </th>
    <th class="print" scope="col">Desc 3  </th>
    <th class="print" scope="col">Desc 4  </th>
    <th class="print" scope="col">Importe Archivo </th>
    <th class="print" scope="col">Importe Sistema </th>
  </tr>
  <!-- START BLOCK : fila_ok -->
  <tr>
    <td class="print">{num_fact}</td>
    <td class="vprint">{num_pro} {nombre_pro} </td>
    <td class="print">{fecha_inv}</td>
    <td class="print">{fecha_sis}</td>
    <td class="rprint">{cantidad}</td>
    <td class="rprint">{desc1}</td>
    <td class="rprint">{desc2}</td>
    <td class="rprint">{desc3}</td>
    <td class="rprint">{desc4}</td>
    <td class="rprint">{importe_inv}</td>
    <td class="rprint">{importe_sis}</td>
  </tr>
	<!-- END BLOCK : fila_ok -->
  <tr>
    <th colspan="4" class="rprint_total">Totales</th>
    <th class="rprint_total">{cantidad_ok}</th>
    <th class="rprint_total">{desc1_ok}</th>
    <th class="rprint_total">{desc2_ok}</th>
    <th class="rprint_total">{desc3_ok}</th>
    <th class="rprint_total">{desc4_ok}</th>
    <th class="rprint_total">{importe_inv_ok}</th>
    <th class="rprint_total">{importe_sis_ok}</th>
  </tr>
</table>
<!-- END BLOCK : ok -->
<!-- START BLOCK : dif -->
<p align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold;">Facturas Encontradas con Diferencias</p>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Factura</th>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Fecha Inv </th>
    <th class="print" scope="col">Fecha Sis </th>
    <th class="print" scope="col">Cantidad</th>
    <th class="print" scope="col">Desc 1 </th>
    <th class="print" scope="col">Desc 2 </th>
    <th class="print" scope="col">Desc 3  </th>
    <th class="print" scope="col">Desc 4  </th>
    <th class="print" scope="col">Importe Archivo </th>
    <th class="print" scope="col">Importe Sistema </th>
    <th class="print" scope="col">Diferencia</th>
  </tr>
  <!-- START BLOCK : fila_dif -->
  <tr>
    <td class="print">{num_fact}</td>
    <td class="vprint">{num_pro} {nombre_pro} </td>
    <td class="print">{fecha_inv}</td>
    <td class="print">{fecha_sis}</td>
    <td class="rprint">{cantidad}</td>
    <td class="rprint">{desc1}</td>
    <td class="rprint">{desc2}</td>
    <td class="rprint">{desc3}</td>
    <td class="rprint">{desc4}</td>
    <td class="rprint">{importe_inv}</td>
    <td class="rprint">{importe_sis}</td>
    <td class="rprint">{dif}</td>
  </tr>
	<!-- END BLOCK : fila_dif -->
  <tr>
    <th colspan="4" class="rprint_total">Totales</th>
    <th class="rprint_total">{cantidad_dif}</th>
    <th class="rprint_total">{desc1_dif}</th>
    <th class="rprint_total">{desc2_dif}</th>
    <th class="rprint_total">{desc3_dif}</th>
    <th class="rprint_total">{desc4_dif}</th>
    <th class="rprint_total">{importe_inv_dif}</th>
    <th class="rprint_total">{importe_sis_dif}</th>
    <th class="rprint_total">{dif_dif}</th>
  </tr>
</table>
<!-- END BLOCK : dif -->
<!-- START BLOCK : no -->
<p align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold;">Facturas No Encontradas en Sistema </p>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Factura</th>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Condiciones</th>
    <th class="print" scope="col">Cantidad</th>
    <th class="print" scope="col">Subtotal</th>
    <th class="print" scope="col">IVA</th>
    <th class="print" scope="col">Total</th>
  </tr>
  <!-- START BLOCK : fila_no -->
  <tr>
    <td class="print">{num_fact}</td>
    <td class="vprint"> {nombre_pro} </td>
    <td class="print">{fecha}</td>
    <td class="vprint">{cond}</td>
    <td class="rprint">{cantidad}</td>
    <td class="rprint">{subtotal}</td>
    <td class="rprint">{iva}</td>
    <td class="rprint">{total}</td>
  </tr>
	<!-- END BLOCK : fila_no -->
  <tr>
    <th colspan="4" class="rprint_total">Totales</th>
    <th class="rprint_total">{cantidad_no}</th>
    <th class="rprint_total">{subtotal}</th>
    <th class="rprint_total">{iva}</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<!-- END BLOCK : no -->
<!-- START BLOCK : no_sis -->
<p align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; font-weight:bold;">Facturas No Encontradas en Archivo </p>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Factura</th>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Importe</th>
    <th class="print" scope="col">Descuentos</th>
    <th class="print" scope="col">Subtotal</th>
    <th class="print" scope="col">IVA</th>
    <th class="print" scope="col">Total</th>
  </tr>
  <!-- START BLOCK : fila_no_sis -->
  <tr>
    <td class="print">{num_fact}</td>
    <td class="vprint"> {num_pro} {nombre_pro} </td>
    <td class="print">{fecha}</td>
    <td class="rprint">{importe}</td>
    <td class="rprint">{desc}</td>
    <td class="rprint">{subtotal}</td>
    <td class="rprint">{iva}</td>
    <td class="rprint">{total}</td>
  </tr>
	<!-- END BLOCK : fila_no_sis -->
  <tr>
    <th colspan="3" class="rprint_total">Totales</th>
    <th class="rprint_total">{importe}</th>
    <th class="rprint_total">{desc}</th>
    <th class="rprint_total">{subtotal}</th>
    <th class="rprint_total">{iva}</th>
	<th class="rprint_total">{total}</th>
  </tr>
</table>
<!-- END BLOCK : no_sis -->
<br style="page-break-after:always;" />
<!-- END BLOCK : cia -->
<!--<p>
  <input type="button" class="boton" value="Regresar" onclick="document.location='./zap_com_compras.php'" />
</p>-->
<!-- END BLOCK : result -->
</body>
</html>
