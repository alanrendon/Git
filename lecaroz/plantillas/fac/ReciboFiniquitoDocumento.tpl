<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Recibo Finiquito</title>

<style type="text/css">
body {
	width: 180mm;
	margin-left: 20mm;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12pt;
}

@media screen {
	.noDisplay {
		display: block;
	}
}

@media print {
	.noDisplay {
		display: none;
	}
}

#nombreCia {
	margin-left: 210px;
	margin-top: 40px;
	font-size: 16pt;
	font-weight: bold;
	border-bottom: double 4px #C00;
}

#logo {
	float: left;
}

#footer {
	margin-top: 230px;
	border-top: double 4px #C00;
	font-size: 8pt;
}
</style>

</head>

<body>
<p align="right"><strong>M&eacute;xico D.F., a {dia} de {mes} de {anio}</strong></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p align="center"><strong><u>RECIBO FINIQUITO</u></strong></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>NOMBRE DEL TRABAJADOR: <strong>{empleado_nombre}</strong><strong></strong></p>
<p><strong>&nbsp;</strong></p>
<p>FECHA DE INGRESO: <strong>{fecha_inicio}</strong><strong></strong><br />
  FECHA DE SALIDA: <strong>{fecha_termino}</strong><br />
  SUELDO DIARIO: <strong>{sueldo}</strong><strong></strong><br />
  A&Ntilde;OS DE ANTIG&Uuml;EDAD: <br />
  PRIMA  VACACIONAL: <br />
  AGUINALDO: </p>
<p>&nbsp;</p>
<p>RECIB&Iacute; DE: <strong>{cia_nombre}</strong><br />
  LA CANTIDAD DE: <strong>{neto}</strong><u></u></p>
<p>&nbsp;</p>
<p align="justify">Cantidad que recibo quedando totalmente saldadas y finiquitadas todas y cada una de las prestaciones a que tuve derecho, derivadas de mi relaci&oacute;n de trabajo, dejando constancia legal de haber recibido puntualmente los salarios ordinarios y extraordinarios, as&iacute; como las prestaciones que conforme a la ley, ten&iacute;a derecho.</p>
<p>&nbsp;</p>
<p align="justify">Manifestando que no se me adeuda cantidad alguna por concepto de horas extras, s&eacute;ptimos d&iacute;as, descansos obligatorios, as&iacute; como cualquier otra prestaci&oacute;n de car&aacute;cter laboral, ya que siempre he recibido los pagos de manera puntual y oportuna.</p>
<p>&nbsp;</p>
<p align="justify">En consecuencia nada tengo que reclamar a <strong>{cia_nombre}</strong>, con quien doy por terminada la relaci&oacute;n de trabajo,  con fundamento en la Fracci&oacute;n I, del art&iacute;culo 53, de la Ley Federal del trabajo, quedando totalmente pagada cualquier cantidad que se me adeudara. Por lo tanto, el presente ampara el finiquito m&aacute;s amplio que en derecho proceda.</p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center">________________________________________<br />
<strong>{empleado_nombre}</strong></p>
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" onclick="self.close()" />
</p>
</body>
</html>
