<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">

<!--START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
				
		if (num_cia.value > 0) {
			if (cia[num_cia.value] == null) {
				alert("Compañía "+num_cia.value+" Erronea");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
			}
			else {
				nombre.value   = cia[num_cia.value];
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
		}
	}
function valida_registro(){
	if(document.form.num_cia.value==""){
		alert("Debes especificar una compañía");
		return;
	}
	else if(document.form.anio.value==""){
		alert("Debes especificar un año para la consulta");
		return;
	}
	else
		document.form.submit();
}

</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title"><font size="+3">Modificaci&oacute;n de gastos de oficina</font></p>

<form name="form" action="./bal_gast_caja_mod.php" method="get">
<input name="temp" type="hidden">
<table border="1" class="tabla">
<tr class="tabla">
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
</tr>
<tr class="tabla">
      <td class="tabla" align="center"><input name="num_cia" type="text" class="insert" id="num_cia" size="5" maxlength="3" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select(); actualiza_compania(this,form.nombre)" onKeyDown="if(event.keyCode==13) document.form.mes.focus();">
        <input name="nombre" type="text" id="nombre" size="26" class="vnombre" readonly></td>
      <td class="tabla">	  
	  <select name="mes" size="1" class="insert" id="mes">
        <!-- START BLOCK : mes -->
        <option value="{mes}" {selected}>{nombre_mes}</option>
        <!-- END BLOCK : mes -->
      </select></td>
      <td class="tabla"><input name="anio" type="text" class="insert" id="anio" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if(event.keyCode==13) document.form.mes.focus();" value="{anio_actual}" size="5" maxlength="5"></td>
</tr>
</table>
<p>
</p>
<img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
<input type="button" name="enviar" class="boton" value="Consultar" onclick='valida_registro()'>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
</td>
</tr>
</table>
<!--END BLOCK : obtener_datos -->

<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">MODIFICACIÓN DE GASTOS DE CAJA PARA LA COMPAÑÍA <br>{nombre_cia} <br> CORRESPONDIENTES A {mes} DEL AÑO {anio}</p>

<form name="form" action="./actualiza_bal_gast_caja.php" method="post">
<input name="temp" type="hidden">
<input name="cont" type="hidden" id="cont" value="{contador}">  
<table class="tabla">
    <tr class="tabla">
      <th scope="col" class="tabla">Fecha</th>
      <th scope="col" class="tabla">Concepto</th>
      <th scope="col" class="tabla">Clave</th>
      <th scope="col" class="tabla">Tipo</th>
      <th scope="col" class="tabla">Importe</th>
      <th scope="col" class="tabla">Comentario</th>
      <th scope="col" class="tabla">Borrar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr class="tabla">
      <td class="tabla">{fecha}
	  <input name="id{i}" type="hidden" value="{id}">
	  </td>
      <td class="tabla">
	  
	  <select name="concepto{i}" id="concepto{i}" class="insert">
	  
	  <!-- START BLOCK : concepto -->
	  <option value="{codigo}" {selected}>{descripcion}</option>
	  <!-- END BLOCK : concepto -->
	  
      </select></td>
      <td class="tabla"><select name="clave{i}" id="clave{i}" class="insert">
        <option value="true" {selected1}>SI afecta a balance</option>
		<option value="false" {selected2}>NO afecta a balance</option>
      </select></td>
      <td class="tabla"><select name="tipo{i}" class="insert" id="tipo{i}">
        <option value="0" {selected3}>Egresos</option>
		<option value="1" {selected4}>Ingresos</option>

      </select></td>
      <td class="tabla">
	  <input name="importe{i}" type="text" class="insert" id="importe{i}" value="{importe}" size="10" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.value=form.temp.value;" onFocus="form.temp.value=this.value;" onKeyDown="if(event.keyCode==13) form.comentario{i}.select();"></td>
      <td class="tabla"><input name="comentario{i}" type="text" class="vinsert" id="comentario{i}" value="{comentario}" onKeyDown="if(event.keyCode==13) form.importe{next}.select();"></td>
      <td class="tabla"><input name="borrado{i}" type="checkbox" id="borrado{i}" value="checkbox" onChange="if(this.checked==false) form.borrar{i}.value=0; else if(this.checked==true) form.borrar{i}.value=1">
        <input name="borrar{i}" type="hidden" value="0" size="5"></td>
    </tr>
	<!-- END BLOCK : rows -->	
  </table>
  <p>
  <input name="regresar" type="button" value="Regresar" class="boton" onClick="parent.history.back();">&nbsp;&nbsp;
  <input name="enviar" type="button" value="Modificar" class="boton" onClick="document.form.submit();">
  </p>
</form>
</td>
</tr>
</table>

<!-- END BLOCK : modificar -->



