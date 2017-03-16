window.addEvent('domready', function() {

	validator = new FormValidator(null, {
		showErrors: true,
		selectOnFocus: true
	});

	$$('.validate').each(function(el, i)
	{
		validator.addElementEvents(el);

		el.addEvents({
			'change': function()
			{
				new Request(
				{
					url: 'ProduccionTurnosEfectivoReporte.php',
					data:
					{
						'accion': 'actualizar_porcentaje',
						'num_cia': $$('input[id=num_cia]')[i].get('value'),
						'anio': $$('input[id=anio]')[i].get('value'),
						'mes': $$('input[id=mes]')[i].get('value'),
						'porcentaje': $$('input[id=porcentaje]')[i].get('value')
					},
					onSuccess: function() {
						var porcentaje = $$('input[id=porcentaje]')[i].get('value').getNumericValue();
						var efectivo = $$('input[id=efectivo_pan_dulce]')[i].get('value').getNumericValue();
						var ieps = 0;

						ieps = porcentaje > 0 && efectivo > 0 ? (efectivo * porcentaje / 100) * 0.08 : 0;

						$$('input[id=ieps]')[i].set('value', ieps > 0 ? ieps.numberFormat(2, '.', ',') : '');
					}
				}).send();
			},
			'keydown': function(e)
			{
				if (e.key == 'enter' || e.key == 'down')
				{
					e.stop();

					if (i + 1 <= $$('.validate').length - 1)
					{
						$$('.validate')[i + 1].select();
					}
					else
					{
						$$('.validate')[0].select();
					}
				}
				else if (e.key == 'up')
				{
					e.stop();

					if (i > 0)
					{
						$$('.validate')[i - 1].select();
					}
					else
					{
						$$('.validate')[$$('.validate').length - 1].select();
					}
				}
			}
		});
	});
	
	document.id('cerrar').addEvent('click', function() {
		self.close();
	});
	
});
