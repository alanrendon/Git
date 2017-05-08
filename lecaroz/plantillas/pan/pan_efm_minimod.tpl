<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
/*		window.opener.document.form.method = "post";
		window.opener.document.form.target = "_self";
		window.opener.document.form.action = "./hojadiaria.php?tabla=produccion";
*/		self.close();
	}
	
//	window.onload = cerrar();
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">EFECTIVO MODIFICADO <br>
{num_cia} {nombre}<br>
para el {dia} de {mes} del {anio}</p>
	<table class="tabla">
	  <tr class="tabla">
		<th scope="row" class="vtabla">AM</th>
		<td class="rtabla">{am}</td>
	  </tr>
	  <tr class="tabla">
		<th scope="row" class="vtabla">AM Error </th>
		<td class="rtabla">{am_error}</td>
	  </tr>
	  <tr class="tabla">
		<th scope="row" class="vtabla">PM</th>
		<td class="rtabla">{pm}</td>
	  </tr>
	  <tr class="tabla">
		<th scope="row" class="vtabla">PM Error </th>
		<td class="rtabla">{pm_error}</td>
	  </tr>
	  <tr class="tabla">
		<th scope="row" class="vtabla">Pastel</th>
		<td class="rtabla">{pastel}</td>
	  </tr>
	  <tr class="tabla">
		<th scope="row" class="vtabla">Venta en Puerta </th>
		<td class="rtabla"><strong>{venta_pta}</strong></td>
	  </tr>
	  <tr class="tabla">
	    <th scope="row" class="vtabla">Pastillaje</th>
	    <td class="rtabla">{pastillaje}</td>
	    </tr>
	  <tr class="tabla">
	    <th scope="row" class="vtabla">Otros</th>
	    <td class="rtabla">{otros}</td>
	    </tr>
	  <tr class="tabla">
	    <th scope="row" class="vtabla">Clientes</th>
	    <td class="rtabla">{ctes}</td>
	    </tr>
	  <tr class="tabla">
	    <th scope="row" class="vtabla">Corte Pan </th>
	    <td class="rtabla">{corte1}</td>
	    </tr>
	  <tr class="tabla">
	    <th scope="row" class="vtabla">Corte Pastel </th>
	    <td class="rtabla">{corte2}</td>
	    </tr>
	  <tr class="tabla">
	    <th scope="row" class="vtabla">Descuento Pastel </th>
	    <td class="rtabla">{desc_pastel}</td>
	    </tr>
	</table>
    <p>
      <input type="button" name="cierra" value="Cerrar" onClick="cerrar();">
    </p></td>
</tr>
</table>
<!-- END BLOCK : cerrar -->

<!-- START BLOCK : modificar -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.venta_pta.value == "") {
			alert("Debe haber venta en puerta");
			document.form.venta_pta.select();
			return false;
		}
		else
			document.form.submit();
	}
function venta_puerta(am_total,am_error,pm_total,pm_error,pastel,venta_puerta){
	if(am_total.value=="")
		var am_t=0;
	else 
		var am_t=parseFloat(am_total.value);
	if(am_error.value=="")
		var am_e=0;
	else 
		var am_e=parseFloat(am_error.value);

	if(pm_total.value=="")
		var pm_t=0;
	else 
		var pm_t=parseFloat(pm_total.value);
	if(pm_error.value=="")
		var pm_e=0;
	else 
		var pm_e=parseFloat(pm_error.value);
		
	if(pastel.value=="")
		var pas=0;
	else 
		var pas=parseFloat(pastel.value);
	var total=0;
	total = am_t - am_e + pm_t - pm_e + pas;
	venta_puerta.value=total.toFixed(2);
}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Modificar efectivo de la compa&ntilde;&iacute;a<br>
{num_cia} {nombre}<br>
para el {dia} de {mes} del {anio}</p>
<form name="form" method="post" action="./pan_efm_minimod.php">
<input name="temp" type="hidden">
<input name="idmodifica" type="hidden" id="idmodifica" value="{id}">
<input name="idefectivo" type="hidden" id="idefectivo" value="{iderfectivo}">
<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
<input name="fecha" type="hidden" id="fecha" value="{fecha}">
<table class="tabla">
  <tr>
    <th class="tabla" colspan="2">AM</th>
    <th class="tabla" colspan="2">PM</th>
    <th class="tabla" rowspan="2">Pastel</th>
    <th class="tabla" rowspan="2">Venta<br>
      Puerta</th>
    <th class="tabla" rowspan="2">Pastillaje</th>
    <th class="tabla" rowspan="2">Otros</th>
    <th class="tabla" rowspan="2">Clientes</th>
    <th class="tabla" rowspan="2">Corte<br>
      Pan </th>
    <th class="tabla" rowspan="2">Corte<br>
      Pastel</th>
    <th class="tabla" rowspan="2">Descuento<br>
      Pastel</th>
  </tr>
  <tr>
    <th class="tabla">Total</th>
    <th class="tabla">Error</th>
    <th class="tabla">Total</th>
    <th class="tabla">Error</th>
  </tr>
  <tr>
    <td class="tabla"><input name="am" type="text" class="insert" id="am" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am,form.am_error,form.pm,form.pm_error,form.pastel,form.venta_pta);" onKeyDown="if (event.keyCode == 13) form.am_error.select();" value="{am}" size="5"></td>
    <td class="tabla"><input name="am_error" type="text" class="insert" id="am_error" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am,form.am_error,form.pm,form.pm_error,form.pastel,form.venta_pta);" onKeyDown="if (event.keyCode == 13) form.pm.select();" value="{am_error}" size="5"></td>
    <td class="tabla"><input name="pm" type="text" class="insert" id="pm" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am,form.am_error,form.pm,form.pm_error,form.pastel,form.venta_pta);" onKeyDown="if (event.keyCode == 13) form.pm_error.select();" value="{pm}" size="5"></td>
    <td class="tabla"><input name="pm_error" type="text" class="insert" id="pm_error" onFocus="form.temp.value=this.value" onChange=" valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am,form.am_error,form.pm,form.pm_error,form.pastel,form.venta_pta);" onKeyDown="if (event.keyCode == 13) form.pastel.select();" value="{pm_error}" size="5"></td>
    <td class="tabla"><input name="pastel" type="text" class="insert" id="pastel" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select(); venta_puerta(form.am,form.am_error,form.pm,form.pm_error,form.pastel,form.venta_pta);" onKeyDown="if (event.keyCode == 13) form.pastillaje.select();" value="{pastel}" size="5"></td>
    <td class="tabla"><input name="venta_pta" type="text" class="insert" id="venta_pta" value="{venta_pta}" size="7" readonly></td>
    <td class="tabla"><input name="pastillaje" type="text" class="insert" id="pastillaje" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.otros.select();" value="{pastillaje}" size="5"></td>
    <td class="tabla"><input name="otros" type="text" class="insert" id="otros" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.ctes.select();" value="{otros}" size="5"></td>
    <td class="tabla"><input name="ctes" type="text" class="insert" id="ctes" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.corte1.select();" value="{ctes}" size="5"></td>
    <td class="tabla"><input name="corte1" type="text" class="insert" id="corte1" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.corte2.select();" value="{corte1}" size="5"></td>
    <td class="tabla"><input name="corte2" type="text" class="insert" id="corte2" onFocus="form.temp.value=this.value" onChange="valor=isInt(this,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.desc_pastel.select();" value="{corte2}" size="5"></td>
    <td class="tabla"><input name="desc_pastel" type="text" class="insert" id="desc_pastel" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if (event.keyCode == 13) form.num_cia.select();" value="{desc_pastel}" size="5"></td>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Cerrar ventana" onClick="self.close()">
&nbsp;&nbsp;&nbsp;
<input name="enviar" type="button" class="boton" id="enviar" value="Modificar" onClick="valida_registro()">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.am.select();
</script>
</td>
</tr>
</table>
<!-- END BLOCK : modificar -->