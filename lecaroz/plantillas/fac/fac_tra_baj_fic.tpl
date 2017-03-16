<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<!-- START BLOCK : styles -->
<style type="text/css">
#ficha {
	width: 200mm;
	height: 132mm;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	border: solid 1px #000;
}

#semana {
	width: 35%;
	margin-left: 90mm;
}

#fecha {
	width: 35%;
	margin-left: 90mm;
}

#cia {
	text-align: center;
}

#tabla_formato {
	border-collapse: collapse;
}

#tabla_formato td {
	border: solid 1px #000;
}

#aviso {
	width: 70mm;
	font-size: 8pt;
	margin-top: 3mm;
	margin-left: 10mm;
}

#firma {
	float: right;
	width: 90mm;
	margin-top: 12mm;
	text-align: center;
	font-size: 8pt;
	border-top: solid 1px #000;
	margin-right: 20mm;
}

#nota {
	width: 70mm;
	font-size: 8pt;
	border: solid 1px #000;
	margin-top: 3mm;
	margin-left: 10mm;
	padding: 1mm 2mm;
}

#puesto {
	float: right;
	width: 50mm;
	margin-top: 13mm;
	text-align: center;
	font-size: 8pt;
	border-top: solid 1px #000;
	margin-right: 40mm;
}
</style>
<!-- END BLOCK : styles -->
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Formato para N&oacute;mina
 </p>
  <form action="./fac_tra_baj_fic.php" method="get" name="form">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compañía</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if(event.keyCode==13)semana.select()" size="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Semana</th>
      <td class="vtabla"><input name="semana" type="text" class="insert" id="semana" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode==13)anio.select()" size="2" maxlength="2" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode==13)num_cia.select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.semana) <= 0) {
		alert('Debe especificar la semana');
		f.semana.select();
	}
	else if (get_val(f.anio) <= 0) {
		alert('Debe especificar el año');
		f.anio.select();
	}
	else
		f.submit();
}

window.onload = f.semana.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : ficha -->
<div id="ficha">
  <div id="semana">
    SEMANA: {semana} {anio}
  </div>
  <div id="fecha">
    FECHA: {fecha}
  </div>
  <div id="cia">
    {nombre}<br />{nombre_corto}
  </div>
  <div id="tabla">
    <table width="98%" align="center" id="tabla_formato">
      <tr>
        <td width="50%" align="center" style="font-size:8pt;" scope="col">OBSERVACIONES DEL ENCARGADO</td>
        <td width="50%" align="center" style="font-size:8pt;" scope="col">FAVOR DE NO RAYAR, TACHAR O PONER CORRECTOR EN LA NOMINA</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" align="center" style="font-size:8pt;">Sr. Encargado para poder agilizar las bajas de los trabajadores <span style="font-size:10pt;font-weight:bold;">FAVOR DE MARCAR ESTE NUM. DE TELEFONO. 5276-6579</span></td>
      </tr>
    </table>
  </div>
  <div id="firma">
    NOMBRE(S) Y FIRMA  </div>
  <div id="aviso">
    * SR. ENCARGADO TIENE USTED UNA SEMANA PARA REGRESAR COMPLETAMENTE FIRMADA Y REVISADA LA NOMINA, ANTES DE MANDARLA A LA OFICINA.
  </div>
  <div id="puesto">
    PUESTO  </div>
  <div id="nota">
    <span style="font-weight:bold;text-decoration:underline;">NOTA:</span> EN CASO DE FALTAR ALGUNA FIRMA EN LA NOMINA SE LE REGRESARA, TENIENDO QUE DEVOLVERLA LO MAS PRONTO POSIBLE A LA OFICINA PARA SU REVISION.
  </div>
</div>
{salto}
<!-- END BLOCK : ficha -->
</body>
</html>
