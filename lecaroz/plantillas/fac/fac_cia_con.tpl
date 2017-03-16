<!-- START BLOCK : datos -->
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.listado.value == "cia" && document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		else {
			document.form.submit();
			return;
		}
	}
</script>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="get" action="./fac_cia_con.php">
<p class="title">Consulta de Compa&ntilde;&iacute;as</p>
<table class="tabla">
  <tr>
    <th class="vtabla"><input name="listado" type="radio" value="cia" checked onClick="form.num_cia.style.visibility = 'visible'">
      Compa&ntilde;&iacute;a 
      <input name="num_cia" type="text" class="insert" id="num_cia" size="5" maxlength="3"></th>
    <th class="vtabla"><input name="listado" type="radio" value="todas" onClick="form.num_cia.style.visibility = 'hidden'">
      Todas las compa&ntilde;&iacute;as </th>
  </tr>
</table>
<p>
<input type="button" class="boton" onClick="valida_registro()" value="Generar listado">
</p>
</form>
</td>
</tr>
</table>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Listado de Compañías</p>
<!-- START BLOCK : bloque -->
<table width="100%" class="tabla" >
  <tr>
    <th colspan="5" class="vprint">{num_cia} {nombre_cia} ({nombre_corto}) </th>
    <th class="vprint">{depende}</th>
  </tr>
  <tr>
    <th class="vprint" width="10%">Direcci&oacute;n</th>
    <td width="22%" class="vprint">{direccion}</td>
    <th class="vprint" width="14%">No.IMSS</th>
    <td class="vprint" width="20%">{no_imss}</td>
    <th class="vprint" width="12%">Contador</th>
    <td class="vprint">{contador}</td>
  </tr>
  <tr>
    <th class="vprint">Tel&eacute;fono</th>
    <td class="vprint">{telefono}</td>
    <th class="vprint">Del. IMSS </th>
    <td class="vprint">{del_imss}</td>
    <th class="vprint">Administrador</th>
    <td class="vprint">{administrador}</td>
  </tr>
  <tr>
    <th class="vprint">E-m@il</th>
    <td class="vprint">{email}</td>
    <th class="vprint">Subdel. IMSS </th>
    <td class="vprint">{sub_imss}</td>
    <th class="vprint">Auditor</th>
    <td class="vprint">{auditor}</td>
  </tr>
  <tr>
    <th class="vprint">RFC</th>
    <td class="vprint">{rfc}</td>
    <th class="vprint">No. Infonavit </th>
    <td class="vprint">{no_infonavit}</td>
    <th class="vprint">Aseguradora</th>
    <td class="vprint">{aseguradora}</td>
  </tr>
  <tr>
    <th class="vprint">Banco</th>
    <td class="vprint">{banco}</td>
    <th class="vprint">No. cuenta de luz </th>
    <td class="vprint">{no_luz}</td>
    <th class="vprint">Sindicato</th>
    <td class="vprint">{sindicato}</td>
  </tr>
  <tr>
    <th class="vprint">CLABE</th>
    <td class="vprint">{clabe}</td>
    <th class="vprint">Contrato Recolecci&oacute;n</th>
    <td class="vprint">{contrato}</td>
    <th class="vprint">Operadora</th>
    <td class="vprint">{operadora}</td>
  </tr>
  <tr>
    <th class="vprint">Tipo de Persona</th>
    <td class="vprint">{tipo}</td>
    <th class="vprint">Subcuenta deudores </th>
    <td class="vprint">{deudores}</td>
    <th class="vprint">C&oacute;digo Gasolina</th>
    <td class="vprint">{gasolina}</td>
  </tr>
</table>
<br>
<!-- END BLOCK : bloque -->
</td>
</tr>
</table>
<!-- END BLOCK : listado -->
