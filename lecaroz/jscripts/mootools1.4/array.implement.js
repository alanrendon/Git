Array.implement({
	
	getNumericValue: function() {
		return this.map(function(el) {
			return el.getNumericValue();
		});
	},
	
	sum: function() {
		for (var i = 0, sum = 0; i < this.length; sum += this[i++]);
		
		return sum;
	}
	
});
