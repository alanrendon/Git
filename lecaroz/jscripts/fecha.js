// JavaScript Document
function actualiza_fecha(input_fecha) {
	var fecha = input_fecha.value;
	var temp = new Date();
	var anio_actual = temp.getFullYear();
	var mes_actual  = temp.getMonth();
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
			
			if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
				if (dia >= 1 && dia <= diasxmes[mes] && mes <= 12) {
					input_fecha.value = dia+"/"+mes+"/"+anio;
					return true;
				}
			}
			else {
				input_fecha.value = "";
				alert("Rango de fecha no valido");
				input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = "";
			alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
			input_fecha.focus();
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
		anio = parseInt(fecha.substring(4),10);
		if (anio > 20)
			anio = anio + 1900;
		else
			anio = anio + 2000;
		
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
			
			if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
				if (dia >= 1 && dia <= diasxmes[mes] && mes <= 12) {
					input_fecha.value = dia+"/"+mes+"/"+anio;
					return true;
				}
			}
			else {
				input_fecha.value = "";
				alert("Rango de fecha no valido");
				input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = "";
			alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
			input_fecha.focus();
			return false;
		}
	}
	else {
		input_fecha.value = "";
		alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
		input_fecha.focus();
		return false;
	}
}

function fecha(input_fecha) {
	var fecha = input_fecha.value;
	var temp = new Date();
	var anio_actual = 1900;
	var mes_actual  = temp.getMonth();
	var dia_actual  = temp.getDate();
	
	// Si la fecha tiene el formato ddmmaaaa
	if (fecha.length == 8) {
		// Descomponer fecha en dia, mes y año
		if (parseInt(fecha.charAt(0),10) == 0)
			dia = parseInt(fecha.charAt(1),10);
		else
			dia = parseInt(fecha.substring(0,2),10);
		if (parseInt(fecha.charAt(2)) == 0)
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
			
			if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
				if (dia >= 1 && dia <= diasxmes[mes] && mes <= 12) {
					input_fecha.value = (dia > 9 ? dia : '0' + dia) + "/" + (mes > 9 ? mes : '0' + mes) + "/" + anio;
					return true;
				}
			}
			else {
				input_fecha.value = "";
				alert("Rango de fecha no valido");
				input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = "";
			alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
			input_fecha.focus();
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
			
			if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
				if (dia >= 1 && dia <= diasxmes[mes] && mes <= 12) {
					input_fecha.value = (dia > 9 ? dia : '0' + dia) + "/" + (mes > 9 ? mes : '0' + mes) + "/" + anio;
					return true;
				}
			}
			else {
				input_fecha.value = "";
				alert("Rango de fecha no valido");
				input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = "";
			alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
			input_fecha.focus();
			return false;
		}
	}
	else if (fecha.length == 10) {
		var partes = fecha.split("/");
		
		var dia = parseInt(partes[0], 10);
		var mes = parseInt(partes[1], 10);
		var anio = parseInt(partes[2], 10);
		
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
			
			if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
				if (dia >= 1 && dia <= diasxmes[mes] && mes <= 12) {
					input_fecha.value = (dia > 9 ? dia : '0' + dia) + "/" + (mes > 9 ? mes : '0' + mes) + "/" + anio;
					return true;
				}
			}
			else {
				input_fecha.value = "";
				alert("Rango de fecha no valido");
				input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = "";
			alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
			input_fecha.focus();
			return false;
		}
	}
	else {
		input_fecha.value = "";
		alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
		input_fecha.focus();
		return false;
	}
}

function actualiza_fecha2(input_fecha, fecha_temp) {
	var fecha = input_fecha.value;
	var temp = new Date();
	var anio_actual = temp.getFullYear();
	var mes_actual  = temp.getMonth();
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
			
			if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
				if (dia >= 1 && dia <= diasxmes[mes] && mes <= 12) {
					input_fecha.value = (dia > 9 ? dia : '0' + dia) + "/" + (mes > 9 ? mes : '0' + mes) + "/" + anio;
					return true;
				}
			}
			else {
				input_fecha.value = fecha_temp.value;
				alert("Rango de fecha no valido");
				input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = fecha_temp.value;
			alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
			input_fecha.focus();
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
		anio = parseInt(fecha.substring(4),10);
		if (anio > 20)
			anio = anio + 1900;
		else
			anio = anio + 2000;
		
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
			
			if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
				if (dia >= 1 && dia <= diasxmes[mes] && mes <= 12) {
					input_fecha.value = (dia > 9 ? dia : '0' + dia) + "/" + (mes > 9 ? mes : '0' + mes) + "/" + anio;
					return true;
				}
			}
			else {
				input_fecha.value = fecha_temp.value;
				alert("Rango de fecha no valido");
				input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = fecha_temp.value;
			alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
			input_fecha.focus();
			return false;
		}
	}
	else if (fecha.length == 10) {
		var partes = fecha.split("/");
		
		var dia = parseInt(partes[0], 10);
		var mes = parseInt(partes[1], 10);
		var anio = parseInt(partes[2], 10);
		
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
			
			if (mes >= 1 && mes <= 12 && dia >= 1 && dia <= 31) {
				if (dia >= 1 && dia <= diasxmes[mes] && mes <= 12) {
					input_fecha.value = (dia > 9 ? dia : '0' + dia) + "/" + (mes > 9 ? mes : '0' + mes) + "/" + anio;
					return true;
				}
			}
			else {
				input_fecha.value = fecha_temp.value;
				alert("Rango de fecha no valido");
				input_fecha.focus();
				return false;
			}
		}
		else {
			input_fecha.value = fecha_temp.value;
			alert("Año no valido. Debe ser igual o menor al año en curso ("+anio_actual+")");
			input_fecha.focus();
			return false;
		}
	}
	else {
		input_fecha.value = fecha_temp.value;
		alert("Formato de fecha no válido. Solo se admite 'ddmmaaaa'");
		input_fecha.focus();
		return false;
	}
}