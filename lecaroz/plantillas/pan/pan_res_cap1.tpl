<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">CAPTURA INICIAL DE REZAGOS </P>
<p class="title">{num_cia}&#8212;{nom_cia}<br>{fecha}</P>
<script type="text/javascript" language="JavaScript">
	
function valida_registro() {


if (confirm("¿Son correctos los datos de la pantalla?"))
	document.form.submit();
}
	
	</script>
	
	

<form name="form" method="post" action="./insert_pan_res_cap.php?tabla={tabla}">
<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" size="5">
<input name="fecha" type="hidden" id="fecha" value="{fecha}" size="5">
<input name="cont" type="hidden" id="cont" value="{cont}" size="5">  
<table class="tabla">
    <tr>
      <th class="tabla" colspan="2">Expendio</th>
      <th class="tabla">Importe</th>
      
    </tr>
	<!-- START BLOCK : rows -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="tabla" align="center">
        <input name="num_exp{i}" type="hidden" class="insert" id="num_exp{i}" value="{num_exp}" size="5">{num_exp}
      </th>
	  <td class="vtabla" align="center">
	  {nombre_exp}
	  </td>
      <td class="rtabla">
        <input name="importe{i}" type="hidden" class="insert" id="importe{i}" value="{importe}" size="15" onKeyDown="if (event.keyCode == 13) document.form.importe{next}.select();">
        <strong>{importe1}</strong> </td>
      
    </tr>
	<!-- END BLOCK : rows -->
    <tr>
      <th class="tabla"  colspan="2"align="center">TOTAL</th>

      <td class="rtabla"><strong><font size="+1">{total}</font></strong></td>
    </tr>
  </table>
  <p>
    <img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" name="enviar" class="boton" value="Capturar Rezagos" onclick='valida_registro()'> &nbsp;&nbsp;
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Regresar" onclick='parent.history.back()'>
</p>
</form>
</td>
</tr>
</table>