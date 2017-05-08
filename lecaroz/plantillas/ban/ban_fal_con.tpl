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
<td align="center" valign="middle"><p class="title">Consulta de Faltantes de Cometra</p>
  <form action="./ban_fal_con.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="this.form.submit()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Faltantes de Cometra</p>
  <table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="6" class="tabla" scope="col">{num_cia} - {nombre_cia} </th>
      </tr>
    <tr>
      <th class="tabla" scope="col">Fecha</th>
	  <th class="tabla" scope="col">Depósito</th>
      <th class="tabla" scope="col">Faltante</th>
      <th class="tabla" scope="col">Sobrante</th>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
      <th class="tabla" scope="col">&nbsp;</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{fecha}</td>
	  <td class="rtabla">{deposito}</td>
      <td class="rtabla"><font color="#0000FF">{faltante}</font></td>
      <td class="rtabla"><font color="#FF0000">{sobrante}</font></td>
      <td class="vtabla">{descripcion}</td>
      <td class="tabla"><input type="button" class="boton" value="Modificar" onClick="mod({id})">
        <input type="button" class="boton" value="Eliminar" onClick="del({id})"></td>
      </tr>
	  <!-- END BLOCK : fila -->
    <tr>
      <th class="rtabla">Totales</th>
	  <th class="rtabla">{deposito}</th>
      <th class="rtabla"><font color="#0000FF">{faltante}</font></th>
      <th class="rtabla"><font color="#FF0000">{sobrante}</font></th>
      <th colspan="2" class="tabla">&nbsp;</th>
      </tr>
    <tr>
      <th colspan="2" class="rtabla">Diferencia</th>
      <th colspan="2" class="tabla">{diferencia}</th>
      <th colspan="2" class="tabla">&nbsp;</th>
    </tr>
    <tr>
      <td colspan="6">&nbsp;</td>
      </tr>
	 <!-- END BLOCK : cia -->
	 <!-- START BLOCK : no_result -->
	 <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th colspan="6" class="tabla">No hay resultados </th>
      </tr>
	 <!-- END BLOCK : no_result -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_fal_con.php'">
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function mod(id) {
		window.open("ban_fal_mod.php?id=" + id,"mod","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=200");
	}
	
	function del(id) {
		window.open("ban_fal_del.php?id=" + id,"del","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=200");
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
