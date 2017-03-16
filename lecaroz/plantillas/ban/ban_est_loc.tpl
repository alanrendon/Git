<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Estatus de Locales</p>
  <form action="./ban_est_loc.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Inmobiliaria</th>
      <th class="tabla" scope="col">Local</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
      <th class="tabla" scope="col">Tipo</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia({i})" onKeyDown="movCursor(event.keyCode,anio[{i}],null,anio[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
        <input name="nombre[]" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
      <td class="tabla"><select name="local[]" class="insert" id="local">
	  	<option value=""></option>
      </select>
      </td>
      <td class="tabla"><select name="mes[]" class="insert" id="mes">
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
      <td class="tabla"><input name="anio[]" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,num_cia[{next}],num_cia[{i}],null,anio[{back}],anio[{next}])" value="{anio}" size="4" maxlength="4" /></td>
      <td class="tabla"><select name="tipo[]" class="insert" id="tipo">
        <option value="0" selected="selected">BAJA</option>
        <option value="1">PAGADO</option>
        <option value="2">PENDIENTE</option>
      </select>
      </td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), arr = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : cia -->
<!-- START BLOCK : arr -->
arr[{arr}] = new Array({locales});
<!-- END BLOCK : arr -->

function cambiaCia(i) {
	if (f.num_cia[i].value == '' || f.num_cia[i].value == '0') {
		f.num_cia[i].value = '';
		f.nombre[i].value = '';
	}
	else if (cia[get_val(f.num_cia[i])] != null)
		f.nombre[i].value = cia[get_val(f.num_cia[i])];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia[i].value = f.tmp.value;
		f.num_cia[i].select();
	}
	
	cambiaLocales(i);
}

function cambiaLocales(i) {
	var j, k;
	
	if (get_val(f.num_cia[i]) == 0) {
		f.local[i].length = 1;
		f.local[i].options[0].value = '';
		f.local[i].options[0].text = '';
	}
	else {
		f.local[i].length = arr[get_val(f.num_cia[i])] != null && arr[get_val(f.num_cia[i])].length != undefined ? 1 + arr[get_val(f.num_cia[i])].length / 3 : 1;
		if (arr[get_val(f.num_cia[i])] != null && arr[get_val(f.num_cia[i])].length != undefined) {
			f.local[i].options[0].value = '';
			f.local[i].options[0].text = '';
			for (j = 0, k = 1; j < arr[get_val(f.num_cia[i])].length; j += 3, k++) {
				f.local[i].options[k].value = arr[get_val(f.num_cia[i])][j];
				f.local[i].options[k].text = arr[get_val(f.num_cia[i])][j + 1]/* + ' - ' + arr[get_val(f.num_cia[i])][j + 2]*/;
			}
		}
		else {
			f.local[i].length = 1;
			f.local[i].options[0].value = '';
			f.local[i].options[0].text = '';
		}
	}
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null && enter) enter.select();
	else if (keyCode == 37 && lt != null && lt) lt.select();
	else if (keyCode == 39 && rt != null && rt) rt.select();
	else if (keyCode == 38 && up != null && up) up.select();
	else if (keyCode == 40 && dn != null && dn) dn.select();
}

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
	else
		f.num_cia[0].select();
}

window.onload = f.num_cia[0].select();
//-->
</script>
</body>
</html>
