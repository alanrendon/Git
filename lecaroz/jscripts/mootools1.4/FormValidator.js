var FormValidator = new Class({
	
	Implements: Options,
	
	options: {
		showErrors: false,
		selectOnFocus: true,
		dateSeparator: '/'
	},
	
	Functions: [
		'focus',
		'toText',
		'onlyText',
		'onlyLetters',
		'onlyNumbers',
		'onlyNumbersAndLetters',
		'toDate',
		'toTime',
		'toInterval',
		'toIntervalFloats',
		'toIntervalChars',
		'toNumber',
		'toInt',
		'toPosInt',
		'toColorInt',
		'toFloat',
		'toPosFloat',
		'toColorFloat',
		'numberFormat',
		'numberPosFormat',
		'numberColorFormat',
		'toPhoneNumber',
		'toEmail',
		'toRFC',
		'toRFCsimple',
		'toUpper',
		'toLower',
		'cleanText',
		'personalPattern'
	],
	
	initialize: function(form, options) {
		this.setOptions(options);
		
		if (form)
		{
			var elements = form.getElements('.validate');
			
			if (elements) {
				elements.each(function(el) {
					this.addElementEvents(el);
				}, this);
			}
		}
	},
	
	addElementEvents: function(el) {
		el.get('class').split(' ').each(function(fn) {
			if (this.Functions.indexOf(fn) >= 0) {
				eval('this.' + fn + '(el)');
			}
		}, this);

		return el;
	},
	
	focus: function(el) {
		el.addEvent('focus', function() {
			el.store('tmp', el.get('value'));
			
			if (this.options.selectOnFocus) {
				el.select();
			}
		}.bind(this));
	},
	
	/*
	UNICODE CHARACTERS
	Ñ = \u00D1
	ñ = \u00F1
	á = \u00E1
	é = \u00E9
	í = \u00ED
	ó = \u00F3
	ú = \u00FA
	Á = \u00C1
	É = \u00C9
	Í = \u00CD
	Ó = \u00D3
	Ú = \u00DA
	- = \u002D
	. = \u002E
	, = \u002C
	; = \u003B
	¿ = \u00BF
	? = \u003F
	$ = \u0024
	# = \u0023
	¡ = \u00A1
	! = \u0021
	% = \u0025
	& = \u0026
	: = \u003A
	/ = \u002F
	' = \u0027
	( = \u0028
	) = \u0029
	* = \u002A
	+ = \u002B
	< = \u003C
	= = \u003D
	> = \u003E
	@ = \u0040
	[ = \u005B
	] = \u005D
	*/
	
	toText: function(el) {
		el.addEvent('change', function() {
			el.set('value', this.get('value').replace(/[^\w\s\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA\u002D\u002E\u002C\u003B\u00BF\u003F\u0024\u0023\u00A1\u0021\u0025\u0026\u003A\u002F\u0027\u0028\u0029\u002A\u002B\u003C\u003D\u003E\u0040\u005B\u005D]/g, ''));
		});
	},
	
	onlyText: function(el) {
		el.addEvent('change', function() {
			el.set('value', this.get('value').replace(/[^a-zA-Z\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA\s]/g, ''));
		});
	},
	
	onlyLetters: function(el) {
		el.addEvent('change', function() {
			el.set('value', this.get('value').replace(/[^a-zA-Z\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]/g, ''));
		});
	},
	
	onlyNumbers: function(el) {
		el.addEvent('change', function() {
			el.value = this.value.replace(/\D/g, '');
		});
	},
	
	onlyNumbersAndLetters: function(el) {
		el.addEvent('change', function() {
			el.value = this.value.replace(/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]/g, '');
		});
	},
	
	toDate: function(el) {
		el.addEvent('change', function() {
			if (el.get('value') != '') {
				// [DDMMAAAA], [DDMMAA], [DD/MM/AAAA], [DD/MM/AA], [DD-MM-AAAA], [DD-MM-AA], [DD MM AAAA], [DD MM AA]
				var pattern = /(\d{1,2})[\/|\-|\s]?(\d{1,2})[\/|\-|\s]?(\d{2,4})/,
					string = el.get('value');
				
				if (pattern.test(string)) {
					var pieces = pattern.exec(string).map(function(piece) {
							return piece.toInt(10);
						}),
						year = pieces[3] < 70 ? pieces[3] + 2000 : pieces[3],
						month = pieces[2],
						day = pieces[1],
						days_per_month = [
							31,	// Enero
							((year % 4) == 0 && (year % 400) != 0) || (year % 400) == 0 ? 29 : 28,	// Febrero
							31,	// Marzo
							30,	// Abril
							31,	// Mayo
							30,	// Junio
							31,	// Julio
							31,	// Agosto
							30,	// Septiembre
							31,	// Octubre
							30,	// Noviembre
							31	// Diciembre
						];
					
					if (month < 1 || month > 12) {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'El valor del mes debe estar entre 1 y 12');
					} else if (day < 1 || day > days_per_month[month - 1]) {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'El valor del día debe estar entre 1 y ' + days_per_month[month - 1]);
					} else {
						el.set('value', (day < 10 ? '0' : '') + day + this.options.dateSeparator + (month < 10 ? '0' : '') + month + this.options.dateSeparator + year);
					}
				} else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'Solo se permiten los siguientes formatos de fecha:\n\n* DDMMAA ó DDMMAAAA\n\n* DD/MM/AA ó DD/MM/AAAA\n\n* DD-MM-AA ó DD-MM-AAAA\n\n* DD MM AAA ó DD MM AA');
				}
			}
		}.bind(this));
	},
	
	toTime: function(el) {
		el.addEvent('change', function() {
			if (el.get('value') != '') {
				// [HHMM], [HH:MM]
				var pattern = /(\d{1,2})[\:|\s]?(\d{2})/,
					string = el.get('value');
				
				if (pattern.test(string)) {
					var pieces = pattern.exec(string).map(function(piece) {
							return piece.toInt(10);
						}),
						hour = pieces[1],
						minutes = pieces[2];
					
					if (hour < 0 || hour > 23) {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'El valor de la hora debe estar entre 0 y 24');
					} else if (minutes < 0 || minutes > 59) {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'El valor de los minutos debe estar entre 0 y 59');
					} else {
						el.set('value', (hour < 10 ? '0' : '') + hour + ':' + (minutes < 10 ? '0' : '') + minutes);
					}
				} else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'Solo se permiten los siguientes formatos de hora:\n\n* HHMM\n\n* HH:MM');
				}
			}
		}.bind(this));
	},
	
	toInterval: function(el) {
		el.addEvent('change', function() {
			this.set('value', this.get('value').match(/([0-9]+(-(?=[0-9])[0-9]+)?)/g));
		});
	},
	
	toIntervalFloats: function(el) {
		el.addEvent('change', function() {
			this.set('value', this.get('value').match(/(([0-9]+\.(?=[0-9])?[0-9]+|[0-9]+)(-(?=[0-9])([0-9]+\.(?=[0-9])?[0-9]+|[0-9]+))?)/g));
		});
	},
	
	toIntervalChars: function(el) {
		el.addEvent('change', function() {
			this.set('value', this.get('value').match(/([0-9a-zA-Z\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]{1,}(-(?=[0-9a-zA-Z\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA])[0-9a-zA-Z\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]{1,})?)/g));
		});
	},
	
	toNumber: function(el) {
		el.addEvent('change', function() {
			if (el.get('value') != '') {
				var value = el.get('value').getNumericValue();
				
				if (!!(value || value === 0)) {
					el.set('value', value);
				} else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toInt: function(el) {
		el.addEvent('change', function() {
			if (el.get('value') != '') {
				var value = el.get('value').getNumericValue();
				
				if (!!(value || value === 0)) {
					el.set('value', value.round());
				} else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toPosInt: function(el) {
		el.addEvent('change', function() {
			if (el.get('value') != '') {
				var value = el.get('value').getNumericValue();
				
				if (!!(value || value === 0) && value.round() >= 0) {
					el.set('value', value.round());
				} else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toColorInt: function(el) {
		el.addEvent('change', function() {
			if (el.get('value') != '') {
				var value = el.get('value').getNumericValue();
				
				if (!!(value || value === 0)) {
					el.set({
						'value': value.round(),
						'styles': {
							'color': value >= 0 ? '' : 'red'
						}
					});
				} else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toFloat: function(el) {
		el.addEvent('change', function() {
			if (el.get('value') != '') {
				var value = el.get('value').getNumericValue();
				
				if (!!(value || value === 0)) {
					if (el.get('precision') != null && el.get('precision').getNumericValue() >= 0) {
						value = value.round(el.get('precision').getNumericValue());
					}
					
					el.set('value', value);
				} else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toPosFloat: function(el) {
		el.addEvent('change', function() {
			if (el.get('value') != '') {
				var value = el.get('value').getNumericValue();
				
				if (!!(value || value === 0) && value >= 0) {
					if (el.get('precision') != null && el.get('precision').getNumericValue() >= 0) {
						value = value.round(el.get('precision').getNumericValue());
					}
					
					el.set('value', value);
				} else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toColorFloat: function(el) {
		el.addEvent('change', function() {
			if (el.get('value') != '') {
				var value = el.get('value').getNumericValue();
				
				if (!!(value || value === 0)) {
					if (el.get('precision') != null && el.get('precision').getNumericValue() >= 0) {
						value = value.round(el.get('precision').getNumericValue());
					}
					
					el.set({
						'value': value,
						'styles': {
							'color': value >= 0 ? '' : 'red'
						}
					});
				} else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	numberFormat: function(el) {
		el.addEvents({
			
			'focus': function() {
				if (el.get('value') != '') {
					el.set('value', el.get('value').getNumericValue());
					
					if (this.options.selectOnFocus) {
						el.select();
					}
				}
			}.bind(this),
			
			'blur': function() {
				if (el.get('value') != '') {
					var value = el.get('value').getNumericValue();
					
					if (!!(value || value === 0)) {
						var precision = null,
							dec_point = '.',
							thousands_sep = ',';
						
						if (el.get('precision') != null) {
							precision = el.get('precision').getNumericValue();
						}
						
						if (el.get('dec_point') != null) {
							dec_point = el.get('dec_point');
						}
						
						if (el.get('thousands_sep') != null) {
							thousands_sep = el.get('thousands_sep');
						}
						
						el.set('value', value.numberFormat(precision, dec_point, thousands_sep));
					} else {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'Solo se permiten números');
					}
				}
			}.bind(this)
			
		});
	},
	
	numberPosFormat: function(el) {
		el.addEvents({
			
			'focus': function() {
				if (el.get('value') != '') {
					el.set('value', el.get('value').getNumericValue());
					
					if (this.options.selectOnFocus) {
						el.select();
					}
				}
			}.bind(this),
			
			'blur': function() {
				if (el.get('value') != '') {
					var value = el.get('value').getNumericValue();
					
					if (!!(value || value === 0) && value >= 0) {
						var precision = null,
							dec_point = '.',
							thousands_sep = ',';
						
						if (el.get('precision') != null) {
							precision = el.get('precision').getNumericValue();
						}
						
						if (el.get('dec_point') != null) {
							dec_point = el.get('dec_point');
						}
						
						if (el.get('thousands_sep') != null) {
							thousands_sep = el.get('thousands_sep');
						}
						
						el.set('value', value.numberFormat(precision, dec_point, thousands_sep));
					} else {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'Solo se permiten números positivos');
					}
				}
			}.bind(this)
			
		});
	},
	
	numberColorFormat: function(el) {
		el.addEvents({
			
			'focus': function() {
				if (el.get('value') != '') {
					el.set('value', el.get('value').getNumericValue());
					
					if (this.options.selectOnFocus) {
						el.select();
					}
				}
			}.bind(this),
			
			'blur': function() {
				if (el.get('value') != '') {
					var value = el.get('value').getNumericValue();
					
					if (!!(value || value === 0)) {
						var precision = null,
							dec_point = '.',
							thousands_sep = ',';
						
						if (el.get('precision') != null) {
							precision = el.get('precision').getNumericValue();
						}
						
						if (el.get('dec_point') != null) {
							dec_point = el.get('dec_point');
						}
						
						if (el.get('thousands_sep') != null) {
							thousands_sep = el.get('thousands_sep');
						}
						
						el.set({
							'value': value.numberFormat(precision, dec_point, thousands_sep),
							'styles': {
								'color': value >= 0 ? '' : 'red'
							}
						});
					} else {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'Solo se permiten números');
					}
				}
			}.bind(this)
			
		});
	},
	
	toPhoneNumber: function(el) {
		el.addEvents({
			
			'focus': function() {
				el.set('value', el.get('value').replace(/\D/g, ''));
				
				if (this.options.selectOnFocus) {
					el.select();
				}
			}.bind(this),
			
			'blur': function() {
				if (el.get('value') != '') {
					el.set('value', el.get('value').replace(/\D/g, ''));
					
					var pattern = /(01|044|045)?(800|900|\d{2})?(\d{4})(\d{4})/;
					
					if (pattern.test(el.get('value'))) {
						var pieces = pattern.exec(el.get('value'));
						
						el.set('value',
							( !! (pieces[1] || pieces[1] === 0) || !! (pieces[2] || pieces[2] === 0) ? '(' : '') +
							( !! (pieces[1] || pieces[1] === 0) ? pieces[1] + ' ' : '') +
							( !! (pieces[2] || pieces[2] === 0) ? pieces[2] + ') ' : '') +
							pieces[3] + ' ' + pieces[4]
						);
					} else {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'Solo se permiten números con el siguiente formato:\n\n[01|044|045][Lada(2 dígitos)][Número(8 dígitos)]');
					}
				}
			}.bind(this)
			
		});
	},
	
	toEmail: function(el) {
		el.addEvent('blur', function() {
			if (el.get('value') != '') {
				var pattern = /^([\w\-\.]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w\-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/;
				
				if (!pattern.test(el.get('value'))) {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'La sintaxis del correo electrónico no es válida');
				}
			}
		}.bind(this));
	},
	
	toRFC: function(el) {
		el.addEvent('blur', function() {
			if (el.get('value') != '') {
				var pattern = /^([a-zA-ZñÑ\&]{3,4})([\d]{2})([\d]{2})([\d]{2})([a-zA-Z0-9]{3})$/;
				
				if (pattern.test(el.get('value'))) {
					var pieces = pattern.exec(el.get('value')).map(function(piece) {
							return !!(piece || piece === 0) ? piece.toInt(10) : null;
						});
					var year = pieces[2] < 60 ? pieces[2] + 2000 : pieces[2] + 1900;
					var month = pieces[3];
					var day = pieces[4];
					var days_per_month = [
							31,	// Enero
							((year % 4) == 0 && (year % 400) != 0) || (year % 400) == 0 ? 29 : 28,	// Febrero
							31,	// Marzo
							30,	// Abril
							31,	// Mayo
							30,	// Junio
							31,	// Julio
							31,	// Agosto
							30,	// Septiembre
							31,	// Octubre
							30,	// Noviembre
							31	// Diciembre
						];
					
					if (month < 1 || month > 12) {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'La sintaxis del RFC no es válida');
					} else if (day < 1 || day > days_per_month[month - 1]) {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'La sintaxis del RFC no es válida');
					}
				}
				else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'La sintaxis del RFC no es válida');
				}
			}
		}.bind(this));
	},
	
	toRFCsimple: function(el) {
		el.addEvent('blur', function() {
			if (el.get('value') != '') {
				var pattern = /^([a-zA-ZñÑ\&]{3,4})([\d]{2})([\d]{2})([\d]{2})([a-zA-Z0-9]{3})?$/;
				
				if (pattern.test(el.get('value'))) {
					var pieces = pattern.exec(el.get('value')).map(function(piece) {
							return !!(piece || piece === 0) ? piece.toInt(10) : null;
						});
					var year = pieces[2] < 60 ? pieces[2] + 2000 : pieces[2] + 1900;
					var month = pieces[3];
					var day = pieces[4];
					var days_per_month = [
							31,	// Enero
							((year % 4) == 0 && (year % 400) != 0) || (year % 400) == 0 ? 29 : 28,	// Febrero
							31,	// Marzo
							30,	// Abril
							31,	// Mayo
							30,	// Junio
							31,	// Julio
							31,	// Agosto
							30,	// Septiembre
							31,	// Octubre
							30,	// Noviembre
							31	// Diciembre
						];
					
					if (month < 1 || month > 12) {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'La sintaxis del RFC no es válida');
					} else if (day < 1 || day > days_per_month[month - 1]) {
						el.set('value', el.retrieve('tmp', ''));
						
						this.errorAlert(el, 'La sintaxis del RFC no es válida');
					}
				}
				else {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'La sintaxis del RFC no es válida');
				}
			}
		}.bind(this));
	},
	
	toCURP: function(element) {
		el.addEvent('blur', function() {
			if (el.get('value') != '') {
				var pattern = /^([a-zA-Z\u00D1\u00F1\u0026]{4})([\d]{6})(H|M)([a-zA-Z]{2})([a-zA-Z]{3})([\d]{2})$/;
				
				if (!pattern.test(el.get('value'))) {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'La sintaxis de la CURP no es válida');
				}
			}
		}.bind(this));
	},
	
	toUpper: function(el) {
		el.addEvent('blur', function() {
			el.set('value', el.get('value').toUpperCase());
		});
	},
	
	toLower: function(el) {
		el.addEvent('blur', function() {
			el.set('value', el.get('value').toLowerCase());
		});
	},
	
	cleanText: function(el) {
		el.addEvent('blur', function() {
			el.set('value', el.get('value').clean());
		});
	},
	
	personalPattern: function(el) {
		el.addEvent('change', function() {
			if (el.get('pattern') != null && el.get('value') != '') {
				var pattern = new RegExp(el.get('pattern'), 'g');
				
				if (!pattern.test(el.get('value'))) {
					el.set('value', el.retrieve('tmp', ''));
					
					this.errorAlert(el, 'La sintaxis no es válida');
				}
			}
		}.bind(this));
	},
	
	errorAlert: function(el, msg) {
		if (this.options.showErrors) {
			alert(msg);
		}
		
		el.select();
	}
	
});

Element.implement({
	
	FormValidator: function(options) {
		return this.store('FormValidator', new FormValidator(this, options));
	}
	
});
