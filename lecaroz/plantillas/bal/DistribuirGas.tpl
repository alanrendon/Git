<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="./smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Distribuir Gas</div>
  <div id="captura" align="center">
    <form action="DistribuirGas.php" method="get" name="DistribuirGas" class="formulario" id="DistribuirGas">
	  <table class="tabla_captura">
        <tr>
          <th align="left" scope="row">Compa&ntilde;ia</th>
          <td><input name="num_cia" type="text" class="cap toPosInt alignRight" id="num_cia" onkeydown="if(event.keyCode==13)anio.select()" size="1" /><input name="nombre" type="text" class="disabled" id="nombre" size="30" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">A&ntilde;o</th>
          <td><input name="anio" type="text" class="cap toPosInt alignCenter" id="anio" onkeydown="if(event.keyCode==13)num_cia.focus()" value="{anio}" size="4" maxlength="4" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Mes</th>
          <td><select name="mes" id="mes">
            <option value="1"{1}>ENERO</option>
            <option value="2"{2}>FEBRERO</option>
            <option value="3"{3}>MARZO</option>
            <option value="4"{4}>ABRIL</option>
            <option value="5"{5}>MAYO</option>
            <option value="6"{6}>JUNIO</option>
            <option value="7"{7}>JULIO</option>
            <option value="8"{8}>AGOSTO</option>
            <option value="9"{9}>SEPTIEMBRE</option>
            <option value="10"{10}>OCTUBRE</option>
            <option value="11"{11}>NOVIEMBRE</option>
            <option value="12"{12}>DICIEMBRE</option>
          </select></td>
        </tr>
      </table>
      <p>
        <input type="button" class="boton" value="Distribuir" onclick="validar()" />
      </p>
	</form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var f;

window.addEvent('domready', function() {
	f = new Formulario('DistribuirGas');
	
	f.form.num_cia.addEvent('change', cambiaCia);
	
	f.form.num_cia.select();
});

var cambiaCia = function() {
	if (f.form.num_cia.value.getVal() > 0) {
		new Request({
			url: 'DistribuirGas.php',
			method: 'get',
			onSuccess: function(nombre)
			{
				if (nombre == '') {
					alert('La compañía ' + f.form.num_cia.value + ' no se encuentra en el catálogo');
					
					f.form.num_cia.value = '';
					f.form.nombre.value = '';
				}
				else
					f.form.nombre.value = nombre;
			}
		}).send('c=' + f.form.num_cia.value.getVal());
	}
	else {
		f.form.num_cia.value = null;
		f.form.nombre.value = null;
	}
}

function validar() {
	if (f.form.anio.value.getVal() < 2000) {
		alert('Debe especificar el año');
		f.form.anio.focus();
		return false;
	}
	
	if (confirm('¿Esta completamente seguro de realizar la distribución de gas?'))
		f.form.submit();
}
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
