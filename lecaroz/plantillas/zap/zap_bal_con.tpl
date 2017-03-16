<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : password -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Balances </p>
  <form action="./zap_bal_con.php" method="post" name="form"><p>
    <input name="password" type="password" class="vinsert" id="password" />
  </p>
  <p>
    <input name="Enviar" type="submit" class="boton" value="Siguiente" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
window.onload = document.form.password.select();
</script>
<!-- END BLOCK : password -->
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Balances</p>
  <form action="./bal_esr_zap.php" method="get" name="form" target="_blank">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="compania" type="text" class="insert" id="compania" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) anio.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onclick="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) compania.select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->

function cambiaCia() {
	if (f.compania.value == '' || f.compania.value == '0') {
		f.compania.value = '';
		f.nombre.value = '';
	}
	else if (cia[get_val(f.compania)] != null)
		f.nombre.value = cia[get_val(f.compania)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.compania.value = f.tmp.value;
		f.compania.select();
	}
}

function validar() {
	if (get_val(f.compania) > 0 && get_val(f.compania) < 900) {
		alert('No se puede generar balance de la compañía solicitada');
		f.compania.select();
	}
	else if (get_val(f.anio) < 2005) {
		alert('Debe especificar el año de consulta');
		f.anio.select();
	}
	else {
		//var win = window.open("","","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768");
		f.submit();
		//win.focus();
	}
}

window.onload = f.compania.select();
//-->
</script>
<!-- END BLOCK : datos -->
</body>
</html>
