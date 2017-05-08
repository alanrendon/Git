<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cartas</p>
  <form action="./ban_car_fol.php" method="post" name="form" target="carta">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp))cambiaCia()" onkeyup="if (event.keyCode==13)atencion.select()" size="3" />
        <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" size="80" readonly="true" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Dirig&iacute;da a</th>
      <td class="vtabla"><input name="atencion" type="text" class="vinsert" id="atencion" style="width:100%;" onkeyup="if (event.keyCode==13)referencia.select()" size="80" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Contacto</th>
      <td class="vtabla"><select name="idcontacto" class="insert" id="idcontacto">
        <option value="NULL" selected="selected"></option>
        <!-- START BLOCK : contacto -->
        <option value="{id}">{nombre}</option>
        <!-- END BLOCK : contacto -->
      </select>
</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Referente</th>
      <td class="vtabla"><input name="referencia" type="text" class="vinsert" id="referencia" style="width:100%;" onkeyup="if (event.keyCode==13)cuerpo.focus()" size="80" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" id="tipo" value="1" checked="checked" />
        Pantalla
          <input type="radio" name="tipo" id="tipo" value="2" />
          Archivo</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Contenido</th>
      <td class="vtabla"><textarea name="cuerpo" cols="100" rows="20" class="insert" style="text-transform:none;" id="cuerpo"></textarea></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Dar seguimiento</th>
      <td class="vtabla"><input name="seguimiento" type="radio" id="radio" value="1" checked="checked" />
        Si
          <input type="radio" name="seguimiento" id="radio2" value="0" />
          No</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Generar Carta" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./ban_car_fol.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
	else
		f.nombre_cia.value = result;
}

function validar() {
	if (get_val(f.num_cia) <= 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
		return false;
	}
	else if (f.atencion.value.length < 10) {
		alert('Debe especificar a quien va dirigida la carta');
		f.atencion.select();
		return false;
	}
	else if (f.cuerpo.value.length < 20) {
		alert('Debe escribir el contenido de la carta');
		f.cuerpo.focus();
		return false;
	}
	else if (confirm('¿Son correctos los datos de la carta?')) {
		if (f.tipo[0].checked) {
			var win = window.open('', 'carta', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
			f.submit();
			win.focus();
		}
		else if (f.tipo[1].checked) {
			f.target = '_self';
			f.submit();
		}
		
		f.reset();
	}
	else
		f.num_cia.select();
}

window.onload = f.num_cia.select();
//-->
</script>
</body>
</html>
