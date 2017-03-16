<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">

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
<td align="center" valign="middle"><p class="title">Revisi&oacute;n de recibos sin imprimir </p>
  <form name="form" action="./ren_recibos_cap.php" method="get">
  <input name="temp" type="hidden">
  
  <table class="tabla">
  <tr class="tabla">
    <th class="tabla">Mes</th>
    <th class="tabla">A&ntilde;o</th>
    <th class="tabla">Impresion</th>
  </tr>
  <!-- START BLOCK : mes -->
  <tr class="tabla">
    <td class="vtabla">{nombre_mes}<input name="mes{i}" type="hidden" id="mes{i}" value="{mes}"></td>
    <td class="tabla">{anio}<input name="anio{i}" type="hidden" id="anio{i}" value="{anio}"></td>
    <td class="tabla"><input name="imprime{i}" type="button" class="boton" id="imprime{i}" value="Imprimir" onClick="document.location = './ren_recibos_pen.php?temp=0&mes={mes}&anio={anio}'"></td>
  </tr>
  <!-- END BLOCK : mes -->
</table>
</form>  
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.anio.select();
</script>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->


<!-- START BLOCK : impresion -->
<script language="JavaScript" type="text/JavaScript">
	function imprime_fichas(arrendador,inicio,fin){
		window.open('./fichas_rentas.php?arrendador='+arrendador+'&inicio='+inicio+'&fin='+fin,'Recibos Rentas','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,menubar=yes,width=800,height=400,left=150, top=100');
		return;
	}
	function revisar(){
		if(document.form.arrendadores.value==0)
			document.location='./ren_recibos_pen.php';
		else{
			if(confirm("Esta dejando recibos sin imprimir, \r ¿Está seguro(a) de salir de la ventana?"))
				document.location='./ren_recibos_pen.php';
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
            <th class="tabla">Por favor introduzca <br>
              recibos con los siguientes <br>
              folios: </th>
            <th class="tabla">Recibos</th>
          </tr>
          <!-- START BLOCK : recibos_arrendador -->
          <tr>
            <th class="rtabla">{cod_arrendador}</th>
            <td class="vtabla">{nombre_arrendador}</td>
            <td class="tabla">{finicio} - {ffinal} </td>
            <td class="tabla"><input name="boton" type="button" class="boton" id="boton" onClick="imprime_fichas({cod_arrendador},{finicio},{ffinal}); this.disabled=true; form.arrendadores.value=parseInt(form.arrendadores.value) -1;" value="Generar recibos"></td>
          </tr>
          <!-- END BLOCK : recibos_arrendador -->
        </table>
        <p>
          <input name="enviar" type="button" value="Salir" onClick="revisar();" class="boton">
        </p>
    </form></td>
  </tr>
</table>
<!-- END BLOCK : impresion -->