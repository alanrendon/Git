<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consulta de facturas de pastel pendientes </p>
<form name="form" method="get" action="./pan_fpan_con.php">
  <input name="tmp" type="hidden" id="tmp">
  <table class="tabla">
    <tr class="tabla">
      <th class="tabla">
        <label>
      Compa&ntilde;&iacute;a</label>
        <input name="operadora" type="hidden" class="insert" id="operadora" value="{opera}">
      </th>
    </tr>
    <!-- START BLOCK : lista -->
	<tr class="tabla">
      <td class="tabla" colspan="2">
		<input name="cias" type="text" class="insert" id="cias" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia()" onKeyDown="if (event.keyCode == 13) this.blur()" size="3">
        <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" size="30">		
        <!--<select name="cias" size="1" class="insert">
		
		  <option value="{num_cia}" class="insert">{num_cia}&#8212;{nom_cia}</option>
		
		</select>-->	  </td>
	</tr>
	<!-- END BLOCK : lista -->
	  <!-- START BLOCK : mensaje -->
	  <tr class="tabla">
		  <td class="tabla">
		  <font size="+2" color="#0066FF">{mensaje}</font>
		  </td>
	  </tr>
	  <!-- END BLOCK : mensaje -->	  
  </table>
  <br>
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="consultar" {disabled}>
</p>
</form>
<script language="javascript" type="text/javascript">
var f = document.form, cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : cia -->

function cambiaCia() {
	if (f.cias.value == '' || f.cias.value == '0') {
		f.cias.value = '';
		f.nombre.value = '';
	}
	else if (cia[get_val(f.cias)] != null)
		f.nombre.value = cia[get_val(f.cias)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.cias.value = f.tmp.value;
		f.cias.select();
	}
}

window.onload = f.cias.select();

</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : facturas -->
<script language="JavaScript" type="text/JavaScript">
	function detalle_factura(letra,inicio,fini,cia,numf,id){
		window.open('./pan_bloc_detalle.php?letra='+letra+'&inicio='+inicio+'&final='+fini+'&cia='+cia+'&folios='+numf+'&id='+id,'borrar','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=500,height=800,left=300, top=100');
		return;
	}

	function borrar_bloc(id) {
		window.open('./pan_bloc_minidel.php?id='+id,'borrar','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=500');
		return;
	}


</script>
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

<p>
<input name="regresar" type="button" value="Regresar" onClick="parent.history.back();" class="boton">
&nbsp;&nbsp;
<input type="button" value="Imprimir" class="boton" onClick="window.print()">
</p>

</td>
</tr>
</table>
<!-- END BLOCK : facturas -->