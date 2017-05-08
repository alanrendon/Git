<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- START BLOCK : obtener_dato -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">MODIFICACI&Oacute;N DE PROVEEDORES</p>
	<form action="./fac_prov_mod.php" method="get" name="form">
	<table border="1" class="tabla">
	  <tr class="tabla">
		<th scope="col" class="tabla">Proveedor</th>
	  </tr>
	  <tr class="tabla">
		<td scope="row" class="tabla"><input name="proveedor" type="text" class="insert" id="proveedor" size="4" maxlength="4"></td>
	  </tr>
	</table>
	<p>
	  <input type="button" name="enviar" value="Consultar" class="boton" onClick="if (form.proveedor.value=='') {alert('Debe especificar un número de proveedor'); form.proveedor.select();} else document.form.submit();">
	</p>
	</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.proveedor.select();</script>
<!-- END BLOCK : obtener_dato -->

<!-- START BLOCK : modificar -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() 
	{
		if(document.form.num_proveedor.value <= 0) 
		{
			alert('Debe especificar un numero para el proveedor');
			document.form.num_proveedor.select();
		}
		else if(document.form.nombre.value == "") 
		{
			alert('Debe especificar un nombre para el proveedor');
			document.form.nombre.select();
		}
		else 
		{
			if (confirm("¿Son correctos los datos?"))
				document.form.submit();
			else
				document.form.nombre.select();
		}
	}
	
	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.nombre.select();
		}
		else
			document.form.nombre.select();
	}


</script>


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">MODIFICACION DE DATOS DE PROVEEDOR</p>
<form action="./actualiza_proveedor.php?tabla={tabla}" method="post" name="form" id="form" onKeyDown="if (event.keyCode == 13) valida_registro();">
<table class="tabla">
      <caption class="tabla">Datos del Proveedor</caption>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">N&uacute;mero proveedor</th>
        <td class="tabla">
          <input name="num_proveedor" type="text" readonly class="nombre" id="num_proveedor" value="{id}" size="5" maxlength="5">


        </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Nombre</th>
        <td class="tabla">
          <input name="nombre" type="text" class="insert" id="nombre" value="{nombre}" size="50" maxlength="100">
          </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Direcci&oacute;n</th>
        <td class="tabla">
          <textarea name="direccion" cols="50" rows="3" wrap="VIRTUAL" class="insert" id="direccion">{direccion}</textarea>
        </td>
      </tr>
       <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Tel&eacute;fono 1</th>
        <td class="tabla">
          <input name="telefono1" type="text" class="insert" id="telefono1" value="{telefono1}" size="20" maxlength="20">
        </td>
      </tr>
	  <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Tel&eacute;fono 2</th>
        <td class="tabla">
          <input name="telefono2" type="text" class="insert" id="telefono2" value="{telefono2}" size="20" maxlength="20">
        </td>
      </tr>
	  <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Fax</th>
        <td class="tabla">
          <input name="fax" type="text" class="insert" id="fax" value="{fax}" size="20" maxlength="20">
        </td>
      </tr>
	  <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">e-m@il</th>
        <td class="tabla">
          <input name="email" type="text" class="insert" id="email" value="{email}" size="50" maxlength="50">
        </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">R.F.C.</th>
        <td class="tabla">
          <input name="rfc" type="text" class="insert" id="rfc" value="{rfc}" size="13" maxlength="17">
        </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <td class="tabla" colspan="2">
          <strong>Prioridad del Proveedor</strong><br>
  <select name="prioridad" class="insert" id="prioridad">

    <option value="{valueprioridad1}" {selected1}>{descripcion1}</option>
	<option value="{valueprioridad2}" {selected2}>{descripcion2}</option>

  </select></td>
        
      </tr>
</table>
<br>
<table class="tabla">
    <caption class="tabla">Datos de Cuenta Bancaria</caption>
    <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
      <th class="vtabla" colspan="2">Pago Interbancario </th>
      <td class="tabla" colspan="2">
	            <p>
<span class="tabla"><select name="pago_via_interbancaria" class="insert" id="pago_via_interbancaria">

    <option value="{interbancario1}" {selected3}>{descripcion3}</option>
    <option value="{interbancario2}" {selected4}>{descripcion4}</option>
  </select></span>            <br>
          </p>
	  </td>
    </tr>
	<tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
      <th class="vtabla">Banco</th>
      <td class="tabla">
        <select name="idbancos" class="insert" id="idbancos">
          <!-- START BLOCK : banco -->
          <option value="{valuebanco}" {selected}>{idbanco} - {nombrebanco}</option>
          <!-- END BLOCK : banco -->
        </select>
</td>
      <th class="vtabla">CLABE</th>
      <td class="vtabla" ><input name="clabe_banco" type="text" class="insert" id="clabe_banco" value="{clabe_banco}" size="3" maxlength="3">
      |<input name="clabe_plaza" type="text" class="insert" id="clabe_plaza" value="{clabe_plaza}" size="3" maxlength="3">
      |<input name="clabe_cuenta" type="text" class="insert" id="clabe_cuenta" value="{clabe_cuenta}" size="11" maxlength="11">
      |<input name="clabe_identificador" type="text" class="insert" id="clabe_identificador" value="{clabe_identificador}"size="1" maxlength="1">
    </tr>
	<tr>
	  <th class="vtabla" colspan="2">Para abono en cuenta</th>

	  <td class="vtabla" colspan="2">&nbsp;
			<select name="abono_cuenta" class="insert" id="abono_cuenta">

              <option value="{valueabono1}" {selected9}>{descripcion9}</option>
			  <option value="{valueabono2}" {selected10}>{descripcion10}</option>

            </select>

	  </td>
	  </tr>


</table>
<br>
<table class="tabla">
    <caption class="tabla">
    Datos Generales 
    </caption>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Tipo de pago</th>
        <td class="tabla">
          <select name="idtipopago" class="insert" id="idtipopago">

            <!-- START BLOCK : pago -->
            <option value="{valuepago}" {selected}>{idpago} - {namepago}</option>
            <!-- END BLOCK : pago -->
          </select>
        </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">D&iacute;as de cr&eacute;dito autorizado</th>
        <td class="tabla">
          <input name="diascredito" type="text" class="insert" id="diascredito" value="{diascredito}" size="5" maxlength="5">
        </td>
      </tr>

      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Resta a compras</th>
        <td class="tabla">

            <select name="restacompras" class="insert" id="restacompras">
              <option value="{valueresta1}" {selected5}>{descripcion5}</option>
			  <option value="{valueresta2}" {selected6}>{descripcion6}</option>
            </select>
     </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Proveedor u otros</th>
        <td class="tabla">
          <select name="idtipoproveedor" class="insert"  id="idtipoproveedor">
            <!-- START BLOCK : proveedor -->
            <option value="{valueproveedor}" {selected}>{valueproveedor} - {nameproveedor}</option>
            <!-- END BLOCK : proveedor -->
          </select>
</td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Tiempo entrega mercancia</th>
        <td class="tabla">
          <input name="tiempoentrega" type="text" class="insert" id="tiempoentrega" value="{tiempoentrega}" size="5" maxlength="5">
        </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Tipo de Persona</th>
        <td class="tabla">
            <select name="tipopersona" class="insert" id="tipopersona">

              <option value="{valuepersona1}" {selected7}>{descripcion7}</option>
			  <option value="{valuepersona2}" {selected8}>{descripcion8}</option>

            </select></td>
      </tr>
    </table>
  <p>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Regresar" onclick='parent.history.back();'>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input name="actualizar" type="button" class="boton" id="actualizar" onclick="valida_registro();" value="Actualizar">
</p>
</form>
</td>
</tr>
</table>

<!-- END BLOCK : modificar -->	