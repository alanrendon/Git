<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : disabled -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Aguinaldos</p>
	<p class="title">USTED NO TIENE ACCESO A ESTE PROGRAMA</p>
</td>
</tr>
</table>
<!-- END BLOCK : disabled -->
<!-- START BLOCK : programa -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Aguinaldos</p>
  <table>
    <tr>
      <td align="center" valign="top" scope="col"><p><font face="Arial, Helvetica, sans-serif">Generar Aguinaldos </font></p>
        <form action="./fac_tra_agu.php" method="get" name="generar">
          <input name="tmp" type="hidden" id="tmp">
          <table class="tabla">
        <tr>
          <th class="vtabla" scope="row">Fecha</th>
          <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" value="{fecha}" size="10" maxlength="10" readonly="true"></td>
        </tr>
        <tr>
          <th class="vtabla" scope="row">% Incremento </th>
          <td class="vtabla"><input name="incremento" type="text" class="insert" id="incremento" value="{incremento}" size="5" maxlength="5"></td>
        </tr>
        <tr>
          <th class="vtabla" scope="row">Repartir en </th>
          <td class="vtabla"><input name="bill[]" type="checkbox" id="bill[]" value="1000" checked>
            Billetes de 1000<br>
            <input name="bill[]" type="checkbox" id="bill" value="500" checked>
            Billetes de 500<br>
            <input name="bill[]" type="checkbox" id="bill" value="200" checked>
            Billetes de 200<br>
            <input name="bill[]" type="checkbox" id="bill" value="100" checked>
            Billetes de 100<br>
            <input name="bill[]" type="checkbox" id="bill" value="50" checked>
            Billetes de 50<br>
            <input name="bill[]" type="checkbox" id="bill" value="20" checked>
            Billetes de 20</td>
        </tr>
      </table>
        <p>
          <input type="button" class="boton" value="Generar Aguinaldos" onClick="validar_aguinaldos(this.form)" {disabled_agu}>
        </p></form>
<script language="javascript" type="text/javascript">
	function validar_aguinaldos(form) {
		if (form.fecha.value.length < 8) {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else if (form.incremento.value < 0) {
			alert("Debe especificar el porcentaje de incremento");
			form.incremento.select();
			return false;
		}
		else {
			var ok = false;
			for (i = 0; i < form.bill.length; i++) {
				if (form.bill[i].checked) {
					ok = true;
				}
			}
			
			if (!ok) {
				alert("Debe seleciconar al menos una de las denominaciones");
				return false;
			}
			else if (confirm("¿Desea generar los aguinaldos?")) {
				form.submit();
			}
			else {
				form.fecha.select();
				return false;
			}
				
		}
	}
</script>
<form action="./fac_agu_tot.php" method="get" name="form_tot" target="totales" id="form_tot">
<table class="tabla">
<tr>
  <th class="vtabla"><input name="mancomunadas" type="checkbox" id="mancomunadas" value="1">
  <font face="Arial, Helvetica, sans-serif">Mancomunadas</font></th>
  <th class="vtabla"><input name="button" type="button" class="boton" value="Listado de Totales" onClick="print_totales()"></th>
</tr>
<tr>
  <th colspan="2" class="tabla"><input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[1].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[2].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[3].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[4].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[5].select()" size="3"><br>
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[6].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[7].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[8].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[9].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[10].select()" size="3"><br>
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[11].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[12].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[13].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[14].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[15].select()" size="3"><br>
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[16].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[17].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[18].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[19].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[0].select()" size="3"></th>
  </tr>
</table>
</form>
<form action="./fac_agu_rec.php" method="get" name="form2">
  <p>
  <font face="Arial, Helvetica, sans-serif">Cia.:</font>
  <input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3">
&nbsp;&nbsp;  
<input name="button2" type="button" class="boton" onClick="validar_recibos(this.form)" value="Imprimir Recibos">
</p>
</form>
<script language="javascript" type="text/javascript">
function validar_recibos(form) {
	if (confirm("¿Desea imprimir los recibos?"))
		form.submit();
	else
		return false;
}
</script>
<form name="form1" method="get" action="./fac_agu_rec.php">
  <font face="Arial, Helvetica, sans-serif">Num. Emp.</font>
  <input name="num_emp" type="text" class="insert" id="num_emp" size="4" maxlength="4">
&nbsp;&nbsp;  
<input type="button" class="boton" value="Imprimir Recibo" onClick="validar_recibo_emp(this.form)">
</form>
<script language="javascript" type="text/javascript">
function validar_recibo_emp(form) {
	if (form.num_emp.value <= 0) {
		alert("Debe especificar el número de empleado");
		form.num_emp.select();
		return false;
	}
	else {
		form.submit();
	}
}
</script>
<p>
  <input type="button" class="boton" value="Imprimir Comprobantes" onClick="print_fichas()">
</p>
<script language="javascript" type="text/javascript">
	function print_totales() {
		ventana = window.open("./fac_agu_tot.php","totales","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=1024,height=768");
		document.form_tot.submit();
		ventana.focus();
	}
	
	function print_recibos() {
		document.location = './fac_agu_rec.php';
	}
	
	function print_fichas() {
		ventana = window.open("./cometra_agu.php","fichas","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=1024,height=768");
		ventana.focus();
	}
</script>
<input type="button" class="boton" value="Buscar Trabajador" onClick="minibus()"></td>
      <td scope="col">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td align="center" valign="top" scope="col"><p><font face="Arial, Helvetica, sans-serif">Generar Listados</font></p>
        <form action="./fac_tra_lis_agu.php" method="get" name="listados" id="listados">
          <input name="tmp" type="hidden" id="tmp">
          <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;ia</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Puesto</th>
      <td class="vtabla"><select name="cod_puestos" class="insert" id="cod_puestos">
        <option value="" selected>-</option>
		<!-- START BLOCK : puesto -->
		<option value="{cod_puestos}">{cod_puestos} {descripcion}</option>
		<!-- END BLOCK : puesto -->
      </select></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Turno</th>
      <td class="vtabla"><select name="cod_turno" class="insert" id="cod_turno">
        <option value="" selected>-</option>
		<!-- START BLOCK : turno -->
		<option value="{cod_turno}">{cod_turno} {descripcion}</option>
		<!-- END BLOCK : turno -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Datos<br>
        Adicionales</th>
      <td class="vtabla"><input name="puesto" type="checkbox" id="puesto" value="1">
        Puesto<br>
            <input name="turno" type="checkbox" id="turno" value="1">
              Turno<br>
                  <input name="antiguedad" type="checkbox" id="antiguedad" value="1">
                    Antig&uuml;edad<br>
      <input name="agu_ant" type="checkbox" id="agu_ant" value="1">
        Aguinaldo Anterior<br>
        <input name="status_ant" type="checkbox" id="status_ant" value="1">
        Estatus Aguinaldo Anterior         <br>
        <input name="agu_act" type="checkbox" id="agu_act" value="1">
        Aguinaldo Actual <br>
        <input name="status" type="checkbox" id="status" value="1">
        Estatus Aguinaldo<br>
        <input name="notes" type="checkbox" id="notes" value="1">
        Anotaciones<br>
        <input name="desglose" type="checkbox" id="desglose" value="1">
        Desglose de Aguinaldos </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Criterios de<br> 
        Ordenaci&oacute;n </th>
      <td class="vtabla">1. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
		  <option value="cia_aguinaldos">Compa&ntilde;&iacute;a Mancomunada</option>
          <option value="catalogo_puestos.sueldo DESC, cod_puestos">Puesto</option>
          <option value="catalogo_turnos.orden_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select>
        <br>
        2. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
          <option value="" selected>-</option>
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
          <option value="catalogo_puestos.sueldo DESC, cod_puestos">Puesto</option>
          <option value="catalogo_turnos.orden_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select>
        <br>
        3. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
          <option value="" selected>-</option>
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
          <option value="catalogo_puestos.sueldo DESC, cod_puestos">Puesto</option>
          <option value="catalogo_turnos.orden_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select>
        <br>
        4. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
          <option value="" selected>-</option>
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
          <option value="catalogo_puestos.sueldo DESC, cod_puestos">Puesto</option>
          <option value="catalogo_turnos.orden_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">No incluir expendios<br>
        ni empleados personales </th>
      <td class="vtabla"><input name="no_exp" type="checkbox" id="no_exp" value="1"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Separar por<br />Tipo de listado</th>
      <td class="vtabla">
        <input name="tipo" type="radio" id="tipo_1" value="1">Todos<br />
        <input name="tipo" type="radio" id="tipo_2" value="2">Normales<br />
        <input name="tipo" type="radio" id="tipo_3" value="3">Con incidencias
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cias. no incluidas </th>
      <td class="vtabla"><input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[1].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[2].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[3].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[4].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[5].select()" size="3"><br>
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[6].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[7].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[8].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[9].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[10].select()" size="3"><br>
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[11].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[12].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[13].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[14].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[15].select()" size="3"><br>
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[16].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[17].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[18].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[19].select()" size="3">
	  <input name="cia[]" type="text" class="insert" id="cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cia[0].select()" size="3"></td>
    </tr>
  </table>  
          <p><strong><font face="Arial, Helvetica, sans-serif" size="-2">Nota: para sacar el listado final de aguinaldos, debe seleccionar los siguientes criterios de orden:<br> 
            1. Compa&ntilde;&iacute;as mancomunadas, 2. Puesto, 3. Turno, 4. N&uacute;mero de empleado</font> </strong></p>
          <p>
    <input type="button" class="boton" value="Generar Listado" onClick="validar_listado(this.form)">
</p>
  </form>
  <script language="javascript" type="text/javascript">
  function validar_listado(form) {
    if (document.getElementById('tipo_1').checked)
    {
      form.action = "fac_tra_lis_agu.php";
    }
    else if (document.getElementById('tipo_2').checked)
    {
      form.action = "fac_tra_lis_agu_normales.php";
    }
    else if (document.getElementById('tipo_3').checked)
    {
      form.action = "fac_tra_lis_agu_incidencias.php";
    }
		window.open("","listado","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768");
		form.target = "listado";
		form.submit();
	}

function minibus() {
	var win = window.open("fac_tra_minibus.php", "minibus","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
	win.focus();
}
	</script>
  </td>
    </tr>
	
  </table></td>
</tr>
</table>
<!-- END BLOCK : programa -->
</body>
</html>
