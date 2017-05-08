<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : fecha -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de Otros Dep&oacute;sitos </p>
<form name="form" method="get" action="./ban_dot_mod.php" onKeyPress="if (event.keyCode == 13) return false;">
<input name="temp" type="hidden">
  <table class="tabla">
   <tr>
     <th class="vtabla">Compa&ntilde;&iacute;a</th>
     <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) anio.select();" size="3" maxlength="3"></td>
   </tr>
   <tr>
     <th class="vtabla">Mes</th>
     <td class="vtabla"><select name="mes" class="insert" id="mes">
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
   </tr>
   <tr>
     <th class="vtabla">Anio</th>
     <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) num_cia.select();" value="{anio}" size="4" maxlength="4"></td>
   </tr>
   <!--<tr>
      <th class="vtabla" scope="row">Fecha de captura </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onKeyDown="if (event.keyCode == 13) form.enviar.focus();" value="{fecha}" size="10" maxlength="10"></td>
    </tr>-->
  </table>
  <p>
    <input name="enviar" type="button" class="boton" id="enviar" value="Siguiente" onClick="valida_registro(form)">
  </p>
</form>

</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.anio.value < 2000) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : fecha -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificar Otros Dep&oacute;sitos </p>
  <!-- START BLOCK : desglozado -->
  <table class="print">
    <tr>
      <th class="print" scope="col">Cia.</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Fecha</th>
      <th class="print" scope="col">Fecha<br>
        Captura</th>
      <th class="print" scope="col">Acreditado</th>
      <th class="print" scope="col">Nombre</th>
      <th class="print" scope="col">Concepto</th>
      <th class="print" scope="col">Dep&oacute;sito</th>
      <th class="print" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : grupo_des -->
	<!-- START BLOCK : fila_des -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="print" style="color:#0000CC;">{fecha}</td>
      <td class="print">{fecha_cap}</td>
      <td class="vprint">{acre}</td>
      <td class="vprint">{nombre}</td>
      <td class="vprint">{concepto}</td>
      <td class="rprint"><strong class="rtabla">{deposito}</strong></td>
      <td class="rprint"><input type="button" class="boton" value="Modificar" onClick="modificar({id})">
        <input type="button" class="boton" value="Borrar" onClick="borrar({id})"></td>
	</tr>
	<!-- END BLOCK : fila_des -->
	<!-- START BLOCK : total_des -->
    <tr>
      <th colspan="7" class="rprint">Total</th>
      <th class="rprint_total">{total}</th>
      <th class="rprint_total">&nbsp;</th>
    </tr>
	<!-- END BLOCK : total_des -->
	<tr>
      <td colspan="9">&nbsp;</td>
      </tr>
	<!-- END BLOCK : grupo_des -->
  </table>
  <!-- END BLOCK : desglozado -->  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location = './ban_dot_mod.php'">
</p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function modificar(id) {
		window.open("./ban_dot_minimod.php?id="+id,"mod","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=600");
	}
	
	function borrar(id) {
		window.open("./ban_dot_minidel.php?id="+id,"mod","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=300,height=200");
	}
</script>
<!-- END BLOCK : listado -->
<!-- START BLOCK : no_listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p><strong><font face="Geneva, Arial, Helvetica, sans-serif" color="#FF0000">No hay dep&oacute;sitos</font> </strong></p>
  <p><strong>
    <input name="Button" type="button" class="boton" value="Regresar" onClick="document.location='./ban_dot_mod.php'">
  </strong></p></td>
</tr>
</table>
<!-- END BLOCK : no_listado -->
