<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
	function actualiza_arrendador(num_arr, nombre) {
		arrendador = new Array();
		<!-- START BLOCK : nombre_arrendador -->
		arrendador[{num_arr}] = '{nombre_arrendador}';
		<!-- END BLOCK : nombre_arrendador -->
				
		if (num_arr.value > 0) {
			if (arrendador[num_arr.value] == null) {
				alert("Arrendador "+num_arr.value+" erroneo");
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
function valida(){
	if(document.form.arrendador.value==""){
		alert("Debes especificar un arrendador");
		return;
	}
	else if(document.form.folio.value==""){
		alert("Debes especificar un folio");
		return;
	}
	else
		document.form.submit();
}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n de recibos de rentas </p>
  <form name="form" action="./ren_recibos_can.php" method="get">
  <input name="temp" type="hidden">
  
  <table class="tabla">
  <tr class="tabla">
    <th class="tabla">ARRENDADOR</th>
    <th class="tabla">FOLIO</th>
  </tr>
  <tr class="tabla">
    <td class="vtabla">
	  <input name="arrendador" type="text" class="insert" id="arrendador" size="4" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.value=''; else actualiza_arrendador(this,form.nombre_arrendador);" onKeyDown="if(event.keyCode==13) form.folio.select();">
      <input name="nombre_arrendador" type="text" class="vnombre" id="nombre_arrendador" size="75" readonly>	</td>
    <td class="vtabla"><input name="folio" type="text" class="insert" id="folio" size="10" onKeyDown="if(event.keyCode==13) form.arrendador.select();" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
  </tr>
</table>
<p>
<input name="enviar" value="Buscar folio" type="button" onClick="valida();" class="boton">
</p>
</form>  
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.arrendador.select();
</script>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->


<!-- START BLOCK : cancelacion -->
<script language="JavaScript" type="text/JavaScript">

</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="center" valign="middle">
      <p class="title">Cancelaci&oacute;n de recibos de rentas </p>
      <form name="form" method="post" action="./ren_recibos_can.php">
	  
	  <table class="tabla">
  <tr>
    <th class="vtabla">FOLIO DE RECIBO </th>
    <td class="tabla"><input name="id" type="hidden" value="{id}"><strong>{folio}</strong></td>
  </tr>
  <tr>
    <th class="vtabla">ARRENDADOR</th>
    <td class="vtabla">{arrendador}</td>
  </tr>
  <tr>
    <th class="vtabla">ARRENDATARIO</th>
    <td class="vtabla">{arrendatario}</td>
  </tr>
  <tr>
    <th class="vtabla">BLOQUE</th>
    <td class="tabla">{bloque}</td>
  </tr>
  <tr>
    <th class="vtabla">RENTA</th>
    <td class="tabla">{renta}</td>
  </tr>
  <tr>
    <th class="vtabla">AGUA</th>
    <td class="tabla">{agua}</td>
  </tr>
  <tr>
    <th class="vtabla">MANTENIMIENTO</th>
    <td class="tabla">{mantenimiento}</td>
  </tr>
  <tr>
    <th class="vtabla">I.V.A.</th>
    <td class="tabla">{iva}</td>
  </tr>
  <tr>
    <th class="vtabla">RETENCI&Oacute;N I.S.R. </th>
    <td class="tabla">{ret_isr}</td>
  </tr>
  <tr>
    <th class="vtabla">RETENCI&Oacute;N I.V.A. </th>
    <td class="tabla">{ret_iva}</td>
  </tr>
  <tr>
    <th class="vtabla">NETO</th>
    <td class="tabla">{neto}</td>
  </tr>
</table>

        <p>
          <input name="regresar" type="button" class="boton" id="regresar" onClick="document.location = './ren_recibos_can.php'" value="Regresar">
		  &nbsp;&nbsp;&nbsp;
		  <input name="enviar" type="button" class="boton" id="enviar" onClick="if(confirm('¿Está segura de cancelar este folio?')) document.form.submit();" value="CANCELAR FOLIO">
        </p>
    </form></td>
  </tr>
</table>
<!-- END BLOCK : cancelacion -->