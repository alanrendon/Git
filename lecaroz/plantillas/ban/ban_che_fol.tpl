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
<td align="center" valign="middle"><p class="title">Consulta de Cheques por Folio</p>
  <form action="./ban_che_fol.php" method="get" name="form" onClick="if (event.keyCode == 13) return false"><table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Folio</th>
      <td class="vtabla" scope="col"><input name="num_cheque" type="text" class="insert" id="num_cheque" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) next.focus()" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="col">Cuenta</th>
      <td class="vtabla" scope="col"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER SERFIN</option>
      </select></td>
    </tr>
  </table>  
  <p>
    <input name="next" type="button" class="boton" id="next" onClick="validar(form)" value="Siguiente">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.num_cheque.value <= 0) {
			alert("Debe especificar el folio");
			form.num_cheque.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cheque.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Resultados de la b&uacute;squeda del Folio </p>
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Conciliado</th>
      <th class="tabla" scope="col">Cancelado</th>
      <th class="tabla" scope="col">Beneficiario</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">No. Cheque </th>
      <th colspan="2" class="tabla" scope="col">Cod. Gasto </th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Facturas</th>
      </tr>
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rtabla">{num_cia}</td>
      <td class="vtabla">{nombre_cia}</td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{fecha_con}</td>
      <td class="tabla">{fecha_cancelacion}</td>
      <td class="vtabla">{a_nombre}</td>
      <td class="tabla">{folio}</td>
      <td class="tabla"><strong>{num_cheque}</strong></td>
      <td class="rtabla">{codgastos}</td>
      <td class="vtabla">{descripcion}</td>
      <td class="tabla">{importe}</td>
      <td class="tabla">{concepto}</td>
      <td class="tabla">{facturas}</td>
      </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_che_fol.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
