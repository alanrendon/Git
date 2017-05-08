// JavaScript Document

Number.implement({
	
	numberFormat: function() {
		var precision = null;
		var dec_point = '.';
		var thousands_sep = '';
		
		$A(arguments).each(function(arg, index) {
			switch (index) {
				case 0:
					try {
						precision = arg.toInt();
						
						if ($type(precision) != 'number' && precision != null) {
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
						
						if ($type(dec_point) != 'string') {
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
						
						if ($type(thousands_sep) != 'string') {
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
		
		var string = this.round(precision).toString();
		var pieces = /([?:\+|-])?(\d{1,})+\.?(\d{0,})*/.exec(string);
		
		var dec_part = $chk(pieces[3]) ? pieces[3] : '';
		var int_part = pieces[2];
		var sign = $chk(pieces[1]) ? pieces[1] : '';
		var formatted_number = sign;
		
		var i, j;
		var cont = 0;
		for (i = 0, j = int_part.length; i < int_part.length; i++, j--) {
			if ((j % 3) != 0) {
				formatted_number += int_part[i];
			}
			else {
				formatted_number += (i > 0 ? thousands_sep : '') + int_part[i];
			}
		}
		
		if (precision != null) {
			if (dec_part.length > 0) {
				formatted_number += dec_point + dec_part;
				
				if (dec_part.length < precision) {
					for (i = 0; i < precision - dec_part.length; i++) {
						formatted_number += '0';
					}
				}
			}
			else {
				formatted_number += dec_point;
				for (i = 0; i < precision; i++) {
					formatted_number += '0';
				}
			}
		}
		else {
			if (dec_part.length > 0) {
				formatted_number += dec_point + dec_part;
			}
		}
		
		return formatted_number;
	}
	
});
