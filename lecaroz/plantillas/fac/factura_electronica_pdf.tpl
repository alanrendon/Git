<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Factura Electrónica</title>
		<style>
			body, button, input, textarea {
				font-family: Arial, Helvetica, sans-serif;
				font-size: 10pt;
			}

			/********************************************************/
			/*                    Tipografía                        */
			/********************************************************/

			.bold { font-weight: bold; }

			.italic { font-style: italic; }

			.underline {
				padding-bottom: 0.15em;
				border-bottom: 1px solid;
			}

			.wrapped {
				display: block;
				border-width: 3px;
				border-style: solid;
				border-color: #8f8f8f;
				-webkit-border-radius: 7px;
				-moz-border-radius: 7px;
				border-radius: 7px;
			}

			.monospace {
				font-family: "Courier New", Courier, monospace;
			}

			.wordwrap {
				white-space: pre-wrap;		/* CSS3 */
				white-space: -moz-pre-wrap;	/* Firefox */
				white-space: -pre-wrap;		/* Opera <7 */
				white-space: -o-pre-wrap;	/* Opera 7 */
				word-wrap: break-word;		/* IE */
			}

			/********************************************************/
			/*                   Colores genericos                  */
			/********************************************************/

			.silver,
			.silver-color .color {
				color: #cccccc !important;
				}
				.icon-silver:before {
					color: #cccccc;
				}
			.black,
			.black-color .color {
				color: black !important;
				}
				.icon-black:before {
					color: black;
				}
			.anthracite,
			.anthracite-color .color {
				color: #4c4c4c !important;
				}
				.icon-anthracite:before {
					color: #4c4c4c;
				}
			.grey,
			.grey-color .color {
				color: #a5a5a5 !important;
				}
				.icon-grey:before {
					color: #a5a5a5;
				}
			.white,
			.white-color .color {
				color: white !important;
				}
				.icon-white:before {
					color: white;
				}
			.red,
			.red-color .color {
				color: #dd380d !important;
				}
				.icon-red:before {
					color: #dd380d;
				}
			.orange,
			.orange-color .color {
				color: #ffae00 !important;
				}
				.icon-orange:before {
					color: #ffae00;
				}
			.green,
			.green-color .color {
				color: #99c624 !important;
				}
				.icon-green:before {
					color: #99c624;
				}
			.blue,
			.blue-color .color {
				color: #0059a0 !important;
				}
				.icon-blue:before {
					color: #0059a0;
				}

			/********************************************************/
			/*                      Utilidades                      */
			/********************************************************/

			/* Alineación del texto */
			.align-left		{ text-align: left; }
			.align-center	{ text-align: center; }
			.align-right	{ text-align: right; }

			/* Elementos flotantes */
			.float-left		{ float: left; }
			.float-right	{ float: right; }
			.clear-left		{ clear: left; }
			.clear-right	{ clear: right; }
			.clear-both		{ clear: both; }

			/* Rellenos */
			.with-padding			{ padding: 20px !important; }
			.with-mid-padding		{ padding: 10px !important; }
			.with-small-padding		{ padding: 5px !important; }
			.no-padding				{ padding: 0 !important; }

			/* Márgenes */
			.large-margin-top		{ margin-top: 30px !important; }
			.margin-top				{ margin-top: 16px !important; }
			.mid-margin-top			{ margin-top: 8px !important; }
			.small-margin-top		{ margin-top: 4px !important; }
			.no-margin-top			{ margin-top: 0 !important; }
			.large-margin-right		{ margin-right: 30px !important; }
			.margin-right			{ margin-right: 16px !important; }
			.mid-margin-right		{ margin-right: 8px !important; }
			.small-margin-right		{ margin-right: 4px !important; }
			.no-margin-right		{ margin-right: 0 !important; }
			.large-margin-left		{ margin-left: 30px !important; }
			.margin-left			{ margin-left: 16px !important; }
			.mid-margin-left		{ margin-left: 8px !important; }
			.small-margin-left		{ margin-left: 4px !important; }
			.no-margin-left			{ margin-left: 0 !important; }
			.large-margin-bottom	{ margin-bottom: 30px !important; }
			.margin-bottom			{ margin-bottom: 16px !important; }
			.mid-margin-bottom		{ margin-bottom: 8px !important; }
			.small-margin-bottom	{ margin-bottom: 4px !important; }
			.no-margin-bottom		{ margin-bottom: 0 !important; }

			/********************************************************/
			/*                        Tablas                        */
			/********************************************************/

			table {
				*border-collapse: collapse;	/* IE7 and lower */
				border-spacing: 0;
				width: 100%;
				border: 3px solid #8f8f8f;
				-moz-border-radius: 7px;
				-webkit-border-radius: 7px;
				border-radius: 7px;
			}

			td, th {
				padding: 2px 5px;
				vertical-align: text-top;
			}

			th {
				background-color: #f0eeef;
			}

			th:first-child {
				-moz-border-radius: 4px 0 0 0;
				-webkit-border-radius: 4px 0 0 0;
				border-radius: 4px 0 0 0;
			}

			th:last-child {
				-moz-border-radius: 0 4px 0 0;
				-webkit-border-radius: 0 4px 0 0;
				border-radius: 0 4px 0 0;
			}

			th:only-child {
				-moz-border-radius: 4px 4px 0 0;
				-webkit-border-radius: 4px 4px 0 0;
				border-radius: 4px 4px 0 0;
			}

			tbody tr:nth-child(even) {
				background: #ebf0f6;
			}
		</style>
	</head>
	<body>
		<div style="overflow:hidden;">
			<div class="float-left" style="width:22%;">
				<img src="{base_dir}/imagenes/logos_cfds/{logo}" />
			</div>
			<div class="float-left" style="width:48%;">
				<div class="with-padding">
					<div class="bold">{razon_social_emisor}</div>
					<div class="bold">{rfc_emisor}</div>
					<div class="bold italic">{regimen_fiscal_emisor}</div>
					<div class="small-margin-top">{domicilio_fiscal_emisor}</div>
					<div class="bold blue mid-margin-top">Matriz</div>
					<div class="small-margin-top">{domicilio_fiscal_matriz}</div>
				</div>
			</div>
			<div class="float-left align-center" style="width:30%;">
				<div class="wrapped with-mid-padding">
					<div class="bold red">{tipo_documento}</div>
					<div class="bold blue">Comprobante de ingreso</div>
					<div class="bold blue small-margin-top">Serie y folio</div>
					<div>{folio}</div>
					<div class="bold blue small-margin-top">Fecha y hora de emisión</div>
					<div>{fecha_emision}</div>
					<div class="bold blue small-margin-top">Fecha y hora de certificación</div>
					<div>{fecha_certificacion}</div>
					<div class="bold blue small-margin-top">Lugar de expedición</div>
					<div>{lugar_expedicion}</div>
				</div>
			</div>
		</div>
		<div class="wrapped with-mid-padding mid-margin-top" style="overflow:hidden;">
			<!-- START BLOCK : bloque_normal -->
			<div class="float-left" style="width:60%;">
				<div class="bold blue">Receptor del comprobante fiscal</div>
				<div class="bold small-margin-top">{razon_social_receptor}</div>
				<div class="bold">{rfc_receptor}</div>
				<div class="small-margin-top">{domicilio_fiscal_receptor}</div>
			</div>
			<div class="float-left" style="width:10%;">&nbsp;</div>
			<div class="float-left align-center" style="width:30%;">
				<div class="bold blue">Folio fiscal</div>
				<div>{folio_fiscal}</div>
				<div class="bold blue small-margin-top">No. certificado digital</div>
				<div>{no_certificado_digital}</div>
				<div class="bold blue small-margin-top">Serie certificado SAT</div>
				<div>{serie_certificado_sat}</div>
			</div>
			<!-- END BLOCK : bloque_normal -->
			<!-- START BLOCK : bloque_consignatario -->
			<div class="float-left" style="width:40%;">
				<div class="bold blue">Receptor del comprobante fiscal</div>
				<div class="bold small-margin-top">{razon_social_receptor}</div>
				<div class="bold">{rfc_receptor}</div>
				<div class="small-margin-top">{domicilio_fiscal_receptor}</div>
			</div>
			<div class="float-left" style="width:30%;">
				<div class="bold blue">Consignado a</div>
				<div class="bold small-margin-top">{razon_social_consignatario}</div>
				<div class="bold">{rfc_consignatario}</div>
				<div class="small-margin-top">{domicilio_fiscal_consignatario}</div>
			</div>
			<div class="float-left align-center" style="width:30%;">
				<div class="bold blue">Folio fiscal</div>
				<div>{folio_fiscal}</div>
				<div class="bold blue small-margin-top">No. certificado digital</div>
				<div>{no_certificado_digital}</div>
				<div class="bold blue small-margin-top">Serie certificado SAT</div>
				<div>{serie_certificado_sat}</div>
			</div>
			<!-- END BLOCK : bloque_consignatario -->
		</div>
		<div class="mid-margin-top">
			<table>
				<thead>
					<tr>
						<th class="blue align-right" style="width:10%">Cantidad</th>
						<th class="blue align-left" style="width:12%">Unidad</th>
						<th class="blue align-left" style="width:48%">Descripción</th>
						<th class="blue align-right" style="width:15%">Precio</th>
						<th class="blue align-right" style="width:15%">Importe</th>
					</tr>
				</thead>
				<tbody>
					<!-- START BLOCK : concepto -->
					<tr>
						<td class="align-right">{cantidad}</td>
						<td>{unidad}</td>
						<td>{descripcion}</td>
						<td class="align-right">${precio}</td>
						<td class="align-right">${importe}</td>
					</tr>
					<!-- START BLOCK : datos_aduanales -->
					<tr>
						<td style="font-size:8pt;" colspan="5"><span class="bold">Datos aduanales</span><br />#Pedimento: {numero_pedimento}<br />Fecha: {fecha_entrada}<br />Aduana: {aduana_entrada}</td>
					</tr>
					<!-- END BLOCK : datos_aduanales -->
					<!-- END BLOCK : concepto -->
				</tbody>
			</table>
		</div>
		<div class="wrapped with-mid-padding mid-margin-top" style="overflow:hidden;">
			<div class="float-left" style="width:60%;">
				<div><strong>Forma de pago:</strong> {forma_pago}</div>
				<div><strong>Método de pago:</strong> {metodo_pago}</div>
				<div><strong>Cuenta de pago:</strong> {cuenta_pago}</div>
				<div><strong>Condiciones de pago:</strong> {condiciones_pago}</div>
				<div><strong>Importe con letra:</strong> {importe_letra}</div>
			</div>
			<div class="float-left" style="width:40%;">
				<div style="border-left:2px dotted  #8f8f8f;">
					<!-- <div class="bold blue align-center margin-left">Importe</div> -->
					<div class="bold margin-left" style="overflow:hidden;">
						<div class="float-left" style="width:60%;">Subtotal</div>
						<div class="float-left align-right" style="width:40%;">${subtotal}</div>
					</div>
					<!-- START BLOCK : ieps -->
					<div class="bold margin-left" style="overflow:hidden;">
						<div class="float-left" style="width:60%;">IEPS {porcentaje_ieps}%</div>
						<div class="float-left align-right" style="width:40%;">${ieps}</div>
					</div>
					<!-- END BLOCK : ieps -->
					<!-- START BLOCK : iva -->
					<div class="bold margin-left" style="overflow:hidden;">
						<div class="float-left" style="width:60%;">IVA {porcentaje_iva}%</div>
						<div class="float-left align-right" style="width:40%;">${iva}</div>
					</div>
					<!-- END BLOCK : iva -->
					<!-- START BLOCK : retenciones -->
					<div class="bold margin-left" style="overflow:hidden;">
						<div class="float-left" style="width:60%;">Retencion IVA</div>
						<div class="float-left align-right" style="width:40%;">-${retencion_iva}</div>
					</div>
					<div class="bold margin-left" style="overflow:hidden;">
						<div class="float-left" style="width:60%;">Retencion ISR</div>
						<div class="float-left align-right" style="width:40%;">-${retencion_isr}</div>
					</div>
					<!-- END BLOCK : retenciones -->
					<div class="bold mid-margin-top margin-left" style="border-top:3px solid #8f8f8f; overflow:hidden; padding-top:5px;">
						<div class="float-left" style="width:60%;">Total</div>
						<div class="float-left align-right" style="width:40%;">${total}</div>
					</div>
				</div>
			</div>
		</div>
		<div class="wrapped with-mid-padding mid-margin-top">
			<div class="bold blue">Cadena original del complemento de certificación digital del SAT</div>
			<div class="monospace wordwrap small-margin-top">{cadena_original}</div>
		</div>
		<div class="mid-margin-top" style="overflow:hidden;">
			<div class="float-left" style="width:18%;">
				<img src="{codigo_qr}" />
			</div>
			<div class="float-left" style="width:82%;">
				<div class="wrapped with-mid-padding">
					<div class="bold blue">Sello digital del CFDI</div>
					<div class="monospace wordwrap small-margin-top">{sello_digital_cfdi}</div>
				</div>
				<div class="wrapped with-mid-padding mid-margin-top">
					<div class="bold blue">Sello digital del SAT</div>
					<div class="monospace wordwrap small-margin-top">{sello_digital_sat}</div>
				</div>
			</div>
		</div>
		<!-- START BLOCK : observaciones -->
		<div class="wrapped with-mid-padding mid-margin-top">
			<div class="bold blue">Observaciones</div>
			<div class="small-margin-top">{observaciones}</div>
		</div>
		<!-- END BLOCK : observaciones -->
		<div class="bold red align-center mid-margin-top">Este documento es una representación impresa de un CFDI</div>
	</body>
</html>
