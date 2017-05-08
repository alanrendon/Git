<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/prints.css" rel="stylesheet" type="text/css">

<!-- START BLOCK : obtener_datos -->
<script language="JavaScript" type="text/JavaScript">
function valida()
{
	if (document.form.tipo_con.value==0 && (document.form.num_prov.value=="" || document.form.num_prov.value <=0)){
		alert("Error en la compañía");
		document.form.cia.select();
		}
	else
		document.form.submit();
		
}
</script>



<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">CONSULTA DE PROVEEDORES</p>
<form action="./fac_prov_con.php" method="get" name="form">

<table border="1" class="tabla">
<tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
	<th class="vtabla">
	      <label>
	      <input name="tipo_con" type="radio" value="0" checked>Proveedor 
		    </label>
		  <input name="num_prov" type="text" class="insert" size="5">
	    
	</th>
    <th class="vtabla">	      <label>
     <input type="radio" name="tipo_con" value="1">
     Todos los proveedores</label></th>
</tr>
<tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
    <td class="tabla" colspan="2">
	      <label>
	      <input name="desgloce" type="radio" value="0" checked>
	      Solo nombres </label>
	      <label>
	      <input type="radio" name="desgloce" value="1">
	      Desglosado</label></td>
</tr>
<tr  onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');" class="tabla">
  <td class="vtabla" colspan="2"><p>
    <label>
    <input name="orden" type="radio" value="0" checked>
  Alfab&eacute;ticamente</label>
    <br>
    <label>
    <input type="radio" name="orden" value="1">
  Por número de proveedor</label>
    <br>
  </p></td>
</tr>
</table>
<p>
  <input class="boton" name="enviar" type="button" value="Consultar" onClick='valida();'>
</p>
</form>	
	<script language="JavaScript" type="text/JavaScript">window.onload = document.form.num_prov.select();</script>

</td>
</tr>
</table>
<!-- START BLOCK : obtener_datos -->

<!-- START BLOCK : proveedor_nombre -->

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.<br>CONSULTA DE PROVEEDORES</p>
<table border="1" class="print">
  <tr>
    <th scope="col" class="print">N&uacute;mero</th>
    <th scope="col" class="print">Nombre</th>
  </tr>
<!-- START BLOCK : rows -->
  <tr class="print">
    <th class="print">{num_proveedor}</th>
    <td class="vprint">{nombre}</td>
  </tr>
 <!-- END BLOCK : rows -->
</table>

</td>
</tr>
</table>

<!-- END BLOCK : proveedor_nombre -->

<!-- START BLOCK : proveedor_desgloce -->

<!-- START BLOCK : prov -->
<table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">OFICINAS ADMINISTRATIVAS MOLLENDO S. DE R.L. DE C.V.</p><table class="tabla">
      <caption class="tabla">Datos del Proveedor</caption>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">PROVEEDOR</th>
        <td class="vtabla">
          <strong>{id} {nombre}</strong>
          </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Direcci&oacute;n</th>
        <td class="vtabla">
          {direccion}
        </td>
      </tr>
       <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Tel&eacute;fono 1</th>
        <td class="vtabla">
          {telefono1}
        </td>
      </tr>
	  <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Tel&eacute;fono 2</th>
        <td class="vtabla">
          {telefono2}
        </td>
      </tr>
	  <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Fax</th>
        <td class="vtabla">
          {fax}
        </td>
      </tr>
	  <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">e-m@il</th>
        <td class="vtabla">
          {email}
        </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">R.F.C.</th>
        <td class="vtabla">
          {rfc}
        </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <td class="tabla" colspan="2">
          <strong>Prioridad del Proveedor</strong><br>
		{prioridad} </td>
        
      </tr>
</table>
<br>
<table class="tabla">
    <caption class="tabla">Datos de Cuenta Bancaria</caption>
    <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
      <th class="vtabla" colspan="2">Pago Interbancario </th>
      <td class="vtabla" colspan="2">{interbancario}</td>
    </tr>
	<tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
      <th class="vtabla">Banco</th>
      <td class="vtabla">
        {banco}
</td>
      <th class="vtabla">CLABE</th>
      <td class="vtabla" >{clabe_banco}
        |
          {clabe_plaza}
        |
        {clabe_cuenta}
        |
        {clabe_identificador}    </tr>
</table>
<br>
<table class="tabla">
    <caption class="tabla">
    Datos Generales 
    </caption>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Tipo de pago</th>
        <td class="vtabla">
          {tipo_pago}
        </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">D&iacute;as de cr&eacute;dito autorizado</th>
        <td class="vtabla">
          {diascredito}
        </td>
      </tr>

      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Resta a compras</th>
        <td class="vtabla">{compras}
        </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Proveedor u otros</th>
        <td class="vtabla">
          {tipo_proveedor}
</td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Tiempo entrega mercancia</th>
        <td class="vtabla">
          {tiempo_entrega}
        </td>
      </tr>
      <tr onMouseOver="overTD(this,'#ACD2DD');" onMouseOut="outTD(this,'');">
        <th class="vtabla">Tipo de Persona</th>
        <td class="vtabla">
          {tipo_persona}		</td>
      </tr>
    </table>
</td>
</tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : prov -->
<!-- END BLOCK : proveedor_desgloce -->