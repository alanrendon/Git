<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Consulta de Quejas de Pedidos</title>
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

<style type="text/css">
.aplus {
	font-weight:bold;
	color: #00C;
	text-decoration: none;
}

.aplus:link {
}

.aplus:visited {
}

.aplus:hover {
	cursor: pointer;
}

.aplus:active {
}

.aminus {
	font-weight:bold;
	color: #C00;
	text-decoration: none;
}

.aminus:link {
}

.aminus:visited {
}

.aminus:hover {
	cursor: pointer;
}

.aminus:active {
}
</style>
</head>

<body>
<div id="contenedor">
  <div id="titulo"> Consulta de Mensajes </div>
  <div id="captura" align="center">
    <form action="ConsultaQuejas.php" method="get" name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
        <tr>
          <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
          <td><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="1" /><input name="nombre" type="text" class="disabled" id="nombre" size="30" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Administrador</th>
          <td class="linea_on"><select name="idadmin" id="idadmin">
            <option value="" selected="selected"></option>
			<!-- START BLOCK : admin -->
            <option value="{id}">{admin}</option>
			<!-- END BLOCK : admin -->
          </select>          </td>
        </tr>
        <tr>
          <th align="left" scope="row">Tipo</th>
          <td class="linea_on"><select name="tipo" id="tipo">
			   <option value="" selected="selected"></option>
            <option value="1">RECADO</option>
            <option value="2">REPORTE</option>
            <option value="3">PEDIDO</option>
          </select></td>
        </tr>
        <tr>
          <th align="left" scope="row">Clasificaci&oacute;n</th>
          <td class="linea_on"><select name="idclase" id="idclase">
            <option value="" selected="selected"></option>
			<!-- START BLOCK : clase -->
			<option value="{id}">{concepto}</option>
			<!-- END BLOCK : clase -->
          </select>          </td>
        </tr>
        <tr>
          <th align="left" scope="row">Periodo</th>
          <td><input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" value="{fecha1}" size="10" maxlength="10" /> al <input name="fecha2" type="text" class="cap  toDate alignCenter" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Status</th>
          <td class="linea_on"><input name="status" type="radio" class="checkbox" value="0" checked="checked" />
            Activos<br />
            <input name="status" type="radio" class="checkbox" value="1" />
            Aclarados</td>
        </tr>
      </table>
      <p>
        <input name="buscar" type="button" class="boton" id="buscar" value="Buscar" />
      </p>
	  <div style="display:none;">
	    <input name="num_cia_copy" type="text" class="cap toPosInt alignCenter" id="num_cia_copy" size="1" />
		<input name="nombre_cia_copy" type="text" class="disabled" id="nombre_cia_copy" size="30" />
		<select name="idclase_copy" id="idclase_copy"></select>
		<input name="quejoso_copy" type="text" class="cap toText toUpper" id="quejoso_copy" size="30" maxlength="255" />
		<textarea name="queja_copy" cols="40" rows="5" class="cap toText toUpper" id="queja_copy" style="width:100%"></textarea>
	  </div>
    </form>
  </div>
  <div align="center" id="Resultado"></div>
</div>
<!-- START IGNORE -->
<script language="javascript" type="text/javascript">
<!--
var f, r;
var canModify = true;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('num_cia').addEvents({
		change: function() {
			if (this.get('value').getVal() > 0) {
				new Request({
					url: 'ConsultaQuejas.php',
					method: 'get',
					data: {
						c: this.get('value')
					},
					onSuccess: function(nombre)
					{
						if (nombre == '') {
							alert('La compañía ' + this.get('value') + ' no se encuentra en el catálogo');
							
							this.set('value', '');
							$('nombre').set('value', '');
						}
						else
							$('nombre').set('value', nombre);
					}
				}).send();
			}
			else {
				this.set('value', '');
				$('nombre').set('value', '');
			}
		},
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha1').select();
			}
		}
	});
	
	$('fecha1').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha2').select();
			}
		}
	});
	
	$('fecha2').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('num_cia').select();
			}
		}
	});
	
	$('buscar').addEvent('click', function() {
		new Request({
			url: 'ConsultaQuejas.php',
			method: 'get',
			data: {
				num_cia: $('num_cia').get('value'),
				idadmin: $('idadmin').get('value'),
				tipo: $('tipo').get('value'),
				idclase: $('idclase').get('value'),
				fecha1: $('fecha1').get('value'),
				fecha2: $('fecha2').get('value'),
				status: f.form.status[0].checked ? 0 : 1
			},
			onRequest: function() {
				$('Resultado').empty();
				
				new Element('img', {
					src: 'imagenes/ajax-loader.gif'
				}).inject($('Resultado'));
				
				new Element('p', {
					html: 'Buscando...',
					align: 'center'
				}).inject($('Resultado'));
			},
			onSuccess: function(data) {
				if (data == '') {
					alert('No hay resultados');
					
					//$('Resultado').set('html', '<span style="color:#C00;font-size:14pt;font-weight:bold;">No hay resultados</span>');
					$('Resultado').empty();
					
					r = null;
				}
				else {
					$('Resultado').set('html', data);
					
					r = new Formulario('Listado');
					
					$('checkall').addEvent('change', function() {
						$$('input[id=id]').each(function(el) {
							el.checked = $('checkall').checked;
						});
					});
					
					$('Resultado').getElements('img[id^=mod]').each(function(el, i) {
						el.addEvents({
							mouseover: function() {
								this.setStyle('cursor', 'pointer');
							},
							mouseout: function() {
								this.setStyle('cursor', 'default');
							},
							click: Modify.pass($defined(r.form.id.length) ? r.form.id[i] : r.form.id)
						});
					});
					
					$('Resultado').getElements('img[id^=del]').each(function(el, i) {
						el.addEvents({
							mouseover: function() {
								this.setStyle('cursor', 'pointer');
							},
							mouseout: function() {
								this.setStyle('cursor', 'default');
							},
							click: Delete.pass($defined(r.form.id.length) ? r.form.id[i] : r.form.id)
						});
					});
					
					$('nueva').addEvent('click', function() {
						f.form.reset();
						r = null;
						$('Resultado').empty();
					});
					
					$('imprimir').addEvent('click', function() {
						var query = $('Datos').toQueryString();
						var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=800,height=600';
						var win = window.open('ConsultaQuejas.php?list=1&' + query, 'cartas', opt);
						win.focus();
					});
				}
			}
		}).send();
	});
	
	$('num_cia').focus();
});

function cambiar(i, id, accion) {
	new Request({
		url: 'ConsultaQuejas.php',
		method: 'get',
		data: {
			'id': id,
			'accion': accion
		},
		onSuccess: function(queja) {
			$('queja' + i).set('html', queja);
			$('cambiar' + i).toggleClass('aplus');
			$('cambiar' + i).toggleClass('aminus');
			$('cambiar' + i).set({
				text: '[' + (accion == '+' ? '-' : '+') + ']',
				href: "javascript:cambiar(" + i + "," + id + ",'" + (accion == '+' ? '-' : '+') + "')"
			});
		}
	}).send();
}

var Modify = function() {
	var id = arguments[0];
	
	if (!canModify) {
		alert('Otro registro ya esta siendo modificado');
		$('num_cia_mod').focus();
		return false;
	}
	
	new Request({
		url: 'ConsultaQuejas.php',
		method: 'post',
		data: {
			'accion': 'retrieve',
			'id': id.get('value')
		},
		onSuccess: function(result) {
			var data = result.split('|');
			
			if (data[0] == 2) {
				alert('El registro ya ha sido eliminado por otro usuario');
				return false;
			}
			
			id.set('disabled', true);
			
			var tr = id.getParent('tr');
			
			var tr_backup = tr.clone(true, true);
			tr_backup.getElement('img[id=mod]').cloneEvents(tr.getElement('img[id=mod]'));
			tr_backup.getElement('img[id=del]').cloneEvents(tr.getElement('img[id=del]'));
			
			var tr_content = tr.getChildren('td');
			
			var ok = new Element('img', {
				src: 'menus/insert.gif',
				id: 'ok',
				name: 'ok',
				width: 16,
				height: 16
			});
			var cancel = new Element('img', {
				src: 'imagenes/delete16x16.png',
				id: 'cancel',
				name: 'cancel',
				width: 16,
				height: 16
			});
			
			ok.addEvents({
				mouseover: function() {
					this.setStyle('cursor', 'pointer');
				},
				mouseout: function() {
					this.setStyle('cursor', 'default');
				},
				click: elementReplace.pass([tr, tr_backup, true])
			});
			
			cancel.addEvents({
				mouseover: function() {
					this.setStyle('cursor', 'pointer');
				},
				mouseout: function() {
					this.setStyle('cursor', 'default');
				},
				click: elementReplace.pass([tr, tr_backup])
			});
			
			tr_content[1].empty();
			ok.inject(tr_content[1]);
			cancel.inject(tr_content[1]);
			
			tr_content[2].empty();
			tr_content[3].empty();
			tr_content[5].empty();
			tr_content[6].empty();
			
			var id_mod = new Element('input', {
				id: 'id_mod',
				name: 'id_mod',
				type: 'hidden',
				value: id.get('value')
			});
			var num_cia = $('num_cia_copy').clone();
			var nombre = $('nombre_cia_copy').clone();
			var clase = $('idclase_copy').clone();
			var quejoso = $('quejoso_copy').clone();
			var queja = $('queja_copy').clone();
			
			num_cia.cloneEvents($('num_cia_copy'));
			clase.cloneEvents($('idclase_copy'));
			quejoso.cloneEvents($('quejoso_copy'));
			queja.cloneEvents($('queja_copy'));
			
			num_cia.set({
				id: 'num_cia_mod',
				name: 'num_cia_mod',
				value: data[1]
			});
			nombre.set({
				id: 'nombre_mod',
				name: 'nombre_mod',
				value: data[2],
				disabled: true
			});
			clase.set({
				id: 'idclase_mod',
				name: 'idclase_mod'
			});
			new Element('option', {
				value: 'NULL',
				text: '',
				selected: data[3] == 0 ? true : false
			}).inject(clase);
			new Request({
				url: 'ConsultaQuejas.php',
				method: 'post',
				data: {
					accion: 'retrieveCat',
					id: data[3]
				},
				onSuccess: function(cat) {
					if (cat == '')
						return false;
					
					var regs = cat.split('|');
					
					regs.each(function(reg) {
						var r = reg.split(',');
						
						new Element('option', {
							value: r[0],
							text: r[1],
							selected: r[0] == data[3] ? true : false
						}).inject(clase);
					});
				}
			}).send();
			quejoso.set({
				id: 'quejoso_mod',
				name: 'quejoso_mod',
				value: data[4]
			});
			queja.set({
				id: 'queja_mod',
				name: 'queja_mod',
				value: data[5]
			});
			
			num_cia.addEvents({
				change: function() {
					if (this.get('value').getVal() > 0) {
						new Request({
							url: 'ConsultaQuejas.php',
							method: 'get',
							data: {
								c: this.get('value')
							},
							onSuccess: function(nombre)
							{
								if (nombre == '') {
									alert('La compañía ' + this.get('value') + ' no se encuentra en el catálogo');
									
									this.set('value', '');
									$('nombre_mod').set('value', '');
								}
								else
									$('nombre_mod').set('value', nombre);
							}
						}).send();
					}
					else {
						this.set('value', '');
						$('nombreMod').set('value', '');
					}
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						$('quejoso_mod').select();
					}
				}
			});
			quejoso.addEvents({
				change: function() {
					this.set('value', this.get('value').clean());
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						$('queja_mod').focus();
					}
				}
			});
			queja.addEvents({
				change: function() {
					this.set('value', this.get('value').clean());
				},
				keydown: function(e) {
					if (e.key == 'enter') {
						e.stop();
						$('num_cia_mod').select();
					}
				}
			});
			
			id_mod.inject(tr_content[2]);
			num_cia.inject(tr_content[2]);
			nombre.inject(tr_content[2]);
			clase.inject(tr_content[3]);
			quejoso.inject(tr_content[5]);
			queja.inject(tr_content[6]);
			
			num_cia.select();
			canModify = false;
		}
	}).send();
}

var elementReplace = function() {
	var tr_old = arguments[0], tr_old_children = tr_old.getChildren('td');
	var tr_new = arguments[1], tr_new_children = tr_new.getChildren('td');
	
	if (arguments[2]) {
		tr_new_children[2].set('html', $('num_cia_mod').get('value') + ' ' + $('nombre_mod').get('value'));
		tr_new_children[3].set('html', $('idclase_mod').options[$('idclase_mod').options.selectedIndex].get('text'));
		tr_new_children[5].set('html', $('quejoso_mod').get('value'));
		tr_new_children[6].getElement('div[id^=queja]').set('html', $('queja_mod').get('value').substr(0, 30) + ($('queja_mod').get('value').length > 33 ? '...' : ''));
		
		new Request({
			url: 'ConsultaQuejas.php',
			method: 'post',
			data: {
				accion: 'update',
				id: tr_new_children[0].getElement('input[id=id]').get('value'),
				num_cia: $('num_cia_mod').get('value'),
				clase: $('idclase_mod').get('value'),
				quejoso: $('quejoso_mod').get('value'),
				queja: $('queja_mod').get('value')
			},
			onSuccess: function(result) {
				if (result != '') {
					var win = window.open('', '', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600');
					win.document.writeln();
				}
			}
		}).send();
	}
	
	for (var i = 1, l = tr_old_children.length; i < l; i++)
		tr_new_children[i].replaces(tr_old_children[i]);
	
	tr_old_children[0].getElement('input[id=id]').set('disabled', false);
	canModify = true;
}

var Delete = function() {
	var id = arguments[0];
	
	if (confirm('¿Desea borrar el registro?')) {
		new Request({
			url: 'ConsultaQuejas.php',
			method: 'post',
			data: {
				'accion': 'delete',
				'id': id.get('value')
			},
			onSuccess: function() {
				if ($defined(r.form.id.length)) {
					id.getParent('tr').getAllNext().each(function(el) {
						el.toggleClass('linea_off');
						el.toggleClass('linea_on');
					});
					
					id.getParent('tr').destroy();
				}
				else {
					f.form.reset();
					r = null;
					$('Resultado').empty();
				}
			}
		}).send();
	}
}
//-->
</script>
<!-- END IGNORE -->
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
