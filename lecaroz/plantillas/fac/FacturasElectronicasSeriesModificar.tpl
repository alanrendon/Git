<form action="" method="get" name="Datos" class="FormValidator FormStyles" id="Datos">
	 <table class="tabla_captura">
  		<tr class="linea_off">
  			<th align="left" scope="row">Compa&ntilde;&iacute;a</th>
  			<td><input name="id" type="hidden" id="id" value="{id}">
			{num_cia} {nombre_cia}</td>
		 </tr>
  		<tr class="linea_on">
  			<th align="left" scope="row">Tipo</th>
  			<td><input name="tipo_serie" type="radio" id="radio" value="1"{tipo_serie_1} />
  			Factura<br />
  			<input type="radio" name="tipo_serie" id="radio2" value="2"{tipo_serie_2} />
  			Recibo de arrendamiento<br />
  			<input type="radio" name="tipo_serie" id="radio3" value="3"{tipo_serie_3} />
  			Nota de cr&eacute;dito</td>
  			</tr>
  		<tr class="linea_off">
  			<th align="left" scope="row">Serie</th>
  			<td><input name="serie" type="text" class="valid onlyLetters toUpper" id="serie" value="{serie}" size="10" maxlength="10" /></td>
		 </tr>
  		<tr class="linea_on">
  			<th align="left" scope="row">Folio inicial</th>
  			<td><input name="folio_inicial" type="text" class="valid Focus toPosInt right" id="folio_inicial" value="{folio_inicial}" size="10" /></td>
		 </tr>
  		<tr class="linea_off">
  			<th align="left" scope="row">Folio final</th>
  			<td><input name="folio_final" type="text" class="valid Focus toPosInt right" id="folio_final" value="{folio_final}" size="10" /></td>
		 </tr>
  		<tr class="linea_on">
  			<th align="left" scope="row">Folio actual</th>
  			<td><input name="folio_actual" type="text" class="valid Focus toPosInt right" id="folio_actual" value="{folio_actual}" size="10" /></td>
		 </tr>
  		<tr class="linea_off">
  			<th align="left" scope="row">N&uacute;mero de aprobaci&oacute;n</th>
  			<td><input name="no_aprobacion" type="text" class="valid Focus toPosInt right" id="no_aprobacion" value="{no_aprobacion}" size="14" maxlength="14" /></td>
		 </tr>
  		<tr class="linea_on">
  			<th align="left" scope="row">Fecha de aprobaci&oacute;n</th>
  			<td><input name="fecha_aprobacion" type="text" class="valid Focus toDate center" id="fecha_aprobacion" value="{fecha_aprobacion}" size="10" maxlength="10" /></td>
		 </tr>
  		<tr class="linea_off">
  			<th align="left" scope="row">A&ntilde;o de aprobaci&oacute;n</th>
  			<td><input name="anio_aprobacion" type="text" class="valid Focus toPosInt center" id="anio_aprobacion" value="{anio_aprobacion}" size="4" maxlength="4" /></td>
		 </tr>
  		<tr class="linea_on">
  			<th align="left" scope="row">Serie del cettificado</th>
  			<td><input name="serie_certificado" type="text" class="valid onlyNumbers" id="serie_certificado" value="{serie_certificado}" size="20" maxlength="20" /></td>
		 </tr>
  		<tr class="linea_off">
  			<th align="left" scope="row">Archivo certificado</th>
  			<td><input name="archivo_certificado" type="text" class="valid toText" id="archivo_certificado" value="{archivo_certificado}" size="40" /></td>
		 </tr>
  		<tr class="linea_on">
  			<th align="left" scope="row">Contrase&ntilde;a certificado</th>
  			<td><input name="contrasenia_certificado" type="text" class="valid toText" id="contrasenia_certificado" value="{contrasenia_certificado}" size="30" /></td>
		 </tr>
  		<tr class="linea_off">
  			<th align="left" scope="row">Archivo llave</th>
  			<td><input name="archivo_llave" type="text" class="valid toText" id="archivo_llave" value="{archivo_llave}" size="40" /></td>
		 </tr>
  		<tr class="linea_on">
  			<th align="left" scope="row">Contrase&ntilde;a llave</th>
  			<td><input name="contrasenia_llave" type="text" class="valid toText" id="contrasenia_llave" value="{contrasenia_certificado}" size="30" /></td>
		 </tr>
     <tr class="linea_off">
        <th align="left" scope="row">Tipo CFD</th>
        <td><input name="tipo_cfd" type="radio" value="1"{tipo_cfd_1} />
        	CFD
       		<input type="radio" name="tipo_cfd" value="2"{tipo_cfd_2} />
       		CFDI</td>
     </tr>
		</table>
   <p>
	 	<input type="button" name="regresar" id="regresar" value="Regresar" />
	 &nbsp;&nbsp;
	 <input type="button" name="modificar" id="modificar" value="Modificar" />
	 </p>
		</form>