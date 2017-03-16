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
<td align="center" valign="middle"><form action="./pan_mod_ticket.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
    <input name="fecha" type="hidden" id="fecha" value="{fecha}" />
    <table class="tabla">
  <tr>
    <th colspan="2" class="tabla" scope="col">Pastel</th>
    </tr>
  <tr>
    <th class="tabla" scope="col">Pan</th>
    <th class="tabla" scope="col">Pastel</th>
  </tr>
  <tr>
    <td class="tabla"><input name="pan[]" type="text" class="insert" id="pan" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,pastel[0],null,pastel[0],pan[4],pan[1])" value="{pan0}" size="5" /></td>
    <td class="tabla"><input name="pastel[]" type="text" class="insert" id="pastel" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,pan[1],pan[0],null,pastel[4],pastel[1])" value="{pastel0}" size="5" /></td>
  </tr>
  <tr>
    <td class="tabla"><input name="pan[]" type="text" class="insert" id="pan" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,pastel[1],null,pastel[1],pan[0],pan[2])" value="{pan1}" size="5" /></td>
    <td class="tabla"><input name="pastel[]" type="text" class="insert" id="pastel" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,pan[2],pan[1],null,pastel[0],pastel[2])" value="{pastel1}" size="5" /></td>
  </tr>
  <tr>
    <td class="tabla"><input name="pan[]" type="text" class="insert" id="pan" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,pastel[2],null,pastel[2],pan[1],pan[3])" value="{pan2}" size="5" /></td>
    <td class="tabla"><input name="pastel[]" type="text" class="insert" id="pastel" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,pan[3],pan[2],null,pastel[1],pastel[3])" value="{pastel2}" size="5" /></td>
  </tr>
  <tr>
    <td class="tabla"><input name="pan[]" type="text" class="insert" id="pan" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,pastel[3],null,pastel[3],pan[2],pan[4])" value="{pan3}" size="5" /></td>
    <td class="tabla"><input name="pastel[]" type="text" class="insert" id="pastel" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,pan[4],pan[3],null,pastel[2],pastel[4])" value="{pastel3}" size="5" /></td>
  </tr>
  <tr>
    <td class="tabla"><input name="pan[]" type="text" class="insert" id="pan" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,pastel[4],null,pastel[4],pan[3],pan[0])" value="{pan4}" size="5" /></td>
    <td class="tabla"><input name="pastel[]" type="text" class="insert" id="pastel" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,pan[0],pan[0],null,pastel[3],pastel[0])" value="{pastel4}" size="5" /></td>
  </tr>
</table>

  <p>
    <input type="button" class="boton" value="Cancelar" onclick="self.close()" />
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 13 && lt && lt != null) lt.select();
	else if (keyCode == 13 && rt && rt != null) rt.select();
	else if (keyCode == 13 && up && up != null) up.select();
	else if (keyCode == 13 && dn && dn != null) dn.select();
}

function validar() {
	if (confirm("¿Son correctos los datos?"))
		f.submit();
}

window.onload = f.pan[0].select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	window.opener.document.location.reload();
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
