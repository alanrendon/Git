<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : revision -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Recibos de renta <br>
{nombre_mes} del {anio}</p>

  <form name="form" action="./ren_recibos_cap1.php" method="post">
  <input name="mes" type="hidden" value="{mes}">
  <input name="anio" type="hidden" value="{anio}">
  <input name="registros" type="hidden" id="registros" value="{cont}">
  <input name="registros_arrendatarios" type="hidden" id="registros_arrendatarios" value="{registros}">  
<table class="tabla">
  <!-- START BLOCK : arrendadores -->
  <tr>
 	<th colspan="10" class="vtabla"><strong>ARRENDADOR:</strong> {cod_arrendador} {nombre_arrendador}</th>
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
    
  </tr>
  
  <!-- START BLOCK : arrendatarios -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vtabla"><font color="#{color}">{nombre_arrendatario}</font>
      <input name="arrendatario{i}" type="hidden" value="{arrendatario}">
	  <input name="arrendador{i}" type="hidden" value="{arrendador}">
	  <input type="hidden" name="nombre_arrendador{i}" value="{nombre_arrendador}">
	  
	</td>
	<td class="tabla">{bloque2}<input name="bloque{i}" type="hidden" id="bloque{i}" value="{bloque1}"></td>
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
    <td class="tabla"><input name="neto{i}" type="hidden" class="vinsert" id="neto{i}" value="{neto}" size="10">
    {neto1}</td>

    
  </tr>
  <!-- END BLOCK : arrendatarios -->
  <!-- END BLOCK : arrendadores -->
</table>

<p>
<input name="regresar" type="button" value="Regresar" onClick="document.location='./ren_recibos_cap.php?mes={mes}&anio={anio}'" class="boton">&nbsp;&nbsp;
<input name="enviar" type="button" value="Siguiente" class="boton" {disabled} onClick="document.form.submit();">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.recibo0.select();
</script>

</td>
</tr>
</table>

<!-- END BLOCK : revision -->

<!-- START BLOCK : impresion -->
<script language="JavaScript" type="text/JavaScript">
	function imprime_fichas(arrendador,inicio,fin){
		window.open('./fichas_rentas.php?arrendador='+arrendador+'&inicio='+inicio+'&fin='+fin,'Recibos Rentas','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,menubar=yes,width=800,height=400,left=150, top=100');
		return;
	}
	function revisar(){
		if(document.form.arrendadores.value==0)
			document.location='./ren_recibos_cap.php';
		else{
			if(confirm("Esta dejando recibos sin imprimir, \r ¿Está seguro(a) de salir de la ventana?"))
				document.location='./ren_recibos_cap.php';
			else
				return;
		}
	}

</script>



<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Recibos de renta <br>
{nombre_mes} del {anio} </p>
<form name="form">
<input name="arrendadores" type="hidden" value="{valor}">


<table class="tabla">
  <tr>
    <th class="tabla" colspan="2">Arrendador</th>
    <th class="tabla">Por favor introduzca <br>recibos con los siguientes <br>folios: </th>
    <th class="tabla">Recibos</th>
  </tr>
 <!-- START BLOCK : recibos_arrendador --> 
  <tr>
    <th class="rtabla">{cod_arrendador}</th>
	<td class="vtabla">{nombre_arrendador}</td>
    <td class="tabla">{finicio} - {ffinal} </td>
    <td class="tabla"><input name="boton" type="button" class="boton2" id="boton" onClick="imprime_fichas({cod_arrendador},{finicio},{ffinal}); this.disabled=true; form.arrendadores.value=parseInt(form.arrendadores.value) -1;" value="Generar recibos"></td>
  </tr>
 <!-- END BLOCK : recibos_arrendador -->
</table>
<p>
<input name="enviar" type="button" value="Salir" onClick="revisar();" class="boton2">
</p>

</form>
</td>
</tr>
</table>
<!-- END BLOCK : impresion -->


