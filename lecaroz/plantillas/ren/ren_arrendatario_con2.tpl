<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida_registro(){
	if(document.form.tipo_con1.value==0 && document.form.num_arrendatario.value == ""){
		alert("Revise el Arrendatario");
		document.form.num_arrendatario.select();
		return;
	}
	else
		document.form.submit();
}
</script>

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Arrendatarios</p>

<form name="form" action="./ren_arrendatario_con.php" method="get">
<input name="tipo_con1" type="hidden" value="1">

  <table class="tabla">
    <tr class="tabla">
      <th class="tabla" colspan="2">Consultar por</th>
    </tr>
    <tr class="tabla">
      <td class="tabla">
        <label>
        <input type="radio" name="tipo_con" value="0" onChange="document.form.tipo_con1.value=0">
      Arrendatario</label>
        <input name="num_arrendatario" type="text" class="insert" id="num_arrendatario" size="5" maxlength="3">
      </td>
      <td class="tabla">
        <label>
        <input name="tipo_con" type="radio" value="1" checked onChange="document.form.tipo_con1.value=1">
      Todos los arrendatarios </label>
      </td>
    </tr>
  </table>
  <p>
  <input type="button" name="enviar" value="Enviar" onClick="valida_registro();" class="boton">
  </p>
</form> 
</td>
</tr>
</table>

<!-- END BLOCK : obtener_datos -->



<!-- START BLOCK : por_arrendatario -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Arrendatarios</p>
    <table class="tabla">
      <tr>
        <th class="vtabla" scope="row">Arrendatario </th>
        <td class="vtabla">{cod_arrendatario} {nombre_arrendatario} </td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Local</th>
        <td class="vtabla">{local}          </td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Tipo de Bloque </th>
        <td class="vtabla">{bloque}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Direcci&oacute;n</th>
        <td class="vtabla">{direccion}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Arrendador</th>
        <td class="vtabla">{nombre_arrendador}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Representante</th>
        <td class="vtabla">{representante}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Nombre del aval </th>
        <td class="vtabla">{aval}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Direcci&oacute;n del aval </th>
        <td class="vtabla">{direccion_aval}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Direcci&oacute;n Fiscal </th>
        <td class="vtabla">{dir_fiscal}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">R.F.C.</th>
        <td class="vtabla">{rfc}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Giro</th>
        <td class="vtabla">{giro}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Fecha inicio de contrato </th>
        <td class="vtabla">{fecha_inicio}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Fecha final de contrato </th>
        <td class="vtabla">{fecha_final}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Renta con recibo </th>
        <td class="vtabla">{con_recibo}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Renta sin recibo </th>
        <td class="vtabla">{sin_recibo}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Cuota de agua </th>
        <td class="vtabla">{agua}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Cuota de mantenimiento </th>
        <td class="vtabla">{mantenimiento}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Rentas en dep&oacute;sito </th>
        <td class="vtabla">{rentas}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Cargo por da&ntilde;os </th>
        <td class="vtabla">{danos}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Cargo contrato terminado </th>
        <td class="vtabla">{terminado}</td>
      </tr>
	  
      <tr>
        <th class="vtabla" scope="row">Incremento anual </th>
        <td class="vtabla">{incremento}</td>
      </tr>


      <tr>
        <th class="vtabla" scope="row">Retenci&oacute;n I.S.R. </th>
        <td class="vtabla">{isr_ret}
		</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Retenci&oacute;n I.V.A. </th>
        <td class="vtabla">{iva_ret}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Fianza</th>
        <td class="vtabla">{fianza}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Tipo persona </th>
        <td class="vtabla">{persona}</td>
      </tr>
      <tr>
        <th class="vtabla" scope="row">Impresion mensual de recibos </th>
        <td class="vtabla">{recibos_mensual}</td>
      </tr>
    </table>
 	<p>
	<input type="button" name="regresar" class="boton" onClick="document.location = './ren_arrendatario_con.php'" value="Regresar">
	</p>
</td>
</tr>
</table>
<!-- END BLOCK : por_arrendatario -->

<!-- START BLOCK : todos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top"><p class="print_encabezado"><strong>CONSULTA DE ARRENDATARIOS</strong></p>
<!-- 
  <table class="print">
    <tr>
      <th class="vprint" scope="row">Arrendatario </th>
      <td colspan="3" class="vprint">{cod_arrendatario} {nombre_arrendatario} </td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Local</th>
      <td colspan="3" class="vprint">{local} </td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Tipo de Bloque </th>
      <td colspan="3" class="vprint">{bloque}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Direcci&oacute;n</th>
      <td colspan="3" class="vprint">{direccion}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Arrendador</th>
      <td colspan="3" class="vprint">{nombre_arrendador}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Representante</th>
      <td colspan="3" class="vprint">{representante}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Nombre del aval </th>
      <td colspan="3" class="vprint">{aval}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Direcci&oacute;n del aval </th>
      <td colspan="3" class="vprint">{direccion_aval}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">R.F.C.</th>
      <td colspan="3" class="vprint">{rfc}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Giro</th>
      <td colspan="3" class="vprint">{giro}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Fecha inicio de contrato </th>
      <td class="vprint">{fecha_inicio}</td>
      <th class="vprint">Fecha final de contrato</th>
      <td class="vprint">{fecha_final}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Renta con recibo </th>
      <td class="vprint">{con_recibo}</td>
      <th class="vprint">Renta sin recibo </th>
      <td class="vprint">{sin_recibo}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Cuota de agua </th>
      <td class="vprint">{agua}</td>
      <th class="vprint">Cuota de mantenimiento </th>
      <td class="vprint">{mantenimiento}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Rentas en dep&oacute;sito </th>
      <td class="vprint">{rentas}</td>
      <th class="vprint">Incremento anual </th>
      <td class="vprint">{incremento}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Cargo por da&ntilde;os </th>
      <td class="vprint">{danos}</td>
      <th class="vprint">Cargo contrato terminado</th>
      <td class="vprint">{terminado}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Retenci&oacute;n I.S.R. </th>
      <td class="vprint">{isr_ret} </td>
      <th class="vprint">Retenci&oacute;n I.V.A. </th>
      <td class="vprint">{iva_ret}</td>
    </tr>
    <tr>
      <th class="vprint" scope="row">Fianza</th>
      <td class="vprint">{fianza}</td>
      <th class="vprint">Tipo persona </th>
      <td class="vprint">{persona}</td>
    </tr>
  </table>
--> 

<table class="print">
  <tr>
    <th class="print"><strong>ARRENDATARIO</strong></th>
    <th class="print"><strong>LOCAL</strong></th>
    <th class="print"><strong>BLOQUE</strong></th>
<!--    <th class="print"><strong>DIRECCION</strong></th> -->
    <th class="print"><strong>REPRESENTANTE</strong></th>
    <th class="print"><strong>AVAL</strong></th>
    <th class="print"><strong>DIRECCION <br> 
      AVAL</strong></th>
    <th class="print"><strong>R.F.C.</strong></th>
    <th class="print"><strong>GIRO</strong></th>
    <th class="print"><strong>INICIO<br> 
      CONTRATO </strong></th>
    <th class="print"><strong>FIN<br> 
      CONTRATO</strong></th>
    <th class="print"><strong>RENTA</strong></th>
    <th class="print"><strong>AGUA</strong></th>
    <th class="print"><strong>MANTENIMIENTO</strong></th>
    </tr>
	<!-- START BLOCK : arrendador -->
  <tr>
    <th colspan="14" class="vprint">{cod_arrendador} {arrendador}</th>
  </tr>
<!-- START BLOCK : arrendatarios -->  
  <tr>
    <td class="vprint">{cod_arrendatario} {nombre_arrendatario}</td>
    <td class="vprint">{local}</td>
    <td class="print">{bloque}</td>
<!--    <td class="vprint">{direccion}</td> -->
    <td class="vprint">{representante}</td>
    <td class="vprint">{aval}</td>
    <td class="vprint">{direccion_aval}</td>
    <td class="print">{rfc}</td>
    <td class="vprint">{giro}</td>
    <td class="print">{fecha_inicio}</td>
    <td class="print">{fecha_final}</td>
    <td class="print">{con_recibo}</td>
    <td class="print">{agua}</td>
    <td class="print">{mantenimiento}</td>
    </tr>
<!-- END BLOCK : arrendatarios -->
<!-- END BLOCK : arrendador -->	
</table>



</td>
</tr>
</table>
<!-- END BLOCK : todos -->