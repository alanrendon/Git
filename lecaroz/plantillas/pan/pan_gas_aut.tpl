<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() 
	{
		if (document.form.codgastos.value==""){
			alert("Inserte un código de gasto por favor");
			return;
		}
		else
			document.form.submit();
	}

	function actualiza_gas(codgastos, nombre) {
		// Arreglo con los nombres de las materias primas
		gas = new Array();				// Materias primas
		<!-- START BLOCK : nombre_gas -->
		gas[{codgastos1}] = '{nombre_gas}';
		<!-- END BLOCK : nombre_gas -->
				
		if (codgastos.value > 0) {
			if (gas[codgastos.value] == null) {
				alert("Código de gasto "+codgastos.value+" erroneo");
				codgastos.value = "";
				nombre.value  = "";
				codgastos.select();
			}
			else {
				nombre.value   = gas[codgastos.value];
			}
		}
		else if (codgastos.value == "") {
			codgastos.value = "";
			nombre.value  = "";
		}
	}
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
	<p class="title">CAPTURA DE LIMITE DE GASTOS </p>
	<form name="form" action="./pan_gas_aut.php" method="get">
		<input name="temp" type="hidden">
	  <table class="tabla">
		<tr>
		  <th scope="row" class="tabla" colspan="2">Gasto</th>
		</tr>
		<tr class="tabla">
		  <td class="tabla">C&oacute;digo
			<input name="codgastos" type="text" class="insert" id="codgastos" size="5" maxlength="3" onChange="actualiza_gas(this,form.nombre)"></td>
		  <td class="tabla"><input name="nombre" type="text" class="vnombre" id="nombre" size="50"></td>
		</tr>
	  </table>
	  
	  <p>
	  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
	  &nbsp;&nbsp;</p>
	</form>
	<script language="JavaScript" type="text/JavaScript">
	window.onload=document.form.codgastos.select();
	</script>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
	<p class="title">CAPTURA DE LIMITE DE GASTOS:</p>
	<form name="formi" action="./insert_pan_gas_aut.php" method="post">
	<input name="temp" type="hidden">
    <input name="contador" type="hidden" id="contador" value="{contador}">    
    <input name="codgastos" type="hidden" id="codgastos" value="{codgastos1}">
    <table class="tabla">
	  <tr>
		<th scope="col" colspan="2" class="tabla">Compa&ntilde;&iacute;a</th>
		<th scope="col" class="tabla">{nombre_gastos}</th>
	  </tr>
	  <!-- START BLOCK : rows -->
	  <tr class="tabla">
		<th class="tabla">{num_cia}<input name="num_cia{i}" type="hidden" id="num_cia{i}" value="{num_cia}"></th>
		<th class="vtabla">{nombre_cia}</th>
		<td class="tabla"><input name="limite{i}" type="text" class="rinsert" id="limite{i}" value="{limite}" size="10" onKeyDown="if(event.keyCode==13) form.limite{next}.select()" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"></td>
	  </tr>
	  <!-- END BLOCK : rows -->
	</table>
	<p>
  <input type="button" name="regresar" value="Regresar" class="boton" onClick="document.location='./pan_gas_aut.php'">&nbsp;&nbsp;
  <input name="enviar" type="button" class="boton" id="enviar" onClick="document.formi.submit();" value="Capturar" {disabled}>
	</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.formi.importe0.select();
</script>
</td>
</tr>
</table>

<!-- END BLOCK : captura -->


