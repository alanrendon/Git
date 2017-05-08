// JavaScript Document

window.addEvent('domready', function() {
	Inicio();
});

var Inicio = function() {
	new Request({
		'url': 'MateriasPrimasCatalogo.php',
		'data': 'accion=inicio',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando inicio...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));
			
			$('productos').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('nombre').focus();
					}
				}
			});
			
			$('nombre').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('productos').focus();
					}
				}
			});
			
			$('consultar').addEvent('click', Consultar);
			
			$('productos').select();
		}
	}).send();
}

var updSelect = function() {
	var Select = arguments[0],
		Options = arguments[1];
	
	if (Options.length > 0) {
		Select.length = Options.length;
		
		$each(Select.options, function(el, i) {
			el.set(Options[i]);
		});
		
		Select.selectedIndex = 0;
	}
	else {
		Select.length = 0;
		
		Select.selectedIndex = -1;
	}
}

var Consultar = function() {
	if ($type(arguments[0]) == 'string' && arguments[0] != '') {
		param = arguments[0];
	}
	else {
		param = $('Datos').toQueryString();
	}
	
	if (arguments[1] && $type(arguments[1]) == 'string' && arguments[1] != '') {
		param += '&page=' + arguments[1];
	}
	
	new Request({
		'url': 'MateriasPrimasCatalogo.php',
		'data': 'accion=consultar&' + param,
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Consultando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			if (result != '') {
				$('captura').empty().set('html', result);
				
				$('alta').addEvent('click', Alta);
				
				$$('a[id=page]').each(function(a) {
					var page = a.get('page');
					
					a.removeProperty('page');
					
					a.addEvent('click', Consultar.pass([param, page]));
				});
				
				$$('tr[id^=row]').addEvents({
					'mouseover': function() {
						this.addClass('highlight');
					},
					'mouseout': function() {
						this.removeClass('highlight');
					}
				});
				
				$$('img[id^=controlada]').each(function(el) {
					var codmp = el.get('alt');
					
					el.removeProperty('alt');
					
					el.addEvents({
						'mouseover': function(e) {
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							this.setStyle('cursor', 'default');
						},
						'click': function(e) {
							new Request({
								'url': 'MateriasPrimasCatalogo.php',
								'data': 'accion=cambiarStatusControlada&codmp=' + codmp,
								'onRequest': function() {
									el.set('src', '/lecaroz/imagenes/_loading.gif');
								},
								'onSuccess': function(result) {
									var data = JSON.decode(result);
									
									if (data.status) {
										el.set('src', '/lecaroz/iconos/accept.png');
									}
									else if (!data.status) {
										el.set('src', '/lecaroz/iconos/accept_blank.png');
									}
								}
							}).send();
						}
					});
				});
				
				$$('img[id^=pedido]').each(function(el) {
					var codmp = el.get('alt');
					
					el.removeProperty('alt');
					
					el.addEvents({
						'mouseover': function(e) {
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							this.setStyle('cursor', 'default');
						},
						'click': function(e) {
							new Request({
								'url': 'MateriasPrimasCatalogo.php',
								'data': 'accion=cambiarStatusPedido&codmp=' + codmp,
								'onRequest': function() {
									el.set('src', '/lecaroz/imagenes/_loading.gif');
								},
								'onSuccess': function(result) {
									var data = JSON.decode(result);
									
									if (data.status) {
										el.set('src', '/lecaroz/iconos/accept.png');
									}
									else if (!data.status) {
										el.set('src', '/lecaroz/iconos/accept_blank.png');
									}
								}
							}).send();
						}
					});
				});
				
				$$('img[id^=sin_existencia]').each(function(el) {
					var codmp = el.get('alt');
					
					el.removeProperty('alt');
					
					el.addEvents({
						'mouseover': function(e) {
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							this.setStyle('cursor', 'default');
						},
						'click': function(e) {
							new Request({
								'url': 'MateriasPrimasCatalogo.php',
								'data': 'accion=cambiarStatusSinExistencia&codmp=' + codmp,
								'onRequest': function() {
									el.set('src', '/lecaroz/imagenes/_loading.gif');
								},
								'onSuccess': function(result) {
									var data = JSON.decode(result);
									
									if (data.status) {
										el.set('src', '/lecaroz/iconos/accept.png');
									}
									else if (!data.status) {
										el.set('src', '/lecaroz/iconos/accept_blank.png');
									}
								}
							}).send();
						}
					});
				});

				$$('img[id^=reporte_consumos_mas]').each(function(el) {
					var codmp = el.get('alt');
					
					el.removeProperty('alt');
					
					el.addEvents({
						'mouseover': function(e) {
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							this.setStyle('cursor', 'default');
						},
						'click': function(e) {
							new Request({
								'url': 'MateriasPrimasCatalogo.php',
								'data': 'accion=cambiarStatusReporteConsumosMas&codmp=' + codmp,
								'onRequest': function() {
									el.set('src', '/lecaroz/imagenes/_loading.gif');
								},
								'onSuccess': function(result) {
									var data = JSON.decode(result);
									
									if (data.status) {
										el.set('src', '/lecaroz/iconos/accept.png');
									}
									else if (!data.status) {
										el.set('src', '/lecaroz/iconos/accept_blank.png');
									}
								}
							}).send();
						}
					});
				});

				$$('img[id^=grasa]').each(function(el) {
					var codmp = el.get('alt');
					
					el.removeProperty('alt');
					
					el.addEvents({
						'mouseover': function(e) {
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							this.setStyle('cursor', 'default');
						},
						'click': function(e) {
							new Request({
								'url': 'MateriasPrimasCatalogo.php',
								'data': 'accion=cambiarStatusGrasa&codmp=' + codmp,
								'onRequest': function() {
									el.set('src', '/lecaroz/imagenes/_loading.gif');
								},
								'onSuccess': function(result) {
									var data = JSON.decode(result);
									
									if (data.status) {
										el.set('src', '/lecaroz/iconos/accept.png');
									}
									else if (!data.status) {
										el.set('src', '/lecaroz/iconos/accept_blank.png');
									}
								}
							}).send();
						}
					});
				});

				$$('img[id^=azucar]').each(function(el) {
					var codmp = el.get('alt');
					
					el.removeProperty('alt');
					
					el.addEvents({
						'mouseover': function(e) {
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							this.setStyle('cursor', 'default');
						},
						'click': function(e) {
							new Request({
								'url': 'MateriasPrimasCatalogo.php',
								'data': 'accion=cambiarStatusAzucar&codmp=' + codmp,
								'onRequest': function() {
									el.set('src', '/lecaroz/imagenes/_loading.gif');
								},
								'onSuccess': function(result) {
									var data = JSON.decode(result);
									
									if (data.status) {
										el.set('src', '/lecaroz/iconos/accept.png');
									}
									else if (!data.status) {
										el.set('src', '/lecaroz/iconos/accept_blank.png');
									}
								}
							}).send();
						}
					});
				});
				
				$$('img[id^=mod]').each(function(img) {
					var codmp = img.get('alt');
					
					img.removeProperty('alt');
					
					img.addEvents({
						'mouseover': function(e) {
							this.setStyle('cursor', 'pointer');
						},
						'mouseout': function(e) {
							this.setStyle('cursor', 'default');
						},
						'click': Modificar.pass(codmp)
					});
				});
				
				$('regresar').addEvent('click', Inicio);
				
				$('listado').addEvent('click', Listado);
				
				$('alta').addEvent('click', Alta);
			}
			else {
				alert('No hay resultados');
				
				Inicio();
			}
		}
	}).send();
}

var Alta = function() {
	new Request({
		'url': 'MateriasPrimasCatalogo.php',
		'data': 'accion=alta',
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));

			$('imagen').addEvent('change', function()
			{
				if ($('imagen').get('value') == '')
				{
					alert('Debe seleccionar una imagen JPG');
				}
				else
				{
					var request = new Request.File({
						url: 'MateriasPrimasCatalogo.php',
						onRequest: function()
						{
							
						},
						onSuccess: function(result)
						{
							var data = JSON.decode(result);

							if (data.status < 0)
							{
								alert(data.error);

								$('imagen').set('value', '');
							}
							else
							{
								$('imagen_tmp').set('value', data.image);

								$('img').set('src', data.image);
							}
						}
					});

					request.append('accion', 'imagen_tmp');
					request.append('imagen', $('imagen').files[0]);

					request.send();
				}
			});

			$('drop_img').addEvent('click', function()
			{
				$('imagen').set('value', '');
				$('imagen_tmp').set('value', '');

				$('img').set('src', 'img_mp/sin_imagen.jpg');
			});

			$('nombre').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('prioridad_orden').select();
					}
				}
			});
			
			$('prioridad_orden').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('porcentaje_ieps').focus();
					}
				}
			});

			$('porcentaje_ieps').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('nombre').focus();
					}
				}
			});
			
			$('regresar').addEvent('click', Consultar.pass(param));
			
			$('alta').addEvent('click', doAlta);
			
			$('nombre').focus();
		}
	}).send();
}

var doAlta = function() {
	if ($('nombre').get('value') == '') {
		alert('Debe especificar el nombre del producto');
		
		$('nombre').select();
	} else if ($('porcentaje_ieps').get('value').getNumericValue() > 10) {
		alert('El porcentaje de I.E.P.S. no puede ser mayor a 10%');

		$('porcentaje_ieps').select();
	} else {
		new Request({
			'url': 'MateriasPrimasCatalogo.php',
			'data': 'accion=doAlta&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Registrando nuevo producto...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				var data = JSON.decode(result);
				
				$('captura').empty();
				
				Consultar.run(param);
				
				alert('El producto "' + data.nombre + '" fue dado de alta con el código "' + data.codmp + '"');
			}
		}).send();
	}
}

var Modificar = function() {
	var codmp = arguments[0];
	
	new Request({
		'url': 'MateriasPrimasCatalogo.php',
		'data': 'accion=modificar&codmp=' + codmp,
		'onRequest': function() {
			$('captura').empty();
			
			new Element('img', {
				'src': 'imagenes/_loading.gif'
			}).inject($('captura'));
			
			new Element('span', {
				'text': ' Cargando...'
			}).inject($('captura'));
		},
		'onSuccess': function(result) {
			$('captura').empty().set('html', result);
			
			new FormValidator($('Datos'), {
				showErrors: true,
				selectOnFocus: true
			});
			
			new FormStyles($('Datos'));

			$('imagen').addEvent('change', function()
			{
				if ($('imagen').get('value') == '')
				{
					alert('Debe seleccionar una imagen JPG');
				}
				else
				{
					var request = new Request.File({
						url: 'MateriasPrimasCatalogo.php',
						onRequest: function()
						{
							
						},
						onSuccess: function(result)
						{
							var data = JSON.decode(result);

							if (data.status < 0)
							{
								alert(data.error);

								$('imagen').set('value', '');
							}
							else
							{
								$('imagen_tmp').set('value', data.image);

								$('img').set('src', data.image);
							}
						}
					});

					request.append('accion', 'imagen_tmp');
					request.append('imagen', $('imagen').files[0]);

					request.send();
				}
			});

			$('drop_img').addEvent('click', function()
			{
				$('imagen').set('value', '');
				$('imagen_src').set('value', '');
				$('imagen_tmp').set('value', '');

				$('img').set('src', 'img_mp/sin_imagen.jpg');
			});
			
			$('nombre').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('prioridad_orden').select();
					}
				}
			});
			
			$('prioridad_orden').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('porcentaje_ieps').focus();
					}
				}
			});

			$('porcentaje_ieps').addEvents({
				'keydown': function(e) {
					if (e.key == 'enter') {
						e.stop();
						
						$('nombre').focus();
					}
				}
			});
			
			$('regresar').addEvent('click', Consultar.pass(param));
			
			$('modificar').addEvent('click', doModificar);
			
			$('nombre').focus();
		}
	}).send();
}

var doModificar = function() {
	if ($('nombre').get('value') == '') {
		alert('Debe especificar el nombre del producto');
		
		$('nombre').select();
	} else if ($('porcentaje_ieps').get('value').getNumericValue() > 10) {
		alert('El porcentaje de I.E.P.S. no puede ser mayor a 10%');

		$('porcentaje_ieps').select();
	} else {
		new Request({
			'url': 'MateriasPrimasCatalogo.php',
			'data': 'accion=doModificar&' + $('Datos').toQueryString(),
			'onRequest': function() {
				$('captura').empty();
				
				new Element('img', {
					'src': 'imagenes/_loading.gif'
				}).inject($('captura'));
				
				new Element('span', {
					'text': ' Modificando producto...'
				}).inject($('captura'));
			},
			'onSuccess': function(result) {
				$('captura').empty();
				
				Consultar.run(param);
			}
		}).send();
	}
}

var Listado = function() {
	var tipo = arguments[0],
		url = 'MateriasPrimasCatalogo.php',
		query = '?accion=listado&' + param,
		opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
	
	var win = window.open(url + query, '', opt);
	
	win.focus();
}
