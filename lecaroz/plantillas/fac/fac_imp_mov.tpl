<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Carta de Alta/Baja del IMSS </p>
  <form action="./fac_imp_mov.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Altas</th>
      <td class="vtabla"><input name="tipo" type="radio" value="altas" checked></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Bajas</th>
      <td class="vtabla"><input name="tipo" type="radio" value="bajas"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Contador</th>
      <td class="vtabla"><select name="idcontador" class="insert" id="contador">
        <!-- START BLOCK : contador -->
		<option value="{idcontador}">{contador}</option>
		<!-- END BLOCK : contador -->
      </select></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		window.open("","aviso","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=800,height=600");
		form.target = "aviso";
		form.submit();
	}
</script>
</body>
</html>
