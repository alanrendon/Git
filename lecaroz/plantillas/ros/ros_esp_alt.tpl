<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">


<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Alta de notas especiales</p>
<form action="./ros_esp_alt.php" method="get" name="form">
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla">Ingrese la compañía que repartirá materia prima</th>
  </tr>
  <tr class="tabla">
    <td class="tabla">
	<select name="num_cia" class="insert">
	<!-- START BLOCK : compania -->
	  <option value="{num_cia}">{num_cia}&#8212;{nombre_cia}</option>
	<!-- END BLOCK : compania -->
    </select></td>
  </tr>
</table>
<br>

<input name="enviar" type="button" class="boton" id="enviar" value="Siguiente" onClick="document.form.submit();">

</form>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : asignacion -->
<script type="text/javascript" language="JavaScript">
	function actualiza_compania(num_cia, nombre) {
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia2 -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia2 -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.select();
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

</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">La compañía {num_cia} {nombre_cia}<br>repartir&aacute; materia prima a las siguientes compañías</p>
<form action="./ros_esp_alt.php" name="form" method="post">
<input name="cia" type="hidden" value="{num_cia}">
<table class="tabla">
  <tr>
    <th scope="col" class="tabla">N&uacute;mero</th>
    <th scope="col" class="tabla">Nombre</th>
  </tr>
  <!-- START BLOCK : cias -->
  <tr>
    <td class="tabla"><input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" size="5" onKeyDown="if(event.keyCode==13) form.num_cia{next}.select();" onChange="actualiza_compania(this,form.nombre{i})"></td>
    <td class="tabla"><input name="nombre{i}" type="text" id="nombre{i}" size="50" class="vnombre" readonly></td>
  </tr>
  <!-- END BLOCK : cias -->
</table>
&nbsp;
<p>
  <input name="regresar" type="button" class="boton" id="regresar" onClick="parent.history.back();" value="Regresar">
  &nbsp;&nbsp;
  <input type="button" name="enviar" value="Enviar" onClick="document.form.submit();" class="boton">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia0.select();
</script>


</td>
</tr>
</table>

<!-- END BLOCK : asignacion -->