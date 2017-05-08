<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Consulta de Blocs de pastel</p>
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if(document.form.tipo_list.value==1)
		document.form.submit();
	
	else{
		if (document.form.cia.value < 0 || document.form.cia.value=="")
			alert("Compañía erronea");
		else
		document.form.submit();
	}
}

function activa(valor){
//	var this.valor=0
	this.valor = parseInt(valor);
	if(this.valor==0){
		document.form.cia.disabled=false;
		document.form.tipo_list.value=0;
		document.form.desglosado.disabled=true;
		document.form.cia.select();
	}
	
	else if(this.valor==1){
		document.form.cia.disabled=true;
		document.form.desglosado.disabled=false;
		document.form.tipo_list.value=1;
	}
}

</script>

<form name="form" method="get" action="./pan_bloc_con.php">
  <table class="tabla">
    <tr class="tabla">
      <th class="vtabla">
        <label>
      Compa&ntilde;&iacute;a</label>
        <input name="cia" type="text" class="insert" id="cia" onKeyDown="if (event.keyCode == 13) document.form.cia.select();" size="10">
      </th>
    </tr>
    <tr class="tabla">
      <td class="vtabla" colspan="2">
        <label> </label>
        <p>
          <label>
          <input type="radio" name="status" value="0" onChange="form.stat.value=0">
  Terminado</label>
          <br>
          <label>
          <input type="radio" name="status" value="1" onChange="form.stat.value=1">
  En proceso</label>
          <br>
          <label>
          <input type="radio" name="status" value="2" onChange="form.stat.value=2">
  Sin usar</label>
          <br>
          <label>
          <input type="radio" name="status" value="3" checked onChange="form.stat.value=3">
  Todos</label>
          <input name="stat" type="hidden" class="insert" id="stat" onKeyDown="if (event.keyCode == 13) document.form.cia.select();" value="3" size="10">
          <br>
        </p></td>
    </tr>
    <tr class="tabla">
      <td class="vtabla" colspan="2">
	  <input name="listados" type="checkbox" id="listados" value="checkbox" onChange="if(this.checked==false) activa(0); else if (this.checked==true) activa(1);">
        Listados de estados de blocs 
	    <input name="tipo_list" type="hidden" value="0" size="5"><br>
	  
	  <input name="desglosado" type="checkbox" value="checkbox" disabled onChange="if(this.checked==false) form.tipo_list2.value=0; else if (this.checked==true) form.tipo_list2.value=1;"> 
	  Desglosado por blocs
      <input name="tipo_list2" type="hidden" value="0" size="5">
</td>
    </tr>
  </table>
  <br>
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="valida();" value="consultar">
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.cia.select();</script>
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->


<!-- START BLOCK : bloc -->
<script language="JavaScript" type="text/JavaScript">
	function detalle_factura(letra,inicio,fini,cia,numf,id){
		window.open('./pan_bloc_detalle1.php?letra='+letra+'&inicio='+inicio+'&final='+fini+'&cia='+cia+'&folios='+numf+'&id='+id,'detalle','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=500,height=800,left=300, top=100');
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

<p class="title">Blocs enviados a la  compa&ntilde;&iacute;a <br>
   {num_cia}&#8212;{nom_cia}</p>
<p class="title">Responsable: {operadora}</p>
<table border="1" class="tabla">
	  <tr class="tabla">
		<th class="tabla" >Folio Inicial</th>
		<th class="tabla" >Folio Final</th>
		<th class="tabla" >N&uacute;mero de folios </th>
		<th class="tabla" >Fecha de envio </th>
		<th class="tabla" >Status</th>
		<th class="tabla" ></th>
	    <th class="tabla" ></th>
	  </tr>
	
	<!-- START BLOCK : rows -->
	  <tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
		<td class="tabla">{let_folio}&#8212;{folio_inicial}</td>
		<td class="tabla">{let_folio}&#8212;{folio_final}</td>	
		<td class="tabla">{num_folios}</td>
		<td class="tabla">{fecha}</td>
		<td class="tabla">{status}</td>
		<td class="tabla"><input type="button" class="boton" name="detalle" value="Detalle" onClick="detalle_factura('{let_folio1}',{folio_inicial},{folio_final},{num_cia},{num_folios},{idbloc});">
		<!-- START BLOCK : borrado -->
		</td>
	    <td class="tabla"><input type="button" class="boton" name="boton" value="Borrar" onClick="if({id_user}==27 || {id_user}==1) borrar_bloc({id}); else alert('No tienes derechos de borrar el bloc');"></td>
        <!-- END BLOCK : borrado -->
	  </tr>
	  <!-- END BLOCK : rows -->
	</table>
<p>
<input name="regresar" type="button" value="Regresar" onClick="parent.history.back();" class="boton">
</p>

</td>
</tr>
</table>
<!-- END BLOCK : bloc -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="print_encabezado">Relación de Blocs por Panadería al {dia} de {mes} del {anio}</p>
<!-- START BLOCK : cias -->
<table class="print">
  <tr class="print">
    <td colspan="5" class="print"><strong>{num_cia} &nbsp; {nombre_cia}</strong></td>
  </tr>
  <tr class="print">
    <th scope="col" class="print">Folio bloc</th>
    <th scope="col" class="print">Fecha de envío</th>
	<th scope="col" class="print">Sin usar</th>
    <th scope="col" class="print">En proceso</th>
    <th scope="col" class="print">Terminados</th>
  </tr>
  <!-- START BLOCK : rows1 -->
  <tr>
    <td class="vprint">{let_folio}&nbsp;{num_remi}</td>
    <td class="print">{fecha}</td>	
    <td class="print">{sin_usar}</td>
    <td class="print">{proceso}</td>
    <td class="print">{terminados}</td>
  </tr>
  <!-- END BLOCK : rows1 -->
  <tr class="print">
    <th class="print">Totales</th>
    <th class="print">{total_blocs}</th>	
    <th class="print">{total_sin_usar}</th>
    <th class="print">{total_proceso}</th>
    <th class="print">{total_terminados}</th>
  </tr>
</table>
 <!-- END BLOCK : cias -->
</td>
</tr>
</table>

<!-- END BLOCK : listado -->


<!-- START BLOCK : total -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">

<p class="print_encabezado">Relación de Blocs por Panadería al {dia} de {mes} del {anio}</p>
<table class="print">
  <tr class="print">
    
  </tr>
  <tr class="print">
	<th scope="col" class="print">Compañía</th>
    <th scope="col" class="print">Blocs enviados </th>
	<th scope="col" class="print">Sin usar</th>
    <th scope="col" class="print">En proceso</th>
    <th scope="col" class="print">Terminados</th>
  </tr>
<!-- START BLOCK : rows3 -->
  <tr class="print">
	<td class="vprint"><strong>{num_cia} &nbsp; {nombre_cia}</strong></td>
    <td class="print"><strong>{total_blocs}</strong></td>
    <td class="print">{total_sin_usar}</td>
    <td class="print">{total_proceso}</td>
    <td class="print">{total_terminados}</td>
  </tr>
 <!-- END BLOCK : rows3 -->
</table>

</td>
</tr>
</table>

<!-- END BLOCK : total -->



