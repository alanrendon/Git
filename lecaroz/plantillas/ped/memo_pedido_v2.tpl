<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}

.tdlist {
	border: 1px solid Black;
	font-size: 10pt;
}

.tdspace {
	border-bottom: 1px solid Black;
}
-->
</style></head>

<body>
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	alert("No hay resultados");
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : memo -->
<!-- START BLOCK : memo_header -->
<table width="100%">
  <tr>
    <td width="10%" style="font-weight: bold;">{num_cia}</td>
    <td width="80%" align="center" style="font-size: 12pt; font-weight: bold;">{nombre_cia} <br>
PEDIDOS CORRESPONDIENTES AL MES DE {mes} DE {anio} </td>
    <td width="10%" align="right" style="font-weight: bold;">{fecha}</td>
  </tr>
</table>
<br>
<span style=" font-weight: bold;">SR. {encargado}
<br>
Presente</span><br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Se le manda la Lista de Pedido para que usted nos apunte sus requerimientos {texto}.<br>
<br>
<!-- END BLOCK : memo_header -->
<!-- START BLOCK : table -->
<table width="100%" >
  <tr valign="top">
    <td width="49%">
	<!-- START BLOCK : col_1 -->
	<table width="100%" style="border-collapse: collapse; border: 1px solid Black;">
      <tr>
        <th colspan="2" scope="col" style="border: 1px solid Black;">PRODUCTO</th>
        <th scope="col" style="border: 1px solid Black;">CANTIDAD</th>
        <th scope="col" style="border: 1px solid Black;">UNIDAD</th>
      </tr>
      <!-- START BLOCK : fila_1 -->
	  <tr>
        <td class="tdlist">{codmp}</td>
        <td class="tdlist">{nombre}</td>
        <td class="tdlist">&nbsp;</td>
        <td class="tdlist">{unidad}</td>
      </tr>
	  <!-- END BLOCK : fila_1 -->
    </table>
	<!-- END BLOCK : col_1 -->	</td>
    <td width="2%">&nbsp;</td>
    <td width="49%">
	<!-- START BLOCK : col_2 -->
	<table width="100%" style="border-collapse: collapse; border: 1px solid Black;">
      <tr>
        <th colspan="2" scope="col" style="border: 1px solid Black;">PRODUCTO</th>
        <th scope="col" style="border: 1px solid Black;">CANTIDAD</th>
        <th scope="col" style="border: 1px solid Black;">UNIDAD</th>
      </tr>
      <!-- START BLOCK : fila_2 -->
	  <tr>
        <td class="tdlist">{codmp}</td>
        <td class="tdlist">{nombre}</td>
        <td class="tdlist">&nbsp;</td>
        <td class="tdlist">{unidad}</td>
      </tr>
	  <!-- END BLOCK : fila_2 -->
    </table>
	<!-- END BLOCK : col_2 -->	</td>
  </tr>
</table>
{salto}
<!-- END BLOCK : table -->
<!-- START BLOCK : footer -->
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Se le recuerda que usted deber&aacute; conservar una copia para cualquier aclaraci&oacute;n. Verifique bien su pedido para que no le falte nada. <br>
<br>
<p style="font-weight:bold; text-align: center;">
_________________________________________<br> 
MIRIAM LORANCA PAZOS</p>
<span style="font-size: 6pt;">CCP. {admin}</span>
<!--<div align="right" style="font-size: 8pt; font-weight: bolder;">Ver al reverso</div>-->
<!--<br style="page-break-after:always;">-->
<!-- END BLOCK : footer -->
<!-- START BLOCK : reverso -->
<p>Por favor anotar en esta parte si usted tiene algun requerimiento de avio que no se encuentre en esta lista. Para que as&iacute; se le pueda hacer su pedido completo.</p>
<table width="100%" align="center" cellspacing="6">
  <tr>
    <th height="30" class="tdspace" scope="col">&nbsp;</th>
    <th class="tdspace" scope="col">&nbsp;</th>
    <th width="5%" scope="col">&nbsp;</th>
    <th class="tdspace" scope="col">&nbsp;</th>
    <th class="tdspace" scope="col">&nbsp;</th>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
    <td class="tdspace">&nbsp;</td>
  </tr>
</table>
<!-- END BLOCK : reverso -->
{salto}
<!-- END BLOCK : memo -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	alert("No hay resultados");
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
