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
<td align="center" valign="middle"><p class="title">Hojas de Inventarios por e-mail</p>
  <form action="./bal_ifm_mail.php" method="get" name="form" id="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;as</th>
      <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[1].select(); else if (event.keyCode == 37) num_cia[9].select();" value="{num_cia}" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[2].select(); else if (event.keyCode == 37) num_cia[0].select();" value="{num_cia}" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[3].select(); else if (event.keyCode == 37) num_cia[1].select();" value="{num_cia}" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[4].select(); else if (event.keyCode == 37) num_cia[2].select();" value="{num_cia}" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[5].select(); else if (event.keyCode == 37) num_cia[3].select();" value="{num_cia}" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[6].select(); else if (event.keyCode == 37) num_cia[4].select();" value="{num_cia}" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[7].select(); else if (event.keyCode == 37) num_cia[5].select();" value="{num_cia}" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[8].select(); else if (event.keyCode == 37) num_cia[6].select();" value="{num_cia}" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[9].select(); else if (event.keyCode == 37) num_cia[7].select();" value="{num_cia}" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[10].select(); else if (event.keyCode == 37) num_cia[8].select();" value="{num_cia}" size="3">
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[0].select(); else if (event.keyCode == 37) num_cia[9].select();" value="{num_cia}" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Agregar recibo de avio </th>
      <td class="vtabla"><p>
              <input name="rec" type="checkbox" id="rec" value="1">
          Si</p>        </td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Enviar" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	f.submit();
}

window.onload = f.num_cia[0].select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cia -->
<style>
table.tabla { border: 2px solid #73A8B7; }
p.title { font-family: Arial, Helvetica, sans-serif;  font-size: 16pt;  font-style: normal;  font-weight: bold;    }
th.tabla { background-color: #73A8B7; border: 1px solid #73A8B7;  font-family: Arial, Helvetica, sans-serif;  color: White;  text-align: center;  vertical-align: middle;  font-size: 10pt;        }
td.vtabla { border: 1px solid #73A8B7;  font-family: Arial, Helvetica, sans-serif;  text-align: left;  vertical-align: middle;  font-size: 10pt;      }
td.tabla { border: 1px solid #73A8B7;  font-family: Arial, Helvetica, sans-serif;  text-align: center;  font-size: 10pt;     }
</style>
<p class="title" align="center"><strong>OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</strong> </P>
<p class="title" align="center"><strong>LISTADO DE MATERIAS PRIMAS PARA INVENTARIOS CORRESPONDIENTES A {nombre_mes} <br>{num_cia}&nbsp;{nombre_cia}</strong></P>
<!-- START BLOCK : hoja -->
<table width="90%" border="0" align="center" cellpadding="0">
  <!-- START BLOCK : nombre_cia -->
  <tr class="tabla">
    <th class="tabla" colspan="4" style="border-color:#000000 "><font size="+1">{num_cia} {nombre_cia}</font></th>
  </tr>
  <!-- END BLOCK : nombre_cia -->
  <tr class="tabla">
    <th class="tabla" width="65%" colspan="2" style="border-color:#000000 "><font size="+1">Nombre</font></th>
    <th class="tabla" width="25%" style="border-color:#000000 "><font size="+1">Existencia</font></th>
    <th class="tabla" width="10%" style="border-color:#000000 "><font size="+1">Unidad</font></th>
  </tr>
<!-- START BLOCK : fila -->
<!-- START BLOCK : empaque -->
	<tr class="tabla">
	<td class="tabla" colspan="4" style="border-color:#000000 ">
	<font size="+1"><strong>MATERIAL DE EMPAQUE</strong></font>
	</td>
	</tr>
<!-- END BLOCK : empaque -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	<td class="tabla" style="border-color:#000000 "><font size="+1">{codmp}</font></td> 
    <td class="vtabla" style="border-color:#000000 "><font size="+1">{nombre_mp}</font></td>
    <td class="tabla" style="border-color:#000000 "><font size="+1">&nbsp;</font></td>
    <td class="vtabla" style="border-color:#000000 "><font size="+1">{unidad}</font></td>
  </tr>
 <!-- END BLOCK : fila -->
</table>
<!-- START BLOCK : salto_pagina -->
<br style="page-break-after:always;">
<!-- END BLOCK : salto_pagina -->
<!-- START BLOCK : salto_hoja_par -->
<br>
<!-- END BLOCK : salto_hoja_par -->
<!-- END BLOCK : hoja -->
<!-- END BLOCK : cia -->
</body>
</html>
