$(function(){
	$('#popup').toggle(function(){
		$('#main').animate({
			"position":"relative",
			"left":"-40%"
			});

		$('.popup ').addClass('chua');
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
 $(document).click(function(){
     if($('.popup ').hasClass('chua')){

	     if($('.ui-header').hasClass('ui-fixed-hidden')){
		 $('.ui-header').removeClass('ui-fixed-hidden');
		 }

}
	 });

	})

