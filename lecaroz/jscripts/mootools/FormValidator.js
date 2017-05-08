// JavaScript Document

var FormValidator = new Class({
	
	Implements: Options,
	
	options: {
		showErrors: false,
		selectOnFocus: false
	},
	
	Form: null,
	
	Functions: [
		'Focus',
		'toText',
		'onlyText',
		'onlyLetters',
		'onlyNumbers',
		'onlyNumbersAndLetters',
		'toDate',
		'toTime',
		'toTimeHMS',
		'toInterval',
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
		'toRFCopcional',
		'toCURP',
		'toUpper',
		'toLower',
		'cleanText',
		'personalPattern'
	],
	
	initialize: function(form, options) {
		this.setOptions(options);
		
		if ($chk(form)) {
			this.Form = form;
			
			this.addEventsToElements();
		}
	},
	
	addEventsToElements: function() {
		var elements = this.Form.getElements('.valid');
		
		elements.each(function(element) {
			this.addElementEvents(element);
		}, this);
	},
	
	addElementEvents: function(element) {
		element.get('class').split(' ').each(function(fn) {
			if (this.Functions.indexOf(fn) >= 0) {
				eval('this.' + fn + '(element)');
			}
		}, this);
	},
	
	Focus: function(element) {
		element.addEvent('focus', function() {
			element.store('tmp', element.get('value'));
			
			if (this.options.selectOnFocus) {
				element.select();
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
	
	toText: function(element) {
		element.addEvent('change', function() {
			element.set('value', this.get('value').replace(/[^\w\s\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA\u002D\u002E\u002C\u003B\u00BF\u003F\u0024\u0023\u00A1\u0021\u0025\u0026\u003A\u002F\u0027\u0028\u0029\u002A\u002B\u003C\u003D\u003E\u0040\u005B\u005D]/g, ''));
		});
	},
	
	onlyText: function(element) {
		element.addEvent('change', function() {
			element.set('value', this.get('value').replace(/[^a-zA-Z\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA\s]/g, ''));
		});
	},
	
	onlyLetters: function(element) {
		element.addEvent('change', function() {
			element.set('value', this.get('value').replace(/[^a-zA-Z\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]/g, ''));
		});
	},
	
	onlyNumbers: function(element) {
		element.addEvent('change', function() {
			element.value = this.value.replace(/\D/g, '');
		});
	},
	
	onlyNumbersAndLetters: function(element) {
		element.addEvent('change', function() {
			element.value = this.value.replace(/[^a-zA-Z0-9\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]/g, '');
		});
	},
	
	toDate: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('value'))) {
				// [DDMMAAAA], [DDMMAA], [DD/MM/AAAA], [DD/MM/AA], [DD-MM-AAAA], [DD-MM-AA]
				var pattern = /(\d{1,2})[\/|\-]?(\d{1,2})[\/|\-]?(\d{2,4})/,
					string = element.get('value');
				
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
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'El valor del mes debe estar entre 1 y 12');
					}
					else if (day < 1 || day > days_per_month[month - 1]) {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'El valor del día debe estar entre 1 y ' + days_per_month[month - 1]);
					}
					else {
						element.set('value', (day < 10 ? '0' : '') + day + '/' + (month < 10 ? '0' : '') + month + '/' + year);
					}
				}
				else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'Solo se permiten los siguientes formatos de fecha:\n\n* DDMMAA ó DDMMAAAA\n\n* DD/MM/AA ó DD/MM/AAAA\n\n* DD-MM-AA ó DD-MM-AAAA');
				}
			}
		}.bind(this));
	},
	
	toTime: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('value'))) {
				// [HHMM], [HH:MM]
				var pattern = /(\d{1,2})[\:]?(\d{2})/,
					string = element.get('value');
				
				if (pattern.test(string)) {
					var pieces = pattern.exec(string).map(function(piece) {
							return piece.toInt(10);
						}),
						hour = pieces[1],
						minutes = pieces[2];
					
					if (hour < 0 || hour > 23) {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'El valor de la hora debe estar entre 0 y 24');
					}
					else if (minutes < 0 || minutes > 59) {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'El valor de los minutos debe estar entre 0 y 59');
					}
					else {
						element.set('value', (hour < 10 ? '0' : '') + hour + ':' + (minutes < 10 ? '0' : '') + minutes);
					}
				}
				else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'Solo se permiten los siguientes formatos de hora:\n\n* HHMM\n\n* HH:MM');
				}
			}
		}.bind(this));
	},

	toTimeHMS: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('value'))) {
				// [HHMMSS], [HH:MM:SS]
				var pattern = /(\d{1,2})[\:]?(\d{2})[\:]?(\d{2})/,
					string = element.get('value');
				
				if (pattern.test(string)) {
					var pieces = pattern.exec(string).map(function(piece) {
							return piece.toInt(10);
						}),
						hour = pieces[1],
						minutes = pieces[2],
						seconds = pieces[3];
					
					if (hour < 0 || hour > 23) {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'El valor de la hora debe estar entre 0 y 24');
					}
					else if (minutes < 0 || minutes > 59) {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'El valor de los minutos debe estar entre 0 y 59');
					}
					else if (seconds < 0 || seconds > 59) {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'El valor de los segundos debe estar entre 0 y 59');
					}
					else {
						element.set('value', (hour < 10 ? '0' : '') + hour + ':' + (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
					}
				}
				else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'Solo se permiten los siguientes formatos de hora:\n\n* HHMMSS\n\n* HH:MM:SS');
				}
			}
		}.bind(this));
	},
	
	toInterval: function(element) {
		element.addEvent('change', function() {
			this.set('value', this.get('value').match(/([0-9]{1,}(-(?=[0-9])[0-9]{1,})?)/g));
		});
	},
	
	toIntervalChars: function(element) {
		element.addEvent('change', function() {
			this.set('value', this.get('value').match(/([0-9a-zA-Z\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]{1,}(-(?=[0-9a-zA-Z\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA])[0-9a-zA-Z\u00D1\u00F1\u00E1\u00E9\u00ED\u00F3\u00FA\u00C1\u00C9\u00CD\u00D3\u00DA]{1,})?)/g));
		});
	},
	
	toNumber: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('value'))) {
				var value = element.get('value').getNumericValue();
				
				if ($chk(value)) {
					element.set('value', value);
				}
				else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toInt: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('value'))) {
				var value = element.get('value').getNumericValue();
				
				if ($chk(value)) {
					element.set('value', value.round());
				}
				else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toPosInt: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('value'))) {
				var value = element.get('value').getNumericValue();
				
				if ($chk(value) && value.round() >= 0) {
					element.set('value', value.round());
				}
				else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toColorInt: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('value'))) {
				var value = element.get('value').getNumericValue();
				
				if ($chk(value)) {
					element.set({
						'value': value.round(),
						'styles': {
							'color': value >= 0 ? '' : 'red'
						}
					});
				}
				else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toFloat: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('value'))) {
				var value = element.get('value').getNumericValue();
				
				if ($chk(value)) {
					if ($chk(element.get('precision')) && $chk(element.get('precision').getNumericValue())) {
						value = value.round(element.get('precision').getNumericValue());
					}
					
					element.set('value', value);
				}
				else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toPosFloat: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('value'))) {
				var value = element.get('value').getNumericValue();
				
				if ($chk(value) && value >= 0) {
					if ($chk(element.get('precision')) && $chk(element.get('precision').getNumericValue())) {
						value = value.round(element.get('precision').getNumericValue());
					}
					
					element.set('value', value);
				}
				else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	toColorFloat: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('value'))) {
				var value = element.get('value').getNumericValue();
				
				if ($chk(value)) {
					if ($chk(element.get('precision')) && $chk(element.get('precision').getNumericValue())) {
						value = value.round(element.get('precision').getNumericValue());
					}
					
					element.set({
						'value': value,
						'styles': {
							'color': value >= 0 ? '' : 'red'
						}
					});
				}
				else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'Solo se permiten números');
				}
			}
		}.bind(this));
	},
	
	numberFormat: function(element) {
		element.addEvents({
			'focus': function() {
				if ($chk(element.get('value'))) {
					element.set('value', element.get('value').getNumericValue());
					
					if (this.options.selectOnFocus) {
						element.select();
					}
				}
			}.bind(this),
			'blur': function() {
				if ($chk(element.get('value'))) {
					var value = element.get('value').getNumericValue();
					
					if ($chk(value)) {
						var precision = null,
							dec_point = '.',
							thousands_sep = ',';
						
						if ($chk(element.get('precision'))) {
							precision = element.get('precision').getNumericValue();
						}
						if ($chk(element.get('dec_point'))) {
							dec_point = element.get('dec_point');
						}
						if ($chk(element.get('thousands_sep'))) {
							thousands_sep = element.get('thousands_sep');
						}
						
						element.set('value', value.numberFormat(precision, dec_point, thousands_sep));
					}
					else {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'Solo se permiten números');
					}
				}
			}.bind(this)
		});
	},
	
	numberPosFormat: function(element) {
		element.addEvents({
			'focus': function() {
				if ($chk(element.get('value'))) {
					element.set('value', element.get('value').getNumericValue());
					
					if (this.options.selectOnFocus) {
						element.select();
					}
				}
			}.bind(this),
			'blur': function() {
				if ($chk(element.get('value'))) {
					var value = element.get('value').getNumericValue();
					
					if ($chk(value) && value >= 0) {
						var precision = null,
							dec_point = '.',
							thousands_sep = ',';
						
						if ($chk(element.get('precision'))) {
							precision = element.get('precision').getNumericValue();
						}
						if ($chk(element.get('dec_point'))) {
							dec_point = element.get('dec_point');
						}
						if ($chk(element.get('thousands_sep'))) {
							thousands_sep = element.get('thousands_sep');
						}
						
						element.set('value', value.numberFormat(precision, dec_point, thousands_sep));
					}
					else {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'Solo se permiten números');
					}
				}
			}.bind(this)
		});
	},
	
	numberColorFormat: function(element) {
		element.addEvents({
			'focus': function() {
				if ($chk(element.get('value'))) {
					element.set('value', element.get('value').getNumericValue());
					
					if (this.options.selectOnFocus) {
						element.select();
					}
				}
			}.bind(this),
			'blur': function() {
				if ($chk(element.get('value'))) {
					var value = element.get('value').getNumericValue();
					
					if ($chk(value)) {
						var precision = null,
							dec_point = '.',
							thousands_sep = ',';
						
						if ($chk(element.get('precision'))) {
							precision = element.get('precision').getNumericValue();
						}
						if ($chk(element.get('dec_point'))) {
							dec_point = element.get('dec_point');
						}
						if ($chk(element.get('thousands_sep'))) {
							thousands_sep = element.get('thousands_sep');
						}
						
						element.set({
							'value': value.numberFormat(precision, dec_point, thousands_sep),
							'styles': {
								'color': value >= 0 ? '' : 'red'
							}
						});
					}
					else {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'Solo se permiten números');
					}
				}
			}.bind(this)
		});
	},
	
	toPhoneNumber: function(element) {
		element.addEvents({
			'focus': function() {
				element.set('value', element.get('value').replace(/\D/g, ''));
				
				if (this.options.selectOnFocus) {
					element.select();
				}
			}.bind(this),
			'blur': function() {
				if ($chk(element.get('value'))) {
					element.set('value', element.get('value').replace(/\D/g, ''));
					
					var pattern = /(01|044|045)?(800|900|\d{2})?(\d{4})(\d{4})/;
					
					if (pattern.test(element.get('value'))) {
						var pieces = pattern.exec(element.get('value'));
						
						element.set('value',
							($chk(pieces[1]) || $chk(pieces[2]) ? '(' : '') +
							($chk(pieces[1]) ? pieces[1] + ' ' : '') +
							($chk(pieces[2]) ? pieces[2] + ') ' : '') +
							pieces[3] + ' ' + pieces[4]
						);
					}
					else {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'Solo se permiten números con el siguiente formato:\n\n[01|044|045][Lada(2 dígitos)][Número(8 dígitos)]');
					}
				}
			}.bind(this)
		});
	},
	
	toEmail: function(element) {
		element.addEvent('blur', function() {
			if ($chk(element.get('value'))) {
				var pattern = /^([\w\-\.]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w\-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/;
				
				if (!pattern.test(element.get('value'))) {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'La sintaxis del correo electrónico no es válida');
				}
			}
		}.bind(this));
	},
	
	toRFC: function(element) {
		element.addEvent('blur', function() {
			if ($chk(element.get('value'))) {
				var pattern = /^([a-zA-Z\u00D1\u00F1\u0026]{3,4})([\d]{2})([\d]{2})([\d]{2})([a-zA-Z0-9]{3})$/;
				
				if (pattern.test(element.get('value'))) {
					var pieces = pattern.exec(element.get('value')).map(function(piece) {
							return $chk(piece) ? piece.toInt(10) : null;
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
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'La sintaxis del RFC no es válida');
					} else if (day < 1 || day > days_per_month[month - 1]) {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'La sintaxis del RFC no es válida');
					}
				} else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'La sintaxis del RFC no es válida');
				}
			}
		}.bind(this));
	},
	
	toRFCopcional: function(element) {
		element.addEvent('blur', function() {
			if ($chk(element.get('value'))) {
				var pattern = /^([a-zA-Z\u00D1\u00F1\u0026]{3,4})([\d]{2})([\d]{2})([\d]{2})([a-zA-Z0-9]{3})?$/;

				if (pattern.test(element.get('value'))) {
					var pieces = pattern.exec(element.get('value')).map(function(piece) {
							return $chk(piece) ? piece.toInt(10) : null;
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
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'La sintaxis del RFC no es válida');
					} else if (day < 1 || day > days_per_month[month - 1]) {
						element.set('value', element.retrieve('tmp', ''));
						
						this.errorAlert(element, 'La sintaxis del RFC no es válida');
					}
				} else {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'La sintaxis del RFC no es válida');
				}
			}
		}.bind(this));
	},
	
	toCURP: function(element) {
		element.addEvent('blur', function() {
			if ($chk(element.get('value'))) {
				var pattern = /^([a-zA-Z\u00D1\u00F1\u0026]{4})([\d]{6})(H|M)([a-zA-Z]{2})([a-zA-Z]{3})([\d]{2})$/;
				
				if (!pattern.test(element.get('value'))) {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'La sintaxis de la CURP no es válida');
				}
			}
		}.bind(this));
	},
	
	toUpper: function(element) {
		element.addEvent('blur', function() {
			element.set('value', element.get('value').toUpperCase());
		});
	},
	
	toLower: function(element) {
		element.addEvent('blur', function() {
			element.set('value', element.get('value').toLowerCase());
		});
	},
	
	cleanText: function(element) {
		element.addEvent('blur', function() {
			element.set('value', element.get('value').clean());
		});
	},
	
	personalPattern: function(element) {
		element.addEvent('change', function() {
			if ($chk(element.get('pattern')) && $chk(element.get('value'))) {
				var pattern = new RegExp(element.get('pattern'), 'g');
				
				if (!pattern.test(element.get('value'))) {
					element.set('value', element.retrieve('tmp', ''));
					
					this.errorAlert(element, 'La sintaxis no es válida');
				}
			}
		}.bind(this));
	},
	
	errorAlert: function(element, msg) {
		if (this.options.showErrors) {
			alert(msg);
		}
		element.select();
	}
	
});
