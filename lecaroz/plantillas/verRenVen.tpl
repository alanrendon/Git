<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="./styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Contratos Vencidos</p>
  <form action="carta_contrato_vencido.php" method="post" name="form" target="cartas">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col"><input name="checkall" type="checkbox" id="checkall" onclick="checkAll()" value="" /></th>
      <th class="tabla" scope="col">Local</th>
      <th class="tabla" scope="col">Arrendatario</th>
      <th class="tabla" scope="col">Giro</th>
      <th class="tabla" scope="col">Inicio</th>
      <th class="tabla" scope="col">Fin</th>
      <th class="tabla" scope="col">Tipo</th>
      <th class="tabla" scope="col">INPC</th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr>
      <td class="tabla"><input name="id{i}" type="checkbox" id="id{i}" value="{id}" /></td>
      <td class="vtabla">{num_local} {nombre_local}</td>
      <td class="vtabla">{nombre_arr}</td>
      <td class="vtabla">{giro}</td>
      <td class="tabla">{fecha_ini}</td>
      <td class="tabla">{fecha_fin}</td>
      <td class="tabla">{tipo}</td>
      <td class="tabla"><input name="inpc[]" type="text" class="rinsert" id="inpc" onfocus="tmp.value=this.value;this.select()" onchange="isFloat(this,tmp)" onkeydown="if(event.keyCode==13){
if(inpc.length==undefined){
inpc.blur();
}
else{
inpc[{next}].select();
}
}" size="5" maxlength="5" /></td>
    </tr>
    <!-- END BLOCK : fila -->
  </table>
  <p>
    <input name="" type="button" class="boton" onclick="self.close()" value="Cerrar" />
    &nbsp;&nbsp;
    <input name="" type="button" class="boton" onclick="imprimirCartas()" value="Cartas" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function checkAll() {
	if (f.inpc.length == undefined)
		f.id0.checked = f.checkall.checked;
	else
		for (var i = 0; i < f.inpc.length; i++)
			document.getElementById('id' + i).checked = f.checkall.checked;
}

function imprimirCartas() {
	var cont = 0;
	
	if (f.inpc.length == undefined)
		cont += f.id0.checked ? 1 : 0;
	else
		for (var i = 0; i < f.inpc.length; i++)
			cont += document.getElementById('id' + i).checked ? 1 : 0;
	
	if (cont == 0) {
		alert('Debe seleccionar al menos un contrato');
		return false;
	}
	else {
		var win = window.open('', 'cartas', "toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768");
		f.submit();
		win.focus();
	}
}

window.onload = f.inpc.length == undefined ? f.inpc.select() : f.inpc[0].select();
//-->
</script>
</body>
</html>
