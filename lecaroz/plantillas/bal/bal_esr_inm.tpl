<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Balances de Inmobiliarias </p>
  <form action="" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a(s)</th>
      <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[1].select();
else if (event.keyCode == 40) anyo.select();" size="3" maxlength="3" />
        <input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[2].select();
else if (event.keyCode == 40) anyo.select();" size="3" maxlength="3" />
		<input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[3].select();
else if (event.keyCode == 40) anyo.select();" size="3" maxlength="3" />
		<input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[4].select();
else if (event.keyCode == 40) anyo.select();" size="3" maxlength="3" />
		<input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[5].select();
else if (event.keyCode == 40) anyo.select();" size="3" maxlength="3" />
		<input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[6].select();
else if (event.keyCode == 40) anyo.select();" size="3" maxlength="3" />
		<input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[7].select();
else if (event.keyCode == 40) anyo.select();" size="3" maxlength="3" />
		<input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[8].select();
else if (event.keyCode == 40) anyo.select();" size="3" maxlength="3" />
		<input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[9].select();
else if (event.keyCode == 40) anyo.select();" size="3" maxlength="3" />
		<input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) anyo.select();
else if (event.keyCode == 40) anyo.select();" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected="selected"></option>
        <!-- START BLOCK : admin -->
        <option value="{id}">{nombre}</option>
        <!-- END BLOCK : admin -->
      </select>
</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anyo" type="text" class="insert" id="anyo" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[0].select()" value="{anyo}" size="4" maxlength="4" /></td>
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
      </select>      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Ingresos extraordinarios </th>
      <td class="vtabla"><input name="no_ing" type="checkbox" id="no_ing" value="1" />
        No incluir en balance </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Estatus</th>
      <td class="vtabla" id="status">&nbsp;</td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Generar/Actualizar" onclick="generar()" />
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Imprimir" onclick="imprimir()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function generar() {
	if (get_val(f.anyo) <= 2000) {
		alert('Debe especificar el año');
		f.anyo.select();
		return false;
	}
	
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	var opt = 'anyo=' + get_val(f.anyo) + '&mes=' + get_val(f.mes);
	for (var i = 0; i < f.num_cia.length; i++)
		opt += '&num_cia[]=' + f.num_cia[i].value;
	
	document.getElementById('status').innerHTML = '<img src="./imagenes/loading.gif" width="40" height="40" align="middle" /> Generando/Actualizando...';
	myConn.connect("./generar_balance_inm.php", "GET", opt, Status);
}

var Status = function(oXML) {
	document.getElementById('status').innerHTML = oXML.responseText;
}

function imprimir() {
	var opt = 'anyo=' + get_val(f.anyo) + '&mes=' + get_val(f.mes);
	opt += '&idadmin=' + get_val(f.idadmin);
	opt += f.no_ing.checked ? '&no_ing=1' : '';
	for (var i = 0; i < f.num_cia.length; i++)
		opt += '&num_cia[]=' + f.num_cia[i].value;
	
	var win = window.open('balance_inm.php?' + opt, 'balances_ros','toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
	win.focus();
}

window.onload = f.num_cia[0].select();
//-->
</script>
</body>
</html>
