
<script type="text/javascript" language="JavaScript">
	function valida()
	{
		imprimir_factura();
	}

	function imprimir_factura(cia,numero){
		window.open('./cometra.php','borrar','toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=800,height=800,left=300, top=100');
		return;
	}


	
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">GENERACI&Oacute;N DE FICHAS DE COMETRA</p>
<form name="form" action="./cometra.php" method="post" target="_blank">

<input name="temp" type="hidden">
<input name="contador" type="hidden" id="contador" value="{contador}">
<table class="tabla" >
  <tr class="tabla">
    <th class="tabla" colspan="2">Compa&ntilde;&iacute;a</th>
    <th class="tabla">Importe</th>
  </tr>
<!-- START BLOCK : cia -->
  <tr class="tabla">
	<td class="rtabla">{num_cia}</td>
    <td class="vtabla">{nombre_cia}<input name="num_cia{i}" type="hidden" class="insert" id="num_cia{i}" value="{num_cia}" size="10" maxlength="15"></td>
    <td class="tabla">
	<input name="importe{i}" type="text" class="insert" id="importe{i}" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();" onKeyDown="if(event.keyCode==13) document.form.importe{next}.select();" size="10" maxlength="15">
	</td>
  </tr>
<!-- END BLOCK : cia -->
</table>
<p>
  <input type="button" name="enviar" value="Siguiente" class="boton" onClick="form.submit();">
</p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.importe0.select();
</script>

</td>
</tr>
</table>