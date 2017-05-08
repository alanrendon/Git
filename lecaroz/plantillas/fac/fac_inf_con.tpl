<!-- START BLOCK : cia -->
<form name="form" method="get" action="./fac_inf_con.php">
<input name="temp" type="hidden">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Pr&eacute;stamos de Infonavit</p>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro();"> 
    </p></td>
</tr>
</table>
</form>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.num_cia <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : cia -->

<!-- START BLOCK : empleados -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="get" action="./fac_inf_con.php">
<p class="title">Listado de Pr&eacute;stamos de Infonavit</p>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="vtabla" scope="col">{num_cia} - {nombre_cia} </th>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Empleado</th>
    </tr>
    <!-- START BLOCK : fila1 -->
	<tr>
      <td class="tabla"><input name="id" type="radio" value="{id}"></td>
      <td class="vtabla">{num_emp} - {nombre_emp} </td>
    </tr>
	<!-- END BLOCK : fila1 -->
  </table>  <p class="title">
  <input type="button" class="boton" value="Regresar" onClick="history.back()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="valida_registro()">
  </p>
  </form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		var count = 0;
		
		if (document.form.id.length == undefined) {
			if (!document.form.id.checked) {
				alert("Debe seleccionar al menos un empleado de la lista");
				return false;
			}
			else
				document.form.submit();
		}
		else {
			for (i=0; i<document.form.id.length; i++)
				if (document.form.id[i].checked)
					count++;
			if (count <= 0) {
				alert("Debe seleccionar al menos un empleado de la lista");
				return false;
			}
			else
				document.form.submit();
		}
	}
</script>
<!-- END BLOCK : empleados -->

<!-- START BLOCK : pagos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top"><p class="title">Listado de Pr&eacute;stamos de Infonavit</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Empleado</th>
    </tr>
    <tr>
      <th class="tabla">{num_cia} - {nombre_cia} </th>
      <th class="tabla">{nombre}</th>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Fecha mov. </th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila2 -->
	<tr>
      <td class="tabla">{fecha_mov}</td>
      <td class="tabla">{folio}</td>
      <td class="vtabla">{mes}</td>
      <th class="rtabla">{importe}</th>
    </tr>
	<!-- END BLOCK : fila2 -->
	<tr>
      <th colspan="3" class="rtabla">Total</th>
      <th class="rtabla">{total}</th>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onClick="history.back()">
  </p></td>
</tr>
</table>
<!-- END BLOCK : pagos -->
