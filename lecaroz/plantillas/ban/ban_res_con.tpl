<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		// Actualizar ventana de conciliacion
		window.opener.document.form.target = "_self";
		window.opener.document.form.method = "get";
		window.opener.document.form.action = "./ban_conciliacion.php";
		window.opener.document.form.submit();
		
		// Cerrar ventana actual
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : resultados -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (confirm("¿Son correctos los datos?"))
			document.form.submit();
		else
			return false;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Cuenta</th>
    <th class="tabla" scope="col">Fecha de Conciliaci&oacute;n</th>
  </tr>
  <tr>
    <td class="tabla" scope="row"><font size="+2" color="#0066FF"><strong>{num_cia} - {nombre_cia} </strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{cuenta}</strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{fecha_con}</strong></font></td>
  </tr>
</table>
<br>
<table width="30%" class="tabla">
  <tr>
    <th width="40%" class="vtabla" scope="col">Saldo Inicial </th>
    <th class="rtabla style1" scope="col"><font size="+1">{fsaldo_inicial}</font></th>
  </tr>
</table>

<br>
<form name="form" method="post" action="./ban_res_con.php?tabla={tabla}">
<!-- START BLOCK : cargos -->
<table width="30%" class="tabla">
  <!-- START BLOCK : cargo -->
  <tr>
	<!-- START BLOCK : cargo_title -->
	<th width="40%" rowspan="{span}" class="vtabla" scope="row">Cargos</th>
	<!-- END BLOCK : cargo_title -->
	<td class="rtabla"><input name="id{i}" type="hidden" value="{id}"><strong>{cargo}</strong></td>
	<td class="tabla"><input name="fecha_con{i}" type="text" class="insert" onChange="if (actualiza_fecha(this)) return; else this.select();" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.fecha_con{next}.select();
else if (event.keyCode == 38) form.fecha_con{back}.select();" value="{fecha}" size="10" maxlength="10"></td>
  </tr>
  <!-- END BLOCK : cargo -->
  <tr>
    <th class="vtabla" scope="row">Total</th>
    <th class="rtabla">{total_cargos}</th>
  </tr>
</table>
<!-- END BLOCK : cargos -->
<br>
<!-- START BLOCK : abonos -->
<table width="30%" class="tabla">
  <!-- START BLOCK : abono -->
  <tr>
	<!-- START BLOCK : abono_title -->
	<th width="40%" rowspan="{span}" class="vtabla" scope="row">Abonos</th>
	<!-- END BLOCK : abono_title -->
	<td class="rtabla"><input name="id{i}" type="hidden" value="{id}"><strong>{abono}</strong></td>
	<td class="tabla"><input name="fecha_con{i}" type="text" class="insert" id="fecha_con{i}" onChange="if (actualiza_fecha(this)) return; else this.select();" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.fecha_con{next}.select();
else if (event.keyCode == 38) form.fecha_con{back}.select();" value="{fecha}" size="10" maxlength="10"></td>
  </tr>
  <!-- END BLOCK : abono -->
  <tr>
    <th class="vtabla" scope="row">Total</th>
    <th class="rtabla">{total_abonos}</th>
  </tr>
</table>
<!-- END BLOCK : abonos -->
<input name="numfilas" type="hidden" value="{numfilas}">
<br>
<table width="30%" class="tabla">
  <tr>
    <th width="40%" class="vtabla" scope="col">Saldo Final </th>
    <th class="rtabla" scope="col"><input name="saldo_final" type="hidden" value="{saldo_final}"><font size="+1">{fsaldo_final}</font></th>
  </tr>
</table>
</form>
<p>
  <input name="" type="button" class="boton" onClick="self.close()" value="Cerrar ventana">
&nbsp;&nbsp;
<input type="button" class="boton" value="Conciliar" onClick="valida_registro()">
</p>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.fecha_con0.select();</script>
<!-- END BLOCK : resultados -->
