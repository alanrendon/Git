<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Carta de Contratos Vencidos
 </p>
  <form action="./ren_car_ven.php" method="post" name="form">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Local</th>
      <th class="tabla" scope="col">Arrendatario</th>
      <th class="tabla" scope="col">Vencimiento</th>
      <th class="tabla" scope="col">Renta</th>
      <th class="tabla" scope="col">%</th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr>
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}" /></td>
      <td class="tabla">{num_local}</td>
      <td class="vtabla">{arr}</td>
      <td class="tabla">{fecha_final}</td>
      <td class="rtabla">{renta}</td>
      <td class="tabla">{por_incremento}</td>
    </tr>
    <!-- END BLOCK : fila -->
    <!-- START BLOCK : no_result -->
    <tr>
      <td colspan="6" class="tabla"><strong>No hay contratos por vencer</strong></td>
      </tr>
    <!-- END BLOCK : no_result -->
  </table>  
  <br />
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">% de Incremento General</th>
      <td class="vtabla"><input name="inc" type="text" class="insert" id="inc" onfocus="tmp.value=this.value;this.select()" onchange="isFloat(this,2,tmp)" onkeydown="if (event.keyCode == 13) this.blur()" size="5" maxlength="5" /></td>
    </tr>
  </table>
  <p>
    <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" onclick="validar()" />
</p></form>
  </td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function validar() {
	var cont = 0;
	
	if (f.id.length == undefined)
		cont = f.id.checked ? 1 : 0;
	else
		for (var i = 0; i < f.id.length; i++)
			cont += f.id[i].checked ? 1 : 0;
	
	if (cont > 0) {
		
	}
	else {
		alert('Debe seleccionar al menos un arrendatario');
		return false;
	}
}

window.onload = function () {
	if (!f.id)
		f.siguiente.disabled = true;
	
	f.inc.select();
}
//-->
</script>
</body>
</html>
