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
<td align="center" valign="middle"><p class="title">Diferencia de Saldos Conciliados</p>
  <form action="./ban_dif_sal_v2.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">Banorte</option>
        <option value="2" selected>Santander</option>
      </select></td>
    </tr>
  </table>  <p>
    <input type="submit" class="boton" value="Siguiente">
  </p></form></td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : dif -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Diferencia de Saldos Conciliados </p>
<table width="100%" class="tabla">
  <tr>
    <th colspan="6" class="tabla" scope="col" style="font-size: 12pt; ">{banco}</th>
    <th colspan="2" class="tabla" style="font-size: 12pt; " scope="col">{fecha}</th>
    </tr>
  <tr>
    <th class="tabla" scope="col">Cia</th>
    <th class="tabla" scope="col">N&uacute;mero de Cuenta </th>
    <th class="tabla" scope="col">Nombre</th>
    <th class="tabla" scope="col">Saldo Conciliado </th>
    <th class="tabla" scope="col">Pendientes</th>
    <th class="tabla" scope="col">Saldo Final </th>
    <th class="tabla" scope="col">Saldo Capturado </th>
    <th class="tabla" scope="col">Diferencia</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';" onClick="estadoCuenta('{num_cia}','{cuenta}','{dia}','{mes}','{anio}',1)">{num_cia}</td>
    <td class="tabla">{clabe_cuenta}</td>
    <td class="vtabla">{nombre}</td>
    <td class="rtabla" style="font-weight: bold; color: #0000CC;">{saldo_con}</td>
    <td class="rtabla" style="font-weight: bold; color: #0000CC;">{pendientes}</td>
    <td class="rtabla" style="font-weight: bold; color: #0000CC;">{saldo_final}</td>
    <td class="rtabla" style="font-weight: bold; color: #CC0000;">{saldo_cap}</td>
    <td class="rtabla" style="font-weight: bold; color: #FF6600;">{diferencia}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="7" class="tabla">Total</th>
    <th class="rtabla">{total}</th>
  </tr>
</table>
<br>
<input type="button" class="boton" value="Regresar" onClick="document.location='./ban_dif_sal_v2.php'">

</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function estadoCuenta(num_cia, cuenta, dia, mes, anio, tipo) {
	var url = "./ban_esc_con_v2.php";
	var opt;
	
	opt = "?num_cia=" + num_cia + "&cuenta=" + cuenta + "&fecha1=01/" + mes + "/" + anio + "&fecha2=" + dia + "/" + mes + "/" + anio + "&tipo=0&cerrar=1";
	
	var ven = window.open(url + opt, "pend", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
	ven.focus();
}
-->
</script>
<!-- END BLOCK : dif -->
</body>
</html>
