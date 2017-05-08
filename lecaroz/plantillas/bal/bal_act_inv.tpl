<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Actualizaci&oacute;n de Inventario</p>
<form name="form" method="get" action="./bal_act_inv.php">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
    <th class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3"></th>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Todas</th>
    <th class="vtabla"><input name="tipo" type="radio" value="todas"></th>
  </tr>
  <tr>
    <th class="vtabla" scope="row">Controlada</th>
    <th class="vtabla"><input name="tipo" type="radio" value="controlada" checked></th>
  </tr>
  <tr>
    <th class="vtabla" scope="row">No controlada </th>
    <th class="vtabla"><input name="tipo" type="radio" value="no_controlada"></th>
  </tr>
  <tr>
    <th colspan="2" class="vtabla" scope="row">Panader&iacute;as 
      <input name="rango" type="radio" value="pan" checked>
      &nbsp;&nbsp;Rosticer&iacute;as 
      <input name="rango" type="radio" value="ros"></th>
    </tr>
</table>
<p>
  <input name="enviar" type="submit" class="boton" id="enviar" value="Siguiente">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<script language="javascript" type="text/javascript">
	function modificar(id,tipo,rango) {
		window.open("./bal_ifm_minimod.php?id="+id+"&tipo="+tipo+"&rango="+rango,"costo","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480")
		return;
	}
	
	function valida_registro() {
		if (confirm("¿Esta seguro de actualizar los inventarios?"))
			document.form.submit();
		else
			return false;
	}
	
	function aux(num_cia,codmp,mes,anio) {
		var window_aux = window.open("./pan_miniaux.php?num_cia="+num_cia+"&codmp="+codmp+"&mes="+mes+"&anio="+anio,"miniaux","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
		window_aux.moveTo(0,0);
	}
</script>
<p class="title">Actualizaci&oacute;n de Inventario</p>
<form name="form" method="post" action="./bal_act_inv.php?accion=1">
<input name="rango" type="hidden" value="{rango}">
<table class="tabla">
  <tr>
  	<th class="vtabla">Mes</th>
    <td class="vtabla"><strong>{mes}</strong></td>
  </tr>
</table>
<br>
<table class="tabla">
  <!-- START BLOCK : cia -->
   <tr>
    <th colspan="8" class="tabla" scope="row"><a name="{num_cia}"></a>{num_cia} - {nombre_cia}</th>
    </tr>
  <tr>
    <th class="tabla" scope="col">Materia Prima </th>
    <th class="tabla" scope="col">Existencia<br> 
    c&oacute;mputo</th>
    <th class="tabla" scope="col">Existencia<br> 
    f&iacute;sica </th>
    <th class="tabla" scope="col">Faltantes</th>
    <th class="tabla" scope="col">Sobrantes</th>
    <th class="tabla" scope="col">Costo<br> 
      unitario </th>
    <th class="tabla" scope="col">Costo<br> 
      total </th>
    <th class="tabla" scope="col">&nbsp;</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vtabla" scope="row" onClick="aux({num_cia},{codmp},{mes},{anio})">{codmp} {mp}</th>
    <td class="rtabla"><strong>{existencia}</strong></td>
    <td class="rtabla"><strong>{inventario}</strong></td>
    <td class="rtabla"><strong>{falta}</strong></td>
    <td class="rtabla"><strong>{sobra}</strong></td>
    <td class="rtabla"><strong>{costo_unitario}</strong></td>
    <td class="rtabla"><strong>{costo_total}</strong></td>
    <td class="rtabla"><input name="Button" type="button" class="boton" value="Modificar" onClick="modificar({id},'{tipo}',rango.value)"></td>
  </tr>
  <!-- END BLOCK : fila -->
   <tr>
     <th colspan="6" class="rtabla" scope="row">&nbsp;</th>
     <th class="rtabla" scope="row"><font color="#990000">{contra}</font></th>
     <th class="rtabla" scope="row"><font color="#000099">{favor}</font></th>
   </tr>
   <tr>
     <th colspan="6" class="rtabla" scope="row">Total</th>
     <th colspan="2" class="tabla" scope="row"><font color="#{color_total}">{total}</font></th>
     </tr>
   <tr>
    <th colspan="8" scope="row">&nbsp;</th>
    </tr>
  <!-- END BLOCK : cia -->
</table>
<p>
  <input type="button" class="boton" value="Cancelar" onClick="document.location='./bal_act_inv.php'"> 
&nbsp;&nbsp;
<input name="enviar" type="button" class="boton" id="enviar" value="Actualizar" onClick="valida_registro();">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
