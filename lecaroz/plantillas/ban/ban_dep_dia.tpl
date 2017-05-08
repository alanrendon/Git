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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
 <p class="title">Listado de Dep&oacute;sitos por D&iacute;a</p>
 <table class="tabla">
   <tr>
     <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
     <th class="tabla" scope="col">Efectivo</th>
   </tr>
   <tr>
     <td class="tabla"><strong><font size="+1">{num_cia} - {nombre_cia} </font></strong></td>
     <td class="tabla"><strong><font size="+1">{efectivo} </font></strong></td>
   </tr>
 </table>
 <!-- START BLOCK : depositos -->
  <br>
 <table class="tabla">
   <tr>
     <th class="tabla" scope="col">&nbsp;</th>
     <th class="tabla" scope="col">Cuenta</th>
     <th class="tabla" scope="col">Concepto</th>
	 <th class="tabla" scope="col">Fecha Dep&oacute;sito </th>
     <th class="tabla" scope="col">Importe</th>
     <th class="tabla" scope="col">Fecha Conciliaci&oacute;n</th>
     <th class="tabla" scope="col">C&oacute;digo Movimiento </th>
   </tr>
   <!-- START BLOCK : fila -->
   <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
     <td class="tabla"><input type="button" class="boton" value="Mod" onClick="modificar({id})">
       <input type="button" class="boton" value="Div" onClick="dividir({id})"></td>
     <td class="tabla">{cuenta}</td>
     <td class="vtabla">{concepto}</td>
	  <td class="tabla">{fecha}</td>
     <td class="rtabla"><strong>{importe}</strong></td>
     <td class="tabla">{fecha_con}</td>
     <td class="vtabla">{cod_mov} {descripcion} </td>
   </tr>
   <!-- END BLOCK : fila -->
   <tr>
     <th colspan="4" class="rtabla">Total dep&oacute;sitos</th>
     <th class="rtabla">{total}</th>
     <th colspan="3" rowspan="2" class="tabla">&nbsp;</th>
     </tr>
   <tr>
     <th colspan="4" class="rtabla">Diferencia</th>
     <th class="rtabla">{diferencia}</th>
     </tr>
 </table>
 <!-- END BLOCK : depositos -->
 <!-- START BLOCK : no_depositos -->
 <p><strong><font color="#FF0000" face="Geneva, Arial, Helvetica, sans-serif">No hay dep&oacute;sitos para este d&iacute;a </font></strong></p>
 <script language="javascript" type="text/javascript">
 	window.onload = self.close();
 </script>
 <!-- END BLOCK : no_depositos -->
 <p>
   <input type="button" class="boton" value="Cerrar ventana" onClick="cerrar()"> 
 </p>
 </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function modificar(id) {
		window.open("./ban_dep_minimod.php?id="+id,"modificar","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=300");
	}
	
	function dividir(id) {
		window.open("ban_dep_div_v2.php?id=" + id + "&efe=1","dividir","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=640,height=480");
	}
	
	function cerrar() {
		window.opener.location = 'ban_con_dep_v2.php';
		self.close();
	}
</script>
</body>
</html>
