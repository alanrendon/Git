<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado Anualizado de Producci&oacute;n</p>
  <form action="./bal_com_pro.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr id="cia_row">
      <th class="vtabla" scope="row">Compa&ntilde;ia</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr id="admin_row">
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected>-</option>
        <option value="-1">TODOS LOS ADMIN.</option>
        <!-- START BLOCK : admin -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
    <tr id="cias_row" style="display: none;">
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;as</th>
      <td class="vtabla"><input name="cias[]" type="text" class="insert" id="cias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) cias[1].select()" size="3" maxlength="3" disabled>
		<input name="cias[]" type="text" class="insert" id="cias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) cias[2].select()" size="3" maxlength="3" disabled>
		<input name="cias[]" type="text" class="insert" id="cias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) cias[3].select()" size="3" maxlength="3" disabled>
		<input name="cias[]" type="text" class="insert" id="cias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) cias[4].select()" size="3" maxlength="3" disabled>
		<input name="cias[]" type="text" class="insert" id="cias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) cias[5].select()" size="3" maxlength="3" disabled>
		<input name="cias[]" type="text" class="insert" id="cias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) cias[6].select()" size="3" maxlength="3" disabled>
		<input name="cias[]" type="text" class="insert" id="cias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) cias[7].select()" size="3" maxlength="3" disabled>
		<input name="cias[]" type="text" class="insert" id="cias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) cias[8].select()" size="3" maxlength="3" disabled>
		<input name="cias[]" type="text" class="insert" id="cias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) cias[9].select()" size="3" maxlength="3" disabled>
		<input name="cias[]" type="text" class="insert" id="cias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) codgastos.select()" size="3" maxlength="3" disabled></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Turnos</th>
      <td class="vtabla"><input name="turno[]" type="checkbox" id="turno" value="1">
        Frances de D&iacute;a<br>
        <input name="turno[]" type="checkbox" id="turno" value="2">
        Frances de Noche <br>
        <input name="turno[]" type="checkbox" id="turno" value="3">
        Bizcochero<br>
        <input name="turno[]" type="checkbox" id="turno" value="4">
        Repostero<br>
        <input name="turno[]" type="checkbox" id="turno" value="8">
        Piconero<br>
        <input name="turno[]" type="checkbox" id="turno" value="9">
        Gelatinero</td>
    </tr>
	
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) {
if (com.checked) cias[0].select();
else num_cia.select();
}" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Comparativo</th>
      <td class="vtabla"><input name="com" type="checkbox" id="com" value="1" onClick="cambiaOpciones(this)">
        Si</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Porcentajes de<br> 
      Raya / Producci&oacute;n </th>
      <td class="vtabla"><input name="por" type="checkbox" id="por" value="1">
        Si</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function cambiaOpciones(check) {
		var form = check.form;
		
		if (check.checked) {
			// Deshabilitar campos compañía, y admin
			document.getElementById("cia_row").style.display = "none";
			document.getElementById("admin_row").style.display = "none";
			form.num_cia.disbaled = true;
			form.idadmin.disabled = true;
			// Habilitar compañías
			document.getElementById("cias_row").style.display = "table-row";
			for (var i = 0; i < form.cias.length; i++)
				form.cias[i].disabled = false;
			form.cias[0].select();
		}
		else {
			// Habilitar campos compañía, y admin
			window.opener.document.getElementById("cia_row").style.display = "table-row";
			document.getElementById("admin_row").style.display = "table-row";
			form.num_cia.disbaled = false;
			form.idadmin.disabled = false;
			// deshabilitar compañías
			document.getElementById("cias_row").style.display = "none";
			for (var i = 0; i < form.cias.length; i++)
				form.cias[i].disabled = true;
			form.num_cia.select();
		}
	}
	
	function valida_registro(form) {
		if (form.anio.value < 2005) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" align="center" class="print_encabezado">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td width="20%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="print_encabezado">Listado de Producci&oacute;n Anualizada del {anio}<br>
      <span style="font-size: 12pt;">{turno}</span></td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Ene</th>
      <th class="print" scope="col">Feb</th>
      <th class="print" scope="col">Mar</th>
      <th class="print" scope="col">Abr</th>
      <th class="print" scope="col">May</th>
      <th class="print" scope="col">Jun</th>
      <th class="print" scope="col">Jul</th>
      <th class="print" scope="col">Ago</th>
      <th class="print" scope="col">Sep</th>
      <th class="print" scope="col">Oct</th>
      <th class="print" scope="col">Nov</th>
      <th class="print" scope="col">Dic</th>
      <th class="print" scope="col">Total</th>
      <th class="print" scope="col">Prom</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rprint">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint">{1}</td>
      <td class="rprint">{2}</td>
      <td class="rprint">{3}</td>
      <td class="rprint">{4}</td>
      <td class="rprint">{5}</td>
      <td class="rprint">{6}</td>
      <td class="rprint">{7}</td>
      <td class="rprint">{8}</td>
      <td class="rprint">{9}</td>
      <td class="rprint">{10}</td>
      <td class="rprint">{11}</td>
      <td class="rprint">{12}</td>
      <td class="rprint"><strong>{total}</strong></td>
      <td class="rprint"><strong>{prom}</strong></td>
    </tr>
	<!-- END BLOCK : fila -->
	<tr>
	  <th colspan="2" class="rprint"> <strong> <font size="2">Totales</font></strong></th>
      <th class="rprint_total"><strong>{t1}</strong></th>
      <th class="rprint_total"><strong>{t2}</strong></th>
      <th class="rprint_total"><strong>{t3}</strong></th>
      <th class="rprint_total"><strong>{t4}</strong></th>
      <th class="rprint_total"><strong>{t5}</strong></th>
      <th class="rprint_total"><strong>{t6}</strong></th>
      <th class="rprint_total"><strong>{t7}</strong></th>
      <th class="rprint_total"><strong>{t8}</strong></th>
      <th class="rprint_total"><strong>{t9}</strong></th>
      <th class="rprint_total"><strong>{t10}</strong></th>
      <th class="rprint_total"><strong>{t11}</strong></th>
      <th class="rprint_total"><strong>{t12}</strong></th>
	  <th class="rprint_total">{total}</th>
	  <th class="rprint_total">{prom}</th>
	</tr>	
</table>
<br style="page-break-after:always;">

<!-- END BLOCK : listado -->
<!-- START BLOCK : porcentajes -->
<table width="100%">
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" align="center" class="print_encabezado">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td width="20%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="print_encabezado">Listado de Porcentajes de Raya entre Producci&oacute;n Anualizada del {anio}<br>
      <span style="font-size: 12pt;">{turno}</span></td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Ene</th>
      <th class="print" scope="col">Feb</th>
      <th class="print" scope="col">Mar</th>
      <th class="print" scope="col">Abr</th>
      <th class="print" scope="col">May</th>
      <th class="print" scope="col">Jun</th>
      <th class="print" scope="col">Jul</th>
      <th class="print" scope="col">Ago</th>
      <th class="print" scope="col">Sep</th>
      <th class="print" scope="col">Oct</th>
      <th class="print" scope="col">Nov</th>
      <th class="print" scope="col">Dic</th>
      <th class="print" scope="col">Prom</th>
    </tr>
	<!-- START BLOCK : fila_por -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rprint">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
      <td class="rprint">{1}</td>
      <td class="rprint">{2}</td>
      <td class="rprint">{3}</td>
      <td class="rprint">{4}</td>
      <td class="rprint">{5}</td>
      <td class="rprint">{6}</td>
      <td class="rprint">{7}</td>
      <td class="rprint">{8}</td>
      <td class="rprint">{9}</td>
      <td class="rprint">{10}</td>
      <td class="rprint">{11}</td>
      <td class="rprint">{12}</td>
      <td class="rprint"><strong>{prom}</strong></td>
    </tr>
	<!-- END BLOCK : fila_por -->	
</table>
<p align="center" style="font-family: Arial, Helvetica, sans-serif; font-size: 10pt;">NOTA: Falta tomar en cuenta Gasto de Panaderos</p>
<br style="page-break-after:always;">
<!-- END BLOCK : porcentajes -->