<script language="javascript" type="text/javascript">
	function validar(f) {
		if (get_val(f.compania) > 0 && f.compania.disabled == false) {
			if (get_val(f.compania) <= 100) {
				if (get_val(f.mes) < 6 && get_val(f.anio) <= 2005)
					f.action = "./bal_esr_pan.php";
				else
					f.action = "./bal_esr_pan_v2.php";
			}
			else if (get_val(f.compania) > 100 && get_val(f.compania) < 200)
				f.action = "./bal_esr_con.php";
			else if (get_val(f.compania) >= 900 && get_val(f.compania) <= 950)
				f.action = "./bal_esr_zap.php";
		}
		else if (document.form.tipo[0].checked == true) {
			if (document.form.mes.value < 6 && document.form.anio.value == 2005)
				document.form.action = "./bal_esr_pan.php";
			else
				document.form.action = "./bal_esr_pan_v2.php";
		}
		else if (document.form.tipo[1].checked == true)
			document.form.action = "./bal_esr_con.php";
		else if (document.form.tipo[2].checked == true)
			document.form.action = "./bal_esr_zap.php";
		
		if (document.form.todas.cheched == false && document.form.compania.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.compania.focus();
			return false;
		}
		/*else if (document.form.todas.checked == true){
			document.form.submit();
		}*/
		else
			document.form.submit();
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form action="./bal_esr_con.php" method="get" name="form" target="_blank" onKeyDown="if(event.keyCode == 13) form.enviar.focus();">
<input name="temp" type="hidden">
<input name="ini" type="hidden" value="101">
<input name="fin" type="hidden" value="200">
<table class="tabla">
  <tr>
    <th class="vtabla">Compa&ntilde;&iacute;a</th>
    <td class="vtabla"><input name="compania" type="text" class="insert" id="compania" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" size="5" maxlength="5"> <input name="todas" type="checkbox" id="todas" value="TRUE" onClick="if (this.checked) form.compania.disabled=true; else form.compania.disabled=false">
      (Todas)</td>
    <th class="vtabla">Mes</th>
    <td class="vtabla">
	<select name="mes" class="insert">
	  <option value="1" {1}>ENERO</option>
	  <option value="2" {2}>FEBRERO</option>
	  <option value="3" {3}>MARZO</option>
	  <option value="4" {4}>ABRIL</option>
	  <option value="5" {5}>MAYO</option>
	  <option value="6" {6}>JUNIO</option>
	  <option value="7" {7}>JULIO</option>
	  <option value="8" {8}>AGOSTO</option>
	  <option value="9" {9}>SEPTIEMBRE</option>
	  <option value="10" {10}>OCTUBRE</option>
	  <option value="11" {11}>NOVIEMBRE</option>
	  <option value="12" {12}>DICIEMBRE</option>
	</select>
	</td>
    <th class="vtabla">A&ntilde;o</th>
    <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" value="{anio}" size="4" maxlength="4"></td>
  </tr>
  <tr>
    <th class="vtabla">&nbsp;</th>
    <td class="vtabla"><input name="tipo" type="radio" value="panaderias" checked>
      Panader&iacute;as</td>
    <th class="vtabla">&nbsp;</th>
    <td class="vtabla"><input name="tipo" type="radio" value="rosticerias">
      Rosticer&iacute;as</td>
    <th class="vtabla">&nbsp;</th>
    <td class="vtabla"><input name="tipo" type="radio" value="zapaterias">
    Zapaterias</td>
  </tr>
  <tr>
    <th colspan="6" class="vtabla"><input name="no_gastos" type="checkbox" id="no_gastos" value="TRUE">
      No tomar gastos extraordinarios </th>
    </tr>
</table>
<p>
  <input name="enviar" type="button" class="boton" id="enviar" value="Resultados" onClick="validar(this.form)">
</p>
</form>
<script language="javascript" type="text/javascript">
	window.onload = document.form.compania.select();
	
	window.onfocus = document.form.compania.select();
</script>
</td>
</tr>
</table>