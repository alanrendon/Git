<table class="tabla_captura">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="15" align="left" class="font14" scope="col">{num_cia} {nombre_cia}</th>
    <th scope="col"><img src="imagenes/tool16x16.png" width="16" height="16" /></th>
  </tr>
  <tr>
  	<th>Tipo</th>
    <th>Serie</th>
    <th>Folio<br />
    Inicial</th>
    <th>Folio<br />
    Final</th>
    <th>Folio<br>
    	Actual</th>
    <th>Número de<br>
    	Aprobación</th>
    <th>Fecha de <br />
    Aprobaci&oacute;n</th>
    <th>A&ntilde;o de<br />
    Aprobaci&oacute;n</th>
    <th>Serie del<br>
    	Certificado</th>
    <th>Archivo<br />
    Certificado</th>
    <th>Contraseña<br>
    	Certificado</th>
    <th>Archivo<br />
    Llave</th>
    <th>Contrase&ntilde;a<br>
    	Llave</th>
    <th>Tipo CFD</th>
    <th>Estatus</th>
    <th><img src="imagenes/insert16x16.png" name="alta" width="16" height="16" id="alta" /></th>
  </tr>
  <!-- START BLOCK : row -->
  <tr id="row" class="linea_{color}">
  	<td>{tipo_serie}</td>
    <td>{serie}</td>
    <td align="right" class="blue">{folio_inicial}</td>
    <td align="right" class="blue">{folio_final}</td>
    <td align="right" class="green">{folio_actual}</td>
    <td align="right">{no_aprobacion}</td>
    <td align="center">{fecha_aprobacion}</td>
    <td align="center">{anio_aprobacion}</td>
    <td align="right">{serie_certificado}</td>
    <td class="orange">{archivo_certificado}</td>
    <td class="orange">{contrasenia_certificado}</td>
    <td class="purple">{archivo_llave}</td>
    <td class="purple">{contrasenia_llave}</td>
    <td>{tipo_cfd}</td>
    <td>{status}</td>
    <td align="center"><img src="imagenes/pencil16x16.png" alt="{id}" name="modificar" width="16" height="16" id="modificar" /><img src="iconos/cancel.png" alt="{id}" name="baja" width="16" height="16" id="baja" /></td>
  </tr>
  <!-- END BLOCK : row -->
  <tr>
    <td colspan="16">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
</table>
<p>
  <input type="submit" name="regresar" id="regresar" value="Regresar" />
</p>