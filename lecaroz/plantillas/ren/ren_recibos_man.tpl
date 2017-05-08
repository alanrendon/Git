<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">

function actualiza_arrendador(num_arr, nombre) {
	arrendador = new Array();
	<!-- START BLOCK : nombre_arrendador -->
	arrendador[{num_arr}] = '{nombre_arrendador}';
	<!-- END BLOCK : nombre_arrendador -->
			
	if (num_arr.value > 0) {
		if (arrendador[num_arr.value] == null) {
			alert("Arrendatario "+num_arr.value+" erroneo");
			num_arr.value = "";
			nombre.value  = "";
			num_arr.select();
		}
		else {
			nombre.value = arrendador[num_arr.value];
		}
	}
	else if (num_arr.value == "") {
		num_arr.value = "";
		nombre.value  = "";
	}
}


function valida_registro(){
	if(document.form.anio.value=="" || document.form.anio.value < 0){
		alert("Revise el año de consulta");
		document.form.anio.select();
	}
	else
		document.form.submit();
}

</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de recibos de renta</p>
  <form name="form" action="./ren_recibos_man.php" method="get">
  <input name="temp" type="hidden">
  
  <table class="tabla">
  <tr class="tabla">
    <th class="tabla">Mes</th>
    <th class="tabla">A&ntilde;o</th>
  </tr>
  <tr class="tabla">
    <td class="tabla">
	<select name="mes" class="insert" id="mes">
	<!-- START BLOCK : mes -->
      <option value="{mes}" {selected}>{nombre_mes}</option>
	<!-- END BLOCK : mes -->
    </select></td>
    <td class="tabla"><input name="anio" type="text" class="insert" id="anio" value="{anio_actual}" size="5" onFocus="form.temp.value=this.value" onKeyDown="if(event.keyCode==13) form.arrendatario.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr class="tabla">
    <th class="tabla">ARRENDATARIO</th>
    <th class="tabla">FOLIO</th>
  </tr>
  <tr class="tabla">
    <td class="vtabla">
      <input name="arrendatario" type="text" class="insert" id="arrendatario" size="4" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.value=''; else actualiza_arrendador(this,form.nombre_arrendador);" onKeyDown="if(event.keyCode==13) form.folio.select();">
      <input name="nombre_arrendador" type="text" class="vnombre" id="nombre_arrendador" size="75" readonly>
    </td>
    <td class="vtabla"><input name="folio" type="text" class="insert" id="folio" size="10" onKeyDown="if(event.keyCode==13) form.anio.select();" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="tabla">&nbsp;&nbsp;TIPO DE RECIBO A GENERAR&nbsp;&nbsp; </th>
    <th class="tabla">&nbsp;&nbsp;DATOS INCLUIDOS EN EL RECIBO &nbsp;&nbsp;</th>
  </tr>
  <tr>
    <td class="vtabla"><p>
      <label>
      <input name="tipo_recibo" type="radio" value="0" checked onChange="document.form.meses.style.visibility='hidden';document.form.tr.disabled=true; document.form.tipo_recibo1.value=0;">
  Mensual</label>
      <br>
      <label>
      <input type="radio" name="tipo_recibo" value="1" onChange="document.form.meses.style.visibility='hidden'; document.form.tr.disabled=false;document.form.tipo_recibo1.value=1;">
  Trimestral</label>
      <br>
      <label>
      <input type="radio" name="tipo_recibo" value="2" onChange="document.form.meses.style.visibility='hidden';document.form.tr.disabled=false; document.form.tipo_recibo1.value=2;">
  Semestral</label>
      <br>
      <label>
      <input type="radio" name="tipo_recibo" value="3" onChange="document.form.meses.style.visibility='visible'; document.form.meses.select();document.form.tr.disabled=false; document.form.tipo_recibo1.value=3;">
  Meses a pagar</label>
      <input name="meses" type="text" size="3" class="insert" style="visibility:hidden" onFocus="form.temp.value=this.value">
	  <br>
	  <label><input type="checkbox" name="tr" value="checkbox" disabled onChange="if(this.checked==false) form.recibos.value=0; else form.recibos.value=1;"> Generar un solo recibo</label><input name="recibos" type="hidden" id="recibos" value="0" size="3">
      <input name="tipo_recibo1" type="hidden" id="tipo_recibo1" value="0" size="3">
    </p></td>
    <td class="vtabla">
	<label><input name="ren" type="checkbox" id="ren" value="checkbox" checked onChange="if(this.checked==false) form.renta.value=0; else form.renta.value=1">
	Renta</label>   <input name="renta" type="hidden" id="renta" value="1" size="3">	
	<br>
	<label><input name="ag" type="checkbox" id="ag" value="checkbox" checked onChange="if(this.checked==false) form.agua.value=0; else form.agua.value=1">
	Agua</label>   <input name="agua" type="hidden" id="agua" value="1" size="3">	
	<br>
	<label><input name="ma" type="checkbox" id="ma" value="checkbox" checked onChange="if(this.checked==false) form.mantenimiento.value=0; else form.mantenimiento.value=1">
	Mantenimiento</label> <input name="mantenimiento" type="hidden" id="mantenimiento" value="1" size="3">	
	<br>
	<label><input name="iv" type="checkbox" id="iv" value="checkbox" checked onChange="if(this.checked==false) form.iva.value=0; else form.iva.value=1">
	I.V.A.</label> <input name="iva" type="hidden" id="iva" value="1" size="3">	
	<br>
	<label><input name="isr" type="checkbox" id="isr" value="checkbox" checked onChange="if(this.checked==false) form.isr_ret.value=0; else form.isr_ret.value=1">
	I.S.R. Retenido</label> <input name="isr_ret" type="hidden" id="isr_ret" value="1" size="3">	
	<br>
	<label><input name="ivr" type="checkbox" id="ivr" value="checkbox" checked onChange="if(this.checked==false) form.iva_ret.value=0; else form.iva_ret.value=1">
	I.V.A. Retenido</label> 
	<input name="iva_ret" type="hidden" id="iva_ret" value="1" size="3">
	</td>
  </tr>
  <tr>
	<td colspan="2" class="vtabla">
	<label>
	<input name="cambio_concepto" type="checkbox" id="cambio_concepto" value="checkbox" onChange="if(this.checked==false){ form.con1.value = 0; form.concepto.style.visibility='hidden'; } else { form.con1.value=1; form.concepto.style.visibility='visible'; form.concepto.select();}">
	Cambiar concepto</label>
	<input name="concepto" type="text" size="50" class="insert" style="visibility:hidden">
	<input name="con1" type="hidden" value="0" size="3"><br>
	<label>
	<input type="checkbox" name="importe" value="checkbox" onChange="if(this.checked==false){ form.renta_mod.value = 0; form.importe_nuevo.style.visibility='hidden'; } else { form.renta_mod.value=1; form.importe_nuevo.style.visibility='visible'; form.importe_nuevo.select();}">  
	Modificar importe de renta
	</label>
	<input name="importe_nuevo" type="text" class="insert" id="importe_nuevo" style="visibility:hidden " size="10" onFocus="form.temp.value=this.value;" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.value='';" onKeyDown="if(event.keyCode==13) form.anio.select();">
    <input name="renta_mod" type="hidden" id="renta_mod" value="0" size="3">	<br>
	</td>  
  </tr>
  <tr>
  <td class="tabla" colspan="2">
	<label>
	<input name="mant1" type="checkbox" value="checkbox" onChange="if(this.checked==false) form.isr_mant.value=0; else form.isr_mant.value=1;">Aplicar I.S.R. Retenido al Mantenimiento </label><input type="hidden" name="isr_mant" value="0"><br>
	<label>
	<input name="mant2" type="checkbox" value="checkbox" onChange="if(this.checked==false) form.iva_mant.value=0; else form.iva_mant.value=1;">Aplicar I.V.A. Retenido al Mantenimiento </label><input type="hidden" name="iva_mant" value="0">    
  </td>
  </tr>
</table>


<p>
    <input name="enviar" type="button" class="boton" id="enviar" onClick="valida_registro();" value="Siguiente">
  </p>
</form>  
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.anio.select();
</script>
  
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : revision -->

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de recibos de renta 
<!-- START BLOCK : aviso -->
<br>
<font color="#0000FF">PAGO ADELANTADO</font>
<!-- END BLOCK : aviso -->

</p>
<form name="form" action="./ren_recibos_man.php" method="post">
<input type="hidden" value="{contador}" name="contador">
  <table class="tabla">
    <tr>
      <th colspan="11" class="vtabla"><strong>ARRENDADOR:</strong> {cod_arrendador} {nombre_arrendador}</th>
    </tr>
    <tr>
      <td class="tabla"><strong>ARRENDATARIO</strong></td>
      <td class="tabla"><strong>BLOQUE</strong></td>
      <td class="tabla"><strong>RECIBO</strong></td>
      <td class="tabla"><strong>RENTA</strong></td>
      <td class="tabla"><strong>AGUA</strong></td>
      <td class="tabla"><strong>MANTENIMIENTO</strong></td>
      <td class="tabla"><strong>I.V.A.</strong></td>
      <td class="tabla"><strong>I.S.R. RET.</strong></td>
      <td class="tabla"><strong>I.V.A. RET.</strong> </td>
      <td class="tabla"><strong>NETO</strong></td>
      <td class="tabla"><strong>PAGO DE</strong> </td>
    </tr>
	<!-- START BLOCK : arrendatario -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><font color="#{color}">{nombre_arrendatario}</font>
          <input name="arrendatario{i}" type="hidden" id="arrendatario{i}" value="{arrendatario}">
          <input name="arrendador{i}" type="hidden" id="arrendador{i}" value="{arrendador}">
          <input name="nombre_arrendador{i}" type="hidden" id="nombre_arrendador{i}" value="{nombre_arrendador}">      </td>
      <td class="tabla">{bloque2}          
        <input name="bloque{i}" type="hidden" id="bloque{i}" value="{bloque1}"></td>
      <td class="tabla"><input name="recibo{i}" type="hidden" class="vinsert" id="recibo{i}" value="{recibo}" size="10">
          <font color="#{color}">{recibo}</font></td>
      <td class="tabla"><input name="renta{i}" type="hidden" class="vinsert" id="renta{i}" value="{renta}" size="10">
      {renta1}</td>
      <td class="tabla"><input name="agua{i}" type="hidden" class="vinsert" id="agua{i}" value="{agua}" size="10">
      {agua1}</td>
      <td class="tabla"><input name="mantenimiento{i}" type="hidden" class="vinsert" id="mantenimiento{i}" value="{mantenimiento}" size="10">
      {mantenimiento1}</td>
      <td class="tabla"><input name="iva{i}" type="hidden" class="vinsert" id="iva{i}" value="{iva}" size="10">
      {iva1}</td>
      <td class="tabla"><input name="isr_ret{i}" type="hidden" class="vinsert" id="isr_ret{i}" value="{isr_ret}" size="10">
      {isr_ret1}</td>
      <td class="tabla"><input name="iva_ret{i}" type="hidden" class="vinsert" id="iva_ret{i}" value="{iva_ret}" size="10">
      {iva_ret1}</td>
      <td class="tabla"><input name="neto{i}" type="hidden" class="vinsert" id="neto{i}2" value="{neto}" size="10">      
        {neto1}</td>
      <td class="vtabla">{fecha1}
        <input name="fecha_pago{i}" type="hidden" id="fecha_pago{i}" value="{fecha_pago}">
        <input name="fecha{i}" type="hidden" id="fecha{i}" value="{fecha}" size="10">
		<input name="comentario{i}" type="hidden" id="comentario{i}" value="{comentario}" size="10">		</td>
    </tr>
	<!-- END BLOCK : arrendatario -->
	
	<!-- START BLOCK : comentario -->
	<tr>
		<th class="tabla">Comentario</th>
		<td colspan="10" class="vtabla"><strong>{comentario}</strong></td>
	</tr>
	<!-- END BLOCK : comentario -->
  </table>
  <p>
	<input name="regresar" type="button" value="Regresar" onClick="document.location='./ren_recibos_man.php'" class="boton">&nbsp;&nbsp;
	<input name="enviar" type="button" value="Siguiente" class="boton" onClick="document.form.submit();">
	</p>
</form>

</td>
</tr>
</table>
<!-- END BLOCK : revision -->


<!-- START BLOCK : impresion -->
<script language="JavaScript" type="text/JavaScript">
	function imprime_fichas(arrendador,inicio,fin,arrendatario){
		window.open('./fichas_rentas2.php?arrendador='+arrendador+'&inicio='+inicio+'&fin='+fin+'&arrendatario='+arrendatario,'Recibos Rentas','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,menubar=yes,width=800,height=400,left=150, top=100');
		document.location='./ren_recibos_man.php';
		return;
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">IMPRIMIR RECIBO</p>

<table class="tabla">
  <tr>
    <td colspan="2" class="tabla">POR FAVOR COLOQUE EN LA IMPRESORA  RECIBOS DE RENTAS CON LAS SIGUIENTES CARACTERISTICAS</td>
  </tr>
  <tr>
    <th class="tabla">Arrendador</th>
    <th class="tabla">Folio</th>
  </tr>
  <!-- START BLOCK : recibos_imp -->
  <tr>
    <td class="vtabla">{nombre_arrendador}</td>
	<td class="tabla">{finicio}</td>
  </tr>
  <!-- END BLOCK : recibos_imp -->
</table>
<p><input name="enviar" type="button" value="Terminar" onClick="imprime_fichas({num_arrendador},{finicio},{ffinal},{num_arrendatario})" class="boton"></p>
</td>
</tr>
</table>
<!-- END BLOCK : impresion -->

