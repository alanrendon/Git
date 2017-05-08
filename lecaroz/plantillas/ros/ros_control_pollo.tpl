<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">RELACIÓN DE COMPRA DE MATERIA PRIMA DE ROSTICERIAS </p>

<form name="form" method="post" action="./insert_pollos_cap.php?tabla={tabla}">
<table border="1" class="tabla">
  
  <!-- START BLOCK : cias -->
  <tr class="tabla">
	<th class="vtabla" colspan="9">{num_cia} - {nombre_cia}</th>
  </tr>
  <tr class="tabla">
    <th scope="col" class="tabla" colspan="2"><input name="contador" type="hidden" value="{count}">MATERIA PRIMA</th>
    <th scope="col" class="tabla">LUNES</th>
    <th scope="col" class="tabla">MARTES</th>
    <th scope="col" class="tabla">MIERCOLES</th>
    <th scope="col" class="tabla">JUEVES</th>
    <th scope="col" class="tabla">VIERNES</th>
    <th scope="col" class="tabla">SABADO</th>
    <th scope="col" class="tabla">DOMINGO</th>
  </tr>
  
  <!-- START BLOCK : rows -->
  <tr class="tabla" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rtabla">{codmp} <input name="codmp{i}" type="hidden" value="{codmp}"> <input name="num_cia{i}" type="hidden" value="{num_cia}"></td>
	<td class="vtabla">{nombre}</td>
    <td class="tabla">{lunes}<input name="lunes{i}" type="hidden" value="{lunes}"></td>
    <td class="tabla">{martes}<input name="martes{i}" type="hidden" value="{martes}"></td>
    <td class="tabla">{miercoles}<input name="miercoles{i}" type="hidden" value="{miercoles}"></td>
    <td class="tabla">{jueves}<input name="jueves{i}" type="hidden" value="{jueves}"></td>
    <td class="tabla">{viernes}<input name="viernes{i}" type="hidden" value="{viernes}"></td>
    <td class="tabla">{sabado}<input name="sabado{i}" type="hidden" value="{sabado}"></td>
    <td class="tabla">{domingo}<input name="domingo{i}" type="hidden" value="{domingo}"></td>
  </tr>
  <!-- END BLOCK : rows -->
  <!-- END BLOCK : cias -->
  
</table>

<p>
  <input type="button" name="Submit" value="Actualizar" onClick="document.form.submit();" class="boton">
</p>
</form>
</td>
</tr>
</table>
