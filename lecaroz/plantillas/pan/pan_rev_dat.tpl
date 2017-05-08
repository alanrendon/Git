<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/lecaroz/jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools/Array.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools/FormValidator.js"></script>
</head>

<body>
<!-- START BLOCK : cias -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Revisi&oacute;n de Datos de Panader&iacute;as</p>
			<form action="pan_rev_dat.php" method="post" name="form">
				<input name="action" type="hidden" id="action" value="hoja">
				<input name="tmp" type="hidden" id="tmp">
				<table class="tabla">
					<tr>
						<th class="tabla">Usuario</th>
					</tr>
					<tr>
						<td class="tabla" style="font-size:14pt; font-weight:bold;">{usuario}</td>
					</tr>
				</table>
				<br>
				<table class="tabla">
					<tr>
						<th colspan="2" class="tabla">Compa&ntilde;&iacute;a</th>
						<th class="tabla">Fecha</th>
						<th class="tabla">&nbsp;</th>
						<!--<th class="tabla">Directo</th>-->
					</tr>
					<!-- START BLOCK : cia -->

						<tr{bgcolor}>


						<td class="vtabla"><input name="opt" type="radio" value="{opt}" onClick="next.disabled=false;tmp.value=this.value"{disabled}></td>
						<td class="vtabla" style="font-size:12pt; font-weight:bold;">{num_cia} - {nombre} </td>
						<td class="tabla" style="font-size:12pt; font-weight:bold;">{fecha}</td>
						<td class="tabla" style="font-size:12pt; font-weight:bold;"><input type="button" value="Saltar" onClick="saltar({num_cia},'{fecha}')">
							<input type="button" onClick="borrar({num_cia},'{fecha}')" value="Borrar">
							<input type="button" value="Arreglar duplicado" onClick="duplicado({num_cia},'{fecha}')"></td>
						<!--<td class="tabla" style="font-size:12pt; font-weight:bold;"><input type="button" class="boton" value="..." onClick="insertarEfectivoDirecto('{opt}')"></td>-->
					</tr>
					<!-- START BLOCK : void -->
					<tr>
						<td colspan="5" class="tabla" style="font-size:12pt; font-weight:bold;">&nbsp;</td>
					</tr>
					<!-- END BLOCK : void -->
					<!-- END BLOCK : cia -->
					<!-- END BLOCK : no_cias -->
					<tr>
						<td colspan="5" class="tabla" style="font-size:12pt; font-weight:bold;">No se ha recibo nueva infromaci&oacute;n por parte de las panaderias </td>
					</tr>
					<!-- END BLOCK : no_cias -->
				</table>
				<p>
					<input name="next" type="button" disabled="true" class="boton" value="Siguiente" onClick="validar()">
				</p>
			</form></td>
	</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function insertarEfectivoDirecto(opt) {
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./insEfeDir.php", "GET", 'opt=' + opt, alertaEfectivoDirecto);
}

var alertaEfectivoDirecto = function (oXML) {
	alert(oXML.responseText);
}

function saltar(num_cia, fecha) {
	if (confirm('¿Desea saltar el día de esta compañía?')) {
		var url = 'pan_rev_dat.php?action=saltar&num_cia=' + num_cia + '&fecha=' + fecha;

		document.location = url;
	}
}

function borrar(num_cia, fecha) {
	if (confirm('¿Desea borrar los datos del día de esta compañía?')) {
		var url = 'pan_rev_dat.php?action=borrar&num_cia=' + num_cia + '&fecha=' + fecha;

		document.location = url;
	}
}

function duplicado(num_cia, fecha) {
	var url = 'pan_rev_dat.php?action=duplicado&num_cia=' + num_cia + '&fecha=' + fecha;

	document.location = url;
}

function validar() {
	var tmp = f.tmp.value.split('|'), fecha = tmp[1].split('/'), dia = get_val2(fecha[0]), diasxmes = new Array(), nomina = 0;
	diasxmes[1] = 31;
	diasxmes[2] = get_val2(fecha[2]) % 4 == 0 ? 29 : 28;
	diasxmes[3] = 31;
	diasxmes[4] = 30;
	diasxmes[5] = 31;
	diasxmes[6] = 30;
	diasxmes[7] = 31;
	diasxmes[8] = 31;
	diasxmes[9] = 30;
	diasxmes[10] = 31;
	diasxmes[11] = 30;
	diasxmes[12] = 31;

	// [17-Dic-2007] Si el día de de revisión cae en decena o fin de mes pedir el importe de la nómina,
	// para posteriormente compararlo en la pantalla de gastos
	if (dia == 10 || dia == 20 || dia == diasxmes[get_val2(fecha[2])]) {
		nomina = get_val2(prompt('Introdusca el importe de nómina'));

		if (nomina == 0) {
			alert('No puede continuar sin introducir el importe de nómina');
			return false;
		}
	}

	var url = './pan_rev_dat.php?action=hoja&num_cia=' + tmp[0] + '&fecha=' + escape(tmp[1]) + '&dir=r&nom=' + nomina;
	document.location = url;
}
//-->
</script>
<!-- END BLOCK : cias -->
<!-- START BLOCK : hoja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Hoja de Diario</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<table>
				<tr>
					<td colspan="3" valign="top"><table width="100%" class="tabla">
							<tr>
								<th class="tabla">Turno</th>
								<th class="tabla">Producci&oacute;n</th>
								<th class="tabla">Importe Raya </th>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Frances Noche </td>
								<td class="rtabla" style="color:#0000CC;">{pro2}&nbsp;</td>
								<td class="rtabla" style="color:#CC0000;">{raya2}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Frances D&iacute;a </td>
								<td class="rtabla" style="color:#0000CC;">{pro1}&nbsp;</td>
								<td class="rtabla" style="color:#CC0000;">{raya1}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Bizcocheros</td>
								<td class="rtabla" style="color:#0000CC;">{pro3}&nbsp;</td>
								<td class="rtabla" style="color:#CC0000;">{raya3}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Reposteros</td>
								<td class="rtabla" style="color:#0000CC;">{pro4}&nbsp;</td>
								<td class="rtabla" style="color:#CC0000;">{raya4}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Piconeros</td>
								<td class="rtabla" style="color:#0000CC;">{pro8}&nbsp;</td>
								<td class="rtabla" style="color:#CC0000;">{raya8}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Gelatineros</td>
								<td class="rtabla" style="color:#0000CC;">{pro9}&nbsp;</td>
								<td class="rtabla" style="color:#CC0000;">{raya9}&nbsp;</td>
							</tr>
							<tr>
								<th class="tabla">Suma</th>
								<th class="rtabla" style="color:#0000CC; font-size:14pt;">{pro}&nbsp;</th>
								<th class="rtabla" style="color:#CC0000; font-size:14pt;">{raya}&nbsp;</th>
							</tr>
						</table></td>
					<td>&nbsp;</td>
					<td rowspan="5" valign="top"><table width="100%" class="tabla">
							<tr>
								<th class="tabla">Gastos</th>
								<th class="tabla">Importe</th>
							</tr>
							<!-- START BLOCK : gasto_hoja -->
							<tr>
								<td class="vtabla">{concepto}</td>
								<td class="rtabla">{importe}</td>
							</tr>
							<!-- END BLOCK : gasto_hoja -->
							<tr>
								<th class="tabla">Total de Gastos </th>
								<th class="rtabla" style="font-size:14pt;">{total_gastos}</th>
							</tr>
						</table></td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td valign="top"><table width="100%" class="tabla">
							<tr>
								<th colspan="3" class="tabla">Rendimiento</th>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">F.N.</td>
								<td class="rtabla">{bultos2}&nbsp;</td>
								<td class="rtabla">{ren2}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">F.D.</td>
								<td class="rtabla">{bultos1}&nbsp;</td>
								<td class="rtabla">{ren1}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">B.D.</td>
								<td class="rtabla">{bultos3}&nbsp;</td>
								<td class="rtabla">{ren3}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">REP.</td>
								<td class="rtabla">{bultos4}&nbsp;</td>
								<td class="rtabla">{ren4}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">PIC.</td>
								<td class="rtabla">{bultos8}&nbsp;</td>
								<td class="rtabla">{ren8}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">GEL.</td>
								<td class="rtabla">{bultos9}&nbsp;</td>
								<td class="rtabla">{ren9}&nbsp;</td>
							</tr>
						</table></td>
					<td>&nbsp;</td>
					<td>
						<table width="100%" class="tabla">
							<tr>
								<th class="tabla">Agua</th>
								<th class="tabla">Medici&oacute;n</th>
								<th class="tabla">Hora</th>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Toma 1 </td>
								<td class="rtabla">{med1}&nbsp;</td>
								<td class="tabla">{hora1}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Toma 2</td>
								<td class="rtabla">{med2}&nbsp;</td>
								<td class="tabla">{hora2}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Toma 3 </td>
								<td class="rtabla">{med3}&nbsp;</td>
								<td class="tabla">{hora3}&nbsp;</td>
							</tr>
							<tr>
								<th class="tabla">Camioneta</th>
								<th class="tabla">Km</th>
								<th class="tabla">Dinero</th>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Unidad 1 </td>
								<td class="rtabla">{km1}&nbsp;</td>
								<td class="rtabla">{din1}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Unidad 2 </td>
								<td class="rtabla">{km2}&nbsp;</td>
								<td class="rtabla">{din2}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Unidad 3 </td>
								<td class="rtabla">{km3}&nbsp;</td>
								<td class="rtabla">{din3}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Unidad 4 </td>
								<td class="rtabla">{km4}&nbsp;</td>
								<td class="rtabla">{din4}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Undiad 5 </td>
								<td class="rtabla">{km5}&nbsp;</td>
								<td class="rtabla">{din5}&nbsp;</td>
							</tr>
						</table>
						<!-- START BLOCK : tanques -->
						<table width="100%" class="tabla">
							<tr>
								<th class="tabla">Tanque</th>
								<th class="tabla">Capacidad</th>
								<th class="tabla">Lectura</th>
								<th class="tabla">Entrada</th>
								<th class="tabla">Nota</th>
							</tr>
							<!-- START BLOCK : tanque -->
							<tr>
								<td class="vtabla" style="font-weight:bold;">{num_tanque} {nombre_tanque}</td>
								<td class="rtabla">{capacidad}</td>
								<td class="rtabla">{lectura}</td>
								<td class="rtabla">{entrada}</td>
								<td class="rtabla">{nota_entrada}</td>
							</tr>
							<!-- END BLOCK : tanque -->
						</table>
						<!-- END BLOCK : tanques -->
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td valign="top"><table width="100%" class="tabla">
							<tr>
								<th class="tabla">Corte</th>
								<th class="tabla">Caja</th>
								<th class="tabla">Clientes</th>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">A.M.</td>
								<td class="rtabla">{am}&nbsp;</td>
								<td class="rtabla">{clientes_am}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Error</td>
								<td class="rtabla" style="color:#CC0000;">{error_am}&nbsp;</td>
								<td class="rtabla" style="color:#CC0000;">{error_clientes_am}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">P.M.</td>
								<td class="rtabla">{pm}&nbsp;</td>
								<td class="rtabla">{clientes_pm}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Error</td>
								<td class="rtabla" style="color:#CC0000;">{error_pm}&nbsp;</td>
								<td class="rtabla" style="color:#CC0000;">{error_clientes_pm}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Pastel A.M.</td>
								<td class="rtabla">{pastel_am}&nbsp;</td>
								<td class="rtabla">{clientes_am_pastel}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Pastel P.M.</td>
								<td class="rtabla">{pastel_pm}&nbsp;</td>
								<td class="rtabla">{clientes_pm_pastel}&nbsp;</td>
							</tr>
							<tr>
								<th class="vtabla">TOTAL C.</th>
								<th class="rtabla" style="font-size:14pt;">{total_caja}&nbsp;</th>
								<th class="rtabla" style="font-size:14pt;">{total_clientes}&nbsp;</th>
							</tr>
							<!-- START BLOCK : error_leyenda -->
							<tr>
								<td class="tabla" style="font-size:12pt;font-weight:bold;color:#C00;" colspan="3">La suma de errores no debe pasar de 50 pesos </td>
							</tr>
							<!-- END BLOCK : error_leyenda -->
							<!-- START BLOCK : error_clientes -->
							<tr>
								<td class="tabla" style="font-size:12pt;font-weight:bold;color:#C00;" colspan="3">La cantidad de clientes difiere del promedio diario
									({prom_clientes})</td>
							</tr>
							<!-- END BLOCK : error_clientes -->
						</table></td>
					<td>&nbsp;</td>
					<td><table width="100%" class="tabla">
							<tr>
								<th colspan="2" class="tabla">Consecutivo de Corte
									<label>
										<input name="" type="button" class="boton" onClick="mod({num_cia},'{fecha}')" value="Mod.">
									</label></th>
							</tr>
							<tr>
								<th class="tabla">Pan</th>
								<th class="tabla">Pastel</th>
							</tr>
							<tr>
								<td class="rtabla">{corte_pan_1}&nbsp;</td>
								<td class="rtabla">{corte_pastel_1}&nbsp;</td>
							</tr>
							<tr>
								<td class="rtabla">{corte_pan_2}&nbsp;</td>
								<td class="rtabla">{corte_pastel_2}&nbsp;</td>
							</tr>
							<tr>
								<td class="rtabla">{corte_pan_3}&nbsp;</td>
								<td class="rtabla">{corte_pastel_3}&nbsp;</td>
							</tr>
							<tr>
								<td class="rtabla">{corte_pan_4}&nbsp;</td>
								<td class="rtabla">{corte_pastel_4}&nbsp;</td>
							</tr>
							<tr>
								<td class="rtabla">{corte_pan_5}&nbsp;</td>
								<td class="rtabla">{corte_pastel_5}&nbsp;</td>
							</tr>
							<tr>
								<td class="rtabla">{corte_pan_6}&nbsp;</td>
								<td class="rtabla">{corte_pastel_6}&nbsp;</td>
							</tr>
						</table></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" rowspan="3" valign="top"><form action="" method="post" name="fac">
							<table width="100%" class="tabla">
								<tr>
									<th colspan="2" class="tabla">Avio Recibido el d&iacute;a </th>
								</tr>
								<tr>
									<th class="tabla">Proveedor</th>
									<th class="tabla">Factura</th>
								</tr>
								<!-- START BLOCK : avio_rec -->
								<tr>
									<td class="vtabla"><input name="facid[]" type="checkbox" id="facid" value="{id}"{checked}>
										{prov}</td>
									<td class="vtabla">{fac}</td>
								</tr>
								<!-- END BLOCK : avio_rec -->
							</table>
						</form></td>
					<td rowspan="3">&nbsp;</td>
					<td><table width="100%" height="100%" class="tabla">
							<tr>
								<th class="tabla">Observaciones</th>
							</tr>
							<tr>
								<td class="vtabla">&nbsp;{observaciones}</td>
							</tr>
						</table></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><table width="100%" class="tabla">
							<tr>
								<th colspan="2" class="tabla">Prueba de Pan </th>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Sobrante de Ayer </td>
								<td class="rtabla">{sobrante_ayer}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Producci&oacute;n</td>
								<td class="rtabla">{pro}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Pan Comprado </td>
								<td class="rtabla">{pan_comprado}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Total D&iacute;a </td>
								<td class="rtabla">{total_dia}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Venta en Puerta </td>
								<td class="rtabla">{venta_puerta}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Venta Reparto </td>
								<td class="rtabla">{reparto}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Descuento</td>
								<td class="rtabla">{desc}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Sobrante para Ma&ntilde;ana</td>
								<td class="rtabla">{sobrante_manana}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Pan Contado </td>
								<td class="rtabla">{pan_contado}&nbsp;</td>
							</tr>
							<tr>
								<th class="vtabla" style="font-weight:bold;">Faltante</th>
								<th class="rtabla">{faltante}&nbsp;</th>
							</tr>
						</table></td>
				</tr>
				<tr>
					<td colspan="3" valign="top">&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td rowspan="3" valign="top"><form action="./pan_shift_otros.php" method="get" name="otros">
							<table width="100%" class="tabla">
								<tr>
									<th class="tabla" scope="row"><input type="button" class="boton" value="S" onClick="shift_otros({num_cia},'{fecha}','up')"></th>
									<th colspan="2" class="tabla" scope="row">Prueba de Efectivo </th>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;">&nbsp;</td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Cambio Ayer </td>
									<td class="rtabla">{cambio_ayer}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;"><input name="item[]" type="checkbox" id="item" value="barredura"></td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Barredura</td>
									<td class="rtabla">{barredura}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;"><input name="item[]" type="checkbox" id="item" value="pasteles"></td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Pasteles</td>
									<td class="rtabla">{pasteles}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;"><input name="item[]" type="checkbox" id="item" value="bases"></td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Bases</td>
									<td class="rtabla">{bases}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;"><input name="item[]" type="checkbox" id="item" value="esquilmos"></td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Esquilmos</td>
									<td class="rtabla"><input name="esquilmos" type="hidden" id="esquilmos" value="{esquilmos}">
										<input name="obs" type="hidden" id="obs" value="{obs}">
										{esquilmos}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;"><input name="item[]" type="checkbox" id="item" value="botes"></td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Botes</td>
									<td class="rtabla">{botes}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;"><input name="item[]" type="checkbox" id="item" value="pastillaje"></td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Pastillaje</td>
									<td class="rtabla">{pastillaje}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;"><input name="item[]" type="checkbox" id="item" value="costales"></td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Costales</td>
									<td class="rtabla">{costales}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;">&nbsp;</td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Abono Obreros</td>
									<td class="rtabla">{abono_obreros}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;">&nbsp;</td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Abonos</td>
									<td class="rtabla">{abonos}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;">&nbsp;</td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Puerta</td>
									<td class="rtabla">{total_caja}&nbsp;</td>
								</tr>
								<tr>
									<td class="tabla" scope="row" style="font-weight:bold;">&nbsp;</td>
									<td class="vtabla" scope="row" style="font-weight:bold;">Tiempo aire</td>
									<td class="rtabla">{tiempo_aire}&nbsp;</td>
								</tr>
								<tr>
									<th class="vtabla" scope="row"><input type="button" class="boton" value="B" onClick="shift_otros({num_cia},'{fecha}','dn')"></th>
									<th class="vtabla" scope="row">Suma</th>
									<th class="rtabla" style="font-size:14pt;">{suma_prueba1}&nbsp;</th>
								</tr>
							</table>
						</form></td>
					<td rowspan="3" valign="top">&nbsp;</td>
					<td valign="top"><table width="100%" class="tabla">
							<tr>
								<th colspan="2" class="tabla">Pastillaje</th>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Existencia</td>
								<td class="rtabla">{existencia_inicial}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Venta del d&iacute;a </td>
								<td class="rtabla">{venta_pastillaje}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Compras</td>
								<td class="rtabla"><input name="compra_pastillaje" type="hidden" id="compra_pastillaje" value="{compra_pastillaje}">
								{compra_pastillaje}&nbsp;</td>
							</tr>
							<tr>
								<th class="vtabla">Existencia FInal </th>
								<th class="rtabla" style="font-size:14pt;">{existencia_final}&nbsp;</th>
							</tr>
						</table></td>
					<td rowspan="3">&nbsp;</td>
					<td rowspan="3" valign="top"><table width="100%" class="tabla">
							<tr>
								<th colspan="5" class="tabla">Prestamos a Plazo </th>
							</tr>
							<tr>
								<th class="tabla">Nombre</th>
								<th class="tabla">Saldo Anterior</th>
								<th class="tabla">Prestamo</th>
								<th class="tabla">Abono</th>
								<th class="tabla">Saldo Actual </th>
							</tr>
							<!-- START BLOCK : prestamo_hoja -->
							<tr>
								<td class="vtabla">{nombre}&nbsp;</td>
								<td class="rtabla">{saldo_ant}&nbsp;</td>
								<td class="rtabla" style="color:#CC0000;">{cargo}&nbsp;</td>
								<td class="rtabla" style="color:#0000CC;">{abono}&nbsp;</td>
								<td class="rtabla">{saldo_act}&nbsp;</td>
							</tr>
							<!-- END BLOCK : prestamo_hoja -->
							<tr>
								<th class="rtabla">Total</th>
								<th class="rtabla" style="font-size:14pt;">{saldo_ant}&nbsp;</th>
								<th class="rtabla" style="color:#CC0000; font-size:14pt;">{cargo}</th>
								<th class="rtabla" style="color:#0000CC; font-size:14pt;">{abono_obreros}&nbsp;</th>
								<th class="rtabla" style="font-size:14pt;">{saldo_act}&nbsp;</th>
							</tr>
						</table>
						{pres_max} </td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td valign="bottom"><table width="100%" class="tabla">
							<tr>
								<td class="vtabla" style="font-weight:bold;">Efectivo</td>
								<td class="rtabla">{efectivo}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Gastos</td>
								<td class="rtabla">{total_gastos}&nbsp;</td>
							</tr>
							<tr>
								<td class="vtabla" style="font-weight:bold;">Raya</td>
								<td class="rtabla">{raya}&nbsp;</td>
							</tr>
							<tr>
								<th class="vtabla">Suma</th>
								<th class="rtabla" style="font-size:14pt;">{suma_prueba2}&nbsp;</th>
							</tr>
						</table></td>
				</tr>
			</table>
			<p>
				<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Imprimir" onClick="imprimir({num_cia},'{fecha}')">
				&nbsp;&nbsp;
				<input name="next" type="button" class="boton" id="next" onClick="validar('r')" value="Siguiente >>"{disabled}>
			</p></td>
	</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.fac;
var limite_pan_contado = {_limite_pan_contado};
var pan_contado = {_pan_contado};

function imprimir(num_cia, fecha) {
	var url = './hoja_diaria.php?num_cia=' + num_cia + '&fecha=' + fecha;
	var win = window.open(url, "mod", "toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=1024,height=768");
	win.focus();
}

function validar(dir) {
	var obs = '';

	if (pan_contado > limite_pan_contado) {
		var por_dif = pan_contado * 100 / limite_pan_contado - 100;

		var txt = 'El pan contado sobrepasa el 50% de la producción promedio mensual.\n';
		txt += '\n50% Produccion mensual\t\t' + numberFormat(limite_pan_contado, 2);
		txt += '\nPan contado\t\t\t\t\t' + numberFormat(pan_contado, 2) + ' (+' + numberFormat(por_dif, 2) + '%)';
		txt += '\n\n¿Desea validar la hoja autorizando el pan contado (se guardara un registro)?';

		if (confirm(txt)) {
		}
		else {
			return false;
		}
	}

	if (get_val(document.getElementById('esquilmos')) > 0 && document.getElementById('obs').value.replace(/^\s+|\s+$/g, '').replace(/\s+/g, ' ') == '') {
		do {
			obs = prompt('Debe escribir una observación sobre los esquilmos');

			if (obs != null) {
				obs = obs.replace(/^\s+|\s+$/g, '').replace(/\s+/g, ' ');
			}
			else {
				return false;
			}
		} while (obs == '');

		document.getElementById('obs').value = obs;
	}

	f.action = './pan_rev_dat.php?action=fac_mod&num_cia={num_cia}&fecha={fecha}&dir=' + dir + '&nom={nom}&compra_pastillaje=' + $('compra_pastillaje').get('value').getNumericValue() + (get_val(document.getElementById('esquilmos')) > 0 ? '&obs=' + document.getElementById('obs').value : '');
	f.submit();
}

function shift_otros(num_cia, fecha, dir) {
	document.otros.action = './pan_mod_otros.php?num_cia=' + num_cia + '&fecha=' + fecha + '&dir=' + dir;
	var win = window.open('', "shift", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=400");
	win.focus();
	document.otros.submit();
}

function mod(num_cia, fecha) {
	var url = './pan_mod_ticket.php?num_cia=' + num_cia + '&fecha=' + fecha;
	var win = window.open(url, "mod", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=300,height=400");
	win.focus();
}

function nomina(num_cia, fecha) {
	// Desglozar fecha
	var date = fecha.split('/');

	var dia = get_val2(date[0]), mes = get_val2(date[1]), anio = get_val2(date[2]);

	if (dia == 10 || dia == 20 || dia == 30 || (mes == 2 && dia == 28))
		if (confirm('¿Tiene la nomina en mano (se guardara registro de autorización)?')) {
			var myConn = new XHConn();

			if (!myConn)
				alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

			// Pedir datos
			myConn.connect("./pan_rev_dat.php", "GET", 'num_cia=' + num_cia + '&fecha=' + fecha + '&nom=1', autNom);
		}
		else
			document.getElementById('next').disabled = true;
}

var autNom = function (oXML) {
	var result = oXML.responseText;

	if (result.length > 0) alert(result);
}

function prestamos(num_cia, fecha) {
	var myConn = new XHConn();

	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

	// Pedir datos
	myConn.connect("./pan_rev_dat.php", "GET", 'num_cia=' + num_cia + '&fecha=' + fecha + '&pres=1', autPres);
}

var autPres = function (oXML) {
	var result = oXML.responseText;

	if (get_val2(result) != 0) {
		alert('El total de prestamos en el sistema y el de la hoja no coinciden');
		document.getElementById('next').disabled = true;
	}
}

//window.onload = function () { nomina({num_cia}, '{fecha}');/* prestamos({num_cia}, '{fecha}');*/ };
//-->
</script>
<!-- END BLOCK : hoja -->
<!-- START BLOCK : pastillaje_compras -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Facturas de compra de pastillaje</p>
			<form method="post" name="datos" id="datos" class="FormValidator">
				<input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
				<input name="fecha" type="hidden" id="fecha" value="{fecha}">
				<input name="nom" type="hidden" id="nom" value="{nom}">
				<table class="tabla">
					<tr>
						<th class="tabla">Compa&ntilde;&iacute;a</th>
						<th class="tabla">Fecha</th>
					</tr>
					<tr>
						<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
						<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
					</tr>
				</table>
				<br>
				<table class="tabla">
					<thead>
						<tr>
							<th class="tabla">Proveedor</th>
							<th class="tabla">Factura</th>
							<th class="tabla">Importe</th>
						</tr>
					</thead>
					<tbody id="facturas">
						<!-- START BLOCK : row_fac_pastillaje -->
						<tr>
							<td class="tabla"><input name="proveedor[]" type="text" class="valid toText toUpper cleanText vinsert" id="proveedor" value="{proveedor}" size="30" maxlength="200"></td>
							<td class="tabla"><input name="factura[]" type="text" class="valid onlyNumbersAndLetters toUpper rinsert" id="factura" value="{factura}" size="10" maxlength="50"></td>
							<td class="tabla"><input name="importe[]" type="text" class="valid Focus numberPosFormat rinsert" precision="2" id="importe" value="{importe}" size="10"></td>
						</tr>
						<!-- END BLOCK : row_fac_pastillaje -->
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2" class="rtabla">Total facturas</th>
							<th class="rtabla" id="total_facs">{total_facs}</th>
						</tr>
						<tr>
							<th colspan="2" class="rtabla">Total compras</th>
							<th class="rtabla"><input name="total_compras" type="hidden" id="total_compras" value="{total_compras}">
							{total_compras}</th>
						</tr>
					</tfoot>
				</table>
				<p>
					<input name="regresar" type="button" class="boton" id="regresar" value="&lt;&lt; Regresar">
					&nbsp;&nbsp;
					<input type="button" class="boton" value="Inicio" onClick="document.location='pan_rev_dat.php'">
				&nbsp;&nbsp;
				<input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente &gt;&gt;">
				</p>
			</form></td>
	</tr>
</table>
<script language="javascript" type="text/javascript">
window.addEvent('domready', function() {
	validator = new FormValidator($('datos'));

	$$('input[id=proveedor]').each(function(el, i) {
		el.addEvents({
			keydown: function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();

					$$('input[id=factura]')[i].select();
				} else if (e.key == 'up') {
					e.stop();

					if (i > 0) {
						$$('input[id=proveedor]')[i - 1].focus();
					} else {
						$$('input[id=proveedor]')[$$('input[id=proveedor]').length - 1].focus();
					}
				} else if (e.key == 'down') {
					e.stop();

					if (i < $$('input[id=proveedor]').length - 1) {
						$$('input[id=proveedor]')[i + 1].focus();
					} else {
						$$('input[id=proveedor]')[0].focus();
					}
				}
			}
		});
	});

	$$('input[id=factura]').each(function(el, i) {
		el.addEvents({
			keydown: function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();

					$$('input[id=importe]')[i].select();
				} else if (e.key == 'left') {
					e.stop();

					$$('input[id=proveedor]')[i].select();
				} else if (e.key == 'up') {
					e.stop();

					if (i > 0) {
						$$('input[id=factura]')[i - 1].focus();
					} else {
						$$('input[id=factura]')[$$('input[id=factura]').length - 1].focus();
					}
				} else if (e.key == 'down') {
					e.stop();

					if (i < $$('input[id=factura]').length - 1) {
						$$('input[id=factura]')[i + 1].focus();
					} else {
						$$('input[id=factura]')[0].focus();
					}
				}
			}
		});
	});

	$$('input[id=importe]').each(function(el, i) {
		el.addEvents({
			change: calcular_total,
			keydown: function(e) {
				if (e.key == 'enter') {
					e.stop();

					if (i + 1 > $$('input[id=proveedor]').length - 1) {
						new_row(i + 1);
					}

					$$('input[id=proveedor]')[i + 1].select();
				} else if (e.key == 'left') {
					e.stop();

					$$('input[id=factura]')[i].select();
				} else if (e.key == 'up') {
					e.stop();

					if (i > 0) {
						$$('input[id=importe]')[i - 1].focus();
					} else {
						$$('input[id=importe]')[$$('input[id=importe]').length - 1].focus();
					}
				} else if (e.key == 'down') {
					e.stop();

					if (i < $$('input[id=importe]').length - 1) {
						$$('input[id=importe]')[i + 1].focus();
					} else {
						$$('input[id=importe]')[0].focus();
					}
				}
			}
		});
	});

	$('regresar').addEvent('click', validar.pass('l'));
	$('siguiente').addEvent('click', validar.pass('r'));

	$('proveedor').focus();
});

var new_row = function(i) {
	var table = $('facturas'),
		tr = new Element('tr').inject(table),
		td1 = new Element('td', {
			class: 'tabla'
		}).inject(tr),
		td2 = new Element('td', {
			class: 'tabla'
		}).inject(tr),
		td3 = new Element('td', {
			class: 'tabla'
		}).inject(tr),
		proveedor = new Element('input', {
			id: 'proveedor',
			name: 'proveedor[]',
			type: 'text',
			class: 'valid toText toUpper cleanText vinsert',
			size: 30,
			maxlength: 200
		}).addEvents({
			keydown: function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();

					$$('input[id=factura]')[i].select();
				} else if (e.key == 'up') {
					e.stop();

					if (i > 0) {
						$$('input[id=proveedor]')[i - 1].focus();
					} else {
						$$('input[id=proveedor]')[$$('input[id=proveedor]').length - 1].focus();
					}
				} else if (e.key == 'down') {
					e.stop();

					if (i < $$('input[id=proveedor]').length - 1) {
						$$('input[id=proveedor]')[i + 1].focus();
					} else {
						$$('input[id=proveedor]')[0].focus();
					}
				}
			}
		}).inject(td1),
		factura = new Element('input', {
			id: 'factura',
			name: 'factura[]',
			type: 'text',
			class: 'valid onlyNumbersAndLetters toUpper rinsert',
			size: 10
		}).addEvents({
			keydown: function(e) {
				if (e.key == 'enter' || e.key == 'right') {
					e.stop();

					$$('input[id=importe]')[i].select();
				} else if (e.key == 'left') {
					e.stop();

					$$('input[id=proveedor]')[i].select();
				} else if (e.key == 'up') {
					e.stop();

					if (i > 0) {
						$$('input[id=factura]')[i - 1].focus();
					} else {
						$$('input[id=factura]')[$$('input[id=factura]').length - 1].focus();
					}
				} else if (e.key == 'down') {
					e.stop();

					if (i < $$('input[id=factura]').length - 1) {
						$$('input[id=factura]')[i + 1].focus();
					} else {
						$$('input[id=factura]')[0].focus();
					}
				}
			}
		}).inject(td2),
		importe = new Element('input', {
			id: 'importe',
			name: 'importe[]',
			type: 'text',
			class: 'valid Focus numberPosFormat rinsert',
			precision: 2,
			size: 10
		}).addEvents({
			change: calcular_total,
			keydown: function(e) {
				if (e.key == 'enter') {
					e.stop();

					if (i + 1 > $$('input[id=proveedor]').length - 1) {
						new_row(i + 1);
					}

					$$('input[id=proveedor]')[i + 1].select();
				} else if (e.key == 'left') {
					e.stop();

					$$('input[id=factura]')[i].select();
				} else if (e.key == 'up') {
					e.stop();

					if (i > 0) {
						$$('input[id=importe]')[i - 1].focus();
					} else {
						$$('input[id=importe]')[$$('input[id=importe]').length - 1].focus();
					}
				} else if (e.key == 'down') {
					e.stop();

					if (i < $$('input[id=importe]').length - 1) {
						$$('input[id=importe]')[i + 1].focus();
					} else {
						$$('input[id=importe]')[0].focus();
					}
				}
			}
		}).inject(td3);

	validator.addElementEvents(proveedor);
	validator.addElementEvents(factura);
	validator.addElementEvents(importe);
}

var calcular_total = function() {
	$('total_facs').set('html', $$('input[id=importe]').get('value').getNumericValue().sum().numberFormat(2, '.', ','));
}

var validar = function(dir) {
	if ($$('input[id=importe]').filter(function(el, i) {
		return $$('input[id=proveedor]')[i].get('value') != '' && $$('input[id=factura]')[i].get('value') != '' && el.get('value').getNumericValue() > 0
	}).get('value').getNumericValue().sum() > ($('total_compras').get('value').getNumericValue() * 0.90).round(2)) {
		alert('El total de las facturas debe ser la mitad del total de compras de pastillaje (' + ($('total_compras').get('value').getNumericValue() * 0.90).numberFormat(2, '.', ',') + ').\n\nRecuerde que para la comparativa solo se toman los registros con proveedor, factura e importe que no esten vacíos');
	} else {
		$('datos').set('action', 'pan_rev_dat.php?action=compra_pastillaje_guardar&num_cia=' + $('num_cia').get('value') + '&fecha=' + $('fecha').get('value') + '&dir=' + dir + '&nom=' + $('nom').get('value')).submit();
	}
}
</script>
<!-- END BLOCK : pastillaje_compras -->
<!-- START BLOCK : pro_new -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Productos y precios que no estan en el control</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<table class="tabla">
				<!-- START BLOCK : turno_new -->
				<tr>
					<th colspan="3" class="vtabla" style="font-size:14pt; ">{turno}</th>
				</tr>
				<tr>
					<th class="tabla">Producto</th>
					<th class="tabla">Precio<br>
						Raya</th>
					<th class="tabla">Precio<br>
						Venta</th>
				</tr>
				<!-- START BLOCK : producto_new -->
				<tr>
					<td class="vtabla" style="font-weight:bold; font-size:12pt;">{cod} {nombre}</td>
					<td class="rtabla" style="font-weight:bold; font-size:12pt; color:#CC0000;">{precio_raya}</td>
					<td class="rtabla" style="font-weight:bold; font-size:12pt; color:#0000CC;">{precio_venta}</td>
				</tr>
				<!-- END BLOCK : producto_new -->
				<tr>
					<td colspan="3" class="tabla">&nbsp;</td>
				</tr>
				<!-- END BLOCK : turno_new -->
			</table>
			<p>
				<input type="button" class="boton" value="Modificar" onClick="document.location='./pan_rev_dat.php?action=pro_mod&num_cia={num_cia}&fecha={fecha}&nom={nom}'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
			</p></td>
	</tr>
</table>
<!-- END BLOCK : pro_new -->
<!-- START BLOCK : pro -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Producci&oacute;n</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<table class="tabla">
				<!-- START BLOCK : turno -->
				<tr>
					<th colspan="6" class="vtabla" style="font-size:14pt; ">{turno}</th>
				</tr>
				<tr>
					<th class="tabla">Producto</th>
					<th class="tabla">Piezas</th>
					<th class="tabla">Precio<br>
						Raya</th>
					<th class="tabla">Importe<br>
						Raya</th>
					<th class="tabla">Precio<br>
						Venta</th>
					<th class="tabla">Importe<br>
						Producci&oacute;n</th>
				</tr>
				<!-- START BLOCK : producto -->
				<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
					<td class="vtabla" style="font-weight:bold; font-size:12pt;">{cod_producto} {nombre} </td>
					<td class="rtabla" style="font-weight:bold; font-size:12pt; color:#006600;">{piezas}</td>
					<td class="rtabla" style="font-weight:bold; font-size:12pt; color:#CC0000;">{precio_raya}</td>
					<td class="rtabla" style="font-weight:bold; font-size:12pt; color:#CC0000;">{imp_raya}</td>
					<td class="rtabla" style="font-weight:bold; font-size:12pt; color:#0000CC;">{precio_venta}</td>
					<td class="rtabla" style="font-weight:bold; font-size:12pt; color:#0000CC;">{imp_produccion}</td>
				</tr>
				<!-- END BLOCK : producto -->
				<tr>
					<th colspan="3" class="rtabla">Totales</th>
					<th class="rtabla" style="font-size:12pt;">{raya_ganada}</th>
					<th class="tabla">&nbsp;</th>
					<th class="rtabla" style="font-size:12pt;">{total_produccion}</th>
				</tr>
				<tr>
					<th colspan="3" class="rtabla">Raya Pagada </th>
					<th class="rtabla" style="font-size:12pt;">{raya_pagada}</th>
					<th colspan="2" class="tabla">&nbsp;</th>
				</tr>
				<tr>
					<td colspan="6" class="tabla">&nbsp;</td>
				</tr>
				<!-- END BLOCK : turno -->
			</table>
			<br>
			<table class="tabla">
				<tr>
					<th colspan="2" class="tabla">Raya Ganada </th>
					<th colspan="2" class="tabla">Raya Pagada </th>
					<th colspan="2" class="tabla">Produccion Total </th>
				</tr>
				<tr>
					<th colspan="2" class="tabla" style="font-size:14pt;">{raya_ganada}</th>
					<th colspan="2" class="tabla" style="font-size:14pt;">{raya_pagada}</th>
					<th colspan="2" class="tabla" style="font-size:14pt;">{produccion_total}</th>
				</tr>
			</table>
			<p>
				<input type="button" class="boton" value="<< Regresar" onClick="document.location='./pan_rev_dat.php?action=hoja&num_cia={num_cia}&fecha={fecha}&dir=l&nom={nom}'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Modificar" onClick="document.location='./pan_rev_dat.php?action=pro_mod&num_cia={num_cia}&fecha={fecha}&nom={nom}'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./pan_rev_dat.php?action=pro_muestras&num_cia={num_cia}&fecha={fecha}&dir=r&nom={nom}'">
			</p></td>
	</tr>
</table>
<!-- END BLOCK : pro -->
<!-- START BLOCK : mod_pro -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Modificar Producci&oacute;n </p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<form action="./pan_rev_dat.php?action=mod_pro&num_cia={num_cia}&fecha={fecha}&dir=r&nom={nom}" method="post" name="form">
				<input name="tmp" type="hidden" id="tmp">
				<table class="tabla">
					<!-- START BLOCK : turno_mod -->
					<tr>
						<th colspan="6" class="vtabla" style="font-size:14pt; ">{turno}
							<input name="codturno[]" type="hidden" id="codturno" value="{cod_turno}">
							<input name="idtot[]" type="hidden" id="idtot" value="{idtot}"></th>
					</tr>
					<tr>
						<th class="tabla">Producto</th>
						<th class="tabla">Piezas</th>
						<th class="tabla">Precio<br>
							Raya</th>
						<th class="tabla">Importe<br>
							Raya</th>
						<th class="tabla">Precio<br>
							Venta</th>
						<th class="tabla">Importe<br>
							Producci&oacute;n</th>
					</tr>
					<!-- START BLOCK : producto_mod -->
					<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
						<td class="vtabla" style="font-weight:bold; font-size:12pt;"><input name="idpro[]" type="hidden" id="idpro" value="{idpro}">
							<input name="cod_turno" type="hidden" id="cod_turno" value="{cod_turno}">
							{cod_producto} {nombre} </td>
						<td class="tabla"><input name="piezas[]" type="text" class="rinsert" id="piezas" style="font-weight:bold; font-size:12pt; color:#006600;" onChange="if(inputFormat(this,-1,true))calculaImporte({i})" onKeyDown="movCursor(event.keyCode,piezas{next},null,precio_raya{index},piezas{back},piezas{next})" value="{piezas}" size="8"></td>
						<td class="tabla"><input name="precio_raya[]" type="text" class="rinsert" id="precio_raya" style="font-weight:bold; font-size:12pt; color:#CC0000;" onFocus="tmp.value=this.value;this.select()" onChange="if(validarPrecioRaya(this,tmp))calculaImporte({i})" onKeyDown="movCursor(event.keyCode,piezas{next},piezas{index},precio_venta{index},precio_raya{back},precio_raya{next})" value="{precio_raya}" size="8"></td>
						<td class="tabla"><input name="imp_raya[]" type="text" class="rnombre" id="imp_raya" style="font-weight:bold; font-size:12pt; color:#CC0000;" value="{imp_raya}" size="10" readonly="true"></td>
						<td class="tabla"><input name="precio_venta[]" type="text" class="rinsert" id="precio_venta" style="font-weight:bold; font-size:12pt; color:#0000CC;" onFocus="tmp.value=this.value;this.select()" onChange="if(validarPrecioVenta(this,tmp))calculaImporte({i})" onKeyDown="movCursor(event.keyCode,piezas{next},precio_raya{index},null,precio_venta{back},precio_venta{next})" value="{precio_venta}" size="8"></td>
						<td class="tabla"><input name="imp_produccion[]" type="text" class="rnombre" id="imp_produccion" style="font-weight:bold; font-size:12pt; color:#0000CC;" value="{imp_produccion}" size="10" readonly="true"></td>
					</tr>
					<!-- END BLOCK : producto_mod -->
					<tr>
						<th colspan="3" class="rtabla">Totales</th>
						<th class="tabla"><input name="total_imp_raya{cod_turno}" type="text" class="rnombre" id="total_imp_raya{cod_turno}" style="font-size:12pt;" value="{total_imp_raya}" size="10" readonly="true"></th>
						<th class="tabla">&nbsp;</th>
						<th class="tabla"><input name="total_imp_produccion{cod_turno}" type="text" class="rnombre" id="total_imp_produccion{cod_turno}" style="font-size:12pt;" value="{total_imp_produccion}" size="10" readonly="true"></th>
					</tr>
					<tr>
						<th colspan="3" class="rtabla">Raya Pagada </th>
						<th class="rtabla"><input name="total_raya_pagada{cod_turno}" type="text" class="rinsert" id="total_raya_pagada{cod_turno}" style="font-size:12pt;font-weight:bold;" onFocus="tmp.value=this.value;this.select()" onChange="if(inputFormat(this,2,true))calculaGranTotal()" onKeyDown="movCursor(event.keyCode,piezas{next},null,null,piezas{back},piezas{next})" value="{total_raya_pagada}" size="10"></th>
						<th colspan="2" class="tabla">&nbsp;</th>
					</tr>
					<tr>
						<td colspan="6" class="tabla">&nbsp;</td>
					</tr>
					<!-- END BLOCK : turno_mod -->
				</table>
				<br>
				<table class="tabla">
					<tr>
						<th colspan="2" class="tabla">Raya Ganada </th>
						<th colspan="2" class="tabla">Raya Pagada </th>
						<th colspan="2" class="tabla">Produccion Total </th>
					</tr>
					<tr>
						<th colspan="2" class="tabla"><input name="raya_ganada" type="text" disabled class="nombre" id="raya_ganada" style="font-size:14pt;" value="{raya_ganada}" size="12"></th>
						<th colspan="2" class="tabla"><input name="raya_pagada" type="text" disabled class="nombre" id="raya_pagada" style="font-size:14pt;" value="{raya_pagada}" size="12"></th>
						<th colspan="2" class="tabla"><input name="produccion_total" type="text" disabled class="nombre" id="produccion_total" style="font-size:14pt;" value="{produccion_total}" size="12"></th>
					</tr>
				</table>
				<p>
					<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
					&nbsp;&nbsp;
					<input type="button" class="boton" onClick="validar()" value="Modificar">
				</p>
			</form></td>
	</tr>
</table>
<script language="javascript" type="application/javascript">
var f = document.form;

function validarPrecioRaya(el, tmp) {
	if (el.value == '' || el.value == '0') {
		el.value = '';
		return true;
	}

	var val = 0, por = false;

	if (el.value.indexOf('%') >= 0) {
		val = get_val2(el.value.replace('%', ''));
		por = true;
	}
	else
		val = get_val(el);

	if (val < 0) {
		alert('El precio o porcentaje de raya no puede ser menor a 0');
		el.value = tmp.value;
		el.select();
		return false;
	}

	if (por == true && val > 100) {
		alert('El porcentaje de raya no puede ser mayor al 100%');
		el.value = tmp.value;
		el.select();
		return false;
	}

	el.value = por ? '%' + numberFormat(val, 2) : numberFormat(val, 4);

	return true;
}

function validarPrecioVenta(el, tmp) {
	if (el.value == '' || el.value == '0') {
		el.value = '';
		return true;
	}

	var val = get_val(el);

	if (val < 0) {
		alert('El precio de venta no puede ser menor a 0');
		el.value = tmp.value;
		el.select();
		return false;
	}

	el.value = numberFormat(val, 3);

	return true;
}

function calculaImporte(i) {
	var piezas = 0, precio_raya = 0, precio_venta = 0, raya = 0, pro = 0,  imp_raya, imp_produccion, turno, por = false;

	if (f.piezas.length == undefined) {
		piezas = get_val(f.piezas);
		precio_venta = get_val(f.precio_venta);
		precio_raya = f.precio_raya.value.indexOf('%') >= 0 ? get_val2(f.precio_raya.value.replace('%', '')) : get_val(f.precio_raya);
		por = f.precio_raya.value.indexOf('%') >= 0 ? true : false;

		imp_raya = f.imp_raya;
		imp_produccion = f.imp_produccion;
	}
	else {
		piezas = get_val(f.piezas[i]);
		precio_venta = get_val(f.precio_venta[i]);
		precio_raya = f.precio_raya[i].value.indexOf('%') >= 0 ? get_val2(f.precio_raya[i].value.replace('%', '')) / 100 : get_val(f.precio_raya[i]);
		por = f.precio_raya[i].value.indexOf('%') >= 0 ? true : false;

		imp_raya = f.imp_raya[i];
		imp_produccion = f.imp_produccion[i];
	}

	if (piezas == 0) {
		imp_raya.value = '';
		imp_produccion.value = '';
	}
	else {
		produccion = piezas * precio_venta;

		if (por) {
			raya = produccion * precio_raya;
		}
		else {
			raya = piezas * precio_raya;
		}

		imp_raya.value = raya > 0 ? numberFormat(raya, 2) : '';
		imp_produccion.value = produccion > 0 ? numberFormat(produccion, 2) : '';
	}

	calculaTotalTurno(get_val(f.cod_turno.length == undefined ? f.cod_turno : f.cod_turno[i]));
}

function calculaTotalTurno(turno) {
	var raya_ganada = 0, imp_pro = 0;

	if (f.cod_turno.length == undefined) {
		raya_ganada += get_val(f.imp_raya);
		imp_pro += get_val(f.imp_produccion);
	}
	else {
		for (var i = 0; i < f.cod_turno.length; i++) {
			if (get_val(f.cod_turno[i]) == turno) {
				raya_ganada += get_val(f.imp_raya[i]);
				imp_pro += get_val(f.imp_produccion[i]);
			}
		}
	}

	document.getElementById('total_imp_raya' + turno).value = raya_ganada > 0 ? numberFormat(raya_ganada, 2) : '';
	document.getElementById('total_raya_pagada' + turno).value = raya_ganada > 0 ? numberFormat(raya_ganada, 2) : '';
	document.getElementById('total_imp_produccion' + turno).value = imp_pro > 0 ? numberFormat(imp_pro, 2) : '';

	calculaGranTotal();
}

function calculaGranTotal() {
	var raya_ganada = 0, raya_pagada = 0, produccion_total = 0;

	if (f.codturno.length == undefined) {
		raya_ganada += get_val(document.getElementById('total_imp_raya' + f.codturno.value));
		raya_pagada += get_val(document.getElementById('total_raya_pagada' + f.codturno.value));
		produccion_total += get_val(document.getElementById('total_imp_produccion' + f.codturno.value));
	}
	else {
		for (var i = 0; i < f.codturno.length; i++) {
			raya_ganada += get_val(document.getElementById('total_imp_raya' + f.codturno[i].value));
			raya_pagada += get_val(document.getElementById('total_raya_pagada' + f.codturno[i].value));
			produccion_total += get_val(document.getElementById('total_imp_produccion' + f.codturno[i].value));
		}
	}

	f.raya_ganada.value = raya_ganada > 0 ? numberFormat(raya_ganada, 2) : '';
	f.raya_pagada.value = raya_pagada > 0 ? numberFormat(raya_pagada, 2) : '';
	f.produccion_total.value = produccion_total > 0 ? numberFormat(produccion_total, 2) : '';
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
}

window.onload = f.piezas.length == undefined ? f.piezas.select : f.piezas[0].select();
</script>
<!-- END BLOCK : mod_pro -->
<!-- START BLOCK : pro_muestras -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Muestras</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<table class="tabla">
				<tr>
					<th class="tabla">Producto</th>
					<th class="tabla">Turno</th>
					<th class="tabla">Piezas</th>
					<th class="tabla">Precio de Raya </th>
					<th class="tabla">Raya Ganada </th>
				</tr>
				<!-- START BLOCK : muestras -->
				<tr>
					<td class="vtabla">{cod} {producto} </td>
					<td class="vtabla">{turno}</td>
					<td class="rtabla">{piezas}</td>
					<td class="rtabla">{precio_raya}</td>
					<td class="rtabla">{imp_raya}</td>
				</tr>
				<!-- END BLOCK : muestras -->
			</table>
			<p>
				<input type="button" class="boton" value="<< Regresar" onClick="document.location='./pan_rev_dat.php?action=pro&num_cia={num_cia}&fecha={fecha}&dir=l&nom={nom}'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./pan_rev_dat.php?action=exp&num_cia={num_cia}&fecha={fecha}&dir=r&nom={nom}'">
			</p></td>
	</tr>
</table>
<!-- END BLOCK : pro_muestras -->
<!-- START BLOCK : cambio_exp -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Expendios Modificados</p>
			<form action="./pan_rev_dat.php?action=mod_exp&num_cia={num_cia}&fecha={fecha}&nom={nom}" method="post" name="form">
				<table class="tabla">
					<tr>
						<th class="tabla">Compa&ntilde;&iacute;a</th>
						<th class="tabla">Fecha</th>
					</tr>
					<tr>
						<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
						<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
					</tr>
				</table>
				<br>
				<!-- START BLOCK : new_exps -->
				<table class="tabla">
					<tr>
						<th colspan="3" class="tabla">Expendios nuevos </th>
					</tr>
					<tr>
						<th class="tabla">No</th>
						<th class="tabla">Nombre</th>
						<th class="tabla">Porcentaje</th>
					</tr>
					<!-- START BLOCK : new_exp -->
					<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
						<td class="vtabla">{num}</td>
						<td class="vtabla"><input name="num_new[]" type="hidden" id="num_new" value="{num}">
							<input name="nombre_new[]" type="hidden" id="nombre_new" value="{nombre}">
							{nombre}</td>
						<td class="tabla"><input name="por_new[]" type="hidden" id="por_new" value="{por}">
							{por}%</td>
					</tr>
					<!-- END BLOCK : new_exp -->
				</table>
				<br>
				<!-- END BLOCK : new_exps -->
				<!-- START BLOCK : mod_exps -->
				<table class="tabla">
					<tr>
						<th colspan="3" class="tabla">Expendios que cambiaron de nombre </th>
					</tr>
					<tr>
						<th class="tabla">No</th>
						<th class="tabla">Nombre Panaderia </th>
						<th class="tabla">Nombre Cat&aacute;logo </th>
					</tr>
					<!-- START BLOCK : mod_exp -->
					<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
						<td class="tabla"><input name="num_mod[]" type="hidden" id="num_mod" value="{num}">
							{num}</td>
						<td class="vtabla"><input name="nombre_mod[]" type="hidden" id="nombre_mod" value="{nombre_tmp}">
							{nombre_tmp}</td>
						<td class="vtabla">{nombre_cat}</td>
					</tr>
					<!-- END BLOCK : mod_exp -->
				</table>
				<br>
				<!-- END BLOCK : mod_exps -->
				<!-- START BLOCK : por_exps -->
				<table class="tabla">
					<tr>
						<th colspan="4" class="tabla">Porcentajes de ganancia que cambiaron <br>
							(al validar se impondran los porcentajes<br>
							de oficinas menores al de panaderias)</th>
					</tr>
					<tr>
						<th class="tabla">No</th>
						<th class="tabla">Nombre</th>
						<th class="tabla">Porcentaje <br>
							Anterior </th>
						<th class="tabla">Porcentaje <br>
							Nuevo </th>
					</tr>
					<!-- START BLOCK : por_exp -->
					<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
						<td class="tabla"><input name="num_por[]" type="hidden" id="num_por" value="{num}">
							{num}</td>
						<td class="vtabla">{nombre}</td>
						<td class="tabla"><input name="por_cat[]" type="hidden" id="por_cat" value="{por_cat}">
							{por_cat}%</td>
						<td class="tabla"><input name="por_tmp[]" type="hidden" id="por" value="{por_tmp}">
							{por_tmp}%</td>
					</tr>
					<!-- END BLOCK : por_exp -->
				</table>
				<!-- END BLOCK : por_exps -->
				<p>
					<input type="button" class="boton" value="Cancelar" onClick="document.location='./pan_rev_dat.php'">
					&nbsp;&nbsp;
					<input type="button" class="boton" value="Validar" onClick="validar()">
				</p>
			</form></td>
	</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (confirm('¿Son correctos todos los datos?'))
		f.submit();
}
//-->
</script>
<!-- END BLOCK : cambio_exp -->
<!-- START BLOCK : exp -->
<style type="text/css">
a {
	text-decoration:none; color:#000000; cursor:default;
}
</style>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Movimiento de Expendios </p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<table class="tabla">
				<tr>
					<th colspan="2" class="tabla">Expendio</th>
					<th class="tabla">Rezago<br>
						Anterior</th>
					<th class="tabla">Partidas</th>
					<th class="tabla">Devuelto</th>
					<th class="tabla">%</th>
					<th class="tabla">Total</th>
					<th class="tabla">Abono</th>
					<th class="tabla">Nuevo<br>
						Rezago</th>
				</tr>
				<!-- START BLOCK : mov_exp -->
				<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
					<td class="rtabla" style="font-weight:bold; font-size:12pt;">{num}</td>
					<td class="vtabla" style="font-weight:bold; font-size:12pt;{color}">{nombre}</td>
					<td class="rtabla" style="font-weight:bold; font-size:12pt;"{color_rezago_ant}><a href="javascript:void(null)" title="{mensaje_rezago_ant}" style="color:#006600;">{rezago_ant}</a></td>
					<td class="rtabla" style="color:#0000CC; font-size:12pt;">{pan_p_venta}</td>
					<td class="rtabla"{color_dev}><a href="javascript:void(null)" title="{mensaje_dev}" style="color:#CC0000; font-size:12pt;">{dev}</a></td>
					<td class="rtabla" style=" font-size:12pt;">{por}</td>
					<td class="rtabla"{color_pan_exp}><a href="javascript:void(null)" title="{mensaje_pan_exp}" style="color:#6600CC; font-size:12pt;">{pan_p_exp}</a></td>
					<td class="rtabla"{color_abono}><a href="javascript:void(null)" title="{mensaje_abono}" style="color:#990033; font-size:12pt;">{abono}</a></td>
					<td class="rtabla" style="font-weight:bold;"{color_rezago}><a href="javascript:void(null)" title="{mensaje_rezago}" style="color:#006600; font-size:12pt;">{rezago}</a></td>
				</tr>
				<!-- END BLOCK : mov_exp -->
				<tr>
					<th colspan="2" class="rtabla">Total</th>
					<th class="rtabla" style="font-size:12pt;">{rezago_ant}</th>
					<th class="rtabla" style="font-size:12pt;">{pan_p_venta}</th>
					<th class="rtabla" style="font-size:12pt;">{dev}</th>
					<th class="rtabla" style="font-size:12pt;">{por}</th>
					<th class="rtabla" style="font-size:12pt;">{pan_p_exp}</th>
					<th class="rtabla" style="font-size:12pt;">{abono}</th>
					<th class="rtabla" style="font-size:12pt;">{rezago}</th>
				</tr>
			</table>
			<p>
				<input type="button" class="boton" value="<< Regresar" onClick="document.location='./pan_rev_dat.php?action=pro&num_cia={num_cia}&fecha={fecha}&dir=l&nom={nom}'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
				&nbsp;&nbsp;
				<!-- <input type="button" class="boton" value="Siguiente >>" onClick="document.location='./pan_rev_dat.php?action=avio&num_cia={num_cia}&fecha={fecha}&dir=r&nom={nom}'"{disabled}> -->
				<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./pan_rev_dat.php?action=pasteles&num_cia={num_cia}&fecha={fecha}&dir=r&nom={nom}'"{disabled}>
			</p></td>
	</tr>
</table>
<!-- END BLOCK : exp -->
<!-- START BLOCK : avio_error -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Movimientos de Avio</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>

			<!-- START BLOCK : avg -->

			<p style="font-family:Arial, Helvetica, sans-serif;">Los siguientes productos por turno sobrepasan el promedio de consumo</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Producto</th>
					<th class="tabla">Turno</th>
					<th class="tabla">Promedio</th>
					<th class="tabla">Consumo</th>
					<th class="tabla">% Diferencia </th>
				</tr>
				<!-- START BLOCK : avg_row -->
				<tr>
					<td class="vtabla">{cod} {nombre} </td>
					<td class="vtabla">{turno}</td>
					<td class="rtabla">{promedio}</td>
					<td class="rtabla">{consumo}</td>
					<td class="rtabla" {color}>{dif}</td>
				</tr>
				<!-- END BLOCK : avg_row -->
			</table>

			<!-- END BLOCK : avg -->
			<!-- START BLOCK : no_avg -->

			<p style="font-family:Arial, Helvetica, sans-serif;">Los siguientes productos no tienen promedio de consumo</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Producto</th>
					<th class="tabla">Turno</th>
					<th class="tabla">Consumo</th>
				</tr>
				<!-- START BLOCK : no_avg_row -->
				<tr>
					<td class="vtabla">{cod} {nombre} </td>
					<td class="vtabla">{turno}</td>
					<td class="rtabla">{consumo}</td>
				</tr>
				<!-- END BLOCK : no_avg_row -->
			</table>

			<!-- END BLOCK : no_avg -->

			<!-- START BLOCK : over_avg -->

			<p style="font-family:Arial, Helvetica, sans-serif;">Los siguientes productos tienen inventario para mas de 25 d&iacute;as</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Producto</th>
					<th class="tabla">Consumo de<br />25 d&iacute;as</th>
					<th class="tabla">Inventario</th>
				</tr>
				<!-- START BLOCK : over_avg_row -->
				<tr>
					<td class="vtabla">{cod} {nombre} </td>
					<td class="rtabla">{consumo}</td>
					<td class="rtabla">{existencia}</td>
				</tr>
				<!-- END BLOCK : over_avg_row -->
			</table>

			<!-- END BLOCK : over_avg -->

			<p>
				<input type="button" class="boton" value="<< Regresar" onClick="document.location='./pan_rev_dat.php?action=exp&num_cia={num_cia}&fecha={fecha}&dir=l&nom={nom}'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./pan_rev_dat.php?action=avio&num_cia={num_cia}&fecha={fecha}&dir=r&nom={nom}&avg=1&{ids}'">
			</p></td>
	</tr>
</table>
<!-- END BLOCK : avio_error -->
<!-- START BLOCK : avio -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Movimientos de Avio</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<table width="100%" class="tabla">
				<tr>
					<th width="28%" class="tabla">Producto</th>
					<th width="6%" class="tabla">Existencia<br>
						Anterior</th>
					<th width="6%" class="tabla">Entrada</th>
					<th width="6%" class="tabla">Total</th>
					<th width="6%" class="tabla">FD</th>
					<th width="6%" class="tabla">FN</th>
					<th width="6%" class="tabla">BD</th>
					<th width="6%" class="tabla">REP</th>
					<th width="6%" class="tabla">PIC</th>
					<th width="6%" class="tabla">GEL</th>
					<th width="6%" class="tabla">DES</th>
					<th width="6%" class="tabla">Consumo<br>
						Total</th>
					<th width="6%" class="tabla">Para<br>
						Ma&ntilde;ana</th>
				</tr>
				<!-- START BLOCK : avi_row -->
				<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
					<td class="vtabla" style="font-weight:bold;{color}">&nbsp;{codmp} {nombre}&nbsp;</td>
					<td class="rtabla" style="color:#{color_ini}; font-weight:bold;">&nbsp;{ext_ini}&nbsp;</td>
					<td class="rtabla" style="color:#0000CC; font-weight:bold;">&nbsp;{entrada}&nbsp;</td>
					<td class="rtabla" style="color:#{color_total}; font-weight:bold;">&nbsp;{total}&nbsp;</td>
					<td class="rtabla" style="color:#990000; font-weight:bold;{bgcolor1}">&nbsp;{1}&nbsp;</td>
					<td class="rtabla" style="color:#990033; font-weight:bold;{bgcolor2}">&nbsp;{2}&nbsp;</td>
					<td class="rtabla" style="color:#990066; font-weight:bold;{bgcolor3}">&nbsp;{3}&nbsp;</td>
					<td class="rtabla" style="color:#990099; font-weight:bold;{bgcolor4}">&nbsp;{4}&nbsp;</td>
					<td class="rtabla" style="color:#9900CC; font-weight:bold;{bgcolor8}">&nbsp;{8}&nbsp;</td>
					<td class="rtabla" style="color:#9900FF; font-weight:bold;{bgcolor9}">&nbsp;{9}&nbsp;</td>
					<td class="rtabla" style="color:#9966FF; font-weight:bold;{bgcolor10}">&nbsp;{10}&nbsp;</td>
					<td class="rtabla" style="color:#CC0000; font-weight:bold;">&nbsp;{consumo}&nbsp;</td>
					<td class="rtabla" style="color:#{color_fin}; font-weight:bold;">&nbsp;{ext_fin}&nbsp;</td>
				</tr>
				<!-- END BLOCK : avi_row -->
			</table>

			<!-- START BLOCK : no_control_avio -->

			<p style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; color:#C00;">Los siguientes productos no estan en el control de av&iacute;o</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Cod.</th>
					<th class="tabla">Producto</th>
					<th class="tabla">Turno</th>
				</tr>
				<!-- START BLOCK : no_control_avio_row -->
				<tr>
					<td class="vtabla">{codmp}</td>
					<td class="vtabla">{nombre}</td>
					<td class="vtabla">{turno}</td>
				</tr>
				<!-- END BLOCK : no_control_avio_row -->
			</table>

			<!-- END BLOCK : no_control_avio -->

			<p>
				<input type="button" class="boton" value="<< Regresar" onClick="document.location='./pan_rev_dat.php?action=exp&num_cia={num_cia}&fecha={fecha}&dir=l&nom={nom}'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./pan_rev_dat.php?action=pasteles&num_cia={num_cia}&fecha={fecha}&dir=r&nom={nom}'"{disabled}>
			</p></td>
	</tr>
</table>
<!-- END BLOCK : avio -->
<!-- START BLOCK : pasteles_error -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Pasteles</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<p style="font-weight:bold; font-size:12pt;">Los siguientes pedidos no han sido liquidados, y su fecha de entrega es de días anteriores.</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Remisi&oacute;n</th>
					<th class="tabla">Fecha de<br />entrega</th>
					<th class="tabla">Kilos</th>
					<th class="tabla">Precio<br />por kilo</th>
					<th class="tabla">Importe<br />de pastel</th>
					<th class="tabla">Importe<br />de pan</th>
					<th class="tabla">Base</th>
					<th class="tabla">Pastillaje</th>
					<th class="tabla">Otros</th>
					<th class="tabla">Total</th>
					<th class="tabla">A cuenta</th>
					<th class="tabla">Resta por<br />pagar</th>
				</tr>
				<!-- START BLOCK : pastel_error_row -->
				<tr>
					<td class="rtabla" style="font-weight:bold;">{num_remision}</td>
					<td class="rtabla" style="font-weight:bold; color:#660;">{fecha_entrega}</td>
					<td class="rtabla">{kilos}</td>
					<td class="rtabla">{precio_kilo}</td>
					<td class="rtabla">{importe_pastel}</td>
					<td class="rtabla">{importe_pan}</td>
					<td class="rtabla">{base}</td>
					<td class="rtabla">{pastillaje}</td>
					<td class="rtabla">{otros_efectivos}</td>
					<td class="rtabla" style="font-weight:bold; color:#C00;">{total}</td>
					<td class="rtabla" style="font-weight:bold; color:#060;">{a_cuenta}</td>
					<td class="rtabla" style="font-weight:bold; color:#C00;">{resta_pagar}</td>
				</tr>
				<!-- END BLOCK : pastel_error_row -->
			</table>
			<p>
				<!-- <input type="button" class="boton" value="<< Regresar" onClick="document.location='pan_rev_dat.php?action=avio&num_cia={num_cia}&fecha={fecha}&nom={nom}&dir=l'"> -->
				<input type="button" class="boton" value="<< Regresar" onClick="document.location='pan_rev_dat.php?action=exp&num_cia={num_cia}&fecha={fecha}&nom={nom}&dir=l'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
			</p>
		</td>
	</tr>
</table>
<!-- END BLOCK : pasteles_error -->
<!-- START BLOCK : pasteles -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Pasteles</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<form method="post" name="form">
				<input name="tmp" type="hidden" id="tmp">
				<input name="nom" type="hidden" id="nom" value="{nom}">
				<input name="sueldo" type="hidden" id="sueldo" value="{sueldo}">
				<table class="tabla">
					<tr>
						<th class="tabla">Remisi&oacute;n</th>
						<th class="tabla">Fecha de<br />entrega</th>
						<th class="tabla">Kilos</th>
						<th class="tabla">Precio<br />por kilo</th>
						<th class="tabla">% descuento</th>
						<th class="tabla">Importe<br />de pastel</th>
						<th class="tabla">Importe<br />de pan</th>
						<th class="tabla">Base</th>
						<th class="tabla">Pastillaje</th>
						<th class="tabla">Bocadillos</th>
						<th class="tabla">Flete</th>
						<th class="tabla">Total</th>
						<th class="tabla">A cuenta</th>
						<th class="tabla">Abono</th>
						<th class="tabla">Resta por<br />pagar</th>
						<th class="tabla">Devoluci&oacute;n<br />de base</th>
						<th class="tabla">Importe<br />cancelaci&oacute;n</th>
					</tr>
					<!-- START BLOCK : pastel_row -->
					<tr>
						<td class="rtabla" style="font-weight:bold;">{num_remision}</td>
						<td class="rtabla" style="font-weight:bold; color:#660;">{fecha_entrega}</td>
						<td class="rtabla">{kilos}</td>
						<td class="rtabla">{precio_kilo}</td>
						<td class="rtabla">{descuento}</td>
						<td class="rtabla">{importe_pastel}</td>
						<td class="rtabla">{importe_pan}</td>
						<td class="rtabla">{base}</td>
						<td class="rtabla">{pastillaje}</td>
						<td class="rtabla">{bocadillos}</td>
						<td class="rtabla">{flete}</td>
						<td class="rtabla" style="font-weight:bold; color:#C00;">{total}</td>
						<td class="rtabla" style="font-weight:bold; color:#060;">{a_cuenta}</td>
						<td class="rtabla" style="font-weight:bold; color:#00C;">{abono}</td>
						<td class="rtabla" style="font-weight:bold; color:#C00;">{resta_pagar}</td>
						<td class="rtabla" style="font-weight:bold; color:#660;">{devolucion_base}</td>
						<td class="rtabla" style="font-weight:bold; color:#600;">{importe_cancelacion}</td>
					</tr>
					<!-- END BLOCK : pastel_row -->
				</table>
				<p>
					<!-- <input type="button" class="boton" value="<< Regresar" onClick="document.location='./pan_rev_dat.php?action=avio&num_cia={num_cia}&fecha={fecha}&dir=l&nom={nom}'"> -->
					<input type="button" class="boton" value="<< Regresar" onClick="document.location='./pan_rev_dat.php?action=exp&num_cia={num_cia}&fecha={fecha}&dir=l&nom={nom}'">
					&nbsp;&nbsp;
					<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
					&nbsp;&nbsp;
					<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./pan_rev_dat.php?action=gastos&num_cia={num_cia}&fecha={fecha}&dir=r&nom={nom}'"{disabled}>
				</p>
			</form>
		</td>
	</tr>
</table>
<!-- END BLOCK : pasteles -->
<!-- START BLOCK : gastos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Gastos</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<form method="post" name="form">
				<input name="tmp" type="hidden" id="tmp">
				<input name="nom" type="hidden" id="nom" value="{nom}">
				<input name="sueldo" type="hidden" id="sueldo" value="{sueldo}">
				<table class="tabla">
					<tr>
						<th class="tabla">F</th>
						<th class="tabla">Concepto</th>
						<th class="tabla">C&oacute;digo</th>
						<th class="tabla">Turno/Renta</th>
						<th class="tabla">Importe</th>
						<th class="tabla">O</th>
					</tr>
					<!-- START BLOCK : gas_row -->
					<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
						<td class="tabla"><input name="valid{i}" type="checkbox" id="valid{i}" value="{id}" {valid}></td>
						<td class="vtabla" style="color:#0000CC; font-weight:bold;"><input name="concepto[]" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) {
if (codgastos.length == undefined) codgastos.select();
else codgastos[{i}].select();
}
else if (event.keyCode == 38) {
if (concepto.length == undefined) this.blur();
else concepto[{back}].select();
}
else if (event.keyCode == 40) {
if (concepto.length == undefined) this.blur();
else concepto[{next}].select();
}" value="{concepto}" size="30" maxlength="255"></td>
						<td class="tabla"><input name="id[]" type="hidden" id="id" value="{id}">
							<input name="codgastos[]" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaGasto({i})" onKeyDown="if (event.keyCode == 13) {
if (codgastos.length == undefined) concepto.select();
else concepto[{next}].select();
}
else if (event.keyCode == 38) {
if (codgastos.length == undefined) this.blur();
else codgastos[{back}].select();
}
else if (event.keyCode == 40) {
if (codgastos.length == undefined) this.blur();
else codgastos[{next}].select();
}
else if (event.keyCode == 37) {
if (concepto.length == undefined) concepto.select();
else concepto[{i}].select();
}" value="{codgastos}" size="3">
							<input name="desc[]" type="text" disabled class="vnombre" id="desc" value="{desc}" size="30"></td>
						<td class="vtabla"><select name="turno[]" class="insert" id="turno"{display_turno}>
								<option value=""{-}></option>
								<option value="1"{1}>FRANCES DE DIA</option>
								<option value="2"{2}>FRANCES DE NOCHE</option>
								<option value="3"{3}>BIZCOCHERO</option>
								<option value="4"{4}>REPOSTERO</option>
								<option value="8"{8}>PICONERO</option>
								<option value="9"{9}>GELATINERO</option>
								<option value="10"{10}>DESPACHO</option>
							</select>
							<select name="idrenexp[]" class="insert" id="idrenexp"{display_exp}>
								<option value=""></option>
								<!-- START BLOCK : ren -->
								<option value="{id}"{ren_sel}>{nombre}</option>
								<!-- END BLOCK : ren -->
							</select></td>
						<td class="rtabla" style="font-weight:bold;"><input name="importe[]" type="hidden" id="importe" value="{importe}">
							{importe}</td>
						<td class="rtabla" style="font-weight:bold;"><input name="omitir{i}" type="checkbox" id="omitir{i}" value="{id}" {omitir}></td>
					</tr>
					<!-- END BLOCK : gas_row -->
					<!-- START BLOCK : gas_pas -->
					<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
						<td class="tabla">&nbsp;</td>
						<td class="vtabla" style="color:#0000CC; font-weight:bold;">CANCELACION PEDIDO {tipo_pedido} #{num_remi}</td>
						<td class="vtabla" style="font-weight:bold;">115 - DEVOLUCION DE PAN</td>
						<td class="tabla">&nbsp;</td>
						<td class="rtabla" style="font-weight:bold;">{importe}</td>
						<td class="rtabla" style="font-weight:bold;">&nbsp;</td>
					</tr>
					<!-- END BLOCK : gas_pas -->
					<!-- START BLOCK : gas_pre -->
					<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
						<td class="tabla">&nbsp;</td>
						<td class="vtabla" style="color:#0000CC; font-weight:bold;">{concepto}</td>
						<td class="vtabla" style="font-weight:bold;">41 - PRESTAMO EMPLEADO</td>
						<td class="tabla">&nbsp;</td>
						<td class="rtabla" style="font-weight:bold;">{importe}</td>
						<td class="rtabla" style="font-weight:bold;">&nbsp;</td>
					</tr>
					<!-- END BLOCK : gas_pre -->
					<tr>
						<th colspan="4" class="rtabla">&nbsp;</th>
						<th class="rtabla" style="font-size:12pt; ">{total}</th>
						<th class="rtabla">&nbsp;</th>
					</tr>
				</table>
				<p>
					<input type="button" class="boton" value="<< Regresar" onClick="validar('l')">
					&nbsp;&nbsp;
					<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
					&nbsp;&nbsp;
					<input type="button" class="boton" value="Siguiente >>" onClick="validar('r')">
				</p>
			</form></td>
	</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, gasto = new Array(), renexp = new Array(), alert_cont = 0, nomina = 0;
<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{desc}";
<!-- END BLOCK : gasto -->
<!-- START BLOCK : re -->
renexp[{id}] = {importe};
<!-- END BLOCK : re -->

function cambiaGasto(i) {
	var inputGasto = null, nombreGasto = null, turno, renexp;

	inputGasto = f.codgastos.length == undefined ? f.codgastos : f.codgastos[i];
	nombreGasto = f.desc.length == undefined ? f.desc : f.desc[i];
	turno = f.turno.length == undefined ? f.turno : f.turno[i];
	renexp = f.idrenexp.length == undefined ? f.idrenexp : f.idrenexp[i];

	if (inputGasto.value == "" || inputGasto.value == "0") {
		inputGasto.value = "";
		nombreGasto.value = "";
		renexp.style.display = 'none';
		turno.style.display = 'block';
	}
	else if (gasto[get_val(inputGasto)] != null) {
		nombreGasto.value = gasto[get_val(inputGasto)];

		if (get_val(inputGasto) == 101
		 || get_val(inputGasto) == 102
		 || get_val(inputGasto) == 100
		 || get_val(inputGasto) == 125
		 || get_val(inputGasto) == 6
		 || get_val(inputGasto) == 3
		 || get_val(inputGasto) == 159
		 || get_val(inputGasto) == 177
		 || get_val(inputGasto) == 5
		 || get_val(inputGasto) == 152
		 || get_val(inputGasto) == 8
		 || get_val(inputGasto) == 41
		 || get_val(inputGasto) == 1
		 || get_val(inputGasto) == 2
		 || get_val(inputGasto) == 4)
			eval('f.valid' + i).checked = true;

		// [14-Nov-2007] Si el código es el 49 renta expendio cambiar campo de turno por campo de limite de renta de expendio
		if (get_val(inputGasto) == 49) {
			turno.style.display = 'none';
			renexp.style.display = 'block';
		}
		else {
			turno.style.display = 'block';
			renexp.style.display = 'none';
		}
	}
	else {
		alert("El código de gasto no se encuentra en el catálogo");
		inputGasto.value = f.tmp.value;
	}
}

function msg(mensaje, campo) {
	alert(mensaje);
	eval(f.campo).select();
	return false;
}

function validar(dir) {
	if (f.codgastos) {
	// Validar que todos los gastos hayan sido codificados
	if (f.codgastos.length == undefined && get_val(f.codgastos) <= 0) {
		alert("Debe códificar todos los gastos");
		f.codgastos.select();
		return false;
	}
	else
		for (var i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) <= 0) {
				alert("Debe códificar todos los gastos");
				f.codgastos[i].select();
				return false;
			}

	// Validar el turno en los códigos de mercancia
	if (f.codgastos.length == undefined && get_val(f.codgastos) == 23 && (f.turno.selectedIndex == 0/* || f.turno.value == '10'*/)) {
		alert("Debe seleccionar el turno para las mercancias");
		f.turno.focus();
		return false;
	}
	else
		for (i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) == 23 && (f.turno[i].selectedIndex == 0/* || f.turno[i].value == '10'*/)) {
				alert("Debe seleccionar el turno para las mercancias");
				f.turno[i].focus();
				return false;
			}

	// [14-Dic-2007] Validar el turno en los códigos de leche
	if (f.codgastos.length == undefined && get_val(f.codgastos) == 9 && f.turno.selectedIndex == 0) {
		alert("Debe seleccionar el turno para la leche");
		f.turno.focus();
		return false;
	}
	else
		for (i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) == 9 && f.turno[i].selectedIndex == 0) {
				alert("Debe seleccionar el turno para la leche");
				f.turno[i].focus();
				return false;
			}

	// [14-Nov-2007] Validar que las rentas de expendios sean igual a sus limites
	if (f.codgastos.length == undefined && get_val(f.codgastos) == 49 && f.turno.selectedIndex == 0) {
		if (f.idrenexp.selectedIndex == 0) {
			alert("Debe seleccionar el limite de renta del expendio");
			f.idrenexp.focus();
			return false;
		}
		else if (get_val(f.importe) != renexp[get_val(f.idrenexp)]) {
			alert("El importe de renta no coincide con el del catálogo");
			f.idrenexp.focus();
			return false;
		}
	}
	else
		for (i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) == 49)
				if (f.idrenexp[i].selectedIndex == 0) {
					alert("Debe seleccionar el limite de renta del expendio");
					f.idrenexp[i].focus();
					return false;
				}
				else if (get_val(f.importe[i]) != renexp[get_val(f.idrenexp[i])]) {
					alert("El importe de renta no coincide con el del catálogo");
					f.idrenexp[i].focus();
					return false;
				}

	// [17-Dic-2007] Validar nómina en decena, que el importe de los gastos 1 SUELDO EMPLEADOS y 160 SUELDO CHOFERES sea igual al importe capturado al principio de la revisión
	var fecha = '{fecha}'.split('/'), dia = get_val2(fecha[0]), diasxmes = new Array(), sueldo = 0;
	diasxmes[1] = 31;
	diasxmes[2] = get_val2(fecha[1]) % 4 == 0 ? 29 : 28;
	diasxmes[3] = 31;
	diasxmes[4] = 30;
	diasxmes[5] = 31;
	diasxmes[6] = 30;
	diasxmes[7] = 31;
	diasxmes[8] = 31;
	diasxmes[9] = 30;
	diasxmes[10] = 31;
	diasxmes[11] = 30;
	diasxmes[12] = 31;

	if (dia == 10 || dia == 20 || dia == diasxmes[get_val2(fecha[2])]) {
		var sueldos = 0;

		// Sumar sueldos
		if (f.codgastos.length == undefined && (get_val(f.codgastos) == 1 || get_val(f.codgastos) == 160))
			sueldos = get_val(f.importe);
		else
			for (i = 0; i < f.codgastos.length; i++)
				if (get_val(f.codgastos[i]) == 1 || get_val(f.codgastos[i]) == 160)
					sueldos += get_val(f.importe[i]);

		if (sueldos + nomina != get_val(f.nom)) {
			alert('El importe de la nómina es diferente a la suma de los sueldos ' + (sueldos + nomina) + '-' + get_val(f.nom));
			return false;
		}
	}

	// Preguntar si los gastos de sueldo a empleados son correctos
	if (f.codgastos.length == undefined && (get_val(f.codgastos) == 1 || get_val(f.codgastos) == 2 || get_val(f.codgastos) == 3 || get_val(f.codgastos) == 4))
		alert_cont++;
	else
		for (i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) == 1 || get_val(f.codgastos[i]) == 2 || get_val(f.codgastos[i]) == 3 || get_val(f.codgastos[i]) == 4)
				alert_cont++;
	}

	if (alert_cont > 0 && !confirm('Hay códigos de sueldo, ¿Son correctos?')) return false;

	f.action = './pan_rev_dat.php?action=gastos_mod&num_cia={num_cia}&fecha={fecha}&dir=' + dir + '&nom={nom}';
	f.submit();
}

function obtenerNomina() {
	var myConn = new XHConn();

	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');

	myConn.connect("./pan_rev_dat.php", "GET", 'getNomina=1&num_cia={num_cia}&fecha={fecha}', valorNomina);
}

var valorNomina = function (oXML) {
	var result = get_val2(oXML.responseText);

	nomina = result;
}

window.onload = {init}
//-->
</script>
<!-- END BLOCK : gastos -->
<!-- START BLOCK : pres -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Prestamos</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<form action="" method="post" name="form">
				<input name="tmp" type="hidden" id="tmp">
				<table class="tabla">
					<tr>
						<th class="tabla">Cat&aacute;logo
							<input type="button" class="boton" value="Listar" onClick="listar({num_cia})"></th>
						<th class="tabla">Nombre</th>
						<th class="tabla">Saldo<br>
							Anterior</th>
						<th class="tabla">Prestamo</th>
						<th class="tabla">Abono</th>
						<th class="tabla">Nuevo<br>
							Saldo</th>
					</tr>
					<!-- START BLOCK : pres_row -->
					<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
						<!-- START BLOCK : cell_pres -->
						<td class="tabla"><input name="id[]" type="hidden" id="id" value="{id}">
							<input name="id_emp[]" type="hidden" id="id_emp" value="{id_emp}">
							<input name="abono[]" type="hidden" id="abono" value="{abono}">
<input name="num_emp[]" type="text" class="insert" id="num_emp" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaEmp({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) {
if (num_emp.length == undefined) this.blur();
else num_emp[{next}].select();
}
else if (event.keyCode == 38) {
if (num_emp.length == undefined) this.blur();
else num_emp[{back}].select();
}" value="{num_emp}" size="4">
							<input name="nombre[]" type="text" class="vnombre" id="nombre" value="{nombre_real}" size="30"></td>
						<!-- END BLOCK : cell_pres -->
						<!-- START BLOCK : cell_no_pres -->
						<td>&nbsp;</td>
						<!-- END BLOCK : cell_no_pres -->
						<td class="vtabla" style="font-weight:bold;">{nombre}</td>
						<td class="rtabla" style="font-weight:bold;">&nbsp;{saldo_ini}&nbsp;</td>
						<td class="rtabla" style="color:#CC0000; font-weight:bold;">&nbsp;{cargo}&nbsp;</td>
						<td class="rtabla" style="color:#0000CC; font-weight:bold;">&nbsp;{abono}&nbsp;</td>
						<td class="rtabla" style="font-weight:bold;">&nbsp;{saldo_fin}&nbsp;</td>
					</tr>
					<!-- END BLOCK : pres_row -->
					<tr>
						<th colspan="2" class="rtabla">Total</th>
						<th class="rtabla">{saldo_ini}</th>
						<th class="rtabla" style="color:#CC0000;">{cargos}</th>
						<th class="rtabla" style="color:#0000CC;">{abonos}</th>
						<th class="rtabla">{saldo_fin}</th>
					</tr>
				</table>
				<!-- START BLOCK : error_prestamos -->
				<p style="font-family:Arial, Helvetica, sans-serif;font-weight:bold;font-size:14pt;color:#C00;">El saldo de la panaderia no corresponde con el de la oficina ({saldo}) </p>
				<!-- END BLOCK : error_prestamos -->
				<p>
					<input type="button" class="boton" value="<< Regresar" onClick="validar('l')">
					&nbsp;&nbsp;
					<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
					&nbsp;&nbsp;
					<input type="button" class="boton" value="Siguiente >>" onClick="validar('r')"{disabled}>
				</p>
			</form></td>
	</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
<!--
var f = document.form, emp = new Array();
<!-- START BLOCK : emp -->
emp[{num_emp}] = new Array();
emp[{num_emp}][0] = {id_emp};
emp[{num_emp}][1] = "{nombre}";
emp[{num_emp}][2] = {saldo_emp};
<!-- END BLOCK : emp -->

function listar(num_cia) {
	var win = window.open("./listar_emp.php?num_cia=" + num_cia,"listar_emp.php?num_cia=" + num_cia,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=400,height=400");
	win.focus();
}

function cambiaEmp(i) {
	var num_emp, nombre, id_emp;
	num_emp = f.num_emp.length == undefined ? f.num_emp : f.num_emp[i];
	nombre = f.nombre.length == undefined ? f.nombre : f.nombre[i];
	id_emp = f.id_emp.length == undefined ? f.id_emp : f.id_emp[i];
	abono = f.abono.length == undefined ? f.abono : f.abono[i];

	if (num_emp.value == '' || num_emp.value == '0') {
		num_emp.value = '';
		nombre.value = '';
		id_emp.value = '';
	}
	else if (emp[get_val(num_emp)] != null) {
		if (emp[get_val(num_emp)][2] - abono.get('value').getNumericValue() >= 0) {
			id_emp.value = emp[get_val(num_emp)][0];
			nombre.value = emp[get_val(num_emp)][1];
		} else {
			alert('El abono es mayor al saldo del empleado [' + emp[get_val(num_emp)][2].numberFormat(2, '.', ',') + ']');

			num_emp.value = '';
			nombre.value = '';
			id_emp.value = '';
		}


	}
	else {
		alert("El empleado no se encuentra en el catálogo");
		num_emp.value = f.tmp.value;
		num_emp.select();
	}
}

function validar(dir) {
	if (!!f.num_emp)
	{
		if (f.num_emp.length == undefined && f.num_emp.value == '') {
			alert('Debe especificar el número de empleado para el movimiento');
			f.num_emp.select();
			return false;
		}
		else
			for (var i = 0; i < f.num_emp.length; i++)
				if (f.num_emp[i].value == '') {
					alert('Debe especificar el número de empleado para el movimiento');
					f.num_emp[i].select();
					return false;
				}
	}

	f.action = './pan_rev_dat.php?action=pres_mod&num_cia={num_cia}&fecha={fecha}&dir=' + dir;
	f.submit();
}

window.onload = f.num_emp.length == undefined ? f.num_emp.select() : f.num_emp[0].select();
//-->
</script>
<!-- END BLOCK : pres -->
<!-- START BLOCK : pres_error -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><p class="title">Prestamos</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<p style="font-family:Arial, Helvetica, sans-serif;font-weight:bold;font-size:14pt;color:#C00;">Los nombres de los siguientes empleados en sistema y en panader&iacute;a no coinciden (deben ser identicos a partir del 5 de noviembre de 2012)</p>
			<table class="tabla">
				<tr>
					<th class="tabla">Sistema</th>
					<th class="tabla">Panader&iacute;a</th>
				</tr>
				<!-- START BLOCK : pres_error_row -->
				<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
					<td class="tabla">{num_emp} {nombre_sistema}</td>
					<td class="vtabla" style="font-weight:bold;">{nombre_panaderia}</td>
				</tr>
				<!-- END BLOCK : pres_error_row -->
			</table>
			<p>
				<input type="button" class="boton" value="<< Regresar" onClick="document.location='pan_rev_dat.php?action=pres&num_cia={num_cia}&fecha={fecha}&dir=l'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
			</p></td>
	</tr>
</table>
<!-- END BLOCK : pres_error -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="center" valign="middle"><table class="tabla">
				<tr>
					<th class="tabla">Compa&ntilde;&iacute;a</th>
					<th class="tabla">Fecha</th>
				</tr>
				<tr>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
					<td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
				</tr>
			</table>
			<br>
			<table class="tabla">
				<tr>
					<th class="vtabla" scope="row" style="font-size:16pt;">Venta en Puerta </th>
					<td class="rtabla" style="font-size:16pt; font-weight:bold; color:#0000CC;">{venta_puerta}</td>
				</tr>
				<tr>
					<th class="vtabla" scope="row" style="font-size:16pt;">Pasteles</th>
					<td class="rtabla" style="font-size:16pt; font-weight:bold; color:#0000CC;">{pasteles}</td>
				</tr>
				<tr>
					<th class="vtabla" scope="row" style="font-size:16pt;">Abonos</th>
					<td class="rtabla" style="font-size:16pt; font-weight:bold; color:#0000CC;">{abonos}</td>
				</tr>
				<tr>
					<th class="vtabla" scope="row" style="font-size:16pt;">Pastillaje</th>
					<td class="rtabla" style="font-size:16pt; font-weight:bold; color:#0000CC;">{pastillaje}</td>
				</tr>
				<tr>
					<th class="vtabla" scope="row" style="font-size:16pt;">Otros</th>
					<td class="rtabla" style="font-size:16pt; font-weight:bold; color:#0000CC;">{otros}</td>
				</tr>
				<tr>
					<th class="vtabla" scope="row" style="font-size:16pt;">Raya Pagada </th>
					<td class="rtabla" style="font-size:16pt; font-weight:bold; color:#CC0000;">{raya_pagada}</td>
				</tr>
				<tr>
					<th class="vtabla" scope="row" style="font-size:16pt;">Gastos</th>
					<td class="rtabla" style="font-size:16pt; font-weight:bold; color:#CC0000;">{gastos}</td>
				</tr>
				<tr>
					<th class="vtabla" scope="row" style="font-size:16pt;">Efectivo</th>
					<th class="rtabla" style="font-size:20pt;">{efectivo}</th>
				</tr>
			</table>

			<!-- START BLOCK : error -->

			<p style="font-family:Arial, Helvetica, sans-serif;font-weight:bold;font-size:14pt;color:#C00;"> ERROR: La hoja ya ha sido validada con anterioridad, favor de revisar los datos en el sistema </p>

			<!-- END BLOCK : error -->
			<!-- START BLOCK : horror -->

			<p style="font-family:Arial, Helvetica, sans-serif;font-weight:bold;font-size:14pt;color:#C00;"> ERROR: Si eres Didia no puedes validar las hojas, por grosera &nbsp;&nbsp;&nbsp;(&quot;\(&not;....&not;
				)/&quot;)</p>

			<!-- END BLOCK : horror -->

			<p>
				<input type="button" class="boton" value="<< Regresar" onClick="document.location='./pan_rev_dat.php?action=pres&num_cia={num_cia}&fecha={fecha}&dir=l'">
				&nbsp;&nbsp;
				<input type="button" class="boton" value="Inicio" onClick="document.location='./pan_rev_dat.php'">
				&nbsp;&nbsp;
				<input name="terminar" type="button" class="boton" id="terminar" onClick="validar(this)" onDblClick="alert('No dar doble click');return false;" value="Terminar"{disabled_terminar}>
			</p></td>
	</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(boton) {
	var url = './pan_rev_dat.php?action=finish&num_cia={num_cia}&fecha={fecha}&dir=r';

	if (confirm("¿Esta segura de que todos los datos son correctos?")) {
		boton.disabled = true;
		document.location = url;
	}
}
//-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
