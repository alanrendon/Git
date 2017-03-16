String.implement({
	
	getNumericValue: function()
	{
		var string = this.replace(/[^\+\-\d\.]/g, '');
		
		if (string == '') {
			return 0;
		}/* else if (!(/^(?:\+|-)?\d+(\.\d+)?(e[-|\+]?\d+)?/.test(string))) {
			return null;
		} else if (!!!(parseFloat(string) || parseFloat(string) === 0)) {
			return null;
		}*/ else if (/\./.test(string)) {
			return parseFloat(string);
		} else {
			return parseInt(string, 10);
		}
	}
	
});
