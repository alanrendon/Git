<!-- START BLOCK : obtener_compania -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title" align="center">Movimiento de Expendios</p>
<form name="form" action="./pan_exp_cap.php" method="get" onKeyPress="if (event.keyCode == 13) return false">
<table class="tabla" align="center">
    <tr align="center">
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="compania" type="text" class="insert" id="compania" size="3" maxlength="3" onKeyDown="if (event.keyCode == 13) valida_registro(this)">        </th>
      <!--
	  <th class="vtabla">Fecha (dd/mm/aaaa) </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" size="10" maxlength="10" value="{fecha}"></td>
	  -->
    </tr>
</table>
<p></p>
<input name="next" type="button" class="boton" id="next" onClick="valida_registro(compania)" value="Siguiente">
</form>
</td>
</tr>
</table>
<script type="text/javascript" language="JavaScript">
	function valida_registro(num_cia) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no es tuya");
				num_cia.value = "";
				num_cia.select();
				return false;
			}
			else {
				document.form.submit();
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			return false;
		}
	}
	
	window.onload = document.form.compania.select();
</script>
<!-- END BLOCK : obtener_compania -->

<!-- START BLOCK : hoja -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.compania.value <= 0) {
			alert('Debe especificar una compañía');
			document.form.compania.select();
		}
		else if(document.form.fecha.value == "") {
			alert('Debe especificar la fecha');
			document.form.fecha.select();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.compania.select();
		}
	}
		
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.compania.select();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" valign="middle">
<td>
<p class="title" align="center">Movimiento de Expendios</p>
<form name="form" action="./ver_pan_exp_cap.php?tabla={tabla}" method="post">
<input type="hidden" name="numfilas" value="{numfilas}">
<input name="temp" type="hidden">
<table class="tabla" align="center">
    <tr align="center">
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="compania" type="hidden" value="{num_cia}"><font size="+1"><b>{num_cia} &#8212; {nombre_cia}</b></font></td>
      <th class="vtabla">Fecha</th>
      <td class="vtabla"><input name="fecha" type="hidden" value="{fecha}"><font size="+1"><b>{fecha}</b></font></td>
    </tr>
  </table>
  <br>
  <table class="tabla" align="center">
	<tr>
      <th class="tabla" align="center" colspan="2">C&oacute;digo y nombre de expendio</th>
      <th align="center" class="tabla">Partidas</th>
      <th align="center" class="tabla">Devuelto</th>
      <th class="tabla" align="center">Total</th>
      <th class="tabla" align="center">Abono </th>
    </tr>
    <!-- START BLOCK : rows -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td align="center" class="tabla">
          <input name="num_expendio{i}" type="hidden" value="{exp}"><b>{exp_pan}</b>
      </td>
      <td align="left" class="vtabla">
          <input name="nombre{i}" type="hidden" value="{nombre}"><b>{nombre}</b>
      </td>

      <td class="tabla" align="center">
          <input name="pan_p_venta{i}" type="text" class="insert" id="pan_p_venta{i}" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.devolucion{i}.select();
else if (event.keyCode == 37) form.abono{i}.select();
else if (event.keyCode == 38) form.pan_p_venta{back}.select();
else if (event.keyCode == 40) form.pan_p_venta{next}.select();" value="{pan_p_venta}" size="12" maxlength="12">
      </td>
      <td align="center" class="tabla">
        <input name="devolucion{i}" type="text" class="insert" id="devolucion{i}" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) pan_p_expendio{i}.select();
else if (event.keyCode == 37) pan_p_venta{i}.select();
else if (event.keyCode == 38) devolucion{back}.select();
else if (event.keyCode == 40) devolucion{next}.select();" value="{devolucion}" size="12" maxlength="12">
      </td>
      <td class="tabla" align="center">
          <input name="pan_p_expendio{i}" type="text" class="insert" id="pan_p_expendio{i}" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) abono{i}.select();
else if (event.keyCode == 37) devolucion{i}.select();
else if (event.keyCode == 38) pan_p_expendio{back}.select();
else if (event.keyCode == 40) pan_p_expendio{next}.select();" value="{pan_p_expendio}" size="12" maxlength="12">
      </td>
      <td class="tabla" align="center">
        <input name="abono{i}" type="text" class="insert" id="abono{i}" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13) pan_p_venta{next}.select();
else if (event.keyCode == 39) pan_p_venta{i}.select();
else if (event.keyCode == 37) pan_p_expendio{i}.select();
else if (event.keyCode == 38) abono{back}.select();
else if (event.keyCode == 40) abono{next}.select();" value="{abono}" size="12" maxlength="12">
      </td>
    </tr>
	<!-- END BLOCK : rows -->
  </table>
  <p align="center">
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" name="enviar" value="Captura de Movimientos" onclick="valida_registro()"><br><br>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
  </p>
</form>  
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	window.onload = document.form.pan_p_venta0.select();
</script>
<!-- END BLOCK : hoja -->