/**
 * weebo horizontal menu plugin for jQuery
 * v1.0
 * weebo jForm component tools.
 *
 * By Jan Prochazka aka prochor666, prochor666@gmail.com
 *
 * As featured on multizone.cz
 * Please use as you wish at your own risk.
 */

(function($) {  
	$.fn.weeboJForm = function(options) 
	{ 
		var options = $.extend( { checkBoxes: '.jform-checkbox', radioBoxes: '.jform-radio' }, options);  
		
		// Handle forms
		var formWrapper = this;
		
		var checkBoxState = function(obj){
			var ch = obj.find(options.checkBoxes);
			var setName = ch.attr('type');
			$("input[type='"+setName+"']").parent('label').toggleClass('jform-checkbox-checked', false);
			$("input[type='"+setName+"']:checked").parent('label').toggleClass('jform-checkbox-checked', true);
		}
		
		var radioState = function(obj){
			var r = obj.find(options.radioBoxes);
			var setName = r.attr('type');
			$("input[type='"+setName+"']").parent('label').toggleClass('jform-radio-checked', false);
			$("input[type='"+setName+"']:checked").parent('label').toggleClass('jform-radio-checked', true);
		}
		
		formWrapper.each( function(){
			
			var cells = $(this).find('.jform-cell');
			
			cells.each( function(){
				
				checkBoxState($(this));
				radioState($(this));
			});
			
		});
		
		$(options.checkBoxes).off('change').on('change', function(){
			checkBoxState($(this).parent().parent('.jform-cell'));
		});
		
		$(options.radioBoxes).off('change').on('change', function(){
			radioState($(this).parent().parent('.jform-cell'));
		});
	};  
})(jQuery); 


