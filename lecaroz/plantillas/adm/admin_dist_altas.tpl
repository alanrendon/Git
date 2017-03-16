<!-- START BLOCK : obtener_datos  -->
<script type="text/javascript" language="JavaScript">
	function valida()
	{
		if(document.form.cia.value <= 0) {
			alert('Debe especificar una compañía');
			document.form.cia.select();
		}
		else if(document.form.cia.value==""){
			alert('Debe especificar una compañía');
			document.form.cia.select();
		}
		else if(document.form.numero.value <= 0) {
			alert('Debe especificar un número de accionistas');
			document.form.numero.select();
		}
		else if(document.form.numero.value==""){
			alert('Debe especificar un número de accionistas');
			document.form.numero.select();
		}
		else if(document.form.numero.value >5){
			alert('No puede haber mas de 5 accionistas');
			document.form.numero.select();
			}
		else
			document.form.submit();
	}
	
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">ALTA DE PORCENTAJES DE DISTRIBUCIONES </p>
<form name="form" action="./admin_dist_altas.php" method="get">
<table class="tabla" >
  <tr class="tabla">
    <th class="tabla">Número de compañía</th>
    <th class="tabla"><input name="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.numero.select();"></th>
  </tr>
  <tr class="tabla">
    <td class="tabla">Número de accionistas</td>
    <td class="tabla"><input name="numero" type="text" class="insert" id="numero" onKeyDown="if(event.keyCode==13)form.enviar.focus();" value="3" size="4" maxlength="4"></td>
  </tr>
</table>
<p>
  <input type="button" name="enviar" value="Siguiente" class="boton" onClick="valida();">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.cia.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : distribuciones -->
<script language="JavaScript" type="text/JavaScript">
	function actualiza_accionista(numero, nombre) {
		// Arreglo con los nombres de los gastos
		acc = new Array();
		<!-- START BLOCK : nombre_acc -->
		acc[{num_accionista}] = '{nombre_corto}';
		<!-- END BLOCK : nombre_acc -->
		
		if (parseInt(numero.value) > 0) {
			if (acc[parseInt(numero.value)] == null) {
				alert("Número "+parseInt(numero.value)+" no esta en el catálogo de accionistas");
				numero.value = "";
				nombre.value  = "";
				numero.select();
				return false;
			}
			else {
				numero.value = parseFloat(numero.value);
				nombre.value  = acc[parseInt(numero.value)];
				return;
			}
		}
		else if (numero.value == "") {
			numero.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	var casillas=new Array({i});
	
/*
	function revisa_numero(accionista,indice);
	var i=0;
	var valor=accionista.value;
	casillas[indice]=valor.value;
	for(i=0;i<)
*/
	function verifica(valor,temp,total)
	{
	if (temp.value=="") temp.value=0;
	if (valor.value=="") valor.value=0;
	if (total.value=="") total.value=0;

	var entrada=parseFloat(valor.value);
	var tem=parseFloat(temp.value);
	var suma=parseFloat(total.value);
	
	if(tem > 0)
		{
		suma-=tem;
		suma+=entrada;
		}
	else
		suma+=entrada;
	
	total.value=suma.toFixed(2);
	
	if(total.value==100)
		{
		document.form.enviar.disabled=false;

		}
	else
		{
		document.form.enviar.disabled=true;

		}
	}


</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">ALTA DE PORCENTAJES DE DISTRIBUCIONES </p>
<form name="form" action="./insert_dist_porc.php" method="post">
<input name="temp" type="hidden">
<input name="contador" type="hidden" value="{conta}">
<input name="num_cia" type="hidden" value="{num_cia}">
<table class="tabla">
  <tr class="tabla">
    <th class="tabla" colspan="3">{num_cia}&nbsp;{nombre_cia}</th>

  </tr>
  <!-- START BLOCK : rows -->
  <tr class="tabla">
    <td class="tabla">
	<input name="num_accionista{i}" type="text" class="insert" size="3" maxlength="3" onChange="actualiza_accionista(this,nombre{i});" onKeyDown="if(event.keyCode==13) form.porcentaje{i}.select();">
	</td>
    <td class="tabla"><input name="nombre{i}" class="vnombre" type="text" id="nombre{i}" size="50" readonly ></td>
	<td class="tabla"><input name="porcentaje{i}" type="text" class="rinsert" id="porcentaje{i}" size="7" maxlength="7" onChange="verifica(this,form.temp,form.total)" onKeyDown="if(event.keyCode==13) form.num_accionista{next}.select();" onFocus="form.temp.value=this.value"></td>
  </tr>
  <!-- END BLOCK : rows -->
  <tr class="tabla">
    <td class="rtabla" colspan="2">Porcentaje final</td>

    <td class="tabla"><input name="total" type="text" class="rinsert" id="total" size="4" maxlength="7"></td>
  </tr>
</table>
<p>
  <input type="button" name="regresar" value="Regresar" class="boton" onClick="parent.history.back();">&nbsp;&nbsp;
  <input type="button" name="enviar" value="Guardar" class="boton" disabled onClick="document.form.submit();">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_accionista0.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : distribuciones -->
