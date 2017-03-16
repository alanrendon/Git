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
<td align="center" valign="middle"><p class="title">Consulta de Locales</p>
  <form action="./ren_art_con.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Local</th>
      <td class="vtabla"><input name="local" type="text" class="insert" id="local" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) arr.select()" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Inmobiliaria</th>
      <td class="vtabla"><input name="arr" type="text" class="insert" id="arr" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) local.select()" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Bloque</th>
      <td class="vtabla"><input name="bloque" type="radio" value="0" checked>
        Todos<br>
        <input name="bloque" type="radio" value="1">
        Propios<br>
        <input name="bloque" type="radio" value="2">
        Ajenos</td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
´<script language="javascript" type="text/javascript">
<!--
function validar(f) {
	f.submit();
}

window.onload = document.form.local.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Locales</p>
  <table class="tabla">
    <!-- START BLOCK : arr -->
	<tr>
      <th colspan="10" class="vtabla" scope="col">{cod} {arr} </th>
      </tr>
    <tr>
      <th class="tabla" scope="col">Local</th>
      <th class="tabla" scope="col">Bloque</th>
      <th class="tabla" scope="col">Arrendatario</th>
      <th class="tabla" scope="col">R.F.C.</th>
      <th class="tabla" scope="col">Renta</th>
      <th class="tabla" scope="col">Mantenimiento</th>
      <th class="vtabla" scope="col">Agua</th>
      <th class="vtabla" scope="col">Ret. I.V.A. </th>
      <th class="vtabla" scope="col">Ret. I.S.R. </th>
      <th class="vtabla" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{local} {nombre} </td>
      <td class="tabla" style="color: #{color};">{bloque}</td>
      <td class="vtabla">{art}</td>
      <td class="tabla">{rfc}</td>
      <td class="rtabla">{renta}</td>
      <td class="rtabla">{mantenimiento}</td>
      <td class="rtabla">{agua}</td>
      <td class="tabla">{ret}</td>
      <td class="tabla">{isr}</td>
      <td class="tabla"><input type="button" class="boton" value="M" onClick="mod({id})">
        <input type="button" class="boton" value="B" onClick="del({id})"></td>
    </tr>
	<!-- END BLOCK : fila -->
	<tr>
	  <td colspan="10" class="vtabla">&nbsp;</td>
	  </tr>
	<!-- END BLOCK : arr -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ren_art_con.php'"> 
    </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function mod(id) {
	var win = window.open("./ren_art_mod.php?id=" + id,"mod","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
	win.focus();
}

function del(id) {
	var win = window.open("./ren_art_del.php?id=" + id,"del","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=300,height=200");
	win.focus();
}
//-->
</script>
<!-- END BLOCK : listado -->
</body>
</html>
