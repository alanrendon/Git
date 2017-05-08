// JavaScript Document

function validarTel(obj) {
	if (obj.value == '')
		return true;
	
	var patron = /(01|044|045)?(800|900|\d{2})?(\d{4})(\d{4})/;
	
	if (patron.test(obj.value))
	{
		var partes = patron.exec(obj.value);
	}
	else
	{
		alert('Error: el campo solo acepta n˙meros y el formato [01|044|045][Lada(2 dÌgitos)][N˙mero(8 dÌgitos)]');
		
		obj.value = f.tmp.value;
		obj.select();
		return false;
	}
	
	obj.value = (partes[1] != undefined) || (partes[2] != undefined) ? '(' : '';
	obj.value += partes[1] != undefined ? partes[1] + ' ' : '';
	obj.value += partes[2] != undefined ? partes[2] + ') ' : '';
	obj.value += partes[3] + ' ' + partes[4];
}

function validarRFC(obj) {
	if (obj.value == '')
	{
		return true;
	}
	
	var patron = /^([a-zA-Z]{3,4})(\d{2})([0|1]{1})(\d{1})([0|1|2|3]{1})(\d{1})([a-zA-Z0-9]{3})?$/;
	if (patron.test(obj.value))
	{
		var partes = patron.exec(obj.value);
	}
	else
	{
		alert('Error: la sintaxis del RFC es incorrecta');
		
		obj.value = f.tmp.value;
		obj.select();
		return false;
	}
	
	var anio = parseInt(partes[2], 10) >= 70 ? parseInt(partes[2], 10) + 1900 : parseInt(partes[2], 10) + 2000;
	var mes = parseInt(partes[3] + partes[4], 10);
	var dia = parseInt(partes[5] + partes[6], 10);
	console.log(anio + '-' + mes + '-' + dia);
	if (mes < 1 || mes > 12)
	{
		alert('Error: el mes debe estar entre 01 y 12');
		
		obj.value = f.tmp.value;
		obj.select();
		return false;
	}
	
	var diasxmes = [31, anio % 4 == 0 ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
	if (dia < 1 || dia > diasxmes[mes - 1])
	{
		alert('Error: el dia debe estar entre 01 y ' + diasxmes[mes - 1]);
		
		obj.value = f.tmp.value;
		obj.select();
		return false;
	}
}

function validarEmail(obj) {
	if (obj.value == '')
	{
		return true;
	}
	
	var patron = /^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	if (patron.test(obj.value))
	{
		return true;
	}
	else
	{
		alert('Error: el formato del email es incorrecto, deberia ser por ejemplo \'nombre@hotmail.com\'');
		
		obj.value = f.tmp.value;
		obj.select();
		return false;
	}
}

function toText(obj) {
	obj.value = obj.value.replace(/[^a-zA-ZÒ—0-9\s\,\.]|^\s+|\s+$/g, '');
	obj.value = obj.value.replace(/\s{2,}/g, ' ');
}