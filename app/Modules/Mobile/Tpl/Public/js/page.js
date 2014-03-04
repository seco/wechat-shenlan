$(function(){
 
	$('#popup').toggle(function(){
		 
		 
		 	
		 
		$('.ui-header').animate({
			"width":"60%"
		});	
		},
		function(){
		 
		$('#main').animate({
			"position":"relative",
			"left":"0"
			});
		$('.popup ').animate({
			"right":"-40%"
			});	
		$('.popup ').removeClass('chua');
		$('.ui-header').animate({
			"width":"100%"
		});	
	 
		}
		)
 
	})
 