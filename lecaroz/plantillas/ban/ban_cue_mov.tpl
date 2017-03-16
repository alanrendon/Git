<!-- START BLOCK : enviar_archivo -->
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Movimientos de cuenta Banorte </p>
<form name="form" enctype="multipart/form-data" method="post" action="">
<table class="tabla">
<tr>
 <input name="MAX_FILE_SIZE" type="hidden" value="5242880">
 <th class="vtabla">Archivo de Movimientos:</th>
 <td class="vtabla"><input name="userfile" type="file" class="vinsert" id="userfile" size="40" readonly="true"></td>
</tr>
</table>
<p>
	<input name="enviar" type="submit" class="boton" id="enviar" value="Enviar">
</p>
</form>
</td>
</tr>
</table>
 <!-- END BLOCK : enviar_archivo -->
 <!-- START BLOCK : listado -->
 <table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
 <p class="title">Movimientos de cuentas Banorte</p>
 <p class="title">{md5}</p>
 <table class="print">
  <tr>
    <th class="print" scope="col">No. Cia. </th>
    <th class="print" scope="col">Nombre</th>
    <th class="print" scope="col">Cuenta</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Dep&oacute;sito</th>
    <th class="print" scope="col">Retiro</th>
    <th class="print" scope="col">No. documento </th>
    <th class="print" scope="col">Concepto</th>
	<th class="print" scope="col">C&oacute;digo Banco </th>
	<th class="print" scope="col">Saldo</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
    <td class="print">{num_cia}</td>
    <td class="vprint">{nombre}</td>
    <td class="print">{cuenta}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">{deposito}</td>
    <td class="rprint">{retiro}</td>
    <td class="print">{num_documento}</td>
    <td class="vprint">{concepto}</td>
	<td class="print">{cod_mov}</td>
	<td class="vprint">{saldo}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
</td>
</tr>
</table>
 <!-- END BLOCK : listado -->