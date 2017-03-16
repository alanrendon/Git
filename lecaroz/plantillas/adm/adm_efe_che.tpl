<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">

<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">CAT&Aacute;LOGO PARA REVISI&Oacute;N DE EFECTIVOS </p>

<form name="form" method="post" action="./adm_efe_che.php">
  <table class="tabla">
    <tr class="tabla">
      <th class="tabla">
        <label>ALTA DE USUARIO PARA GENERAR REPORTE DE EFECTIVOS </label></th>
    </tr>
    <tr class="tabla">
      <td class="tabla">	  <select name="idusuario" class="insert">
	<!-- START BLOCK : rows -->
		<option value="{iduser}" class="insert">{nombre}</option>
	<!-- END BLOCK : rows -->
      </select>
        </td>
	</tr>
  </table>
<p>  
<input name="enviar" type="button" class="boton" id="enviar" onClick="document.form.submit();" value="Alta">
</p>
</form>
<script language="javascript" type="text/javascript">window.onload = document.form.idusuario.select();</script>
</td>
</tr>
</table>

