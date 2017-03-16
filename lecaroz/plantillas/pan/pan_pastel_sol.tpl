<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Solicitud de modificaci&oacute;n de facturas de pastel</p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.num_cia0.value<0 || document.form.num_cia0.value=="")
		alert("Compañía erronea");
	if (document.form.num_fact0.value<0 || document.form.num_fact0.value=="")
		alert("Revise el número de factura por favor");
	if (document.form.descripcion0.value=="")
		alert("Introduzca la razón de la modificación");
	else
	document.form.submit();
}
	function actualiza_compania(num_cia, nombre) 
	{
		// Arreglo con los nombres de las materias primas
		cia = new Array();				// Materias primas
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) 
		{
			if (cia[parseInt(num_cia.value)] == null) 
			{
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else 
			{
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") 
		{
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}



</script>


<form name="form" method="get" action="./pan_pastel_sol.php">
<input name="temp" type="hidden">
  <table class="tabla">
	 <tr class="tabla">
      <th scope="col" class="tabla" rowspan="2">Compa&ntilde;&iacute;a</th>
      <th scope="col" class="tabla" rowspan="2">Factura </th>
      <th class="tabla" rowspan="2">Descripción</th>
	 <th colspan="8" class="tabla">Selecciona datos a modificar </th>
	 </tr> 

    <tr class="tabla">
      <th class="tabla">Kilos</th>
      <th class="tabla">Precio<br>unidad </th>
      <th class="tabla">Pan</th>
      <th class="tabla">Base</th>
      <th class="tabla">P&eacute;rdida</th>
      <th class="tabla">Fecha <br> entrega</th>
	  <th class="tabla">Cancelar</th>
      <th class="tabla">Cambio<br>fecha</th>
    </tr>
	 
	<!-- START BLOCK : rows -->
    <tr class="tabla">
      <td class="vtabla"><input name="num_cia{i}" type="text" class="insert" id="num_cia{i}" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select(); actualiza_compania(this,form.nom_cia{i})" onKeyDown="if(event.keyCode==13) form.let_folio{i}.select();" size="5">
        <input name="nom_cia{i}" type="text" class="vnombre" id="nom_cia{i}" size="25" readonly>		</td>
      <td class="tabla">
        <input name="let_folio{i}" type="text" class="rinsert" id="let_folio{i}" onKeyDown="if(event.keyCode==13) form.num_fact{i}.select();" size="1" maxlength="1">
   
        <input name="num_fact{i}" type="text" class="vinsert" id="num_fact{i}" size="8" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if(event.keyCode==13) form.descripcion{i}.select();"></td>
		<td class="tabla"><input name="descripcion{i}" type="text" class="vinsert" id="descripcion{i}" onKeyDown="if(event.keyCode==13) form.num_cia{next}.focus();" size="20" maxlength="50"></td>

	    <td class="vtabla">
		
	      <label>
	      <input type="radio" name="kilos{i}" value="1" onChange="form.kilos1{i}.value=1;">
  Mas</label><br>

	      <label>
	      <input type="radio" name="kilos{i}" value="0" onChange="form.kilos1{i}.value=0;">
          
          Menos</label>
	
		  <input name="kilos1{i}" type="hidden" value="-1" size="4" >
		  </td>
	    <td class="tabla">
		<input name="precio_unidad{i}" type="checkbox" id="precio_unidad{i}" value="0" onChange="if(form.precio_unidad1{i}.value==0) form.precio_unidad1{i}.value=1; else if (form.precio_unidad1{i}.value==1) form.precio_unidad1{i}.value=0;">
		<input name="precio_unidad1{i}" type="hidden" value="0" size="3">
		</td>
	    <td class="tabla">
		<input name="otros{i}" type="checkbox" id="otros{i}" value="0" onChange="if(form.otros1{i}.value==0) form.otros1{i}.value=1; else if (form.otros1{i}.value==1) form.otros1{i}.value=0;">
		<input name="otros1{i}" type="hidden" value="0" size="4">
		</td>
	    <td class="tabla">
		<input name="base{i}" type="checkbox" id="base{i}" value="0" onChange="if(form.base1{i}.value==0) form.base1{i}.value=1; else if (form.base1{i}.value==1) form.base1{i}.value=0;">
		<input name="base1{i}" type="hidden" value="0" size="4">
		</td>
	    <td class="tabla"><input name="perdida{i}" type="checkbox" id="perdida{i}" value="checkbox" onChange="if(form.perdida1{i}.value==0) form.perdida1{i}.value=1; else if (form.perdida1{i}.value==1) form.perdida1{i}.value=0;">
	      <input name="perdida1{i}" type="hidden" id="perdida1{i}" value="0" size="4"></td>
	    <td class="tabla">		
		<input name="fecha_cambio{i}" type="checkbox" id="fecha_cambio{i}" value="checkbox" onChange="if(form.cambio_fecha1{i}.value==0) form.cambio_fecha1{i}.value=1; else if (form.cambio_fecha1{i}.value==1) form.cambio_fecha1{i}.value=0;">
	      <input name="cambio_fecha1{i}" type="hidden" id="cambio_fecha1{i}" value="0" size="4"></td>
	    <td class="tabla">
		<input name="cancelar{i}" type="checkbox" id="cancelar{i}" value="0" onChange="if(form.cancelar1{i}.value==0) form.cancelar1{i}.value=1; else if (form.cancelar1{i}.value==1) form.cancelar1{i}.value=0;">
		<input name="cancelar1{i}" type="hidden" value="0" size="4">
		
		</td>
	    <td class="tabla"><input name="fecha_nueva{i}" type="checkbox" id="fecha_nueva{i}" value="0" onChange="if(form.fecha_nueva1{i}.value==0) form.fecha_nueva1{i}.value=1; else if (form.fecha_nueva1{i}.value==1) form.fecha_nueva1{i}.value=0;">
	      <input name="fecha_nueva1{i}" type="hidden" id="fecha_nueva1{i}" value="0" size="4"></td>
	    <!-- END BLOCK : rows -->
    </tr>
  </table>

<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="Enviar">
<input name="borrar" type="button" class="boton" id="borrar" onClick="document.form.reset();" value="Borrar">
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia0.select();</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : pasteles -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Solicitud de modificaci&oacute;n de facturas de pastel<br>
<font color="#0000FF" size="-1">Recuerda que para modificación de datos como:<br> kilos, precio unidad, otros y base<BR>
al momento de modificar la nota, se genera Automáticamente el control verde,<br> 
por lo que es necesario NO capturar el control verde,
ya que te puede causar errores en tu efectivo,<BR> NO SE HARÁN MODIFICACIONES.</font>

</p>


<form name="form" method="post" action="./insert_pan_pastel.php">
  <table class="tabla">
    <tr class="tabla">
      <th scope="col" class="tabla">Compa&ntilde;&iacute;a</th>
      <th scope="col" class="tabla">N&uacute;mero de factura </th>
      <th class="tabla">Descripción</th>

      <th class="tabla">Estado</th>
      <th class="tabla">Kilos</th>
      <th class="tabla">Precio unidad </th>
      <th class="tabla">Pan</th>
      <th class="tabla">Base</th>
      <th class="tabla">P&eacute;rdida</th>
      <th class="tabla">Fecha entrega </th>
      <th class="tabla">Cancelar</th>
      <th class="tabla">Cambio<br>fecha</th>
    </tr>
	<!-- START BLOCK : renglones -->
    <tr class="tabla">
      <td class="vtabla"><input name="num_cia{i}" type="hidden" class="insert" id="num_cia{i}" value="{num_cia}" size="5"> 
        {num_cia}&#8212;{nom_cia} </td>
      <td class="tabla">{let_folio1}&nbsp;{num_fact}
        <input name="let_folio{i}" type="hidden" class="rinsert" id="let_folio{i}" value="{let_folio}" size="4" maxlength="1">
   
        <input name="num_fact{i}" type="hidden" class="vinsert" id="num_fact{i}" value="{num_fact}" size="8" ></td>
		<td class="tabla">{descripcion}
		  <input name="descripcion{i}" type="hidden" class="vinsert" id="descripcion{i}" value="{descripcion}" size="50" maxlength="50"></td>
		
	    <td class="tabla">
		<!-- START BLOCK : edo_ok -->
		{edo}
		<!-- END BLOCK : edo_ok -->
		<!-- START BLOCK : edo_error -->
		<font color="#FF0000">{edo}</font>
		<!-- END BLOCK : edo_error -->
		</td>
	    <td class="tabla">{kilos}
	      <input name="kilos1{i}" type="hidden" id="kilos1{i}" value="{kilos1}" size="4" ></td>
	    <td class="tabla">{precio_unidad}
	      <input name="precio_unidad1{i}" type="hidden" id="precio_unidad1{i}" value="{precio_unidad1}" size="3"></td>
	    <td class="tabla">{otros}
	      <input name="otros1{i}" type="hidden" id="otros1{i}" value="{otros1}" size="4"></td>
	    <td class="tabla">{base}
	      <input name="base1{i}" type="hidden" id="base1{i}" value="{base1}" size="4"></td>
	    <td class="tabla">{perdida}
	      <input name="perdida1{i}" type="hidden" id="perdida1{i}" value="{perdida1}" size="4"></td>
	    <td class="tabla">{cambio_fecha}
	      <input name="cambio_fecha1{i}" type="hidden" id="cambio_fecha1{i}" value="{cambio_fecha1}" size="4"></td>
	    <td class="tabla">{cancelar}
		  <input name="cancelar1{i}" type="hidden" id="cancelar1{i}" value="{cancelar1}" size="4"></td>

	    <td class="tabla">{fecha_nueva}
		<input name="fecha_nueva1{i}" type="hidden" id="fecha_nueva1{i}" value="{fecha_nueva1}" size="4"></td>
	    <!-- END BLOCK : renglones -->
    </tr>
  </table>
<p>
  <input name="regresar" type="button" class="boton" id="regresar" onClick="parent.history.back();" value="Regresar">  &nbsp;
<!-- START BLOCK : enviar -->
<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="Enviar">
<!-- END BLOCK : enviar -->
</p>
<input name="contador" type="hidden" class="insert" id="contador" value="{cont}" size="5">
</form>

</td>
</tr>
</table>
<!-- END BLOCK : pasteles -->