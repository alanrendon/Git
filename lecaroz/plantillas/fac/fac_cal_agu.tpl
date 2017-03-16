<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./styles/tablas.css" rel="stylesheet" type="text/css">
<link href="./styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Calcular Aguinaldo</p>
  <form action="" method="get" name="calc" onSubmit="return false"><table class="tabla">
    <tr>
      <th class="tabla" scope="col">Puesto</th>
      <th class="tabla" scope="col">Antig&uuml;edad</th>
      <th class="tabla" scope="col">Aguinaldo</th>
    </tr>
    <tr>
      <td class="tabla"><select name="puesto" class="insert" id="puesto" onChange="calcula_aguinaldo()">
        <!-- START BLOCK : puesto -->
		<option value="{sueldo}">{cod_puestos} - {descripcion}</option>
		<!-- END BLOCK : puesto -->
      </select></td>
      <td class="tabla"><input name="anios" type="text" class="insert" id="anios" size="2" maxlength="2" onChange="calcula_aguinaldo()">
        A&ntilde;os 
          <input name="meses" type="text" class="insert" id="meses" size="2" maxlength="2" onChange="calcula_aguinaldo()">
          Meses</td>
      <td class="tabla"><input name="aguinaldo" type="text" class="insert" id="aguinaldo" value="0.00" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <td colspan="3" class="tabla"><input name="bill" type="checkbox" id="bill" value="1000" onClick="calcula_aguinaldo()" checked>
        1000 
          <input name="bill" type="checkbox" id="bill" value="500" onClick="calcula_aguinaldo()" checked>
          500 
          <input name="bill" type="checkbox" id="bill" value="200" onClick="calcula_aguinaldo()" checked>
          200 
          <input name="bill" type="checkbox" id="bill" value="100" onClick="calcula_aguinaldo()" checked>
          100 
          <input name="bill" type="checkbox" id="bill" value="50" onClick="calcula_aguinaldo()" checked>
          50 
          <input name="bill" type="checkbox" id="bill" value="20" onClick="calcula_aguinaldo()" checked>
          20</td>
      </tr>
  </table>  <p>&nbsp;</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
var calc = document.calc;

function calcula_aguinaldo() {
	var anios = !isNaN(parseInt(calc.anios.value)) ? parseInt(calc.anios.value) : 0;
	var meses = !isNaN(parseInt(calc.meses.value)) ? parseInt(calc.meses.value) : 0;
	var sueldo = !isNaN(parseFloat(calc.puesto.value)) ? parseFloat(calc.puesto.value) : 0;
	
	var aguinaldo = 0;
	var vacaciones = 0;
	
	if (anios <= 1) {
		aguinaldo = 0.80 * (15 / 12 * (sueldo * (anios == 1 ? 12 : meses)));
	}
	else if (anios > 1) {
		aguinaldo = sueldo * 15;
	}
	
	if (anios == 1 && meses > 0) {
		vacaciones = (7 + ((3 / 12) * meses)) * sueldo;
	}
	else if (anios == 2) {
		vacaciones = (10 + ((3 / 12) * meses)) * sueldo;
	}
	else if (anios == 3) {
		vacaciones = (12 + ((3 / 12) * meses)) * sueldo;
	}
	else if (anios > 3) {
		vacaciones = (15 + ((anios - 4) / 5) * 3) * sueldo;
	}
	
	var total_aguinaldo = Math.round((aguinaldo + vacaciones) * 1.10);
	
	var bill = new Array();
	var count = 0;
	for (i = 0; i < calc.bill.length; i++) {
		if (calc.bill[i].checked) {
			bill[count] = parseInt(calc.bill[i].value);
			count++;
		}
	}
	
	var residuo = total_aguinaldo;
	for (i = 0; i < bill.length; i++)
		if (residuo % bill[i] > 0)
			residuo = residuo % bill[i];
		else
			break;
	
	if (residuo > 0)
		total_aguinaldo = residuo < bill[bill.length - 1] / 2 ? total_aguinaldo - residuo : total_aguinaldo + bill[bill.length - 1] - residuo;
	
	calc.aguinaldo.value = total_aguinaldo.toFixed(2);
}
</script>
</body>
</html>
