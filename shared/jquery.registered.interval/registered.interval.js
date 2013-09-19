(function( $ ){
	$.fn.registeredInterval = function(options){

// send an empty options array to this function to clear the interval set on an element.
//send "func" and "delay" to set an interval on the element.  
	var settings ={ 
		timeout: 0
	
		 };
	var targetElement = this;

	return this.each(function() {

	var intervalId;	
	if (options) {
	$.extend(settings, options);
	}
	if (settings.func && settings.delay) {
		intervalId = setInterval(settings.func, settings.delay);
		targetElement.data( "intervalId" , intervalId);	
		if(settings.timeout > 0){	
			setTimeout(function(){clearInterval(intervalId);},settings.timeout);
		} 
	} else {
		intervalId = targetElement.data( "intervalId" );	
		clearInterval(intervalId);
	};
	
	


	});
	};
}) (jQuery);
