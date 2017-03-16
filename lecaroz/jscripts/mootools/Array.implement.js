// JavaScript Document

Array.implement({
	
	getNumericValue: function() {
		return this.map(function(el) {
			return el.getNumericValue();
		});
	},
	
	sum: function() {
		for (var i = 0, sum = 0; i < this.length; sum += this[i++]);
		
		return sum;
	},
	
	max: function() {
		var max = this[0];
		
		this.each(function(value) {
			if (value > max) {
				max = value;
			}
		});
		
		return max;
	},
	
	min: function() {
		var min = this[0];
		
		this.each(function(value) {
			if (value < min) {
				min = value;
			}
		});
		
		return min;
	}
	
});
