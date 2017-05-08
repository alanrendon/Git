<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="/styles/listado.css" rel="stylesheet" type="text/css">
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

<input name="tipo_con1" type="hidden" value="4">
<input name="temp" type="hidden" value="0">
<input name="tipo_con2" type="hidden" value="0">

  <table class="tabla">
    <tr class="tabla">
      <th class="tabla" colspan="3">Consultar por</th>
    </tr>
    <tr class="tabla">
      <td class="tabla" colspan="3">
        <label>
        <input type="radio" name="tipo_con" value="0" onChange="document.form.tipo_con1.value=0; num_arrendatario.style.visibility='visible'; num_arrendador.style.visibility='hidden'; mes.style.visibility='hidden'; anio.style.visibility='hidden'; importe.style.visibility='hidden'; bloque.style.visibility='hidden'; form.num_arrendatario.select();">
      Arrendatario</label>
        <input name="num_arrendatario" type="text" class="insert" id="num_arrendatario" size="5" maxlength="3" style="visibility:hidden ">
		&nbsp;&nbsp;
        <label>
        <input type="radio" name="tipo_con" value="1" onChange="document.form.tipo_con1.value=1; num_arrendador.style.visibility='visible'; num_arrendatario.style.visibility='hidden'; mes.style.visibility='hidden'; anio.style.visibility='hidden'; importe.style.visibility='hidden'; bloque.style.visibility='hidden';form.num_arrendador.select();">
      Arrendador</label>
	  <input name="num_arrendador" type="text" class="insert" id="num_arrendador" size="5" maxlength="3" style="visibility:hidden ">
		
      </td>
    </tr>
	
	<tr class="tabla">
	  <th class="vtabla">
        <label>
        <input name="tipo_con" type="radio" value="2" onChange="document.form.tipo_con1.value=2; mes.style.visibility='visible'; anio.style.visibility='visible'; num_arrendatario.style.visibility='hidden'; num_arrendador.style.visibility='hidden'; importe.style.visibility='hidden'; bloque.style.visibility='hidden'; form.anio.select();">
      Contrato </label>
	  
	  </th>
	  <th class="vtabla">
        <label>
        <input name="tipo_con" type="radio" value="3" onChange="document.form.tipo_con1.value=3; importe.style.visibility='visible'; num_arrendatario.style.visibility='hidden'; num_arrendador.style.visibility='hidden'; mes.style.visibility='hidden'; anio.style.visibility='hidden'; bloque.style.visibility='hidden'; form.importe.select();">
      Renta </label>
	  
	  </th>
	  <th class="vtabla">
	  <label>
	  <input name="tipo_con" type="radio" value="5" onChange="document.form.tipo_con1.value=5; importe.style.visibility='hidden'; num_arrendatario.style.visibility='hidden'; num_arrendador.style.visibility='hidden'; mes.style.visibility='hidden'; anio.style.visibility='hidden'; bloque.style.visibility='visible';">
	  Bloque</label></th>
	</tr>
	<tr class="tabla">
	  <td class="vtabla">
	  	  Mes<select name="mes" class="insert" style="visibility:hidden ">
		  <!-- START BLOCK : meses -->
	  	    <option value="{mes}">{nombre_mes}</option>
		  <!-- END BLOCK : meses -->
	  	  </select> Año <input name="anio" type="text" class="insert" style="visibility:hidden " value="{anio_actual}" size="3">
		  <br>
		    <label>
		    <input name="contrato" type="radio" value="0" checked onChange="form.tipo_con2.value=0;">
  Inicio de contrato</label>
		    <br>
		    <label>
		    <input type="radio" name="contrato" value="1" onChange="form.tipo_con2.value=1;">
  Fin de contrato</label>
	  </td>
	  <td class="tabla">Monto <input type="text" name="importe" class="insert" size="3" style="visibility:hidden ">
	  
	  </td>
	  <td class="tabla"><select name="bloque" class="insert" id="bloque" style="visibility:hidden ">
	    <option value="0">Internos</option>
	    <option value="1">Externos</option>
	    </select></td>
	</tr>
	
    <tr class="tabla">
      <td class="tabla" colspan="3">
        <label>
        <input name="tipo_con" type="radio" value="4" checked onChange="document.form.tipo_con1.value=4; num_arrendatario.style.visibility='hidden'; num_arrendador.style.visibility='hidden'; mes.style.visibility='hidden'; anio.style.visibility='hidden'; importe.style.visibility='hidden'; bloque.style.visibility='hidden';">
      <strong>Todos los arrendatarios</strong> </label>
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
<td align="center" valign="top"><p class="listado_encabezado"><strong>LISTADO DE ARRENDATARIOS {mensaje}</strong></p>

  <table class="listado">
  <tr>
    <th class="listado"><strong>ARRENDATARIO</strong></th>
    <th class="listado"><strong>LOCAL</strong></th>
    <th class="listado"><strong>BLOQUE</strong></th>
<!--    <th class="listado"><strong>DIRECCION</strong></th> -->
    <th class="listado"><strong>REPRESENTANTE</strong></th>
    <th class="listado"><strong>AVAL</strong></th>
    <th class="listado"><strong>DIRECCION <br> AVAL</strong></th> 
    <th class="listado"><strong>R.F.C.</strong></th>
    <th class="listado"><strong>GIRO</strong></th>
    <th class="listado"><strong>INICIO<br> 
      CONTRATO </strong></th>
    <th class="listado"><strong>FIN<br> 
      CONTRATO</strong></th>
    <th class="listado"><strong>RENTA</strong></th>
    <th class="listado"><strong>AGUA</strong></th>
    <th class="listado"><strong>MANT.</strong></th>
    <th class="listado"><strong>DIRECCION <br>FISCAL</strong> </th> 
    <th class="listado"><strong>TIPO <br>PERSONA </strong></th>
    <th class="listado"><strong>RECIBO <br>MENSUAL</strong></th>
    <th class="listado"><strong>IVA <br>RET</strong></th>
    <th class="listado"><strong>ISR<br>RET</strong></th>
  </tr>
	<!-- START BLOCK : arrendador -->
  <tr>
    <th colspan="18" class="vlistado">{cod_arrendador}&#8212; {arrendador}</th>
  </tr>
<!-- START BLOCK : arrendatarios -->  
  <tr>
    <td class="vlistado">{cod_arrendatario} &#8212;{nombre_arrendatario}</td>
    <td class="vlistado">{local}</td>
    <td class="listado">{bloque}</td>
<!--    <td class="vlistado">{direccion}</td> -->
    <td class="vlistado">{representante}</td>
    <td class="vlistado">{aval}</td>
    <td class="vlistado">{direccion_aval}</td> 
    <td class="listado">{rfc}</td>
    <td class="vlistado">{giro}</td>
    <td class="listado">{fecha_inicio}</td>
    <td class="listado">{fecha_final}</td>
    <td class="listado">{con_recibo}</td>
    <td class="listado">{agua}</td>
    <td class="listado">{mantenimiento}</td>
    <td class="vlistado">{dir_fiscal}</td> 
    <td class="listado">{persona}</td>
    <td class="listado">{mensual}</td>
    <td class="listado">{iva_ret}</td>
    <td class="listado">{isr_ret}</td>
  </tr>
<!-- END BLOCK : arrendatarios -->
<!-- END BLOCK : arrendador -->	
</table>



</td>
</tr>
</table>
<!-- END BLOCK : todos -->