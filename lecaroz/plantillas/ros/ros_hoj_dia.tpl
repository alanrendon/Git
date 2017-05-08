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
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Hoja de Rosticer&iacute;a</p>
  <form action="./ros_hoj_dia.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) fecha.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected="selected"></option>
        <!-- START BLOCK : admin -->
        <option value="{id}">{admin}</option>
        <!-- END BLOCK : admin -->
      </select>
</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{fecha}" size="10" maxlength="10" /></td>
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
	/*if (get_val(f.num_cia) == 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
	}
	else */if (f.fecha.value.length < 8) {
		alert('Debe especificar la fecha de consulta');
		f.fecha.select();
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : hoja -->
<style type="text/css">
.titulos_hoja {
	font-family:Arial, Helvetica, sans-serif; font-size:14pt; font-weight:bold;
}

.tabla_hoja {
	font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-style:solid; border-width:1px; border-color:#000000;
}

.celda_tabla_hoja {
	 border-style:solid; border-width:1px; border-color:#000000;
}
</style>
<table width="100%" align="center" class="titulos_hoja">
  <tr>
    <td>{num_cia}</td>
    <td>&nbsp;</td>
    <td align="right">{num_cia}</td>
  </tr>
  <tr>
    <td>Rosticer&iacute;a: {num_cia} </td>
    <td align="center">{nombre}</td>
    <td align="right">Fecha: {dia_esc} {dia} {mes_esc} {anio} </td>
  </tr>
</table>
<br />
<table width="100%" align="center" class="tabla_hoja">
  <tr>
    <th class="celda_tabla_hoja">Productos</th>
    <th class="celda_tabla_hoja">Existencia</th>
    <th class="celda_tabla_hoja">Mercancias<br />
    Recibidas</th>
    <th class="celda_tabla_hoja"><p>Total</p>    </th>
    <th class="celda_tabla_hoja">Para<br />
    Ma&ntilde;ana</th>
    <th class="celda_tabla_hoja">Venta <br />
    Total </th>
    <th class="celda_tabla_hoja">Precio<br />
    Venta</th>
    <th class="celda_tabla_hoja">Importe<br />
    Venta</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="celda_tabla_hoja">{producto}</td>
    <td align="right" class="celda_tabla_hoja">{existencia}</td>
    <td align="right" class="celda_tabla_hoja">{mercancias}</td>
    <td align="right" class="celda_tabla_hoja">{total}</td>
    <td align="right" class="celda_tabla_hoja">{para_manyana}</td>
    <td align="right" class="celda_tabla_hoja">{venta_total}</td>
    <td align="right" class="celda_tabla_hoja">{precio_venta}</td>
    <td align="right" class="celda_tabla_hoja">{importe_venta}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<br />
<table width="100%" align="center">
  <tr>
    <td valign="top"><table width="98%" class="tabla_hoja">
      <tr>
        <th colspan="2" class="celda_tabla_hoja">Detalle de Gastos </th>
        </tr>
      <tr>
        <th class="celda_tabla_hoja">Concepto</th>
        <th class="celda_tabla_hoja">Importe</th>
      </tr>
      <!-- START BLOCK : gasto -->
	  <tr>
        <td class="celda_tabla_hoja">{concepto}</td>
        <td align="right" class="celda_tabla_hoja">{importe}</td>
      </tr>
	  <!-- END BLOCK : gasto -->
      <tr>
        <th align="right" class="celda_tabla_hoja">Total</th>
        <th align="right" class="celda_tabla_hoja">{total_gastos}</th>
      </tr>
    </table></td>
    <td align="center" valign="top"><table width="98%" class="tabla_hoja">
      <tr>
        <th colspan="2" class="celda_tabla_hoja">Detalle de Otros Abonos </th>
        </tr>
	  <tr>
        <th class="celda_tabla_hoja">Concepto</th>
        <th class="celda_tabla_hoja">Importe</th>
      </tr>
	  <!-- START BLOCK : otro -->
      <tr>
        <td class="celda_tabla_hoja">{concepto}</td>
        <td align="right" class="celda_tabla_hoja">{importe}</td>
      </tr>
      <!-- END BLOCK : otro -->
	  <tr>
        <th align="right" class="celda_tabla_hoja">Total</th>
        <th align="right" class="celda_tabla_hoja">{total_otros}</th>
      </tr>
    </table></td>
    <td align="right" valign="top"><table width="98%" class="tabla_hoja">
      <tr>
        <th align="left" class="celda_tabla_hoja">Ventas</th>
        <th align="right" class="celda_tabla_hoja">{ventas}</th>
      </tr>
      <tr>
        <th align="left" class="celda_tabla_hoja">Gastos</th>
        <th align="right" class="celda_tabla_hoja">{total_gastos}</th>
      </tr>
      <tr>
        <th align="left" class="celda_tabla_hoja">Otros</th>
        <th align="right" class="celda_tabla_hoja">{total_otros}</th>
      </tr>
      <tr>
        <th align="left" class="celda_tabla_hoja">Efectivo</th>
        <th align="right" class="celda_tabla_hoja">{efectivo}</th>
      </tr>
    </table></td>
  </tr>
</table>
<br />
<table width="100%" align="center" class="tabla_hoja">
  <tr>
    <th colspan="5" class="celda_tabla_hoja" scope="col">Prestamos</th>
  </tr>
  <tr>
    <th class="celda_tabla_hoja">Nombre</th>
    <th class="celda_tabla_hoja">Adeudo</th>
    <th class="celda_tabla_hoja">Prestado</th>
    <th class="celda_tabla_hoja">Abono</th>
    <th class="celda_tabla_hoja">Resta</th>
  </tr>
  <!-- START BLOCK : prestamo -->
  <tr>
    <td class="celda_tabla_hoja">{nombre}</td>
    <td align="right" class="celda_tabla_hoja">{adeudo}</td>
    <td align="right" class="celda_tabla_hoja">{prestado}</td>
    <td align="right" class="celda_tabla_hoja">{abono}</td>
    <td align="right" class="celda_tabla_hoja">{resta}</td>
  </tr>
  <!-- END BLOCK : prestamo -->
  <tr>
    <th align="right" class="celda_tabla_hoja">Total</th>
    <th align="right" class="celda_tabla_hoja">{adeudo}</th>
    <th align="right" class="celda_tabla_hoja">{prestado}</th>
    <th align="right" class="celda_tabla_hoja">{abono}</th>
    <th align="right" class="celda_tabla_hoja">{resta}</th>
  </tr>
</table>
{salto}
<!-- END BLOCK : hoja -->
</body>
</html>
