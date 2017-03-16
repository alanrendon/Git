// JavaScript Document

var showAlert = false;			// Mostrar alertas
var currentDate = new Date();	// Fecha actual del sistema

function inputDateFormat(textInput) {
	if (!textInput)
		return false;
	
	if (textInput.type != 'text')
		return false;
	
	if (textInput.value == '')
		return true;
	
	var patron1 = /(\d{2})(\d{2})(\d{2,4})/;
	var patron2 = /(\d{1,2})\/(\d{1,2})\/(\d{2,4})/;
	var array = false;
	if (patron1.test(textInput.value))
		array = patron1.exec(textInput.value);
	else if (patron2.test(textInput.value))
		array = patron2.exec(textInput.value);
	
	if (!array) {
		if (showAlert)
			alert("El formato de captura es: DDMMAAAA");
		
		if (textInput.form.tmp)
			textInput.value = textInput.form.tmp.value;
		else
			textInput.value = '';
		
		textInput.select();
		return false;
	}
	
	var day = parseInt(array[1], 10), month = parseInt(array[2], 10), year = array[3].length == 2 ? parseInt(array[3], 10) + 2000 : parseInt(array[3], 10);
	
	var daysPerMonth = new Array();
	daysPerMonth[1] = 31;
	daysPerMonth[2] = year % 4 == 0 ? 29 : 28;
	daysPerMonth[3] = 31;
	daysPerMonth[4] = 30;
	daysPerMonth[5] = 31;
	daysPerMonth[6] = 30;
	daysPerMonth[7] = 31;
	daysPerMonth[8] = 31;
	daysPerMonth[9] = 30;
	daysPerMonth[10] = 31;
	daysPerMonth[11] = 30;
	daysPerMonth[12] = 31;
	
	if (month < 1 || month > 12) {
		if (showAlert)
			alert("MM (Mes) debe estar entre 01 y 12");
		
		if (textInput.form.tmp)
			textInput.value = textInput.form.tmp.value;
		else
			textInput.value = '';
		
		textInput.select();
		return false;
	}
	
	if (day < 1 || day > daysPerMonth[month]) {
		if (showAlert)
			alert("DD (Día) debe estar entre 01 y " + daysPerMonth[month]);
		
		if (textInput.form.tmp)
			textInput.value = textInput.form.tmp.value;
		else
			textInput.value = '';
		
		textInput.select();
		return false;
	}
	
	textInput.value = (day < 10 ? '0' + day : day) + '/' + (month < 10 ? '0' + month : month) + '/' + year;
	return true;
}

// Objeto Numero
function objNumero(numero) {
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
				cad = new String(num);//console.log(cad);
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

function numberFormat(numero, presicion) {
	var tmp = new objNumero(numero);
	
	return tmp.formato(presicion, true);
}

function inputFormat(input, pre, pos_only) {
	if (input.value == "" || input.value == "0") {
		input.value = "";
		return true;
	}
	else if (isNaN(parseFloat(input.value.replace(/\,/g, "")))) {
		alert("Solo se permiten números");
		input.value = input.form.tmp.value;
		return false;
	}
	
	var value = parseFloat(input.value.replace(/\,/g, ""));
	
	if (pos_only && value < 0) {
		alert("No se permiten números negativos");
		input.value = input.form.tmp.value;
		return false;
	}
	
	input.value = numberFormat(value, pre);
	
	return true;
}