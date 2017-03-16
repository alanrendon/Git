<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Liquidaci&oacute;n de Faltantes de Cometra</p>
  <form action="./ban_fal_liq.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia()" onKeyDown="if (event.keyCode == 13) this.blur()" size="3" maxlength="3">
        <input name="nombre" type="text" disabled class="vnombre" id="nombre" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected></option>
        <!-- START BLOCK : admin -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="this.form.submit()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript">
<!--
var f = document.form;

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./ban_fal_liq.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
	else
		f.nombre.value = result;
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : liquidar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Liquidaci&oacute;n de Faltantes de Cometra</p>
  <form action="./ban_fal_liq.php" method="post" name="form"><table class="tabla">
    <!-- START BLOCK : cia -->
    <tr>
      <th colspan="5" class="tabla" scope="col">{num_cia} - {nombre_cia}</th>
      <th class="tabla" scope="col">{encargado}</th>
    </tr>
    <tr>
      <th class="tabla"><input name="checkall" type="checkbox" id="checkall" onClick="checkBlock({ini},{fin},this);calculaDif({num_cia})"></th>
      <th class="tabla">Fecha</th>
      <th class="tabla">Dep&oacute;sito</th>
      <th class="tabla">Faltante</th>
      <th class="tabla">Sobrante</th>
      <th class="tabla">Descrici&oacute;n</th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}">
      <input name="id[]" type="checkbox" id="id" value="{id}" onClick="calculaDif({num_cia})"></td>
      <td class="tabla">{fecha}</td>
      <td class="rtabla">{deposito}</td>
      <td class="rtabla"><font color="#0000FF">
        <input name="fal[]" type="hidden" id="fal" value="{faltante}">
        {faltante}</font></td>
      <td class="rtabla"><font color="#FF0000">
        <input name="sob[]" type="hidden" id="sob" value="{sobrante}">
        {sobrante}</font></td>
      <td class="vtabla">{descripcion}</td>
    </tr>
    <!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="rtabla">Totales</th>
      <th class="rtabla">{deposito}</th>
      <th class="rtabla">{faltante}</th>
      <th class="rtabla">{sobrante}</th>
      <th class="tabla">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" class="rtabla">Diferencia</th>
      <th colspan="2" class="tabla"><font color="#{color_dif}">{diferencia}</font></th>
      <th class="tabla"><input name="dif{num_cia}" type="text" class="rnombre" id="dif{num_cia}" size="10"></th>
    </tr>
    <tr>
      <td colspan="7">&nbsp;</td>
    </tr>
    <!-- END BLOCK : cia -->
    <!-- START BLOCK : no_result -->
    <tr>
      <th colspan="6" class="tabla">No hay resultados </th>
    </tr>
    <!-- END BLOCK : no_result -->
  </table>
  <p>
    <!--<input type="button" class="boton" value="Terminar" onClick="document.location='./ban_fal_liq.php?listado=1'">-->
	<input type="button" class="boton" value="Cancelar" onClick="document.location='./ban_fal_liq.php'">
&nbsp;&nbsp;
    <input name="button" type="button" class="boton" onClick="liquidar(this.form)" value="Liquidar">
   </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var f = document.form;
	
	function checkBlock(ini, fin, checkblock) {
		var form = checkblock.form;
		
		if (form.id.length == undefined) {
			form.id.checked = checkblock.checked ? true : false;
		}
		else {
			for (i = ini; i <= fin; i++) {
				form.id[i].checked = checkblock.checked ? true : false;
			}
		}
	}
	
	function liquidar(form) {
		var count = 0;
		
		if (form.id.length == undefined) {
			count += form.id.checked ? 1 : 0;
		}
		else {
			for (i = 0; i < form.id.length; i++) {
				count += form.id[i].checked ? 1 : 0;
			}
		}
		
		if (count == 0) {
			alert("Debe seleccionar al menos un movimiento");
			return false;
		}
		else if (confirm("¿Desea liquidar los movimiento seleccionados?")) {
			form.submit();
		}
		else {
			return false;
		}
	}
	
	function calculaDif(cia) {
		var dif = 0;
		
		if (f.id.length == undefined)
			dif += f.id.checked ? get_val(f.fal) - get_val(f.sob) : 0;
		else
			for (var i = 0; i < f.id.length; i++)
				if (get_val(f.num_cia[i]) == cia)
					dif += f.id[i].checked ? get_val(f.fal[i]) - get_val(f.sob[i]) : 0;
		
		eval('f.dif' + cia).value = dif != 0 ? numberFormat(dif, 2) : '';
		eval('f.dif' + cia).style.color = dif > 0 ? '0000FF' : 'FF0000';
	}
</script>
<!-- END BLOCK : liquidar -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Faltantes de Cometra Liquidados <br>
      el {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="60%" align="center" class="print">
    <!-- START BLOCK : cia_lis -->
	<tr>
      <th colspan="4" class="print" scope="col"><font size="+1" color="#000000">Cia.: {num_cia}&nbsp;&nbsp;&nbsp;Cuenta: {cuenta}&nbsp;&nbsp;&nbsp;Nombre: {nombre_cia}</font> </th>
    </tr>
    <tr>
      <th width="35%" class="print" scope="col"><font color="#000000">C&oacute;digo</font></th>
      <th width="35%" class="print" scope="col"><font color="#000000">Concepto</font></th>
      <th width="15%" class="print" scope="col"><font color="#000000">Importe</font></th>
      <th width="15%" class="print" scope="col"><font color="#000000">Fecha</font></th>
    </tr>
    <!-- START BLOCK : fila_lis -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint">{codigo}</td>
      <td class="vprint">{concepto}</td>
      <td class="rprint">{importe}</td>
      <td class="print">{fecha}</td>
    </tr>
	<!-- END BLOCK : fila_lis -->
    <tr>
      <th colspan="2" class="rprint"><font color="#000000">Total</font></th>
      <th class="rprint_total">{total}</th>
      <th class="print">&nbsp;</th>
    </tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<!-- END BLOCK : cia_lis -->
	<tr>
	  <th colspan="2" class="rprint_total">Gran Total </th>
      <th class="rprint_total">{gran_total}</th>
      <th class="print">&nbsp;</th>
	</tr>
</table>

<script language="javascript" type="text/javascript">
	window.onload = window.print();
</script>
<!-- END BLOCK : listado -->
</body>
</html>
