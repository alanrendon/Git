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
<td align="center" valign="middle"><p class="title">Listado Anualizado de Expendios </p>
  <form action="./bal_exp_anu.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr id="cia_row">
      <th class="vtabla" scope="row">Compa&ntilde;ia</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr id="admin_row">
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected>-</option>
        <option value="-1">TODOS LOS ADMIN.</option>
        <!-- START BLOCK : admin -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Campo</th>
      <td class="vtabla"><input name="campo" type="radio" value="rezago_anterior" checked>
        Rezago Inicial <br>
        <input name="campo" type="radio" value="pan_p_venta">
        Pan para Venta<br>
        <input name="campo" type="radio" value="pan_p_expendio">
        Pan para Expendio<br>
        <input name="campo" type="radio" value="abono">
        Abono<br>
        <input name="campo" type="radio" value="devolucion"> 
        Devolucion<br>
        <input name="campo" type="radio" value="rezago"> 
        Rezago Final  </td>
    </tr>
	
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Expendios</th>
      <td class="vtabla">        <input name="exp" type="checkbox" id="exp" value="1">
        Si</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.anio.value < 2005) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" align="center" class="print_encabezado">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td width="20%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="print_encabezado">Listado de Expendios Anualizados del {anio}<br>
      <span style="font-size: 12pt;">{campo}</span></td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Ene</th>
      <th class="print" scope="col">Feb</th>
      <th class="print" scope="col">Mar</th>
      <th class="print" scope="col">Abr</th>
      <th class="print" scope="col">May</th>
      <th class="print" scope="col">Jun</th>
      <th class="print" scope="col">Jul</th>
      <th class="print" scope="col">Ago</th>
      <th class="print" scope="col">Sep</th>
      <th class="print" scope="col">Oct</th>
      <th class="print" scope="col">Nov</th>
      <th class="print" scope="col">Dic</th>
      <th class="print" scope="col">Total</th>
      <th class="print" scope="col">Promedio</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rprint">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint">{1}</td>
      <td class="rprint">{2}</td>
      <td class="rprint">{3}</td>
      <td class="rprint">{4}</td>
      <td class="rprint">{5}</td>
      <td class="rprint">{6}</td>
      <td class="rprint">{7}</td>
      <td class="rprint">{8}</td>
      <td class="rprint">{9}</td>
      <td class="rprint">{10}</td>
      <td class="rprint">{11}</td>
      <td class="rprint">{12}</td>
      <td class="rprint"><strong>{total}</strong></td>
      <td class="rprint"><strong>{prom}</strong></td>
    </tr>
	<!-- END BLOCK : fila -->
	<tr>
	  <th colspan="2" class="rprint"> <strong> <font size="2">Totales</font></strong></th>
      <th class="rprint_total"><strong>{t1}</strong></th>
      <th class="rprint_total"><strong>{t2}</strong></th>
      <th class="rprint_total"><strong>{t3}</strong></th>
      <th class="rprint_total"><strong>{t4}</strong></th>
      <th class="rprint_total"><strong>{t5}</strong></th>
      <th class="rprint_total"><strong>{t6}</strong></th>
      <th class="rprint_total"><strong>{t7}</strong></th>
      <th class="rprint_total"><strong>{t8}</strong></th>
      <th class="rprint_total"><strong>{t9}</strong></th>
      <th class="rprint_total"><strong>{t10}</strong></th>
      <th class="rprint_total"><strong>{t11}</strong></th>
      <th class="rprint_total"><strong>{t12}</strong></th>
	  <th class="rprint_total">{total}</th>
	  <th class="rprint_total">{prom}</th>
	</tr>	
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : listado -->
<!-- START BLOCK : expendios -->
<table width="100%">
  <tr>
    <td width="20%" class="print_encabezado">Cia: {num_cia}</td>
    <td width="60%" align="center" class="print_encabezado">{nombre}</td>
    <td width="20%" class="rprint_encabezado">Cia: {num_cia}</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="print_encabezado">Listado de Expendios Anualizados del {anio}<br>
      <span style="font-size: 12pt;">{campo}</span></td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th class="print" scope="col">Expendio</th>
      <th class="print" scope="col">Ene</th>
      <th class="print" scope="col">Feb</th>
      <th class="print" scope="col">Mar</th>
      <th class="print" scope="col">Abr</th>
      <th class="print" scope="col">May</th>
      <th class="print" scope="col">Jun</th>
      <th class="print" scope="col">Jul</th>
      <th class="print" scope="col">Ago</th>
      <th class="print" scope="col">Sep</th>
      <th class="print" scope="col">Oct</th>
      <th class="print" scope="col">Nov</th>
      <th class="print" scope="col">Dic</th>
      <th class="print" scope="col">Total</th>
      <th class="print" scope="col">Promedio</th>
    </tr>
    <!-- START BLOCK : exp -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint">{exp}</td>
      <td class="rprint">{1}</td>
      <td class="rprint">{2}</td>
      <td class="rprint">{3}</td>
      <td class="rprint">{4}</td>
      <td class="rprint">{5}</td>
      <td class="rprint">{6}</td>
      <td class="rprint">{7}</td>
      <td class="rprint">{8}</td>
      <td class="rprint">{9}</td>
      <td class="rprint">{10}</td>
      <td class="rprint">{11}</td>
      <td class="rprint">{12}</td>
      <td class="rprint">{total}</td>
      <td class="rprint">{prom}</td>
    </tr>
	<!-- END BLOCK : exp -->
    <tr>
      <th class="rprint">Totales</th>
      <th class="rprint_total">{1}</th>
      <th class="rprint_total">{2}</th>
      <th class="rprint_total">{3}</th>
      <th class="rprint_total">{4}</th>
      <th class="rprint_total">{5}</th>
      <th class="rprint_total">{6}</th>
      <th class="rprint_total">{7}</th>
      <th class="rprint_total">{8}</th>
      <th class="rprint_total">{9}</th>
      <th class="rprint_total">{10}</th>
      <th class="rprint_total">{11}</th>
      <th class="rprint_total">{12}</th>
      <th class="rprint_total">{total}</th>
      <th class="rprint_total">{prom}</th>
    </tr>
  </table>
  {salto}
<!-- END BLOCK : expendios -->
</body>
</html>
