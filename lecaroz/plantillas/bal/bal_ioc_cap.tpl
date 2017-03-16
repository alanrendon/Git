<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Impuestos pagados por otras compa&ntilde;&iacute;as</p>
<form name="form" method="post" action="./bal_ioc_cap.php">
<input type="hidden" name="temp">
<table class="tabla">
  <tr>
    <th class="vtabla">Mes</th>
	<td class="vinsert">
		<select name="mes" class="insert" id="mes">
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
		</select>
	</td>
    <th class="vtabla">A&ntilde;o</th>
    <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" value="{anio}" size="4" maxlength="4"></td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Cia. que presto </th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Importe</th>
    <th class="tabla" scope="col">Cia. a la que prestaron </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla"><input name="num_cia_egreso[]" type="text" class="insert" id="num_cia_egreso" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia(this,nombre_cia_egreso[{i}])" onKeyDown="if (event.keyCode == 13) concepto[{i}].select()" size="5" maxlength="5">
    <input name="nombre_cia_egreso[]" type="text" disabled="true" class="vnombre" id="nombre_cia_egreso" size="30"></td>
    <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13) monto[{i}].select()" size="50" maxlength="50"></td>
    <td class="tabla"><input name="monto[]" type="text" class="insert" id="monto" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) num_cia_ingreso[{i}].select()" size="12" maxlength="12"></td>
    <td class="tabla"><input name="num_cia_ingreso[]" type="text" class="insert" id="num_cia_ingreso" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia(this,nombre_cia_ingreso[{i}])" onKeyDown="if (event.keyCode == 13) num_cia_egreso[{next}].select()" size="5" maxlength="5">
    <input name="nombre_cia_ingreso[]" type="text" disabled="true" class="vnombre" id="nombre_cia_ingreso" size="30"></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<p>
  <input name="" type="button" class="boton" value="Borrar">
&nbsp;&nbsp;  
<input type="button" class="boton" value="Capturar impuestos">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
	var cia = new Array();
	<!-- START BLOCK : cia -->
	cia[{num_cia}] = "{nombre}";
	<!-- END BLOCK : cia -->
	
	function cambiaCia(num, nombre) {
		if (num.value == "") {
			nombre.value = "";
		}
		else if (cia[num.value] != null) {
			nombre.value = cia[num.value];
		}
		else {
			alert("No existe la compañía en el catálogo");
			num.value = num.form.temp.value;
			num.focus();
		}
	}
	
	function validar() {
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
	}
	
	window.onload = form.num_cia_egreso[0].select();
</script>