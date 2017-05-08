<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Dep&oacute;sitos pendientes de conciliar</title>
	<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/table_layout.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/FormValidator2.0.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/mbox/mBoxCore.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/mbox/mBoxModal.css" rel="stylesheet" type="text/css" />
	<link href="/lecaroz/styles/mbox/mBoxTooltip.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/lecaroz/menus/stm31.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/string.implement.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/number.implement.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/array.implement.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Core.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.Confirm.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Tooltip.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/moment/moment-with-locales.min.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
	<script type="text/javascript" src="/lecaroz/jscripts/ban/ConciliacionDepositos.js"></script>
	<style type="text/css">
		.info-table {
			border-collapse: collapse;
			border: solid 1px #000;
			background-color: #fff;
		}
		.info-table td,
		.info-table th {
			border: solid 1px #000;
		}
		.info-table th {
			background-color: #999;
		}

		.logo_banco {
			padding: 0px 0px 0px 18px;
			background-repeat: no-repeat;
		}
		.logo_banco_1 {
			background-image: url(imagenes/Banorte16x16.png);
		}
		.logo_banco_2 {
			background-image: url(imagenes/Santander16x16.png);
		}
	</style>
</head>

<body>

	<div id="contenedor">
		<div id="titulo">Dep&oacute;sitos pendientes de conciliar</div>
		<div id="captura" align="center"> </div>
	</div>

	<div id="tarjeta_wrapper" style="display:none;">
		<form action="" class="FormValidator" id="tarjeta_form">
			Importe de tarjeta:
			<input type="text" class="validate focus numberPosFormat right" precision="2" id="tarjeta_importe" size="10" value="">
			&nbsp;&nbsp;
			Fecha:
			<input type="text" class="validate focus toDate center" id="tarjeta_fecha" size="10" maxlength="10" value="">
		</form>
	</div>

	<script language="javascript" type="text/javascript" src="/lecaroz/menus/{menucnt}"></script>

</body>
</html>
