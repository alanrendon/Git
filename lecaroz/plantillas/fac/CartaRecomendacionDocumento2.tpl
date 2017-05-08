<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Acta Administrativa</title>

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
<div id="logo"><img src="imagenes/LogoLecaroz.jpg" width="200" height="75" /></div>
<div id="nombreCia">
  <div align="center">{nombre_cia}</div>
</div>
<p>&nbsp;</p>
<p align="right"><strong>MEXICO D.F., A {dia} DE {mes} DE {anio}</strong></p>
<p align="right">&nbsp;</p>
<p align="right"><strong>&nbsp;</strong></p>
<p>A QUIEN CORREPONDA:</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p align="justify">Me permito hacer de su conocimiento que el C. <strong>{empleado_nombre}</strong> labor&oacute; bajo mis &oacute;rdenes desde el d&iacute;a <strong>{fecha_inicio}</strong> al <strong>{fecha_termino}</strong> y me consta su responsabilidad y competencia en el trabajo, &nbsp;desempe&ntilde;ando  principalmente el puesto de <strong>{puesto}</strong><strong>. </strong></p>
<p align="justify">&nbsp;</p>
<p align="justify">Por lo  anterior no tengo inconveniente ninguno en recomendarlo ampliamente agradeciendo de antemano la atenci&oacute;n y facilidades que le puedan brindar.</p>
<p>&nbsp;</p>
<p align="justify">Se extiende la presente para los efectos legales que al interesado convenga.</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p align="center"><strong>A T E N T A M E N T E</strong></p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center"><strong>___________________________________________</strong><br />
    <strong>JESUS MARIA ZUBIZARRETA CEBERIO</strong><br />
    <strong>APODERADO LEGAL</strong></p>
<div id="footer" align="center">{cia_direccion}</div>
<p align="center" class="noDisplay">
  <input name="cerrar" type="button" id="cerrar" value="Cerrar" onclick="self.close()" />
</p>
</body>
</html>
