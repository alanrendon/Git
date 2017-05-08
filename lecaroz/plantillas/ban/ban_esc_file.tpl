<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
function valida()
{
if(document.form.anio.value=="" || document.form.anio.value < 0){
	alert("Revise el año");
	document.form.anio.select();
}
else
	document.form.submit();
}
</script>

<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Generaci&oacute;n del archivo de Estados de cuenta del mes para contadores </p>
	<form action="./bal_esc_file.php" method="get" name="form">
	<input name="temp" type="hidden">
	<table border="1" class="tabla">
	  <tr>
		<th scope="row" colspan="2" class="tabla"><input name="tipo" type="radio" id="tipo_1" value="fecha" checked=""> Generado el
		  <select name="mes" size="1" class="insert">
        <!-- START BLOCK : mes -->
	    <option value="{num_mes}" {selected}>{nom_mes}</option>
        <!-- END BLOCK : mes -->
		</select>
		  del
		  <input name="anio" type="text" class="insert" id="anio" value="{anio_actual}" size="5"  onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';">
		 </th>
	  </tr>
	  <tr>
		<th scope="row" colspan="2" class="tabla"><input name="tipo" type="radio" id="tipo_2" value="fecha_con"> Conciliado el
		  <select name="mes_con" size="1" class="insert">
        <!-- START BLOCK : mes_con -->
	    <option value="{num_mes}" {selected}>{nom_mes}</option>
        <!-- END BLOCK : mes_con -->
		</select>
		  del
		  <input name="anio_con" type="text" class="insert" id="anio_con" value="{anio_actual}" size="5"  onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';">
		 </th>
	  </tr>
	  <tr>
	    <th scope="row" colspan="2" class="vtabla">Cuenta
	      <select name="cuenta" class="insert" id="cuenta">
	        <option value="1" selected>BANORTE</option>
	        <option value="2">SANTANDER SERFIN</option>
	        </select></th>
	    </tr>
	</table>
	<p>
	<table class="tabla">
      <tr>
        <th class="vtabla">Compañías</th>
        <td class="tabla"><input name="num_cia0" type="text" class="vinsert" id="num_cia0" size="5" onKeyDown="if(event.keyCode==13) form.num_cia1.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
        <td class="tabla"><input name="num_cia1" type="text" class="vinsert" id="num_cia1" size="5" onKeyDown="if(event.keyCode==13) form.num_cia2.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
        <td class="tabla"><input name="num_cia2" type="text" class="vinsert" id="num_cia2" size="5" onKeyDown="if(event.keyCode==13) form.num_cia3.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
        <td class="tabla"><input name="num_cia3" type="text" class="vinsert" id="num_cia3" size="5" onKeyDown="if(event.keyCode==13) form.num_cia4.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
        <td class="tabla"><input name="num_cia4" type="text" class="vinsert" id="num_cia4" size="5" onKeyDown="if(event.keyCode==13) form.num_cia5.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
        <td class="tabla"><input name="num_cia5" type="text" class="vinsert" id="num_cia5" size="5" onKeyDown="if(event.keyCode==13) form.num_cia6.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
        <td class="tabla"><input name="num_cia6" type="text" class="vinsert" id="num_cia6" size="5" onKeyDown="if(event.keyCode==13) form.num_cia7.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
        <td class="tabla"><input name="num_cia7" type="text" class="vinsert" id="num_cia7" size="5" onKeyDown="if(event.keyCode==13) form.num_cia8.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
        <td class="tabla"><input name="num_cia8" type="text" class="vinsert" id="num_cia8" size="5" onKeyDown="if(event.keyCode==13) form.num_cia9.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
        <td class="tabla"><input name="num_cia9" type="text" class="vinsert" id="num_cia9" size="5" onKeyDown="if(event.keyCode==13) form.num_cia0.select();" onChange="valor=isInt(this,form.temp); if (valor==false) this.value='';"></td>
      </tr>
    </table>
	<p>
	<input type="button" name="enviar" class="boton" value="Generar Archivo" onclick='valida()'>
	</p>
	</form>
	<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia0.select()
</script>

</td>
</tr>
</table>
