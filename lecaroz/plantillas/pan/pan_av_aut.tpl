<!-- START BLOCK : obtener_datos -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() 
	{
		if (document.form.codmp.value==""){
			alert("Inserte un código de materia prima por favor");
			return;
		}
		else
			document.form.submit();
	}

	function actualiza_mp(codmp, nombre) {
		// Arreglo con los nombres de las materias primas
		mp = new Array();				// Materias primas
		<!-- START BLOCK : nombre_mp -->
		mp[{codmp}] = '{nombre_mp}';
		<!-- END BLOCK : nombre_mp -->
				
		if (codmp.value > 0) {
			if (mp[codmp.value] == null) {
				alert("Materia Prima "+codmp.value+" Erronea");
				codmp.value = "";
				nombre.value  = "";
				codmp.select();
			}
			else {
				nombre.value   = mp[codmp.value];
			}
		}
		else if (codmp.value == "") {
			codmp.value = "";
			nombre.value  = "";
		}
	}
</script>
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
	<p class="title">CAPTURA DE CONSUMOS MAXIMOS DE MATERIA PRIMA</p>
	<form name="form" action="./pan_av_aut.php" method="get">
	  <p>
		<input name="temp" type="hidden">
	</p>
	  <table class="tabla">
		<tr>
		  <th scope="row" class="tabla" colspan="2">Materia Prima </th>
		</tr>
		<tr class="tabla">
		  <td class="tabla">C&oacute;digo
			<input name="codmp" type="text" class="insert" id="codmp" size="5" maxlength="3" onChange="actualiza_mp(this,form.nombre)"></td>
		  <td class="tabla"><input name="nombre" type="text" class="vnombre" id="nombre" size="50"></td>
		</tr>
	  </table>
	  
	  <p>
	  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar" onclick='valida_registro()'>
	  &nbsp;&nbsp;</p>
	</form>
	<script language="JavaScript" type="text/JavaScript">
	window.onload=document.form.codmp.select();
	</script>
</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
	<p class="title">CAPTURA DE CONSUMO M&Aacute;XIMO PARA:</p>
	<p class="title">{codmp}</p>
	<form name="formi" action="./insert_pan_av_aut.php" method="post">
	<input name="temp" type="hidden">
    <input name="contador" type="hidden" id="contador" value="{contador}">	
    <input name="codmp" type="hidden" id="codmp" value="{codmp1}">
    <table class="tabla">
	  <tr>
		<th scope="col" colspan="2" class="tabla">Compa&ntilde;&iacute;a</th>
		<th scope="col" class="tabla">Frances de dia </th>
		<th scope="col" class="tabla">Frances de noche </th>
		<th scope="col" class="tabla">Bizcochero</th>
		<th scope="col" class="tabla">Repostero</th>
	    <th scope="col" class="tabla">Piconero</th>
	  </tr>
	  <!-- START BLOCK : rows -->
	  <tr class="tabla">
		<th class="tabla">{num_cia}<input name="num_cia{i}" type="hidden" id="num_cia{i}" value="{num_cia}"></th>
		<th class="vtabla">{nombre_cia}</th>
		<td class="tabla"><input name="fd{i}" type="text" class="rinsert" id="fd{i}" value="{fd}" size="10" onKeyDown="if(event.keyCode==13) form.fn{i}.select()" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"></td>
		<td class="tabla"><input name="fn{i}" type="text" class="rinsert" id="fn{i}" value="{fn}" size="10" onKeyDown="if(event.keyCode==13) form.biz{i}.select()" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"></td>
		<td class="tabla"><input name="biz{i}" type="text" class="rinsert" id="biz{i}" value="{biz}" size="10" onKeyDown="if(event.keyCode==13) form.rep{i}.select()" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"></td>
		<td class="tabla"><input name="rep{i}" type="text" class="rinsert" id="rep{i}" value="{rep}" size="10" onKeyDown="if(event.keyCode==13) form.pic{i}.select()" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"></td>
	    <td class="tabla"><input name="pic{i}" type="text" class="rinsert" id="pic{i}" value="{pic}" size="10" onKeyDown="if(event.keyCode==13) form.fd{next}.select()" onFocus="form.temp.value=this.value" onChange="valor=isFloat(this,2,form.temp); if (valor==false) this.select();"></td>
	  </tr>
	  <!-- END BLOCK : rows -->
	</table>
	<p>
  <input type="button" name="regresar" value="Regresar" class="boton" onClick="document.location='./pan_av_aut.php'">&nbsp;&nbsp;
  <input name="enviar" type="button" class="boton" id="enviar" onClick="document.formi.submit();" value="Capturar" {disabled}>
	</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.formi.fd0.select();
</script>
</td>
</tr>
</table>

<!-- END BLOCK : captura -->


