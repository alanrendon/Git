<script type="text/javascript" language="JavaScript">

	function valida_registro() {
		/*if(document.form.num_gastos.value <= 0) {
			alert('Debe especificar un numero de gastos a insertar');
			document.form.idcia.focus();
		}
		else {*/
//if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();
		//}
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

<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Pago de Reservas para el año {anio_ac} <br>{reserva}</p>
<form name="form" method="POST" action="actualiza_bal_pago.php?tabla={tabla}">
<input name="reserva" type="hidden" id="reserva" value="{res}">
<input name="anio" type="hidden" id="anio" value="{anio}">
<input name="contador" type="hidden" id="contador" value="{contador}">
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
    <td class="rtabla"><input name="pagado{i}" type="hidden" class="insert" id="pagado{i}" value="{pago}" size="10">
      <strong>{pago1}</strong></td>
  </tr>
 <!-- END BLOCK : rows -->
<!-- START BLOCK : total -->
  <tr class="tabla">
    <th class="tabla" colspan="2">Total</th>
    <th class="rtabla"><strong><font size="+1">{total}</font></strong></th>
  </tr>
 <!-- END BLOCK : total -->
</table>



<p>	<img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Continuar" onclick="if(confirm('¿Capturar datos?')) document.form.submit(); else return false;">

    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Regresar" onclick='parent.history.back()'>
</p>
</form>
</td>
</tr>
</table>