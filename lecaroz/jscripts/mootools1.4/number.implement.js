Number.implement({
	
	numberFormat: function()
	{
		var precision = null;
		var dec_point = '.';
		var thousands_sep = '';
		
		Array.each(arguments, function(arg, index) {
			switch (index) {
				case 0:
					try {
						precision = arg.toInt();
						
						if (typeOf(precision) != 'number' && precision != null) {
							throw(-1);
						}
					}
					catch(error) {
						if (error == -1) {
							alert('Number.numberFormat: el primer argumento \'presicion\' debe ser de tipo \'integer\'');
						}
					}
				break;
				
				case 1:
					try {
						dec_point = arg;
						
						if (typeOf(dec_point) != 'string') {
							throw(-1);
						}
					}
					catch(error) {
						if (error == -1) {
							alert('Number.numberFormat: el segundo argumento \'punto_decimal\' debe ser de tipo \'string\'');
						}
					}
				break;
				
				case 2:
					try {
						thousands_sep = arg;
						
						if (typeOf(thousands_sep) != 'string') {
							throw(-1);
						}
					}
					catch(error) {
						if (error == -1) {
							alert('Number.numberFormat: el tercer argumento \'separador_miles\' debe ser de tipo \'string\'');
						}
					}
				break;
			}
		});
		
		var number = (this + '').replace(/[^0-9+\-Ee.]/g, '');
		
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+precision) ? 0 : Math.abs(precision),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};
		
		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}
		
		return s.join(dec);
	}
	
});
