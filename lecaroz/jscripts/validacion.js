// JavaScript Document
// Funciones para validación de datos

// +------------------------------------------------------------------------------+
// | boolean isInt(input_object dato, hidden_object temp)                         |
// |                                                                              |
// | Valida si un dato proveniente de un objeto input es entero o nulo, en caso   |
// | contrario regresa el objeto a su valor anterior y muestra un mensaje de      |
// | error.                                                                       |
// +------------------------------------------------------------------------------+
function isInt(dato, temp) {
	var value_dato = parseInt(dato.value);
	
	if (dato.value == "")
		return true;
	else if (value_dato >= 0) {
		dato.value = value_dato;
		return true;
	}
	else {
		alert("No se permiten caracteres o valores negativos");
		dato.value = temp.value;
		dato.select();
		return false;
	}
}

// +------------------------------------------------------------------------------+
// | boolean isFloat(input_object dato, int dec, hidden_object temp)              |
// |                                                                              |
// | Valida si un dato proveniente de un objeto input es flotante o nulo, en caso |
// | contrario regresa el objeto a su valor anterior y muestra un mensaje de      |
// | error.                                                                       |
// +------------------------------------------------------------------------------+
function isFloat(dato, dec, temp) {
	var value_dato = parseFloat(dato.value);
	
	if (dato.value == "")
		return true;
	else if (value_dato >= 0) {
		dato.value = value_dato.toFixed(dec);
		return true;
	}
	else {
		alert("No se permiten caracteres o valores negativos");
		dato.value = temp.value;
		dato.select();
		return false;
	}
}

// +------------------------------------------------------------------------------+
// | boolean isFloat(input_object dato, int dec, hidden_object temp)              |
// |                                                                              |
// | Valida si un dato proveniente de un objeto input es flotante o nulo, en caso |
// | contrario regresa el objeto a su valor anterior y muestra un mensaje de      |
// | error.                                                                       |
// +------------------------------------------------------------------------------+
function isFloat2(dato, dec, temp) {
	var value_dato = parseFloat(dato.value);
	
	if (dato.value == "")
		return true;
	else if (value_dato >= 0) {
		dato.value = value_dato.toFixed(dec);
		return true;
	}
	else if (value_dato < 0) {
		dato.value = value_dato.toFixed(dec);
		return true;
	}
	else {
		alert("No se permiten caracteres");
		dato.value = temp.value;
		dato.select();
		return false;
	}
}

// +------------------------------------------------------------------------------+
// | boolean isDate(input_object input_fecha)                                     |
// |                                                                              |
// | Valida si un dato proveniente de un objeto input es una fecha valida, en     |
// | caso contrario regresa el objeto a su valor anterior y muestra un mensaje de |
// | error.                                                                       |
// +------------------------------------------------------------------------------+
function isDate(input_fecha) {
	var fecha = input_fecha.value;
	var temp = new Date();
	var anio_actual = temp.getFullYear();
	var mes_actual  = temp.getMonth() + 1;
	var dia_actual  = temp.getDate();
	
	// Si la fecha tiene el formato ddmmaaaa
	if (fecha.length == 8) {
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0)
			dia = parseInt(fecha.charAt(1),10);
		else
			dia = parseInt(fecha.substring(0,2),10);
		if (parseInt(fecha.charAt(2),10) == 0)
			mes = parseInt(fecha.charAt(3),10);
		else
			mes = parseInt(fecha.substring(2,4),10);
		anio = parseInt(fecha.substring(4),10);

		// Validar año de captura
		if (anio <= anio_actual) {
			// Generar dias por mes
			var diasxmes = new Array();
			diasxmes[1]  = 31; // Enero
			if (anio%4 == 0)
				diasxmes[2] = 29; // Febrero año bisiesto
			else
				diasxmes[2] = 28; // Febrero
			diasxmes[3]  = 31; // Marzo
			diasxmes[4]  = 30; // Abril
			diasxmes[5]  = 31; // Mayo
			diasxmes[6]  = 30; // Junio
			diasxmes[7]  = 31; // Julio
			diasxmes[8]  = 31; // Agosto
			diasxmes[9]  = 30; // Septiembre
			diasxmes[10] = 31; // Octubre
			diasxmes[11] = 30; // Noviembre
			diasxmes[12] = 31; // Diciembre
			
			if (mes > 1 && mes <= 12 && dia >= 1 && dia <= diasxmes[mes]) {
				if ((mes == mes_actual && dia >= 1 && dia <= diasxmes[mes]) || (mes == mes_actual - 1 && dia >= 1 && dia <= diasxmes[mes-1] && dia_actual <= 4)) {
					input_fecha.value = dia + "/" + mes + "/" + anio;
					return true;
				}
				else {
					input_fecha.value = "";
					alert("Rango de fecha no valido");
					//input_fecha.focus();
					return false;
				}
			}
			else if (mes == 1 && dia >= 1 && dia <= diasxmes[mes]) {
				if ((mes == mes_actual && dia >= 1 && dia <= diasxmes[mes]) || (mes == 12 && dia >= 1 && dia <= diasxmes[12] && dia_actual <= 4)) {
					input_fecha.value = dia + "/" + mes + "/" + anio;
					return true;
				}
				else {
					input_fecha.value = "";
					alert("Rango de fecha no valido");
					//input_fecha.focus();
					return false;
				}
			}
			else {
				input_fecha.value = "";
				alert("Rango de fecha no valido");
				//input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = "";
			alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
			//input_fecha.focus();
			return false;
		}
	}
	else if (fecha.length == 6) {
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0)
			dia = parseInt(fecha.charAt(1),10);
		else
			dia = parseInt(fecha.substring(0,2),10);
		if (parseInt(fecha.charAt(2),10) == 0)
			mes = parseInt(fecha.charAt(3),10);
		else
			mes = parseInt(fecha.substring(2,4),10);
		anio = parseInt(fecha.substring(4),10) + 2000;

		// El año de captura de ser el año en curso
		if (anio <= anio_actual) {
			// Generar dias por mes
			var diasxmes = new Array();
			diasxmes[1] = 31; // Enero
			if (anio%4 == 0)
				diasxmes[2] = 29; // Febrero año bisiesto
			else
				diasxmes[2] = 28; // Febrero
			diasxmes[3] = 31; // Marzo
			diasxmes[4] = 30; // Abril
			diasxmes[5] = 31; // Mayo
			diasxmes[6] = 30; // Junio
			diasxmes[7] = 31; // Julio
			diasxmes[8] = 31; // Agosto
			diasxmes[9] = 30; // Septiembre
			diasxmes[10] = 31; // Octubre
			diasxmes[11] = 30; // Noviembre
			diasxmes[12] = 31; // Diciembre
			
			if (mes > 1 && mes <= 12 && dia >= 1 && dia <= diasxmes[mes]) {
				if ((mes == mes_actual && dia >= 1 && dia <= diasxmes[mes]) || (mes == mes_actual - 1 && dia >= 1 && dia <= diasxmes[mes-1] && dia_actual <= 4)) {
					input_fecha.value = dia + "/" + mes + "/" + anio;
					return true;
				}
				else {
					input_fecha.value = "";
					alert("Rango de fecha no valido");
					//input_fecha.focus();
					return false;
				}
			}
			else if (mes == 1 && dia >= 1 && dia <= diasxmes[mes]) {
				if ((mes == mes_actual && dia >= 1 && dia <= diasxmes[mes]) || (mes == 12 && dia >= 1 && dia <= diasxmes[12] && dia_actual <= 4)) {
					input_fecha.value = dia + "/" + mes + "/" + anio;
					return true;
				}
				else {
					input_fecha.value = "";
					alert("Rango de fecha no valido");
					//input_fecha.focus();
					return false;
				}
			}
			else {
				input_fecha.value = "";
				alert("Rango de fecha no valido");
				//input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = "";
			alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
			//input_fecha.focus();
			return false;
		}
	}
	else {
		input_fecha.value = "";
		alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
		//input_fecha.focus();
		return false;
	}
}

function date_format(input, tmp) {
	var fecha = input.value;
	
	// Si la fecha tiene el formato ddmmaaaa
	if (fecha.length == 8) {
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0)
			dia = parseInt(fecha.charAt(1),10);
		else
			dia = parseInt(fecha.substring(0,2),10);
		if (parseInt(fecha.charAt(2),10) == 0)
			mes = parseInt(fecha.charAt(3),10);
		else
			mes = parseInt(fecha.substring(2,4),10);
		anio = parseInt(fecha.substring(4),10);

		// Generar dias por mes
		var diasxmes = new Array();
		diasxmes[1]  = 31; // Enero
		if (anio%4 == 0)
			diasxmes[2] = 29; // Febrero año bisiesto
		else
			diasxmes[2] = 28; // Febrero
		diasxmes[3]  = 31; // Marzo
		diasxmes[4]  = 30; // Abril
		diasxmes[5]  = 31; // Mayo
		diasxmes[6]  = 30; // Junio
		diasxmes[7]  = 31; // Julio
		diasxmes[8]  = 31; // Agosto
		diasxmes[9]  = 30; // Septiembre
		diasxmes[10] = 31; // Octubre
		diasxmes[11] = 30; // Noviembre
		diasxmes[12] = 31; // Diciembre
		
		if (mes > 1 && mes <= 12 && dia >= 1 && dia <= diasxmes[mes]) {
			input.value = dia + "/" + mes + "/" + anio;
			return true;
		}
		else if (mes == 1 && dia >= 1 && dia <= diasxmes[mes]) {
			input.value = dia + "/" + mes + "/" + anio;
			return true;
		}
		else {
			input.value = "";
			alert("Rango de fecha no valido");
			input.blur();
			return false;
		}
	}
	else if (fecha.length == 6) {
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0)
			dia = parseInt(fecha.charAt(1),10);
		else
			dia = parseInt(fecha.substring(0,2),10);
		if (parseInt(fecha.charAt(2),10) == 0)
			mes = parseInt(fecha.charAt(3),10);
		else
			mes = parseInt(fecha.substring(2,4),10);
		anio = parseInt(fecha.substring(4),10) + 2000;

		// Generar dias por mes
		var diasxmes = new Array();
		diasxmes[1] = 31; // Enero
		if (anio%4 == 0)
			diasxmes[2] = 29; // Febrero año bisiesto
		else
			diasxmes[2] = 28; // Febrero
		diasxmes[3] = 31; // Marzo
		diasxmes[4] = 30; // Abril
		diasxmes[5] = 31; // Mayo
		diasxmes[6] = 30; // Junio
		diasxmes[7] = 31; // Julio
		diasxmes[8] = 31; // Agosto
		diasxmes[9] = 30; // Septiembre
		diasxmes[10] = 31; // Octubre
		diasxmes[11] = 30; // Noviembre
		diasxmes[12] = 31; // Diciembre
		
		if (mes > 1 && mes <= 12 && dia >= 1 && dia <= diasxmes[mes]) {
			input.value = dia + "/" + mes + "/" + anio;
			return true;
		}
		else if (mes == 1 && dia >= 1 && dia <= diasxmes[mes]) {
			input.value = dia + "/" + mes + "/" + anio;
			return true;
		}
		else {
			input.value = tmp.value;
			alert("Rango de fecha no valido");
			input.blur();
			return false;
		}
	}
	else {
		input.value = tmp.value;
		alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
		input.blur();
		return false;
	}
}

// Objeto Numero
function oNumero(numero) {
	//Propiedades
	this.valor = numero || 0
	this.dec = -1;
	
	//Métodos
	this.formato = numFormat;
	this.ponValor = ponValor;
	
	//Definición de los métodos
	function ponValor(cad) {
		if (cad == '-' || cad == '+') return
		if (cad.length == 0) return
		if (cad.indexOf('.') >= 0)
			this.valor = parseFloat(cad);
		else
			this.valor = parseInt(cad);
	}
	
	function numFormat(dec, miles) {
		var num = this.valor, signo = 3, expr;
		var cad = "" + this.valor;
		var ceros = "", pos, pdec, i;
		
		for (i = 0; i < dec; i++)
			ceros += '0';
		pos = cad.indexOf('.')
		if (pos < 0)
			cad = cad + "." + ceros;
		else {
			pdec = cad.length - pos - 1;
			if (pdec <= dec) {
				for (i = 0; i < (dec - pdec); i++)
					cad += '0';
			}
			else {
				num = num * Math.pow(10, dec);
				num = Math.round(num);
				num = num / Math.pow(10, dec);
				cad = new String(num);
			}
		}
		pos = cad.indexOf('.');
		if (pos < 0) pos = cad.lentgh;
		if (cad.substr(0, 1) == '-' || cad.substr(0, 1) == '+')
		signo = 4;
		if (miles && pos > signo)
		do {
			expr = /([+-]?\d)(\d{3}[\.\,]\d*)/;
			cad . match(expr);
			cad = cad.replace(expr, RegExp.$1 + ',' + RegExp.$2);
		} while (cad.indexOf(',') > signo)
		if (dec < 0) cad = cad.replace(/\./, '');
		
		return cad;
	}
}

function number_format(numero, presicion) {
	var tmp = new oNumero(numero);
	
	return tmp.formato(presicion, true);
}

function input_format(input, pre, pos_only) {
	if (input.value == "" || input.value == "0") {
		input.value = "";
		return true;
	}
	else if (isNaN(parseFloat(input.value.replace(",", "")))) {
		alert("Solo se permiten números");
		input.value = input.form.tmp.value;
		return false;
	}
	
	var value = parseFloat(input.value.replace(",", ""));
	
	if (pos_only && value < 0) {
		alert("No se permiten números negativos");
		input.value = input.form.tmp.value;
		return false;
	}
	
	input.value = number_format(value, pre);
	
	return true;
}

function get_val(input) {
	var val;
	
	if (input.value.indexOf('.') >= 0)
		val = !isNaN(parseFloat(input.value.replace(/\,/g, ''))) ? parseFloat(input.value.replace(/\,/g, '')) : 0;
	else
		val = !isNaN(parseInt(input.value.replace(/\,/g, ''))) ? parseInt(input.value.replace(/\,/g, '')) : 0;
	
	return val;
}

function get_val2(str) {
	var val;
	
	if (str.indexOf('.') >= 0)
		val = !isNaN(parseFloat(str.replace(/\,/g, ''))) ? parseFloat(str.replace(/\,/g, '')) : 0;
	else
		val = !isNaN(parseInt(str.replace(/\,/g, ''))) ? parseInt(str.replace(/\,/g, '')) : 0;
	
	return val;
}