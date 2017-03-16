<!-- START BLOCK : obtener_datos -->
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/listado.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CONSUMO AVIO CONTROLADO </P>
<form name="form" method="get" action="./bal_avio_con.php">
<input name="temp" type="hidden" value="">
  <table class="tabla">
  <tr class="tabla">
  <th class="tabla" colspan="2">Mes 
		<select name="mes" size="1" class="insert" id="mes">
        <!-- START BLOCK : mes -->
	    <option value="{num_mes}" {checked}>{nom_mes}</option>
        <!-- END BLOCK : mes -->
		</select>
		
    A&ntilde;o
    <input name="anio" type="text" class="insert" value="{anio_actual}" size="5"> </th>
  </tr>
</table>
  <p>
  <input type="button" name="enviar" class="boton" value="Consultar" onclick='document.form.submit();'>
  </p>
</form>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="98%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="listado_encabezado"><strong>OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</strong></p>
<p class="listado_encabezado">CONSUMOS DEL MES DE {mes} DEL {anio}</p>
<table cellpadding="0" cellspacing="0" class="listado">
  <tr class="listado">
    <th class="listado" colspan="2" rowspan="2">COMPA&Ntilde;&Iacute;A</th>
    <th class="listado" colspan="2">HARINA</th>
    <th class="listado" colspan="2">AZÚCAR<br>REFINADA</th>
    <th class="listado" colspan="2">AZÚCAR<br>ESTANDAR</th>
    <th class="listado" colspan="2">CHANTILLY</th>
    <th class="listado" colspan="2">GRASA</th>
    <th class="listado" colspan="2">MANTEQUILLA<br>FINA</th>
    <th class="listado" colspan="2">MANTEQUILLA<br>OJALDRE</th>
  </tr>
  <tr>

    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
  </tr>
  
  <!-- START BLOCK : rows -->
  <tr>
    <th class="rlistado">{num_cia}</th>
    <th class="vlistado">{nombre}</th>
    <td class="rlistado">{harina_ent}</td>
    <td class="rlistado">{harina_sal}</td>
    <td class="rlistado">{az1_ent}</td>
    <td class="rlistado">{az1_sal}</td>
    <td class="rlistado">{az2_ent}</td>
    <td class="rlistado">{az2_sal}</td>
    <td class="rlistado">{cha_ent}</td>
    <td class="rlistado">{cha_sal}</td>
    <td class="rlistado">{gra_ent}</td>
    <td class="rlistado">{gra_sal}</td>
    <td class="rlistado">{man1_ent}</td>
    <td class="rlistado">{man1_sal}</td>
    <td class="rlistado">{man2_ent}</td>
    <td class="rlistado">{man2_sal}</td>
  </tr>
  <!-- END BLOCK : rows -->
  <tr>
    <td colspan="2" class="rlistado" rowspan="2"><strong>TOTALES:</strong></td>
    <td class="rlistado"><strong>{total_harina_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_az1_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_az2_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_cha_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_gra_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_man1_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_man2_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
  </tr>
  <tr>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_harina_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_az1_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_az2_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_cha_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_gra_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_man1_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_man2_sal}</strong></td>
  </tr>
  
</table></td>
</tr>
</table>
{salto}

<table width="100%"  height="98%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="listado_encabezado"><strong>OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</strong></p>
<p class="listado_encabezado">CONSUMOS DEL MES DE {mes} DEL {anio}</p>

<table cellpadding="0" cellspacing="0" class="listado">
  <tr class="listado">
    <th class="listado" colspan="2" rowspan="2">COMPA&Ntilde;&Iacute;A</th>
    <th class="listado" colspan="2">MARGARINA<br>REAL</th>
    <th class="listado" colspan="2">PORCINA</th>
    <th class="listado" colspan="2">ACEITE</th>
    <th class="listado" colspan="2">HUEVO</th>
    <th class="listado" colspan="2">LEVADURA</th>
    <th class="listado" colspan="2">GAS</th>
  </tr>
  <tr>

    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Entrada&nbsp;&nbsp;&nbsp;</td>
    <td class="listado">&nbsp;&nbsp;&nbsp;Salida&nbsp;&nbsp;&nbsp;</td>
  </tr>
  
  <!-- START BLOCK : rows1 -->
  <tr>
    <th class="rlistado">{num_cia}&nbsp;&nbsp;</th>
    <th class="vlistado">{nombre}</th>
    <td class="rlistado">{mar_ent}</td>
    <td class="rlistado">{mar_sal}</td>
    <td class="rlistado">{por_ent}</td>
    <td class="rlistado">{por_sal}</td>
    <td class="rlistado">{ace_ent}</td>
    <td class="rlistado">{ace_sal}</td>
    <td class="rlistado">{hue_ent}</td>
    <td class="rlistado">{hue_sal}</td>
    <td class="rlistado">{lev_ent}</td>
    <td class="rlistado">{lev_sal}</td>
    <td class="rlistado">{gas_ent}</td>
    <td class="rlistado">{gas_sal}</td>
  </tr>
  <!-- END BLOCK : rows1 -->
  <tr>
    <td colspan="2" class="rlistado" rowspan="2"><strong>TOTALES:</strong></td>
    <td class="rlistado"><strong>{total_mar_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_por_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_ace_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_hue_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_lev_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_gas_ent}</strong></td>
    <td class="rlistado">&nbsp;</td>
  </tr>
    <tr>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_mar_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_por_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_ace_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_hue_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_lev_sal}</strong></td>
    <td class="rlistado">&nbsp;</td>
    <td class="rlistado"><strong>{total_gas_sal}</strong></td>
  </tr>

</table>


</td>
</tr>
</table>



<!-- END BLOCK : listado -->
