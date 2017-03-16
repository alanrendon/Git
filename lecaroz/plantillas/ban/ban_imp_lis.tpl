<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Distribuci&oacute;n de Impuestos</p>
  <form action="./ban_imp_lis.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <td class="vtabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3">
        <input name="fil" type="checkbox" id="fil" value="1">Incluir filiales </td>
    </tr>
    <tr>
      <th class="vtabla">Mes</th>
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
      </select></td>
    </tr>
    <tr>
      <th class="vtabla">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.anio) < 2000) {
		alert("Debe especificar el año de consulta");
		f.anio.select();
		return false;
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
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
    <td width="60%" class="print_encabezado" align="center">Distribuci&oacute;n de Impuestos<br>
    del mes de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="100%" align="center" class="print">
    <tr>
      <th rowspan="2" class="print" scope="col">Cia</th>
      <th rowspan="2" class="print" scope="col">ISR</th>
      <!-- <th rowspan="2" class="print" scope="col">IETU</th> -->
      <th rowspan="2" class="print" scope="col">IEPS<br>Gravado</th>
      <th rowspan="2" class="print" scope="col">IEPS<br>Acreditable</th>
      <th rowspan="2" class="print" scope="col">Retenci&oacute;n<br>ISR<br>Renta</th>
      <th rowspan="2" class="print" scope="col">Retenci&oacute;n<br>ISR<br>Honorarios</th>
      <th rowspan="2" class="print" scope="col">Retenci&oacute;n<br>Honorarios<br>Consejo</th>
      <!-- <th rowspan="2" class="print" scope="col">Cr&eacute;dito<br>al<br>Salario</th> -->
      <th rowspan="2" class="print" scope="col">Subsidio<br>al<br>Empleo</th>
      <th rowspan="2" class="print" scope="col">Total<br>ISR<br>Pagar</th>
      <th rowspan="2" class="print" scope="col">Retenci&oacute;n<br>IVA<br>Honorarios</th>
      <th rowspan="2" class="print" scope="col">Retenci&oacute;n<br>IVA<br>Renta</th>
      <th rowspan="2" class="print" scope="col">Retenci&oacute;n<br>IVA<br>Fletes</th>
      <th rowspan="2" class="print" scope="col">Total<br>Ret IVA<br>a Pagar</th>
      <th colspan="2" class="print" scope="col">IVA</th>
      <th rowspan="2" class="print" scope="col">IVA<br>a<br>Declarar</th>
      <th rowspan="2" class="print" scope="col">Acumulado<br>anual</th>
    </tr>
    <tr>
      <th class="print" scope="col">Trasladado</th>
      <th class="print" scope="col">Acreditable</th>
    </tr>
	<!-- START BLOCK : bloque -->
  <!-- START BLOCK : fila -->
	  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint">{num_cia} {nombre_corto} </td>
      <td class="rprint">{isr}</td>
      <!-- <td class="rprint">{ietu}</td> -->
      <td class="rprint">{ieps_gravado}</td>
      <td class="rprint">{ieps_excento}</td>
      <td class="rprint">{ret_isr_ren}</td>
      <td class="rprint">{ret_isr_hon}</td>
      <td class="rprint">{ret_hon_con}</td>
      <td class="rprint">{cre_sal}</td>
      <td class="rprint"><strong style="color:#C00;">{isr_pago}</strong></td>
      <td class="rprint">{ret_iva_hon}</td>
      <td class="rprint">{ret_iva_ren}</td>
      <td class="rprint">{ret_iva_fle}</td>
      <td class="rprint"><strong style="color:#C00;">{iva_pago}</strong></td>
      <td class="rprint">{iva_tras}</td>
      <td class="rprint">{iva_acre}</td>
      <td class="rprint"><strong style="color:#C00;">{iva_dec}</strong></td>
      <td class="rprint">{acu_anual}</td>
	  </tr>
	<!-- END BLOCK : fila -->
  <!-- START BLOCK : totales -->
	  <tr>
      <th class="rprint">Totales</th>
      <th class="rprint">{isr}</th>
      <!-- <th class="rprint">{ietu}</th> -->
      <th class="rprint">{ieps_gravado}</th>
      <th class="rprint">{ieps_excento}</th>
      <th class="rprint">{ret_isr_ren}</th>
      <th class="rprint">{ret_isr_hon}</th>
      <th class="rprint">{ret_hon_con}</th>
      <th class="rprint">{cre_sal}</th>
      <th class="rprint">{isr_pago}</th>
      <th class="rprint">{ret_iva_hon}</th>
      <th class="rprint">{ret_iva_ren}</th>
      <th class="rprint">{ret_iva_fle}</th>
      <th class="rprint">{iva_pago}</th>
      <th class="rprint">{iva_tras}</th>
      <th class="rprint">{iva_acre}</th>
      <th class="rprint">{iva_dec}</th>
      <th class="rprint">{acu_anual}</th>
    </tr>
	<tr>
	  <td colspan="15" class="print">&nbsp;</td>
	</tr>
	<!-- END BLOCK : totales -->
	<!-- END BLOCK : bloque -->
	<!-- START BLOCK : gtotales -->
	<tr>
      <th class="rprint">Gran Total</th>
      <th class="rprint">{isr}</th>
      <!-- <th class="rprint">{ietu}</th> -->
      <th class="rprint">{ieps_gravado}</th>
      <th class="rprint">{ieps_excento}</th>
      <th class="rprint">{ret_isr_ren}</th>
      <th class="rprint">{ret_isr_hon}</th>
      <th class="rprint">{ret_hon_con}</th>
      <th class="rprint">{cre_sal}</th>
      <th class="rprint">{isr_pago}</th>
      <th class="rprint">{ret_iva_hon}</th>
      <th class="rprint">{ret_iva_ren}</th>
      <th class="rprint">{ret_iva_fle}</th>
      <th class="rprint">{iva_pago}</th>
      <th class="rprint">{iva_tras}</th>
      <th class="rprint">{iva_acre}</th>
      <th class="rprint">{iva_dec}</th>
      <th class="rprint">{acu_anual}</th>
    </tr>
	<!-- END BLOCK : gtotales -->
  </table>
  {salto}
<!-- END BLOCK : listado -->
</body>
</html>
