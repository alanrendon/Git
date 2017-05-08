<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida()
{
if(document.form.cia.value=="" || document.form.cia.value < 0){
	alert("Por favor revise la compañía");
	document.form.cia.select();
}
else if(document.form.proveedor.value=="" || document.form.proveedor.value < 0){
	alert("Por favor revise el proveedor");
	document.form.proveedor.select();
}
else if(document.form.anio.value=="" || document.form.anio.value < 0){
	alert("Revise el año");
	document.form.anio.select();
}
else 
	document.form.submit();
}
</script>

<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style1 {font-size: 12px}
-->
</style>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">CARTAS DE ALTA Y BAJAS DE EMPLEADOS </p>
	<form action="/plantillas/fac/./fac_carta_imss.php" method="get" name="form">
		<input name="temp" type="hidden">
	<table border="1" class="tabla">
	  <tr class="tabla">
		<th scope="col" class="tabla">Contador 
		  <select name="contador" size="1" class="insert" id="contador">
            <!-- START BLOCK : contadores -->
            <option value="{num_contador}">{nombre}</option>
            <!-- END BLOCK : contadores -->
          </select></th>
	  </tr>
	  <tr class="tabla">
		<td scope="row" class="tabla"> <p>
		  <label>
		  <input name="tipo_carta" type="radio" value="0" checked>
  Altas</label>
		
		  <label>
		  <input type="radio" name="tipo_carta" value="1">
  Bajas</label>
		  <br>
		  </p></td>
	  </tr>
	</table>
<br>
<table class="tabla" >
  <tr class="tabla">
    <th scope="col" class="tabla">Número de Trabajador </th>
    <th scope="col" class="tabla">Número de Compa&ntilde;&iacute;a </th>
  </tr>
 <!-- START BLOCK : rows -->
  <tr class="tabla">
    <td class="tabla"><input name="num_emp{i}" type="text" class="insert" value="{num_emp}" size="5" onKeyDown="if(event.keyCode==13) form.num_cia{i}.select();" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
    <td class="tabla">      <input name="num_cia{i}" type="text" class="insert" value="{num_cia}" size="5" onKeyDown="if(event.keyCode==13) form.num_emp{next}.select();" onFocus="form.temp.value=this.value;" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
  </tr>
  <!-- END BLOCK : rows -->
</table>
	<p>
	<input type="button" name="enviar" class="boton" value="Continuar" onclick='document.form.submit();'>
	</p>
	</form>
	<script language="JavaScript" type="text/JavaScript">window.onload=document.form.num_emp0.select();</script>

</td>
</tr>
</table>
	<script language="JavaScript" type="text/JavaScript">window.onload=form.cia.select()</script>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : carta -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr class="tabla"> 
<td align="center" valign="top">
<p></p>
<p class="print_encabezado">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p>
	<table width="90%" border="0" cellpadding="0" cellspacing="0">
	  <tr class="print" bordercolor=""> 
		<td width="50%" >&nbsp;</td>
		<td width="50%" align="right"><font size="2">{fecha}</font></td>
      </tr>
	  <tr class="print">
		<td width="50%" align="left">C.P. {contador}</td>
		<td width="50%">&nbsp;</td>
	  </tr>
	  <tr class="print">
	  	<td colspan="2">&nbsp;</td>
	  </tr>
	  <tr class="print">
		<td width="50%" align="left"><font size="2">P R E S E N T E</font>  </td>
		<td width="50%">  </td>
	  </tr>
	  <tr class="print">
	    <td align="left" colspan="2">&nbsp;</td>

	    </tr>
	  <tr class="print">
	    <td align="left" colspan="2"><font size="2">POR MEDIO DE LA PRESENTE ME PERMITO SALUDARLE Y A SU VEZ INDICARLE QUE SE DEN DE {estado} DEL I.M.S.S., LOS TRABAJADORES QUE A CONTINUACION SE MENCIONAN:</font></td>

	    </tr>
	</table>
	<p>
	<p>
<table width="90%" border="0" cellpadding="0" >
  <tr class="print">
    <td scope="col" width="50%" align="left"><font size="2">NOMBRE DEL EMPLEADO</font> </td>
    <td scope="col" width="50%" align="left"><font size="2">NOMBRE DE LA COMPA&Ntilde;&Iacute;A</font> </td>
  </tr>
<!-- START BLOCK : empleados -->
  <tr class="print">
    <td width="50%"><font size="1">{nombre_empleado}</font></td>
    <td width="50%"><font size="1">{nombre_cia}</font></td>
  </tr>
 <!-- END BLOCK : empleados -->
</table>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<table width="90%" border="0" cellpadding="0" >
  <tr class="print">
    <td scope="col" width="50%" align="center"><font size="2">FIRMA</font> </td>
    </tr>
</table><p>&nbsp;</p></td>
</tr>
</table>

<!-- END BLOCK : carta -->
