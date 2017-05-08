<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Imprimir Contrato de Arrendamiento</p>
  <form action="./ren_imp_con.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td class="vtabla"><input name="arr" type="text" class="insert" id="arr" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) local.select()" size="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Local</th>
      <td class="vtabla"><input name="local" type="text" class="insert" id="local" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) arr.select()" size="3" /></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="this.form.submit()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
window.onload = document.form.arr.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Imprimir Contrato de Arrendamiento</p>
  <table class="tabla">
    <!-- START BLOCK : arr -->
	<tr>
      <th colspan="3" class="vtabla" scope="col">{arr} {nombre} </th>
    </tr>
    <tr>
      <th class="tabla" scope="col">Local</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Imp</th>
    </tr>
    <!-- START BLOCK : local -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{num_local}</td>
      <td class="vtabla">{nombre}</td>
      <td class="tabla"><input type="button" class="boton" value="..." onclick="contrato({num_local})" /></td>
    </tr>
	<!-- END BLOCK : local -->
    <tr>
      <td colspan="3" class="tabla">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : arr -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./ren_imp_con.php'" />
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function contrato(num_local) {
	var url = "./ren_imp_con.php?num_local=" + num_local;
	var opt = "toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=800,height=600";
	var win = window.open(url, 'contrato', opt);
}
//-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
