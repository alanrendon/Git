<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Comprobante Fiscal Digital</title>
<style type="text/css">
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}

#title {
	font-size: 12pt;
	font-weight: bold;
}

#cia {
	font-size: 12pt;
	font-weight: bold;
}

#nota {
	color: #C00;
	font-weight: bold;
}

#footer {
	font-size: 8pt;
	font-weight: bold;
}
</style>
</head>

<!-- THE CAKE IS A LIE -->
<body>
<p id="title">COMPROBANTE FISCAL DIGITAL</p>
<p id="cia">{nombre_cia}<br />
	{rfc_cia}</p>
<p>Comprobante n&uacute;mero <strong>{folio}</strong> expedido a nombre de <strong>{nombre_cliente}</strong> con R.F.C. <strong>{rfc_cliente}</strong> por un importe total de <strong>${total} ({total_escrito})</strong>.</p>
<p id="nota">IMPORTANTE: Por favor, revise que la informaci&oacute;n contenida en el comprobante sea  correcta, solo podr&aacute; solicitar correcciones dentro del mes que se ha emitido el  mismo, pasado este tiempo no se admiten cambios ni reclamaciones.</p>
<hr />
<p id="footer">Favor de no responder a este correo electr&oacute;nico, este buz&oacute;n no se supervisa y no recibira respuesta. Si necesita m&aacute;s informaci&oacute;n escriba al correo <a href="mailto:{email_ayuda}">{email_ayuda}</a> y con gusto le atenderemos.</p>
</body>
<!-- THE CAKE IS A LIE -->
</html>
