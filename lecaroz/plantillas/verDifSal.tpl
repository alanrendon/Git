<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="./styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-family:Arial, Helvetica, sans-serif; font-weight:bold;">Diferencia en Saldos Conciliados.</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Banco</th>
      <th class="tabla" scope="col">Cuenta</th>
      <th class="tabla" scope="col">Oficina</th>
      <th class="tabla" scope="col">Pendientes</th>
      <th class="tabla" scope="col">Banco</th>
      <th class="tabla" scope="col">Diferencia</th>
      <th class="tabla" scope="col">Dias<br />
        de<br />
        Diferencia</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="vtabla">{num_cia} {nombre}</td>
      <td class="tabla">{nombre_banco}</td>
      <td class="tabla">{cuenta}</td>
      <td class="rtabla">{oficina}</td>
      <td class="rtabla">{pendientes}</td>
	  <td class="rtabla">{banco}</td>
	  <td class="rtabla">{dif}</td>
	  <td class="tabla">{dias}</td>
	</tr>
	<!-- END BLOCK : fila -->
  </table>  
  <p style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:20pt;">SI NO CORRIGE EL DIA DE HOY LOS SALDOS NO PODRA SEGUIR CONCILIANDO</p>
  <p>
    <input name="" type="button" class="boton" onclick="self.close()" value="Cerrar" />
</p></td>
</tr>
</table>
</body>
</html>
