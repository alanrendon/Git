// JavaScript Document

String.implement({
	
	getVal: function()
	{
		//var str = this.replace(/[^[\+|-]?\d+\.\d]/g, '');
		var str = this.replace(/[^\+\-\d\.]/g, '');
		
		if ((isNaN(parseFloat(str)) && isNaN(parseInt(str, 10))) || str == undefined)
		{
			return false;
		}
		else if (/\./.test(str))
		{
			return !isNaN(parseFloat(str)) ? parseFloat(str) : 0;
		}
		else
		{
			return !isNaN(parseInt(str, 10)) ? parseInt(str, 10) : 0;
		}
	}
	
});

Number.implement({
	
	numberFormat: function()
	{
		var precision = null;
		var punto_decimal = '.';
		var separador_miles = '';
		
		$A(arguments).each(function(arg, index)
		{
			switch (index)
			{
				case 0:
					try
					{
						precision = arg.toInt();
						
						if ($type(precision) != 'number' && precision != null)
						{
							throw(-1);
						}
						
						if (precision < 0)
						{
							precision = 0;
						}
					}
					catch(er)
					{
						if (er == -1)
						{
							console.log('Number.numberFormat: el primer argumento \'presicion\' debe ser de tipo \'integer\'');
							return false;
						}
					}
				break;
				case 1:
					try
					{
						punto_decimal = arg;
						
						if ($type(punto_decimal) != 'string')
						{
							throw(-2);
						}
					}
					catch(er) {
						if (er == -2)
						{
							console.log('Number.numberFormat: el segundo argumento \'punto_decimal\' debe ser de tipo \'string\'');
						}
					}
				break;
				case 2:
					try
					{
						separador_miles = arg;
						
						if ($type(separador_miles) != 'string')
						{
							throw(-2);
						}
					}
					catch(er) {
						if (er == -2)
						{
							console.log('Number.numberFormat: el tercer argumento \'separador_miles\' debe ser de tipo \'string\'');
						}
					}
				break;
			}
		});
		
		//var str = new String(this.round(precision));
		//var str = str.toString();
		var str = this.round(precision).toString();
		var partes = /([?:\+|-])?(\d{1,})+\.?(\d{0,})*/.exec(str);
		
		var parte_real = partes[3] != undefined ? partes[3] : '';
		var parte_entera = partes[2];
		var signo = partes[1] != undefined ? partes[1] : '';
		var nuevo_str = signo;
		
		var i, j;
		var cont = 0;
		for (i = 0, j = parte_entera.length; i < parte_entera.length; i++, j--)
		{
			if ((j % 3) != 0)
			{
				nuevo_str += parte_entera[i];
			}
			else
			{
				nuevo_str += (i > 0 ? separador_miles : '') + parte_entera[i];
			}
		}
		
		if (precision != null)
		{
			if (parte_real.length > 0)
			{
				nuevo_str += punto_decimal + parte_real;
				
				if (parte_real.length < precision)
				{
					for (i = 0; i < precision - parte_real.length; i++)
					{
						nuevo_str += '0';
					}
				}
			}
			else {
				nuevo_str += punto_decimal;
				for (i = 0; i < precision; i++)
				{
					nuevo_str += '0';
				}
			}
		}
		else
		{
			if (parte_real.length > 0)
			{
				nuevo_str += punto_decimal + parte_real;
			}
		}
		
		return nuevo_str;
	}

});