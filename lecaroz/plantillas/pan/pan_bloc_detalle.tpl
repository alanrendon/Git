<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">DESGLOSE DE BLOC <br>
  {num_cia} - {nombre_cia}</p>
<table class="tabla">
   <tr class="">
     <th class="tabla">Folio</th>
     <th class="tabla">A cuenta</th>	 
     <th class="tabla">Total de la <br>factura</th>
     <th class="tabla">Resta por <br>pagar</th>	 
     <th class="tabla">Fecha de <br>entrega</th>
     <th class="tabla">Estado</th>

   </tr>
<!-- START BLOCK : rows -->
   <tr class="tabla">
      <td class="vtabla"><a href="javascript:detalle({num_cia},{num_folio})">{let_folio} &nbsp; {num_folio}</a> </td>
      <td class="rtabla">{abono}</td>
      <td class="rtabla">{total}</td>
      <td class="rtabla">{resta}</td>
      <td class="tabla">{fecha_entrega}</td>
      <td class="tabla">
	  <!-- START BLOCK : error -->
	  <img src="./menus/delete.gif">
  	  <!-- END BLOCK : error -->
	  
	  <!-- START BLOCK : ok -->
	  <img src="./menus/insert.gif">
  	  <!-- END BLOCK : ok -->
	  </td>


   </tr>
<!-- END BLOCK : rows -->   
  </table>

<p>
  <input type="button" value="Cerrar" class="boton" onClick="self.close()">
  <!-- START BLOCK : borrar -->
&nbsp;&nbsp;<input type="button" value="Borrar" class="boton" onClick="borrar({id})">
<!-- END BLOCK : borrar -->
</p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function detalle(num_cia, num_folio) {
	var url = "pan_rfa_con.php?fecha=&consulta=factura&num_fac=" + num_folio + "&cia=" + num_cia + "&bandera=1&close=1";
	var ven = window.open(url,"detalle_nota","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
	ven.focus();
}

function borrar(id) {
	window.open('./pan_bloc_minidel.php?id='+id+'&det=1','borrar','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=500');
	return;
}
//-->
</script>
