<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
function valida()
{
if(document.form.anio.value=="" || document.form.anio.value < 0){
	alert("Revise el año");
	document.form.anio.select();
}
else 
	document.form.submit();
}
</script>

<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Generaci&oacute;n del archivo de facturas del mes para contadores </p>
	<form action="./fac_file_cap.php" method="get" name="form">
	<input name="temp" type="hidden">
	<table border="1" class="tabla">
	  <tr class="tabla">
	    <th scope="row" colspan="2" class="tabla">Contador 
	      <select name="contador" id="contador" class="insert">
	        <option value=""></option>
	        <!-- START BLOCK : contador -->
			<option value="{id}">{nombre}</option>
			<!-- END BLOCK : contador -->
	        </select>
	      </th>
	    </tr>
	  <tr class="tabla">
		<th scope="row" colspan="2" class="tabla"> Mes 
		  <select name="mes" size="1" class="insert">
        <!-- START BLOCK : mes -->
	    <option value="{num_mes}" {checked}>{nom_mes}</option>
        <!-- END BLOCK : mes -->
		</select>
		  del 
		  <input name="anio" type="text" class="insert" id="cia3" value="{anio_actual}" size="5">		 </th>
	  </tr>
	  <tr class="tabla">
	    <th scope="row" colspan="2" class="tabla"><input name="tipo" type="radio" value="1" checked>
	      Pagadas
	        <input name="tipo" type="radio" value="2">
	        Pendientes
	        <input name="tipo" type="radio" value="3" />
	        Todas</th>
	    </tr>
	  <tr class="tabla">
	    <th scope="row" colspan="2" class="tabla"><input name="cia" type="radio" value="1" checked>
	      Panaderias
	        <input name="cia" type="radio" value="2">
	        Zapaterias</th>
	    </tr>
	</table>
	<p>
	<input type="button" name="enviar" class="boton" value="Generar Archivo" onclick='valida()'>
	</p>
	</form>
</td>
</tr>
</table>
