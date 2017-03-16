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
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pedidos Manuales con Proveedor </p>
  <form action="./ped_sis_man.php" method="post" name="form">
  <input type="hidden" name="temp">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Producto</th>
	  <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Cantidad</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia(this,nombre_cia[{i}])" onKeyDown="if (event.keyCode == 13) codmp[{i}].select()" size="3" maxlength="3">
        <input name="nombre_cia[]" type="text" disabled="true" class="vnombre" id="nombre_cia" size="30"></td>
      <td class="tabla"><input name="codmp[]" type="text" class="insert" id="codmp" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaMp(this,nombre_mp[{i}])" onKeyDown="if (event.keyCode == 13) num_proveedor[{i}].select()" size="4" maxlength="4">
		<input name="nombre_mp[]" type="text" disabled="true" class="vnombre" id="nombre_mp" size="30"></td>
      <td class="tabla"><input name="num_proveedor[]" type="text" class="insert" id="num_proveedor" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaPro(codmp[{i}],this,nombre_pro[{i}])" onKeyDown="if (event.keyCode == 13) cantidad[{i}].select()" size="4" maxlength="4">
        <input name="nombre_pro[]" type="text" disabled="true" class="vnombre" id="nombre_pro" size="30"></td>
	  <td class="tabla"><input name="cantidad[]" type="text" class="insert" id="cantidad" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" size="10" maxlength="10"></td>
      </tr>
	  <!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var cia = new Array();
	var mp = new Array();
	var pro = new Array();
	
	<!-- START BLOCK : cia -->
	cia[{num_cia}] = "{nombre_cia}";
	<!-- END BLOCK : cia -->
	<!-- START BLOCK : mp -->
	mp[{codmp}] = "{nombre_mp}";
	<!-- END BLOCK : mp -->
	<!-- START BLOCK : pro -->
	pro[{pro}] = "{nombre_pro}";
	<!-- END BLOCK : pro -->
	
	function cambiaCia(num, nombre) {
		if (num.value == "")
			nombre.value = "";
		else if (cia[num.value] != null)
			nombre.value = cia[num.value];
		else {
			alert("La compañía no se encuentra en el catalogo");
			num.value = num.form.temp.value;
			num.select();
		}
	}
	
	function cambiaMp(cod, nombre) {
		if (cod.value == "")
			nombre.value = "";
		else if (mp[cod.value] != null)
			nombre.value = mp[cod.value];
		else {
			alert("La compañía no se encuentra en el catalogo");
			cod.value = cod.form.temp.value;
			cod.select();
		}
	}
	
	function cambiaPro(cod, prov, nombre) {
		if (prov.value == "")
			nombre.value = "";
		else if (pro[prov.value] != null)
			nombre.value = pro[prov.value];
		else {
			alert("El proveedor no existe en el catalogo");
			prov.value = prov.form.temp.value;
			prov.select();
		}
	}
	
	function validar(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.num_cia[0].select();
	}
	
	window.onload = document.form.num_cia[0].select();
</script>
<!-- END BLOCK : captura -->
</body>
</html>
