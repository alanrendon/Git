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
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Facturas</p>
  <form action="" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3"></td>
      </tr>
    <tr>
      <th class="vtabla">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1" {1}>ENERO</option>
        <option value="2" {2}>FEBRERO</option>
        <option value="3" {3}>MARZO</option>
        <option value="4" {4}>ABRIL</option>
        <option value="5" {5}>MAYO</option>
        <option value="6" {6}>JUNIO</option>
        <option value="7" {7}>JULIO</option>
        <option value="8" {8}>AGOSTO</option>
        <option value="9" {9}>SEPTIEMBRE</option>
        <option value="10" {10}>OCTUBRE</option>
        <option value="11" {11}>NOVIEMBRE</option>
        <option value="12" {12}>DICIEMBRE</option>
      </select></td>
      </tr>
    <tr>
      <th class="vtabla">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" value="{anio}" size="4" maxlength="4"></td>
      </tr>
    <tr>
      <th class="vtabla">D&iacute;a</th>
      <td class="vtabla"><input name="dia" type="text" class="insert" id="dia" value="1" size="2" maxlength="2"></td>
      </tr>
    <tr>
      <th class="vtabla">Solo alternativos </th>
      <td class="vtabla"><input name="alt" type="checkbox" id="alt" value="1">
        Si</td>
    </tr>
    <tr>
      <th class="vtabla">Tama&ntilde;o de la hoja </th>
      <td class="vtabla"><input name="tamano" type="radio" value="carta" checked>
        Carta&nbsp;&nbsp;
        <input name="tamano" type="radio" value="oficio">
        Oficio</td>
      </tr>
    <tr>
      <th class="vtabla">Orden</th>
      <td class="vtabla"><input name="orden" type="radio" value="0" checked>
        Ascendente
          <input name="orden" type="radio" value="1">
          Descendente</td>
    </tr>
  </table>  
  <p>
    <input name="Button" type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.anio.value <= 2000) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?")) {
				window.open("","facturas","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=800,height=600");
				
				form.action = "./ban_imp_fac_cia.php";
				form.target = "facturas";
				
				form.submit();
			}
			else
				form.num_cia.select();
	}
	
	window.onload = document.form.num_cia.select();
</script>
</body>
</html>
