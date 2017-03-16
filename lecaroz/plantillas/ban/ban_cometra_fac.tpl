
<script type="text/javascript" language="JavaScript">
	function valida()
	{
		if(document.form.cia.value <= 0) {
			alert('Debe especificar una compañía');
			document.form.cia.select();
		}
		else if(document.form.cia.value==""){
			alert('Debe especificar una compañía');
			document.form.cia.select();
		}
		else if(document.form.numero.value <= 0) {
			alert('Debe especificar un número de accionistas');
			document.form.numero.select();
		}
		else if(document.form.numero.value==""){
			alert('Debe especificar un número de accionistas');
			document.form.numero.select();
		}
		else if(document.form.numero.value >150){
			alert('No puede imprimir mas de 150 comprobantes');
			document.form.numero.select();
			}
		else
			imprimir_factura(document.form.cia.value,document.form.numero.value);
	}

	function imprimir_factura(cia,numero){
		var win = window.open('','facturas','toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=800,height=800,left=300, top=100');
		document.form.submit();
		win.focus();
		return;
	}


	
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">GENERACI&Oacute;N DE FICHAS DE COMETRA </p>
<form action="./cometra.php" method="get" name="form" target="facturas">
<table class="tabla" >
  <tr class="tabla">
    <th class="tabla">Número de compañía</th>
    <th class="tabla"><input name="cia[]" id="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.cia[1].select();">
	<input name="cia[]" id="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.cia[2].select();">
	<input name="cia[]" id="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.cia[3].select();">
	<input name="cia[]" id="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.cia[4].select();">
	<input name="cia[]" id="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.cia[5].select();">
	<input name="cia[]" id="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.cia[6].select();">
	<input name="cia[]" id="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.cia[7].select();">
	<input name="cia[]" id="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.cia[8].select();">
	<input name="cia[]" id="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.cia[9].select();">
	<input name="cia[]" id="cia" type="text" class="insert" size="4" maxlength="4" onKeyDown="if(event.keyCode==13)form.numero.select();"></th>
  </tr>
  <tr class="tabla">
    <td class="tabla">Número de fichas </td>
    <td class="tabla"><input name="numero" type="text" class="insert" id="numero" onKeyDown="if(event.keyCode==13)form.cia[0].select();" value="100" size="5" maxlength="4"></td>
  </tr>
  <!--
  
  <tr class="tabla">
    <td class="tabla" colspan="2"><input name="tipo" type="checkbox" id="tipo" value="checkbox" onChange="if(this.checked==false){ form.bandera.value=0 } else form.bandera.value=1;">
      Insertar importe 
        <input name="bandera" type="hidden" class="insert" id="bandera" value="0"size="4" maxlength="4"></td>
  </tr>
  <tr class="tabla">
    <td class="tabla" colspan="2"><input name="importe" type="text" class="insert" id="importe" size="10" maxlength="15"></td>
  </tr>
  
  -->
</table>
<p>
  <input type="button" name="enviar" value="Siguiente" class="boton" onClick="valida();">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.cia.select();
</script>

</td>
</tr>
</table>