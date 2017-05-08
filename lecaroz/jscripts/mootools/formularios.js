// JavaScript Document
var Formulario = new Class({
	
	//mostrarAlertas: false,
	
	initialize: function(form)
	{
		// 'tmp' almacena temporalmente el valor contenido en el objeto de formulario 'input' seleccionado
		this.tmp = null;
		
		// 'error' TRUE=Error en el campo de captura
		this.error = false;
		
		// 'mostrarAlertas' TRUE = muestra las alertas de errores en la captura de datos
		this.mostrarAlertas = false;
		
		// Contenedor del formulario
		this.form = $(form);
		// Obtener elementos 'input' tipo 'text' del formulario
		this.caps = this.form.getElements('input[class^=cap]');
		this.texts = this.form.getElements('textarea[class^=cap]');
		this.selects  = this.form.getElements('select');
		this.botones  = this.form.getElements('input[class^=boton]');
		this.ros  = this.form.getElements('input[class^=readOnly]');
		this.dis  = this.form.getElements('input[class^=disabled]');
		
		// Añadir estilos y eventos a los campos de captura 'cap'
		this.caps.each(function(cap) {
			cap.addEvents({
				'mouseover': function()
				{
					this.addClass('over');
				},
				'mouseout': function()
				{
					this.removeClass('over');
				},
				'focus': function()
				{
					this.addClass('highlight');
					Formulario.tmp = this.get('value');
				},
				'blur': function()
				{
					this.removeClass('highlight');
				}
			});
		});
		
		// Añadir estilos y eventos a los campos de captura 'cap'
		this.texts.each(function(text) {
			text.addEvents({
				'mouseover': function()
				{
					this.addClass('over');
				},
				'mouseout': function()
				{
					this.removeClass('over');
				},
				'focus': function()
				{
					this.addClass('highlight');
					Formulario.tmp = this.get('value');
				},
				'blur': function()
				{
					this.removeClass('highlight');
				}
			});
		});
		
		// Añadir estilos y eventos a los campos de captura 'cap'
		this.selects.each(function(sel) {
			sel.addEvents({
				'mouseover': function()
				{
					this.addClass('over');
				},
				'mouseout': function()
				{
					this.removeClass('over');
				},
				'focus': function()
				{
					this.addClass('highlight');
				},
				'blur': function()
				{
					this.removeClass('highlight');
				}
			});
		});
		
		// Añadir estilos y eventos a los campos tipo boton
		this.botones.each(function(boton) {
			boton.addEvents({
				'mouseover': function()
				{
					this.addClass('boton_over');
				},
				'mouseout': function()
				{
					this.removeClass('boton_over');
				},
			});
		});
		
		// Añadir campos de 'Solo Lectura'
		this.ros.each(function(ro)
		{
			ro.readOnly = true;
		});
		
		// Añadir campos 'Deshabilitados'
		this.dis.each(function(di)
		{
			di.disabled = true;
		});
		
		// Añadir eventos y funciones especiales a cada campo de captura 'cap'
		this.caps.each(function(cap)
		{
			if (cap.hasClass('toText'))
			{
				cap.addEvent('change', function()
				{
					var str = this.value.replace(/[^\wñÑ\s\-\.,;¿\?\$\%]/g, '');
					this.value = str;
				});
			}
			else if (cap.hasClass('onlyText'))
			{
				cap.addEvent('change', function()
				{
					var str = this.value.replace(/[^a-zA-ZñÑ\s]/g, '');
					this.value = str;
				});
			}
			else if (cap.hasClass('onlyLetters'))
			{
				cap.addEvent('change', function()
				{
					var str = this.value.replace(/[^a-zA-ZñÑ]/g, '');
					this.value = str;
				});
			}
			else if (cap.hasClass('onlyNumbersAndLetters'))
			{
				cap.addEvent('change', function()
				{
					var str = this.value.replace(/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]/g, '');
					this.value = str;
				});
			}
			else if (cap.hasClass('onlyNumbers'))
			{
				cap.addEvent('change', function()
				{
					var str = this.value.replace(/[^0-9]/g, '');
					this.value = str;
				});
			}
			else if (cap.hasClass('toNumber'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
					}
					
					this.value = value != null ? value : Formulario.tmp;
				});
			}
			else if (cap.hasClass('toDate'))
			{
				cap.addEvent('change', function()
				{
					var value = this.value;
					
					if (value == '')
					{
						return true;
					}
					
					// Patrón corto [DDMMAAAA] ó [DDMMAA]
					var fecha_patron_corto = /(\d{2})(\d{2})(\d{2,4})/;
					// Patrón normal [DD/MM/AAAA], [DD/MM/AA], [DD-MM-AAAA], [DD-MM-AA]
					var fecha_patron_normal = /(\d{1,2})[\/|\-](\d{1,2})[\/|\-](\d{2,4})/;
					var partes_fecha = false;
					
					if (fecha_patron_corto.test(value))
					{
						partes_fecha = fecha_patron_corto.exec(value);
					}
					else if (fecha_patron_normal.test(value))
					{
						partes_fecha = fecha_patron_normal.exec(value);
					}
					else
					{
						this.value = Formulario.tmp;
						return false;
					}
					
					var dia = partes_fecha[1].toInt();
					var mes = partes_fecha[2].toInt();
					var anio = (partes_fecha[3].length == 2 && partes_fecha[3].toInt() < 70 ? partes_fecha[3].toInt() + 2000 : partes_fecha[3].toInt());
					
					var dias_por_mes = [31, (((anio % 4) == 0 || (anio % 400) == 0) && anio % 100 != 0 ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
					
					if (mes < 1 || mes > 12)
					{
						this.value = Formulario.tmp;
						return false;
					}
					
					if (dia < 1 || dia > dias_por_mes[mes - 1])
					{
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = (dia < 10 ? '0' + dia : dia) + '/' + (mes < 10 ? '0' + mes : mes) + '/' + anio;
					return true;
				});
			}
			else if (cap.hasClass('toInterval')) {
				cap.addEvent('change', function()
				{
					if (this.get('value') == '') {
						return true;
					}
					
					this.set('value', this.get('value').match(/([0-9]{1,}(-(?=[0-9])[0-9]{1,})?)/g));
				});
			}
			else if (cap.hasClass('toInt'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.focus();
						return false;
					}
					
					this.value = value != null && value >= 0 ? value.toInt() : Formulario.tmp;
				});
			}
			else if (cap.hasClass('toFloat'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.focus();
						return false;
					}
					
					this.value = value != null && value >= 0 ? value.toFloat() : Formulario.tmp;
				});
			}
			else if (cap.hasClass('toPosInt'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {console.log('Formulario.mostrarAlertas = ' + Formulario.mostrarAlertas);
						if (Formulario.mostrarAlertas == true)
						{console.log('Error: el campo solo acepta números');
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null && value >= 0 ? value.toInt() : Formulario.tmp;
				});
			}
			else if (cap.hasClass('toPosFloat'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null && value >= 0 ? value.toFloat() : Formulario.tmp;
				});
			}
			else if (cap.hasClass('toColorInt'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.toInt() : Formulario.tmp;
					this.setStyle('color', value < 0 ? 'red' : '');
				});
			}
			else if (cap.hasClass('toColorFloat'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.toFloat() : Formulario.tmp;
					this.setStyle('color', value < 0 ? 'red' : '');
				});
			}
			else if (cap.hasClass('numFormat10'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(4, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numFormat9'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(9, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numFormat8'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(8, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numFormat7'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(7, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numFormat6'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(6, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numFormat5'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(5, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numFormat4'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(4, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numFormat3'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(3, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numFormat2'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(2, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numFormat1'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(1, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numFormat'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(null, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat10'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(10, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat9'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(9, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat8'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(8, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat7'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(7, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat6'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(6, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat5'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(5, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat4'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(4, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat3'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(3, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat2'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(2, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat1'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(1, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numPosFormat'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(null, '.', ',') : Formulario.tmp;
				});
			}
			else if (cap.hasClass('numColorFormat4'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(4, '.', ',') : Formulario.tmp;
					this.setStyle('color', value < 0 ? 'red' : '');
				});
			}
			else if (cap.hasClass('numColorFormat3'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(3, '.', ',') : Formulario.tmp;
					this.setStyle('color', value < 0 ? 'red' : '');
				});
			}
			else if (cap.hasClass('numColorFormat2'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(2, '.', ',') : Formulario.tmp;
					this.setStyle('color', value < 0 ? 'red' : '');
				});
			}
			else if (cap.hasClass('numColorFormat1'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value > 0 ? value.numberFormat(1, '.', ',') : Formulario.tmp;
					this.setStyle('color', value < 0 ? 'red' : '');
				});
			}
			else if (cap.hasClass('numColorFormat'))
			{
				cap.addEvent('change', function()
				{
					if (this.value == '') {
						return true;
					}
					
					var value = this.value.getVal();
					
					if (value === false) {
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el campo solo acepta números');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
					
					this.value = value != null ? value.numberFormat(null, '.', ',') : Formulario.tmp;
					this.setStyle('color', value < 0 ? 'red' : '');
				});
			}
			else if (cap.hasClass('phoneNumber'))
			{
				cap.addEvents({
					'blur': function()
					{
						if (this.value == '')
						{
							return true;
						}
						
						var patron = /(01|044|045)?(800|900|\d{2})?(\d{4})(\d{4})/;
						var partes_tel = false;
						
						if (patron.test(this.value))
						{
							partes_tel = patron.exec(this.value);
						}
						else
						{
							if (Formulario.mostrarAlertas == true)
							{
								alert('Error: el campo solo acepta números y el formato [01|044|045][Lada(2 dígitos)][Número(8 dígitos)]');
							}
							
							this.value = Formulario.tmp;
							return false;
						}
						
						this.value = (partes_tel[1] != undefined) || (partes_tel[2] != undefined) ? '(' : '';
						this.value += partes_tel[1] != undefined ? partes_tel[1] + ' ' : '';
						this.value += partes_tel[2] != undefined ? partes_tel[2] + ') ' : '';
						this.value += partes_tel[3] + ' ' + partes_tel[4];
					},
					'focus': function()
					{
						this.value = this.value.replace(/[^\d]/g, '');
					}
				});
			}
			else if (cap.hasClass('eMail'))
			{
				cap.addEvent('blur', function()
				{
					if (this.value == '')
					{
						return true;
					}
					
					var patron = /^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
					if (patron.test(this.value))
					{
						return true;
					}
					else
					{
						if (Formulario.mostrarAlertas == true)
						{
							alert('Error: el formato del email es incorrecto, deberia ser por ejemplo \'nombre@dominio.com\'');
						}
						
						this.value = Formulario.tmp;
						return false;
					}
				});
			}
			
			if (cap.hasClass('toUpper'))
			{
				cap.addEvent('blur', function()
				{
					this.value = this.value.toUpperCase();
				});
			}
			else if (cap.hasClass('toLower'))
			{
				cap.addEvent('blur', function()
				{
					this.value = this.value.toLowerCase();
				});
			}
			
			if (cap.hasClass('clean'))
			{
				cap.addEvent('blur', function()
				{
					this.value = this.value.clean();
				});
			}
		});
		
		this.texts.each(function(text) {
			if (text.hasClass('toUpper'))
			{
				text.addEvent('blur', function()
				{
					this.value = this.value.toUpperCase();
				});
			}
			else if (text.hasClass('toLower'))
			{
				text.addEvent('blur', function()
				{
					this.value = this.value.toLowerCase();
				});
			}
			
			if (text.hasClass('clean'))
			{
				text.addEvent('blur', function()
				{
					this.value = this.value.clean();
				});
			}
		});
	},
	
	obtenerCampoCatalogo: function(opt)
	{
		var response = null;
		
		if (opt.valor.trim() == '')
		{
			$(opt.actualiza).setText('');
		}
		else
		{
			var ajax = new Ajax('catalogo.php',
			{
				method: 'get',
				data: Object.toQueryString(opt),
				update: opt.actualiza,
				onComplete: function()
				{
					console.log(this.response);
				}
			}).request();
			
		}
	}

});

