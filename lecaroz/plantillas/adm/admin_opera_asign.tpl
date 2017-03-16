<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">ASIGNACI&Oacute;N DE COMPA&Ntilde;&Iacute;AS POR OPERADORA </p>

<form name="form" method="get" action="./admin_opera_asign.php">
  <table class="tabla">
    <tr class="tabla">
      <th class="tabla" colspan="2">
        <label>Tipo de Asignaci&oacute;n </label></th>
    </tr>
    <tr class="tabla">
      <td class="vtabla" colspan="2">
        <p>
          <label>
          <input name="status" type="radio" value="0" checked>
  General</label>
  		</td>
	</tr>
	<tr class="tabla">
	<td class="vtabla">
          <label>
          <input type="radio" name="status" value="1">
  Por Operadora </label>
		  <br>
          <label>
          <input type="radio" name="status" value="2">
  Por Traspaso </label>

        </p></td>
		<td class="tabla">
		<select name="operadora" class="insert">
	<!-- START BLOCK : nombre_opera0 -->
		  <option value="{idoperadora}" {selected}>{nombre_opera}</option>
	<!-- END BLOCK : nombre_opera0 -->
		</select>
		
		</td>
    </tr>
  </table>
  <br>
  <input name="temp" type="hidden" value="{temp}">
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="consultar">
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.operadora.focus();</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : por_cia -->
<script language="JavaScript" type="text/JavaScript">
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">ASIGNACIÓN GENERAL DE PANADERIAS</p>
<form action="./insert_admin_opera.php" method="post" name="form">
<input name="status" type="hidden" value="{status}">
<input name="contador" type="hidden" id="contador" value="{contador}">
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla" colspan="2">COMPAÑÍA</th>
    <th scope="col" class="tabla">OPERADORA</th>
  </tr>
 <!-- START BLOCK : rows1 -->
  <tr class="tabla">
    <th class="tabla">{num_cia}<input name="num_cia{i}" type="hidden" value="{num_cia}"></th>
    <td class="vtabla">{nombre_cia}</td>
    <td class="tabla">
	<select name="operadora{i}" class="insert">
<!-- START BLOCK : nombre_opera1 -->
	  <option value="{idoperadora}" {selected}>{nombre_opera}</option>
<!-- END BLOCK : nombre_opera1 -->
	</select>
	</td>
  </tr>
 <!-- END BLOCK : rows1 -->
</table>
<p><input name="regresar" type="button" class="boton" value="Regresar" onClick="document.location='./admin_opera_asign.php'">
&nbsp;&nbsp;
<input type="button" name="enviar" value="Enviar" onClick="document.form.submit();" class="boton">
</p>

</form>


</td>
</tr>
</table>
<!-- END BLOCK : por_cia -->


<!-- START BLOCK : por_operadora -->

<script type="text/javascript" language="JavaScript">
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
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Asignación de compañías para <br>
   {operadora}</p>

<form method="post" name="form" action="./insert_admin_opera.php">
<input name="status" type="hidden" value="{status}">
<input name="operadora" type="hidden" id="operadora" value="{idoperadora}">
<input name="contador" type="hidden" id="contador" value="20">
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla" colspan="2">Compa&ntilde;&iacute;a</th>
  </tr>
  <!-- START BLOCK : rows2 -->
  <tr class="tabla">
    <td class="tabla"><input name="num_cia{i}" type="text" id="num_cia{i}" value="{num_cia}" size="4" maxlength="3" class="insert" onChange="actualiza_compania(this,form.nombre_cia{i});" onKeyDown="if(event.keyCode==13) document.form.num_cia{next}.select();"></td>
    <td class="tabla"><input name="nombre_cia{i}" type="text" value="{nombre}" size="50" class="vnombre" readonly></td>
  </tr>
  <!-- END BLOCK : rows2 -->
</table>
<p><input name="regresar2" type="button" class="boton" value="Regresar" onClick="document.location='./admin_opera_asign.php'">&nbsp;&nbsp;
  <input type="button" name="enviar2" value="Enviar" onClick="document.form.submit();" class="boton">
</p>
</form>

<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia0.select();
</script>

</td>
 </tr>
</table>
<!-- END BLOCK : por_operadora -->

<!-- START BLOCK : por_traspaso -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Traspaso  de panaderías a diferentes operadoras <br>Compañías a cargo de {operadora}</p>

<form action="./insert_admin_opera.php" method="post" name="form">
<input name="status" type="hidden" value="{status}">
<input name="contador" type="hidden" id="contador" value="{contador}">
<table class="tabla">
  <tr class="tabla">
    <th scope="col" class="tabla" colspan="2">COMPAÑÍA</th>
    <th scope="col" class="tabla">TRASPASAR A:</th>
  </tr>
  <!-- START BLOCK : rows3 -->
  <tr class="tabla">
    <th class="tabla"><input name="num_cia{i}" type="hidden" value="{num_cia}">{num_cia}</th>
    <td class="vtabla">{nombre_cia}</td>
	<td class="tabla"><select name="operadora{i}" class="insert" id="operadora{i}">
      <!-- START BLOCK : nombre_opera2 -->
      <option value="{idoperadora}" {selected}>{nombre_opera}</option>
      <!-- END BLOCK : nombre_opera2 -->
    </select>
	</td>
  </tr>
  <!-- END BLOCK : rows3 -->
</table>
<p><input name="regresar3" type="button" class="boton" value="Regresar" onClick="document.location='./admin_opera_asign.php'">&nbsp;&nbsp;
  <input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="Enviar">
</p>
</form>
</td>
 </tr>
</table>
<!-- END BLOCK : por_traspaso -->



