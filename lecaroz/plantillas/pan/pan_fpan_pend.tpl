<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : facturas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="title">Facturas pendientes de pago <br>
   {num_cia}&#8212;{nom_cia}</p>
<p class="title">Responsable: {operadora}</p>

<table class="tabla">
  <tr class="tabla">
    <th class="tabla" colspan="2">Folio</th>
    <th class="tabla">Total factura </th>
    <th class="tabla">Pendiente</th>
    <th class="tabla">Fecha de pago </th>
  </tr>
<!-- START BLOCK : rows -->
  <tr class="tabla">
    <td class="tabla">{let_folio}</td>
	<td class="tabla">{num_fact}</td>
    <td class="tabla">{total}</td>
    <td class="tabla">{resta}</td>
    <td class="tabla">{fecha_entrega}</td>
  </tr>
<!-- END BLOCK : rows -->  
</table>

<!-- START BLOCK : sin_notas -->
<p><font size="+1" color="#0000FF">NO TIENES NOTAS PENDIENTES PARA ESTA COMPAÑÍA</font></p>
<!-- END BLOCK : sin_notas -->

<!-- START BLOCK : con_notas -->
<p class="tabla" ><font color="#990033" size="+1">Tienes que meter estas notas pendientes <br > de lo contrario no podrás capturar notas de pastel</font></p>
<!-- END BLOCK : con_notas -->
<p>
<input name="regresar" type="button" value="Cerrar" class="boton" onclick="self.close();">
</p>

</td>
</tr>
</table>
<!-- END BLOCK : facturas -->