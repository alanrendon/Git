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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Trabajadores</p>
  <form action="./fac_tra_con3.php" method="get" name="form">
  <input name="temp" type="hidden">
  <input name="buscar" type="hidden" value="1">
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
          <option value="" selected>-</option>
          <!-- START BLOCK : cod_turno -->
		  <option value="{cod_turno}">{turno}</option>
		  <!-- END BLOCK : cod_turno -->
        </select>
        <br>
        Por puesto 
        <select name="puesto" class="insert" id="puesto">
          <option value="" selected>-</option>
          <!-- START BLOCK : cod_puesto -->
		  <option value="{cod_puesto}">{puesto}</option>
		  <!-- END BLOCK : cod_puesto -->
        </select></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" onClick="valida_registro(form)" value="Siguiente"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function inicio() {

		{mensaje}
		document.form.num_cia.select();
	}
	
	function valida_registro(form) {
		/*if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else*/
			form.submit();
	}
	
	window.onload = inicio();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : hoja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title" title="title">Consulta de Trabajadores </p>
  <form action="./fac_tra_con2.php" method="post" name="form">
  <input name="temp" type="hidden">
  <input name="numfilas" type="hidden" value="{numfilas}">
  <table align="center" class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="7" class="vtabla"><font size="+1">{num_cia} &#8212; {nombre_cia}</font></th>
    </tr>
	<tr>
      <th class="tabla" scope="col">No.</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Puesto</th>
      <th class="tabla" scope="col">Turno</th>
      <th class="tabla" scope="col">No. Afiliaci&oacute;n </th>
      <th class="tabla" scope="col">Antig&uuml;edad</th>
      <th class="tabla" scope="col">Status</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" onClick="consultar({id})">
      <td class="tabla">
      {num_emp}</td>
      <td class="vtabla">{nombre}</td>
      <td class="tabla">{puesto}</td>
      <td class="tabla">{turno}</td>
      <td class="tabla">{num_afiliacion}</td>
      <td class="tabla">{fecha_alta}</td>
      <td class="tabla"><strong>{status}</strong></td>
	</tr>
	<!-- END BLOCK : fila -->

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
    <input type="button" class="boton" value="Regresar" onClick="document.location = './fac_tra_con3.php'">
  </p>
  </form>
 
 </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function consultar(id) {
		var con = window.open("fac_tra_minicon.php?id=" + id,"","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=600");
		con.focus();
	}
</script>
  <!-- END BLOCK : hoja -->
</body>
</html>
