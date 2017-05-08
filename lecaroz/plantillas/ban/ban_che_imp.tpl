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
<td align="center" valign="middle">

<p class="title">Impresi&oacute;n de Cheques </p>
<form name="form" method="get" action="./ban_che_imp.php">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th width="174" class="vtabla"><input name="tipo" type="radio" value="todos" onClick="form.fecha1.disabled=true;form.fecha2.disabled=true;form.num_cia.disabled=true;form.num_proveedor.disabled=true;form.folio1.disabled=true;form.folio2.disabled=true;form.num_cia_folio.disabled=true;" checked>
      Todos</th>
    <td width="262" class="vtabla">Imprimir todos los cheques</td>
    </tr>
  <tr>
    <th class="vtabla"><input name="tipo" type="radio" value="folio" onClick="form.fecha1.disabled=true;form.fecha2.disabled=true;form.num_cia.disabled=true;form.num_proveedor.disabled=true;form.folio1.disabled=false;form.folio2.disabled=false;form.folio1.select();form.num_cia_folio.disabled=false;">
      Por folio</th>
    <td class="vtabla">del 
      <input name="folio1" type="text" disabled="true" class="insert" id="folio12" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.folio2.select();
else if (event.keyCode == 37) form.num_cia_folio.select();" size="7" maxlength="7"> 
      al 
      <input name="folio2" type="text" disabled="true" class="insert" id="folio2" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 37) form.folio1.select();
else if (event.keyCode == 13 || event.keyCode == 39) form.num_cia_folio.select();" size="7" maxlength="7"> 
      de la compa&ntilde;&iacute;a 
      <input name="num_cia_folio" type="text" disabled="true" class="insert" id="num_cia_folio" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.ultimo_folio.select();
else if (event.keyCode == 37) form.folio2.select();" size="3" maxlength="3"></td>
    </tr>
  <tr>
    <th class="vtabla"><input name="tipo" type="radio" value="cia" onClick="form.fecha1.disabled=true;form.fecha2.disabled=true;form.num_cia.disabled=false;form.num_proveedor.disabled=true;form.folio1.disabled=true;form.folio2.disabled=true;form.num_cia.select();form.num_cia_folio.disabled=true;">
      Por compa&ntilde;&iacute;a </th>
    <td class="vtabla"><input name="num_cia" type="text" disabled="true" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13) form.ultimo_folio.select();" size="3" maxlength="3"></td>
    </tr>
  <tr>
    <th class="vtabla"><input name="tipo" type="radio" value="proveedor" onClick="form.fecha1.disabled=true;form.fecha2.disabled=true;form.num_cia.disabled=true;form.num_proveedor.disabled=false;form.folio1.disabled=true;form.folio2.disabled=true;form.num_proveedor.select();form.num_cia_folio.disabled=true;">
      Por proveedor </th>
    <td class="vtabla"><input name="num_proveedor" type="text" disabled="true" class="insert" id="num_proveedor" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13) form.ultimo_folio.focus();" size="3" maxlength="4"></td>
  </tr>
  <tr>
    <th class="vtabla"><input name="tipo" type="radio" value="fecha" onClick="form.fecha1.disabled=false;form.fecha2.disabled=false;form.num_cia.disabled=true;form.num_proveedor.disabled=true;form.folio1.disabled=true;form.folio2.disabled=true;form.fecha1.select();form.num_cia_folio.disabled=true;">
      Por fecha</th>
    <td class="vtabla">del 
      <input name="fecha1" type="text" disabled="true" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) form.fecha2.select();" value="{fecha1}" size="10" maxlength="10"> 
      al 
      <input name="fecha2" type="text" disabled="true" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 37) form.fecha1.select();
else if (event.keyCode == 13 || event.keyCode == 39) form.ultimo_folio.select();" value="{fecha2}" size="10" maxlength="10"> 
      <font size="-2">(ddmmaa)</font> </td>
    </tr>
  <tr>
    <th class="vtabla">Ultimo folio en los cheques</th>
    <td class="vtabla"><input name="ultimo_folio" type="text" class="insert" id="ultimo_folio" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13) form.siguiente.focus();" size="8" maxlength="8">
      &nbsp;&nbsp;
      <input name="orden" type="radio" value="asc">
      Ascendente 
      <input name="orden" type="radio" value="desc" checked>
      Descendente</td>
  </tr>
  <tr>
    <th class="vtabla">&iquest;Solo para polizas? </th>
    <td class="vtabla"><input name="poliza" type="checkbox" id="poliza" onClick="if (this.checked) ultimo_folio.disabled=true; else ultimo_folio.disabled=false;" value="1">
      Si</td>
  </tr>
</table>

<p>
  <input name="siguiente" type="button" class="boton" id="siguiente" onClick="valida_registro();" value="Siguiente">
</p>
</form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.tipo[1].checked) {
			if (document.form.folio1.value <= 0) {
				alert("Debe especificar el folio inicial");
				document.form.folio1.select();
				return false;
			}
			else if (document.form.folio2.value <= 0) {
				alert("Debe especificar el folio final");
				document.form.folio2.select();
				return false;
			}
			else if (document.form.num_cia_folio.value <= 0) {
			alert("Debe especificar la compañía");
			document.form.num_cia_folio.select();
			return false;
			}
			else if (document.form.ultimo_folio.disabled == false && document.form.ultimo_folio.value <= 0) {
				alert("Debe especificar el último folio de los cheques");
				document.form.ultimo_folio.select();
				return false;
			}
			else
				document.form.submit();
		}
		else if (document.form.tipo[2].checked) {
			if (document.form.num_cia.value <= 0) {
				alert("Debe especificar la compañía");
				document.form.num_cia.select();
				return false;
			}
			else if (document.form.ultimo_folio.disabled == false && document.form.ultimo_folio.value <= 0) {
				alert("Debe especificar el último folio de los cheques");
				document.form.ultimo_folio.select();
				return false;
			}
			else
				document.form.submit();
		}
		else if (document.form.tipo[3].checked) {
			if (document.form.num_proveedor <= 0) {
				alert("Debe especificar el proveedor");
				document.form.num_proveedor.select();
				return false;
			}
			else if (document.form.ultimo_folio.disabled == false && document.form.ultimo_folio.value <= 0) {
				alert("Debe especificar el último folio de los cheques");
				document.form.ultimo_folio.select();
				return false;
			}
			else
				document.form.submit();
		}
		else if (document.form.tipo[4].checked) {
			if (document.form.fecha1.value == "") {
				alert("Debe especificar la fecha inicial");
				document.form.fecha1.select();
				return false;
			}
			else if (document.form.fecha2.value == "") {
				alert("Debe especificar la fecha final");
				document.form.fecha2.select();
				return false;
			}
			else if (document.form.ultimo_folio.disabled == false && document.form.ultimo_folio.value <= 0) {
				alert("Debe especificar el último folio de los cheques");
				document.form.ultimo_folio.select();
				return false;
			}
			else
				document.form.submit();
		}
		else {
			if (document.form.ultimo_folio.disabled == false && document.form.ultimo_folio.value <= 0) {
				alert("--Debe especificar el último folio de los cheques");
				document.form.ultimo_folio.select();
				return false;
			}
			else
				document.form.submit();
		}
	}
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Cheques a Imprimir</p>
<form name="form">
<input name="tipo" type="hidden" value="{tipo}">
<input name="param1" type="hidden" value="{param1}">
<input name="param2" type="hidden" value="{param2}">
<input name="param3" type="hidden" value="{param3}">
<input name="num_cheque" type="hidden" value="{ultimo_cheque}">
<input name="orden" type="hidden" value="{orden}">
<input name="numfilas" type="hidden" value="{numfilas}">
  <table width="90%" class="tabla">
    <tr>
      <th width="10%" class="tabla" scope="col">&nbsp;</th>
      <th width="15%" class="tabla" scope="col">Fecha del cheque </th>
      <th width="15%" class="tabla" scope="col">Folio del cheque </th>
      <th width="25%" class="tabla" scope="col">Beneficiario</th>
      <th width="25%" class="tabla" scope="col">Concepto</th>
      <th width="10%" class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : cia -->
	<tr>
      <th class="tabla" scope="col">Cia.: {num_cia} </th>
      <th colspan="2" class="tabla" scope="col">Cuenta.: {cuenta}</th>
      <th colspan="3" class="tabla" scope="col">{nombre_cia}</th>
      </tr>
    <!-- START BLOCK : cheque -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id{i}" type="checkbox" id="id{i}" value="{id}"></td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{folio}</td>
      <td class="vtabla"><strong>{a_nombre}</strong></td>
      <td class="vtabla">{concepto}</td>
      <th class="rtabla">{importe}</th>
    </tr>
	<!-- END BLOCK : cheque -->
    <tr>
      <th class="rtabla">&nbsp;</th>
      <th class="rtabla">&nbsp;</th>
      <th class="rtabla">&nbsp;</th>
      <th class="rtabla">&nbsp;</th>
      <th class="rtabla">&nbsp;</th>
      <th class="rtabla"><font size="+1">{total}</font></th>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>
	<!-- END BLOCK : cia -->
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_che_imp.php'">
&nbsp;&nbsp; 
<input name="automatico" type="checkbox" id="automatico" value="TRUE">
<font size="-1" face="Geneva, Arial, Helvetica, sans-serif">Imprimir autom&aacute;ticamente</font>&nbsp;&nbsp;    
<input type="button" class="boton" value="Imprimir Cheques" onClick="imprimir()">
  </p>
  </form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function imprimir() {
		var mensaje = "Las siguientes acciones se ejecutaran:\n\n";
		mensaje += "1. Generación de cheques\n";
		mensaje += "2. Actualización de la base de datos\n";
		
		// Los cheques se imprimiran automáticamente
		if (document.form.automatico.checked)
			mensaje += "3. Impresión automática\n";
		
		mensaje += "\n¿Desea continuar?\n\n";
		mensaje += "NOTA: Una vez generados los cheques no se podra repetir el proceso";
		
		if (confirm(mensaje)) {
			// Abrir popup para mostrar cheques
			window.open("","cheques","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=800,height=600");
			// Cambiar propiedades del formulario
			document.form.target = "cheques";
			document.form.method = "post";
			document.form.action = "./ban_gen_che.php";
			document.form.submit();
			
			//document.location = "./ban_che_imp.php";
		}
		else
			return false;
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
