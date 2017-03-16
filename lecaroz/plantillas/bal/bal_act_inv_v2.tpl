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
<td align="center" valign="middle"><p class="title">Proceso de Diferencias de Inventario</p>
  <form action="./bal_act_inv_v2.php" method="get" name="form">
  <input name="num_cia" type="hidden" value="{num_cia}">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="todas" checked>
        Todas<br>
        <input name="tipo" type="radio" value="controladas">
        Controladas<br>
        <input name="tipo" type="radio" value="no_controladas">
        No controladas </td>
    </tr>
  </table>  <p>
    <input name="Submit" type="submit" class="boton" value="Siguiente">
  </p></form></td>
</tr>
</table>
<!-- START BLOCK : alerta -->
<script language="javascript" type="text/javascript">
<!--
function alerta() {
	window.open("./alerta_reservas.htm","","top=284,left=112,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=200");
	document.location = "./bal_enc_cap.php";
}

window.onload = alerta();
-->
</script>
<!-- END BLOCK : alerta -->
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Proceso de Diferencias de Inventario</p>
  <form action="./bal_act_inv_v2.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <input name="tipo" type="hidden" value="{tipo}">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <th class="tabla">{num_cia} - {nombre_cia} </th>
      <th class="tabla">{mes}</th>
      <th class="tabla">{anio}</th>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Materia Prima </th>
      <th class="tabla" scope="col">Existencia<br>
        C&oacute;mputo</th>
      <th class="tabla" scope="col">Existencia<br>
        F&iacute;sica</th>
      <th class="tabla" scope="col">Faltantes</th>
      <th class="tabla" scope="col">Sobrantes</th>
      <th class="tabla" scope="col">Costo<br>
        Unitario</th>
      <th colspan="2" class="tabla" scope="col">Costo<br>
        Total</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla" onClick="aux({num_cia},{codmp},{mes},{anio})" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';"><strong><font color="#{color_mp}">{codmp}</font></strong></td>
      <td class="vtabla" onClick="aux({num_cia},{codmp},{mes},{anio})" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';"><strong><font color="#{color_mp}">{nombre_mp}</font></strong></td>
      <td class="rtabla"><strong>{existencia}</strong></td>
      <td class="rtabla" onClick="modificar({id})" onMouseOver="this.style.cursor = 'pointer';" onMouseOut="this.style.cursor = 'default';"><strong>{inventario}</strong></td>
      <td class="rtabla"><strong><font color="#FF0000">{falta}</font></strong></td>
      <td class="rtabla"><strong><font color="#0000FF">{sobra}</font></strong></td>
      <td class="rtabla"><strong>{costo_unitario}</strong></td>
      <td colspan="2" class="rtabla"><strong>{costo_total}</strong></td>
      </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="7" class="vtabla">&nbsp;</th>
      <th class="rtabla"><font color="#FF0000" size="+1">{contra}</font></th>
      <th class="rtabla"><font color="#0000FF" size="+1">{favor}</font></th>
    </tr>
    <tr>
      <th colspan="7" class="rtabla">Total</th>
      <th colspan="2" class="tabla">{total}</th>
      </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Faltantes</th>
      <th class="tabla" scope="col">Sobrantes</th>
      </tr>
    <tr>
      <td class="tabla"><strong><font size="+1" color="#FF0000">{total_contra}</font></strong></td>
      <td class="tabla"><strong><font size="+1" color="#0000FF">{total_favor}</font></strong></td>
      </tr>
    <tr>
      <td colspan="2" class="tabla"><strong>{gran_total}</strong></td>
      </tr>
  </table>
  <br>
  <table class="tabla">
    <tr>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value;this.select();" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia)" onKeyDown="if (event.keyCode == 13) next.focus();" value="{num_cia_next}" size="3" maxlength="3">
        <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" value="{nombre_cia_next}" size="40" maxlength="40"></td>
      <td class="vtabla"><input name="next" type="submit" class="boton" id="next" value="Siguiente >>"></td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location = './bal_act_inv_v2.php?cancelar=1'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Actualizar Todo" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar() {
		if (confirm("Al hacer click en ACEPTAR se generarán todas\nlas diferencias y se actualizarán inventarios.\n¿Esta seguro de realizar el proceso?"))
			document.location = 'bal_act_inv_v2.php?terminar=1';
		else
			return false;
	}
	
	function actualiza_cia(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function aux(num_cia,codmp,mes,anio) {
		var window_aux = window.open("./aux_inv_v3.php?listado=desglozado&controlada=todas&num_cia="+num_cia+"&codmp="+codmp+"&mes="+mes+"&anio="+anio,"miniaux","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
		window_aux.moveTo(0,0);
	}
	
	function modificar(id) {
		window.open("./bal_ifm_minimod_v2.php?id="+id,"costo","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480")
		return;
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : listado -->
</body>
</html>
