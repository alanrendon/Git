<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : reload -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : reload -->
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Trabajadores (Administrador) </p>
  <form action="./fac_tra_con2_v2.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th colspan="4" class="tabla">Criterios de b&uacute;squeda </th>
      </tr>
    <tr>
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td colspan="2" class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_emp.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla">N&uacute;mero de Empleado </th>
      <td colspan="2" class="vtabla"><input name="num_emp" type="text" class="insert" id="num_emp" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) ap_paterno.select();
else if (event.keyCode == 38) num_cia.select();" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla">Nombre</th>
      <td colspan="2" class="vtabla">Apellido Paterno: 
        <input name="ap_paterno" type="text" class="vinsert" id="ap_paterno" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) ap_materno.select();
else if (event.keyCode == 38) num_emp.select();" size="20" maxlength="20">
        &nbsp;&nbsp;&nbsp;Apellido Materno: 
        <input name="ap_materno" type="text" class="vinsert" id="ap_materno" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) nombre.select();
else if (event.keyCode == 37) ap_paterno.select();
else if (event.keyCode == 38) num_emp.select();" size="20" maxlength="20">
        &nbsp;&nbsp;&nbsp;Nombre(s): 
        <input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia.select();
else if (event.keyCode == 37) ap_materno.select();
else if (event.keyCode == 38) num_emp.select();" size="20" maxlength="20"></td>
    </tr>
    <tr>
      <th class="vtabla">Proceso</th>
      <td colspan="2" class="vtabla"><input name="tipo" type="radio" value="mod" checked>
        Modificaci&oacute;n&nbsp;&nbsp;&nbsp;
        <input name="tipo" type="radio" value="lis">
        Listado</td>
    </tr>
    <tr>
      <th class="vtabla">Estatus</th>
      
      <td class="vtabla"><input name="filtro" type="radio" value="todos" checked>        
        Todos<br>        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="bajas" type="checkbox" id="bajas" value="TRUE">        
        Incluir bajas <br>
        <input name="filtro" type="radio" id="solo_aguinaldo" value="solo_aguinaldo">
          Solo Aguinaldo<br>
          <input name="filtro" type="radio" id="imss" value="imss">
          Afiliados al IMSS<br>
          <input name="filtro" type="radio" id="no_imss" value="no_imss">
          No Afiliados <br>
          <input name="filtro" type="radio" value="pen_altas">
          Pendientes de alta<br>
          <input name="filtro" type="radio" value="pen_bajas"> 
          Pendientes de baja  </td>
      <td class="vtabla">Por turno 
        <select name="turno" class="insert" id="turno">
          <option selected>-</option>
          <!-- START BLOCK : cod_turno -->
		  <option value="{cod_turno}">{turno}</option>
		  <!-- END BLOCK : cod_turno -->
        </select>
        <br>
        Por puesto 
        <select name="puesto" class="insert" id="puesto">
          <option selected>-</option>
          <!-- START BLOCK : cod_puesto -->
		  <option value="{cod_puesto}">{puesto}</option>
		  <!-- END BLOCK : cod_puesto -->
        </select></td>
    </tr>
    <tr>
      <th class="vtabla">Orden</th>
      <td colspan="2" class="vtabla">1. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
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
  </table>  
  <p>
   <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)" {disabled}>
  </p>
  <p>
      <input type="button" class="boton" value="Listado Simple" onClick="listado_simple(form)">
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function listado_simple(form) {
		var win = window.open("./fac_tra_lis_sim.php?num_cia=" + form.num_cia.value, "listado_simple", "toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=1024,height=768");
		win.focus();
	}
	
	function inicio() {
		{mensaje}
		document.form.num_cia.select();
	}
	
	function valida_registro(form) {
		if (form.tipo[0].checked == true) {
			/*if (form.num_cia.value <= 0) {
				alert("Debe especificar la compañía");
				form.num_cia.select();
				return false;
			}
			else {*/
				form.target = "_self";
				form.submit();
			//}
		}
		else if (form.tipo[1].checked == true) {
			/*if (form.num_cia.value <= 0 && form.num_emp.value <= 0 && form.nombre.value == "" && form.ap_paterno.value == "" && form.ap_materno.value == "") {
				alert("Debe especificar al menos un criterio de búsqueda");
				form.num_cia.select();
				return false;
			}
			else {*/
				window.open("","tra_lis","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=1024,height=768");
				form.target = "tra_lis";
				form.submit();
			//}
		}
	}
	
	window.onload = inicio();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : hoja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title" title="title">Listado de Trabajadores </p>
  <form action="./fac_tra_con2_v2.php" method="post" name="form">
  <input name="temp" type="hidden">
  <input name="numfilas" type="hidden" value="{numfilas}">
  <table align="center" class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="12" class="vtabla"><font size="+1">{num_cia} &#8212; {nombre_cia}</font></th>
      </tr>
	<tr>
      <th class="tabla" scope="col">No.</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Puesto</th>
      <th class="tabla" scope="col">Turno</th>
      <th class="tabla" scope="col">No. Afiliaci&oacute;n </th>
      <th class="tabla" scope="col">Status</th>
      <th class="tabla" scope="col">Aguinaldo</th>
      <th class="tabla" scope="col">Tipo</th>
      <th class="tabla" scope="col">Antig&uuml;edad</th>
      <th class="tabla" scope="col">Aguinaldo {anio_ant} </th>
      <th class="tabla" scope="col">Aguinaldo {anio_act} </th>
      <th class="rtabla" scope="col"><input type="button" class="boton" value="Alta" onClick="alta({num_cia})"></th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'{bgcolor}');" bgcolor="{bgcolor}">
      <td class="tabla"><input name="id[]" type="hidden" id="id[]" value="{id}">
      <a href="javascript:obs({id})" style="text-decoration:none; color:#000000;">{num_emp}</a></td>
      <td class="vtabla"><input name="nombre[]" type="text" class="vnombre" id="nombre" onClick="modifica_nombre({i},{id})" value="{nombre}" size="25" readonly="true"{otra_cia_color}></td>
      <td class="tabla"><input name="puesto[]" type="text" class="vnombre" id="puesto" onClick="modifica_puesto({i},{id})" value="{puesto}" size="20" readonly="true"></td>
      <td class="tabla"><input name="turno[]" type="text" class="vnombre" id="turno" onClick="modifica_turno({i},{id})" value="{turno}" size="20" readonly="true"></td>
      <td class="tabla">{num_afiliacion}</td>
      <td class="tabla">{status}</td>
      <td class="tabla"><input name="ag[]" type="text" class="nombre" id="ag" onClick="modifica_ag({i},{id})" value="{ag}" size="2" readonly="true"></td>
      <td class="tabla"><input name="tipo[]" type="text" class="nombre" id="tipo" onClick="cambiaTipo({i},{id})" value="{tipo}" size="2" readonly="true"></td>
      <td class="vtabla"><input name="antiguedad[]" type="text" class="vnombre" id="antiguedad" onClick="modifica_antiguedad({i},{id})" value="{antiguedad}" size="8" maxlength="8" readonly="true"></td>
      <td class="rtabla"><input name="aguinaldo_ant[]" type="text" class="rnombre" id="aguinaldo_ant" onClick="/*if (this.value == '')*/ agrega_aguinaldo_ant({i},{id})" value="{ultimo_aguinaldo}" size="10" maxlength="10" readonly="true"></td>
      <td class="rtabla"><input name="idaguinaldo[]" type="hidden" id="idaguinaldo" value="{idaguinaldo}">
      <input name="aguinaldo[]" type="text" class="rnombre" id="aguinaldo" onClick="if (idaguinaldo.length == undefined) modifica_aguinaldo({i},idaguinaldo.value,{id},{anio_act}); else modifica_aguinaldo({i},idaguinaldo[{i}].value,{id},{anio_act});" value="{aguinaldo}" size="10" maxlength="10" readonly="true"></td>
      <td class="rtabla"><input type="button" class="boton" value="Baja" onClick="eliminar({id},{imss},{inf},{pre})" {disabled}><input type="button" class="boton" value="Info" onClick="info({id})" {disabled}></td>
	</tr>
	<!-- END BLOCK : fila -->
		<tr>
	  <th colspan="9" class="rtabla">Total</th>
	  <th class="rtabla">{ultimo_aguinaldo}</th>
	  <th class="rtabla"><input name="total_aguinaldo" type="text" class="rnombre" id="total_aguinaldo" value="{total_aguinaldo}" size="12" readonly="true"></th>
	  <th class="rtabla"><input type="button" class="boton" value="Alta" onClick="alta({num_cia})"></th>
	  </tr>

	<!-- END BLOCK : cia -->
</table>
<br>
<table class="tabla">
<tr>
<th class="tabla">
TOTAL DE EMPLEADOS: {numfilas}
</th>
</tr>
</table>
  <p align="center">
    <input type="button" class="boton" value="Regresar" onClick="document.location = './fac_tra_con2_v2.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Calculadora de Aguinaldos" onClick="calculadora()"> 
</p>
  </form>
 
 </td>
</tr>
</table>
  <script language="javascript" type="text/javascript">
	function calculadora() {
		var ventana = window.open("./fac_cal_agu.php","calculadora","left=212,top=284,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=600,height=200");
		ventana.focus();
	}
	
	function obs(id) {
		var win = window.open("./fac_tra_obs.php?id=" + id,"obs","left=212,top=284,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=600,height=300");
		win.focus();
	}
	
	function modifica_nombre(i, id) {
		var ventana = window.open("fac_tra_mod_nom.php?id=" + id + "&i=" + i,"mod","left=234,top=312,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=300");
		ventana.focus();
	}
	
	function modifica_puesto(i, id) {
		var ventana = window.open("fac_tra_mod_pue.php?id=" + id + "&i=" + i,"mod","left=234,top=312,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=300");
		ventana.focus();
	}
	
	function modifica_turno(i, id) {
		var ventana = window.open("fac_tra_mod_tur.php?id=" + id + "&i=" + i,"mod","left=234,top=312,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=300");
		ventana.focus();
	}
	
	function modifica_antiguedad(i, id) {
		var ventana = window.open("fac_tra_mod_ant.php?id=" + id + "&i=" + i,"mod","left=234,top=312,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=300");
		ventana.focus();
	}
	
	function modifica_aguinaldo(i, idaguinaldo, id, anio) {
		var ventana = window.open("fac_tra_mod_agu.php?idaguinaldo=" + idaguinaldo + "&i=" + i + "&id=" + id + "&anio=" + anio,"mod","left=234,top=312,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400,height=300");
		ventana.focus();
	}
	
	function agrega_aguinaldo_ant(i, id) {
		var ventana = window.open("fac_tra_mod_agu_ant.php?i=" + i + "&id=" + id,"mod","left=184,top=312,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=500,height=300");
		ventana.focus();
	}
	
	function alta(num_cia) {
		var ventana = window.open("fac_tra_altas.php?num_cia=" + num_cia + "&admin=1","alta","left=0,top=.0,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1024,height=768");
		ventana.focus();
	}
	
	function modifica_ag(i, id) {
		var ventana = window.open("fac_tra_mod_ag.php?i=" + i + "&id=" + id,"ag","left=184,top=312,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=500,height=300");
		ventana.focus();
	}
	
	function eliminar(id, imss, inf, pre) {
		if (imss == true)
			if (confirm("El empleado tiene seguro. ¿Desea darlo de baja?"))
				window.open("./fac_tra_con2_v2.php?id="+id+"&baja=1","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1,height=1");
			else
				return false;
		else if (inf == true)
			if (confirm("El empleado tiene crédito infonavit. ¿Desea darlo de baja?"))
				window.open("./fac_tra_con2_v2.php?id="+id,"","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1,height=1");
			else
				return false;
		else if (pre == true) {
			alert("El empleado tiene un prestamo, no puede ser dado de baja.")
			return false;
		}
		else
			if (confirm("¿Desea dar de baja al empleado?"))
				window.open("./fac_tra_con2_v2.php?id="+id,"","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1,height=1");
			else
				return false;
	}
	
	function info(id) {
		var ventana = window.open('fac_tra_info.php?id=' + id, 'info', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600');
		ventana.focus();
	}
	
	function cambiaTipo(i, id) {
		var ventana = window.open("fac_tra_mod_tipo.php?i=" + i + "&id=" + id, "tipo","left=184,top=312,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=500,height=300");
		ventana.focus();
	}
  </script>
  <!-- END BLOCK : hoja -->
</body>
</html>
