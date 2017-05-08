<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">B&uacute;squeda de Cuentas</p>
  <form action="" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><input name="cuenta" type="text" class="vinsert" id="cuenta" onkeyup="buscar()" size="11" maxlength="11" />
        <input name="num_cia" type="hidden" id="num_cia" />
        <input name="clave_cuenta" type="hidden" id="clave_cuenta" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Banco</th>
      <td class="vtabla"><input name="banco" type="text" disabled="true" class="vnombre" id="banco" size="50" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="cia" type="text" disabled="true" class="vnombre" id="cia" size="50" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Contador</th>
      <td class="vtabla"><input name="contador" type="text" disabled="disabled" class="vnombre" id="contador" size="50" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Direcci&oacute;n</th>
      <td class="vtabla"><textarea name="dir" cols="50" rows="5" disabled="disabled" class="vnombre" id="dir"></textarea></td>
    </tr>
  </table>
    <br />
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
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
        </select>        </td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">A&ntilde;o</th>
        <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" value="{anio}" size="4" maxlength="4" /></td>
      </tr>
      <tr>
        <td colspan="2" class="tabla" scope="row"><input type="button" class="boton" value="Actualizar" onclick="actualizar()" /></td>
        </tr>
    </table>
    <p>
      <input type="button" class="boton" value="Listado" onclick="listado()" />
    </p>
  </form>  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cuenta = new Array();
<!-- START BLOCK : cuenta -->
cuenta['{cuenta}'] = new Array();
cuenta['{cuenta}'][0] = "{banco}";
cuenta['{cuenta}'][1] = "{num_cia} {nombre}";
cuenta['{cuenta}'][2] = {num_cia};
cuenta['{cuenta}'][3] = {clave_banco};
cuenta['{cuenta}'][4] = '{contador}';
cuenta['{cuenta}'][5] = '{dir}';
<!-- END BLOCK : cuenta -->

function buscar() {
	if (f.cuenta.value == '' || f.cuenta.value.length < 11) {
		f.banco.value = '';
		f.cia.value = '';
		f.num_cia.value = '';
		f.clave_cuenta.value = '';
		f.contador.value = '';
		f.dir.value = '';
		return false;
	}
	else if (cuenta[f.cuenta.value] != null) {
		f.banco.value = cuenta[f.cuenta.value][0];
		f.cia.value = cuenta[f.cuenta.value][1];
		f.num_cia.value = cuenta[f.cuenta.value][2];
		f.clave_cuenta.value = cuenta[f.cuenta.value][3];
		f.contador.value = cuenta[f.cuenta.value][4];
		f.dir.value = cuenta[f.cuenta.value][5];
	}
	else {
		f.banco.value = '';
		f.cia.value = '';
		f.num_cia.value = '';
		f.clave_cuenta.value = '';
		f.contador.value = '';
		f.dir.value = '';
		return false;
	}
}

function actualizar() {
	if (f.cuenta.value == '' || f.cuenta.value.length < 11 || f.banco.value == '')
		return false;
	
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	// Pedir datos
	myConn.connect("./ban_cue_bus.php", "GET", 'num_cia=' + get_val(f.num_cia) + '&cuenta=' + get_val(f.clave_cuenta) + '&mes=' + get_val(f.mes) + '&anio=' + get_val(f.anio), resultado);
}

var resultado = function (oXML) {
	var result = oXML.responseText;
	
	if (parseInt(result) == 0)
		alert('El estado de cuenta ya esta registrado en el sistema');
	else if (parseInt(result) == 1)
		alert('Se ha insertado registro del estado de cuenta');
}

function listado() {
	var win = window.open("carta_esc_conta.php","carta","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768");
	win.focus();
}

window.onload = f.cuenta.select();
//-->
</script>
</body>
</html>
