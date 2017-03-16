<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n de Facturas de Clientes</p>
  <form action="./pan_fac_del.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.fecha.value == "") {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : cancelacion -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n de Facturas de Clientes</p>
<form action="./pan_fac_del.php" method="post" name="form" onKeyPress="if (event.keyCode == 13) return false;">
 <!-- START BLOCK : factura -->
  <table width="60%" class="tabla">
    <tr>
      <th class="tabla" scope="col"><input type="checkbox" onClick="marcar(this,{r1},{r2})" alt="Marcar | Desmarcar todos"></th>
      <th colspan="3" class="vtabla" scope="col">Cliente: {cliente} </th>
      <th class="vtabla" scope="col">Folio: {folio} </th>
    </tr>
    <tr>
      <th width="5%" class="tabla">&nbsp;</th>
      <th width="10%" class="tabla">Cantidad</th>
      <th width="50%" class="tabla">Descripci&oacute;n</th>
      <th width="15%" class="tabla">Precio Unidad </th>
      <th width="20%" class="tabla">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}"></td>
      <td class="tabla">{cantidad}</td>
      <td class="vtabla">{descripcion}</td>
      <td class="tabla">{precio_unidad}</td>
      <td class="rtabla">{importe}</td>
    </tr>
    <!-- END BLOCK : fila -->
    <tr>
      <th colspan="4" class="rtabla">Sub-Total</th>
      <td class="rtabla"><strong>{subtotal}</strong></td>
    </tr>
    <tr>
      <th colspan="4" class="rtabla">I.V.A.</th>
      <td class="rtabla"><strong>{iva}</strong></td>
    </tr>
    <tr>
      <th colspan="4" class="rtabla">Total</th>
      <td class="rtabla"><strong>{total}</strong></td>
    </tr>
  </table>
  <!-- END BLOCK : factura -->
<p>
  <input type="button" class="boton" value="Regresar" onClick="document.location='./pan_fac_del.php'">
&nbsp;&nbsp;&nbsp;
<input type="button" class="boton" value="Cancelar Facturas" onClick="validar(form)">
</p>
</form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function marcar(m, r1, r2) {
		if (m.form.id.length != undefined)
			for (i=r1; i<r2; i++)
				m.form.id[i].checked = m.checked == true ? true : false;
		else
			m.form.id.checked = m.checked == true ? true : false;
	}
	
	function validar(form) {
		var num_elementos = 0;
		
		if (form.id.length == undefined)
			num_elementos += form.id.checked ? 1 : 0;
			if (form.id.checked == true)
				num_elementos++;
		else
			for (i=0; i<form.id.length; i++)
				num_elementos += form.id[i].checked ? 1 : 0;
		
		if (num_elementos == 0) {
			alert("Debe seleccionar al menos una casilla");
			return false;
		}
		else
			if (confirm("¿Desea cancelar los datos seleccionados?"))
				form.submit();
			else
				return false;
	}
</script>
<!-- END BLOCK : cancelacion -->
</body>
</html>
