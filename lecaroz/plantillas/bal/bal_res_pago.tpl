<script type="text/javascript" language="JavaScript">

	function valida_registro() {
	if(document.form.cod_reserva.value==""){
		alert("Falta código");
		document.form.cod_reserva.select();
		}
	else document.form.submit();
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.form.reset();
		else
			document.form.num_gastos.focus();
	}
	
function actualiza_reserva(num_reserva, nombre) {
	// Arreglo con los nombres de las materias primas
	reserva = new Array();			// Materias primas
	<!-- START BLOCK : nombre_reserva -->
	reserva[{tipo_res}] = '{descripcion}';
	<!-- END BLOCK : nombre_reserva -->
			
	if (num_reserva.value > 0) {
		if (reserva[num_reserva.value] == null) {
			alert("Reserva "+num_reserva.value+" no esta en el catálogo de reservas");
			num_reserva.value = "";
			nombre.value  = "";
			num_reserva.focus();
		}
		else {
			nombre.value   = reserva[num_reserva.value];
		}
	}
	else if (num_reserva.value == "") {
		num_reserva.value = "";
		nombre.value  = "";
	}

	
}


function totales(importe)
{
	numero=new Number(importe.value);
	importe.value=numero.toFixed(2);
}



</script>
<!--START BLOCK : obtener_datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<script language="JavaScript" type="text/JavaScript">
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<p class="title">Pago de Reservas de fin de año</p>
<form name="form" action="./bal_res_pago.php" method="get"  onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();" >
  <p>
  <table border="1" class="tabla">
    <tr class="tabla">
      <th class="tabla" scope="col">C&oacute;digo de Reserva </th>
    </tr>
    <tr>
      <td>
        <input name="cod_reserva" type="text" class="insert" onChange="actualiza_reserva(this,form.nom_reserva)" size="4" maxlength="3">
        <input name="nom_reserva" type="text" id="nom_reserva" size="20" disabled class="vnombre">
      </td>
    </tr>
  </table>
  <p></p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
  <input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload=document.form.cod_reserva.select();</script>
<!--END BLOCK : obtener_datos -->	


<!--START BLOCK : reservas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Pago de Reservas para el año {anio_ac} <br>{reserva}<span class="vtabla">

</span></p>
<form name="form" method="post" action="bal_res_pago1.php?tabla={tabla}">
  <input name="cont" type="hidden" id="cont" value="{cont}">
  <input name="reserva" type="hidden" id="reserva" value="{res1}">
  <input name="temp" type="hidden" id="temp">  
  <table border="1">
  <tr>
    <th scope="col" class="tabla" colspan="2">Compañía</th>
    <th scope="col" class="tabla">Pago</th>
  </tr>
<!-- START BLOCK : rows -->
  <tr>
    <th class="rtabla">
	{num_cia}
	</th>
    <td class="vtabla"><strong>{nom_cia}</strong>
      <input name="num_cia{i}" type="hidden" id="num_cia{i}" value="{num_cia}"></td>
    <td class="vtabla"><input name="pagado{i}" type="text" class="insert" id="pagado{i}" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) pagado{next}.select()" size="10"></td>
  </tr>
 <!-- END BLOCK : rows -->
</table>



<p>
  <input type="button" name="enviar2" class="boton" value="Capturar" onclick='document.form.submit();'>
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload=document.form.pagado0.select();</script>
<!-- END BLOCK : reservas -->