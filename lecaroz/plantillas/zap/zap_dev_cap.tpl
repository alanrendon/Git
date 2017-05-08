<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Devoluciones</p>
  <form action="./zap_dev_cap.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="movCursor(event.keyCode,num_pro,null,null,null,num_pro)" value="{num_cia}" size="3"{readonly} />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="movCursor(event.keyCode,fecha,null,null,num_cia,fecha)" value="{num_pro}" size="3"{readonly} />
        <input name="nombre_pro" type="text" disabled="disabled" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,modelo[0],null,null,num_pro,modelo[0])" value="{fecha}" size="10" maxlength="10"{readonly} /></td>
    </tr>
  </table>
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla">Modelo</th>
      <th class="tabla">Color</th>
      <th class="tabla">Talla</th>
      <th class="tabla">Piezas</th>
      <th class="tabla">Precio</th>
      <th class="tabla">Importe</th>
      <th class="tabla">Observaciones</th>
      </tr>
    <!-- START BLOCK : fila_cap -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="modelo[]" type="text" class="insert" id="modelo" style="width:100%;" onfocus="this.select()" onkeydown="movCursor(event.keyCode,color[{i}],null,color[{i}],modelo[{back}],modelo[{next}])" value="{modelo}" size="6" /></td>
      <td class="tabla"><input name="color[]" type="text" class="vinsert" id="color" onkeydown="movCursor(event.keyCode,talla[{i}],modelo[{i}],talla[{i}],color[{back}],color[{next}])" value="{color}" size="20" maxlength="30" /></td>
      <td class="tabla"><input name="talla[]" type="text" class="rinsert" id="talla" style="width:100%;" onfocus="tmp.value=this.value;this.select()" onchange="input_format(this,1,true)" onkeydown="movCursor(event.keyCode,piezas[{i}],color[{i}],piezas[{i}],talla[{back}],talla[{next}])" value="{talla}" size="4" /></td>
      <td class="tabla"><input name="piezas[]" type="text" class="rinsert" id="piezas" onfocus="tmp.value=this.value;this.select()" onchange="if (input_format(this,-1,true)) calculaImporte({i})" onkeydown="movCursor(event.keyCode,precio[{i}],talla[{i}],precio[{i}],piezas[{back}],piezas[{next}])" value="{piezas}" size="8" /></td>
      <td class="tabla"><input name="precio[]" type="text" class="insert" id="precio" style="width:100%;" onfocus="tmp.value=this.value;this.select()" onchange="if (input_format(this,2,true)) calculaImporte({i})" onkeydown="movCursor(event.keyCode,obs[{i}],piezas[{i}],obs[{i}],precio[{back}],precio[{next}])" value="{precio}" size="6" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="width:100%;" value="{importe}" size="10" /></td>
      <td class="tabla"><input name="obs[]" type="text" class="vinsert" id="obs" onkeydown="movCursor(event.keyCode,modelo[{next}],precio[{i}],null,obs[{back}],obs[{next}])" value="{obs}" size="30" maxlength="100" /></td>
      </tr>
	<!-- END BLOCK : fila_cap -->
	<!-- START BLOCK : fila_reg -->
	<!-- END BLOCK : fila_reg -->
    <tr>
      <th colspan="5" class="rtabla">Total</th>
      <th class="tabla">        <input name="total" type="text" class="rnombre" id="total" style="font-size:14pt;" value="0.00" size="10" /></th>
      <th class="tabla">&nbsp;</th>
      </tr>
  </table>
  <p>
  <input name="" type="button" class="boton" onclick="validar('agregar')" value="Agregar" />
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array();

<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->
<!-- START BLOCK : pro -->
pro[{num_pro}] = "{nombre}";
<!-- END BLOCK : pro -->

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null && enter) enter.select();
	else if (keyCode == 37 && lt != null && lt) lt.select();
	else if (keyCode == 39 && rt != null && rt) rt.select();
	else if (keyCode == 38 && up != null && up) up.select();
	else if (keyCode == 40 && dn != null && dn) dn.select();
}

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre_cia.value = cia[get_val(f.num_cia)];
	else {
		alert("La compañía no se encuentra en el catálogo");
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function cambiaPro() {
	if (f.num_pro.value == '' || f.num_pro.value == '0') {
		f.num_pro.value = '';
		f.nombre_pro.value = '';
	}
	else if (pro[get_val(f.num_pro)] != null)
		f.nombre_pro.value = pro[get_val(f.num_pro)];
	else {
		alert("La compañía no se encuentra en el catálogo");
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
}

function calculaImporte(i) {
	var importe = 0;
	
	if (get_val(f.piezas[i]) > 0 && get_val(f.precio[i]) > 0)
		importe = get_val(f.piezas[i]) * get_val(f.precio[i]);
	
	f.importe[i].value = importe != 0 ? number_format(importe, 2) : '';
	
	calculaTotal();
}

function calculaTotal() {
	var total = 0;
	
	for (var i = 0; i < f.importe.length; i++)
		total += get_val(f.importe[i]);
	
	f.total.value = total != 0 ? number_format(total, 2) : '0.00';
}

function validar(action) {
	if (get_val(f.num_cia) == 0) {
		alert("Debe especificar la compañía");
		f.num_cia.select();
		return false;
	}
	
	if (get_val(f.num_pro) == 0) {
		alert("Debe especificar el proveedor");
		f.num_pro.select();
		return false;
	}
	
	if (get_val(f.total) == 0) {
		alert("El importe total de las devoluciones no puede ser cero");
		f.modelo[0].select();
		return false;
	}
	
	for (var i = 0; i < f.importe.length; i++)
		if (get_val(f.modelo[i]) > 0 || f.color[i].value != '' || get_val(f.talla[i]) > 0 || get_val(f.piezas[i]) > 0 || get_val(f.precio[i]) > 0)
			if (f.modelo[i].value == '' || f.color[i].value == '' || get_val(f.talla[i]) == 0 || get_val(f.piezas[i]) == 0 || get_val(f.precio[i]) == 0) {
				alert("Debe especificar todos los datos del modelo devuelto");
				f.modelo[i].select();
				return false;
			}
	
	if (confirm("¿Son correctos los datos?"))
		f.submit();
}

window.onload = function() { showAlert = true; if (get_val(f.num_cia) > 0) f.modelo[0].select(); else f.num_cia.select(); };
//-->
</script>
<!-- END BLOCK : captura -->
</body>
</html>
