// JavaScript Document

String.implement({
	getNumericValue: function() {
		var string = this.replace(/[^\+\-\d\.e]/g, '');
		
		if (!(/^(?:\+|-)?\d+(\.\d+)?(e[-|\+]?\d+)?/.test(string))) {
			return 0;
		}
		else if (!$chk(parseFloat(string))) {
			return 0;
		}
		else if (/\./.test(string)) {
			return parseFloat(string);
		}
		else {
			return parseInt(string, 10);
		}
	}
});

String.alias('getNumericValue', 'getVal');
