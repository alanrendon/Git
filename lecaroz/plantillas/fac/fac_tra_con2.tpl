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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Trabajadores (Administrador) </p>
  <form action="./fac_tra_con2.php" method="get" name="form">
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
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)" {disabled}> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function inicio() {
		{mensaje}
		document.form.num_cia.select();
	}
	
	function valida_registro(form) {
		if (form.tipo[0].checked == true) {
			if (form.num_cia.value <= 0) {
				alert("Debe especificar la compañía");
				form.num_cia.select();
				return false;
			}
			else {
				form.target = "mainFrame";
				form.submit();
			}
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
  <form action="./fac_tra_con2.php" method="post" name="form">
  <input name="temp" type="hidden">
  <input name="numfilas" type="hidden" value="{numfilas}">
  <table align="center" class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="10" class="vtabla"><font size="+1">{num_cia} &#8212; {nombre_cia}</font></th>
    </tr>
	<tr>
      <th class="tabla" scope="col">No.</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Puesto</th>
      <th class="tabla" scope="col">Turno</th>
      <th class="tabla" scope="col">No. Afiliaci&oacute;n </th>
      <th class="tabla" scope="col">Status</th>
      <th class="tabla" scope="col">Antig&uuml;edad</th>
      <th class="tabla" scope="col">Aguinaldo {anio_ant} </th>
      <th class="tabla" scope="col">Aguinaldo {anio_act} </th>
      <th class="tabla" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id{i}" type="hidden" id="id{i}" value="{id}">
      {num_emp}</td>
      <td class="vtabla">{nombre}</td>
      <td class="tabla"><select name="cod_puestos{i}" class="insert" id="cod_puestos{i}">
        <!-- START BLOCK : puesto -->
		<option value="{id}"{selected}>{nombre}</option>
		<!-- END BLOCK : puesto -->
      </select></td>
      <td class="tabla"><select name="cod_turno{i}" class="insert" id="cod_turno{i}">
        <!-- START BLOCK : turno -->
		<option value="{id}"{selected}>{nombre}</option>
		<!-- END BLOCK : turno -->
      </select></td>
      <td class="tabla">{num_afiliacion}</td>
      <td class="tabla">{status}</td>
      <td class="tabla"><input name="fecha_alta{i}" type="text" class="insert" id="fecha_alta{i}" value="{fecha_alta}" size="10" maxlength="10"></td>
      <td class="rtabla">{ultimo_aguinaldo}</td>
      <td class="tabla"><input name="nuevo_aguinaldo{i}" type="text" class="rinsert" id="nuevo_aguinaldo{i}" value="{nuevo_aguinaldo}" size="10" maxlength="10"></td>
      <td class="rtabla"><input type="button" class="boton" value="Baja" onClick="eliminar({id},{imss},{inf},{pre})" {disabled}></td>
	</tr>
	<!-- END BLOCK : fila -->
		<tr>
	  <th colspan="7" class="rtabla">Total</th>
	  <th class="rtabla">{ultimo_aguinaldo}</th>
	  <th class="rtabla">{nuevo_aguinaldo}</th>
	  <th class="rtabla">&nbsp;</th>
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
    <input type="button" class="boton" value="Regresar" onClick="document.location = './fac_tra_con2.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="valida_registro(form)">
  </p></form>
 
 </td>
</tr>
</table>
  <script language="javascript" type="text/javascript">
  	function valida_registro(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			return false;
	}
	
	function eliminar(id, imss, inf, pre) {
		if (imss == true)
			if (confirm("El empleado tiene seguro. ¿Desea darlo de baja?"))
				window.open("./fac_tra_con2.php?id="+id+"&baja=1","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1,height=1");
			else
				return false;
		else if (inf == true)
			if (confirm("El empleado tiene crédito infonavit. ¿Desea darlo de baja?"))
				window.open("./fac_tra_con2.php?id="+id,"","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1,height=1");
			else
				return false;
		else if (pre == true) {
			alert("El empleado tiene un prestamo, no puede ser dado de baja.")
			return false;
		}
		else
			if (confirm("¿Desea dar de baja al empleado?"))
				window.open("./fac_tra_con2.php?id="+id,"","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1,height=1");
			else
				return false;
	}
  </script>
  <!-- END BLOCK : hoja -->
</body>
</html>
