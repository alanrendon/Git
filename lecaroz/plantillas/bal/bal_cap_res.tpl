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
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Reservas de Compa&ntilde;&iacute;as</p>
<form name="form" action="./bal_cap_res.php" method="get"  onKeyDown="if (event.keyCode == 13) document.form.enviar.focus();" >
<input name="temp" type="hidden" value="">
  <table border="1" class="tabla">
    <tr class="tabla">
      <th class="tabla" scope="col">N&uacute;mero de Compa&ntilde;&iacute;a </th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr class="tabla">
      <td class="tabla" align="center">
        <input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_compania(this, form.nombre_cia)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) anio.select();
else if (event.keyCode == 38 || event.keyCode == 40) cod_reserva.select();" size="3" maxlength="3">
        <input name="nombre_cia" type="text" id="nombre_cia" size="50" disabled class="vnombre">
      </td>
      <td class="tabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) cod_reserva.select();
else if (event.keyCode == 37 || event.keyCode == 39) num_cia.select();
else if (event.keyCode == 40) importe.select();" value="{anio}" size="10" maxlength="10"></td>
    </tr>
  </table>
  <p>
  <table border="1" class="tabla">
    <tr class="tabla">
      <th class="tabla" scope="col">C&oacute;digo de Reserva </th>
      <th class="tabla" scope="col">Importe </th>
    </tr>
    <tr>
      <td>
        <input name="cod_reserva" type="text" class="insert" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_reserva(this,form.nom_reserva)" onKeyDown="if (event.keyCode == 13) importe.select();
else if (event.keyCode == 38) num_cia.select();
else if (event.keyCode  == 37 || event.keyCode == 39) importe.select();" size="4" maxlength="3">
        <input name="nom_reserva" type="text" id="nom_reserva" size="20" disabled class="vnombre">
      </td>
       <td>
           <input name="importe" type="text" class="insert" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13) enviar2.focus();
else if (event.keyCode == 38) anio.select();
else if (event.keyCode == 37 || event.keyCode == 39) cod_reserva.select();" size="15" maxlength="15">
         </td>

    </tr>
  </table>
  <br>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="1" checked>
        Enero</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="2" checked>
        Febrero</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="3" checked>
	    Marzo</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="4" checked>
        Abril</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="5" checked>
        Mayo</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="6" checked>
        Junio</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="7" checked>
        Julio</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="8" checked>
        Agosto</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="9" checked>
        Septiembre</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="10" checked>
	    Octubre</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="11" checked>
        Noviembre</th>
      <th class="vtabla" scope="col"><input name="mes[]" type="checkbox" id="mes" value="12" checked>
	    Diciembre</th>
    </tr>
  </table>
  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;
  <input type="button" name="enviar2" class="boton" value="Capturar" onclick='valida_registro()'></p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_cia.select();</script>
<!--END BLOCK : obtener_datos -->


<!--START BLOCK : reservas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">{fecha}</p>
<script language="JavaScript" type="text/JavaScript">

function totales(importe)
{
numero=new Number(importe.value);
importe.value=numero.toFixed(2);
total=new Number(parseFloat(document.form.total.value) + parseFloat(numero.toFixed(2)));
document.form.total.value=total;
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

<form name="form" method="post" action="insert_bal_cap_res.php?tabla={tabla}">
  <table border="1" class="tabla">
    <tr class="tabla">
     <input name="num_cia" type="hidden" value="{num_cia}" id="num_cia">
     <input name="anio" type="hidden" value="{anio}" id="anio">
      <th class="tabla"><font size="+1">Compa&ntilde;&iacute;a</font></th>
      <td class="tabla"><font size="+1">{num_cia}-{nombre_cia}</font> </td>
      <th class="tabla"><font size="+1"> A&ntilde;o</font> </th>
      <td class="tabla"><font size="+1"> {anio}</font> </td>
    </tr>
  </table>
  <p></p>
  <table border="1" class="tabla">
    <tr class="tabla">
      <th class="tabla">Mes</th>
      <th class="tabla">C&oacute;digo Reserva </th>
      <th class="tabla">Importe</th>
    </tr>
    <!-- START BLOCK : meses -->
    <tr class="tabla">
      <th class="vtabla" align="center">{nombre_mes}
          <input name="mes{i}" type="hidden" value="{m}" size="4">
          <input name="anio{i}" type="hidden" value="{anio}" size="4">
          <input name="num{i}" type="hidden" value="{i}" size="4">
      </th>
      <td class="tabla" align="center"><input name="cod_reserva{i}" type="hidden" class="insert" id="cod_reserva{i}" value="{cod_reserva}"  size="4" maxlength="3">
        {cod_reserva}-{nombre_reserva} </td>
      <td class="tabla" align="center">
        <input class="insert" name="importe{i}" type="text" onChange="totales(this);" value="{importe}" size="15" readonly>{importe1}
</td>
    </tr>
    <!-- END BLOCK : meses -->
	<!-- START BLOCK : totales -->
<script language="JavaScript" type="text/JavaScript">
function ajuste(importe)
{
	numero=new Number(importe.value);
	importe.value=numero.toFixed(2);
	var temp = importe.value - document.form.total.value;
	document.form.importe11.value=temp.toFixed(2);
//	importe.value=numero;
}

function valida() {
/*	if(document.form.pagado.value <= 0) {
		alert('Debe especificar un monto a pagar');
		document.form.pagado.focus();
	}
	else if(parseFloat(document.form.importe11.value) < 0)
	{
		alert('El valor del mes de Diciembre es negativo');
		document.form.pagado.focus();
	}
	else {
	*/if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();
		//}
	}



</script>
    <tr class="tabla">
      <th class="tabla" colspan="2" align="center">TOTAL</th>
      <td class="tabla"><input name="total" type="hidden" class="vnombre" value="{total}" size="15" readonly>{total1}</td>
    </tr>
    <tr class="tabla">
      <th class="tabla" colspan="2" align="center">Pagado</th>
      <td class="tabla"><input name="pagado" type="text" class="insert" id="pagado" onChange="ajuste(this);" value="{pagado}" size="15"></td>
    </tr>
	<!-- END BLOCK : totales -->
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='bal_cap_res.php'">
&nbsp;&nbsp;    
<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida()'>
  </p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : reservas -->