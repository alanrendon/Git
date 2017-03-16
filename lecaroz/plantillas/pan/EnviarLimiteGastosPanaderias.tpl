<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Env&iacute;o de L&iacute;mites de Gastos a Panaderias</title>
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Envío de Límite de Gastos a Panaderias</div>
  <div id="captura" align="center">
    <form action="EnviarLimiteGastosPanaderias.php" method="get" name="LimiteGastos" id="LimiteGastos" class="formulario">
	  <table class="tabla_captura">
        <tr>
          <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
          <td><input name="num_cia" type="text" class="cap toPosInt alignRight" id="num_cia" size="1" /><input name="nombre" type="text" class="disabled" id="nombre" size="30" /></td>
        </tr>
      </table>
      <p>
        <input name="next" type="button" class="boton" id="next" value="Siguiente" />
      </p>
	</form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var f;

window.addEvent('domready', function() {
	f = new Formulario('LimiteGastos');
	
	f.form.num_cia.addEvents({
		change: function() {
			if (this.value.getVal() > 0) {
				new Request({
					url: 'EnviarLimiteGastosPanaderias.php',
					method: 'get',
					data: {
						c: this.value
					},
					onSuccess: function(nombre)
					{
						if (nombre == '') {
							alert('La compañía ' + this.value + ' no se encuentra en el catálogo');
							
							this.value = '';
							f.form.nombre.value = '';
						}
						else
							f.form.nombre.value = nombre;
					}
				}).send();
			}
			else {
				this.value = '';
				f.form.nombre.value = '';
			}
		},
		keydown: function(e) {
			if (e.key == 'enter')
				this.blur();
		}
	});
	
	f.form.next.addEvent('click', function() {
		if (confirm('¿Desea enviar los limites de gastos a las panaderias?'))
			new Request({
				url: 'EnviarLimiteGastosPanaderias.php',
				method: 'get',
				data: {
					num_cia: f.form.num_cia.value
				},
				onSuccess: function(result)
				{console.log(result);
					if (result.getVal() == 1) {
						alert('Se han enviado los datos a las panaderias');
						f.form.num_cia.value = '';
						f.form.nombre.value = '';
						f.form.num_cia.select();
					}
					else if (result.getVal() == -1) {
						alert('No hay limites para la panaderia especificada');
					}
					else if (result.getVal() == -2) {
						alert('No hay conexion con el servidor');
					}
					else if (result.getVal() == -3) {
						alert('No se pudo iniciar sesión');
					}
					else if (result.getVal() == -4) {
						alert('No hay acceso al repositorio');
					}
					else if (result.getVal() == -5) {
						alert('Error al crear archivo de actualización');
					}
					else {
						alert('Ha ocurrido un error:\n\n' + result);
					}
				}
			}).send();
	});
	
	f.form.num_cia.select();
});
-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
