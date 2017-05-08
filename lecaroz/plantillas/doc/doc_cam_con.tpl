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
<td align="center" valign="middle"><p class="title">Consulta de Camionetas</p>
  <form action="./doc_cam_con.php" method="get" name="form" onKeyDown="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) idcamioneta.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected></option>
        <!-- START BLOCK : admin -->
		  <option value="{id}">{admin}</option>
		  <!-- END BLOCK : admin -->
      </select>      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Camioneta</th>
      <td class="vtabla"><input name="idcamioneta" type="text" class="insert" id="idcamioneta" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Entidad</th>
      <td class="vtabla"><select name="entidad" class="insert" id="entidad">
        <option selected>TODAS</option>
        <option value="1">DISTRITO FEDERAL</option>
        <option value="2">ESTADO</option>
        <option value="3">OTRO</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Verificaci&oacute;n</th>
      <td class="vtabla"><select name="color" class="insert" id="color">
        <option selected>TODAS</option>
        <option value="1">AMARILLO</option>
        <option value="2">ROSA</option>
        <option value="3">ROJO</option>
        <option value="4">VERDE</option>
        <option value="5">AZUL</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Estatus</th>
      <td class="vtabla"><select name="estatus" class="insert" id="estatus">
        <option>TODAS</option>
        <option value="1" selected>EN USO</option>
        <option value="2">VENDIDA</option>
        <option value="3">ROBADA</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de veh&iacute;culo </th>
      <td class="vtabla"><select name="tipo_unidad" class="insert" id="tipo_unidad">
        <option value="" selected></option>
        <option value="1">CARGA</option>
        <option value="2">PERSONAL</option>
		<option value="3">PARTICULAR</option>
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Orden</th>
      <td class="vtabla"><input name="orden" type="radio" value="idcamioneta" checked>
        N&uacute;mero<br>
        <input name="orden" type="radio" value="modelo">
        Modelo<br>
        <input name="orden" type="radio" value="anio">
        A&ntilde;o<br>
        <input name="orden" type="radio" value="placas">
        Placas<br>
        <input name="orden" type="radio" value="num_cia">
        Compa&ntilde;&iacute;a<br>
        <input name="orden" type="radio" value="propietario">
        Propietario<br>
        <input name="orden" type="radio" value="usuario">
        Usuario<br>
        <input name="orden" type="radio" value="num_serie">
        Serie<br>
        <input name="orden" type="radio" value="num_motor">
        Motor<br>
        <input name="orden" type="radio" value="num_poliza">
        P&oacute;liza<br>
        <input name="orden" type="radio" value="ven_poliza">
        P&oacute;liza vencida</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		form.submit();
	}
	
	window.onload=document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Camionetas</p>
  <table class="tabla">
    <!-- START BLOCK : result -->
	<tr>
      <th class="tabla" scope="col">No.</th>
      <th class="tabla" scope="col">Modelo</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
      <th class="tabla" scope="col">Placas</th>
      <th class="tabla" scope="col">Color</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Propietario</th>
      <th class="tabla" scope="col">Usuario</th>
      <th class="tabla" scope="col">No. Serie </th>
      <th class="tabla" scope="col">No. Motor </th>
      <th class="tabla" scope="col">Poliza</th>
      <th class="tabla" scope="col">Vencimiento</th>
      <th class="tabla" scope="col">Detalles</th>
      <th class="tabla" scope="col">Acci&oacute;n</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{id}</td>
      <td class="vtabla">{modelo}</td>
      <td class="tabla">{anio}</td>
      <td class="tabla"><strong>{placas}</strong></td>
      <td class="tabla" bgcolor="#{color}">&nbsp;</td>
      <td class="vtabla">{num_cia} - {nombre_cia} </td>
      <td class="vtabla">{propietario}</td>
      <td class="vtabla">{usuario}</td>
      <td class="tabla">{num_serie}</td>
      <td class="tabla">{num_motor}</td>
      <td class="tabla">{num_poliza}</td>
      <td class="tabla">{ven_poliza}</td>
      <td class="tabla"><input type="button" class="boton" value="..." onClick="detalle({id})"></td>
      <td class="tabla"><input type="button" class="boton" value="Modificar" onClick="modificar({id})" {disabled}>
        <input type="button" class="boton" value="Eliminar" onClick="borrar({id})" {disabled}></td>
    </tr>
	<!-- END BLOCK : fila -->
	<!-- END BLOCK : result -->
	<!-- START BLOCK : no_result -->
	<tr>
	  <th class="tabla">No hay resultados</th>
	</tr>
	<!-- END BLOCK : no_result -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./doc_cam_con.php'">
&nbsp;&nbsp;
<input type="button" class="boton" value="Versión Imprimible" onClick="imprimir('{query_string}')"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Archivo de Excel" onClick="archivo('{query_string}')" {disabled}>
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function imprimir(query) {
		var url = "./listado_cam.php?" + query;
		var ventana = window.open(url,"detalle","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=1024,height=768");
		ventana.focus();
	}
	
	function archivo(query) {
		var url = "./archivo_cam.php?" + query;
		document.location = url;
	}
	
	function detalle(id) {
		var url = "./doc_cam_det.php?id=" + id;
		var ventana = window.open(url,"detalle","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
		ventana.focus();
	}
	
	function modificar(id) {
		var url = "./doc_cam_mod.php?id=" + id;
		var ventana = window.open(url,"modificar","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1024,height=768");
		ventana.focus();
	}
	
	function borrar(id) {
		var url = "./doc_cam_del.php?id=" + id;
		var ventana = window.open(url,"borrar","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=200");
		ventana.focus();
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
