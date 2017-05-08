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

function actualiza_compania(num_cia, nombre) {
	cia = new Array();// Materias primas
	<!-- START BLOCK : nom_cia -->
	cia[{num_cia}] = '{nombre_cia}';
	<!-- END BLOCK : nom_cia -->
			
	if (num_cia.value > 0) {
		if (cia[num_cia.value] == null) {
			alert("Compañía "+num_cia.value+" no esta en el catálogo de compañías");
			num_cia.value = "";
			nombre.value  = "";
			num_cia.focus();
		}
		else {
			nombre.value   = cia[num_cia.value];
		}
	}
	else if (num_cia.value == "") {
		num_cia.value = "";
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
<script language="JavaScript" type="text/JavaScript">
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<form name="form" action="./historico.php" method="get"  onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();" >
  <table border="1" class="tabla">
    <tr class="tabla">
      <th class="tabla" scope="col">N&uacute;mero de Compa&ntilde;&iacute;a </th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr class="tabla">
      <td class="tabla" align="center">
        <input name="num_cia" type="text" class="insert" id="num_cia" onChange="actualiza_compania(this, form.nombre_cia)" size="3" maxlength="3">
        <input name="nombre_cia" type="text" id="nombre_cia" size="50" disabled class="vnombre">
      </td>
      <td class="tabla"><input name="anio" type="text" class="insert" id="anio" value="{anio}" size="10" maxlength="10" onChange="actualiza_fecha();"></td>
    </tr>
  </table>
  <p>
  <p></p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
  <input name="enviar" type="button" class="boton" id="enviar" onclick='valida_registro()' value="Capturar">
</form>
</td>
</tr>
</table>
<!--END BLOCK : obtener_datos -->


<!--START BLOCK : historico -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">{fecha}</p>
<script language="JavaScript" type="text/JavaScript">

function total_gral(){
var total_g;
total_g = parseFloat(document.form.ventas0.value)+parseFloat(document.form.ventas1.value)+parseFloat(document.form.ventas2.value)+
parseFloat(document.form.ventas3.value)+parseFloat(document.form.ventas4.value)+parseFloat(document.form.ventas5.value)+
parseFloat(document.form.ventas6.value)+parseFloat(document.form.ventas7.value)+parseFloat(document.form.ventas8.value)+
parseFloat(document.form.ventas9.value)+parseFloat(document.form.ventas10.value)+parseFloat(document.form.ventas11.value);
numero=new Number(total_g);
document.form.total.value=numero.toFixed(2);
}


function total_de_anio_actual(){
var tot_anio_actual;
tot_anio_actual = parseFloat(document.form.anio_actual0.value)+parseFloat(document.form.anio_actual1.value)+parseFloat(document.form.anio_actual2.value)+
parseFloat(document.form.anio_actual3.value)+parseFloat(document.form.anio_actual4.value)+parseFloat(document.form.anio_actual5.value)+
parseFloat(document.form.anio_actual6.value)+parseFloat(document.form.anio_actual7.value)+parseFloat(document.form.anio_actual8.value)+
parseFloat(document.form.anio_actual9.value)+parseFloat(document.form.anio_actual10.value)+parseFloat(document.form.anio_actual11.value);
numero=new Number(tot_anio_actual);
document.form.total_actual.value=numero.toFixed(2);
}

function total_anteior(){
var total_ant;
total_ant = parseFloat(document.form.anio_anterior0.value)+parseFloat(document.form.anio_anterior1.value)+parseFloat(document.form.anio_anterior2.value)+
parseFloat(document.form.anio_anterior3.value)+parseFloat(document.form.anio_anterior4.value)+parseFloat(document.form.anio_anterior5.value)+
parseFloat(document.form.anio_anterior6.value)+parseFloat(document.form.anio_anterior7.value)+parseFloat(document.form.anio_anterior8.value)+
parseFloat(document.form.anio_anterior9.value)+parseFloat(document.form.anio_anterior10.value)+parseFloat(document.form.anio_anterior11.value);
numero=new Number(total_ant);
document.form.total_anterior.value=numero.toFixed(2);
}


function actualiza_compania(num_cia, nombre) {
	// Arreglo con los nombres de las materias primas
	cia = new Array();				// Materias primas
	<!-- START BLOCK : nombre_cia -->
	cia[{num_cia}] = '{nombre_cia}';
	<!-- END BLOCK : nombre_cia -->
			
	if (num_cia.value > 0) {
		if (cia[num_cia.value] == null) {
			alert("Compañía "+num_cia.value+" no esta en el catálogo de compañías");
			num_cia.value = "";
			nombre.value  = "";
			num_cia.focus();
		}
		else {
			nombre.value   = cia[num_cia.value];
		}
	}
	else if (num_cia.value == "") {
		num_cia.value = "";
		nombre.value  = "";
	}
}


</script>

<form name="form" method="post" action="./insert_historico.php?tabla={tabla}">
  <table border="1" class="tabla">
    <tr class="tabla">
     <input name="num_cia" type="hidden" value="{num_cia}" id="num_cia">
     <input name="anio" type="hidden" value="{anio}" id="anio">
      <th class="tabla"><font size="+1">Compa&ntilde;&iacute;a</font></th>
      <td class="tabla"><font size="+1">{num_cia}-{nombre_cia}</font> </td>
    </tr>
  </table>
  <p></p>
  <table border="1" class="tabla">
    <tr class="tabla">
      <th class="tabla">Mes</th>
      <th class="tabla">A&ntilde;o anterior ({anio_anterior})</th>
      <th class="tabla">A&ntilde;o Actual ({anio_actual}) </th>
      <th class="tabla">Ventas</th>
    </tr>
    <!-- START BLOCK : meses -->
    <tr class="tabla">
      <th class="vtabla" align="center">{nombre_mes}
          <input name="mes{i}" type="hidden" value="{m}" size="4">
          <input name="fecha_anio_anterior{i}" type="hidden" id="fecha_anio_anterior{i}" value="{fecha_anio_anterior}" size="4">
          <input name="fecha_anio_actual{i}" type="hidden" id="fecha_anio_actual{i}" value="{fecha_anio_actual}" size="4">
          <input name="num_cia{i}" type="hidden" id="num_cia{i}" value="{num_cia}" size="4">
</th>
      <td class="tabla" align="center"><span class="vtabla">
      <input name="anio_anterior{i}" type="text" class="insert" id="anio_anterior{i}2" value="0" size="15" onKeyDown="if(event.keyCode == 13){var salto={m}; if (salto==12)document.form.anio_actual0.select(); else document.form.anio_anterior{m}.select();}" onChange="total_anteior();">
      </span> </td>
      <td class="tabla" align="center"><input name="anio_actual{i}" type="text" class="insert" id="anio_actual{i}" value="0" size="15" onKeyDown="if(event.keyCode == 13){var salto={m}; if (salto==12)document.form.ventas0.select(); else document.form.anio_actual{m}.select();}" onChange="total_de_anio_actual();"></td>
      <td class="tabla" align="center"><input name="ventas{i}" type="text" class="insert" id="ventas{i}" onChange="total_gral();" value="0" size="15" onKeyDown="if(event.keyCode == 13) document.form.ventas{m}.select();}"></td>
    </tr>
    <!-- END BLOCK : meses -->
<script language="JavaScript" type="text/JavaScript">
function ajuste(importe)
{
	numero=new Number(importe.value);
	importe.value=numero.toFixed(2);
	document.form.importe11.value=importe.value - document.form.total.value;

//	importe.value=numero;
}

function valida() {
	if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();
	}



</script>
    <tr class="tabla">
      <th class="tabla" align="center">TOTAL</th>
      <td class="tabla"><input name="total_anterior" type="text" class="nombre" value="0" size="15" readonly></td>
      <td class="tabla"><input name="total_actual" type="text" class="nombre" value="0" size="15" readonly></td>
      <td class="tabla"><input name="total" type="text" class="nombre" value="0" size="15" readonly></td>
    </tr>
  </table>
  <p> <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
      <input type="button" name="enviar" class="boton" value="Capturar" onclick='valida()'>
&nbsp; <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;
    <input name="button" type="button" class="boton" onclick='borrar()' value="Borrar formulario">
  </p>
</form>

</td>
</tr>
</table>
<!-- END BLOCK : historico -->