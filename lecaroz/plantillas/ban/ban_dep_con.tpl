<!-- START BLOCK : tipo_listado -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		var anio = new Date();
		
		
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			document.form.num_cia.select();
			return false;
		}
		/*else if (document.form.anio.value <= 2000 || document.form.anio.value > anio.getFullYear()) {
			alert("Debe especificar el año de consulta");
			document.form.anio.select();
			return false;
		}*/
		else
			document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Listado de Depositos</p>
<form name="form" method="get" action="ban_dep_con.php">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    <th class="vtabla">Mes</th>
    <td class="vtabla"><select name="mes" class="insert">
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
    <th class="vtabla">A&ntilde;o</th>
    <td class="vtabla"><input name="anio" type="text" id="anio" class="insert" value="{anio}" size="4" maxlength="4" onFocus="temp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()"></td>
    <!--<th class="vtabla">A&ntilde;o</th>
    <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" size="4" maxlength="4"></td>-->
  </tr>
</table>
<p>
<input type="button" class="boton" value="Generar listado" onClick="valida_registro()">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : tipo_listado -->

<!-- START BLOCK : listado -->
<script language="javascript" type="text/javascript">
	function imprimir(boton) {
		boton.style.visibility = 'hidden';
		window.print();
		alert("Imprimiendo...");
		boton.style.visibility = 'visible';
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td class="encabezado" align="left">Cia.: {num_cia} </td>
    <td class="encabezado" align="center">{nombre_cia}</td>
    <td class="encabezado" align="right">Cia.: {num_cia}</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="encabezado" align="center">Depositos a la cuenta {cuenta} <br>
    del d&iacute;a 1 al {dia}  de {mes} del {anio} </td>
    <td>&nbsp;</td>
  </tr>
</table>

<br>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Fecha de Deposito </th>
    <th class="tabla" scope="col">Concepto</th>
    <th class="tabla" scope="col">Importe</th>
    <th colspan="2" class="tabla" scope="col">C&oacute;digo de Movimiento y Descripci&oacute;n</th>
    <th class="tabla" scope="col">Fecha de Conciliaci&oacute;n</th>
    <th class="tabla" scope="col">&nbsp;</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla">{fecha_mov}</td>
    <td class="vtabla">{concepto}</td>
    <td class="rtabla">{importe}</td>
    <td class="vtabla">{cod_mov}</td>
    <td class="vtabla">{nombre} </td>
    <td class="tabla">{fecha_con}</td>
    <td class="tabla"><input type="button" value="Modificar" class="boton" onClick="window.open('./ban_dep_mod.php?id={id}','dep_mod','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480')"></td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="2" class="rtabla">Total</th>
    <th class="rtabla"><font color="#000000" size="+1">{total}</font></th>
    <th colspan="4" class="vtabla">&nbsp;</th>
  </tr>
</table>
<p>
<input type="button" class="boton" value="Regresar" onClick="parent.history.back()">&nbsp;&nbsp;<input type="button" class="boton" value="Imprimir listado" onClick="imprimir(this)">
</p>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->
