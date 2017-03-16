<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Barra de Estatus</title>

<script type="text/javascript" language="javascript1.4" src="./jscripts/XHConn.js"></script>

<style type="text/css">
<!--
body {
	background-color: #73A8B7;
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #FFFFFF;
	font-size: 12pt;
}

a.link {
	color: #FFFFFF;
	text-decoration: none;
}

#boton {
	font-family: Arial, Helvetica, sans-serif;
	border: 1px ridge #EBF8FF;
	background: #B3CCD3 url(../imagenes/boton_back.jpg);
	font-weight: bold;  text-align: center;
	vertical-align: middle;
}

#contenedor {
	text-align: left;
	margin: auto;
}

#menu {
	text-align: center;
	/*padding-bottom: 15px;*/
}

#extras {
}

#usuario {
	/*padding-top: 15px;*/
	float:left;
}

#reloj {
	text-align:center;
}

#botones {
	/*padding-top: 15px;*/
	float:right;
}
-->
</style>
</head>

<body>
<div id="contenedor">
	<div id="menu">
	<!-- START BLOCK : menu -->
	|&nbsp;&nbsp;<a class="link" href="javascript:refreshFrames('{menupath}');">{menu}</a>&nbsp;
	<!-- END BLOCK : menu -->
	|
	</div>
	<div id="extras">
		<div id="usuario">
		Usuario: {user}
		</div>
		<div id="botones">
		<input name="boton" type="button" id="boton" value="Salir" onclick="exit()" />
		</div>
	</div>
</div>
<script language="javascript" type="text/javascript">
<!--
function refreshFrames(menu) {
	parent.topFrame.location = './menu.php?menu=' + menu;
	parent.mainFrame.location = './blank.php';
}

function exit() {
	if (confirm("¿Desea salir del sistema?"))
		parent.location = "./logout.php";
}

function verPenLuz() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verPenLuz.php", "GET", '', alertLuz);
}

var alertLuz = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open("","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
		win.document.writeln(result);
		win.focus();
	}
}

function verDepErr() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verDepErr.php", "GET", '', alertDep);
}

var alertDep = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open("","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
		win.document.writeln(result);
		win.focus();
	}
}

function verBlockPas() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verBlockPas.php", "GET", '', alertPas);
}

var alertPas = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open("","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
		win.document.writeln(result);
		win.focus();
	}
}

function verPedPen() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verPenPed.php", "GET", '', alertPedPen);
}

var alertPedPen = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open("","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
		win.document.writeln(result);
		win.focus();
	}
}

function verRenPen() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verRenPenV2.php", "GET", '', alertRenPen);
}

var alertRenPen = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open("","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
		win.document.writeln(result);
		win.focus();
	}
}

var alertEfe = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open("","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
		win.document.writeln(result);
		win.focus();
	}
}

function verEfe() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verEfe.php", "GET", '', alertPedPen);
}

var alertPedPen = function(oXML) {
	var result = oXML.responseText, fecha, ok = false;

	if (result > 0) {
		var win = window.open("./verEfe.php?fecha=1","","left=412,top=284,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=100");
		win.focus();
	}

	/*if (get_val(result) > 0) {
		do {
			fecha = prompt('Fecha de corte (dd/mm/aaaa)', '{fecha_efe}');

			if ((fecha == '' || fecha == null) && confirm('No especifico la fecha de corte. ¿Desea omitir este paso?'))
				return false;

			//var patron = /(\d{1,2})\/(\d{1,2})\/(\d{2,4})/;alert(fecha);alert(patron.exec(fecha));return false;
			var patron = /^((?:0?[1-9])|(?:[12]\d)|(?:3[01]))\/((?:0?[1-9])|(?:1[0-2]))\/((?:19|20)\d\d)$/;

			if (fecha != '' && !patron.test(fecha))
				alert('Formato de fecha incorrecta, debe ser dd/mm/aaaa');
			else
				ok = true;

		} while(!ok)

		var win = window.open("./ban_efe_red_v2.php?alert=1&fecha=" + fecha,"","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
		win.focus();
	}*/
}

// [28-Feb-2008] Alerta de Fichas de depósito pendientes
function verFicDepPen() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verFicDepPen.php", "GET", '', alertFicDepPen);
}

var alertFicDepPen = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open("","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
		win.document.writeln(result);
		win.focus();
	}
}

// [24-Abr-2008] Alerta de movimientos pendientes por conciliar
function verMovPen() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verMovPen.php", "GET", '', alertMovPen);
}

var alertMovPen = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open("","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768");
		win.document.writeln(result);
		win.focus();
	}
}

// [21-Jul-2008] Alerta de facturas próximas a vencer
function verVenFac() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verVenFac.php", "GET", '', alertVenFac);
}

var alertVenFac = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open('', '', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.document.writeln(result);
		win.focus();
	}
}
// [21-Jul-2008] Alerta de facturas próximas a vencer
function verDifSal() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verDifSal.php", "GET", '', alertDifSal);
}

var alertDifSal = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open('', '', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.document.writeln(result);
		win.focus();
	}
}

// [22-Ene-2009] Contratos de renta por vencer
function verRenVen() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./verRenVen.php", "GET", 'buscar=1', alertRenVen);
}

var alertRenVen = function(oXML) {
	var result = oXML.responseText;

	if (result == '1') {
		var win = window.open('verRenVen.php', 'RenVen', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.focus();
	}
}

// [20-Feb-2009] Asuntos pendientes
function verPenAsu() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('./verPenAsu.php', 'GET', '', alertPenAsu);
}

var alertPenAsu = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open('', '', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.document.writeln(result);
		win.focus();
	}
}

// [13-Abr-2009] Consumos de avio autorizados por operadora
function verConAut() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('./verConAut.php', 'GET', '', alertConAut);
}

var alertConAut = function(oXML) {
	var result = oXML.responseText;

	if (result.length > 0) {
		var win = window.open('', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.document.writeln(result);
		win.focus();
	}
}

// [27-Ago-2009] Extintores proximos a caducar
function verExtCad() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('./verExtCad.php', 'GET', 'status=1', alertExtCad);
}

var alertExtCad = function(oXML) {
	var result = oXML.responseText;

	if (result == '1') {
		var win = window.open('verExtCad.php', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.focus();
	}
}

// [04-Oct-2012] Contratos de trabajadores sin firmar
function verFirConTra() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('AlertaTrabajadoresNoFirmaContrato.php', 'GET', 'accion=verificar', alertFirConTra);
}

var alertFirConTra = function(oXML) {
	var result = oXML.responseText;

	if (result == '1') {
		var win = window.open('AlertaTrabajadoresNoFirmaContrato.php?accion=listado', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.focus();
	}
}

// [28-Mar-2011] Contratos de trabajadores vencidos
function verConTra() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('AlertaTrabajadoresContratosVencidos.php', 'GET', 'accion=verificar', alertConTra);
}

var alertConTra = function(oXML) {
	var result = oXML.responseText;

	if (result == '1') {
		var win = window.open('AlertaTrabajadoresContratosVencidos.php?accion=listado', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.focus();
	}
}

// [28-Mar-2011] Contratos de trabajadores proximos a vencer
function verConTraPro() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('AlertaTrabajadoresContratosProximosVencer.php', 'GET', 'accion=verificar', alertConTraPro);
}

var alertConTraPro = function(oXML) {
	var result = oXML.responseText;

	if (result == '1') {
		var win = window.open('AlertaTrabajadoresContratosProximosVencer.php?accion=listado', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.focus();
	}
}

// [03-Nov-2012] Trabajadores afiliados y no afiliados
function verConTraAfi() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('AlertaTrabajadoresAfiliados.php', 'GET', 'accion=verificar', alertConTraAfi);
}

var alertConTraAfi = function(oXML) {
	var result = oXML.responseText;

	if (result == '1') {
		var win = window.open('AlertaTrabajadoresAfiliados.php?accion=listado', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.focus();
	}
}

// [24-Nov-2013] Facturas validadas vencidas
function verFacValVen() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('AlertaFacturasValidadasVencidas.php', 'GET', 'accion=verificar', alertFacValVen);
}

var alertFacValVen = function(oXML) {
	var result = oXML.responseText;

	if (result == '1') {
		var win = window.open('AlertaFacturasValidadasVencidas.php?accion=listado', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.focus();
	}
}

// [11-Dic-2014] Reporte de saldos para Juan Miguel
function verSaldosConsolidados() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('ban_sal_con_v2.php', 'GET', 'check=1', alertSaldosConsolidados);
}

var alertSaldosConsolidados = function(oXML) {
	var result = oXML.responseText;

	if (result == '1') {
		var win = window.open('ban_sal_con_v2.php?cuenta=0&admin=&conta=', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.focus();
	}
}

// [19-Dic-2014] Licencias de choferes proximas a vencer
function verLicenciasProximasVencer() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('AlertaTrabajadoresLicenciasProximasVencer.php', 'GET', 'accion=verificar', alertLicenciasProximasVencer);
}

var alertLicenciasProximasVencer = function(oXML) {
	var result = oXML.responseText;

	if (result == '1') {
		var win = window.open('AlertaTrabajadoresLicenciasProximasVencer.php?accion=listado', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.focus();
	}
}

// [22-Jul-2015] Documentos faltantes de trabajadores
function verDocumentosFaltantes() {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect('AlertaTrabajadoresDocumentosFaltantes.php', 'GET', 'accion=verificar', alertDocumentosFaltantes);
}

var alertDocumentosFaltantes = function(oXML) {
	var result = oXML.responseText;

	if (result == '1') {
		var win = window.open('AlertaTrabajadoresDocumentosFaltantes.php?accion=listado', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		win.focus();
	}
}

function get_val(str) {
	var val;

	if (str.indexOf('.') >= 0)
		val = !isNaN(parseFloat(str.replace(/\,/g, ''))) ? parseFloat(str.replace(/\,/g, '')) : 0;
	else
		val = !isNaN(parseInt(str.replace(/\,/g, ''))) ? parseInt(str.replace(/\,/g, '')) : 0;

	return val;
}

window.onload = function () { verPenLuz(); verDepErr(); verBlockPas(); verPedPen(); verEfe(); verRenPen(); verFicDepPen(); verMovPen(); verVenFac(); verDifSal(); verRenVen(); verPenAsu(); verConAut(); verExtCad(); verFirConTra(); verConTra(); verConTraPro(); verConTraAfi(); verFacValVen(); verSaldosConsolidados(); verLicenciasProximasVencer(); verDocumentosFaltantes(); };
//-->
</script>
</body>
</html>
