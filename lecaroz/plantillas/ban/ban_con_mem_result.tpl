<form method="post" name="Result" class="formulario" id="Result">
	  <table class="tabla_captura">
      <tr>
        <th scope="col"><input name="checkall" type="checkbox" class="checkbox" id="checkall" checked /></th>
        <th scope="col">Folio</th>
        <th scope="col">Compa&ntilde;&iacute;a</th>
        <th scope="col">Fecha</th>
        <th scope="col">Capturista</th>
        <th scope="col">CCP</th>
        <th scope="col">Memo</th>
        <th scope="col">Fecha Hoja </th>
        <th scope="col"><img src="imagenes/tool16x16.png" width="16" height="16" /></th>
      </tr>
      <!-- START BLOCK : row -->
	  <tr id="row" class="linea_{color}">
	    <td align="right"><input name="id[]" type="checkbox" class="checkbox" id="id" value="{id}" checked /></td>
        <td align="right">{folio}</td>
        <td>{num_cia} {nombre} </td>
        <td align="center">{fecha}</td>
        <td>{capturista}</td>
        <td>{ccp}</td>
        <td>{memo}</td>
        <td align="center">{fecha_hoja}</td>
        <td align="center"><img src="imagenes/WhiteSheet16x16.png" alt="{id}" name="memo" width="16" height="16" id="memo" /></td>
      </tr>
	  <!-- END BLOCK : row -->
    </table>
    <p>
      <input name="cancelar" type="button" class="boton" id="cancelar" value="Cancelar" />
      &nbsp;&nbsp;
    <input name="aclarar" type="button" class="boton" id="aclarar" value="Aclarar" />
    </p>
	</form>