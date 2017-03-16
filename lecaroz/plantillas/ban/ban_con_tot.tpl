<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Totales de Conceptos Bancarios </p>
  <form action="./ban_con_tot.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta" onChange="cambiaCodigos()">
        <option value="1">BANORTE</option>
        <option value="2">SANTANDER SERFIN</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option> </option>
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
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla"><select name="cod_mov" class="insert" id="cod_mov">
        <option value=""> </option>
      </select></td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var f = document.form, cod_mov = new Array();

	cod_mov[1] = new Array({ban});
	cod_mov[2] = new Array({san});

	function cambiaCodigos() {
		var j, k, cuenta = get_val(f.cuenta);

		f.cod_mov.length = cod_mov[cuenta].length != undefined ? cod_mov[cuenta].length / 2 : 1;
		if (cod_mov[cuenta] != null && cod_mov[cuenta].length != undefined)
			for (j = 0, k = 0; j < cod_mov[cuenta].length; j += 2, k++) {
				f.cod_mov.options[k].value = cod_mov[cuenta][j];
				f.cod_mov.options[k].text = cod_mov[cuenta][j] + ' - ' + cod_mov[cuenta][j + 1];
			}
		else {
			f.cod_mov[i].length = 1;
			f.cod_mov[i].options[0].value = '';
			f.cod_mov[i].options[0].text = '';
		}
	}

	function validar(form) {
		if (form.anio.value <= 0) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}

	window.onload = function (){ cambiaCodigos(); document.form.num_cia.select(); };
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Totales de Conceptos Bancarios <br>
    {titulo}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="50%" align="center" class="print">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="4" class="print" scope="col"><font size="+1">{num_cia} - {nombre_cia}</font> </th>
    </tr>
    <tr>
      <th colspan="2" class="print">Concepto</th>
      <th class="print">Total Abonos</th>
      <th class="print">Total Cargos </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" onClick="desgloce({num_cia},{cod_mov},'{fecha1}','{fecha2}')">
      <td width="5%" class="rprint">{cod_mov}</td>
      <td width="45%" class="vprint">{descripcion}</td>
      <td width="25%" class="rprint"><font color="#0000FF">{total_abono}</font></td>
      <td width="25%" class="rprint"><font color="#FF0000">{total_cargo}</font></td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="rprint">Total</th>
      <th class="rprint_total"><font color="#0000FF">{total_abono}</font></th>
      <th class="rprint_total"><font color="#FF0000">{total_cargo}</font></th>
    </tr>
    <tr>
      <th colspan="2" class="rprint">Total General </th>
      <th colspan="2" class="print_total">{total_general}</th>
    </tr>
    <tr>
      <td colspan="4">&nbsp;</td>
    </tr>
	<!-- END BLOCK : cia -->
	<!-- START BLOCK : gran_total -->
	<tr>
      <th colspan="2" class="rprint">Gran Total </th>
      <th colspan="2" class="print_total" style="font-size:12pt;">{total}</th>
    </tr>
	<!-- END BLOCK : gran_total -->
	<!-- START BLOCK : no_result -->
	<th class="tabla">No hay resultados</th>
	<!-- END BLOCK : no_result -->
  </table>
  <p align="center">
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_con_tot.php'">
  </p>
    <script language="javascript" type="text/javascript">
	function desgloce(num_cia, cod_mov, fecha1, fecha2) {
		var url = "./ban_con_des.php?num_cia=" + num_cia + "&cod_mov=" + cod_mov + "&fecha1=" + escape(fecha1) + "&fecha2=" + escape(fecha2);
		var ventana = window.open(url,"desgloce","left=144,top=192,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=640,height=480");
		//ventana.moveTo(192, 144);
		ventana.focus();
	}
  </script>
    <!-- END BLOCK : listado -->
</body>
</html>
