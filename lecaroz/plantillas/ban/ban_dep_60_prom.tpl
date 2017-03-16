<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Dep&oacute;sitos Menores al 60% del Promedio Mensual</p>
  <form action="./ban_dep_60_prom.php" method="post" name="form">
  <input type="hidden" name="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
        <!-- START BLOCK : admin -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Rango</th>
      <td class="vtabla"><input name="rango" type="radio" value="0" checked>
        Todas<br>
        <input name="rango" type="radio" value="1">
        Panader&iacute;as<br>
        <input name="rango" type="radio" value="2">
        Rosticer&iacute;as</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1" {1}>ENERO</option>
        <option value="2" {2}>FEBRERO</option>
        <option value="3" {3}>MARZO</option>
        <option value="4" {4}>ABRIL</option>
        <option value="5" {5}>MAYO</option>
        <option value="6" {6}>JUNIO</option>
        <option value="7" {7}>JULIO</option>
        <option value="8" {8}>AGOSTO</option>
        <option value="9" {9}>SEPTIEMBRE</option>
        <option value="10" {10}>OCTUBRE</option>
        <option value="11" {11}>NOVIEMBRE</option>
        <option value="12" {12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) form.alt[0].select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  
    <br>
    <table class="tabla">
      <tr>
        <th colspan="10" class="tabla">Compa&ntilde;&iacute;as que no se tomara en cuenta sus dep&oacute;sitos</th>
        </tr>
      <tr>
        <td class="tabla"><input name="alt[]" type="text" class="insert" id="alt" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) form.alt[1].select()" size="3" maxlength="3"></td>
        <td class="tabla"><input name="alt[]" type="text" class="insert" id="alt" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) form.alt[2].select()" size="3" maxlength="3"></td>
        <td class="tabla"><input name="alt[]" type="text" class="insert" id="alt" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) form.alt[3].select()" size="3" maxlength="3"></td>
        <td class="tabla"><input name="alt[]" type="text" class="insert" id="alt" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) form.alt[4].select()" size="3" maxlength="3"></td>
        <td class="tabla"><input name="alt[]" type="text" class="insert" id="alt" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) form.alt[5].select()" size="3" maxlength="3"></td>
        <td class="tabla"><input name="alt[]" type="text" class="insert" id="alt" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) form.alt[6].select()" size="3" maxlength="3"></td>
        <td class="tabla"><input name="alt[]" type="text" class="insert" id="alt" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) form.alt[7].select()" size="3" maxlength="3"></td>
        <td class="tabla"><input name="alt[]" type="text" class="insert" id="alt" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) form.alt[8].select()" size="3" maxlength="3"></td>
        <td class="tabla"><input name="alt[]" type="text" class="insert" id="alt" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) form.alt[9].select()" size="3" maxlength="3"></td>
        <td class="tabla"><input name="alt[]" type="text" class="insert" id="alt" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="3" maxlength="3"></td>
      </tr>
    </table>
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td align="right" style="font-family: Arial, Helvetica, sans-serif; font-size: 12pt;"><strong>MEXICO D.F., A {dia} DE {mes} DE {anio} </strong></td>
  </tr>
  <tr>
    <td style="font-family: Arial, Helvetica, sans-serif; font-size: 12pt;"><br>
    <br>
    <br>
    <strong>{admin}</strong><br>
    PRESENTE</td>
  </tr>
  <tr>
    <td style="font-family: Arial, Helvetica, sans-serif; font-size: 12pt;"><br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;POR ESTE CONDUCTO ME PERMITO SALUDARLE Y A SU VEZ COMENTARLE QUE CON ESTA FECHA HEMOS DETECTADO UN <strong>DESCUIDO POR SU PARTE</strong> EN EL CONTROL DE EFECTIVOS. S&Iacute;RVASE A TOMAR NOTA Y CORREGIRLO LO ANTES POSIBLE, PARA QUE ESTO NO OCASIONE PROBLEMAS POSTERIORES. <br>    
    <br></td>
  </tr>
  <tr>
    <td><table width="30%" align="center" class="print">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="3" class="print" scope="col"><font size="+1">{nombre_cia}</font> </th>
    </tr>
    <tr>
      <th width="20%" class="print">D&iacute;a</th>
      <th width="40%" class="print">Efectivo</th>
      <th width="40%" class="print">Dep&oacute;sito</th>
    </tr>
    <!-- START BLOCK : dia -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{dia}</td>
      <td class="rprint">{efectivo}&nbsp;&nbsp;&nbsp;</td>
      <td class="rprint">{deposito}&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<!-- END BLOCK : dia -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="2" class="print">Promedio mensual </th>
	  <th class="rprint">{promedio}&nbsp;&nbsp;&nbsp;</th>
	</tr>
	<tr>
	  <td colspan="8" class="print">&nbsp;</td>
    </tr>
	<!-- END BLOCK : cia -->
</table></td>
  </tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : listado -->
</body>
</html>
