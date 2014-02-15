
$(window).ready(function(){
 

$(window).bind("scroll",function(){
	
 
 
		nowtop = parseInt($(document).scrollTop());	
		$('#cleft_box').css('top', nowtop +100 + 'px')
		$('.header').css('top',nowtop + 'px');
	 
	
		 	 
 
		
		
	})
 }); 	
 