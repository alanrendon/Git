<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.cia.campo0.value <= 0) {
			alert('Debe especificar un numero para la compania');
			document.cia.campo0.focus();
		}
		else {
			if (confirm("¿Son correctos los datos del formulario?"))
				document.cia.submit();
			else
				document.cia.campo0.focus();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?"))
			document.cia.reset();
		else
			document.cia.campo0.focus();
	}
</script>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">

<link href="styles/tablas.css" rel="stylesheet" type="text/css">
<form action="./insercion.php?tabla=companias" method="post" name="cia" id="cia">
  <table border="1" class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Azul</th>
      <th class="tabla" scope="col">Rosa</th>
      <th class="tabla" scope="col">Amarillo</th>
      <th class="tabla" scope="col">Verde</th>
      <th class="tabla" scope="col">Blanco</th>
    </tr>
    <tr>
      <th class="tabla" scope="row">Barredura</th>
      <td><input name="textfield" type="text" class="insert"></td>
      <td><input name="textfield6" type="text" class="insert"></td>
      <td><input name="textfield11" type="text" class="insert"></td>
      <td><input name="textfield16" type="text" class="insert"></td>
      <td><input name="textfield21" type="text" class="insert"></td>
    </tr>
    <tr>
      <th class="tabla" scope="row">Costales</th>
      <td><input name="textfield2" type="text" class="insert"></td>
      <td><input name="textfield7" type="text" class="insert"></td>
      <td><input name="textfield12" type="text" class="insert"></td>
      <td><input name="textfield17" type="text" class="insert"></td>
      <td><input name="textfield22" type="text" class="insert"></td>
    </tr>
    <tr>
      <th class="tabla" scope="row">Botes</th>
      <td><input name="textfield3" type="text" class="insert"></td>
      <td><input name="textfield8" type="text" class="insert"></td>
      <td><input name="textfield13" type="text" class="insert"></td>
      <td><input name="textfield18" type="text" class="insert"></td>
      <td><input name="textfield23" type="text" class="insert"></td>
    </tr>
    <tr>
      <th class="tabla" scope="row">Cubetas</th>
      <td><input name="textfield4" type="text" class="insert"></td>
      <td><input name="textfield9" type="text" class="insert"></td>
      <td><input name="textfield14" type="text" class="insert"></td>
      <td><input name="textfield19" type="text" class="insert"></td>
      <td><input name="textfield24" type="text" class="insert"></td>
    </tr>
    <tr>
      <th class="tabla" scope="row">Otros</th>
      <td><input name="textfield5" type="text" class="insert"></td>
      <td><input name="textfield10" type="text" class="insert"></td>
      <td><input name="textfield15" type="text" class="insert"></td>
      <td><input name="textfield20" type="text" class="insert"></td>
      <td><input name="textfield25" type="text" class="insert"></td>
    </tr>
  </table>
</form>
