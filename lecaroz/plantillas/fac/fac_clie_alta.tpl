<!-- tabla puestos menu facturas y proveedores -->

<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.id.value <= 0) {
			alert('Debe especificar un numero de código');
			document.form.id.select();
		}

		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.form.submit();
			else
				document.form.id.select();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.campo0.focus();
	}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

	<p class="title">Alta de Clientes </p>
	<form name="form" method="get" action="./fac_clie_alta.php?tabla={tabla}">
	  <table class="tabla">
		<tr>
		  <th class="vtabla">C&oacute;digo</th>
		  <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="id" type="text" class="insert" id="id" value="{id}" size="5" maxlength="5" readonly></td>
		</tr>
		<tr>
		  <th class="vtabla">Nombre</th>
		  <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="nombre" type="text" class="vinsert" id="nombre" size="70" maxlength="99"></td>
		</tr>
		<tr>
		  <th class="vtabla">Direcci&oacute;n</th>
		  <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="direccion" type="text" class="vinsert" id="direccion" size="100" maxlength="200"></td>
		</tr>
		<tr>
		  <th class="vtabla">R.F.C.</th>
		  <td class="vtabla" onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');"><input name="rfc" type="text" class="vinsert" id="rfc" size="15" maxlength="15"></td>
		  </tr>
	  </table>
	  <p>
		<img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Alta de Clientes" onclick='valida_registro()'>
		<br><br>
		<img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Borrar formulario" onclick='borrar()'>
	  </p>
	</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.campo1.select();
</script>

	
</td>
</tr>
</table>