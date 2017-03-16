<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Comparativo de Consumos</p>
  <form action="./bal_com_con_anu.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) codmp.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Producto</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod()" onkeydown="if (event.keyCode == 13) anio1.select()" size="3" />
        <input name="nombre_mp" type="text" disabled="disabled" class="vnombre" id="nombre_mp" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;os</th>
      <td class="vtabla"><input name="anio1" type="text" class="insert" id="anio1" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) anio2.select()" value="{anio1}" size="4" maxlength="4" />
        y
          <input name="anio2" type="text" class="insert" id="anio2" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{anio2}" size="4" maxlength="4" /></td>
    </tr>
  </table>  <p>
    <input name="" type="button" class="boton" onclick="validar()" value="Siguiente" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaCia() {
}

function cambiaCod() {
}

function validar() {
	if (get_val(f.num_cia) <= 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
	}
	else if (get_val(f.anio1) <= 2000 || get_val(f.anio2) <= 2000) {
		alert('Debe especificar los años de consulta');
		f.anio1.select();
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<style type="text/css" media="print">
input {
	display: none;
}
</style>
<table width="100%">
  <tr>
    <td class="print_encabezado">{num_cia}</td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="rprint_encabezado">{num_cia}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Comparativo de Consumos Anual <br />
    A&ntilde;os {anio1} y {anio2} Mes de {mes} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Producto</th>
    <th class="print" scope="col">{anio1}</th>
    <th class="print" scope="col">{anio2}</th>
    <th class="print" scope="col">Diferencia</th>
    <th class="print" scope="col">%</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint" style="font-weight:bold">{codmp} {nombre} </td>
    <td class="rprint">{consumo1}</td>
    <td class="rprint">{consumo2}</td>
    <td class="rprint" style="color:#{color}">{dif}</td>
    <td class="rprint" style="color:#{color}">{por}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<p align="center">
<input type="button" class="boton" value="Regresar" onclick="document.location='bal_com_con_anu.php'" />
</p>
<!-- END BLOCK : listado -->
</body>
</html>
