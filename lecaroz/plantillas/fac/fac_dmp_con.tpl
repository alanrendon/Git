<link href="/lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<!-- tabla catalogo_productos_proveedor -->
<script type="text/javascript" language="JavaScript">
	function valida_registro() {
		if(document.form.num_proveedor.value <= 0) {
			alert('Debe especificar un proveedor');
			document.form.num_proveedor.select();
		}
		else {
				document.form.submit();
			}
	}

	function borrar() {
		if (confirm("¿Desea borrar el formulario?")) {
			document.form.reset();
			document.form.num_cia.select();
		}
		else
			document.form.num_cia.select();
	}

</script>

<!-- START BLOCK : obtener_datos -->


<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta al catálogo de Productos por Proveedor</p>
<form name="form" method="get" action="./fac_dmp_con.php" onkeydown="if (event.keyCode == 13) document.form.enviar.focus();">
  <table class="tabla">
  <tr class="tabla">
    <th class="tabla">Número de Proveedor <input name="num_proveedor" type="text" id="num_proveedor" size="5" maxlength="5" class="insert"></th>
  </tr>
</table>


  <p>
  <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Consultar" onclick='valida_registro()'>

  </p>
</form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_proveedor.select();
</script>

</td>
</tr>
</table>
<!-- END BLOCK : obtener_datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<form name="form" method="post" action="./fac_dmp_mod.php?tabla={tabla}" onkeydown="if (event.keyCode == 13) document.form.enviar.focus();">
<table class="tabla">
	<tr class="tabla">
		<th class="tabla" align="center">CATÁLOGO DE PRODUCTOS POR PROVEEDOR</th>
	</tr>
	<tr>
		<td class="tabla" align="center"><strong><font size="+1">{num_proveedor}&#8212;{nom_proveedor}</font></strong>
		<input name="num_proveedor" type="hidden" value="{num_proveedor}">
		<input name="cont" type="hidden" id="cont" value="{count}">
        <input name="temp" type="hidden" id="temp" value="0">
</td>
	</tr>

</table>
<br>
<table class="tabla">
    <tr class="rtabla">
      <th class="tabla" align="center" colspan="2">C&oacute;digo materia prima </th>
      <th class="tabla" align="center">Presentacion</th>
      <th class="tabla" align="center">Contenido</th>
      <th class="tabla" align="center">Precio</th>
      <th class="tabla" align="center">Descuento 1 </th>
      <th class="tabla" align="center">Descuento 2 </th>
      <th class="tabla" align="center">Descuento 3 </th>
      <th class="tabla" align="center">I.V.A.</th>
      <th class="tabla" align="center">IEPS</th>
      <th class="tabla" align="center">Para pedido</th>
      <th class="tabla" align="center"><input type="checkbox" id="checkall">Modificar</th>
    </tr>
	<!-- START BLOCK : rows -->
    <tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
	  <th class="rtabla">{codmp}
	  <input name="codmp{i}" type="hidden" value="{codmp}">
	  <input name="id{i}" type="hidden" value="{id}">
	  </th>
	  <th class="vtabla">{nom_mp}</th>
	  <td class="rtabla"><input name="presentacion{i}" type="hidden" id="presentacion{i}" value="{presentacion}" />
	  	{tipo_presentacion}</td>
      <td class="rtabla">{contenido1}
        <input name="contenido{i}" type="hidden" value="{contenido}">
      </td>
      <td  class="rtabla">{precio1}
        <input name="precio{i}" type="hidden" value="{precio}">
      </td>
      <td class="rtabla">{desc11}
        <input name="desc1{i}" type="hidden" value="{desc1}">
      </td>
      <td class="rtabla">{desc21}
        <input name="desc2{i}" type="hidden" value="{desc2}">
      </td>
      <td class="rtabla">{desc31}
        <input name="desc3{i}" type="hidden" value="{desc3}">
      </td>
      <td class="rtabla">{iva1}
        <input name="iva{i}" type="hidden" value="{iva}">
      </td>
      <td class="rtabla">{ieps1}
        <input name="ieps{i}" type="hidden" value="{ieps}">
      </td>
      <td class="tabla">{para_pedido}
        <input name="para_pedido{i}" type="hidden" value="{para_pedido_val}">
      </td>
      <td class="tabla">
	  <input type="checkbox" name="mod{i}" onclick="if (this.checked==true){
      document.form.modificar{i}.value=1;
      document.form.temp.value=parseFloat(document.form.temp.value) + 1;
      } else if(this.checked==false){
      document.form.modificar{i}.value=0;
      document.form.temp.value=parseFloat(document.form.temp.value) - 1;
      };" onchange="revisa();"><input type="hidden" name="modificar{i}" value="0"></td>
    </tr>
	<!-- END BLOCK : rows -->
</table>

  <p>
<input type="button" name="regresar" class="boton" value="Regresar" onclick='parent.history.back()'>&nbsp;&nbsp;
<input type="button" name="enviar2" class="boton" value="Modificar" onclick='document.form.submit();' disabled>
</p>
</form>
</td>
</tr>
</table>

<script src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script language="JavaScript" type="text/JavaScript">
function revisa()
{
if(parseFloat(document.form.temp.value) > 0) document.form.enviar2.disabled=false;
else
document.form.enviar2.disabled=true;
}

document.id('checkall').addEvent('change', function()
{
  var status = this.checked;

  $$('input[name^=mod]').set('checked', status).each(function(el, i)
  {
    $$('input[name=modificar' + i + ']').set('value', el.checked ? 1 : 0);

    document.id('temp').set('value', $$('input[name^=mod]:checked').length);
  });

  revisa();
});
</script>
<!-- END BLOCK : listado -->

